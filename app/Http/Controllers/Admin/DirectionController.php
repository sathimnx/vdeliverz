<?php

namespace App\Http\Controllers\Admin;

use App\Address;
use App\AddressShop;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DirectionController extends Controller
{
    public function index(){
        $data['addresses'] = AddressShop::with('address', 'shop', 'address.user')->where('alg', 0)->paginate(10);
        if (request()->ajax()) {
            if(isset(request()->search) && !empty(request()->search)){
                $search = request()->search;
                    $data['addresses'] = AddressShop::
                    //     whereHas('address.user', function($q) use($search){
                    //     $q->where('name', 'Like', '%'.$search.'%');
                    // })
                    whereHas('shop', function($s) use($search){
                        $s->where('name', 'Like', '%'.$search.'%');
                    })->where('alg', 0)->paginate(10);
            }
                return view('directions.address_table', array('addresses' => $data['addresses']))->render();
            }
        return view('directions.address', $data);
    }
}