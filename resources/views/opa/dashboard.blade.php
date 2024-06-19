@extends('opa.master_layout')
@section('title')
<title>{{__('admin.Dashboard')}}</title>
@endsection
@section('opa-content')
<!-- Main Content -->
<div class="main-content">
    <section class="section">
      <div class="section-header">
        <h1>{{__('admin.Dashbaord')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('opa.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.OPA Details')}}</div>
            </div>
          </div>

          <div class="section-body">
            <div class="row mt-5">
                <div class="col-md-3">
                  <div class="card card-statistic-1">
                    <div class="card-icon bg-primary">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div class="card-wrap">
                      <div class="card-header">
                        <h4>{{__('admin.Total Sellers')}}</h4>
                      </div>
                      <div class="card-body">
                        {{ $totalSellers }}
                      </div>
                    </div>
                  </div>
                </div>

                    <div class="col-md-3">
                        <a href="{{ route('admin.withdraw-list',$opa->id) }}">
                            <div class="card card-statistic-1">
                                <div class="card-icon bg-danger">
                                <i class="far fa-newspaper"></i>
                                </div>
                                <div class="card-wrap">
                                <div class="card-header">
                                    <h4>{{__('admin.Total Withdraw')}}</h4>
                                </div>
                                <div class="card-body">
                                    
                                </div>
                                </div>
                            </div>
                        </a>
                    </div>



                <div class="col-md-3">
                  <div class="card card-statistic-1">
                    <div class="card-icon bg-warning">
                      <i class="far fa-file"></i>
                    </div>
                    <div class="card-wrap">
                      <div class="card-header">
                        <h4>{{__('admin.Current Balance')}}</h4>
                      </div>
                      <div class="card-body">

                    </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('admin.product-by-seller',$user->id) }}">
                  <div class="card card-statistic-1">
                    <div class="card-icon bg-success">
                      <i class="fas fa-circle"></i>
                    </div>
                    <div class="card-wrap">
                      <div class="card-header">
                        <h4>{{__('admin.Total Products')}}</h4>
                      </div>
                      <div class="card-body">
                        
                      </div>
                    </div>
                  </div>
                </a>
                </div>
              </div>
            <div class="row mt-sm-4">
                <div class="col-12 col-md-12 col-lg-5">
                  <div class="card profile-widget">
                    <div class="profile-widget-header">
                        @if ($user->image)
                        <img alt="image" src="{{ asset($user->image) }}" class="rounded-circle profile-widget-picture">
                        @endif
                      <div class="profile-widget-items">
                        <div class="profile-widget-item">
                          <div class="profile-widget-item-label">{{__('admin.Joined at')}}</div>
                          <div class="profile-widget-item-value">{{ $user->created_at->format('d M Y') }}</div>
                        </div>
                        <div class="profile-widget-item">
                          <div class="profile-widget-item-label">{{__('admin.Balance')}}</div>
                          <div class="profile-widget-item-value">{{ $setting->currency_icon }}</div>
                        </div>
                      </div>
                    </div>
                    <div class="profile-widget-description">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <tr>
                                    <td>{{__('admin.Name')}}</td>
                                    <td>{{ $opa->name }}</td>
                                </tr>
                                <tr>
                                    <td>{{__('admin.Business Name')}}</td>
                                    <td>{{ $opa->business_name }}</td>
                                </tr>
                                <tr>
                                    <td>{{__('admin.Email')}}</td>
                                    <td>{{ $opa->email }}</td>
                                </tr>
                                <tr>
                                    <td>{{__('admin.Phone')}}</td>
                                    <td>{{ $opa->phone }}</td>
                                </tr>
                                <tr>
                                    <td>{{__('admin.Referral Code')}}</td>
                                    <td>{{ $opa->referral_code }}</td>
                                </tr>
                                <tr>
                                    <td>{{__('admin.Referral Link')}}</td>
                                    <td>{{ $opa->referral_code }}</td>
                                </tr>
                                <tr>
                                    <td>{{__('admin.User Status')}}</td>
                                    <td>
                                        @if($opa->status == 1)
                                            <span class="badge badge-info">{{__('admin.Active')}} </span>
                                        @else
                                            <span class="badge badge-dark">{{__('admin.Inactive')}} </span>
                                        @endif
                                    </td>
                                </tr>

                            </table>
                        </div>
                    </div>

                    <div class="card-footer text-center">
                      <div class="font-weight-bold mb-2">{{__('admin.Follow')}} {{ $user->name }}</div>
                      

                    </div>
                  </div>
              </div>

                <div class="col-12 col-md-12 col-lg-7">
                    <div class="card">
                        <form method="post" class="needs-validation" novalidate="" >
                            @method('put')
                            @csrf
                            <div class="card-header">
                                <h4>{{__('admin.Edit Profile')}}</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-6">
                                        <label>{{__('admin.Name')}} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" value="{{ $opa->name }}" name="name">
                                    </div>

                                    <div class="form-group col-6">
                                        <label>{{__('admin.Business Name')}} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" value="{{ $opa->business_name }}" name="business_name">
                                    </div>

                                    <div class="form-group col-6">
                                        <label>{{__('admin.Email')}} <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" value="{{ $opa->email }}" name="email" readonly>
                                    </div>

                                    <div class="form-group col-6">
                                        <label>{{__('admin.Phone')}} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" value="{{ $opa->phone }}" name="phone">
                                    </div>

                                </div>
                                <button class="btn btn-primary" type="submit">{{__('admin.Save Changes')}}</button>
                            </div>

                        </form>
                    </div>
                </div>
              </div>
          </div>
        </section>
      </div>

<script>
  

</script>
@endsection
