@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Product Sub Category')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Create Product Sub Category')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.product-category.index') }}">{{__('admin.Product Category')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Create Product Sub Category')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="{{ route('admin.product-sub-category.index') }}" class="btn btn-primary"><i class="fas fa-list"></i> {{__('admin.Product Sub Category')}}</a>
            <div class="row mt-4">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.product-sub-category.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">

                                <div class="form-group col-12">
                                    <label>{{__('admin.Category')}} <span class="text-danger">*</span></label>
                                    <select name="category" id="category" class="form-control">
                                        <option value="">{{__('admin.Select Category')}}</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Sub Category Name')}} <span class="text-danger">*</span></label>
                                    <input type="text" id="name" class="form-control"  name="name">
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Image')}} <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control"  name="image">
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Priority')}} <span class="text-danger">*</span></label>
                                    <select name="priority" id="priority" class="form-control">
                                        <option value="">{{__('admin.Select Priority')}}</option>   
                                    </select>
                                </div>

                                <div class="form-group col-12">
                                    <label for="child-checkbox">Does this Sub Category have Child Categories?</label>
                                    <span><span><input type="checkbox" id="child-checkbox" name="child-checkbox" onchange="toggleInput()"></span></span>
                                    <br>
                                    <label>{{__('admin.Commission')}}</label>
                                    <input type="number" id="commission" class="form-control"  name="commission">
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Slug')}} <span class="text-danger">*</span></label>
                                    <input type="text" id="slug" class="form-control"  name="slug">
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Status')}} <span class="text-danger">*</span></label>
                                    <select name="status" class="form-control">
                                        <option value="1">{{__('admin.Active')}}</option>
                                        <option value="0">{{__('admin.Inactive')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button class="btn btn-primary">{{__('admin.Save')}}</button>
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
    function toggleInput() {
        var checkbox = document.getElementById("child-checkbox");
        var userInput = document.getElementById("commission");

        if (checkbox.checked) {
            userInput.disabled = true;
            userInput.value = null;
        } else {
            userInput.disabled = false;
        }
    }

    (function($) {
        "use strict";
        $(document).ready(function () {
            $("#name").on("focusout",function(e){
                $("#slug").val(convertToSlug($(this).val()));
            })
        });
    })(jQuery);

    function convertToSlug(Text)
        {
            return Text
                .toLowerCase()
                .replace(/[^\w ]+/g,'')
                .replace(/ +/g,'-');
        }


    var dropdown = document.getElementById("priority");
    // Loop to generate options
    for (var i = 1; i <= 15; i++) {
        // Create an option element
        var option = document.createElement("option");
        // Set the value and text of the option
        option.value = i;
        option.text = i;
        // Append the option to the dropdown
        dropdown.appendChild(option);
    }
</script>
@endsection
