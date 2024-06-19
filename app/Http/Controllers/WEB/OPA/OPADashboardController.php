<?php

namespace App\Http\Controllers\WEB\OPA;

use Auth;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\OPA;
use App\Models\Vendor;
use App\Http\Controllers\Controller;

class OPADashboardController extends Controller
{
    public function index(){
        $user = Auth::guard('opa')->user();
        
        $opa = OPA::where('email', '=', $user->email)->first();
        
        $totalSellers = Vendor::where('referral', '=', $opa->referral_code)->count();
        $setting = Setting::first();

        return view('opa.dashboard', compact('user', 'opa','totalSellers','setting'));
    
    }
}
