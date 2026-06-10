<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BarionSetting;
use Illuminate\Http\Request;

class BarionSettingsController extends Controller
{
    public function edit()
    {
        $settings = BarionSetting::query()->first() ?? new BarionSetting([
            'payee' => '',
            'use_test' => true,
        ]);

        return view('admin.barion.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $existing = BarionSetting::query()->first();

        $request->merge([
            'payee' => filled($request->input('payee')) ? $request->input('payee') : null,
            'pos_key' => filled($request->input('pos_key')) ? $request->input('pos_key') : null,
            'pixel_id' => filled($request->input('pixel_id')) ? trim($request->input('pixel_id')) : null,
        ]);

        $validated = $request->validate([
            'payee' => ['nullable', 'email', 'max:255'],
            'pos_key' => ['nullable', 'string', 'max:512'],
            'use_test' => ['sometimes', 'boolean'],
            'pixel_id' => ['nullable', 'string', 'max:32'],
            'pixel_footer_enabled' => ['sometimes', 'boolean'],
        ]);

        $row = $existing ?? new BarionSetting;
        $row->payee = $validated['payee'] ?? null;
        $row->use_test = $request->boolean('use_test');
        $row->pixel_id = filled($validated['pixel_id'] ?? null) ? strtoupper($validated['pixel_id']) : null;
        $row->pixel_footer_enabled = $request->boolean('pixel_footer_enabled');

        if (array_key_exists('pos_key', $validated) && filled($validated['pos_key'])) {
            $row->pos_key = $validated['pos_key'];
        }

        $row->save();

        return redirect()
            ->route('admin.barion.edit')
            ->with('success', 'Barion beállítások elmentve.');
    }
}
