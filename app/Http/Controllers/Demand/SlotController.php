<?php

namespace App\Http\Controllers\Demand;

use App\Slot;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator, DB, Auth;
use Carbon\Carbon;
use App\DemandAuthorizable;

class SlotController extends Controller
{
    use DemandAuthorizable;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(auth()->user()->hasAnyRole('admin')){
            $data['shops'] = \App\Provider::all();
            $data['slots'] = Slot::where('provider_id', '!=', null)->paginate(10);
        }
        if(auth()->user()->hasAnyRole('provider')){
            $data['shop'] = auth()->user()->provider;
            $data['slots'] = Slot::where('provider_id', auth()->user()->provider->id)->paginate(10);
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

                return view('demand.slot.slots_table', array('slots' => $data['slots']))->render();
            }
        return view('demand.slot.slots', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['shops'] = \App\Provider::all();
        if(auth()->user()->hasAnyRole('provider')){
            $data['shop'] = auth()->user()->provider;
            $data['slots'] = auth()->user()->provider->slots()->paginate(10);
        }
        return view('demand.slot.slot_form', $data ?? NULL);
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
            // dd($request);
            foreach ($request->stocks as $key => $value) {
                $slot = new Slot;
                $slot->provider_id = $request->shop_id;
                $slot->count = $value['count'];
                $slot->available = $value['count'];
                $slot->from = Carbon::parse($value['from'])->format('H:i:s');
                $slot->to = Carbon::parse($value['to'])->format('H:i:s');
                $slot->weekdays = implode(',', $request->weekdays);
                $slot->save();
            }

            flash()->success('Slot Created');
            return redirect()->route('demand.slots.index');
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
            $data['shops'] = \App\Provider::all();
            $data['slot'] = $slot;
            if(auth()->user()->hasAnyRole('provider')){
                $data['shop'] = auth()->user()->provider;
                $data['slots'] = auth()->user()->provider->slots()->paginate(10);
            }
            return view('demand.slot.slot_form', $data ?? NULL);
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
            $data['shops'] = \App\Provider::all();
            $data['slot'] = $slot;
            if(auth()->user()->hasAnyRole('provider')){
                $data['shop'] = auth()->user()->provider;
                $data['slots'] = auth()->user()->provider->slots()->paginate(10);
            }
            return view('demand.slot.slot_form', $data ?? NULL);
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
            $reset = $slot->resetAvailability();
            $request->merge(['weekdays' => implode(',', $request->weekdays),
            'from' => Carbon::parse($request->from)->format('H:i:s'), 'to' => Carbon::parse($request->to)->format('H:i:s'),
             'provider_id' => $request->shop_id, 'shop_id' => null, 'count' => $request->count, 'available' => $request->count - $reset]);

            $slot->update($request->all());
            flash()->success('Slot Updated');
            return redirect()->route('demand.slots.index');
        } catch (\Throwable $th) {
            // return catchResponse();
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
                return redirect()->route('demand.slots.index');
            }
            if(auth()->user()->hasAnyRole('admin')){
            $data['shops'] = \App\Provider::all();
            $data['slots'] = Slot::where('provider_id', $id)->paginate(10);
            }
            if(auth()->user()->hasAnyRole('provider')){
                $data['shop'] = auth()->user()->provider;
                $data['slots'] = auth()->user()->provider->slots()->paginate(2);
            }
            return view('demand.slot.slots', $data ?? NULL);
        } catch (\Throwable $th) {
            return catchResponse();
            //throw $th;
        }
    }
}
