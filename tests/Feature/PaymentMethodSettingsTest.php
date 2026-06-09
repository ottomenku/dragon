<?php

namespace Tests\Feature;

use App\Models\PaymentMethodSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentMethodSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_payment_method_settings(): void
    {
        $admin = User::factory()->create(['id' => 1]);

        PaymentMethodSetting::query()->create([
            'cod_enabled' => true,
            'otp_enabled' => false,
            'barion_enabled' => false,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.payment-methods.update'), [
            'cod_enabled' => '0',
            'otp_enabled' => '1',
            'barion_enabled' => '1',
        ]);

        $response->assertRedirect(route('admin.payment-methods.edit'));
        $response->assertSessionHas('success');

        $settings = PaymentMethodSetting::current();
        $this->assertFalse($settings->cod_enabled);
        $this->assertTrue($settings->otp_enabled);
        $this->assertTrue($settings->barion_enabled);
    }

    public function test_unchecked_checkboxes_are_saved_as_disabled(): void
    {
        $admin = User::factory()->create(['id' => 1]);

        PaymentMethodSetting::query()->create([
            'cod_enabled' => true,
            'otp_enabled' => true,
            'barion_enabled' => true,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.payment-methods.update'), []);

        $response->assertRedirect(route('admin.payment-methods.edit'));
        $response->assertSessionHas('success');

        $settings = PaymentMethodSetting::current();
        $this->assertFalse($settings->cod_enabled);
        $this->assertFalse($settings->otp_enabled);
        $this->assertFalse($settings->barion_enabled);
    }

    public function test_duplicate_checkbox_values_are_handled(): void
    {
        $admin = User::factory()->create(['id' => 1]);

        PaymentMethodSetting::query()->create([
            'cod_enabled' => false,
            'otp_enabled' => false,
            'barion_enabled' => false,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.payment-methods.update'), [
            'cod_enabled' => ['0', '1'],
            'barion_enabled' => ['0', '1'],
        ]);

        $response->assertRedirect(route('admin.payment-methods.edit'));
        $response->assertSessionHas('success');

        $settings = PaymentMethodSetting::current();
        $this->assertTrue($settings->cod_enabled);
        $this->assertFalse($settings->otp_enabled);
        $this->assertTrue($settings->barion_enabled);
    }
}
