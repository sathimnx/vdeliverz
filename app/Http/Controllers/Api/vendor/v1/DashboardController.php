<?php

namespace App\Http\Controllers\Api\vendor\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator, DB;
use \Carbon\Carbon;

class DashboardController extends Controller
{
    public function dashboard(){
        if(!auth('api')->user()->shop){
            return response(['status' => false, 'message' => 'No shops found, Create shop first!']);
        }
        $orders = auth('api')->user()->vendorOrders()->where('confirmed_at', '!=', null)->orderBy('expected_time', 'desc')->paginate(8);

        foreach ($orders as $key => $value) {
            $date = $value->date == now()->format('Y-m-d') ? 'Today' : Carbon::parse($value->confirmed_at)->format('d M Y');
            $order[] =  [
                            'order_id' => $value->id,
                            'referral' => $value->search,
                            'order_status' => $value->order_status,
                            'order_state' => $value->order_state,
                            'date' => $date
                        ];
        }
        $pagination = apiPagination($orders);
        $total_delivered = auth('api')->user()->vendorOrders()->where('delivered_at', '!=', NULL)->count();
        $total_earnings = auth('api')->user()->vendorOrders()->where('delivered_at', '!=', NULL)->sum('amount');
        $unassigned = auth('api')->user()->vendorOrders()->where('order_status', 1)->count();
        $users = \App\User::count();
        return response(['status' => true, 'message' => 'Orders List.', 'users' => $users, 'tot_deliveries' => $total_delivered, 'unassigned' => $unassigned, 'shop_id' => auth('api')->user()->shop->id, 'tot_earning' => $total_earnings, 'currency' => config('constants.currency'), 'orders' => $order ?? [], 'pagination' => $pagination,
                        'terms_and_conditions' => 'http://vdeliverz.in/terms-and-conditions/', 'privacy_policy' => 'http://vdeliverz.in/privacy-policy/']);
    }

    public function analytics(Request $request){

        try {
            $validator = Validator::make($request->all(), [
                'year' => 'required|date_format:Y',
                'month' => 'required|date_format:m',
                'shop_id' => 'required'
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response(['status' => false, 'message' => $errors]);
            }
            $year = $request->year;
            $month = $request->month;
            if($year > date('Y') || $month > date('m')){
                return response(['status' => false, 'message' => 'Invalid Date!']);
            }
            $shop_ids = auth('api')->user()->outlets()->pluck('id')->toArray();
            if($request->shop_id == 'all'){
                $data = $this->allShops($year, $month);
            }else{
                if (!in_array($request->shop_id, $shop_ids)) {
                    return response(['status' => false, 'message' => 'Invalid Shop!']);
                }
                $data = $this->filterShops($request->shop_id, $year, $month);
            }
            // $data = $request->shop_id == 'all' ? $this->allShops($year, $month) : $this->filterShops($request->shop_id, $year, $month);
            $data['status'] = true;
            $data['message'] = 'Analytics Dashboard.';
            return $data;
        } catch (\Throwable $th) {
            dd($th);
        }

    }

    private function allShops($year, $month){
        $orders = auth('api')->user()->vendorOrders()->where('confirmed_at', '!=', null)->orderBy('expected_time', 'desc')->get();
        $months = auth('api')->user()->vendorOrders()->where('delivered_at', '!=', null)->whereYear('delivered_at', $year)->get();
        // dd($months);
        $data['monthly_data'] = [
                            '01' => 0,
                            '02' => 0,
                            '03' => 0,
                            '04' => 0,
                            '05' => 0,
                            '06' => 0,
                            '07' => 0,
                            '08' => 0,
                            '09' => 0,
                            '10' => 0,
                            '11' => 0,
                            '12' => 0,
                        ];
        foreach ($months as $key => $value) {
            $data['monthly_data'][$value->delivered_at->format('m')] += $value->amount;
        }

        $weeks = [1 => 7, 8 => 14, 15 => 21, 22 => 31];
        $i = 1;
        foreach ($weeks as $key => $week) {
            $from = $year."-".$month."-".$key;
            $to = $year."-".$month."-".$week;
            $data['weekly_data'][$i] = auth('api')->user()->vendorOrders()->where('delivered_at', '!=', null)
                                ->whereDate('delivered_at', '>=', $from)->whereDate('delivered_at', '<=' , $to)
                                ->sum('amount');
                                $i++;
        }
        $data['total_delivered'] = $orders->where('order_status', 3)->count();
        $data['total_earnings'] = $orders->where('delivered_at', '!=', null)->sum('amount').' '.config('constants.currency');
        $data['unassigned'] = $orders->where('order_status', 1)->count();
        $data['users'] = \App\User::count();
        $data['shop_id'] = 'all';
        return $data;
    }

    private function filterShops($shop, $year, $month){
        $orders = \App\Order::where('shop_id', $shop)->where('confirmed_at', '!=', null)->orderBy('expected_time', 'desc')->get();
        $months = \App\Order::where('shop_id', $shop)->where('delivered_at', '!=', null)->whereYear('delivered_at', $year)->get();
        // dd($months);
        $data['monthly_data'] = [
                            '01' => 0,
                            '02' => 0,
                            '03' => 0,
                            '04' => 0,
                            '05' => 0,
                            '06' => 0,
                            '07' => 0,
                            '08' => 0,
                            '09' => 0,
                            '10' => 0,
                            '11' => 0,
                            '12' => 0,
                        ];
        foreach ($months as $key => $value) {
            $data['monthly_data'][$value->delivered_at->format('m')] += $value->amount;
        }

        $weeks = [1 => 7, 8 => 14, 15 => 21, 22 => 31];
        $i = 1;
        foreach ($weeks as $key => $week) {
            $from = $year."-".$month."-".$key;
            $to = $year."-".$month."-".$week;
            $data['weekly_data'][$i] = \App\Order::where('shop_id', $shop)->where('delivered_at', '!=', null)
                                ->whereDate('delivered_at', '>=', $from)->whereDate('delivered_at', '<=' , $to)
                                ->sum('amount');
                                $i++;
        }
        $data['total_delivered'] = $orders->where('order_status', 3)->count();
        $data['total_earnings'] = $orders->where('delivered_at', '!=', null)->sum('amount').' '.config('constants.currency');
        $data['unassigned'] = $orders->where('order_status', 1)->count();
        $data['users'] = \App\User::count();
        $data['shop_id'] = $shop;
        return $data;
    }
}
