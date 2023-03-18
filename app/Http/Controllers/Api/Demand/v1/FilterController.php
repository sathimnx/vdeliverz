<?php

namespace App\Http\Controllers\Api\Demand\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator, DB, Session, Auth;

class FilterController extends Controller
{
    public function filterServices(Request $request){
        $validator = Validator::make($request->all(), [
            'type' => 'required',
            "id" => 'required'
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response()->json(['status' => false, 'message' => $errors]);
        }
        // type 1 - A-Z
        // type 2 - Z-A
        // type 3 - Hourly price low - High
        // type 4 - Hourly price High - Low
        // type 5 - Job price Low - High
        // type 6 - Job price High - Low
        switch ($request->type) {
            case '1':
                $services = \App\SubService::where('active', 1)->where('service_id', $request->id)->orderBy('name', 'asc')->get();
                break;
            case '2':
                $services = \App\SubService::where('active', 1)->where('service_id', $request->id)->orderBy('name', 'desc')->get();
                break;
            default:
                $services = \App\SubService::where('active', 1)->where('service_id', $request->id)->get();
                break;
        }
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
        switch ($request->type) {
            case '3':
                $result = collect($result)->sortBy('hour_price');
                break;
            case '4':
                $result = collect($result)->sortByDesc('hour_price');
                break;
            case '5':
                $result = collect($result)->sortBy('job_price');
                break;
            case '6':
                $result = collect($result)->sortByDesc('job_price');
                break;
            default:
                $result;
                break;
        }
        return response(['status' => true, 'message' => 'Filtered Services', 'services' => $result]);
    }

    public function filterProviders(Request $request){
        $validator = Validator::make($request->all(), [
            'type' => 'required',
            "service_id" => 'required',
            // 'latitude' => 'required',
            // 'longitude' => 'required'
            'address_id' => 'required'
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response()->json(['status' => false, 'message' => $errors]);
        }
        // type 1 - A-Z
        // type 2 - Z-A
        // type 3 - Hourly price low - High
        // type 4 - Hourly price High - Low
        // type 5 - Job price Low - High
        // type 6 - Job price High - Low
        // type 7 - popularity
        // type 8 - available
        // type 9 - rating 4+


        $service = \App\SubService::where('active', 1)->where('id', $request->service_id)->first();
        if(!$service){
            return response(['status' => false, 'message' => 'Service not found.']);
        }
        $result = [];
        foreach ($service->providers as $key => $provider) {
            $data = canShowShop($provider->latitude, $provider->longitude, $request->latitude, $request->longitude);
            $providerService = \App\ProviderService::where('provider_id', $provider->id)->where('sub_service_id', $service->id)->first();
            if($data['int_dis'] <= $provider->radius){
                $single = [
                    'provider_id' => $provider->id,
                    'provider_name' => $provider->name,
                    'provider_image' => $provider->image,
                    'provider_area' => $provider->area,
                    'provider_address' => $provider->street.', '.$provider->area.', '.$provider->city,
                    'hour_price' => $providerService->hour,
                    'job_price' => $providerService->job,
                    'distance' => $data['distance'],
                    'rating' => $provider->rating_avg,
                    'total_rating' => $provider->rating_count,
                    'available' => $provider->is_available
                ];
                $result[] = $single;
            }
        }
        switch ($request->type) {
            case '1':
                $result = collect($result)->sortBy('provider_name');
                break;
            case '2':
                $result = collect($result)->sortByDesc('provider_name');
                break;
            case '3':
                $result = collect($result)->sortBy('hour_price');
                break;
            case '4':
                $result = collect($result)->sortByDesc('hour_price');
                break;
            case '5':
                $result = collect($result)->sortBy('job_price');
                break;
            case '6':
                $result = collect($result)->sortByDesc('job_price');
                break;
            case '7':
                $result = collect($result)->sortByDesc('rating');
                break;
            case '8':
                $result = collect($result)->reject(function ($value, $key) {
                    return $value['available'] == false;
                });
                break;
            case '9':
                $result = collect($result)->reject(function ($value, $key) {
                    return $value['rating'] < 4;
                });
                break;
            default:
                $result;
                break;
        }
        return response(['status' => true, 'message' => 'Filtered Services', 'services' => $result->values()]);
    }

    public function filterCarProviders(Request $request){
        $validator = Validator::make($request->all(), [
            'type' => 'required',
            'latitude' => 'required',
            'longitude' => 'required'
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response()->json(['status' => false, 'message' => $errors]);
        }
        // type 1 - A-Z
        // type 2 - Z-A
        // type 3 - Day price low - High
        // type 4 - Day price High - Low
        // type 5 - Month price Low - High
        // type 6 - Month price High - Low
        // type 7 - popularity
        // type 8 - available
        // type 9 - rating 4+


        $carProviders = \App\CarProvider::where('active', 1)->get();
        if($carProviders->isEmpty() || $request->type > 9){
            return response(['status' => false, 'message' => 'Providers not found.']);
        }
        $result = [];
        foreach ($carProviders as $key => $carProvider) {
            $data = scopeIsWithinMaxDistance($carProvider->provider->latitude, $carProvider->provider->longitude, $request->latitude, $request->longitude);
            if($data['int_dis'] <= $carProvider->provider->radius){
                $single = [
                    'provider_id' => $carProvider->provider_id,
                    'car_provider_id' => $carProvider->id,
                    'car_name' => $carProvider->car->name,
                    'car_image' => $carProvider->img_url,
                    'provider_area' => $carProvider->provider->area,
                    'provider_address' => $carProvider->provider->street.', '.$carProvider->provider->area.', '.$carProvider->provider->city,
                    'per_day' => $carProvider->day,
                    'per_month' => $carProvider->month,
                    'distance' => $data['distance'],
                    'rating' => $carProvider->rating_avg,
                    'total_rating' => $carProvider->rating_count,
                    'available' => $carProvider->provider->is_available
                ];
                $result[] = $single;
            }
        }
        switch ($request->type) {
            case '1':
                $result = collect($result)->sortBy('car_name');
                break;
            case '2':
                $result = collect($result)->sortByDesc('car_name');
                break;
            case '3':
                $result = collect($result)->sortBy('per_day');
                break;
            case '4':
                $result = collect($result)->sortByDesc('per_day');
                break;
            case '5':
                $result = collect($result)->sortBy('per_month');
                break;
            case '6':
                $result = collect($result)->sortByDesc('per_month');
                break;
            case '7':
                $result = collect($result)->sortByDesc('rating');
                break;
            case '8':
                $result = collect($result)->reject(function ($value, $key) {
                    return $value['available'] == false;
                });
                break;
            case '9':
                $result = collect($result)->reject(function ($value, $key) {
                    return $value['rating'] < 4;
                });
                break;
            default:
                $result;
                break;
        }
        return response(['status' => true, 'message' => 'Filtered providers', 'providers' => $result->values()]);
    }

    public function searchServices(Request $request){
        $validator = Validator::make($request->all(), [
            'search' => 'required',
            'latitude' => 'required',
            'longitude' => 'required'
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response()->json(['status' => false, 'message' => $errors]);
        }

        $services = \App\SubService::where('active', 1)->where('name', 'LIKE', '%'.$request->search.'%')->get();
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

        return response(['status' => true, 'message' => 'Filtered Services', 'services' => $result]);
    }
}
