<?php

namespace App\Http\Controllers\Demand;

use App\Service;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator, DB, Session;
use App\DemandAuthorizable;

class ServiceController extends Controller
{
    use DemandAuthorizable;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['services'] = Service::latest()->paginate(10);
        if (request()->ajax()) {
            if(isset(request()->search) && !empty(request()->search)){
                    $data['services'] = Service::where('name', 'Like', '%'.request()->search.'%')
                                        ->latest()->paginate(10);
            }
            Session::put(['prev_page_no' => $data['services']->currentPage()]);
                return view('demand.category.category_table', array('services' => $data['services']))->render();
            }
        return view('demand.category.category', $data);
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
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'image' => 'required'
            ]);
            if($validator->fails()){
                flash()->error($validator->messages()->first());
                return back()->withInput();
            }
            $service = new Service;
            $service->name = $request->name;
            $service->image = 'default.png';
            if($request->hasFile('image')){
                if($request->file('image')->isValid())
                {

                    $extension = $request->image->extension();
                    $file_path = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($request->name)).time()."service." .$extension;
                    $request->image->move(config('constants.demand_service_icon_image'), $file_path);
                    $service->image = $file_path;
                }
            }
            $service->order = Service::count() + 1;
            $service->save();
            flash()->success('Service Created');
            return redirect(route('demand.services.index'));
        } catch (\Throwable $th) {
            return catchResponse();
            dd($th);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function show(Service $service)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function edit(Service $service)
    {
        return response($service);
    }
    // php artisan permission:create-role admin web "create_provider-cars | edit_provider-cars | view_provider-cars | delete_provider-cars | create_cars | view_cars | edit_cars | delete_cars | create_services | edit_services | view_services | delete_services | create_providers | edit_providers | view_providers | delete_providers | create_sub-services | edit_sub-services | view_sub-services | delete_sub-services | create_weekdays | edit_weekdays | view_weekdays | delete_weekdays | view_bookings | edit_bookings | create_bookings | delete_bookings"
    // php artisan permission:create-role admin web "create_sub-services | edit_sub-services | view_sub-services | delete_sub-services"
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Service $service)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
            ]);
            if($validator->fails()){
                flash()->error($validator->messages()->first());
                return back()->withInput();
            }
            $service->name = $request->name;
            if($request->hasFile('image')){
                if($request->file('image')->isValid())
                {
                    $image_path = $service->image;  // Value is not URL but directory file path
                    $extension = $request->image->extension();
                    $file_path = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($request->name)).time()."service." .$extension;
                    $request->image->move(config('constants.demand_service_icon_image'), $file_path);
                    $service->image = $file_path;

                    // if(File::exists($image_path)) {
                    //     File::delete($image_path);
                    // }
                }
            }
            $service->save();
            flash()->success('Service Updated');
            return redirect(route('demand.services.index'));
        } catch (\Throwable $th) {
            return catchResponse();
            dd($th);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function destroy(Service $service)
    {
        $service->delete();
        flash()->info('Service Deleted.');
        return back();
    }

    public function orderChange(Request $request, Service $service){
        $old_order = $service->order;
        $service->update(['order' => null]);
        $old_service = Service::where('order', $request->num)->first();
        Service::where('order', $request->num)->update(['order' => $old_order]);
        $service->update(['order' => $request->num]);
        return response(['status' => true, 'id' => $old_service->id ?? NULL, 'order' => $old_order]);
    }
}