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


class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function earningReport(){
        $orders = Order::all();

        $todayOrders = Order::with('user','orderProducts','orderAddress')->orderBy('id','desc')->whereDay('created_at', now()->day)->get();

        $todayTotalOrder = $todayOrders->count();
        $todayPendingOrder = $todayOrders->where('order_status',0)->count();
        $todayEarning = round($todayOrders->sum('total_amount'),2);
        $todayPendingEarning = round($todayOrders->where('payment_status',0)->sum('amount_real_currency'),2);
        $todayProductSale = $todayOrders->where('order_status',3)->sum('product_qty');

        $totalOrders = Order::with('user','orderProducts','orderAddress')->orderBy('id','desc')->get();
        $totalOrder = $totalOrders->count();
        $totalPendingOrder = $totalOrders->where('order_status',0)->count();
        $totalDeclinedOrder = $totalOrders->where('order_status',4)->count();
        $totalCompleteOrder = $totalOrders->where('order_status',3)->count();
        $totalEarning = round($totalOrders->sum('total_amount'),2);
        $totalProductSale = $totalOrders->where('order_status',3)->sum('product_qty');

        $monthlyOrders = Order::with('user','orderProducts','orderAddress')->orderBy('id','desc')->whereMonth('created_at', now()->month)->get();
        $thisMonthEarning = round($monthlyOrders->sum('total_amount'),2);
        $thisMonthProductSale = $monthlyOrders->where('order_status',3)->sum('product_qty');

        $yearlyOrders = Order::with('user','orderProducts','orderAddress')->orderBy('id','desc')->whereYear('created_at', now()->year)->get();
        $thisYearEarning = round($yearlyOrders->sum('total_amount'),2);
        $thisYearProductSale = $yearlyOrders->where('order_status',3)->sum('product_qty');

        $setting = Setting::first();
        $products = Product::all();
        $reviews = ProductReview::all();
        $reports = ProductReport::all();
        $users = User::all();
        $sellers = Vendor::all();
        $subscribers = Subscriber::where('is_verified',1)->get();
        $blogs = Blog::all();
        $categories = Category::get();
        $brands = Brand::get();

        return view('admin.earning_report')->with([
            'totalOrder' => $totalOrder,
            'todayEarning' => $todayEarning,
            'totalEarning' => $totalEarning,
            'thisMonthEarning' => $thisMonthEarning,
            'thisYearEarning' => $thisYearEarning,
            'orders' => $orders,
            'setting' => $setting
        ]);
    }

    public function vendorSales(){
        $orders = Order::all();

        $todayOrders = Order::with('user','orderProducts','orderAddress')->orderBy('id','desc')->whereDay('created_at', now()->day)->get();

        $todayTotalOrder = $todayOrders->count();
        $todayPendingOrder = $todayOrders->where('order_status',0)->count();
        $todayEarning = round($todayOrders->sum('total_amount'),2);
        $todayPendingEarning = round($todayOrders->where('payment_status',0)->sum('amount_real_currency'),2);
        $todayProductSale = $todayOrders->where('order_status',3)->sum('product_qty');

        $totalOrders = Order::with('user','orderProducts','orderAddress')->orderBy('id','desc')->get();
        $totalOrder = $totalOrders->count();
        $totalPendingOrder = $totalOrders->where('order_status',0)->count();
        $totalDeclinedOrder = $totalOrders->where('order_status',4)->count();
        $totalCompleteOrder = $totalOrders->where('order_status',3)->count();
        $totalEarning = round($totalOrders->sum('total_amount'),2);
        $totalProductSale = $totalOrders->where('order_status',3)->sum('product_qty');

        $monthlyOrders = Order::with('user','orderProducts','orderAddress')->orderBy('id','desc')->whereMonth('created_at', now()->month)->get();
        $thisMonthEarning = round($monthlyOrders->sum('total_amount'),2);
        $thisMonthProductSale = $monthlyOrders->where('order_status',3)->sum('product_qty');

        $yearlyOrders = Order::with('user','orderProducts','orderAddress')->orderBy('id','desc')->whereYear('created_at', now()->year)->get();
        $thisYearEarning = round($yearlyOrders->sum('total_amount'),2);
        $thisYearProductSale = $yearlyOrders->where('order_status',3)->sum('product_qty');

        $setting = Setting::first();
        $products = Product::all();
        $reviews = ProductReview::all();
        $reports = ProductReport::all();
        $users = User::all();
        $sellers = Vendor::all();
        $subscribers = Subscriber::where('is_verified',1)->get();
        $blogs = Blog::all();
        $categories = Category::get();
        $brands = Brand::get();

        return view('admin.vendor_sales')->with([
            'totalOrder' => $totalOrder,
            'todayEarning' => $todayEarning,
            'totalEarning' => $totalEarning,
            'thisMonthEarning' => $thisMonthEarning,
            'thisYearEarning' => $thisYearEarning,
            'orders' => $orders,
            'setting' => $setting
        ]);
    }

    public function transactionReport(){
        $orders = Order::all();

        $todayOrders = Order::with('user','orderProducts','orderAddress')->orderBy('id','desc')->whereDay('created_at', now()->day)->get();

        $todayTotalOrder = $todayOrders->count();
        $todayPendingOrder = $todayOrders->where('order_status',0)->count();
        $todayEarning = round($todayOrders->sum('total_amount'),2);
        $todayPendingEarning = round($todayOrders->where('payment_status',0)->sum('amount_real_currency'),2);
        $todayProductSale = $todayOrders->where('order_status',3)->sum('product_qty');

        $totalOrders = Order::with('user','orderProducts','orderAddress')->orderBy('id','desc')->get();
        $totalOrder = $totalOrders->count();
        $totalPendingOrder = $totalOrders->where('order_status',0)->count();
        $totalDeclinedOrder = $totalOrders->where('order_status',4)->count();
        $totalCompleteOrder = $totalOrders->where('order_status',3)->count();
        $totalEarning = round($totalOrders->sum('total_amount'),2);
        $totalProductSale = $totalOrders->where('order_status',3)->sum('product_qty');

        $monthlyOrders = Order::with('user','orderProducts','orderAddress')->orderBy('id','desc')->whereMonth('created_at', now()->month)->get();
        $thisMonthEarning = round($monthlyOrders->sum('total_amount'),2);
        $thisMonthProductSale = $monthlyOrders->where('order_status',3)->sum('product_qty');

        $yearlyOrders = Order::with('user','orderProducts','orderAddress')->orderBy('id','desc')->whereYear('created_at', now()->year)->get();
        $thisYearEarning = round($yearlyOrders->sum('total_amount'),2);
        $thisYearProductSale = $yearlyOrders->where('order_status',3)->sum('product_qty');

        $setting = Setting::first();
        $products = Product::all();
        $reviews = ProductReview::all();
        $reports = ProductReport::all();
        $users = User::all();
        $sellers = Vendor::all();
        $subscribers = Subscriber::where('is_verified',1)->get();
        $blogs = Blog::all();
        $categories = Category::get();
        $brands = Brand::get();

        return view('admin.transaction_report')->with([
            'totalOrder' => $totalOrder,
            'todayEarning' => $todayEarning,
            'totalEarning' => $totalEarning,
            'thisMonthEarning' => $thisMonthEarning,
            'thisYearEarning' => $thisYearEarning,
            'orders' => $orders,
            'setting' => $setting
        ]);
    }

    public function allProducts(){
        $reports = ProductReport::with('user','product','seller')->orderBy('id','desc')->get();

        return view('admin.all_product',compact('reports'));
    }

    public function productStock(){
        $product_stock = Product::all();
        $setting = Setting::first();
        return view('admin.product_stock',compact('product_stock', 'setting'));
    }

    public function whishlistProducts(){
        $wishlist_product = Wishlist::leftJoin('products', 'wishlists.product_id', '=', 'products.id')
    ->select('wishlists.product_id', 'products.*', DB::raw('COUNT(*) as count'))
    ->groupBy('wishlists.product_id')
    ->orderByDesc('count')
    ->get();

        $setting = Setting::first();
        return view('admin.whishlist_products',compact('wishlist_product', 'setting'));
    }

    public function orderReport(){
        $reports = ProductReport::with('user','product','seller')->orderBy('id','desc')->get();

        return view('admin.all_product',compact('reports'));
    }
}
