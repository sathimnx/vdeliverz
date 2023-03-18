<?php

namespace App\Http\Controllers\Api\vendor\v3;

use App\Http\Controllers\Controller;
use App\Slot;
use Illuminate\Http\Request;
use Validator, DB;
use App\Http\Resources\SlotsResource;
use Carbon\Carbon;

class SlotController extends Controller
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
        $slots = Slot::where('shop_id', $request->shop_id)->with('shop')->paginate(10);

        return response(['status' => true, 'message' => 'Slots data', 'slots' => SlotsResource::collection($slots)]);
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
            'from' => 'required|date_format:H:i:s',
            'to' => 'required|date_format:H:i:s',
            'weekdays' => 'required|array',
            'shop_id' => 'required'
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response(['status' => false, 'message' => $errors]);
        }
        $slot = new Slot;
        $slot->shop_id = $request->shop_id;
        $slot->from = Carbon::parse($request->from)->format('H:i:s');
        $slot->to = Carbon::parse($request->to)->format('H:i:s');
        $slot->weekdays = str_replace('"', '', implode(',', $request->weekdays));
        $slot->save();
        return response(['status' => true, 'message' => 'Slot Created.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Slot  $slot
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'slot_id' => 'required'
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response(['status' => false, 'message' => $errors]);
        }
        $slot = Slot::find($request->slot_id);
        return response(['status' => true, 'message' => 'Slot Detail.', 'slot' => new SlotsResource($slot)]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Slot  $slot
     * @return \Illuminate\Http\Response
     */
    public function edit(Slot $slot)
    {
        //
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
        $validator = Validator::make($request->all(), [
            'from' => 'required|date_format:H:i:s',
            'to' => 'required|date_format:H:i:s',
            'weekdays' => 'required|array',
            'shop_id' => 'required',
            "slot_id" => 'required'
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response(['status' => false, 'message' => $errors]);
        }
        $slot = Slot::find($request->slot_id);
        $slot->shop_id = $request->shop_id;
        $slot->from = Carbon::parse($request->from)->format('H:i:s');
        $slot->to = Carbon::parse($request->to)->format('H:i:s');
        $slot->weekdays = str_replace('"', '', implode(',', $request->weekdays));
        $slot->save();
        return response(['status' => true, 'message' => 'Slot Updated.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Slot  $slot
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'slot_id' => 'required'
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response(['status' => false, 'message' => $errors]);
        }
        $slot = Slot::find($request->slot_id);
        if($slot && $slot->delete()){
            return response(['status' => true, 'message' => 'Slot Deleted.']);
        }
        return response(['status' => false, 'message' => 'Slot Not Found.']);
    }
}
