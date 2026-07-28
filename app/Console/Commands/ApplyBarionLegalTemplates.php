<?php

namespace App\Console\Commands;

use App\Models\LegalDocumentSetting;
use Illuminate\Console\Command;

class ApplyBarionLegalTemplates extends Command
{
    protected $signature = 'legal:apply-barion-templates {--force : Felülírja a meglévő szövegeket is}';

    protected $description = 'Barion elfogadóhely jóváhagyáshoz szükséges ÁSZF és adatkezelési sablonok alkalmazása';

    public function handle(): int
    {
        $settings = LegalDocumentSetting::current();

        if (! $this->option('force') && ! $this->input->isInteractive()) {
            $this->error('Nem interaktív módban használja a --force kapcsolót.');

            return self::FAILURE;
        }

        if (! $this->option('force') && ! $this->confirm('Felülírja a jelenlegi ÁSZF és adatkezelési szövegeket a Barion-kompatibilis sablonnal?', true)) {
            $this->info('Megszakítva.');

            return self::SUCCESS;
        }

        $settings->aszf_content = LegalDocumentSetting::defaultAszfContent();
        $settings->gdpr_content = LegalDocumentSetting::defaultGdprContent();
        $settings->save();

        $this->info('ÁSZF és adatkezelési tájékoztató frissítve.');
        $this->warn('Ellenőrizze az adminban az adószám sort, és szükség esetén egészítse ki a tényleges adatokkal.');

        return self::SUCCESS;
    }
}
