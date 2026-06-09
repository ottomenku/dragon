<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LegalDocumentSetting;
use Illuminate\Http\Request;

class LegalDocumentSettingsController extends Controller
{
    public function edit()
    {
        $settings = LegalDocumentSetting::current();

        return view('admin.legal-documents.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'aszf_content' => ['required', 'string'],
            'shipping_terms_content' => ['required', 'string'],
        ]);

        $settings = LegalDocumentSetting::current();
        $settings->aszf_content = $validated['aszf_content'];
        $settings->shipping_terms_content = $validated['shipping_terms_content'];
        $settings->save();

        return redirect()
            ->route('admin.legal-documents.edit')
            ->with('success', 'Az ÁSZF és a szállítási feltételek mentve.');
    }
}
