<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RajaOngkirService
{
    private string $baseUrl;

    private string $apiKey;

    private int $timeout;

    public function __construct()
    {
        $this->baseUrl = config('rajaongkir.base_url');
        $this->apiKey = config('rajaongkir.api_key');
        $this->timeout = config('rajaongkir.timeout', 30);
    }

    /**
     * Search destination by keyword (city/district name)
     */
    public function searchDestination(string $keyword): Collection
    {
        $cacheKey = 'rajaongkir_destination_'.md5($keyword);

        return Cache::remember($cacheKey, now()->addHours(24), function () use ($keyword) {
            try {
                $response = Http::timeout($this->timeout)
                    ->withHeaders(['key' => $this->apiKey])
                    ->get("{$this->baseUrl}/destination/domestic-destination", [
                        'search' => $keyword,
                    ]);

                if ($response->successful()) {
                    $data = $response->json('data', []);

                    return collect($data);
                }

                Log::warning('RajaOngkir search destination failed', [
                    'keyword' => $keyword,
                    'response' => $response->json(),
                ]);

                return collect();
            } catch (ConnectionException $e) {
                Log::error('RajaOngkir connection error', ['error' => $e->getMessage()]);

                return collect();
            }
        });
    }

    /**
     * Calculate domestic shipping cost
     */
    public function calculateCost(int $originId, int $destinationId, int $weight, array $couriers = []): Collection
    {
        if (empty($couriers)) {
            $couriers = array_keys(config('rajaongkir.couriers', []));
        }

        $courierString = implode(':', $couriers);

        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'key' => $this->apiKey,
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ])
                ->asForm()
                ->post("{$this->baseUrl}/calculate/domestic-cost", [
                    'origin' => $originId,
                    'destination' => $destinationId,
                    'weight' => $weight,
                    'courier' => $courierString,
                    'price' => 'lowest',
                ]);

            if ($response->successful()) {
                $data = $response->json('data', []);

                return collect($data)->map(function ($item) {
                    return [
                        'courier_code' => $item['code'] ?? '',
                        'courier_name' => $item['name'] ?? '',
                        'service' => $item['service'] ?? '',
                        'description' => $item['description'] ?? '',
                        'cost' => $item['cost'] ?? 0,
                        'etd' => $item['etd'] ?? '-',
                    ];
                });
            }

            Log::warning('RajaOngkir calculate cost failed', [
                'origin' => $originId,
                'destination' => $destinationId,
                'weight' => $weight,
                'response' => $response->json(),
            ]);

            return collect();
        } catch (ConnectionException $e) {
            Log::error('RajaOngkir connection error', ['error' => $e->getMessage()]);

            return collect();
        }
    }

    /**
     * Get origin city ID from config or site settings
     */
    public function getOriginId(): ?int
    {
        $originId = config('rajaongkir.origin.city_id');

        if ($originId) {
            return (int) $originId;
        }

        $originName = $this->getOriginCityName();
        if ($originName) {
            $destinations = $this->searchDestination($originName);
            $first = $destinations->first();

            return $first['id'] ?? null;
        }

        return null;
    }

    /**
     * Get origin city name from site settings or config
     */
    public function getOriginCityName(): string
    {
        $siteSetting = \App\Models\SiteSetting::getValue('shipping.origin_city');

        return $siteSetting ?: config('rajaongkir.origin.city_name', 'BANDUNG');
    }

    /**
     * Check if API is configured
     */
    public function isConfigured(): bool
    {
        return ! empty($this->apiKey);
    }
}
