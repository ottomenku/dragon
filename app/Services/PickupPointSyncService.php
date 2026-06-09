<?php

namespace App\Services;

use App\Models\PickupPoint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use SimpleXMLElement;

class PickupPointSyncService
{
    /** @return array<string, int> */
    public function syncAll(): array
    {
        return [
            'foxpost' => $this->syncFoxpost(),
            'packeta' => $this->syncPacketa(),
            'gls' => $this->syncGls(),
            'mpl' => $this->syncMpl(),
            'dhl' => 0,
        ];
    }

    public function syncFoxpost(): int
    {
        $items = $this->fetchFoxplusItems(['FOXPOST A-BOX', 'FOXPOST Z-BOX']);

        return $this->replaceCarrierPoints('foxpost', $items);
    }

    public function syncPacketa(): int
    {
        $items = $this->fetchFoxplusItems(['Packeta Z-Pont', 'Packeta Z-BOX']);

        return $this->replaceCarrierPoints('packeta', $items);
    }

    public function syncGls(): int
    {
        $response = Http::timeout(120)->get('https://map.gls-hungary.com/data/deliveryPoints/hu.json');
        if (! $response->ok()) {
            throw new \RuntimeException('A GLS átvételi pontok letöltése sikertelen.');
        }

        $payload = $response->json();
        $items = [];

        foreach ($payload['items'] ?? [] as $item) {
            $contact = $item['contact'] ?? [];
            $items[] = [
                'external_id' => (string) ($item['id'] ?? ''),
                'name' => (string) ($item['name'] ?? 'GLS pont'),
                'address' => (string) ($contact['address'] ?? ''),
                'city' => $this->normalizeCity((string) ($contact['city'] ?? '')),
                'zip' => (string) ($contact['postalCode'] ?? ''),
                'latitude' => $item['location'][0] ?? null,
                'longitude' => $item['location'][1] ?? null,
                'point_type' => (string) ($item['type'] ?? 'parcel-shop'),
            ];
        }

        return $this->replaceCarrierPoints('gls', $items);
    }

    public function syncMpl(): int
    {
        $url = config('services.mpl.postinfo_url');
        if (! $url) {
            return 0;
        }

        $response = Http::timeout(120)->get($url);
        if (! $response->ok()) {
            throw new \RuntimeException('Az MPL átvételi pontok letöltése sikertelen.');
        }

        $xml = new SimpleXMLElement($response->body());
        $items = [];

        foreach ($xml->post as $post) {
            $externalId = trim((string) ($post->ID ?? ''));
            if ($externalId === '') {
                continue;
            }

            $serviceType = strtoupper(trim((string) ($post->ServicePointType ?? '')));
            $pointType = match ($serviceType) {
                'CS' => 'locker',
                'PM' => 'post_office',
                default => 'shop',
            };

            $street = trim((string) ($post->street ?? ''));
            $streetType = trim((string) ($post->street_type ?? ''));
            $houseNumber = trim((string) ($post->house_number ?? ''));
            $address = trim(implode(' ', array_filter([$street, $streetType, $houseNumber])));

            $items[] = [
                'external_id' => $externalId,
                'name' => trim((string) ($post->name ?? 'MPL átvételi pont')),
                'address' => $address !== '' ? $address : trim((string) ($post->address ?? '')),
                'city' => $this->normalizeCity(trim((string) ($post->city ?? ''))),
                'zip' => trim((string) ($post->zip ?? '')),
                'latitude' => $this->nullableFloat((string) ($post->geolat ?? '')),
                'longitude' => $this->nullableFloat((string) ($post->geolng ?? '')),
                'point_type' => $pointType,
            ];
        }

        return $this->replaceCarrierPoints('mpl', $items);
    }

    /** @param list<string> $variants */
    private function fetchFoxplusItems(array $variants): array
    {
        $response = Http::timeout(120)->get('https://cdn.foxpost.hu/foxplus.json');
        if (! $response->ok()) {
            throw new \RuntimeException('A Foxpost/Packeta átvételi pontok letöltése sikertelen.');
        }

        $items = [];

        foreach ($response->json() as $row) {
            $variant = (string) ($row['variant'] ?? '');
            if (! in_array($variant, $variants, true)) {
                continue;
            }

            $externalId = trim((string) ($row['operator_id'] ?? ''));
            if ($externalId === '') {
                $externalId = trim((string) ($row['place_id'] ?? ''));
            }
            if ($externalId === '') {
                continue;
            }

            $items[] = [
                'external_id' => $externalId,
                'name' => trim((string) ($row['name'] ?? 'Átvételi pont')),
                'address' => trim((string) ($row['address'] ?? $row['street'] ?? '')),
                'city' => $this->normalizeCity(trim((string) ($row['city'] ?? ''))),
                'zip' => trim((string) ($row['zip'] ?? '')),
                'latitude' => $this->nullableFloat((string) ($row['geolat'] ?? '')),
                'longitude' => $this->nullableFloat((string) ($row['geolng'] ?? '')),
                'point_type' => str_contains(strtolower($variant), 'z-box') || str_contains(strtolower($variant), 'a-box')
                    ? 'locker'
                    : 'shop',
            ];
        }

        return $items;
    }

    /** @param list<array<string, mixed>> $items */
    private function replaceCarrierPoints(string $carrier, array $items): int
    {
        $now = now();
        $rows = [];

        foreach ($items as $item) {
            $externalId = trim((string) ($item['external_id'] ?? ''));
            if ($externalId === '') {
                continue;
            }

            $rows[$carrier.'|'.$externalId] = [
                'carrier' => $carrier,
                'external_id' => $externalId,
                'name' => Str::limit(trim((string) ($item['name'] ?? 'Átvételi pont')), 255, ''),
                'address' => Str::limit(trim((string) ($item['address'] ?? '')), 255, ''),
                'city' => Str::limit($this->normalizeCity((string) ($item['city'] ?? '')), 255, ''),
                'zip' => Str::limit(trim((string) ($item['zip'] ?? '')), 16, ''),
                'latitude' => $item['latitude'] ?? null,
                'longitude' => $item['longitude'] ?? null,
                'point_type' => Str::limit(trim((string) ($item['point_type'] ?? 'locker')), 32, ''),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        return DB::transaction(function () use ($carrier, $rows) {
            PickupPoint::query()->where('carrier', $carrier)->delete();

            foreach (array_chunk(array_values($rows), 500) as $chunk) {
                PickupPoint::query()->insert($chunk);
            }

            return count($rows);
        });
    }

    private function normalizeCity(string $city): string
    {
        $city = trim($city);
        if ($city === '') {
            return '';
        }

        return mb_convert_case(mb_strtolower($city, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
    }

    private function nullableFloat(string $value): ?float
    {
        $value = trim(str_replace(',', '.', $value));

        return is_numeric($value) ? (float) $value : null;
    }
}
