<?php

namespace App\Http\Controllers\Admin;

use App\Coupon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator, DB, Auth;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data['coupons'] = Coupon::latest()->paginate(10);
            if (request()->ajax()) {
            if(isset(request()->search) && !empty(request()->search)){
                $data['coupons'] = Coupon::where('coupon_code', 'Like', '%'.request()->search.'%')->orWhere('expired_on', 'Like','%'.request()->search.'%')->orWhere('max_order_amount', 'Like','%'.request()->search.'%')->latest()->paginate(10);
            }

                return view('coupon.coupon_table', array('coupons' => $data['coupons']))->render();
            }
            return view('coupon.coupon', $data ?? NULL);
        } catch (\Throwable $th) {
            return catchResponse();
            //throw $th;
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            return view('coupon.coupon_create');
        } catch (\Throwable $th) {
            return catchResponse();
            //throw $th;
        }
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
            $validator = Validator::make($request->all(),[
                'coupon_code' => ['required'],
                'max_order_amount' => 'required',
                'coupon_date' => 'required',
                'coupon_time' => 'required',
                'coupon_percentage' => 'required'
            ]);
            if($validator->fails()){
                flash()->error($validator->messages()->first());
                return redirect()->back();
            }
            $word = preg_replace('/\s+/', "", strtolower($request->coupon_code));;
            $coupon_date = date('Y-m-d', strtotime($request->coupon_date));
            $coupon_time = date('H:i:s', strtotime($request->coupon_time));
            $coupon_end = $coupon_date.' '. $coupon_time;

            $coupon = new Coupon;
            $coupon->coupon_code = strtoupper($word);
            $coupon->coupon_description = $request->coupon_description;
            $coupon->coupon_percentage = $request->coupon_percentage;
            $coupon->max_order_amount = $request->max_order_amount;
            $coupon->min_order_amt = $request->min_order_amt;
            $coupon->Discount_use_amt =$request->Discount_use_amt;
            $coupon->expired_on = $coupon_end;
            if($coupon->save()){
                // $users = \App\User::role('customer')->get();
                // Notification::send($users, new CouponNotification($coupon));
                $alert_data = cusCouponAlert(['coupon_code' => $coupon->coupon_code]);
                $appData = [
                    'users' => \App\User::where('fcm', '!=', null)->get(),
                    'title' => $alert_data['title'],
                    'body' => $alert_data['body'],
                    'icon' => '',
                    'type' => 1
                ];
                sendAppNotification($appData, env('CUS_FCM'));
                flash()->success('Coupon Created Successfully !');
                return redirect()->route('coupons.index');
            }
        } catch (\Throwable $th) {
            return catchResponse();
            dd($th);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Coupon  $coupon
     * @return \Illuminate\Http\Response
     */
    public function show(Coupon $coupon)
    {
        return view('coupon.coupon_edit', compact('coupon'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Coupon  $coupon
     * @return \Illuminate\Http\Response
     */
    public function edit(Coupon $coupon)
    {
        try {
            return view('coupon.coupon_edit', compact('coupon'));
        } catch (\Throwable $th) {
            return catchResponse();
           dd($th);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Coupon  $coupon
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Coupon $coupon)
    {
        try {
            $validator = Validator::make($request->all(),[
                'coupon_code' => ['required'],
                'max_order_amount' => 'required',
                'coupon_date' => 'required',
                'coupon_time' => 'required',
                'coupon_percentage' => 'required'
            ]);
            if($validator->fails()){
                flash()->error($validator->messages()->first());
                return redirect()->back();
            }
            $word = preg_replace('/\s+/', "", strtolower($request->coupon_code));;
            $coupon_date = date('Y-m-d', strtotime($request->coupon_date));
            $coupon_time = date('H:i:s', strtotime($request->coupon_time));
            $coupon_end = $coupon_date.' '. $coupon_time;
            // dd($coupon_end, $request->coupon_date, \Carbon\Carbon::parse($request->coupon_date)->format('d/m/Y'));

            $coupon->coupon_code = strtoupper($word);
            $coupon->coupon_description = $request->coupon_description;
            $coupon->coupon_percentage = $request->coupon_percentage;
            $coupon->max_order_amount = $request->max_order_amount;
            $coupon->min_order_amt = $request->min_order_amt;
            $coupon->Discount_use_amt =$request->Discount_use_amt;
            $coupon->expired_on = $coupon_end;
            if($coupon->save()){
                flash()->success('Coupon Updated Successfully !');
                return redirect()->route('coupons.index');
            }
        } catch (\Throwable $th) {
            return catchResponse();
            dd($th);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Coupon  $coupon
     * @return \Illuminate\Http\Response
     */
    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        flash()->info('Coupon Deleted');
        return back();
    }
}
