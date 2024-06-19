@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Seller')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Add New Seller')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Add New Seller')}}</div>
            </div>
          </div>

          <div class="section-body">
            <div class="row mt-4">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.seller.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="form-group col-12">
                                    <label>{{__('admin.Seller Name')}} <span class="text-danger">*</span></label>
                                    <input type="text" id="name" class="form-control"  name="name">
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Aadhar Card Number')}} <span class="text-danger">*</span></label>
                                    <input type="text" id="aadhar_number" class="form-control"  name="aadhar_number">
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Aadhar Card')}} <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control-file"  name="aadhar_card" onchange="previewAadharImage(event)">
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Aadhar Card Preview')}}</label>
                                    <div>
                                        <img id="aadhar-preview" class="admin-img" src="{{ asset('uploads/website-images/preview.png') }}" alt="">
                                    </div>
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Pan Card Number')}} <span class="text-danger">*</span></label>
                                    <input type="text" id="pan_number" class="form-control"  name="pan_number">
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Pan Card')}} <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control-file"  name="pan_card" onchange="previewPanImage(event)">
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Pan Card Preview')}}</label>
                                    <div>
                                        <img id="pan-preview" class="admin-img" src="{{ asset('uploads/website-images/preview.png') }}" alt="">
                                    </div>
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Phone Number')}} <span class="text-danger">*</span></label>
                                    <input type="text" id="phone" class="form-control"  name="phone" >
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.GSTIN Number')}} <span class="text-danger">*</span></label>
                                    <input type="text" id="gstin" class="form-control"  name="gstin">
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Business Name')}} <span class="text-danger">*</span></label>
                                    <input type="text" id="business_name" class="form-control"  name="business_name">
                                </div>
                               
                                <div class="form-group col-12">
                                    <label>{{__('admin.Business Address')}} <span class="text-danger">*</span></label>
                                    <input type="textarea" id="business_address" class="form-control"  name="business_address">
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Email Address')}} <span class="text-danger">*</span></label>
                                    <input type="text" id="email" class="form-control"  name="email">
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Password')}} <span class="text-danger">*</span></label>
                                    <input type="text" id="password" class="form-control"  name="password">
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Confirm Password')}} <span class="text-danger">*</span></label>
                                    <input id="password_confirmation" type="password" class="form-control" name="password_confirmation">
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Referral Code')}}</label>
                                    <input type="text" id="referral" class="form-control"  name="referral">
                                </div>

                               
                            <div class="row">
                                <div class="col-12">
                                    <button class="btn btn-primary">{{__('admin.Submit')}}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                  </div>
                </div>
          </div>
        </section>
      </div>

<script>
    
    function previewAadharImage(event) {
        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById('aadhar-preview');
            output.src = reader.result;
        }
        reader.readAsDataURL(event.target.files[0]);
    };

    function previewPanImage(event) {
        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById('pan-preview');
            output.src = reader.result;
        }
        reader.readAsDataURL(event.target.files[0]);
    };
</script>


@endsection
