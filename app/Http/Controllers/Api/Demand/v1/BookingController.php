<?php

namespace App\Http\Controllers\Api\Demand\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator, Auth, DB;
use \Carbon\Carbon;
use App\Notifications\DemandNotification;
use Notification;

class BookingController extends Controller
{
    public function summary(Request $request){
        $validator = Validator::make($request->all(), [
            'address_id' => 'required'
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response()->json(['status' => false, 'message' => $errors]);
        }

        $address = \App\Address::where('id', $request->address_id)->where('user_id', auth('api')->user()->id)->first();
        $booking = \App\Booking::where('user_id', auth('api')->user()->id)->where('status', 0)->where('provider_sub_service_id', '!=', null)->first();
        $distance = scopeIsWithinMaxDistance($booking->provider->latitude, $booking->provider->longitude, $address->latitude, $address->longitude);
        if($distance['int_dis'] > $booking->provider->radius){
            return response()->json(['status' => false, 'message' => 'Service not available for this location! Choose different location.']);
        }
        if(!$address || !$booking){
            return response(['status' => false, 'message' => 'Invalid address!']);
        }
        $booking->update(['address_id' => $address->id, 'address_details' => json_encode($address), 'search' => $booking->prefix.$booking->id]);
        $service_details = [
            'booking_id' => $booking->id,
            'referral' => $booking->referral,
            'service_name' => $booking->service_name,
            'provider_name' => $booking->provider->name,
            'booked_date' => $booking->api_book.'-'.Carbon::parse($booking->from)->format('h:i A'),
            "sub_total" => $booking->total_amount,
            'taxes' => $booking->taxes ?? 0,
            'delivery_charge' => $booking->travel_charge,
            'grand_total' => $booking->payable_amount,
            "instructions" => $booking->instructions
        ];
        $cus_address = json_decode($booking->address_details);
        // 1 for successfully booked
        // 2 for booking accepted
        // 3 for booking assigned
        // 4 for booking rejected by vendor
        // 5 for booking canceled by customer
        // 6 for booking Completed
        $address_details = [
            'vendor' => [
                'name' => $booking->provider->name,
                'address' => $booking->provider->street.', '.$booking->provider->area.', '.$booking->provider->city,
                'mobile' => $booking->provider->user->mobile
            ],
            'customer' => [
                'name' => $booking->user->name,
                'address' => $cus_address->address,
                'mobile' => $booking->user->mobile
            ]
        ];

        return response(['status' => true, 'message' => 'Service Summary', 'service_details' => $service_details, 'address_details' => $address_details]);
    }


    //Book a Service
    public function bookService(Request $request){
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required',
            'payment_type' => 'required|integer',
            'payment_id' => 'required_if:payment_type,1'
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response()->json(['status' => false, 'message' => $errors]);
        }
        $booking = \App\Booking::where('id', $request->booking_id)->where('user_id', auth('api')->user()->id)->where('status', 0)->first();
        if(!$booking){
            return response(['status' => false, 'message' => 'No bookings found!']);
        }
        $booking->update([
            'status' => 1,
            'confirmed_at' => now(),
            'paid' => $request->payment_type,
            'payment' => $request->payment_type,
            'payment_id' => $request->payment_id,
            'commission' => number_format(($booking->total_amount / 100) * $booking->provider->comission, 2)
        ]);
        $user = Auth::user();
        Notification::send($user, new DemandNotification($booking));
        return response(['status' => true, 'message' => 'Booking Successfully Completed.', 'booking_id' => $booking->id]);

    }

    public function bookingDetail(Request $request){
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required',
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response()->json(['status' => false, 'message' => $errors]);
        }
        $booking = \App\Booking::find($request->booking_id);
        $service_details = [
            'booking_id' => $booking->id,
            'referral' => $booking->referral,
            'service_name' => $booking->service_name,
            'provider_name' => $booking->provider->name,
            'booked_date' => $booking->api_book.'-'.Carbon::parse($booking->from)->format('h:i A'),
            "sub_total" => $booking->total_amount,
            // 'taxes' => $booking->taxes,
            'delivery_charge' => $booking->travel_charge,
            'grand_total' => $booking->payable_amount,
            'instructions' => $booking->instructions,
            'payment_type' => $booking->payment,
            'confirmed_at' => $booking->confirmed_at

        ];
        $cus_address = json_decode($booking->address_details);
        $address_details = [
            'vendor' => [
                'name' => $booking->provider->name,
                'address' => $booking->provider->street.', '.$booking->provider->area.', '.$booking->provider->city,
                'mobile' => $booking->provider->user->mobile
            ],
            'customer' => [
                'name' => $booking->user->name,
                'address' => $cus_address->address,
                'mobile' => $booking->user->mobile
            ]
        ];
        return response(['status' => true, 'message' => 'Booking Details',
            'service_details' => $service_details,
            'address_details' => $address_details
            ]);
    }

    public function documents(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'address_proof' => 'required|file|max:1000',
                'id_proof' => 'required|file|max:1000',
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            $booking = \App\Booking::where('user_id', auth('api')->user()->id)->where('status', 0)->where('car_provider_id', '!=', null)->first();
            if(!$booking){
                return response()->json(['status' => false, 'message' => 'No Bookings found']);
            }
            if($request->hasFile('address_proof')){
                if($request->file('address_proof')->isValid())
                {
                    $extension = $request->address_proof->extension();
                    $file_path = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($booking->id)).time()."bk." .$extension;
                    $request->address_proof->move(config('constants.demand_address_proof_image'), $file_path);
                    $booking->address_proof = $file_path;
                }
            }
            if($request->hasFile('id_proof')){
                if($request->file('id_proof')->isValid())
                {
                    $extension = $request->id_proof->extension();
                    $file_path = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($booking->id)).time()."bkid." .$extension;
                    $request->id_proof->move(config('constants.demand_id_proof_image'), $file_path);
                    $booking->id_proof = $file_path;
                }
            }
            $booking->save();
            return response(['status' => true, 'message' => 'Documents Submitted!']);
        } catch (\Throwable $th) {
            return apiCatchResponse();
            dd($th);
        }
    }
    public function carSummary(){
        $booking = \App\Booking::where('user_id', auth('api')->user()->id)->where('status', 0)->where('car_provider_id', '!=', null)->first();
        $distance = scopeIsWithinMaxDistance($booking->provider->latitude, $booking->provider->longitude, $booking->address->latitude, $booking->address->longitude);
        if($distance['int_dis'] > $booking->provider->radius){
            return response()->json(['status' => false, 'message' => 'Service not available for this location! Choose different location.']);
        }
        if(!$booking){
            return response(['status' => false, 'message' => 'No bookings found!']);
        }
        switch ($booking->type) {
            case 1:
                $text = $booking->days.'d * '.$booking->charge;
                break;
            case 2:
                $text = $booking->days.'w * '.$booking->charge;
                break;
            case 3:
                $text = $booking->days.'m * '.$booking->charge;
                break;

            default:
                $text = null;
                break;
        }
        $service_details = [
            'booking_id' => $booking->id,
            'referral' => $booking->referral,
            'car_name' => $booking->car_name,
            'provider_name' => $booking->provider->name,
            'pick_date' => $booking->pick_date,
            'pick_time' => $booking->pick_time,
            'drop_date' => $booking->drop_date,
            'drop_time' => $booking->drop_time,
            'deposit' => $booking->amount_paid,
            'delivery_charge' => $booking->travel_charge,
            'payable_total' => number_format($booking->amount_paid + $booking->travel_charge, 2),
            'text' => $text,
            "sub_total" => $booking->total_amount,
            'taxes' => $booking->taxes ?? 0,
            'grand_total' => number_format($booking->total_amount + $booking->taxes, 2),
            "instructions" => $booking->instructions,
        ];
        $cus_address = json_decode($booking->address_details);
        // 1 for successfully booked
        // 2 for booking accepted
        // 3 for booking assigned
        // 4 for booking rejected by vendor
        // 5 for booking canceled by customer
        // 6 for booking Completed
        $address_details = [
            'vendor' => [
                'name' => $booking->provider->name,
                'address' => $booking->provider->street.', '.$booking->provider->area.', '.$booking->provider->city,
                'mobile' => $booking->provider->user->mobile
            ],
            'customer' => [
                'name' => $booking->user->name,
                'address' => $cus_address->address ?? null,
                'mobile' => $booking->user->mobile
            ]
        ];

        return response(['status' => true, 'message' => 'Service Summary', 'service_details' => $service_details, 'address_details' => $address_details]);

    }
    public function carBookingDetail(Request $request){
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required',
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response()->json(['status' => false, 'message' => $errors]);
        }
        $booking = \App\Booking::find($request->booking_id);
        if(!$booking){
            return response(['status' => false, 'message' => 'No bookings found!']);
        }
        switch ($booking->type) {
            case 1:
                $text = $booking->days.'d * '.$booking->charge;
                break;
            case 2:
                $text = $booking->days.'w * '.$booking->charge;
                break;
            case 3:
                $text = $booking->days.'m * '.$booking->charge;
                break;

            default:
                $text = null;
                break;
        }
        $service_details = [
            'booking_id' => $booking->id,
            'referral' => $booking->referral,
            'car_name' => $booking->car_name,
            'provider_name' => $booking->provider->name,
            'pick_date' => $booking->pick_date,
            'pick_time' => $booking->pick_time,
            'drop_date' => $booking->drop_date,
            'drop_time' => $booking->drop_time,
            'deposit' => $booking->amount_paid,
            'delivery_charge' => $booking->travel_charge,
            'payment_type' => $booking->type,
            'payable_total' => number_format($booking->amount_paid + $booking->travel_charge, 2),
            'text' => $text,
            "sub_total" => $booking->total_amount,
            'taxes' => $booking->taxes ?? 0,
            'grand_total' => number_format($booking->total_amount + $booking->taxes, 2),
            "instructions" => $booking->instructions,
            'confirmed_at' => $booking->confirmed_at
        ];
        $cus_address = json_decode($booking->address_details);
        // 1 for successfully booked
        // 2 for booking accepted
        // 3 for booking assigned
        // 4 for booking rejected by vendor
        // 5 for booking canceled by customer
        // 6 for booking Completed
        $address_details = [
            'vendor' => [
                'name' => $booking->provider->name,
                'address' => $booking->provider->street.', '.$booking->provider->area.', '.$booking->provider->city,
                'mobile' => $booking->provider->user->mobile
            ],
            'customer' => [
                'name' => $booking->user->name,
                'address' => $cus_address->address ?? null,
                'mobile' => $booking->user->mobile
            ]
        ];

        return response(['status' => true, 'message' => 'Car Booked Details', 'service_details' => $service_details, 'address_details' => $address_details]);

    }

}
