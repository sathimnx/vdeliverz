<?php

namespace App\Http\Controllers\Admin;

use App\Banner;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator, DB, Auth, Session;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['banners'] = Banner::take(1)->get();
        return view('banner.banner', $data ?? null);
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
                'image' => 'required'
            ]);
            if($validator->fails()){
                flash()->error($validator->messages()->first());
                return back()->withInput();
            }
            $banner = new Banner;
            if($request->hasFile('image')){
                if($request->file('image')->isValid())
                {
                    $extension = $request->image->extension();
                    $file_path = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($request->shop_id)).time()."banner." .$extension;
                    $request->image->move(config('constants.app_banner_image'), $file_path);
                    $banner->image = $file_path;
                }
            }

            $banner->save();
            flash()->success('Banner Created');
            return back();
        } catch (\Throwable $th) {
        //    return catchResponse();
            dd($th);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Banner  $banner
     * @return \Illuminate\Http\Response
     */
    public function show(Banner $banner)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Banner  $banner
     * @return \Illuminate\Http\Response
     */
    public function edit(Banner $banner)
    {
        return response($banner);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Banner  $banner
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Banner $banner)
    {
        try {
            // dd($request);
            $validator = Validator::make($request->all(), [
                'image' => 'required'
            ]);
            if($validator->fails()){
                flash()->error($validator->messages()->first());
                return back()->withInput();
            }

            if($request->hasFile('image')){
                if($request->file('image')->isValid())
                {
                    $extension = $request->image->extension();
                    $file_path = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($request->shop_id)).time()."banner." .$extension;
                    $request->image->move(config('constants.app_banner_image'), $file_path);
                    $banner->image = $file_path;
                }
            }

            $banner->save();
            flash()->success('Banner Updated');
            return back();
        } catch (\Throwable $th) {
           return catchResponse();
            dd($th);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Banner  $banner
     * @return \Illuminate\Http\Response
     */
    public function destroy(Banner $banner)
    {
        $banner->delete();
        flash()->info('Banner Deleted.');
        return back();
    }
}