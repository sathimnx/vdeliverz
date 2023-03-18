<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Shop;
use DB, Validator, Auth;

class SalesController extends Controller
{
    public function index(){
        try {
            if(auth()->user()->hasAnyRole('admin')){
                $data['shops'] = Shop::with(['orders' => function($q){
                    $q->orderBy('amount', 'desc');
                }])->paginate(10);
            }
            if(auth()->user()->hasAnyRole('vendor')){
                $data['shops'] = Shop::where('user_id', Auth::user()->id)->latest()->paginate(10);
            }
            if (request()->ajax()) {
            if(isset(request()->search) && !empty(request()->search)){
                if(auth()->user()->hasAnyRole('vendor')){
                    $data['shops'] = Shop::where('id', Auth::user()->shop->id)->where('name', 'Like', '%'.request()->search.'%')
                                    ->latest()->paginate(10);
                }else{
                    $data['shops'] = Shop::where('name', 'Like', '%'.request()->search.'%')
                                    ->orWhere('area', 'Like','%'.request()->search.'%')
                                    ->orWhere('street', 'Like','%'.request()->search.'%')
                                    ->orWhereHas('user', function($query){
                                        $query->where('name', 'Like', '%'.request()->search.'%')->orWhere('mobile', 'Like', '%'.request()->search.'%');
                                    })->latest()->paginate(10);
                }

            }

                return view('sales.sales_table', array('shops' => $data['shops']))->render();
            }
            return view('sales.sales', $data ?? NULL);
        } catch (\Throwable $th) {
            return catchResponse();
           dd($th);
        }
    }

    public function show($id){
        try {
            $data['shop'] = \App\Shop::find($id);
            $data['orders'] = $data['shop']->orders()->with('user', 'cart')->where('order_status', 3)->where('pay_status', 0)->paginate(10);
            if (request()->ajax()) {
                $view = view('sales.sales_detail_table', array('orders' => $data['orders']))->render();
                return response()->json(['html'=>$view]);
            }
            return view('sales.sales_detail', $data);
        } catch (\Throwable $th) {
            return catchResponse();
            dd($th);
        }
    }

    public function paidSales($id){
        try {
            $data['shop'] = \App\Shop::find($id);
            $data['orders'] = $data['shop']->orders()->where('pay_status', 1)->paginate(10);
            if (request()->ajax()) {
                $view = view('sales.paid_sales_detail_table', array('orders' => $data['orders']))->render();
                return response()->json(['html'=>$view]);
            }
            return view('sales.paid_sales_detail', $data);
        } catch (\Throwable $th) {
            return catchResponse();
            dd($th);
        }
    }
}