<?php

namespace App\Http\Controllers\WEB\OPA\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Auth;
use Hash;
use App\Models\User;
use App\Models\OPA;
use App\Models\Setting;
use App\Models\Vendor;

class OPALoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest:opa')->except('adminLogout');
    }

  

    public function loginPage(){
        $setting = Setting::first();
        return view('opa.login',compact('setting'));
    }


    public function dashboardLogin(Request $request){

        $rules = [
            'email'=>'required|email',
            'password'=>'required',
        ];

        $customMessages = [
            'email.required' => trans('admin_validation.Email is required'),
            'password.required' => trans('admin_validation.Password is required'),
        ];
        $this->validate($request, $rules, $customMessages);

        $credential=[
            'email'=> $request->email,
            'password'=> $request->password
        ];

        $user = User::where('email',$request->email)->first();
        if($user){
            if($user->is_opa==1){
                $opa = OPA::where('user_id', $user->id)->first();
                if(!$opa){
                    $notification= trans('admin_validation.Invalid Email');
                    $notification=array('messege'=>$notification,'alert-type'=>'error');
                    return redirect()->route('opa.login')->with($notification);
                }
                if($opa->status == 1){
                    if(Hash::check($request->password,$user->password)){
                        if(Auth::guard('opa')->attempt($credential,$request->remember)){
                            $notification= trans('admin_validation.Login Successfully');
                            $notification=array('messege'=>$notification,'alert-type'=>'success');
                            return redirect()->route('opa.dashboard')->with($notification);
                        }
                    }else{
                        $notification= trans('admin_validation.Invalid Password');
                        $notification=array('messege'=>$notification,'alert-type'=>'error');
                        return redirect()->route('opa.login')->with($notification);
                    }
                }else{
                    $notification= trans('admin_validation.Inactive account');
                    $notification=array('messege'=>$notification,'alert-type'=>'error');
                    return redirect()->route('opa.login')->with($notification);
                }

            }else{
                $notification= trans('admin_validation.Inactive account');
                $notification=array('messege'=>$notification,'alert-type'=>'error');
                return redirect()->route('opa.login')->with($notification);
            }
        }else{
            $notification= trans('admin_validation.Invalid Email');
            $notification=array('messege'=>$notification,'alert-type'=>'error');
            return redirect()->route('opa.login')->with($notification);
        }

    }


    public function adminLogout(){
        Auth::guard('opa')->logout();
        $notification= trans('admin_validation.Logout Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('opa.login')->with($notification);
    }


    protected function respondWithToken($token, $admin)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'admin' => $admin
        ]);
    }
}
