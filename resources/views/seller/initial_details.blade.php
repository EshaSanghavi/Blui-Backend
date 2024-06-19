@php
use App\Models\AttributeValue;
@endphp
@extends('seller.master_layout')
@section('title')
<title>{{__('seller.Products')}}</title>
@endsection
@section('seller-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Initial Details')}}</h1>
          </div>

          <div class="section-body">
            <div class="row mt-4">
                <div class="col-12">
                <div class="card">
                        <div class="card-body">
                            <form action="{{ route('seller.initial-details-store') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <fieldset class="border p-3" style="border-radius: 4px;"> 
                                    <legend class="w-auto">{{__('admin.Pickup Warehouse Address')}}</legend>

                                        <div class="row ml-0">
                                            <div class="form-group col-6">
                                                <label>{{__('admin.Business Name')}} <span class="text-danger">*</span></label>
                                                <input type="text" id="name" class="form-control"  name="company_name" value="{{ $vendor->business_name }}" style="width: 90%;">
                                            </div>

                                            <div class="form-group col-6">
                                                <label>{{__('admin.Phone')}} <span class="text-danger">*</span></label>
                                                <input type="text" id="mobile" class="form-control"  name="mobile" value="{{ $vendor->phone }}" style="width: 90%;">
                                            </div>
                                        </div>

                                        <div class="row ml-0">
                                            <div class="form-group col-6">
                                                <label>{{__('admin.Address Line 1')}} <span class="text-danger">*</span></label>
                                                <input type="textarea" class="form-control" id="address1" name="address1" placeholder="Company's address apt/wing/building." style="width: 90%;">
                                            </div>

                                            <div class="form-group col-6">
                                                <label>{{__('admin.Address Line 2')}}</label>
                                                <input type="textarea" class="form-control" id="address2" name="address2" placeholder="Company's address landmark." style="width: 90%;">
                                            </div>
                                        </div>

                                        <div class="row ml-0">
                                            <div class="form-group col-6">
                                                <label>{{__('admin.State')}} <span class="text-danger">*</span></label>
                                                <select class="form-control" name="state" id="state" style="width: 90%;">
                                                <option value="">{{__('admin.Select State')}}</option>
                                                @php
                                                    foreach ($states as $state) {
                                                        echo '<option value="'.$state['id']."_".$state['state_name'].'">' . $state['state_name'] . '</option>';
                                                    }
                                                @endphp
                                                </select>
                                            </div>

                                            <div class="form-group col-6">
                                                <label>{{__('admin.City')}} <span class="text-danger">*</span></label>
                                                <select class="form-control" name="city" id="city" style="width: 90%;">
                                                    <option value="">{{__('admin.Select City')}}</option>
                                                </select>                                            
                                            </div>
                                        </div>
                                        
                                        <div class="row ml-0">
                                            <div class="form-group col-6">
                                                <label>{{__('admin.Pincode')}} <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="pincode" name="pincode" style="width: 90%;">
                                            </div>

                                            <div class="form-group col-6">
                                                <label>{{__('admin.Country')}} <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="country" name="country" value="India" placeholder="India" style="width: 90%;" disabled>
                                            </div>
                                        </div>

                                </fieldset>

                                <br>

                                <fieldset class="border p-3" style="border-radius: 4px;"> 
                                    <legend class="w-auto">{{__('admin.Bank Details')}}</legend>

                                        <div class="row ml-0">
                                            <div class="form-group col-6">
                                                <label>{{__('admin.Account Holder Name')}} <span class="text-danger">*</span></label>
                                                <input type="text" id="name" class="form-control"  name="account_holder_name" style="width: 90%;">
                                            </div>

                                            <div class="form-group col-6">
                                                <label>{{__('admin.Account Number')}} <span class="text-danger">*</span></label>
                                                <input type="text" id="mobile" class="form-control"  name="account_number" style="width: 90%;">
                                            </div>
                                        </div>

                                        <div class="row ml-0">
                                            <div class="form-group col-9">
                                                <label>{{__('admin.IFSC Code')}} <span class="text-danger">*</span></label>
                                                <input type="text" id="name" class="form-control"  name="ifsc_code" style="width: 90%;">
                                            </div>
                                        </div>

                                </fieldset>

                                <br> 

                                    <div class="row">
                                        <div class="col-12">
                                            <button class="btn btn-primary py-2 px-5" style="float: right;">{{__('admin.Save')}}</button>
                                        </div>
                                    </div>



                                </form>
                            </div>
                        </div>
                </div>
          </div>
        </section>


<script>
(function($) {
        "use strict";
        var specification = true;
        $(document).ready(function () {
            

            $("#state").on("change",function(){
                var state_id = $("#state").val();
                if(state_id){
                    $.ajax({
                        type:"get",
                        url:"{{url('/seller/city-by-state/')}}"+"/"+state_id,
                        success:function(response){
                            $("#city").html(response.cities);
                        },
                        error:function(err){
                            console.log(err);

                        }
                    })
                }else{
                    var response= "<option value=''>{{__('admin.Select City')}}</option>";
                    $("#city").html(response);
                }

            })

        });
    })(jQuery);
</script>


@endsection