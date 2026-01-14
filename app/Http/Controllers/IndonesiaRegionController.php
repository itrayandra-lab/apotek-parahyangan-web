<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndonesiaCitiesRequest;
use App\Http\Requests\IndonesiaDistrictsRequest;
use App\Http\Requests\IndonesiaVillagesRequest;
use Illuminate\Http\JsonResponse;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\District;
use Laravolt\Indonesia\Models\Province;

class IndonesiaRegionController extends Controller
{
    public function provinces(): JsonResponse
    {
        $provinces = Province::query()
            ->orderBy('name')
            ->get(['code', 'name']);

        return response()->json(['data' => $provinces]);
    }

    public function cities(IndonesiaCitiesRequest $request): JsonResponse
    {
        $province = Province::query()
            ->where('code', (string) $request->string('province_code'))
            ->first();

        if (! $province) {
            return response()->json(['data' => []], 404);
        }

        $cities = $province->cities()
            ->orderBy('name')
            ->get(['code', 'name', 'province_code']);

        return response()->json(['data' => $cities]);
    }

    public function districts(IndonesiaDistrictsRequest $request): JsonResponse
    {
        $city = City::query()
            ->where('code', (string) $request->string('city_code'))
            ->first();

        if (! $city) {
            return response()->json(['data' => []], 404);
        }

        $districts = $city->districts()
            ->orderBy('name')
            ->get(['code', 'name', 'city_code']);

        return response()->json(['data' => $districts]);
    }

    public function villages(IndonesiaVillagesRequest $request): JsonResponse
    {
        $district = District::query()
            ->where('code', (string) $request->string('district_code'))
            ->first();

        if (! $district) {
            return response()->json(['data' => []], 404);
        }

        $villages = $district->villages()
            ->orderBy('name')
            ->get(['code', 'name', 'district_code']);

        return response()->json(['data' => $villages]);
    }
}
