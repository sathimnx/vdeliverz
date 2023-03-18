<?php

namespace App\Http\Controllers\Demand;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB, Validator, Auth;

class DashboardController extends Controller
{
    public function index(){
        $today = today();
        $year = date("Y");
        $month = date("m");
        $data['bookings'] = \App\Booking::where('status', '!=', 0)->get();
        $data['tot_earnings'] = $data['bookings']->where('status', 6)->sum('payable_amount');
        $data['pending'] = $data['bookings']->where('status', 1)->count();
        $data['completed'] = $data['bookings']->where('status', 6)->count();
        $data['monthly'] = $monthly = \App\Booking::Where('paid', 1)->where('status', 6)->whereYear('updated_at', '=', $year)
                                ->select(DB::raw('SUM(payable_amount) as amount, MONTH( completed_at ) as month'))
                                ->groupBy(DB::raw('MONTH(completed_at) ASC'))->get();
        $data['users'] = \App\User::where('active', 1)->count();
        $data['monthly_data'] = [0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0];
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
        $data['final'] = array_replace($data['monthly_data'], $replace);
        $data['dates'] = [];
        $data['daily'] = [];
        for($i=1; $i < $today->daysInMonth + 1; ++$i) {
            $day = \Carbon\Carbon::createFromDate($today->year, $today->month, $i)->format('Y-m-d');
            $data['dates'][] = $i;
            if(Auth::user()->hasAnyRole('provider')){
                $shop_id = Auth::user()->provider->id;
                $data['daily'][] =  \App\Booking::where('provider_id', $shop_id)
                    ->where('status', 6)->whereYear('updated_at', '=', $year)
                    ->whereMonth('updated_at', '=', $month)
                    ->whereDate('updated_at', '=', $day)
                    ->sum(DB::raw('payable_amount'));
            }else{
                $data['daily'][] =  \App\Booking::
                    where('status', 6)->whereYear('updated_at', '=', $year)
                    ->whereMonth('updated_at', '=', $month)
                    ->whereDate('updated_at', '=', $day)
                    ->sum(DB::raw('payable_amount'));
            }

        }
        $data['shops'] = \App\Provider::all();
        return view('demand.dashboard.dashboard', $data);
    }

    public function filter($shop, $year, $month){
        if(Auth::user()->hasAnyRole('provider') || ($shop === 'all' && $year == null)){
            return redirect()->route('dashboard.index');
        }
        $today = $year.'-'.$month.'-01';
        $today = Carbon::parse($today);
        $data['bookings'] =  $shop === 'all' ? \App\Booking::where('status', '!=', 0)->get() : \App\Booking::where('provider_id', $shop)->where('status', '!=', 0)->get();
        $data['tot_earnings'] = $data['bookings']->where('status', 6)->sum('payable_amount');
        $data['pending'] = $data['bookings']->where('status', 1)->count();
        $data['completed'] = $data['bookings']->where('status', 6)->count();
        $monthly = $shop === 'all' ? \App\Booking::Where('paid', 1)->where('status', 6)->whereYear('updated_at', '=', $year)
                                ->select(DB::raw('SUM(payable_amount) as amount, MONTH( completed_at ) as month'))
                                ->groupBy(DB::raw('MONTH(completed_at) ASC'))->get() : \App\Booking::Where('paid', 1)->where('status', 6)->where('provider_id', $shop)->whereYear('updated_at', '=', $year)
                                ->select(DB::raw('SUM(payable_amount) as amount, MONTH( completed_at ) as month'))
                                ->groupBy(DB::raw('MONTH(completed_at) ASC'))->get();
        $data['users'] = \App\User::where('active', 1)->count();
        $data['monthly_data'] = [0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0];
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
        $data['final'] = array_replace($data['monthly_data'], $replace);
        $data['dates'] = [];
        $data['daily'] = [];
        for($i=1; $i < $today->daysInMonth + 1; ++$i) {
            $day = \Carbon\Carbon::createFromDate($today->year, $today->month, $i)->format('Y-m-d');
            $data['dates'][] = $i;


                $data['daily'][] =  $shop === 'all' ? \App\Booking::where('status', 6)->whereYear('updated_at', '=', $year)
                ->whereMonth('updated_at', '=', $month)
                ->whereDate('updated_at', '=', $day)
                ->sum(DB::raw('payable_amount')) : \App\Booking::where('provider_id', $shop)
                    ->where('status', 6)->whereYear('updated_at', '=', $year)
                    ->whereMonth('updated_at', '=', $month)
                    ->whereDate('updated_at', '=', $day)
                    ->sum(DB::raw('payable_amount'));

        }
        $data['shops'] = \App\Provider::all();
        return view('demand.dashboard.dashboard', $data);
    }


}
