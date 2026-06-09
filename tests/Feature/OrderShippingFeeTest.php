<?php

namespace Tests\Feature;

use App\Models\PaymentMethodSetting;
use App\Models\ShippingMethodSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderShippingFeeTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_requires_terms_and_includes_shipping_fee(): void
    {
        PaymentMethodSetting::query()->create([
            'cod_enabled' => true,
            'otp_enabled' => false,
            'barion_enabled' => false,
        ]);

        ShippingMethodSetting::query()->create([
            'mpl_enabled' => true,
            'foxpost_enabled' => false,
            'dhl_enabled' => false,
            'gls_enabled' => false,
            'packeta_enabled' => false,
            'mpl_fee' => 1500,
        ]);

        $payload = [
            'name' => 'Teszt Vásárló',
            'phone' => '+36301234567',
            'shipping_address' => '1234 Budapest, Teszt utca 1.',
            'billing_address' => '1234 Budapest, Teszt utca 1.',
            'items' => [
                ['id' => 1, 'title' => 'Termék', 'price' => 2000, 'qty' => 2],
            ],
            'total_price' => 5500,
            'terms_accepted' => true,
            'payment_method' => 'cod',
            'shipping_method' => 'mpl',
            'delivery_type' => 'home',
        ];

        $response = $this->postJson(route('orders.store'), $payload);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('orders', [
            'name' => 'Teszt Vásárló',
            'total_price' => 5500,
            'shipping_fee' => 1500,
            'shipping_method' => 'mpl',
        ]);
    }

    public function test_order_rejects_incorrect_total_with_shipping_fee(): void
    {
        PaymentMethodSetting::query()->create([
            'cod_enabled' => true,
            'otp_enabled' => false,
            'barion_enabled' => false,
        ]);

        ShippingMethodSetting::query()->create([
            'mpl_enabled' => true,
            'foxpost_enabled' => false,
            'dhl_enabled' => false,
            'gls_enabled' => false,
            'packeta_enabled' => false,
            'mpl_fee' => 1500,
        ]);

        $response = $this->postJson(route('orders.store'), [
            'name' => 'Teszt Vásárló',
            'phone' => '+36301234567',
            'shipping_address' => '1234 Budapest, Teszt utca 1.',
            'billing_address' => '1234 Budapest, Teszt utca 1.',
            'items' => [
                ['id' => 1, 'title' => 'Termék', 'price' => 2000, 'qty' => 1],
            ],
            'total_price' => 2000,
            'terms_accepted' => true,
            'payment_method' => 'cod',
            'shipping_method' => 'mpl',
            'delivery_type' => 'home',
        ]);

        $response->assertStatus(422);
    }
}
