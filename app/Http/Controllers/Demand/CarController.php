<?php

namespace App\Http\Controllers\Demand;

use App\Car;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator, DB, Auth, Session;
use Carbon\Carbon;
use App\DemandAuthorizable;

class CarController extends Controller
{
    use DemandAuthorizable;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['cars'] = Car::latest()->paginate(10);
        if (request()->ajax()) {
            if(isset(request()->search) && !empty(request()->search)){
                    $data['cars'] = Car::where('name', 'Like', '%'.request()->search.'%')
                                        ->latest()->paginate(10);
            }
            Session::put(['prev_page_no' => $data['cars']->currentPage()]);
                return view('demand.cars.cars_table', array('cars' => $data['cars']))->render();
            }
        return view('demand.cars.cars', $data ?? NULL);
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

        return view('demand.cars.cars_form');
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
                'cars' => 'required'
            ]);
            if($validator->fails()){
                flash()->error($validator->messages()->first());
                return back()->withInput();
            }
            // dd($request);
            foreach ($request->cars as $key => $value) {
                if(isset($value['name']) && !empty($value['name'])){
                    $car = new \App\Car;
                    $car->service_id = 1;
                    $car->name = $value['name'];
                    if(isset($value['image']) && !empty($value['image'])){
                            $extension = $value['image']->extension();
                            $file_path = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($value['name'])).time()."car." .$extension;
                            $value['image']->move(config('constants.demand_car_image'), $file_path);
                            $car->image = $file_path;
                    }
                    $car->save();
                }
            }
            flash()->success('Cars Created');
            return redirect()->route('demand.cars.index');
        } catch (\Throwable $th) {
            return catchResponse();
            dd($th);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Car  $car
     * @return \Illuminate\Http\Response
     */
    public function show(Car $car)
    {
        return view('demand.cars.cars_form', compact('car'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Car  $car
     * @return \Illuminate\Http\Response
     */
    public function edit(Car $car)
    {
        return view('demand.cars.cars_form', compact('car'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Car  $car
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Car $car)
    {
        // $validator = Validator::make($request->all(), [
        //     // 'service_id' => 'required',
        //     'stocks' => 'required'
        // ]);
        // if($validator->fails()){
        //     flash()->error($validator->messages()->first());
        //     return back()->withInput();
        // }
        // dd($request);
        // foreach ($request->stocks as $key => $value) {
            // $car = new \App\Car;
            $car->service_id = 1;
            $car->name = $request->stocks[0]['name'];
            if(isset($request->stocks[0]['image']) && !empty($request->stocks[0]['image'])){
                    $extension = $request->stocks[0]['image']->extension();
                    $file_path = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($request->stocks[0]['name'])).time()."car." .$extension;
                    $request->stocks[0]['image']->move(config('constants.demand_car_image'), $file_path);
                    $car->image = $file_path;
            }
            $car->save();
        // }
        flash()->success('Car Updated');
        return redirect()->route('demand.cars.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Car  $car
     * @return \Illuminate\Http\Response
     */
    public function destroy(Car $car)
    {
        $car->delete();
        flash('Car Deleted.');
        return back();
    }
}
