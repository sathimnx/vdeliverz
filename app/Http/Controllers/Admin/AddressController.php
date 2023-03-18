<?php

namespace App\Http\Controllers\Admin;

use App\Address;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
            $data['addresses'] = Address::with('user')->latest()->paginate(10);
            if (request()->ajax()) {
            if(isset(request()->search) && !empty(request()->search)){
                $data['addresses'] = Address::with('user')->where('address', 'Like', '%'.request()->search.'%')->orWhere('landmark', 'Like','%'.request()->search.'%')->orWhere('type', 'Like','%'.request()->search.'%')->latest()->paginate(10);
            }

                return view('address.address_table', array('addresses' => $data['addresses']))->render();
            }
            return view('address.address', $data ?? NULL);
        } catch (\Throwable $th) {
            return catchResponse();
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
        //
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
    public function edit(Address $address)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Address  $address
     * @return \Illuminate\Http\Response
     */
    public function destroy(Address $address)
    {
        $address->delete();
        return back();
    }
}