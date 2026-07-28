<?php

namespace Tests\Feature;

use App\Models\LegalDocumentSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BarionLegalTemplatesTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_aszf_contains_barion_and_dispute_resolution(): void
    {
        $content = LegalDocumentSetting::defaultAszfContent();

        $this->assertStringContainsString('Barion Payment Zrt.', $content);
        $this->assertStringContainsString('Békéltető Testülete', $content);
        $this->assertStringContainsString('ec.europa.eu/consumers/odr', $content);
        $this->assertStringContainsString('Triem Dragonherbs', $content);
    }

    public function test_default_gdpr_contains_barion_pixel(): void
    {
        $content = LegalDocumentSetting::defaultGdprContent();

        $this->assertStringContainsString('Barion Pixel', $content);
        $this->assertStringContainsString('Barion Payment Zrt.', $content);
    }

    public function test_apply_barion_templates_command_updates_settings(): void
    {
        LegalDocumentSetting::current()->update([
            'aszf_content' => '<p>régi</p>',
            'gdpr_content' => '<p>régi gdpr</p>',
        ]);

        $this->artisan('legal:apply-barion-templates --force')
            ->assertSuccessful();

        $settings = LegalDocumentSetting::current()->fresh();
        $this->assertStringContainsString('Barion Payment Zrt.', $settings->aszf_content);
        $this->assertStringContainsString('Barion Pixel', $settings->gdpr_content);
    }
}
