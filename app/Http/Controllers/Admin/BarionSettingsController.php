<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BarionSetting;
use App\Services\BarionPaymentService;
use Illuminate\Http\Request;

class BarionSettingsController extends Controller
{
    public function __construct(
        private BarionPaymentService $barionPayment
    ) {}

    public function edit()
    {
        $settings = BarionSetting::query()->first() ?? new BarionSetting([
            'payee' => '',
            'use_test' => false,
        ]);

        return view('admin.barion.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $existing = BarionSetting::query()->first();

        $request->merge([
            'payee' => filled($request->input('payee')) ? trim((string) $request->input('payee')) : null,
            'pos_key' => filled($request->input('pos_key'))
                ? BarionPaymentService::normalizePosKey((string) $request->input('pos_key'))
                : null,
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

        $flash = $this->applyDetectedEnvironment($row);

        return redirect()
            ->route('admin.barion.edit')
            ->with($flash['type'], $flash['message']);
    }

    public function test(Request $request)
    {
        $settings = BarionSetting::current();
        $posKey = filled($request->input('pos_key'))
            ? BarionPaymentService::normalizePosKey((string) $request->input('pos_key'))
            : $settings?->pos_key;
        $useTest = $request->has('use_test')
            ? $request->boolean('use_test')
            : (bool) ($settings?->use_test);

        $result = $this->barionPayment->verifyConnection($posKey, $useTest);

        return redirect()
            ->route('admin.barion.edit')
            ->with($result['ok'] ? 'success' : 'warning', $result['message']);
    }

    /**
     * @return array{type: string, message: string}
     */
    private function applyDetectedEnvironment(BarionSetting $row): array
    {
        if (! $row->isConfigured()) {
            return ['type' => 'success', 'message' => 'Barion beállítások elmentve.'];
        }

        $detected = $this->barionPayment->detectEnvironment($row->pos_key);

        if ($detected === 'live' && $row->use_test) {
            $row->use_test = false;
            $row->save();

            return [
                'type' => 'success',
                'message' => 'Barion beállítások elmentve. A POSKey az éles környezethez tartozik, ezért a teszt mód ki lett kapcsolva.',
            ];
        }

        if ($detected === 'test' && ! $row->use_test) {
            return [
                'type' => 'warning',
                'message' => 'Mentve, de a POSKey csak a teszt (sandbox) környezetben érvényes. Éles fizetéshez a secure.barion.com → Üzlet → Részletek menüből másolja be a Secret POSKey-t, és válassza az Éles környezetet.',
            ];
        }

        if ($detected === null) {
            return [
                'type' => 'warning',
                'message' => 'Mentve, de a POSKey-t a Barion egyik környezetben sem fogadta el. Ellenőrizze, hogy a Secret kulcsot adta-e meg (nem a nyilvánost), szóköz nélkül.',
            ];
        }

        return ['type' => 'success', 'message' => 'Barion beállítások elmentve. A POSKey a kiválasztott környezetben érvényes.'];
    }
}
