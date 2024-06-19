@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Vendor Sales')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Vendor Sales')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Vendor Sales')}}</div>
            </div>
          </div>


        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-info">
                  <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>{{__('admin.Today Earning')}}</h4>
                  </div>
                  <div class="card-body">
                  {{ $setting->currency_icon }}{{ $todayEarning }}
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-primary">
                  <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>{{__('admin.This Month Earning')}}</h4>
                  </div>
                  <div class="card-body">
                  {{ $setting->currency_icon }} {{ $thisMonthEarning }}
                  </div>
                </div>
              </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-success">
                  <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>{{__('admin.This Year Earning')}}</h4>
                  </div>
                  <div class="card-body">
                  {{ $setting->currency_icon }} {{ $thisYearEarning }}
                  </div>
                </div>
              </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-danger">
                  <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>{{__('admin.Total Earning')}}</h4>
                  </div>
                  <div class="card-body">
                  {{ $setting->currency_icon }}{{ $totalEarning }}
                  </div>
                </div>
              </div>
            </div>
        </div>

          <div class="section-body">
            <div class="row mt-4">
                <div class="col">
                  <div class="card">
                    <div class="card-body">
                      <div class="table-responsive table-invoice">
                        <table class="table table-striped" id="dataTable">
                            <thead>
                                <tr>
                                    <th width="5%">{{__('admin.SN')}}</th>
                                    <th width="15%">{{__('admin.Order Id')}}</th>
                                    <th width="30%">{{__('admin.Date')}}</th>
                                    <th width="15%">{{__('admin.Quantity')}}</th>
                                    <th width="15%">{{__('admin.Amount')}}</th>
                                    <th width="20%">{{__('admin.Payment')}}</th>
                                    <th width="20%">{{__('admin.Order Status')}}</th>
                                  </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $index => $order)
                                    <tr>
                                      <td>{{ ++$index }}</td>
                                      <td>{{ $order->order_id }}</td>
                                      <td>{{ $order->created_at->format('d F, Y') }}</td>
                                      <td>{{ $order->product_qty }}</td>
                                      <td>{{ $setting->currency_icon }}{{ $order->total_amount }}</td>
                                      
                                      <td>
                                            @if($order->payment_status == 1)
                                            <span class="badge badge-success">{{__('admin.success')}} </span>
                                            @else
                                            <span class="badge badge-danger">{{__('admin.Pending')}}</span>
                                            @endif
                                        </td>

                                        <td>
                                            @if ($order->order_status == 1)
                                            <span class="badge badge-warning">{{__('admin.Progress')}} </span>
                                            @elseif ($order->order_status == 2)
                                            <span class="badge badge-info">{{__('admin.Delivered')}} </span>
                                            @elseif ($order->order_status == 3)
                                            <span class="badge badge-success">{{__('admin.Completed')}} </span>
                                            @elseif ($order->order_status == 4)
                                            <span class="badge badge-secondary">{{__('admin.Declined')}} </span>
                                            @else
                                            <span class="badge badge-danger">{{__('admin.Pending')}}</span>
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
    function deleteData(id){
        $("#deleteForm").attr("action",'{{ url("admin/delete-product-report/") }}'+"/"+id)
    }
</script>
@endsection
