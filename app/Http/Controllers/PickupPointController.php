<?php

namespace App\Http\Controllers;

use App\Models\PickupPoint;
use App\Models\ShippingMethodSetting;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PickupPointController extends Controller
{
    public function index(Request $request)
    {
        $enabledCarriers = ShippingMethodSetting::enabledMethodKeys();

        $data = $request->validate([
            'carrier' => ['required', Rule::in($enabledCarriers)],
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $hasPickup = PickupPoint::query()
            ->where('carrier', $data['carrier'])
            ->exists();

        if (! $hasPickup) {
            return response()->json([
                'points' => [],
                'has_pickup' => false,
            ]);
        }

        $query = PickupPoint::query()
            ->where('carrier', $data['carrier'])
            ->orderBy('city')
            ->orderBy('name');

        if (! empty($data['q'])) {
            $term = '%'.$data['q'].'%';
            $query->where(function ($builder) use ($term) {
                $builder
                    ->where('name', 'like', $term)
                    ->orWhere('city', 'like', $term)
                    ->orWhere('zip', 'like', $term)
                    ->orWhere('address', 'like', $term);
            });
        }

        $points = $query
            ->limit(80)
            ->get(['external_id', 'name', 'address', 'city', 'zip', 'point_type'])
            ->map(fn (PickupPoint $point) => [
                'id' => $point->external_id,
                'label' => $point->displayLabel(),
                'name' => $point->name,
                'address' => $point->fullAddress(),
                'type' => $point->point_type,
            ])
            ->values();

        return response()->json([
            'points' => $points,
            'has_pickup' => true,
        ]);
    }
}
