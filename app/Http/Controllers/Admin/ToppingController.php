<?php

namespace App\Http\Controllers\Admin;

use App\Topping;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ToppingController extends Controller
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
//            dd($request);
            foreach ($request->toppings as $key => $value) {
                $stock = new \App\Topping();
                $stock->name = $value['name'];
                $stock->title_id = $value['title_id'];
                $stock->title_name = \App\Title::find($value['title_id'])->pluck('name')->first();
                $stock->variety = $value['variety'];
                $stock->price = round($value['price'], 2);
                $stock->available = $value['available'];
                $stock->product_id = $request->product_id;
                $stock->save();
            }
            flash()->success('Toppings Created');
            return back();
        } catch (\Throwable $th) {
           return catchResponse();
            dd($th);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Topping  $topping
     * @return \Illuminate\Http\Response
     */
    public function show(Topping $topping)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Topping  $topping
     * @return \Illuminate\Http\Response
     */
    public function edit(Topping $topping)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Topping  $topping
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Topping $topping)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Topping  $topping
     * @return \Illuminate\Http\Response
     */
    public function destroy(Topping $topping)
    {
        $topping->delete();
        flash()->info('Toppings Deleted.');
        return back();
    }
}