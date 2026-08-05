<?php

namespace Tests\Feature;

use App\Models\BarionSetting;
use App\Models\PaymentMethodSetting;
use App\Models\User;
use App\Support\BarionBranding;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BarionFooterBrandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_footer_branding_is_independent_of_barion_payment_method(): void
    {
        PaymentMethodSetting::current()->update(['barion_enabled' => false]);

        BarionSetting::query()->create([
            'payee' => 'shop@example.com',
            'pos_key' => 'test-pos-key',
            'use_test' => true,
            'pixel_footer_enabled' => true,
        ]);

        $this->assertTrue(BarionBranding::showFooterBranding());
        $this->assertFalse(BarionBranding::showCheckoutLogos());
    }

    public function test_admin_can_save_completely_empty_barion_settings(): void
    {
        $admin = User::factory()->create(['id' => 1]);

        $this->actingAs($admin)->put(route('admin.barion.update'), [
            'payee' => '',
            'pos_key' => '',
            'pixel_id' => '',
            'pixel_footer_enabled' => '0',
            'use_test' => '0',
        ])->assertRedirect(route('admin.barion.edit'));

        $settings = BarionSetting::current();
        $this->assertNull($settings->payee);
        $this->assertNull($settings->pixel_id);
        $this->assertFalse($settings->pixel_footer_enabled);
    }

    public function test_admin_can_save_pixel_settings_without_payment_credentials(): void
    {
        $admin = User::factory()->create(['id' => 1]);

        $this->actingAs($admin)->put(route('admin.barion.update'), [
            'pixel_id' => 'BP-TEST123456-01',
            'pixel_footer_enabled' => '1',
        ])->assertRedirect(route('admin.barion.edit'));

        $settings = BarionSetting::current();
        $this->assertSame('BP-TEST123456-01', $settings->pixel_id);
        $this->assertTrue($settings->pixel_footer_enabled);
        $this->assertNull($settings->payee);
        $this->assertNull($settings->pos_key);
    }

    public function test_admin_can_toggle_footer_branding_checkbox(): void
    {
        $admin = User::factory()->create(['id' => 1]);

        BarionSetting::query()->create([
            'payee' => 'shop@example.com',
            'pos_key' => 'test-pos-key',
            'use_test' => true,
            'pixel_footer_enabled' => false,
        ]);

        $this->actingAs($admin)->put(route('admin.barion.update'), [
            'payee' => 'shop@example.com',
            'pixel_footer_enabled' => '1',
        ])->assertRedirect(route('admin.barion.edit'));

        $this->assertTrue(BarionSetting::current()->pixel_footer_enabled);
    }

    public function test_welcome_footer_shows_branding_only_when_checkbox_enabled(): void
    {
        PaymentMethodSetting::current()->update(['barion_enabled' => true]);

        BarionSetting::query()->create([
            'payee' => 'shop@example.com',
            'pos_key' => 'test-pos-key',
            'use_test' => true,
            'pixel_footer_enabled' => false,
        ]);

        $this->get(route('welcome'))->assertDontSee('barion-payment-banner-medium-dark.png', false);

        BarionSetting::current()->update(['pixel_footer_enabled' => true]);

        $this->get(route('welcome'))->assertSee('barion-payment-banner-medium-dark.png', false);
    }
}
