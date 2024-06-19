@include('admin.header')
<div id="app">
    <section class="section">
      <div class="container mt-5">
        <div class="row">
        <div class="col-md-4"></div>
          <div class="col-md-4">

            <div class="card card-primary">
              <div class="card-header"><h4>{{__('admin.OPA Registration')}}</h4></div>

              <div class="card-body">
                <form class="needs-validation" method="POST" id="adminLoginForm" action="{{ route('opa.register.submit') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group col-12">
                        <label>{{__('admin.Full Name')}} <span class="text-danger">*</span></label>
                        <input type="text" id="name" class="form-control"  name="name">
                    </div>
                    
                    <div class="form-group col-12">
                        <label>{{__('admin.Business Name')}} <span class="text-danger">*</span></label>
                        <input type="text" id="business_name" class="form-control"  name="business_name">
                    </div>

                    <div class="form-group col-12 ">
                        <label>{{__('admin.Aadhar Card Number')}} <span class="text-danger">*</span></label>
                        <input type="text" id="aadhar_number" class="form-control"  name="aadhar_number">
                    </div>

                    <div class="form-group col-12 ">
                        <label>{{__('admin.Aadhar Card')}} <span class="text-danger">*</span></label>
                        <input type="file" class="form-control-file"  name="aadhar_card" onchange="previewAadharImage(event)">
                    </div>

                    <div class="form-group col-12 ">
                        <label>{{__('admin.Aadhar Card Preview')}}</label>
                        <div>
                            <img id="aadhar-preview" class="admin-img" src="{{ asset('uploads/website-images/preview.png') }}" alt="">
                        </div>
                    </div>

                    <div class="form-group col-12 ">
                        <label>{{__('admin.Pan Card Number')}} <span class="text-danger">*</span></label>
                        <input type="text" id="pan_number" class="form-control"  name="pan_number">
                    </div>

                    <div class="form-group col-12 ">
                        <label>{{__('admin.Pan Card')}} <span class="text-danger">*</span></label>
                        <input type="file" class="form-control-file"  name="pan_card" onchange="previewPanImage(event)">
                    </div>

                    <div class="form-group col-12 ">
                        <label>{{__('admin.Pan Card Preview')}}</label>
                        <div>
                            <img id="pan-preview" class="admin-img" src="{{ asset('uploads/website-images/preview.png') }}" alt="">
                        </div>
                    </div>

                    <div class="form-group col-12 ">
                        <label>{{__('admin.Phone Number')}} <span class="text-danger">*</span></label>
                        <input type="text" id="phone" class="form-control"  name="phone" >
                    </div>

                    <div class="form-group col-12 ">
                        <label>{{__('admin.Email Address')}} <span class="text-danger">*</span></label>
                        <input type="text" id="email" class="form-control"  name="email">
                    </div>

                    <div class="form-group col-12 ">
                        <label>{{__('admin.Password')}} <span class="text-danger">*</span></label>
                        <input type="password" id="password" class="form-control"  name="password">
                    </div>

                    <div class="form-group col-12 ">
                        <label>{{__('admin.Confirm Password')}} <span class="text-danger">*</span></label>
                        <input id="password_confirmation" type="password" class="form-control" name="password_confirmation">
                    </div>

                    
                    <div class="row">
                        <div class="">
                            <button type="submit" class="btn btn-primary">{{__('admin.Submit')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        </div>
    </div>
        </section>
    </div>

@include('admin.footer')


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

