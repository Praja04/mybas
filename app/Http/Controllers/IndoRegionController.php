<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Regency;
use App\Models\Village;
use Illuminate\Http\Request;

class IndoRegionController extends Controller
{
    public function getRegenciesByProvince($province_id)
    {
        $regencies = Regency::where('province_id', $province_id)->get();
        return response()->json(['success' => 1, 'regencies' => $regencies]);
    }

    public function getDistrictsByRegency($regency_id)
    {
        $districts = District::where('regency_id', $regency_id)->get();
        return response()->json(['success' => 1, 'districts' => $districts]);
    }

    public function getVillagesByDistrict($district_id)
    {
        $villages = Village::where('district_id', $district_id)->get();
        return response()->json(['success' => 1, 'villages' => $villages]);
    }
}
