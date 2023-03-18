<?php

namespace App\Http\Controllers\Demand;

use App\CarProvider;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator, DB, Session, Auth;
use App\DemandAuthorizable;

class CarProviderController extends Controller
{
    // use DemandAuthorizable;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth()->user()->hasAnyRole('admin')) {
            $data['providers'] = \App\Provider::all();
            $data['cars'] = \App\CarProvider::with('car', 'provider')->paginate(10);
        }
        elseif (auth()->user()->hasAnyRole('provider')) {
            $data['providers'] = \App\Provider::all();
            $data['cars'] = \App\CarProvider::with('car', 'provider')->where('provider_id', auth()->user()->provider->id)->paginate(10);
        }else{
            abort(404);
        }

        if (request()->ajax()) {
            if(isset(request()->search) && !empty(request()->search)){
                $search = request()->search;
                    $data['cars'] = CarProvider::with('car')->whereHas('car', function($q) use($search){
                        $q->where('name', 'LIKE', '%'.$search.'%');
                    })->latest()->paginate(10);
            }
                Session::put(['prev_page_no' => $data['cars']->currentPage()]);
                return view('demand.provider_cars.cars_table', array('cars' => $data['cars']))->render();
            }
        return view('demand.provider_cars.cars', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(isset(auth()->user()->provider->id) && auth()->user()->provider->c_car == 0){
            abort(404);
        }
        if (auth()->user()->hasAnyRole('admin')) {
            $data['cars'] = \App\Car::where('active', 1)->get();
            $data['providers'] = \App\Provider::where('active', 1)->get();
        }
        elseif (auth()->user()->hasAnyRole('provider')) {
            $data['cars'] = \App\Car::where('active', 1)->get();
            $data['providers'] = \App\Provider::where('user_id', auth()->id())->get();
        }else{
            abort(404);
        }
        return view('demand.provider_cars.cars_form', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request);
        $validator = Validator::make($request->all(), [
            'provider_id' => 'required',
            'car_id' => 'required',
            'day' => 'required',
            'week' => 'required',
            'month' => 'required'
        ]);
        if($validator->fails()){
            flash()->error($validator->messages()->first());
            return back()->withInput();
        }
        $found = CarProvider::where('provider_id', $request->provider_id)->where('car_id', $request->car_id)->first();
        if($found){
            return redirect(route('demand.provider-cars.create'))->with(['message' => 'Car already assigned to this Provider', 'car_provider_id' => $found->id])->withInput();
            // return back()->withErrors(['msg' => 'Invalid car!', 'car_provider_id' => $found->id])->withInput();
        }
        $carProvider = new CarProvider;
        $carProvider->provider_id = $request->provider_id;
        $carProvider->car_id = $request->car_id;
        $carProvider->deposit = $request->deposit;
        $carProvider->day = $request->day;
        $carProvider->week = $request->week;
        $carProvider->month = $request->month;
        if($request->hasFile('image')){
            if($request->file('image')->isValid())
            {
                $extension = $request->image->extension();
                $file_path = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($request->provider_id)).time()."carp." .$extension;
                $request->image->move(config('constants.demand_ca_pro_image'), $file_path);
                $carProvider->image = $file_path;
            }
        }
        $carProvider->save();
        foreach ($request->about as $key => $value) {
            $about = new \App\About;
            $about->name = $value['name'];
            $about->car_provider_id = $carProvider->id;
            $about->icon = $value['icon'];
            $about->save();
        }
        flash()->success('Car Created for Provider');
        return redirect(route('demand.provider-cars.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CarProvider  $carProvider
     * @return \Illuminate\Http\Response
     */
    public function show(CarProvider $carProvider)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CarProvider  $carProvider
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        if (auth()->user()->hasAnyRole('admin')) {
            $data['cars'] = \App\Car::where('active', 1)->get();
            $data['providers'] = \App\Provider::where('active', 1)->get();
            $data['carProvider'] = CarProvider::where('id', $id)->with('specs')->first();
        }
        elseif (auth()->user()->hasAnyRole('provider')) {
            $data['cars'] = \App\Car::where('active', 1)->get();
            $data['providers'] = \App\Provider::where('user_id', auth()->id())->get();
            $data['carProvider'] = CarProvider::where('provider_id', auth()->user()->provider->id)->where('id', $id)->with('specs')->first();
        }else{
            abort(404);
        }
        if(!$data['carProvider']){
            abort(404);
        }
        return view('demand.provider_cars.cars_form', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CarProvider  $carProvider
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // dd($request);
        $validator = Validator::make($request->all(), [
            // 'provider_id' => 'required',
            // 'car_id' => 'required',
            'day' => 'required',
            'week' => 'required',
            'month' => 'required'
        ]);
        if($validator->fails()){
            flash()->error($validator->messages()->first());
            return back()->withInput();
        }
        $carProvider = CarProvider::find($id);
        // $carProvider->provider_id = $request->provider_id;
        // $carProvider->car_id = $request->car_id;
        $carProvider->deposit = $request->deposit;
        $carProvider->day = $request->day;
        $carProvider->week = $request->week;
        $carProvider->month = $request->month;
        if($request->hasFile('image')){
            if($request->file('image')->isValid())
            {
                $extension = $request->image->extension();
                $file_path = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($request->provider_id)).time()."carp." .$extension;
                $request->image->move(config('constants.demand_ca_pro_image'), $file_path);
                $carProvider->image = $file_path;
            }
        }
        $carProvider->save();
        foreach ($request->about as $key => $value) {
            $about = \App\About::find($value['id']);
            $about->name = $value['name'];
            $about->car_provider_id = $carProvider->id;
            $about->save();
        }
        flash()->success('Car Updated for Provider');
        return redirect(route('demand.provider-cars.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CarProvider  $carProvider
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        CarProvider::find($id)->delete();
        flash()->info('Deleted Successfully!');
        return back();
    }

    public function checkUniqueCar(Request $request){
        $carProvider = CarProvider::where('provider_id', $request->provider_id)->where('car_id', $request->car_id)->first();
        if($carProvider){
            return response(['status' => false, 'message' => 'Car already assigned to this Provider']);
        }
        return response(['status' => true, 'message' => 'Car Found']);
    }
    public function filter($provider){
        if($provider === 'all'){
            return redirect(route('demand.provider-cars.index'));
        }
        $data['providers'] = \App\Provider::all();
        $data['cars'] = \App\CarProvider::with('car', 'provider')->where('provider_id', $provider)->paginate(10);
        if (request()->ajax()) {
            if(isset(request()->search) && !empty(request()->search)){
                $search = request()->search;
                    $data['cars'] = CarProvider::with('car')->where('provider_id', $provider)->whereHas('car', function($q) use($search){
                        $q->where('name', 'LIKE', '%'.$search.'%');
                    })->latest()->paginate(10);
            }
                Session::put(['prev_page_no' => $data['cars']->currentPage()]);
                return view('demand.provider_cars.cars_table', array('cars' => $data['cars']))->render();
            }
        return view('demand.provider_cars.cars', $data);
    }
}
