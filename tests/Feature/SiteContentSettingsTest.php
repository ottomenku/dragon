<?php

namespace Tests\Feature;

use App\Models\LegalDocumentSetting;
use App\Models\SiteContentSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteContentSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_gdpr_content(): void
    {
        $admin = User::factory()->create(['id' => 1]);

        $response = $this->actingAs($admin)->put(route('admin.legal-documents.update'), [
            'aszf_content' => '<p>ASZF</p>',
            'shipping_terms_content' => '<p>Szállítás</p>',
            'gdpr_content' => '<p>GDPR szöveg</p>',
        ]);

        $response->assertRedirect(route('admin.legal-documents.edit'));
        $this->assertSame('<p>GDPR szöveg</p>', LegalDocumentSetting::current()->gdpr_content);
    }

    public function test_admin_can_update_contact_and_footer_content(): void
    {
        $admin = User::factory()->create(['id' => 2]);

        $this->actingAs($admin)->put(route('admin.site-content.contact.update'), [
            'contact_content' => '<p>Kapcsolat tartalom</p>',
        ])->assertRedirect(route('admin.site-content.contact.edit'));

        $this->actingAs($admin)->put(route('admin.site-content.footer.update'), [
            'footer_content' => '<p>Lábléc tartalom</p>',
        ])->assertRedirect(route('admin.site-content.footer.edit'));

        $settings = SiteContentSetting::current();
        $this->assertSame('<p>Kapcsolat tartalom</p>', $settings->contact_content);
        $this->assertSame('<p>Lábléc tartalom</p>', $settings->footer_content);
    }

    public function test_welcome_page_shows_editable_content(): void
    {
        SiteContentSetting::current()->update([
            'contact_content' => '<p>Egyedi kapcsolat</p>',
            'footer_content' => '<p>Egyedi lábléc</p>',
        ]);

        LegalDocumentSetting::current()->update([
            'gdpr_content' => '<p>Egyedi GDPR</p>',
        ]);

        $response = $this->get(route('welcome'));

        $response->assertOk();
        $response->assertSee('Egyedi kapcsolat', false);
        $response->assertSee('Egyedi lábléc', false);
        $response->assertSee('Egyedi GDPR', false);
        $response->assertSee('data-bs-target="#contactModal"', false);
    }
}
