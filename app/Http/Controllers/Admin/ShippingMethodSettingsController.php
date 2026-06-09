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
        $rules = [];
        foreach (ShippingMethodSetting::METHODS as $key => $label) {
            $rules[$key.'_fee'] = ['nullable', 'integer', 'min:0', 'max:9999999'];
        }

        $request->validate($rules);

        $settings = ShippingMethodSetting::current();

        foreach (ShippingMethodSetting::METHODS as $key => $label) {
            $enabledColumn = $key.'_enabled';
            $feeColumn = $key.'_fee';
            $settings->{$enabledColumn} = $this->checkboxEnabled($request, $enabledColumn);
            $settings->{$feeColumn} = max(0, (int) $request->input($feeColumn, 0));
        }

        $settings->save();

        if ($settings->enabledMethods() === []) {
            return redirect()
                ->route('admin.shipping-methods.edit')
                ->with('success', 'Mentve. Figyelem: jelenleg egyetlen szállítási mód sincs engedélyezve – a vásárlók nem tudnak rendelést leadni.');
        }

        return redirect()
            ->route('admin.shipping-methods.edit')
            ->with('success', 'A szállítási módok és díjak mentve.');
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

    private function checkboxEnabled(Request $request, string $key): bool
    {
        if (! $request->has($key)) {
            return false;
        }

        $value = $request->input($key);

        if (is_array($value)) {
            $value = end($value);
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
