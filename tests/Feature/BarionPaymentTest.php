<?php

namespace Tests\Feature;

use App\Models\BarionSetting;
use App\Models\Order;
use App\Models\User;
use App\Services\BarionPaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BarionPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_live_pos_key_on_test_setting_switches_to_live_and_starts_payment(): void
    {
        BarionSetting::query()->create([
            'payee' => 'shop@example.com',
            'pos_key' => 'aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee',
            'use_test' => true,
        ]);

        $this->fakeBarionStart(acceptedEnv: 'live');

        $result = app(BarionPaymentService::class)->startPayment($this->makeOrder());

        $this->assertTrue($result['ok']);
        $this->assertSame('pay-live-1', $result['payment_id']);
        $this->assertStringContainsString('secure.barion.com/Pay', $result['redirect_url']);
        $this->assertStringNotContainsString('test.barion.com', $result['redirect_url']);
        $this->assertFalse(BarionSetting::current()->use_test);
    }

    public function test_test_pos_key_on_live_setting_does_not_silently_use_sandbox(): void
    {
        BarionSetting::query()->create([
            'payee' => 'shop@example.com',
            'pos_key' => 'aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee',
            'use_test' => false,
        ]);

        $this->fakeBarionStart(acceptedEnv: 'test');

        $result = app(BarionPaymentService::class)->startPayment($this->makeOrder());

        $this->assertFalse($result['ok']);
        $this->assertStringContainsString('teszt (sandbox)', $result['message']);
        $this->assertFalse(BarionSetting::current()->use_test);
    }

    public function test_authentication_failed_on_both_environments(): void
    {
        BarionSetting::query()->create([
            'payee' => 'shop@example.com',
            'pos_key' => 'aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee',
            'use_test' => false,
        ]);

        $this->fakeBarionStart(acceptedEnv: null);

        $result = app(BarionPaymentService::class)->startPayment($this->makeOrder());

        $this->assertFalse($result['ok']);
        $this->assertStringContainsString('éles POSKey', $result['message']);
    }

    public function test_admin_save_with_live_key_turns_off_test_mode(): void
    {
        $admin = User::factory()->create(['id' => 1]);

        $this->fakeBarionProbe(acceptedEnv: 'live');

        $this->actingAs($admin)->put(route('admin.barion.update'), [
            'payee' => 'shop@example.com',
            'pos_key' => ' {aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee} ',
            'use_test' => '1',
        ])->assertRedirect(route('admin.barion.edit'))
            ->assertSessionHas('success');

        $settings = BarionSetting::current();
        $this->assertFalse($settings->use_test);
        $this->assertSame('aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee', $settings->pos_key);
        $this->assertSame('shop@example.com', $settings->payee);
    }

    public function test_admin_save_warns_when_live_selected_but_key_is_test(): void
    {
        $admin = User::factory()->create(['id' => 1]);

        $this->fakeBarionProbe(acceptedEnv: 'test');

        $this->actingAs($admin)->put(route('admin.barion.update'), [
            'payee' => 'shop@example.com',
            'pos_key' => 'aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee',
            'use_test' => '0',
        ])->assertRedirect(route('admin.barion.edit'))
            ->assertSessionHas('warning');

        $this->assertFalse(BarionSetting::current()->use_test);
    }

    public function test_connection_test_reports_live_key_while_test_is_selected(): void
    {
        $admin = User::factory()->create(['id' => 1]);

        BarionSetting::query()->create([
            'payee' => 'shop@example.com',
            'pos_key' => 'aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee',
            'use_test' => true,
        ]);

        $this->fakeBarionProbe(acceptedEnv: 'live');

        $this->actingAs($admin)->post(route('admin.barion.test'), [
            'use_test' => '1',
        ])->assertRedirect(route('admin.barion.edit'))
            ->assertSessionHas('warning');
    }

    public function test_normalize_pos_key_strips_braces_and_whitespace(): void
    {
        $this->assertSame(
            'aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee',
            BarionPaymentService::normalizePosKey(" {aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee} \n")
        );
        $this->assertNull(BarionPaymentService::normalizePosKey('   '));
    }

    private function makeOrder(): Order
    {
        return Order::query()->create([
            'name' => 'Teszt Vásárló',
            'phone' => '06301234567',
            'shipping_address' => '1234 Budapest, Teszt 1.',
            'billing_address' => '1234 Budapest, Teszt 1.',
            'items' => [['id' => 1, 'title' => 'Termék', 'price' => 1000, 'qty' => 1]],
            'total_price' => 1000,
            'payment_method' => 'barion',
            'payment_status' => 'pending',
        ]);
    }

    private function fakeBarionStart(?string $acceptedEnv): void
    {
        Http::fake(function ($request) use ($acceptedEnv) {
            $env = str_contains($request->url(), 'api.test.barion.com') ? 'test' : 'live';

            if ($acceptedEnv !== null && $env === $acceptedEnv) {
                return Http::response(['PaymentId' => 'pay-'.$env.'-1', 'Status' => 'Prepared']);
            }

            return Http::response($this->authFailedPayload(), 401);
        });
    }

    private function fakeBarionProbe(?string $acceptedEnv): void
    {
        Http::fake(function ($request) use ($acceptedEnv) {
            $env = str_contains($request->url(), 'api.test.barion.com') ? 'test' : 'live';

            if ($acceptedEnv !== null && $env === $acceptedEnv) {
                return Http::response(['Errors' => [['ErrorCode' => 'PaymentNotFound']]], 400);
            }

            return Http::response($this->authFailedPayload(), 401);
        });
    }

    /**
     * @return array{Errors: list<array{ErrorCode: string, Description: string}>}
     */
    private function authFailedPayload(): array
    {
        return [
            'Errors' => [[
                'ErrorCode' => 'AuthenticationFailed',
                'Description' => 'The login information provided is incorrect.',
            ]],
        ];
    }
}
