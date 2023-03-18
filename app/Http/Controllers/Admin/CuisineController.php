<?php

namespace App\Http\Controllers\Admin;

use App\Cuisine;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator, DB, Auth;

class CuisineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['cuisines'] = \App\Cuisine::latest()->paginate(10);
        if (request()->ajax()) {
            if(isset(request()->search) && !empty(request()->search)){
                    $data['cuisines'] = \App\Cuisine::where('name', 'Like', '%'.request()->search.'%')
                                        ->latest()->paginate(10);
            }
                return view('cuisine.cuisines_table', array('cuisines' => $data['cuisines']))->render();
            }
        return view('cuisine.cuisines', $data ?? null);
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
                'name' => 'required|unique:cuisines,name',
            ]);
            if($validator->fails()) {
                flash()->error($validator->messages()->first());
                return back()->withInput();
            }
        Cuisine::create($request->all());
        flash()->success('Cuisine created Successfully');
        return route('cuisines.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Cuisine  $cuisine
     * @return \Illuminate\Http\Response
     */
    public function show(Cuisine $cuisine)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Cuisine  $cuisine
     * @return \Illuminate\Http\Response
     */
    public function edit(Cuisine $cuisine)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Cuisine  $cuisine
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cuisine $cuisine)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Cuisine  $cuisine
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cuisine $cuisine)
    {
        //
    }
}
