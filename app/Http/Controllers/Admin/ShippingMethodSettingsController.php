<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PickupPoint;
use App\Models\ShippingMethodSetting;
use App\Services\PickupPointSyncService;
use Illuminate\Http\Request;

class ShippingMethodSettingsController extends Controller
{
    public function edit()
    {
        $settings = ShippingMethodSetting::current();
        $pickupPointCounts = PickupPoint::query()
            ->selectRaw('carrier, count(*) as total')
            ->groupBy('carrier')
            ->pluck('total', 'carrier');

        return view('admin.shipping-methods.edit', compact('settings', 'pickupPointCounts'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'mpl_enabled' => ['sometimes', 'boolean'],
            'foxpost_enabled' => ['sometimes', 'boolean'],
            'dhl_enabled' => ['sometimes', 'boolean'],
            'gls_enabled' => ['sometimes', 'boolean'],
            'packeta_enabled' => ['sometimes', 'boolean'],
        ]);

        $settings = ShippingMethodSetting::current();
        $settings->mpl_enabled = $request->boolean('mpl_enabled');
        $settings->foxpost_enabled = $request->boolean('foxpost_enabled');
        $settings->dhl_enabled = $request->boolean('dhl_enabled');
        $settings->gls_enabled = $request->boolean('gls_enabled');
        $settings->packeta_enabled = $request->boolean('packeta_enabled');
        $settings->save();

        if ($settings->enabledMethods() === []) {
            return redirect()
                ->route('admin.shipping-methods.edit')
                ->with('success', 'Mentve. Figyelem: jelenleg egyetlen szállítási mód sincs engedélyezve – a vásárlók nem tudnak rendelést leadni.');
        }

        return redirect()
            ->route('admin.shipping-methods.edit')
            ->with('success', 'A szállítási módok beállításai mentve.');
    }

    public function syncPickupPoints(PickupPointSyncService $syncService)
    {
        $counts = $syncService->syncAll();
        $summary = collect($counts)
            ->map(fn (int $count, string $carrier) => strtoupper($carrier).': '.$count)
            ->implode(', ');

        return redirect()
            ->route('admin.shipping-methods.edit')
            ->with('success', 'Átvételi pontok frissítve – '.$summary.'.');
    }
}
