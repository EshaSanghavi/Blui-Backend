@extends('seller.master_layout')
@section('title')
<title>{{ $title }}</title>
@endsection
@section('seller-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.New Support ticket')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('seller.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.New Support Ticket')}}</div>
            </div>
          </div>

          <div class="section-body">
            <div class="row mt-4">
                <div class="col">
                    <div class="card">
                        <div class="card-body">

                        <form id="support_ticket_form" action="{{ route('seller.support-ticket-store') }}" method="POST" enctype="multipart/form-data">
                          @csrf

                            <div class="row">
                                <div class="form-group col-12">
                                    <label>{{__('admin.Issue')}} <span class="text-danger">*</span></label>
                                    <input type="text" id="issue" class="form-control"  name="issue">
                                </div>

                                <div class="form-group col-6">
                                    <label>{{__('admin.Support Ticket Type')}} <span class="text-danger">*</span></label>
                                    <select name="support_ticket_type" class="form-control">
                                        <option value=''>{{__('admin.Select Support Ticket Type')}}</option>
                                        @foreach($support_ticket_types as $type)    
                                          <option value="{{ $type->id }}">{{ $type->type }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-6">
                                    <label>{{__('admin.Callback Number')}} <span class="text-danger">*</span></label>
                                    <input type="text" id="phone" class="form-control"  name="phone" value="{{ $seller->pohone }}">
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Description')}}</label>
                                    <br>
                                    <input type="textarea" class="form-control" id="description" name="description" placeholder="Enter description">
                                </div>
                              
                            </div>
                            
                            <div class="row">
                                <div class="col-12">
                                    <button class="btn btn-primary" style="float: right;">{{__('admin.Submit')}}</button>
                                </div>
                            </div>

                        </form>
                        </div>
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
