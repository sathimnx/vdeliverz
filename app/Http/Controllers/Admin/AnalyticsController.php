<?php

namespace App\Http\Controllers\Admin;

use App\Order;
use Carbon\Carbon;
use DB, Auth, Validator;
use Illuminate\Http\Request;
use \Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use \Spatie\Permission\Models\Permission;

class AnalyticsController extends Controller
{
    public function index(){
        $today = today();
        $year = date("Y");
        $month = date("m");
        $data = $this->dashboardCalculate($today, $month, $year);
        return view('analytics.analytics', $data);
    }

    public function filter($year, $month){
        $today = $year.'-'.$month.'-01';
        $today = Carbon::parse($today);
        $data = $this->dashboardCalculate($today, $month, $year);
        // dd($data);
        return view('analytics.analytics', $data);
    }

    private function dashboardCalculate($today, $month, $year){
        $monthly_data = [0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0];
        // $monthly_data1 = [0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0];


        // Order and Delivery
        $orders = \App\Order::where('order_status', 3)->get();

        $data['orders_delivered'] = $orders->count();

        $cal_orders = Order::with('cart')->where('order_status', 3)->get();
        $today = today();
        $year = date("Y");
        $month = 10;
        $data['dates'] = [];
        $data['daily'] = [];
        for($i = 1; $i < $today->daysInMonth + 1; ++$i) {
            $day = \Carbon\Carbon::createFromDate($today->year, 10, $i)->format('Y-m-d');
            $data['dates'][] = $i;

                $query = \App\Order::where('confirmed_at', '!=', null);

            $data['total_orders'][] = $query->where('order_status', 3)->whereYear('delivered_at', '=', $year)
            ->whereMonth('delivered_at', '=', $month)
            ->whereDate('delivered_at', '=', $day)->count();
            $data['cancelled_orders'][] = $query->where('order_status', 0)->whereYear('canceled_at', '=', $year)
            ->whereMonth('canceled_at', '=', $month)
            ->whereDate('canceled_at', '=', $day)->count();

        }
        // dd($data);
        // Combined datas
        $data['orders_revenue'] = number_format($orders->sum('amount'), 2);
        // $data['mon_orders'] = Order::where('order_status', 3)->whereMonth('delivered_at', 10)->count();
        $data['users'] = \App\User::count();
        $data['total_del_boys'] = \App\User::role('delivery-boy')->count();
        $data['char_tot_del_charge'] = totalDeliveryCharges($cal_orders);
        $data['char_total_discounts'] = totalOrderDiscounts($cal_orders);
        $data['char_total_commissions'] = totalOrderCommission($cal_orders);
        $data['char_shop_total_commissions'] = totalShopEarnings($cal_orders);
        $data['total_del_charges'] = number_format($data['char_tot_del_charge'], 2);
        $data['total_discounts'] = number_format($data['char_total_discounts'], 2);
        $data['total_commissions'] = number_format($data['char_total_commissions'], 2);
        $data['tot_vdel_earnings'] = number_format($data['char_tot_del_charge'] + $data['char_total_commissions'] - $data['char_total_discounts'], 2);
        $data['tot_shop_earnings'] = number_format($data['char_shop_total_commissions'], 2);
        $data['total_shops'] = \App\Shop::count();
        $data['total_products'] = \App\Product::count();
        return $data;
    }

    private function monthlyData($monthly){
        $replace = [];
        for ($i=0; $i < 12 ; $i++) {
            foreach ($monthly as $key => $value) {
                if(isset($value->month) && $value->month == $i + 1){
                    array_push($replace, round($value->amount, 2));
                    unset($monthly[$key]);
                    break;
                }else{
                    array_push($replace, 0);
                    break;
                }
            }
        }
        return $replace;
    }

    private function dailyData($today, $month, $year, $type){
        $data['dates'] = [];
        $data['daily'] = [];
        for($i=1; $i < $today->daysInMonth + 1; ++$i) {
            $day = \Carbon\Carbon::createFromDate($today->year, $today->month, $i)->format('Y-m-d');
            $data['dates'][] = $i;
            $data['daily'][] =  $type == 1 ? \App\Order::
                    where('order_status', 3)->whereYear('updated_at', '=', $year)
                    ->whereMonth('updated_at', '=', $month)
                    ->whereDate('updated_at', '=', $day)
                    ->sum(DB::raw('amount')) : \App\Booking::
                    where('paid', 1)->where('status', 6)->whereYear('updated_at', '=', $year)
                    ->whereMonth('updated_at', '=', $month)
                    ->whereDate('updated_at', '=', $day)
                    ->sum(DB::raw('payable_amount'));
        }
        return $data;
    }

    private function addTwoArrays($one, $two){
        $combine = [];
        foreach ($one as $key => $value) {
            $combine[] = $value + $two[$key];
        }
        return $combine;
    }
}
