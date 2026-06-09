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
        $request->validate([
            'cod_enabled' => ['sometimes', 'boolean'],
            'otp_enabled' => ['sometimes', 'boolean'],
            'barion_enabled' => ['sometimes', 'boolean'],
        ]);

        $settings = PaymentMethodSetting::current();
        $settings->cod_enabled = $request->boolean('cod_enabled');
        $settings->otp_enabled = $request->boolean('otp_enabled');
        $settings->barion_enabled = $request->boolean('barion_enabled');
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
}
