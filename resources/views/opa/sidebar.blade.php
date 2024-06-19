@php
    $setting = App\Models\Setting::first();
@endphp

<div class="main-sidebar">
    <aside id="sidebar-wrapper">
      <div class="sidebar-brand">
        <a href="{{ route('opa.dashboard') }}">{{ $setting->sidebar_lg_header }}</a>
      </div>
      <div class="sidebar-brand sidebar-brand-sm">
        <a href="{{ route('opa.dashboard') }}">{{ $setting->sidebar_sm_header }}</a>
      </div>
      <ul class="sidebar-menu">
          <li class="{{ Route::is('opa.dashboard') ? 'active' : '' }}"><a class="nav-link" href="{{ route('opa.dashboard') }}"><i class="fas fa-home"></i> <span>{{__('admin.Profile')}}</span></a></li>


          <li class="{{ Route::is('opa.seller-create')  ? 'active' : '' }}"><a class="nav-link" href="{{ route('opa.seller-create') }}">{{__('admin.Add New Seller')}}</a></li>
    
          <li class="{{ Route::is('opa.seller-list') || Route::is('opa.seller-show') || Route::is('opa.seller-shop-detail') || Route::is('opa.seller-reviews') || Route::is('opa.show-seller-review-details') || Route::is('opa.send-email-to-seller') || Route::is('opa.email-history') || Route::is('opa.product-by-seller') || Route::is('opa.send-email-to-all-seller') ? 'active' : '' }}"><a class="nav-link" href="{{ route('opa.seller-list') }}">{{__('admin.Seller List')}}</a></li>

          <li class="{{ Route::is('opa.pending-seller-list') ? 'active' : '' }}"><a class="nav-link" href="{{ route('opa.pending-seller-list') }}">{{__('admin.Pending Sellers')}}</a></li>       


        </ul>

    </aside>
  </div>
