<?php

namespace App\Http\Controllers\Api\vendor\v3;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CuisineController extends Controller
{
    public function index(){
        $cuisines = \App\Cuisine::select('id as cuisine_id', 'name')->get();
        return response(['status' => true, 'message' => 'Cuisines List', 'cuisines' => $cuisines]);
    }
}
