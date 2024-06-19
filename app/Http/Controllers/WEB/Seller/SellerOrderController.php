<?php

namespace App\Http\Controllers\WEB\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Setting;
use App\Models\OrderProduct;
use App\Models\OrderProductVariant;
use App\Models\OrderAddress;
use Auth;
class SellerOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web');
    }

    public function index(){
        $seller = Auth::guard('web')->user()->seller;
        $orders = Order::with('user')->whereHas('orderProducts',function($query) use ($seller){
            $query->where(['seller_id' => $seller->id]);
        })->orderBy('id','desc')->paginate(15);
        $title = trans('admin_validation.All Orders');
        $setting = Setting::first();
        return view('seller.order', compact('orders','title','setting'));
    }

    public function pendingOrder(){
        $seller = Auth::guard('web')->user()->seller;
        $orders = Order::with('user')->whereHas('orderProducts',function($query) use ($seller){
            $query->where(['seller_id' => $seller->id]);
        })->orderBy('id','desc')->where('order_status',0)->paginate(15);
        $title = trans('admin_validation.On Hold Orders');
        $setting = Setting::first();
        return view('seller.order', compact('orders','title','setting'));
    }

    public function progressOrder(){
        $seller = Auth::guard('web')->user()->seller;
        $orders = Order::with('user')->whereHas('orderProducts',function($query) use ($seller){
            $query->where(['seller_id' => $seller->id]);
        })->orderBy('id','desc')->where('order_status',1)->paginate(15);
        $title = trans('admin_validation.Confirm Orders');
        $setting = Setting::first();
        return view('seller.order', compact('orders','title','setting'));
    }

    public function dispatchReadyOrder(){
        $orders = Order::with('user')->orderBy('id','desc')->where('order_status',1)->get();
        $title = trans('admin_validation.Ready to Dispatch Orders');
        $setting = Setting::first();

        return view('seller.order', compact('orders','title','setting'));
    }

    public function deliveredOrder(){
        $seller = Auth::guard('web')->user()->seller;
        $orders = Order::with('user')->whereHas('orderProducts',function($query) use ($seller){
            $query->where(['seller_id' => $seller->id]);
        })->orderBy('id','desc')->where('order_status',2)->paginate(15);
        $title = trans('admin_validation.Delivered Orders');
        $setting = Setting::first();
        return view('seller.order', compact('orders','title','setting'));
    }

    public function completedOrder(){
        $seller = Auth::guard('web')->user()->seller;
        $orders = Order::with('user')->whereHas('orderProducts',function($query) use ($seller){
            $query->where(['seller_id' => $seller->id]);
        })->orderBy('id','desc')->where('order_status',3)->paginate(15);
        $title = trans('admin_validation.Completed Orders');
        $setting = Setting::first();
        return view('seller.order', compact('orders','title','setting'));
    }

    public function declinedOrder(){
        $seller = Auth::guard('web')->user()->seller;
        $orders = Order::with('user')->whereHas('orderProducts',function($query) use ($seller){
            $query->where(['seller_id' => $seller->id]);
        })->orderBy('id','desc')->where('order_status',4)->paginate(15);
        $title = trans('admin_validation.Cancelled Orders');
        $setting = Setting::first();
        return view('seller.order', compact('orders','title','setting'));
    }

    public function cashOnDelivery(){
        $seller = Auth::guard('web')->user()->seller;
        $orders = Order::with('user')->whereHas('orderProducts',function($query) use ($seller){
            $query->where(['seller_id' => $seller->id]);
        })->orderBy('id','desc')->where('cash_on_delivery',1)->paginate(15);

        $title = trans('admin_validation.Cash On Delivery');
        $setting = Setting::first();
        return view('seller.order', compact('orders','title','setting'));
    }

    public function show($id){
        $order = Order::with('user','orderProducts.orderProductVariants','orderAddress')->find($id);

        $setting = Setting::first();
        return view('seller.show_order',compact('order','setting'));

    }

    public function updateOrderStatus(Request $request){
        $orders = $request->input('orderIds');
        if($orders==NULL)
        {
            return redirect()->back();
        }
        $orders = trim($orders);
        $orderIds = explode(" ", $orders);
        foreach($orderIds as $id){
            $order = Order::where('order_id', $id)->first();
            if($order->order_status == 0){
                $order->order_status = 1;
                $order->save();
            }
            else if($order->order_status == 1){
                $order->order_status = 2;
                $order->order_approval_date = date('Y-m-d');
                $order->save();
            }
            else if($order->order_status == 2){
                $order->order_status = 3;
                $order->order_delivered_date = date('Y-m-d');
                $order->save();
            }
            else if($order->order_status == 3){
                $order->order_status = 4;
                $order->order_completed_date = date('Y-m-d');
                $order->save();
            }
            else if($order->order_status == 4){
                $order->order_status = 5;
                $order->order_declined_date = date('Y-m-d');
                $order->save();
            }
        }

        

        $notification = trans('admin_validation.Order Status Updated successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }
}
