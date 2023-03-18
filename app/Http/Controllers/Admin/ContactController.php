<?php

namespace App\Http\Controllers\Admin;

use App\Charge;
use App\Contact;
use Validator, DB, Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!auth()->user()->hasAnyRole('admin')){
            abort(404);
        }
        $data['contact'] = Contact::first();
        $data['charge'] = Charge::first();
        $data['dTd_charges'] = DB::table('dTd_charges')->get();
        
        //dd($data['charge']);
        return view('contact.contact', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function chargesUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'basic_charge' => 'required',
            'extra_charge' => 'required',
            'basic_km' => 'required',
            'gst_charge' => 'required',
        ]);
        if($validator->fails()){
            // flash()->error($validator->messages()->first());
            return back()->withInput();
        }

//dd($request->dTd_twoWheeler_basicCharge,$request->dTd_twoWheeler_extraCharge,$request->dTd_threeWheeler_basicKM, $request->dTd_threeWheeler_basicCharge,$request->dTd_threeWheeler_extraCharge);
        $contacts = DB::table('charges')->where('id',1)->update(['basic_charge' => $request->basic_charge, 'extra_charge' => $request->extra_charge, 'basic_km' => $request->basic_km,
        'gst_charge' => $request->gst_charge,'dTd_twoWheeler_basicKM' => $request->dTd_twoWheeler_basicKM,'dTd_twoWheeler_basicCharge' => $request->dTd_twoWheeler_basicCharge,'dTd_twoWheeler_extraCharge' => $request->dTd_twoWheeler_extraCharge,
        'dTd_threeWheeler_basicKM' => $request->dTd_threeWheeler_basicKM,'dTd_threeWheeler_basicCharge' => $request->dTd_threeWheeler_basicCharge,'dTd_threeWheeler_extraCharge' => $request->dTd_threeWheeler_extraCharge]);
    //dd($contacts);
    
        if($request->dtd_fourWheeler_basic_km > 0 && $request->dtd_fourWheeler_basic_km > 0) { DB::table('dTd_charges')->where('id',$request->fourWheelerTypeId)->update(['basic_charge'=>$request->dtd_fourWheeler_basic_charge,
        'basic_km'=>$request->dtd_fourWheeler_basic_km,'extra_charge'=>$request->dTd_fourWheeler_extracharge]); }
         
        flash('Delivery Charges Updated')->success();
        return redirect()->back()->with(['charge_message' => 'Charges Updated.']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'wp' => 'required',
            'mobile' => 'required',
        ]);
        if($validator->fails()){
            flash()->error($validator->messages()->first());
            return back()->withInput();
        }
        $contact = Contact::first();
        if(!$contact){
            $contact = new Contact;
        }
        $contact->email = $request->email;
        $contact->wp = '+91'.$request->wp;
        $contact->mobile = '+91'.$request->mobile;
        $contact->save();

        // flash()->success('Contact Updated');
        return redirect()->back()->with(['message' => 'Contacts Updated.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function show(Contact $contact)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function edit(Contact $contact)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Contact $contact)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function destroy(Contact $contact)
    {
        //
    }
    
     public function notificationAdd(Request $request)
    {
        try{
            if($request->title && $request->Msg)
            $title = $request->title;
            $Msg = $request->Msg;
            $file_path ='';
            if($request->hasFile('image')){
                if($request->file('image')->isValid())
                {
                    $extension = $request->image->extension();
                    $file_path = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($request->title)).time()."notification." .$extension;
                    $request->image->move(config('constants.notification_image'), $file_path);
                    //$banner->image = $file_path;
                }
            }
            //env('APP_URL').config('constants.notification_image'). $file_path);
            
            $data=array('title'=>$title,"message"=>$Msg,"image"=>$file_path);
            DB::table('notification_broadcast')->insert($data);
            
             //dd($file_path == '' ? 'sample.png' : env('APP_URL').config('constants.notification_image'). $file_path);
             $notification=[
                'title'=>$request->title,
                'body'=>$request->Msg,
                'icon'=>"ic_launcher",
                'image' => $file_path == '' ? 'sample.png' : env('APP_URL').config('constants.notification_image'). $file_path,
                'sound'=>"true"
            ];
            $fcmNotification=[
                'to'=>"/topics/vdeliverzuser",
                // 'to'=>"ckLQAAxXSjaTYqAMAAhU16:APA91bEY9BOdFFY1qjPr5uLi0G_VW22nxoWiDO9Tj_7Y1Ekw58QjdNpu6kSlZ-ALvrBZpWeqPdGIiLgWN581Zv5U4th5Gj0ByJGk_wjCzX7HyiVAOvNIeWs2utOFwYtd5j8bHlz1rQey",
                'notification'=>$notification
            ];
            // $data = json_encode($data);
            $url = 'https://fcm.googleapis.com/fcm/send';
            // $server_key = $key;
             $key = 'AAAA0DJLyxg:APA91bGeGKXWlssriDffkuQYsTaJSmqtum5o3MeJLLG0sd3rvnGi5LxSVPbOFL3vihJ5ZrHGLjilryWkAZJ1gu0lJxwrMRtcv3Vm1LHEG86fFtBLK42dCTBMz0oF7x0eD9nw5tFUgjN3';  //customer fcm server token
            //$key = 'AAAAH4IRDPA:APA91bFm5lIZHknK0UnHgWcnPbbfNHjeZPQr5RJpL63EKrR3Zg5t9sLyTZwB1j_DWVl22yu-3b3W8L5S-3bJuerNoy54S5Y2K8iVIih0cXRfj5CeduNKJCJQ7J5bo9Vk02WW3k7YNiis';  //delivery boy fcm server token
            $headers = array(
                'Content-Type:application/json',
                'Authorization:key='.$key
            );
            //CURL request to route notification to FCM connection server (provided by Google)
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
            //dd($ch);
            $result = curl_exec($ch);
            // dd($result);
            $res = json_decode($result);
            //dd($res, $result);
            if ($res) {
               //return $result;
            }
            curl_close($ch);
            //return true;
            return redirect()->back()->with(['message' => 'Notification Broadcasted successfully.']);
               
            
        } catch (\Throwable $th) {
            //return catchResponse();
            dd($th);
        }
     }
     
     public function get_fourWheelerCharges(Request $request)
     {
         $dTd_charges = DB::table('dTd_charges')->where('id',$request->fourWheelerTypeId)->select('basic_charge','basic_km','extra_charge')->first();
         
         return response(['basic_charge'=>$dTd_charges->basic_charge,'basic_km'=>$dTd_charges->basic_km,'extra_charge'=>$dTd_charges->extra_charge]);
     }
}
