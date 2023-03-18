<?php

namespace App\Http\Controllers\Api\Demand\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator, DB, Auth;
use Carbon\Carbon;

class ServiceController extends Controller
{
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Address  $address
     * @return \Illuminate\Http\Response
     */
    public function giveRating(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'provider_id' => 'required',
                'rating' => 'required',
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            $review = new \App\Review;
            $review->storeReviewForProvider($request->provider_id, $request->rating, $request->comments);
            return response()->json(['status' => true, 'message' => 'Rating Submitted']);
        } catch (\Throwable $th) {
            return apiCatchResponse();
            dd($th);
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Address  $address
     * @return \Illuminate\Http\Response
     */
    public function giveCarRating(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'car_provider_id' => 'required',
                'rating' => 'required',
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            $review = new \App\Review;
            $review->storeReviewForCar($request->car_provider_id, $request->rating, $request->comments);
            return response()->json(['status' => true, 'message' => 'Rating Submitted']);
        } catch (\Throwable $th) {
            return apiCatchResponse();
            dd($th);
        }
    }

    public function bookingList(){

        $bookings = auth('api')->user()->bookings()->where('provider_sub_service_id', '!=', null)->latest()->get();
        $result = [];
        foreach ($bookings as $key => $booking) {
            $single = [
                'booking_id' => $booking->id,
                'referral' => $booking->referral,
                'service_name' => $booking->service_name,
                'image' => $booking->provider->image,
                'provider_name' => $booking->provider->name,
                'booked_date' => $booking->api_book.'-'.Carbon::parse($booking->from)->format('h:i A'),
                'delivery_charge' => $booking->travel_charge,
                'grand_total' => $booking->payable_amount,
                'instructions' => $booking->instructions,
                'booking_status' => $booking->status,
                'booking_hint' => $booking->book_state,
                'fair' => $booking->type == 1 ? 'Per Job : '.$booking->charge : 'Per Hour : '.$booking->charge,
                'full_time_left' => $booking->confirmed_at,
                'time_left_min' => $booking->time_left >= 5 ? 0 : $booking->cancel_time['min'],
                'time_left_sec' => $booking->time_left >= 5 ? 0 : $booking->cancel_time['sec'],
                'can_cancel' => !($booking->time_left >= 5),
                'mobile' => $booking->provider->user->mobile
            ];
            $result[] = $single;
        }
        return response(['status' => true, 'message' => 'Booking Lists.', 'bookings' => $result]);
    }

    public function bookedCarsList(){
        $bookings = auth('api')->user()->bookings()->where('car_provider_id', '!=', null)->latest()->get();
        $result = [];
        foreach ($bookings as $key => $booking) {
            switch ($booking->type) {
                case 1:
                    $text = 'Per Day';
                    break;
                case 2:
                    $text = 'Per Week';
                    break;
                case 3:
                    $text = 'Per Month';
                    break;

                default:
                    $text = null;
                    break;
            }
            $single = [
                'booking_id' => $booking->id,
                'referral' => $booking->referral,
                'car_name' => $booking->car_name,
                'pick_date' => $booking->pick_date,
                'pick_time' => $booking->pick_time,
                'drop_date' => $booking->drop_date,
                'drop_time' => $booking->drop_time,
                "sub_total" => $booking->total_amount,
                'taxes' => $booking->taxes ?? 0,
                'delivery_charge' => $booking->travel_charge,
                'grand_total' => $booking->payable_amount,
                "instructions" => $booking->instructions,
                'image' => $booking->provider->image,
                'provider_name' => $booking->provider->name,
                'booked_date' => $booking->api_book.'-'.Carbon::parse($booking->from)->format('h:i A'),
                'grand_total' => $booking->payable_amount,
                'instructions' => $booking->instructions,
                'booking_status' => $booking->status,
                'booking_hint' => $booking->book_state,
                'fair' => $text,
                'full_time_left' => $booking->confirmed_at,
                'time_left_min' => $booking->time_left >= 5 ? 0 : $booking->cancel_time['min'],
                'time_left_sec' => $booking->time_left >= 5 ? 0 : $booking->cancel_time['sec'],
                'can_cancel' => !($booking->time_left >= 5),
                'mobile' => $booking->provider->user->mobile
            ];
            $result[] = $single;
        }
        return response(['status' => true, 'message' => 'Booking Lists.', 'bookings' => $result]);
    }

    public function cancelBooking(Request $request){
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required'
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response()->json(['status' => false, 'message' => $errors]);
        }
        $booking = \App\Booking::where('id', $request->booking_id)->where('user_id', auth('api')->user()->id)->first();
        if(!$booking){
            return response(['status' => false, 'message' => 'Invalid Booking!']);
        }
        $booking->update(['status' => 5, 'cancel_reason' => $request->cancel_reason, 'cancelled_at' => $booking->cancelled_at ?? now()]);
        return response()->json(['status' => true, 'message' => 'Booking Cancelled!']);
    }

    public function addBookingAddress(Request $request){
        $validator = Validator::make($request->all(), [
            'address_id' => 'required'
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response()->json(['status' => false, 'message' => $errors]);
        }
        $address = \App\Address::where('id', $request->address_id)->where('user_id', auth('api')->user()->id)->first();
        $booking = \App\Booking::where('user_id', auth('api')->user()->id)->where('status', 0)->where('car_provider_id', '!=', null)->first();
        if(!$address || !$booking){
            return response(['status' => false, 'message' => 'Invalid address!']);
        }
        $booking->update(['address_id' => $address->id, 'address_details' => json_encode($address), 'search' => $booking->prefix.$booking->id]);
        return response(['status' => true, 'message' => 'Address added.']);
    }



}