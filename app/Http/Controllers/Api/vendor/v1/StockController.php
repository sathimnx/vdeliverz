<?php

namespace App\Http\Controllers\Api\vendor\v1;

use App\Http\Controllers\Controller;
use App\Stock;
use Illuminate\Http\Request;
use Validator, DB;
use App\Http\Resources\StockResource;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $products = auth('api')->user()->products()->get(['products.id as product_id', 'products.name']);
        $products = auth('api')->user()->products;
        foreach ($products as $key => $value) {
            $data[] = [
                'product_id' => $value->id,
                'name' => $value->name
            ];
        }
        return response(['status' => true, 'message' => 'Products List', 'products' => $data ?? NULL]);

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
                'product_id' => 'required',
                'actual_price' => 'required',
                'selling_price' => 'required',
                'available' => 'required',
                'unit' => 'required_without:size',
                'variant' => 'required_without:size',
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response(['status' => false, 'message' => $errors]);
            }
            $stock = new Stock;
            $stock->variant = $request->variant;
            $stock->unit = $request->unit;
            $stock->size = $request->size;
            $stock->actual_price = $request->actual_price;
            $stock->price = $request->selling_price;
            $stock->available = $request->available;
            $stock->product_id = $request->product_id;
            $stock->save();
            return response(['status' => true, 'message' => 'Stock Created']);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Stock  $stock
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'stock_id' => 'required'
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response(['status' => false, 'message' => $errors]);
        }
        $stock = Stock::find($request->stock_id);
        if(!$stock){
            return response(['status' => false, 'message' => 'Stock Not Found.']);
        }
        return response(['status' => true, 'message' => 'Stock Details', 'stock' => new StockResource($stock)]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Stock  $stock
     * @return \Illuminate\Http\Response
     */
    public function edit(Stock $stock)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Stock  $stock
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'stock_id' => 'required',
            'product_id' => 'required',
            'actual_price' => 'required',
            'selling_price' => 'required',
            'available' => 'required',
            'unit' => 'required_without:size',
            'variant' => 'required_without:size',
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response(['status' => false, 'message' => $errors]);
        }
        $stock = Stock::find($request->stock_id);
        if(!$stock){
            return response(['status' => false, 'message' => 'Stock Not Found.']);
        }
        $stock->variant = $request->variant;
        $stock->unit = $request->unit;
        $stock->size = $request->size;
        $stock->actual_price = $request->actual_price;
        $stock->price = $request->selling_price;
        $stock->available = $request->available;
        $stock->product_id = $request->product_id;
        $stock->save();
        return response(['status' => true, 'message' => 'Stock Updated.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Stock  $stock
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'stock_id' => 'required'
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response(['status' => false, 'message' => $errors]);
        }
        $stock = Stock::find($request->stock_id);
        if($stock && $stock->delete()){
            return response(['status' => true, 'message' => 'Stock Deleted.']);
        }
        return response(['status' => false, 'message' => 'Stock Not Found.']);
    }
}
