<?php

namespace App\Services;

use App\Http\Controllers\BaseController;
use App\Models\disease;
use App\Models\User;
use Illuminate\Http\Request;

class DiseaseService extends BaseController
{
    public function getAllClinicalCondetionsInfo()
    {
        return disease::all();
    }

    public function getClinicalCondetionsInfoByDepartment($department)
    {
        return disease::where('Department',$department)->get();
    }
}
