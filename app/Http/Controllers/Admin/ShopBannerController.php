<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\ShopBanner;
use App\Shop;
use Illuminate\Http\Request;
use Validator, DB, Session;

class ShopBannerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['banners'] = ShopBanner::with('shop')->paginate(10);
        $data['shops'] = \App\Shop::orderBy('name')->get();
        if (request()->ajax()) {
                Session::put(['prev_page_no' => $data['banners']->currentPage()]);
                return view('shop_banner.banner_table', array('banners' => $data['banners']))->render();
            }
        return view('shop_banner.banner', $data);
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
                'image' => 'required|file',
            ]);
            if($validator->fails()){
                flash()->error($validator->messages()->first());
                return back()->withInput();
            }
            $banner = new ShopBanner;
            if($request->hasFile('image')){
                if($request->file('image')->isValid())
                {
                    $extension = $request->image->extension();
                    $file_path = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($request->shop_id)).time()."banner." .$extension;
                    $request->image->move(config('constants.app_shop_banner_image'), $file_path);
                    $banner->image = $file_path;
                }
            }
            // if($request->hasFile('banner')){
            //     if($request->file('banner')->isValid())
            //     {
            //         $extension = $request->banner->extension();
            //         $file_path = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($request->shop_id)).time()."shop_banner." .$extension;
            //         $request->banner->move(config('constants.app_shop_banner_image'), $file_path);
            //         $banner->banner = $file_path;
            //     }
            // }

            $banner->shop_id = $request->shop_id;
            $banner->save();
            flash()->success('Shop Banner Created');
            return back();
        } catch (\Throwable $th) {
//            return catchResponse();
            dd($th);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ShopBanner  $shopBanner
     * @return \Illuminate\Http\Response
     */
    public function show(ShopBanner $shopBanner)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ShopBanner  $shopBanner
     * @return \Illuminate\Http\Response
     */
    public function edit(ShopBanner $shopBanner)
    {
        return response($shopBanner);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ShopBanner  $shopBanner
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ShopBanner $shopBanner)
    {
        try {
            // dd($request);
            $validator = Validator::make($request->all(), [
                'shop_id' => 'required',
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
                    $request->image->move(config('constants.app_shop_banner_image'), $file_path);
                    $shopBanner->image = $file_path;
                }
            }
            // if($request->hasFile('banner')){
            //     if($request->file('banner')->isValid())
            //     {
            //         $extension = $request->banner->extension();
            //         $file_path = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($request->shop_id)).time()."shop_banner." .$extension;
            //         $request->banner->move(config('constants.app_shop_banner_image'), $file_path);
            //         $shopBanner->banner = $file_path;
            //     }
            // }
            $shopBanner->shop_id = $request->shop_id;
            $shopBanner->save();
            flash()->success('Shop Banner Updated');
            return back();
        } catch (\Throwable $th) {
//            return catchResponse();
            dd($th);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ShopBanner  $shopBanner
     * @return \Illuminate\Http\Response
     */
    public function destroy(ShopBanner $shopBanner)
    {
        $shopBanner->delete();
        flash()->info('Shop Banner Deleted.');
        return back();
    }
}
