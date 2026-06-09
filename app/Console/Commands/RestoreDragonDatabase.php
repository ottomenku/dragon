<?php

namespace App\Console\Commands;

use App\Models\LegalDocumentSetting;
use App\Models\SiteContentSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class RestoreDragonDatabase extends Command
{
    protected $signature = 'db:restore-dragon
                            {file=dragon_data.sql : A gyökérkönyvtárban lévő SQL dump fájl neve}
                            {--skip-fresh : Ne futtassa a migrate:fresh parancsot (csak adat import)}
                            {--force : Kérdés nélküli migrate:fresh}';

    protected $description = 'Adatbázis helyreállítása dragon_data.sql-ből + új táblák/mezők létrehozása';

    public function handle(): int
    {
        $file = base_path($this->argument('file'));

        if (! File::exists($file)) {
            $this->error("Nem található: {$file}");

            return self::FAILURE;
        }

        if (! $this->option('skip-fresh')) {
            $this->warn('migrate:fresh – minden tábla törlődik és újra létrejön a legfrissebb séma.');
            if (! $this->option('force') && ! $this->confirm('Folytatod?', true)) {
                return self::SUCCESS;
            }

            Artisan::call('migrate:fresh', ['--force' => true]);
            $this->line(Artisan::output());
        }

        $this->info('Adatok importálása...');
        $imported = $this->importData($file);

        if ($imported === false) {
            return self::FAILURE;
        }

        $this->info("Import kész ({$imported} SQL utasítás).");

        LegalDocumentSetting::query()->firstOrCreate([], [
            'aszf_content' => LegalDocumentSetting::defaultAszfContent(),
            'shipping_terms_content' => LegalDocumentSetting::defaultShippingTermsContent(),
            'gdpr_content' => LegalDocumentSetting::defaultGdprContent(),
        ]);

        SiteContentSetting::query()->firstOrCreate([], [
            'contact_content' => SiteContentSetting::defaultContactContent(),
            'footer_content' => SiteContentSetting::defaultFooterContent(),
        ]);

        $this->info('ÁSZF, szállítási feltételek, GDPR és oldaltartalmak alapértelmezett szövegei létrehozva (ha még nem volt).');

        $counts = [
            'users' => DB::table('users')->count(),
            'products' => DB::table('products')->count(),
            'orders' => DB::table('orders')->count(),
            'pickup_points' => DB::table('pickup_points')->count(),
        ];

        $this->table(['Tábla', 'Sorok'], collect($counts)->map(fn ($count, $table) => [$table, $count])->values()->all());

        $this->info('Helyreállítás kész.');

        return self::SUCCESS;
    }

    private function importData(string $file): int|false
    {
        $sql = File::get($file);
        $statements = $this->extractInsertStatements($sql);

        if ($statements === []) {
            $this->error('Nem található importálható INSERT utasítás a fájlban.');

            return false;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $executed = 0;

        try {
            foreach ($statements as $statement) {
                DB::unprepared($statement);
                $executed++;
            }
        } catch (\Throwable $e) {
            $this->error('Import hiba: '.$e->getMessage());
            $this->line('Sikertelen utasítás eleje: '.substr(trim($statement ?? ''), 0, 120).'...');

            return false;
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        return $executed;
    }

    /** @return list<string> */
    private function extractInsertStatements(string $sql): array
    {
        $statements = [];
        $chunks = preg_split('/(?=\RINSERT INTO `)/', $sql) ?: [];

        foreach ($chunks as $chunk) {
            $chunk = trim($chunk);

            if ($chunk === '' || ! str_starts_with($chunk, 'INSERT INTO `')) {
                continue;
            }

            if (preg_match('/^INSERT\s+INTO\s+`migrations`/i', $chunk)) {
                continue;
            }

            if (! preg_match('/^(INSERT INTO `.+?\);)/s', $chunk, $match)) {
                continue;
            }

            $statements[] = $match[1].';';
        }

        return $statements;
    }
}
