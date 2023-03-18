<?php

namespace App\Http\Controllers\Api\v2;

use App\Address;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth, Validator, DB;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $addresses = Auth::user()->addresses()->get(['id as address_id', 'address', 'landmark', 'type', 'latitude', 'longitude']);
            return response()->json(['status' => true, 'message' => 'User Addresses', 'address_list' => $addresses]);
        } catch (\Throwable $th) {
            return apiCatchResponse();
            dd($th);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'address' => 'required',
                'type' => 'required',
                'latitude' => 'required',
                'longitude' => 'required',
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            $request->merge(['user_id' => Auth::user()->id]);
            Address::create($request->all());
            return response()->json(['status' => true, 'message' => 'Address created successfully']);
        } catch (\Throwable $th) {
            return apiCatchResponse();
            dd($th);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Address  $address
     * @return \Illuminate\Http\Response
     */
    public function show(Address $address)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Address  $address
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'address_id' => 'required',
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            $address = Address::where('id', $request->address_id)->where('user_id', Auth::user()->id)->first(['id as address_id', 'address', 'landmark', 'type', 'latitude', 'longitude']);
            if(!$address){
                return response()->json(['status' => false, 'message' => 'Address not found']);
            }
            return response()->json(['status' => true, 'message' => 'Address edit', 'address' => $address]);
        } catch (\Throwable $th) {
            return apiCatchResponse();
            //throw $th;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Address  $address
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Address $address)
    {
        try {
            $validator = Validator::make($request->all(), [
                'address_id' => 'required',
                'address' => 'required',
                'type' => 'required',
                'latitude' => 'required',
                'longitude' => 'required',
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            $address = Address::where('id', $request->address_id)->where('user_id', Auth::user()->id)->first();
            if(!$address){
                return response()->json(['status' => false, 'message' => 'Address not found']);
            }
            $address->latitude = isset($request->latitude) ? $request->latitude : $address->latitude;
            $address->longitude = isset($request->longitude) ? $request->longitude : $address->longitude;
            $address->address = isset($request->address) ? $request->address : $address->address;
            $address->type = isset($request->type) ? $request->type : $address->type;
            $address->landmark = isset($request->landmark) ? $request->landmark : null;
            $address->save();
            return response()->json(['status' => true, 'message' => 'Address Updated successfully']);
        } catch (\Throwable $th) {
            return apiCatchResponse();
            throw $th;
        }
    }

    public function destroy(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'address_id' => 'required'
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            $address = Address::where('id', $request->address_id)->where('user_id', Auth::user()->id)->delete();
            if(!$address){
                return response()->json(['status' => false, 'message' => 'Address not found']);
            }
            return response()->json(['status' => true, 'message' => 'Address Deleted successfully']);
        } catch (\Throwable $th) {
            return apiCatchResponse();
            //throw $th;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Address  $address
     * @return \Illuminate\Http\Response
     */
    public function giveRating(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'shop_id' => 'required',
                'rating' => 'required',
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            $review = new \App\Review;
            $review->storeReviewForShop($request->shop_id, $request->rating);
            return response()->json(['status' => true, 'message' => 'Rating Submitted']);
        } catch (\Throwable $th) {
            return apiCatchResponse();
            dd($th);
        }
    }
}
