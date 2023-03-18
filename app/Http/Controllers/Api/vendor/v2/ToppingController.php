<?php

namespace App\Http\Controllers\Api\vendor\v2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Topping;
use Validator, DB;

class ToppingController extends Controller
{
    public function show(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'topping_id' => 'required'
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response(['status' => false, 'message' => $errors]);
        }
        $topping = Topping::where('id', $request->topping_id)->where('active', 1)->first(['id as topping_id', 'name', 'product_id', 'title_id', 'variety', 'available', 'price']);
        $titles = \App\Title::where('active', 1)->get(['id as title_id', 'name']);
        if(!$topping){
            return response(['status' => false, 'message' => 'Topping Not Found.']);
        }
        return response(['status' => true, 'message' => 'Topping Details', 'topping' => $topping, 'titles' => $titles]);
    }

    public function titles(){
        $titles = \App\Title::where('active', 1)->get(['id as title_id', 'name']);
        return response(['status' => true, 'message' => 'Titles List.', 'titles' => $titles]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title_id' => 'required',
            'product_id' => 'required',
            'name' => 'required',
            'variety' => 'required',
            'available' => 'required',
            'price' => 'required'
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response(['status' => false, 'message' => $errors]);
        }
        $topping = new \App\Topping();
        $topping->name = $request->name;
        $topping->title_id = $request->title_id;
        $topping->title_name = \App\Title::find($request->title_id)->pluck('name')->first();
        $topping->variety = $request->variety;
        $topping->price = number_format($request->price, 2);
        $topping->available = $request->available;
        $topping->product_id = $request->product_id;
        $topping->save();
        return response(['status' => true, 'message' => 'Topping Created.']);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'topping_id' => 'required',
            'title_id' => 'required',
            'product_id' => 'required',
            'name' => 'required',
            'variety' => 'required',
            'available' => 'required',
            'price' => 'required'
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response(['status' => false, 'message' => $errors]);
        }
        $topping = \App\Topping::find($request->topping_id);
        $topping->name = $request->name;
        $topping->title_id = $request->title_id;
        $topping->title_name = \App\Title::find($request->title_id)->pluck('name')->first();
        $topping->variety = $request->variety;
        $topping->price = round($request->price, 2);
        $topping->available = $request->available;
        $topping->product_id = $request->product_id;
        $topping->save();
        return response(['status' => true, 'message' => 'Topping Updated.']);
    }

    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'topping_id' => 'required'
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response(['status' => false, 'message' => $errors]);
        }
        $topping = Topping::find($request->topping_id);
        if($topping && $topping->delete()){
            return response(['status' => true, 'message' => 'Topping Deleted.']);
        }
        return response(['status' => false, 'message' => 'Topping Not Found.']);
    }
}