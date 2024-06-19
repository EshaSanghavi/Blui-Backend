<?php

namespace App\Http\Controllers\WEB\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\CountryState;
use App\Models\City;
use App\Models\User;
use App\Models\OPA;
use App\Models\Vendor;
use App\Models\ProductReview;
use App\Models\Product;
use App\Helpers\MailHelper;
use App\Models\opaMailLog;
use App\Mail\SendSingleopaMail;
use App\Mail\ApprovedopaAccount;
use App\Models\BannerImage;
use App\Models\Setting;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use App\Models\EmailTemplate;
use Auth;
use Image;
use File;
use Mail;
use Str;
class OPAController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(){
        $opas = OPA::with('user')->orderBy('id','desc')->where('status',1)->get();
        $defaultProfile = BannerImage::whereId('15')->first();
        $sellers = Vendor::all();
        $setting = Setting::first();

        return view('admin.opa', compact('opas','defaultProfile','sellers','setting'));

    }

    public function create(){

        return view('admin.create_opa');
    
    }

    public function store(Request $request){
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
        return redirect()->back()->with($notification);
    }

    public function pendingOPAList(){
        $opas = OPA::with('user')->orderBy('id','desc')->where('status',0)->get();
        $defaultProfile = BannerImage::whereId('15')->first();
        $sellers = Vendor::all();
        $setting = Setting::first();

        return view('admin.opa', compact('opas','defaultProfile','sellers','setting'));
    
    }

    public function show($id){
        $opa = OPA::with('user_id')->find($id);
        if($opa){
            $countries = Country::with('countryStates')->orderBy('name','asc')->where('status',1)->get();
            $states = CountryState::with('cities','country')->orderBy('name','asc')->where(['status' => 1, 'country_id' => $opa->user->country_id])->get();
            $cities = City::with('countryState')->orderBy('name','asc')->where(['status' => 1, 'country_state_id' => $opa->user->state_id])->get();
            $user = $opa->user;
            $totalWithdraw = opaWithdraw::with('opa')->where('opa_id',$opa->id)->where('status',1)->sum('total_amount');
            $totalPendingWithdraw = opaWithdraw::with('opa')->where('opa_id',$opa->id)->where('status',0)->sum('withdraw_amount');

            $totalAmount = 0;
            $totalSoldProduct = 0;
            $orderProducts = OrderProduct::with('order')->where('opa_id',$id)->get();
            foreach($orderProducts as $orderProduct){
                if($orderProduct->order->payment_status == 1 && $orderProduct->order->order_status == 3){
                    $price = ($orderProduct->unit_price * $orderProduct->qty) + $orderProduct->vat;
                    $totalAmount = $totalAmount + $price;
                    $totalSoldProduct = $totalSoldProduct + $orderProduct->qty;
                }
            }

            $defaultProfile = BannerImage::whereId('15')->select('image','title')->first();
            $setting = Setting::select('currency_icon')->first();

            return view('admin.show_opa',compact('opa','countries','cities','states','user','totalWithdraw','totalAmount','totalSoldProduct','totalPendingWithdraw','defaultProfile','setting'));

        }else{
            $notification = trans('admin_validation.Something went wrong');
            return response()->json(['notification' => $notification], 500);
        }

    }

    public function stateByCountry($id){
        $states = CountryState::where(['status' => 1, 'country_id' => $id])->get();
        $response='<option value="">'.trans('admin_validation.Select a State').'</option>';
        if($states->count() > 0){
            foreach($states as $state){
                $response .= "<option value=".$state->id.">".$state->name."</option>";
            }
        }
        return response()->json(['states'=>$response]);
    }

    public function cityByState($id){
        $cities = City::where(['status' => 1, 'country_state_id' => $id])->get();
        $response='<option value="">'.trans('admin_validation.Select a City').'</option>';
        if($cities->count() > 0){
            foreach($cities as $city){
                $response .= "<option value=".$city->id.">".$city->name."</option>";
            }
        }
        return response()->json(['cities'=>$response]);
    }

    public function updateOPA(Request $request , $id){
        $user = User::find($id);
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
        ];
        $this->validate($request, $rules,$customMessages);

        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->save();

        $notification=trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }


    public function opaShopDetail($id){
        $opa = OPA::with('user','socialLinks')->where('id', $id)->first();
        $user = $opa->user;
        $setting = Setting::first();
        return view('admin.opa_shop', compact('opa','user','setting'));
    }

    public function removeOPASocialLink($id){
        $socialLink = opaSocialLink::find($id);
        $socialLink->delete();
        return response()->json(['success' => 'Delete Successfully']);
    }

    public function destroy($id)
    {
        $opa = OPA::find($id);
        $banner_image = $opa->banner_image;
        $opa->delete();
        if($banner_image){
            if(File::exists($banner_image))unlink($banner_image);
        }

        opaMailLog::where('opa_id',$id)->delete();
        opaWithdraw::where('opa_id',$id)->delete();
        opaSocialLink::where('opa_id',$id)->delete();

        $notification = trans('admin_validation.Delete Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('admin.opa-list')->with($notification);
    }

    public function changeStatus($id){
        $opa = OPA::find($id);
        if($opa->status == 1){
            $opa->status = 0;
            $opa->save();
            $message = trans('admin_validation.Inactive Successfully');
        }else{
            $opa->status = 1;
            $opa->save();

            $user = User::find($opa->user_id);
            MailHelper::setMailConfig();
            $template = EmailTemplate::where('id',7)->first();
            $subject = $template->subject;
            $message = $template->description;
            $message = str_replace('{{name}}',$user->name,$message);
            Mail::to($user->email)->send(new ApprovedOPAAccount($message,$subject));

            $message = trans('admin_validation.Active Successfully');
        }
        return response()->json($message);
    }

    public function updateOPAShop(Request $request, $id){
        $opa = OPA::find($id);
        $rules = [
            'shop_name'=>'required|unique:opas,email,'.$opa->id,
            'email'=>'required|unique:opas,email,'.$opa->id,
            'phone'=>'required',
            'greeting_msg'=>'required',
            'opens_at'=>'required',
            'closed_at'=>'required',
            'address'=>'required',
        ];
        $customMessages = [
            'shop_name.required' => trans('admin_validation.Shop name is required'),
            'shop_name.unique' => trans('admin_validation.Shop anme is required'),
            'email.required' => trans('admin_validation.Email is required'),
            'email.unique' => trans('admin_validation.Email already exist'),
            'phone.required' => trans('admin_validation.Phone is required'),
            'greeting_msg.required' => trans('admin_validation.Greeting Message is required'),
            'opens_at.required' => trans('admin_validation.Opens at is required'),
            'closed_at.required' => trans('admin_validation.Close at is required'),
            'address.required' => trans('admin_validation.Address is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $opa->phone = $request->phone;
        $opa->open_at = $request->opens_at;
        $opa->closed_at = $request->closed_at;
        $opa->address = $request->address;
        $opa->greeting_msg = $request->greeting_msg;
        $opa->seo_title = $request->seo_title ? $request->seo_title : $request->shop_name;
        $opa->seo_description = $request->seo_description ? $request->seo_description : $request->shop_name;
        $opa->save();

        if($request->logo){
            $exist_banner = $opa->logo;
            $extention = $request->logo->getClientOriginalExtension();
            $banner_name = 'opa-banner'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $banner_name = 'uploads/custom-images/'.$banner_name;
            Image::make($request->logo)
                ->save($banner_name);
            $opa->logo = $banner_name;
            $opa->save();
            if($exist_banner){
                if(File::exists($exist_banner))unlink($exist_banner);
            }
        }

        if($request->banner_image){
            $exist_banner = $opa->banner_image;
            $extention = $request->banner_image->getClientOriginalExtension();
            $banner_name = 'opa-banner'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $banner_name = 'uploads/custom-images/'.$banner_name;
            Image::make($request->banner_image)
                ->save($banner_name);
            $opa->banner_image = $banner_name;
            $opa->save();
            if($exist_banner){
                if(File::exists($exist_banner))unlink($exist_banner);
            }
        }


        if($request->links){
            if(count($request->links) > 0){
                $socialLinks = $opa->socialLinks;
                foreach($socialLinks as $link){
                    $link->delete();
                }
                foreach($request->links as $index=> $link){
                    if($request->links[$index] != null && $request->icons[$index] != null){
                        $socialLink = new opaSocialLink();
                        $socialLink->opa_id = $opa->id;
                        $socialLink->icon=$request->icons[$index];
                        $socialLink->link=$request->links[$index];
                        $socialLink->save();
                    }
                }
            }
        }


        $notification = trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }


    public function opaReview($id){

        $opa = OPA::where('id', $id)->first();
        $user = $opa->user;
        $reviews = ProductReview::with('user','product')->orderBy('id','desc')->where('product_opa_id',$opa->id)->get();


        return view('admin.opa_product_review', compact('reviews','user','opa'));
    }

    public function sendEmailToOPA($id){
        $opa = OPA::find($id);
        $user = User::find($opa->user_id);
        $setting = Setting::first();

        return view('admin.send_opa_email', compact('user','setting','opa'));
    }

    public function showOPAReviewDetails($id){
        $review = ProductReview::with('user','product')->find($id);
        $opa = OPA::where('id', $review->product_opa_id)->first();


        return view('admin.show_opa_product_review', compact('review','opa'));
    }

    public function sendMailtoSingleOPA(Request $request, $id){
        $rules = [
            'subject'=>'required',
            'message'=>'required'
        ];
        $customMessages = [
            'subject.required' => trans('admin_validation.Subject is required'),
            'message.required' => trans('admin_validation.Message is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $user = User::with('opa')->find($id);
        $opa = $user->opa;
        MailHelper::setMailConfig();
        Mail::to($user->email)->send(new SendSingleopaMail($request->subject,$request->message));
        $opaMail = new opaMailLog();
        $opaMail->opa_id = $opa->id;
        $opaMail->subject = $request->subject;
        $opaMail->message = $request->message;
        $opaMail->save();
        $notification = trans('admin_validation.Email Send Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }


    public function emailHistory($id){
        $opa = OPA::where('id', $id)->first();
        $user = $opa->user;
        $emails = opaMailLog::where('opa_id',$opa->id)->orderBy('id','desc')->get();

        return view('admin.email_history', compact('emails','user','opa'));
    }

    public function productBySeller($id){
        $user = User::find($id);
        $opa = OPA::where('user_id', $user->id)->first();
        $products = Product::with('category','brand')->where('opa_id',$opa->id)->get();
        $setting = Setting::select('currency_icon')->first();

        return view('admin.product_by_opa', compact('products','user','opa','setting'));
    }

    public function sendEmailToAllOPA(){
        $setting = Setting::first();
        return view('admin.send_email_to_all_opa',compact('setting'));
    }


    public function sendMailToAllopa(Request $request){
        $rules = [
            'subject'=>'required',
            'message'=>'required'
        ];
        $customMessages = [
            'subject.required' => trans('admin_validation.Subject is required'),
            'message.required' => trans('admin_validation.Message is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $opas = OPA::with('user')->where('status',1)->get();
        MailHelper::setMailConfig();
        foreach($opas as $opa){
            Mail::to($opa->user->email)->send(new SendSingleopaMail($request->subject,$request->message));
            $opaMail = new opaMailLog();
            $opaMail->opa_id = $opa->id;
            $opaMail->subject = $request->subject;
            $opaMail->message = $request->message;
            $opaMail->save();
        }

        $notification = trans('admin_validation.Email Send Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function opaWithdrawList($id){
        $opa = OPA::find($id);
        $user = $opa->user;
        $withdraws = opaWithdraw::where('opa_id',$id)->get();
        $setting = Setting::select('currency_icon')->first();

        return view('admin.opa_withdraw_list', compact('withdraws','user','setting'));
    }

}
