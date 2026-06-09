<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteContentSetting;
use Illuminate\Http\Request;

class SiteContentSettingsController extends Controller
{
    public function editContact()
    {
        $settings = SiteContentSetting::current();

        return view('admin.site-content.contact', compact('settings'));
    }

    public function updateContact(Request $request)
    {
        $validated = $request->validate([
            'contact_content' => ['required', 'string'],
        ]);

        $settings = SiteContentSetting::current();
        $settings->contact_content = $validated['contact_content'];
        $settings->save();

        return redirect()
            ->route('admin.site-content.contact.edit')
            ->with('success', 'A kapcsolat oldal tartalma mentve.');
    }

    public function editFooter()
    {
        $settings = SiteContentSetting::current();

        return view('admin.site-content.footer', compact('settings'));
    }

    public function updateFooter(Request $request)
    {
        $validated = $request->validate([
            'footer_content' => ['required', 'string'],
        ]);

        $settings = SiteContentSetting::current();
        $settings->footer_content = $validated['footer_content'];
        $settings->save();

        return redirect()
            ->route('admin.site-content.footer.edit')
            ->with('success', 'A lábléc tartalma mentve.');
    }
}
