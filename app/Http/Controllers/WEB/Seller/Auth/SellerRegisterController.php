<?php

namespace App\Http\Controllers\WEB\Seller\Auth;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Vendor;
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

class SellerRegisterController extends Controller
{
    public function registerPage()
    {   
        return view('seller.register');
    }

    public function store(Request $request)
    {
        $rules = [
            'name'=>'required',
            'aadhar_number' => 'required',
            'aadhar_card' => 'required|mimes:jpg,png,pdf',
            'pan_number' => 'required',
            'pan_card' => 'required|mimes:jpg,png,pdf',
            'phone'=>'required',
            'gstin' => 'required',
            'business_name' => 'required',
            'business_address' => 'required',
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'email'=>'required'
        ];
        $customMessages = [
            'name.required' => trans('admin_validation.Name is required'),
            'password.required' => trans('admin_validation.Password is required'),
            'password.min' => trans('admin_validation.Password is too short'),
            'password.confirmed' => trans('admin_validation.Passwords do not match'),

            'aadhar_number' => trans('admin_validation.Aadhar Card Number is required'),
            // 'aadhar_number.unique' => trans('admin_validation.Aadhar Card Number already exists'),
            'aadhar_card.required' => trans('admin_validation.Aadhar Card is required'),
            'aadhar_card.mimes' => trans('admin_validation.Aadhar Card must be a file of type: jpeg, png, pdf.'),

            'pan_number' => trans('admin_validation.Pan Card Number is required'),
            // 'pan_number.unique' => trans('admin_validation.Pan Card Number already exists'),
            'pan_card.required' => trans('admin_validation.Pan Card is required'),
            'pan_card.mimes' => trans('admin_validation.Pan Card must be a file of type: jpeg, png, pdf.'),

            'phone.required' => trans('admin_validation.Phone number is required'),
            // 'phone.unique' => trans('admin_validation.Phone number already exists'),

            'gstin' => trans('admin_validation.GSTIN Number is required'),
            // 'gstin.unique' => trans('admin_validation.GSTIN Number already exists'),
            
            'business_name.required' => trans('admin_validation.Business Name is required'),
            'business_address.required' => trans('admin_validation.Business Address is required'),
            
            'email.required' => trans('admin_validation.Email is required'),
            // 'email.unique' => trans('admin_validation.Email already exist')
        ];
        $this->validate($request, $rules,$customMessages);

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->phone = $request->phone;
        $user->is_vendor = 1;
        $user->save();
        $id = $user->id;


        $vendor = new Vendor();
        $vendor->user_id = $id;

        $vendor->aadhar_number = $request->aadhar_number;
        if($request->aadhar_card){
            $extention = $request->aadhar_card->getClientOriginalExtension();
            $image_name = Str::slug($request->seller_name).date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $image_name = 'uploads/custom-images/'.$image_name;
            Image::make($request->aadhar_card)
                ->save($image_name);
            $vendor->aadhar_card=$image_name;
        }

        $vendor->pan_number = $request->pan_number;
        if($request->pan_card){
            $extention = $request->pan_card->getClientOriginalExtension();
            $image_name = Str::slug($request->seller_name).date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $image_name = 'uploads/custom-images/'.$image_name;
            Image::make($request->pan_card)
                ->save($image_name);
            $vendor->pan_card=$image_name;
        }
        
        $vendor->gstin = $request->gstin;

        $vendor->phone = $request->phone;
        $vendor->email = $request->email;

        $vendor->business_name = $request->business_name;
        $vendor->business_address = $request->business_address;
        
        $vendor->referral = $request->referral;
        
        $vendor->save();

        
        $notification=trans('admin_validation.Created Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('seller.login')->with($notification);

    }
}
