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

        $validated = $request->validate([
            'payee' => ['required', 'email', 'max:255'],
            'pos_key' => [
                $existing && filled($existing->pos_key) ? 'nullable' : 'required',
                'string',
                'max:512',
            ],
            'use_test' => ['sometimes', 'boolean'],
            'pixel_id' => ['nullable', 'string', 'max:32', 'regex:/^BP-[A-Z0-9]+-\d{2}$/i'],
        ]);

        $row = $existing ?? new BarionSetting;
        $row->payee = $validated['payee'];
        $row->use_test = $request->boolean('use_test');
        $row->pixel_id = filled($validated['pixel_id'] ?? null) ? strtoupper(trim($validated['pixel_id'])) : null;

        if (! empty($validated['pos_key'])) {
            $row->pos_key = $validated['pos_key'];
        }

        $row->save();

        return redirect()
            ->route('admin.barion.edit')
            ->with('success', 'Barion beállítások elmentve.');
    }
}
