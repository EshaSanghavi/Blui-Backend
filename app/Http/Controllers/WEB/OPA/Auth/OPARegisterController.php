<?php

namespace App\Http\Controllers\WEB\OPA\Auth;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\OPA;
use App\Models\User;
use Carbon\Carbon;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Auth;
use Hash;
use Image;
use File;
use Mail;
use Str;

class OPARegisterController extends Controller
{
    public function registerPage()
    {   
        return view('opa.register');
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $rules = [
            'name'=>'required',
            'aadhar_number' => 'required',
            'aadhar_card' => 'required|file|mimes:jpg,png,pdf',
            'pan_number' => 'required',
            'pan_card' => 'required|file|mimes:jpg,png,pdf',
            'phone'=>'required',
            'email'=>'required',
            'password' => ['required', 'string', 'confirmed']
        ];
        
        $customMessages = [
            'name.required' => trans('admin_validation.Name is required'),
            'password.required' => trans('admin_validation.Password is required'),
            // 'password.min' => trans('admin_validation.Password is too short'),
            'password.confirmed' => trans('admin_validation.Passwords do not match'),

            'aadhar_number' => trans('admin_validation.Aadhar Card Number is required'),
            'aadhar_number.unique' => trans('admin_validation.Aadhar Card Number already exists'),
            'aadhar_card.required' => trans('admin_validation.Aadhar Card is required'),
            'aadhar_card.mimes' => trans('admin_validation.Aadhar Card must be a file of type: jpeg, png, pdf.'),

            'pan_number' => trans('admin_validation.Pan Card Number is required'),
            'pan_number.unique' => trans('admin_validation.Pan Card Number already exists'),
            'pan_card.required' => trans('admin_validation.Pan Card is required'),
            'pan_card.mimes' => trans('admin_validation.Pan Card must be a file of type: jpeg, png, pdf.'),

            'phone.required' => trans('admin_validation.Phone number is required'),
            'phone.unique' => trans('admin_validation.Phone number already exists'),

            'email.required' => trans('admin_validation.Email is required'),
            'email.unique' => trans('admin_validation.Email already exist')
        ];
        $this->validate($request, $rules, $customMessages);
        
        
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->phone = $request->phone;
        $user->is_opa = 1;
        $user->save();
        $id = $user->id;


        $opa = new OPA();
        $opa->user_id = $id;

        $opa->name = $request->name;

        $opa->business_name = $request->business_name;

        $opa->aadhar_number = $request->aadhar_number;
        if($request->aadhar_card){
            $extention = $request->aadhar_card->getClientOriginalExtension();
            $image_name = Str::slug($request->seller_name).date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $image_name = 'uploads/custom-images/'.$image_name;
            Image::make($request->aadhar_card)
                ->save($image_name);
            $opa->aadhar_card=$image_name;
        }

        $opa->pan_number = $request->pan_number;
        if($request->pan_card){
            $extention = $request->pan_card->getClientOriginalExtension();
            $image_name = Str::slug($request->seller_name).date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $image_name = 'uploads/custom-images/'.$image_name;
            Image::make($request->pan_card)
                ->save($image_name);
            $opa->pan_card=$image_name;
        }
        
        $opa->phone = $request->phone;
        $opa->email = $request->email;

        if ($opa->save()) {
            // Query was successful
            $referral_code = (int)$opa->id + 2175;
            $referral_code = 'AB'.$referral_code;
            $opa->referral_code = $referral_code;
            $opa->save();
        } else {
            // Query failed
            $user->delete();
        }
        
        $notification=trans('admin_validation.Created Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('opa.login')->with($notification);

    }
}
