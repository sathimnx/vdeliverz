<?php

namespace App\Http\Controllers\Api\Demand\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator, DB, Auth;
use Carbon\Carbon;
use App\Http\Resources\CarResource;

class DemandController extends Controller
{
    public function dashboard(){
        try {
            $categories = \App\Service::where('active', 1)->orderBy('order', 'asc')->get(['id', 'name', 'image']);
            $cat1 = $categories->splice(2);
            $notify = auth('api')->user() ? auth('api')->user()->demand_notify_count : 0;
            $wish = auth('api')->user() ? auth('api')->user()->providers_count : 0;
            $other_services = categoriesList();
            $banners = appBanners();

            return response()->json(['status' => true,
                'message' => 'Dashboard Data', 'notification_count' => $notify, 'wishlist_count' => $wish,
                'category1' => $categories, 'category2' => $cat1, 'terms_and_conditions' => '',
                'privacy_policy' => '', 'other_services' => $other_services, 'banners' => $banners
                ]);
        } catch (\Throwable $th) {
            return apiCatchResponse();
            dd($th);
        }
    }

    public function categories(){
        try {
            $other_services = \App\Service::where('active', 1)->orderBy('order', 'asc')->get(['id', 'name', 'image']);
            return response()->json(['status' => true,
            'message' => 'Demand Categories Data',
            'categories' => $other_services,
            'other_categories' => categoriesList()
            ]);
        } catch (\Throwable $th) {
            return apiCatchResponse();
            //throw $th;
        }
    }

    public function services(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'latitude' => 'required',
                'longitude' => 'required'
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            if($request->id == 1){
                return $this->carsList($request);
            }
            $categories = \App\Service::where('active', 1)->where('id', '!=', $request->id)->orderBy('order', 'asc')->get(['id', 'name', 'image']);
            $services = \App\SubService::where('active', 1)->where('service_id', $request->id)->get();
            $result = [];
            foreach ($services as $key => $service) {
                $pro_data = serviceProviders($service->providers, $request);
                $single = [
                    'service_id' => $service->id,
                    'service_name' => $service->name,
                    'service_image' => $service->image,
                    'hour_price' => $service->min_hour_price,
                    'job_price' => $service->min_job_price,
                    'vendors' => $pro_data['count']
                ];
                $result[] = $single;
            }
            return response()->json(['status' => true,
                'message' => 'Service List.',
                'categories' => $categories,
                'services' => $result
            ]);
        } catch (\Throwable $th) {
            // return apiCatchResponse();
            dd($th);
        }
    }

    public function providers(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'service_id' => 'required',
                'latitude' => 'required',
                'longitude' => 'required'
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }

            $service = \App\SubService::where('active', 1)->where('id', $request->service_id)->first();
            $result = [];
            foreach ($service->providers as $key => $provider) {
                $data = scopeIsWithinMaxDistance($provider->latitude, $provider->longitude, $request->latitude, $request->longitude);
                if($data['int_dis'] <= $provider->radius){
                    $single = [
                        'provider_id' => $provider->id,
                        'provider_name' => $provider->name,
                        'provider_image' => $provider->image,
                        'provider_area' => $provider->area,
                        'provider_address' => $provider->street.', '.$provider->area.', '.$provider->city,
                        'hour_price' => $provider->hour,
                        'job_price' => $provider->job,
                        'distance' => $data['distance'],
                        'rating' => $provider->rating_avg,
                        'total_rating' => $provider->rating_count,
                        'available' => $provider->is_available
                    ];
                    $result[] = $single;
                }
            }
            return response()->json(['status' => true,
                'message' => 'Providers List.',
                'services' => $result
            ]);
        } catch (\Throwable $th) {
            // return apiCatchResponse();
            dd($th);
        }
    }

    public function providerView(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'provider_id' => 'required',
                'latitude' => 'required',
                'longitude' => 'required',
                'service_id' => 'required'
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            $today = now()->format('l');
            $time = now()->format('H:i:s');
            $provider = \App\Provider::where('id', $request->provider_id)->where('opened', 1)->where('active', 1)->whereRaw("FIND_IN_SET('".$today."', weekdays)")
            ->where('opens_at', '<=', $time)
            ->where('closes_at', '>=', $time)->first();
            $price = \App\ProviderService::where('provider_id', $request->provider_id)->where('sub_service_id', $request->service_id)->first();
            if(!$provider || !$price){
                return response(['status' => false, 'message' => 'Provider currently not available.']);
            }
            $days = [];
            for ($i=0; $i < 7 ; $i++) {
                $pro = $this->providerAvailability(Carbon::now()->addDays($i)->format('l'), $provider);
                $day = [
                    'date' => Carbon::now()->addDays($i)->format('Y-m-d'),
                    'day' => Carbon::now()->addDays($i)->format('M d D'),
                    'timings' => $pro
                ];
                $days[] = $day;
            }
            $data = scopeIsWithinMaxDistance($provider->latitude, $provider->longitude, $request->latitude, $request->longitude);
            $provider_details = [
                'provider_id' => $provider->id,
                'provider_banner' => $provider->banner_image,
                'provider_name' => $provider->name,
                'provider_area' => $provider->area,
                'provider_address' => $provider->street.', '.$provider->area.', '.$provider->city,
                'description' => $provider->description,
                'distance' => $data['distance'],
                'rating' => $provider->rating_avg,
                'total_rating' => $provider->rating_count,
                'is_wishlist' => $provider->wishlist()
            ];
            $reviews = [];
            foreach ($provider->reviews()->latest()->take(10)->get() as $key => $value) {
                $review = [
                    'username' => $value->user->name,
                    'rating' => $value->rating,
                    'comments' => $value->comment,
                    'time' => $value->timeago,
                ];
                $reviews[] = $review;
            }
            $prices = [
                [
                    'price_type' => 0,
                    'note' => 'hourly Price',
                    'price' => $price->hour
                ],
                [
                    'price_type' => 1,
                    'note' => 'Job Price',
                    'price' => $price->job
                ]
            ];
            return response(['status' => true, 'message' => 'Provider Details.', 'provider_details' => $provider_details, 'price_details' => $prices, 'slots' => $days, 'reviews' => $reviews]);
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function booking(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'provider_id' => 'required',
                'service_id' => 'required',
                'date' => 'required',
                'device_id' => 'required',
                'price_type' => 'required',
                'total_hours' => 'required_if:price_type,0',
                'slot_id' => 'required',
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }

            $booking = auth('api')->user()
                        ? \App\Booking::where('user_id', auth('api')->user()->id)->where('status', 0)->where('provider_sub_service_id', '!=', null)->first()
                        : \App\Booking::where('device_id', $request->device_id)->where('status', 0)->where('provider_sub_service_id', '!=', null)->first();
            $provider_service = \App\ProviderService::where('provider_id', $request->provider_id)->where('sub_service_id', $request->service_id)->first();
            $slot = \App\Slot::findOrFail($request->slot_id);
            $provider = \App\Provider::findOrFail($request->provider_id);
            $booking = $booking ?? new \App\Booking;
            $booking->device_id = $request->device_id;
            $booking->user_id = auth('api')->user()->id ?? null;
            $booking->provider_id = $request->provider_id;
            $booking->sub_service_id = $request->service_id;
            $booking->provider_sub_service_id = $provider_service->id;
            $booking->service_name = $provider_service->subService->name;
            $booking->provider_details = json_encode($provider);
            $booking->booked_at = $request->date;
            $booking->type = $request->price_type;
            $booking->charge = $request->price_type == 1 ? $provider_service->job : $provider_service->hour;
            $booking->hours = $request->total_hours;
            $booking->slot_id = $request->slot_id;
            $booking->from = $slot->from;
            $booking->to = $slot->to;
            $booking->travel_charge = $provider->delivery_charge;
            $booking->total_amount = $request->price_type == 1 ? $booking->charge : number_format($provider_service->hour * $booking->hours, 2);
            $booking->payable_amount = number_format($booking->total_amount + $provider->delivery_charge, 2);
            $booking->save();
            return response()->json(['status' => true, 'message' => 'Booked Successfully']);
        } catch (\Throwable $th) {
            dd($th);
        }
    }
    public function addInstructions(Request $request){
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required',
            'instructions' => 'required',
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response()->json(['status' => false, 'message' => $errors]);
        }
        \App\Booking::findOrFail($request->booking_id)->update(['instructions' => $request->instructions]);
        return response()->json(['status' => true, 'message' => 'instructions added.']);
    }

    private function providerAvailability($day, $provider){
        $slots = \App\Slot::where('provider_id', $provider->id)->whereRaw("FIND_IN_SET('".$day."', weekdays)")->where('active', 1)->where('available', '>', 0)->get();
        $result = [];
        foreach ($slots as $key => $value) {
            $single = [
                'slot_id' => $value->id,
                'from' => $value->from_time,
                'to' => $value->to_time
            ];
            $result[] = $single;
        }
        return $result;
    }

    private function carsList($request){
        $carProviders = \App\CarProvider::where('active', 1)->get()->unique('provider_id');
        // $carProviders = $carProviders->unique('provider_id');
        $available = [];
        foreach ($carProviders as $key => $carProvider) {
            $data = scopeIsWithinMaxDistance($carProvider->provider->latitude, $carProvider->provider->longitude, $request->latitude, $request->longitude);
            if($data['int_dis'] <= $carProvider->provider->radius){
                $available[] = [
                    'provider_id' => $carProvider->provider_id,
                    'car_provider_id' => $carProvider->id,
                    'provider_name' => $carProvider->provider->name,
                    'provider_image' => $carProvider->provider->image,
                    'provider_area' => $carProvider->provider->area,
                    'provider_address' => $carProvider->provider->street.', '.$carProvider->provider->area.', '.$carProvider->provider->city,
                    'per_day' => $carProvider->day,
                    'per_month' => $carProvider->month,
                    'distance' => $data['distance'],
                    'rating' => $carProvider->rating_avg,
                    'total_rating' => $carProvider->rating_count,
                    'available' => $carProvider->provider->is_available
                ];
            }
        }
        $categories = \App\Service::where('active', 1)->where('id', '!=', $request->id)->orderBy('order', 'asc')->get(['id', 'name', 'image']);
        return response(['status' => true,
        'message' => 'Cars List.',
        'categories' => $categories,
        'services' => $available]);
    }


}
