<?php

namespace App\Http\Controllers\Admin;

use App\Product;
use App\Title;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator, DB;

class TitleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(auth()->user()->hasAnyRole('vendor')){
            $data['titles'] = Title::paginate(10);
        }else{
            $data['titles'] = Title::with('shop')->paginate(10);
            $data['shops'] = \App\Shop::all();
        }
        if (request()->ajax()) {
            if(isset(request()->search) && !empty(request()->search)){
                if(auth()->user()->hasAnyRole('vendor')){
                    $data['titles'] = Title::where('name', 'Like', '%'.request()->search.'%')
                        ->latest('updated_at')->paginate(10);
                }else{
                    $search = request()->search;
                    $data['titles'] = Title::where('name', 'Like', '%'.request()->search.'%')
                        ->latest('updated_at')->paginate(10);
                }
            }

            return view('topping.category_table', array('titles' => $data['titles']))->render();
        }

        return view('topping.category', $data ?? NULL);
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
            // dd($request);
            $validator = Validator::make($request->all(), [
                'shop_id' => 'required',
                'name' => 'required',
            ]);
            if($validator->fails()){
                flash()->error($validator->messages()->first());
                return back()->withInput();
            }
            $title = new Title();
            $title->name = $request->name;
            $title->shop_id = $request->shop_id;
            $title->save();
            flash()->success('Topping Category Created');
            return back();
        } catch (\Throwable $th) {
           return catchResponse();
            dd($th);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Title  $title
     * @return \Illuminate\Http\Response
     */
    public function show(Title $title)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Title  $title
     * @return \Illuminate\Http\Response
     */
    public function edit(Title $title)
    {
        return response($title);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Title  $title
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Title $title)
    {
        try {
            // dd($request);
            $validator = Validator::make($request->all(), [
                'shop_id' => 'required',
                'name' => 'required',
            ]);
            if($validator->fails()){
                flash()->error($validator->messages()->first());
                return back()->withInput();
            }
            $title->name = $request->name;
            $title->shop_id = $request->shop_id;
            $title->save();
            flash()->success('Topping Category Updated');
            return back();
        } catch (\Throwable $th) {
           return catchResponse();
            dd($th);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Title  $title
     * @return \Illuminate\Http\Response
     */
    public function destroy(Title $title)
    {
        $title->delete();
        flash()->info('Category Deleted!');
        return back();
    }
}