<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebshopSetting;
use Illuminate\Http\Request;

class WebshopSettingsController extends Controller
{
    public function edit()
    {
        $settings = WebshopSetting::current();

        return view('admin.webshop.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'enabled' => ['sometimes', 'boolean'],
        ]);

        $settings = WebshopSetting::current();
        $settings->enabled = $request->boolean('enabled');
        $settings->save();

        return redirect()
            ->route('admin.webshop.edit')
            ->with('success', $settings->enabled
                ? 'A webshop bekapcsolva – a látogatók elérhetik.'
                : 'A webshop kikapcsolva – csak adminok férhetnek hozzá.');
    }
}
