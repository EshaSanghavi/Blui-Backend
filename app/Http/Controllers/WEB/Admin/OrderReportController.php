<?php

namespace App\Http\Controllers\WEB\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Setting;
use App\Models\Product;
use App\Models\ProductReport;
use App\Models\ProductReview;
use App\Models\Vendor;
use App\Models\Subscriber;
use App\Models\User;
use App\Models\Blog;
use App\Models\Wishlist;
use App\Models\Category;
use App\Models\Brand;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OrderReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(){
        $orders = Order::all();
    
        $totalOrders = Order::with('user','orderProducts','orderAddress')->orderBy('id','desc')->get();
        $totalOrder = $totalOrders->count();
        $totalPendingOrder = $totalOrders->where('order_status',0)->count();
        $totalDeclinedOrder = $totalOrders->where('order_status',4)->count();
        $totalCompleteOrder = $totalOrders->where('order_status',3)->count();
        $setting = Setting::first();
        return view('admin.order_report')->with([
            'totalOrder' => $totalOrder,
            'totalPendingOrder' => $totalPendingOrder,
            'totalDeclinedOrder' => $totalDeclinedOrder,
            'totalCompleteOrder' => $totalCompleteOrder,
            'orders' => $orders,
            'setting' => $setting
        ]);
    }

    public function show($id){
        $report = ProductReport::with('user','product','seller')->find($id);
        $product = $report->product;
        $totalReport = ProductReport::where('product_id',$product->id)->count();
        return view('admin.show_product_report',compact('report','totalReport'));
    }

    public function destroy($id){
        $report = ProductReport::find($id);
        $report->delete();
        $notification=trans('admin_validation.Delete Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('admin.product-report')->with($notification);
    }

    public function deactiveProduct($id){
        $report = ProductReport::find($id);
        $product = $report->product;
        $product->status = 0;
        $product->save();
        $notification=trans('admin_validation.Deactive Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }
}
