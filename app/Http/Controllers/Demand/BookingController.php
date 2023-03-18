<?php

namespace App\Http\Controllers\Demand;

use App\Booking;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth, DB, Validator, Session;
use App\DemandAuthorizable;

class BookingController extends Controller
{
    use DemandAuthorizable;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth()->user()->hasAnyRole('admin')) {
            $data['bookings'] = Booking::where('confirmed_at', '!=', null)->latest()->paginate(10);
            $data['shops'] = \App\Provider::all();
        }else{
            $provider_id = auth()->user()->provider->id;
            $data['bookings'] = Booking::where('confirmed_at', '!=', null)->where('provider_id', $provider_id)->latest()->paginate(10);
        }
        if (request()->ajax()) {
            if(isset(request()->search) && !empty(request()->search)){
                if (auth()->user()->hasAnyRole('admin')) {
                    $data['bookings'] = Booking::where('search', 'Like', '%'.request()->search.'%')->where('confirmed_at', '!=', null)
                    ->latest()->paginate(10);
                }else{
                    $data['bookings'] = Booking::where('provider_id', $provider_id)->where('search', 'Like', '%'.request()->search.'%')->where('confirmed_at', '!=', null)
                    ->latest()->paginate(10);
                }

            }
                Session::put(['prev_page_no' => $data['bookings']->currentPage()]);
                return view('demand.booking.booking_table', array('bookings' => $data['bookings']))->render();
            }
        return view('demand.booking.bookings', $data);
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function show(Booking $booking)
    {
        $data['booking'] = $booking;
        if($booking->car_name != null){
            return view('demand.booking.car_booking_detail', $data);
        }
        return view('demand.booking.booking_detail', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function edit(Booking $booking)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Booking $booking)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function destroy(Booking $booking)
    {
        //
    }

    public function reviewOrder(Request $request){
        try {
            // 1 for successfully booked
            // 2 for booking accepted
            // 3 for booking assigned
            // 4 for booking rejected by vendor
            // 5 for booking canceled by customer
            // 6 for booking Completed
            $order = \App\Booking::find($request->order_id);
            $order->status = $request->action;
            if ($request->action == 1){
                $order->accepted_at = now();
            }

            if($request->action == 4){
                $order->rejected_at = now();
            }
            if($request->action == 6){
                $order->completed_at = now();
            }
            $order->save();
            flash()->info('Booking status changed.');
            return back();
        }catch (\Throwable $th){
            return catchResponse();
            dd($th);
        }
    }

    public function showReview(Request $request){
        // 1 for successfully booked
        // 2 for booking accepted
        // 3 for booking assigned
        // 4 for booking rejected by vendor
        // 5 for booking canceled by customer
        // 6 for booking Completed
        $data['order'] = \App\Booking::find($request->id);
        return view('demand.booking.dynamic', array('order' => $data['order']))->render();
    }

    public function filter($shop, $type){
        try {
            if($shop === 'all' && $type == 'all'){
            return redirect(route('demand.bookings.index'));
        }
        if(Auth::user()->hasAnyRole('admin')){
            $data['shops'] = \App\Provider::all();
            if($shop == 'all'){
                $data['bookings'] = \App\Booking::where('status', $type)->where('confirmed_at', '!=', null)->paginate(10);
            }
            if($shop != 'all'){
                $data['bookings'] = \App\Booking::where('provider_id', $shop)->where('confirmed_at', '!=', null)->where('status', $type)->paginate(10);
            }
            if($type == 'all'){
                $data['bookings'] = \App\Booking::where('provider_id', $shop)->where('confirmed_at', '!=', null)->paginate(10);
            }
        }
        if(auth()->user()->hasAnyRole('provider')){
            if( Auth::user()->provider->id != $shop){
                abort(404);
            }
            if($type != 'all'){
                $data['bookings'] = \App\Booking::where('provider_id', $shop)->where('confirmed_at', '!=', null)->where('status', $type)->paginate(10);
            }
            if($type == 'all'){
                $data['bookings'] = \App\Booking::where('provider_id', $shop)->orders()->where('confirmed_at', '!=', null)->paginate(10);
            }
            $data['shop'] = Auth::user()->shop;

        }
        $data['bookingss_count'] = $data['bookings']->total();
        if (request()->ajax()) {
            return view('demand.booking.booking_table', array('bookings' => $data['bookings']))->render();
        }
        return view('demand.booking.bookings', $data);
        } catch (\Throwable $th) {
            // return catchResponse();
            dd($th);
        }
    }
    public function bookFilter($book, $shop, $type){
        try {
            if($book === 'all'){
            return redirect(route('demand.bookings.filter', [$shop, $type]));
        }
        switch ($book) {
            case 'services':
                if(Auth::user()->hasAnyRole('admin')){
                    $data['shops'] = \App\Provider::all();
                    if($shop == 'all'){
                        $data['bookings'] = \App\Booking::where('status', $type)->where('confirmed_at', '!=', null)->where('provider_sub_service_id', '!=', null)->paginate(10);
                    }
                    if($shop != 'all'){
                        $data['bookings'] = \App\Booking::where('provider_id', $shop)->where('confirmed_at', '!=', null)->where('status', $type)->where('provider_sub_service_id', '!=', null)->paginate(10);
                    }
                    if($type == 'all'){
                        $data['bookings'] = \App\Booking::where('provider_id', $shop)->where('confirmed_at', '!=', null)->where('provider_sub_service_id', '!=', null)->paginate(10);
                    }
                    if($type == 'all' && $shop == 'all'){
                        $data['bookings'] = \App\Booking::where('confirmed_at', '!=', null)->where('provider_sub_service_id', '!=', null)->paginate(10);
                    }
                }
                if(auth()->user()->hasAnyRole('provider')){
                    if( Auth::user()->provider->id != $shop){
                        abort(404);
                    }
                    if($type != 'all'){
                        $data['bookings'] = \App\Booking::where('provider_id', $shop)->where('confirmed_at', '!=', null)->where('provider_sub_service_id', '!=', null)->where('status', $type)->paginate(10);
                    }
                    if($type == 'all'){
                        $data['bookings'] = \App\Booking::where('provider_id', $shop)->orders()->where('confirmed_at', '!=', null)->where('provider_sub_service_id', '!=', null)->paginate(10);
                    }
                    $data['shop'] = Auth::user()->shop;

                }
                break;
            case 'cars':
                if(Auth::user()->hasAnyRole('admin')){
                    $data['shops'] = \App\Provider::all();
                    if($shop == 'all'){
                        $data['bookings'] = \App\Booking::where('status', $type)->where('confirmed_at', '!=', null)->where('car_provider_id', '!=', null)->paginate(10);
                    }
                    if($shop != 'all'){
                        $data['bookings'] = \App\Booking::where('provider_id', $shop)->where('confirmed_at', '!=', null)->where('status', $type)->where('car_provider_id', '!=', null)->paginate(10);
                    }
                    if($type == 'all'){
                        $data['bookings'] = \App\Booking::where('provider_id', $shop)->where('confirmed_at', '!=', null)->where('car_provider_id', '!=', null)->paginate(10);
                    }
                    if($type == 'all' && $shop == 'all'){
                        $data['bookings'] = \App\Booking::where('confirmed_at', '!=', null)->where('car_provider_id', '!=', null)->paginate(10);
                    }
                }
                if(auth()->user()->hasAnyRole('provider')){
                    if( Auth::user()->provider->id != $shop){
                        abort(404);
                    }
                    if($type != 'all'){
                        $data['bookings'] = \App\Booking::where('provider_id', $shop)->where('confirmed_at', '!=', null)->where('car_provider_id', '!=', null)->where('status', $type)->paginate(10);
                    }
                    if($type == 'all'){
                        $data['bookings'] = \App\Booking::where('provider_id', $shop)->orders()->where('confirmed_at', '!=', null)->where('car_provider_id', '!=', null)->paginate(10);
                    }
                    $data['shop'] = Auth::user()->shop;

                }
                break;

            default:
                $field = null;
                break;
        }

        $data['bookingss_count'] = $data['bookings']->total();
        if (request()->ajax()) {
            return view('demand.booking.booking_table', array('bookings' => $data['bookings']))->render();
        }
        return view('demand.booking.bookings', $data);
        } catch (\Throwable $th) {
            return catchResponse();
            dd($th);
        }
    }
}