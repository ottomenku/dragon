<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethodSetting;
use Illuminate\Http\Request;

class PaymentMethodSettingsController extends Controller
{
    public function edit()
    {
        $settings = PaymentMethodSetting::current();

        return view('admin.payment-methods.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $settings = PaymentMethodSetting::current();
        $settings->cod_enabled = $this->checkboxEnabled($request, 'cod_enabled');
        $settings->otp_enabled = $this->checkboxEnabled($request, 'otp_enabled');
        $settings->barion_enabled = $this->checkboxEnabled($request, 'barion_enabled');
        $settings->save();

        if ($settings->enabledMethods() === []) {
            return redirect()
                ->route('admin.payment-methods.edit')
                ->with('success', 'Mentve. Figyelem: jelenleg egyetlen fizetési mód sincs engedélyezve – a vásárlók nem tudnak rendelést leadni.');
        }

        return redirect()
            ->route('admin.payment-methods.edit')
            ->with('success', 'A fizetési módok beállításai mentve.');
    }

    private function checkboxEnabled(Request $request, string $key): bool
    {
        if (! $request->has($key)) {
            return false;
        }

        $value = $request->input($key);

        if (is_array($value)) {
            $value = end($value);
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
