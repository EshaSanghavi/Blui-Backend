@php
use App\Models\AttributeValue;
@endphp
@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Products')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Create Product')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Create Product')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="{{ route('admin.product-select-category') }}" class="btn btn-primary"><i class="fas fa-list"></i> {{__('admin.Select Category')}}</a>
            <div class="row mt-4">
                <div class="col-12">
                <div class="card">
                        <div class="card-body">
                            <form action="{{ route('admin.store-product') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" id="category" name="category" value="{{ $category_id }}">
                                <input type="hidden" id="sub_category" name="sub_category" value="{{ $sub_category_id }}">
                                <input type="hidden" id="child_category" name="child_category" value="{{ $child_category_id }}">

                                
                                <fieldset class="border p-3" style="border-radius: 4px;"> 
                                    <legend class="w-auto">Product Details</legend>

                                        <div class="form-group col-12">
                                            <label>{{__('admin.Name')}} <span class="text-danger">*</span></label>
                                            <input type="text" id="name" class="form-control"  name="name" value="{{ old('name') }}">
                                        </div>

                                        <div class="row ml-0">

                                            <div class="form-group col-7">

                                                <label>{{__('admin.Video Link')}}</label>
                                                <input type="text" class="form-control"  name="video_link" style="width: 80%;">

                                            </div>

                                            <div class="form-group col-5 m-0 p-0">
                                                <label for="fileInput">{{__('admin.Upload Images')}} <span class="text-danger">*</span></label>
                                                <div class="image-upload-container">
                                                    <div id="preview-container"></div>
                                                    <input type="file" id="fileInput" name="images[]" accept="image/*" multiple onchange="previewMultipleImages(event)" hidden>
                                                    <label for="fileInput" class="image-upload-button">
                                                        <span>+</span>
                                                    </label>
                                                </div>
                                            </div>
                                            
                                        </div>

                                        @foreach($attributes as $att)
                                            @if($att->types == "Product Details")
                                                <div class="row ml-0">
                                                    <div class="form-group col-6">
                                                        <label>{{ $att->name }}
                                                            @if($att->is_required == 1)
                                                                <span class="text-danger">*</span>
                                                            @endif
                                                        </label>
                                                        <input type="text" class="form-control" name="{{ $att->name }}" id="{{ $att->name}}_other" style="display: none;" onchange="onOther(this)">
                                                        <br>
                                                        @php
                                                            $values = AttributeValue::where('attribute_id', $att->id)->get();
                                                            // Loop through the array and display each item
                                                            if(count($values) === 0){
                                                                echo '<input type="text" class="form-control" name="' . $att->name . '" id="' . $att->name . '_other">';
                                                            }
                                                            else{
                                                                echo '<select class="form-control" name="' . $att->name . '" id="' . $att->name . '_other" onchange="otherValue(this)">';
                                                                echo '<option value="">Select Value</option>';
                                                                foreach ($values as $val) {
                                                                    echo '<option value="' . $val->value . '">' . $val->value . '</option>';
                                                                }
                                                                echo '<option value="other">Other</option>';
                                                                echo '</select>';
                                                            }
                                                        @endphp
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach


                                </fieldset>

                                <br>

                                <fieldset class="border p-3" style="border-radius: 4px;"> 
                                    <legend class="w-auto">Size & Stock</legend>

                                        <div class="form-group col-12">
                                            <label>{{__('admin.Weight')}} <span class="text-danger">*</span></label>
                                           <input type="text" class="form-control" name="weight" value="{{ old('weight') }}">
                                        </div>


                                        <div class="form-group col-12">
                                            <label>{{__('admin.Stock')}} <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" name="quantity" value="{{ old('quantity') }}">
                                        </div>

                                        <div class="form-group col-12">
                                            <label>{{__('admin.SKU')}} <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="sku">
                                        </div>

                                        <div class="form-group col-12">
                                            <label>{{__('admin.Specifications')}}</label>
                                            <div>
                                                <a href="javascript::void()" id="manageSpecificationBox">
                                                    <input name="is_specification" id="status_toggle" type="checkbox" checked data-toggle="toggle" data-on="Enable" data-off="Disabled" data-onstyle="success" data-offstyle="danger">
                                                </a>
                                            </div>
                                        </div>

                                        <div class="form-group col-12" id="specification-box">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <label>{{__('admin.Key')}} <span class="text-danger">*</span></label>
                                                    <select name="keys[]" class="form-control">
                                                        @foreach ($specificationKeys as $specificationKey)
                                                            <option value="{{ $specificationKey->id }}">{{ $specificationKey->key }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-5">
                                                    <label>{{__('admin.Specification')}} <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="specifications[]">
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="button" class="btn btn-success plus_btn" id="addNewSpecificationRow"><i class="fas fa-plus"></i></button>
                                                </div>
                                            </div>
                                        </div>


                                        <div id="hidden-specification-box" class="d-none">
                                            <div class="delete-specification-row">
                                                <div class="row mt-2">
                                                    <div class="col-md-5">
                                                        <label>{{__('admin.Key')}} <span class="text-danger">*</span></label>
                                                        <select name="keys[]" class="form-control">
                                                            @foreach ($specificationKeys as $specificationKey)
                                                                <option value="{{ $specificationKey->id }}">{{ $specificationKey->key }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <label>{{__('admin.Specification')}} <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" name="specifications[]">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="button" class="btn btn-danger plus_btn deleteSpeceficationBtn"><i class="fas fa-trash"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        @foreach($attributes as $att)
                                            @if($att->types == "Size & Stock")
                                                <div class="row ml-0">
                                                    <div class="form-group col-6">
                                                        <label>{{ $att->name }}
                                                            @if($att->is_required == 1)
                                                                <span class="text-danger">*</span>
                                                            @endif
                                                        </label>
                                                        <input type="text" class="form-control" name="{{ $att->name }}" id="{{ $att->name}}_other" style="display: none;" onchange="onOther(this)">
                                                        <br>
                                                        @php
                                                            $values = AttributeValue::where('attribute_id', $att->id)->get();
                                                            // Loop through the array and display each item
                                                            if(count($values) === 0){
                                                                echo '<input type="text" class="form-control" name="' . $att->name . '" id="' . $att->name . '_other">';
                                                            }
                                                            else{
                                                                echo '<select class="form-control" name="' . $att->name . '" id="' . $att->name . '_other" onchange="otherValue(this)">';
                                                                echo '<option value="">Select Value</option>';
                                                                foreach ($values as $val) {
                                                                    echo '<option value="' . $val->value . '">' . $val->value . '</option>';
                                                                }
                                                                echo '<option value="other">Other</option>';
                                                                echo '</select>';
                                                            }
                                                        @endphp
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach

                                </fieldset>

                                <br>

                                <fieldset class="border p-3" style="border-radius: 4px;"> 
                                    <legend class="w-auto">Price & Tax</legend>

                                        <div class="form-group col-12">
                                            <label>{{__('admin.Price')}} <span class="text-danger">*</span></label>
                                           <input type="text" class="form-control" name="price" value="{{ old('price') }}">
                                        </div>

                                        <div class="form-group col-12">
                                            <label>{{__('admin.Offer Price')}}</label>
                                           <input type="text" class="form-control" name="offer_price" value="{{ old('offer_price') }}">
                                        </div>

                                        @foreach($attributes as $att)
                                            @if($att->types == "Price & Tax")
                                                <div class="row ml-0">
                                                    <div class="form-group col-6">
                                                        <label>{{ $att->name }}
                                                            @if($att->is_required == 1)
                                                                <span class="text-danger">*</span>
                                                            @endif
                                                        </label>
                                                        <input type="text" class="form-control" name="{{ $att->name }}" id="{{ $att->name}}_other" style="display: none;" onchange="onOther(this)">
                                                        <br>
                                                        @php
                                                            $values = AttributeValue::where('attribute_id', $att->id)->get();
                                                            // Loop through the array and display each item
                                                            if(count($values) === 0){
                                                                echo '<input type="text" class="form-control" name="' . $att->name . '" id="' . $att->name . '_other">';
                                                            }
                                                            else{
                                                                echo '<select class="form-control" name="' . $att->name . '" id="' . $att->name . '_other" onchange="otherValue(this)">';
                                                                echo '<option value="">Select Value</option>';
                                                                foreach ($values as $val) {
                                                                    echo '<option value="' . $val->value . '">' . $val->value . '</option>';
                                                                }
                                                                echo '<option value="other">Other</option>';
                                                                echo '</select>';
                                                            }
                                                        @endphp
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                </fieldset>

                                <br>

                                <fieldset class="border p-3" style="border-radius: 4px;"> 
                                    <legend class="w-auto">Other Details</legend>

                                        @foreach($attributes as $att)
                                            @if($att->types == "Other Details")
                                                <div class="row ml-0">
                                                    <div class="form-group col-6">
                                                        <label>{{ $att->name }}
                                                            @if($att->is_required == 1)
                                                                <span class="text-danger">*</span>
                                                            @endif
                                                        </label>
                                                        <input type="text" class="form-control" name="{{ $att->name }}" id="{{ $att->name}}_other" style="display: none;" onchange="onOther(this)">
                                                        <br>
                                                        @php
                                                            $values = AttributeValue::where('attribute_id', $att->id)->get();
                                                            // Loop through the array and display each item
                                                            if(count($values) === 0){
                                                                echo '<input type="text" class="form-control" name="' . $att->name . '" id="' . $att->name . '_other">';
                                                            }
                                                            else{
                                                                echo '<select class="form-control" name="' . $att->name . '" id="' . $att->name . '_other" onchange="otherValue(this)">';
                                                                echo '<option value="">Select Value</option>';
                                                                foreach ($values as $val) {
                                                                    echo '<option value="' . $val->value . '">' . $val->value . '</option>';
                                                                }
                                                                echo '<option value="other">Other</option>';
                                                                echo '</select>';
                                                            }
                                                        @endphp
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
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
            $("#name").on("focusout",function(e){
                $("#slug").val(convertToSlug($(this).val()));
            })

            $("#category").on("change",function(){
                var categoryId = $("#category").val();
                if(categoryId){
                    $.ajax({
                        type:"get",
                        url:"{{url('/seller/subcategory-by-category/')}}"+"/"+categoryId,
                        success:function(response){
                            $("#sub_category").html(response.subCategories);
                            var response= "<option value=''>{{__('admin.Select Child Category')}}</option>";
                            $("#child_category").html(response);
                        },
                        error:function(err){
                            console.log(err);

                        }
                    })
                }else{
                    var response= "<option value=''>{{__('admin.Select Sub Category')}}</option>";
                    $("#sub_category").html(response);
                    var response= "<option value=''>{{__('admin.Select Child Category')}}</option>";
                    $("#child_category").html(response);
                }


            })

            $("#sub_category").on("change",function(){
                var SubCategoryId = $("#sub_category").val();
                if(SubCategoryId){
                    $.ajax({
                        type:"get",
                        url:"{{url('/seller/childcategory-by-subcategory/')}}"+"/"+SubCategoryId,
                        success:function(response){
                            $("#child_category").html(response.childCategories);
                        },
                        error:function(err){
                            console.log(err);

                        }
                    })
                }else{
                    var response= "<option value=''>{{__('admin.Select Child Category')}}</option>";
                    $("#child_category").html(response);
                }

            })

            $("#is_return").on('change',function(){
                var returnId = $("#is_return").val();
                if(returnId == 1){
                    $("#policy_box").removeClass('d-none');
                }else{
                    $("#policy_box").addClass('d-none');
                }

            })

            $("#addNewSpecificationRow").on('click',function(){
                var html = $("#hidden-specification-box").html();
                $("#specification-box").append(html);
            })

            $(document).on('click', '.deleteSpeceficationBtn', function () {
                $(this).closest('.delete-specification-row').remove();
            });


            $("#manageSpecificationBox").on("click",function(){
                if(specification){
                    specification = false;
                    $("#specification-box").addClass('d-none');
                }else{
                    specification = true;
                    $("#specification-box").removeClass('d-none');
                }


            })

        });
    })(jQuery);
   
   
    function previewMultipleImages(event) {
  const fileInput = document.getElementById('fileInput');
  const previewContainer = document.getElementById('preview-container');
  const maxFiles = 5; // Adjust the maximum allowed files
     const hiddenInputs = document.querySelectorAll('input[name="images[]"]');
  const fileList = event.target.files;

  // Check for exceeding the maximum file limit
  if (hiddenInputs.length >= maxFiles) {
    alert(`You can only upload a maximum of ${maxFiles} files.`);
    return; // Exit early if exceeding limit
  }

  for (let i = 0; i < fileList.length; i++) {
    const file = fileList[i];

    // Create image container and image elements
    const imageContainer = document.createElement('div');
    imageContainer.classList.add('image-container');
    imageContainer.style.position = 'relative'; // Enable relative positioning for button

    const img = new Image();
    img.classList.add('preview-image');
    img.style.width = '80px';
    img.style.height = '80px';

    // Handle potential errors during image loading
    img.onerror = function() {
      console.error('Error loading image:', file.name);
      // Optionally display an error message to the user
    };

    // Create a FileReader object for each image
    const reader = new FileReader();
    reader.onload = function(e) {
      img.src = e.target.result;

      // Create cancel button and event listener
      const cancelButton = document.createElement('button');
      cancelButton.classList.add('cancel-button'); // Add styling for the button (optional)
      cancelButton.textContent = '✕';
      cancelButton.style.position = 'absolute'; // Absolute positioning for top right corner
      cancelButton.style.top = '-1px'; // Adjust top position (optional)
      cancelButton.style.right = '4px'; // Adjust right position (optional)
      cancelButton.addEventListener('click', function() {
        // Remove image container and hidden input on cancel
        previewContainer.removeChild(imageContainer);
        const hiddenInputs = document.querySelectorAll('input[name="images[]"]');
        for (let j = 0; j < hiddenInputs.length; j++) {
          if (hiddenInputs[j].value === e.target.result) {
            hiddenInputs[j].remove();
            break;
          }
        }
      });

      imageContainer.appendChild(img);
      imageContainer.appendChild(cancelButton);
      previewContainer.appendChild(imageContainer);

      // Capture image data and create a hidden input element
      const imageData = e.target.result;
      const hiddenInput = document.createElement('input');
      hiddenInput.type = 'hidden';
      hiddenInput.name = 'images[]';
      hiddenInput.value = imageData;

      // Add the hidden input to the form
      document.getElementById('yourFormId').appendChild(hiddenInput);
    };

    reader.readAsDataURL(file);
  }
}



function otherValue(ele) {
    var id = ele.name+"_other";
    var otherInput = document.getElementById(id);
    if (ele.value === "other") {
        otherInput.style.display = 'block';
    } else {
        otherInput.style.display = 'none';
        otherInput.value = ''; // Clear the input value if another option is selected
    }
}

function onOther(ele) {
    var id = ele.name;
    var selectElement = document.getElementById(id);
    var newOption = document.createElement('option');
    newOption.value = ele.value;
    newOption.text = ele.value;
    newOption.selected = true;
    selectElement.appendChild(newOption);
}
</script>


@endsection