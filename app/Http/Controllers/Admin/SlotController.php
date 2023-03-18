<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Slot;
use Illuminate\Http\Request;
use Validator, DB, Auth;
use Carbon\Carbon;
use App\Authorizable;

class SlotController extends Controller
{
    use Authorizable;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(auth()->user()->hasAnyRole('admin')){
            $data['shops'] = \App\Shop::all();
            $data['slots'] = Slot::with('shop')->where('shop_id', '!=', null)->paginate(10);
        }
        if(auth()->user()->hasAnyRole('vendor')){
            $data['shop'] = auth()->user()->shop;
            $data['slots'] = Slot::where('shop_id', auth()->user()->shop->id)->paginate(10);
        }
        if (request()->ajax()) {
            // if(isset(request()->search) && !empty(request()->search)){
            //     if(auth()->user()->hasAnyRole('vendor')){
            //         $data['slots'] = Slot::where('shop_id', Auth::user()->shop->id)->where('from', 'Like', '%'.request()->search.'%')
            //                         ->latest()->paginate(2);
            //     }else{
            //         $data['slots'] = Slot::where('from', 'Like', '%'.request()->search.'%')
            //                             ->orWhere('to', 'Like','%'.request()->search.'%')
            //                             ->latest()->paginate(10);
            //     }
            // }

                return view('slot.slots_table', array('slots' => $data['slots']))->render();
            }
        return view('slot.slots', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['shops'] = \App\Shop::all();
        if(auth()->user()->hasAnyRole('vendor')){
            $data['shop'] = auth()->user()->shop;
            $data['slots'] = auth()->user()->shop->slots()->paginate(10);
        }
        return view('slot.slot_form', $data ?? NULL);
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
                'shop_id' => 'required',
                'stocks' => 'required',
                'weekdays' => 'required',
            ]);
            if($validator->fails()){
                flash()->error($validator->messages()->first());
                return back()->withInput();
            }
            foreach ($request->stocks as $key => $value) {
                $slot = new Slot;
                $slot->shop_id = $request->shop_id;
                $slot->from = Carbon::parse($value['from'])->format('H:i:s');
                $slot->to = Carbon::parse($value['to'])->format('H:i:s');
                $slot->weekdays = implode(',', $request->weekdays);
                $slot->save();
            }

            flash()->success('Slot Created');
            return redirect()->route('slots.index');
        } catch (\Throwable $th) {
            return catchResponse();
            dd($th);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Slot  $slot
     * @return \Illuminate\Http\Response
     */
    public function show(Slot $slot)
    {
        try {
            $data['shops'] = \App\Shop::all();
            $data['slot'] = $slot;
            if(auth()->user()->hasAnyRole('vendor')){
                $data['shop'] = auth()->user()->shop;
                $data['slots'] = auth()->user()->shop->slots()->paginate(10);
            }
            return view('slot.slot_form', $data ?? NULL);
        } catch (\Throwable $th) {
            return catchResponse();
            dd($th);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Slot  $slot
     * @return \Illuminate\Http\Response
     */
    public function edit(Slot $slot)
    {
        try {
            $data['shops'] = \App\Shop::all();
            $data['slot'] = $slot;
            if(auth()->user()->hasAnyRole('vendor')){
                $data['shop'] = auth()->user()->shop;
                $data['slots'] = auth()->user()->shop->slots()->paginate(10);
            }
            return view('slot.slot_form', $data ?? NULL);
        } catch (\Throwable $th) {
            return catchResponse();
            dd($th);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Slot  $slot
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Slot $slot)
    {
        try {
            $validator = Validator::make($request->all(), [
                'shop_id' => 'required',
                'from' => 'required',
                'to' => 'required',
                'weekdays' => 'required',
            ]);
            if($validator->fails()){
                flash()->error($validator->messages()->first());
                return back()->withInput();
            }
            $request->merge(['weekdays' => implode(',', $request->weekdays), 'from' => Carbon::parse($request->from)->format('H:i:s'), 'to' => Carbon::parse($request->to)->format('H:i:s')]);
            $slot->update($request->all());
            flash()->success('Slot Updated');
            return redirect()->route('slots.index');
        } catch (\Throwable $th) {
            return catchResponse();
            dd($th);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Slot  $slot
     * @return \Illuminate\Http\Response
     */
    public function destroy(Slot $slot)
    {
        $slot->delete();
        flash()->info('Slot Deleted');
        return back();
    }


    public function filterSlot($id){
        try {
            if($id == 'all'){
                return redirect()->route('slots.index');
            }
            if(auth()->user()->hasAnyRole('admin')){
            $data['shops'] = \App\Shop::all();
            $data['slots'] = Slot::where('shop_id', $id)->paginate(10);
            }
            if(auth()->user()->hasAnyRole('vendor')){
                $data['shop'] = auth()->user()->shop;
                $data['slots'] = auth()->user()->shop->slots()->paginate(10);
            }
            return view('slot.slots', $data ?? NULL);
        } catch (\Throwable $th) {
            return catchResponse();
            //throw $th;
        }
    }
}