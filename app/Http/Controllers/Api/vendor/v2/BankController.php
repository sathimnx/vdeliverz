<?php

namespace App\Http\Controllers\Api\vendor\v2;

use App\Bank;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator, DB;

class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shop_id' => 'required'
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response(['status' => false, 'message' => $errors]);
        }
        $banks = Bank::where('shop_id', $request->shop_id)->get(['id as bank_id', 'name', 'bank_name', 'acc_no', 'ifsc', 'city', 'branch']);
        return response(['status' => true, 'message' => 'Banks List.', 'banks' => $banks]);
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
        $validator = Validator::make($request->all(), [
            'shop_id' => 'required',
            'name' => 'required',
            'acc_no' => 'required',
            'city' => 'required',
            'ifsc' => 'required',
            'branch' => 'required',
            'bank_name' => 'required',
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response(['status' => false, 'message' => $errors]);
        }
        Bank::create([
            'name' => $request->name,
            'acc_no' => $request->acc_no,
            'shop_id' => $request->shop_id,
            'city' => $request->city,
            'ifsc' => $request->ifsc,
            'branch' => $request->branch,
            'bank_name' => $request->bank_name
        ]);
        return response(['status' => true, 'message' => 'Bank Created.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function show(Bank $bank)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function edit(Bank $bank)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Bank $bank)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function destroy(Bank $bank)
    {
        //
    }
}
