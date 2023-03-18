<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Stock;
use Illuminate\Http\Request;

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
            $product = \App\Product::find($request->product_id);
//            if($product->category->id == 1 && sizeof($product->stocks) > 0){
//                flash()->error("Can't create more than one variant in food.");
//                return back();
//            }
            $stock = new Stock;
            $stock->variant = $request->variant;
            $stock->unit = $request->unit;
            $stock->actual_price = round($request->actual_price, 2);
            $stock->price = round($request->selling_price, 2);
            $stock->size = $request->size;
            $stock->available = $request->available;
            $stock->product_id = $request->product_id;
            $stock->save();
            return redirect()->route('products.index');
        } catch (\Throwable $th) {
            return catchResponse();
            //throw $th;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Stock  $stock
     * @return \Illuminate\Http\Response
     */
    public function show(Stock $stock)
    {
        //
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
    public function update(Request $request, Stock $stock)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Stock  $stock
     * @return \Illuminate\Http\Response
     */
    public function destroy(Stock $stock)
    {
        $stock->delete();
        return back();
    }
}
