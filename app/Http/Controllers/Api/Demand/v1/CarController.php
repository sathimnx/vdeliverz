<?php

namespace App\Http\Controllers\Api\Demand\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator, DB, Auth;
use Carbon\Carbon;

class CarController extends Controller
{
    public function availableCars(Request $request){
        $validator = Validator::make($request->all(), [
            'provider_id' => 'required',
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response()->json(['status' => false, 'message' => $errors]);
        }
        $carProviders = \App\CarProvider::where('provider_id', $request->provider_id)->get();
        $available = [];
        foreach ($carProviders as $key => $carProvider) {
            $available[] = [
                'car_provider_id' => $carProvider->id,
                'car_name' => $carProvider->car->name,
                'car_image' => $carProvider->img_url,
                'day_price' => $carProvider->day,
                'month_price' => $carProvider->month,
                'week_price' => $carProvider->week,
                'rating' => $carProvider->rating_avg,
                'total_rating' => $carProvider->rating_count,
            ];
        }

        return response(['status' => true, 'message' => 'Availble Cars', 'total_cars' => count($available), 'cars' => $available]);
    }

    public function car(Request $request){
        $validator = Validator::make($request->all(), [
            "car_provider_id" => 'required'
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response()->json(['status' => false, 'message' => $errors]);
        }
        $carProvider = \App\CarProvider::findOrFail($request->car_provider_id);
        $car_details = [
            'car_provider_id' => $carProvider->id,
            'car_name' => $carProvider->car->name,
            'car_image' => $carProvider->img_url,
            'provider_area' => $carProvider->provider->area,
            'provider_address' => $carProvider->provider->street.', '.$carProvider->provider->area.', '.$carProvider->provider->city,
            'rating' => $carProvider->rating_avg,
            'total_rating' => $carProvider->rating_count,
            'per_day' => $carProvider->day,
            'per_week' => $carProvider->week,
            'per_month' => $carProvider->month,
            'is_fav' => $carProvider->wishlist(),
            'about' => $carProvider->specs()->get(['id as about_id', 'name', 'icon']),
            'deposit' => $carProvider->deposit,
            'delivery_charge' => $carProvider->provider->delivery_charge,
            'payable_total' => number_format($carProvider->deposit + $carProvider->provider->delivery_charge, 2)
        ];
        $reviews = [];
        foreach ($carProvider->reviews()->latest()->take(10)->get() as $key => $value) {
            $review = [
                'username' => $value->user->name,
                'rating' => $value->rating,
                'comments' => $value->comment,
                'time' => $value->timeago,
            ];
            $reviews[] = $review;
        }
        return response(['status' => true, 'message' => 'Car details', 'currency' => $carProvider->provider->currency, 'car_details' => $car_details, 'reviews' => $reviews]);
    }

    public function carBooking(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'car_provider_id' => 'required',
                'pick_up_date' => 'required|date_format:Y-m-d',
                'pick_up_time' => 'required|date_format:H:i:s',
                'device_id' => 'required',
                'price_type' => 'required',
                'pick_type' => 'required',
                'drop_off_date' => 'required_if:price_type,1|date_format:Y-m-d',
                'drop_off_time' => 'required_if:price_type,1|date_format:H:i:s',
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            $pick_up = Carbon::parse($request->pick_up_date.' '.$request->pick_up_time)->format('Y-m-d H:i:s');
            if(Carbon::parse($pick_up) < now()){
                return response(['status' => false, 'message' => 'Invalid Date!']);
            }
            $booking = auth('api')->user()
                        ? \App\Booking::where('user_id', auth('api')->user()->id)->where('status', 0)->where('car_provider_id', '!=', null)->first()
                        : \App\Booking::where('device_id', $request->device_id)->where('status', 0)->where('car_provider_id', '!=', null)->first();
            $carProvider = \App\CarProvider::find($request->car_provider_id);
            switch ($request->price_type) {
                case 1:
                    $drop_off = Carbon::parse($request->drop_off_date.' '.$request->drop_off_time)->format('Y-m-d H:i:s');
                    $first = Carbon::parse($drop_off);
                    $second = Carbon::parse($pick_up);
                    $days_count = $first->diffInDays($second);
                    $charge = $carProvider->day * $days_count;
                    $per = $carProvider->day;
                    break;
                case 2:
                    $charge = $carProvider->week;
                    $drop_off = Carbon::parse($pick_up)->addWeek()->format('Y-m-d H:i:s');
                    $per = $carProvider->week;
                    break;
                case 3:
                    $charge = $carProvider->month;
                    $drop_off = Carbon::parse($pick_up)->addMonth()->format('Y-m-d H:i:s');
                    $per = $carProvider->month;
                    break;
                default:
                    $charge = null;
                    $drop_off = null;
                    $per = null;
                    break;
            }
            $found = $carProvider->hasBooking($pick_up, $drop_off, $request->device_id);
            if($found){
                return response(['status' => false, 'message' => 'Car not available for selected dates. Please try other dates!']);
            }
            $delivery_charge = $request->pick_type == 1 ? $carProvider->provider->delivery_charge : 0;
            $booking = $booking ?? new \App\Booking;
            $booking->device_id = $request->device_id;
            $booking->user_id = auth('api')->user()->id ?? null;
            $booking->provider_id = $carProvider->provider_id;
            $booking->car_provider_id = $carProvider->id;
            $booking->car_id = $carProvider->car_id;
            $booking->car_details = json_encode($carProvider->specs);
            $booking->car_name = $carProvider->car->name;
            $booking->provider_details = json_encode($carProvider->provider);
            $booking->pick_up = $pick_up;
            $booking->drop_off = $drop_off;
            $booking->pick_type = $request->pick_type;
            $booking->type = $request->price_type;
            $booking->days = $days_count ?? 1;
            $booking->charge = $per;
            $booking->travel_charge = $delivery_charge;
            $booking->total_amount = number_format($charge, 2);
            $booking->amount_paid = $carProvider->deposit;
            $booking->payable_amount = number_format($booking->total_amount + $delivery_charge, 2);
            $booking->save();
            return response()->json(['status' => true, 'message' => 'Booked Successfully']);
        } catch (\Throwable $th) {
            return apiCatchResponse();
            dd($th);
        }
    }
}
