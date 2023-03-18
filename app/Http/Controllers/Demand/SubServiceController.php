<?php

namespace App\Http\Controllers\Demand;

use App\SubService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator, DB, Session;
use Illuminate\Support\Facades\File;
use App\DemandAuthorizable;

class SubServiceController extends Controller
{
    use DemandAuthorizable;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['sub_services'] = \App\SubService::latest()->paginate(10);
        if (request()->ajax()) {
            if(isset(request()->search) && !empty(request()->search)){
                    $data['sub_services'] = \App\SubService::where('name', 'Like', '%'.request()->search.'%')
                                        ->latest()->paginate(10);
            }
            Session::put(['prev_page_no' => $data['sub_services']->currentPage()]);
            return view('demand.sub_service.sub_service_table', array('sub_services' => $data['sub_services']))->render();
            }
        return view('demand.sub_service.sub_service', $data ?? NULL);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['services'] = \App\Service::where('active', 1)->where('order', '!=', 1)->get();
        return view('demand.sub_service.sub_service_form', $data ?? NULL);
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
                'service_id' => 'required',
                'stocks' => 'required'
            ]);
            if($validator->fails()){
                flash()->error($validator->messages()->first());
                return back()->withInput();
            }
            // dd($request);
            foreach ($request->stocks as $key => $value) {
                $subService = new \App\SubService;
                $subService->service_id = $request->service_id;
                $subService->name = $value['name'];
                if(isset($value['image']) && !empty($value['image'])){
                        $extension = $value['image']->extension();
                        $file_path = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($value['name'])).time()."sub_service." .$extension;
                        $value['image']->move(config('constants.demand_sub_service_icon_image'), $file_path);
                        $subService->image = $file_path;
                }
                $subService->save();
            }
            flash()->success('Sub-Services Created');
            return redirect()->route('demand.sub-services.index');
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SubService  $subService
     * @return \Illuminate\Http\Response
     */
    public function show(SubService $subService)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SubService  $subService
     * @return \Illuminate\Http\Response
     */
    public function edit(SubService $subService)
    {
        $data['services'] = \App\Service::where('active', 1)->get();
        $data['sub_service'] = $subService;
        return view('demand.sub_service.sub_service_form', $data ?? NULL);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SubService  $subService
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SubService $subService)
    {
        try {
            $validator = Validator::make($request->all(), [
                'service_id' => 'required',
            ]);
            if($validator->fails()){
                flash()->error($validator->messages()->first());
                return back()->withInput();
            }
            // dd($request);
            $subService->service_id = $request->service_id;
            $subService->name = $request->stocks[0]['name'];
            if(isset($request->stocks[0]['image']) && !empty($request->stocks[0]['image'])){
                    // $del_filename = $subService->del_image;
                    // dd($del_filename);
                    $extension = $request->stocks[0]['image']->extension();
                    $file_path = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($request->stocks[0]['name'])).time()."sub_service." .$extension;
                    $request->stocks[0]['image']->move(config('constants.demand_sub_service_icon_image'), $file_path);
                    $subService->image = $file_path;
            }
            $subService->save();
            flash()->success('Sub-Services Updated');
            $url = route('demand.sub-services.index').'?page='.Session::get('prev_page_no');
            return redirect($url);
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SubService  $subService
     * @return \Illuminate\Http\Response
     */
    public function destroy(SubService $subService)
    {
        $subService->delete();
        flash()->info('Sub-Service Deleted!');
        return back();
    }
}
