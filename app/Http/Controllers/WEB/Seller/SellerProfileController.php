<?php

namespace App\Http\Controllers\WEB\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Country;
use App\Models\CountryState;
use App\Models\City;
use App\Models\Vendor;
use App\Models\VendorSocialLink;
use App\Models\SellerWithdraw;
use App\Models\SellerMailLog;
use App\Models\OrderProduct;
use App\Models\Setting;
use App\Models\BannerImage;
use App\Models\VendorBankDetail;
use App\Models\Warehouse;
use App\Services\IthinkLogisticsService;
use Auth;
use Image;
use File;
use Str;
use Hash;
class SellerProfileController extends Controller
{
    private $ithinkLogisticsService;

    public function __construct(IthinkLogisticsService $ithinkLogisticsService)
    {
        $this->middleware('auth:web');
        $this->ithink_logistics = $ithinkLogisticsService;
    }

    public function initialDetails()
    {
        $user = Auth::guard('web')->user();
        $setting = Setting::first();

        $vendor = Vendor::where('user_id', $user->id)->first();

        $jsonData = $this->ithink_logistics->getState()->content();
        $data = json_decode($jsonData, true);
        $state = json_decode($data, true);
        $states = $state['data'];

        return view('seller.initial_details', compact('vendor', 'states', 'setting'));
    }

    public function initialDetailsStore(Request $request)
    {
        $rules = [
            'company_name'=>'required',
            'address1'=>'required',
            'mobile'=>'required',
            'city'=>'required',
            'state'=>'required',
            'pincode'=>'required',
            'account_holder_name'=>'required',
            'account_number'=>'required',
            'ifsc_code'=>'required',
        ];
        $customMessages = [
            'company_name.required' => trans('admin_validation.Company name is required'),
            'address1.required' => trans('admin_validation.Company address is required'),
            'mobile.required' => trans('admin_validation.Phone is required'),
            'city.required' => trans('admin_validation.City is required'),
            'state.required' => trans('admin_validation.State is required'),
            'pincode.required' => trans('admin_validation.Pin code is required'),
            'account_holder_name.required' => trans('admin_validation.Account holder name is required'),
            'account_number.required' => trans('admin_validation.Account number is required'),
            'ifsc_code.required' => trans('admin_validation.IFSC code is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $user = Auth::guard('web')->user();
        $setting = Setting::first();
        $vendor = Vendor::where('user_id', $user->id)->first();

        $data = [
            "company_name" => $request->company_name,
            "mobile" => $request->mobile,
            "address1" => $request->address1,
            "address2" => $request->address2,
            "state" => explode("_", $request->state)[1],
            "state_id" => explode("_", $request->state)[0],
            "city" => explode("_", $request->city)[1],
            "city_id" => explode("_", $request->city)[0],
            "pincode" => $request->pincode,
            "country" => "India",
            "country_id" => 101,
            "gps" => $request->gps,
        ];

        $jsonData = $this->ithink_logistics->addWarehouse($data)->content();
        $response = json_decode(json_decode($jsonData, true));

        if($response->status == "success"){
            $seller_warehouse = new Warehouse();
            $seller_warehouse->ithink_logistics_warehouse_id = $response->warehouse_id;
            $seller_warehouse->seller_id = $vendor->id;
            $seller_warehouse->address1 = $data['address1'];
            $seller_warehouse->address2 = $data['address2'];
            $seller_warehouse->state_id = $data['state_id'];
            $seller_warehouse->state = $data['state'];
            $seller_warehouse->city_id = $data['city_id'];
            $seller_warehouse->city = $data['city'];
            $seller_warehouse->country = $data['country'];
            $seller_warehouse->pincode = $data['pincode'];
            $seller_warehouse->country_id = $data['country_id'];
            $seller_warehouse->gps = $data['gps'];
            $seller_warehouse->save();
        }

        $seller_bank = new VendorBankDetail();
        $seller_bank->seller_id = $vendor->id;
        $seller_bank->account_holder_name = $request->account_holder_name;
        $seller_bank->account_number = $request->account_number;
        $seller_bank->ifsc_code = $request->ifsc_code;
        $seller_bank->save();

        if($seller_bank->save() && $seller_warehouse->save()) 
        {
            $vendor->details_filled = 1; 
            $vendor->save();
        }

        $notification=trans('admin_validation.Details Successfully Stored');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('seller.dashboard')->with($notification);
    }

    public function getCityByState($id){
        $state_id = explode("_", $id)[0];
        $jsonData = $this->ithink_logistics->getCity($state_id)->content();
        $data = json_decode($jsonData, true);
        $city = json_decode($data, true);
        $cities = $city['data'];
        $response='<option value="">'.trans('admin.Select City').'</option>';
        foreach($cities as $city){
            $response .= "<option value=".$city['id']."_".$city['city_name'].">".$city['city_name']."</option>";
        }
        return response()->json(['cities'=>$response]);
    }

    public function index(){
        $user = Auth::guard('web')->user();

        $seller = Vendor::with('user','socialLinks','products')->where('user_id', $user->id)->first();
        $countries = Country::orderBy('name','asc')->where('status',1)->get();
        $states = CountryState::orderBy('name','asc')->where(['status' => 1, 'country_id' => $user->country_id])->get();
        $cities = City::orderBy('name','asc')->where(['status' => 1, 'country_state_id' => $user->state_id])->get();
        $totalWithdraw = SellerWithdraw::where('seller_id',$seller->id)->where('status',1)->sum('total_amount');
        $totalPendingWithdraw = SellerWithdraw::where('seller_id',$seller->id)->where('status',0)->sum('withdraw_amount');

        $totalAmount = 0;
        $totalSoldProduct = 0;
        $orderProducts = OrderProduct::with('order')->where('seller_id', $seller->id)->get();
        foreach($orderProducts as $orderProduct){
            if($orderProduct->order->payment_status == 1 && $orderProduct->order->order_status == 3){
                $price = ($orderProduct->unit_price * $orderProduct->qty) + $orderProduct->vat;
                $totalAmount = $totalAmount + $price;
                $totalSoldProduct = $totalSoldProduct + $orderProduct->qty;
            }
        }

        $defaultProfile = BannerImage::whereId('15')->first();
        $setting = Setting::first();

        return view('seller.seller_profile', compact('user','countries','states','cities','seller','totalWithdraw','totalAmount','totalPendingWithdraw','totalSoldProduct','setting','defaultProfile'));
    }

    public function changePassword(){
        $user = Auth::guard('web')->user();
        $setting = Setting::first();
        return view('seller.change_password', compact('user','setting'));
    }

    public function stateByCountry($id){
        $states = CountryState::where(['status' => 1, 'country_id' => $id])->get();
        return response()->json(['states'=>$states]);
    }

    public function cityByState($id){
        $cities = City::where(['status' => 1, 'country_state_id' => $id])->get();
        return response()->json(['cities'=>$cities]);
    }

    public function updateSellerProfile(Request $request){
        $user = Auth::guard('web')->user();
        $rules = [
            'name'=>'required',
            'email'=>'required|unique:users,email,'.$user->id,
            'phone'=>'required',
            'address'=>'required',
        ];
        $customMessages = [
            'name.required' => trans('admin_validation.Name is required'),
            'email.required' => trans('admin_validation.Email is required'),
            'email.unique' => trans('admin_validation.Email already exist'),
            'phone.required' => trans('admin_validation.Phone is required'),
            'country.required' => trans('admin_validation.Country is required'),
            'zip_code.required' => trans('admin_validation.Zip code is required'),
            'address.required' => trans('admin_validation.Address is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->save();

        if($request->file('image')){
            $old_image=$user->image;
            $user_image=$request->image;
            $extention=$user_image->getClientOriginalExtension();
            $image_name= Str::slug($request->name).date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $image_name='uploads/custom-images/'.$image_name;

            Image::make($user_image)
                ->save($image_name);

            $user->image=$image_name;
            $user->save();
            if($old_image){
                if(File::exists($old_image))unlink($old_image);
            }
        }

        $notification= trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function updatePassword(Request $request){
        $user = Auth::guard('web')->user();
        $rules = [
            'password'=>'required|min:4|confirmed',
        ];

        $customMessages = [
            'password.required' => trans('admin_validation.Password is required'),
            'password.min' => trans('admin_validation.Password must be 4 characters'),
            'password.confirmed' => trans('admin_validation.Confirm password does not match'),
        ];
        $this->validate($request, $rules,$customMessages);

        $user->password = Hash::make($request->password);
        $user->save();
        $notification= trans('admin_validation.Password Change Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function myShop(){
        $user = Auth::guard('web')->user();
        $seller = Vendor::with('socialLinks')->where('user_id',$user->id)->first();

        return view('seller.shop_profile', compact('user','seller'));
    }

    public function updateSellerSop(Request $request){

        $user = Auth::guard('web')->user();
        $seller = Vendor::where('user_id',$user->id)->first();
        $rules = [
            'shop_name'=>'required|unique:vendors,email,'.$seller->id,
            'email'=>'required|unique:vendors,email,'.$seller->id,
            'phone'=>'required',
            'opens_at'=>'required',
            'closed_at'=>'required',
            'address'=>'required',
            'greeting_msg'=>'required',
        ];
        $customMessages = [
            'shop_name.required' => trans('admin_validation.Shop name is required'),
            'shop_name.unique' => trans('admin_validation.Shop anme is required'),
            'email.required' => trans('admin_validation.Email is required'),
            'email.unique' => trans('admin_validation.Email already exist'),
            'phone.required' => trans('admin_validation.Phone is required'),
            'greeting_msg.required' => trans('admin_validation.Greeting Messsage is required'),
            'opens_at.required' => trans('admin_validation.Opens at is required'),
            'closed_at.required' => trans('admin_validation.Close at is required'),
            'address.required' => trans('admin_validation.Address is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $seller->phone = $request->phone;
        $seller->open_at = $request->opens_at;
        $seller->closed_at = $request->closed_at;
        $seller->address = $request->address;
        $seller->greeting_msg = $request->greeting_msg;
        $seller->seo_title = $request->seo_title ? $request->seo_title : $request->shop_name;
        $seller->seo_description = $request->seo_description ? $request->seo_description : $request->shop_name;
        $seller->save();

        if($request->logo){
            $exist_banner = $seller->logo;
            $extention = $request->logo->getClientOriginalExtension();
            $banner_name = 'seller-banner'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $banner_name = 'uploads/custom-images/'.$banner_name;
            Image::make($request->logo)
                ->save($banner_name);
            $seller->logo = $banner_name;
            $seller->save();
            if($exist_banner){
                if(File::exists($exist_banner))unlink($exist_banner);
            }
        }


        if($request->banner_image){
            $exist_banner = $seller->banner_image;
            $extention = $request->banner_image->getClientOriginalExtension();
            $banner_name = 'seller-banner'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $banner_name = 'uploads/custom-images/'.$banner_name;
            Image::make($request->banner_image)
                ->save($banner_name);
            $seller->banner_image = $banner_name;
            $seller->save();
            if($exist_banner){
                if(File::exists($exist_banner))unlink($exist_banner);
            }
        }

        if(count($request->links) > 0){
            $socialLinks = $seller->socialLinks;
            foreach($socialLinks as $link){
                $link->delete();
            }
            foreach($request->links as $index=> $link){
                if($request->links[$index] != null && $request->icons[$index] != null){
                    $socialLink = new VendorSocialLink();
                    $socialLink->vendor_id = $seller->id;
                    $socialLink->icon=$request->icons[$index];
                    $socialLink->link=$request->links[$index];
                    $socialLink->save();
                }
            }
        }

        $notification= trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function removeSellerSocialLink($id){
        $socialLink = VendorSocialLink::find($id);
        $socialLink->delete();
        return response()->json(['success' => trans('admin_validation.Delete Successfully')]);
    }

    public function emailHistory(){
        $user = Auth::guard('web')->user();
        $seller = $user->seller;
        $emails = SellerMailLog::where('seller_id',$seller->id)->orderBy('id','desc')->get();

        return response()->json(['emails' => $emails, 'user' => $user]);

    }
}
