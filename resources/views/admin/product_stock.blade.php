@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Product Report')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Product Report')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item active"><a href="{{ route('admin.product.index') }}">{{__('admin.Product')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Product Report')}}</div>
            </div>
          </div>
    
          
          <div class="section-body">
          <a href="{{route('admin.all-products')}}" class="btn btn-white">{{__('admin.All Products')}}</a>
          <a href="{{route('admin.product-stock')}}" class="btn btn-info">{{__('admin.Product Stock')}}</a>
          <a href="{{route('admin.product-in-wishlist')}}" class="btn btn-white">{{__('admin.Wish Listed Products')}}</a>
    
          <div class="row mt-4">
                <div class="col"> 
                  <div class="card">
                    <div class="card-body">
                      <div class="table-responsive table-invoice">
                        <table class="table table-striped" id="dataTable"> 
                            <thead>
                                <tr>
                                    <th width="5%">{{__('admin.SN')}}</th>
                                    <th width="15%">{{__('admin.Photo')}}</th>
                                    <th width="30%">{{__('admin.Name')}}</th>
                                    <th width="10%">{{__('admin.Price')}}</th>
                                    <th width="10%">{{__('admin.Quantity')}}</th>
                                    <th width="20%">{{__('admin.Type')}}</th>
                                  </tr>
                            </thead>
                            <tbody>
                                @foreach ($product_stock as $index => $product)
                                    <tr>
                                        <td>{{ ++$index }}</td>
                                        <td> <img class="rounded-circle" src="{{ asset($product->thumb_image) }}" alt="" width="100px" height="100px"></td>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $setting->currency_icon }}{{ $product->price }}</td>
                                        <td>{{$product->qty}}</td>
                                        <td>
                                            @if ($product->new_product == 1)
                                            <span class="badge badge-primary p-1">{{__('admin.New')}}</span>
                                            @endif

                                            @if ($product->is_featured == 1)
                                            <span class="badge badge-success p-1">{{__('admin.Featured')}}</span>
                                            @endif

                                            @if ($product->is_top == 1)
                                            <span class="badge badge-warning p-1">{{__('admin.Top')}}</span>
                                            @endif

                                            @if ($product->is_best == 1)
                                            <span class="badge badge-danger p-1">{{__('admin.Best')}}</span>
                                            @endif
                                        </td>
                                    </tr>
                                  @endforeach
                            </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
          </div>
        </section>
      </div>

<script>
    
</script>
@endsection
