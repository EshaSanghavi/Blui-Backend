@php
use App\Models\CategoryAttribute;
use App\Models\SubCategoryAttribute;
use App\Models\ChildCategoryAttribute;
@endphp

@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Product Attribute')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Product Attributes')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item"><a href="{{ route('admin.product-attribute') }}">{{__('admin.Product Attributes')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Edit Product Attribute')}}</div>
            </div>
          </div>

          <div class="section-body">
            <div class="row mt-4">
                <div class="col">
                    <div class="card">
                        <div class="card-body">

                        <form id="attribute_form" action="{{ route('admin.update-attribute',$attribute->id) }}" method="POST" enctype="multipart/form-data">
                          @csrf

                            <div class="row">
                              <div class="form-group col-12">
                                  <label>{{__('admin.Name')}} <span class="text-danger">*</span></label>
                                  <input type="text" id="name" class="form-control"  name="name" value="{{ $attribute->name }}">
                              </div>
                            </div>

                            <div class="row">
                              <div class="form-group col-12">
                                <label>{{__('admin.Priority')}} <span class="text-danger">*</span></label>
                                <select name="priority" id="priority" class="form-control">
                                    <option value="{{ $attribute->priority }}">{{ $attribute->priority }}</option>   
                                </select>
                              </div>  
                            </div>

                            <div class="row">
                                <div class="form-group col-12">
                                    <label>{{__('admin.Attribute Value')}}</label>
                                    <br>
                                    <input type="text" id="attribute_value" name="attribute_value" placeholder="Enter values" data-role="tagsinput" style="width:100%;">
                                </div>
                            </div>


                            <div class="row">

                                <div class="form-group col-4">
                                    <label>{{__('admin.Required for Product')}} <span class="text-danger">*</span></label>
                                      <br>
                                      <a href="javascript::void()" id="manageRequiredBox">
                                          <input name="is_required" id="status_toggle" type="checkbox" checked data-toggle="toggle" data-on="Yes" data-off="No" data-onstyle="info" data-offstyle="secondary">
                                      </a>
                                </div>

                                <div class="form-group col-4">
                                    <label>{{__('admin.Attribute Type')}} <span class="text-danger">*</span></label>
                                    <select name="attribute_type" class="form-control">
                                        <option value=''>{{__('admin.Select Attribute Type')}}</option>
                                        @foreach($attribute_types as $attribute_type)   
                                          @if($attribute->type == $attribute_type->id) 
                                            <option value="{{ $attribute_type->id }}" selected>{{ $attribute_type->type }}</option>
                                          @else 
                                            <option value="{{ $attribute_type->id }}">{{ $attribute_type->type }}</option>
                                          @endif
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-4">
                                  <label>{{__('admin.Category')}} <span class="text-danger">*</span></label>
                                  <div class="border rounded px-3 py-2 min-h-40 d-flex justify-content-between gap-3">
                                    <label class="title-color mb-0">{{ __('admin.Use for all Categories')}}</label>
                                    <label class="switcher">
                                      @if($attribute->category == "ALL")
                                        <input class="switcher_input" type="checkbox" name="allcats" id="allcats" value="ALL" checked>
                                      @else
                                        <input class="switcher_input" type="checkbox" name="allcats" id="allcats" value="ALL">
                                      @endif
                                      <span class="switcher_control"></span>
                                    </label>
                                  </div> 
                              </div>
                              
                            </div>
                              

                            <div class="row">
                                @foreach($data as $categoryData)
                                <div class="col-4 category">
                                    <div class="form-group m-0">
                                        <div class="border rounded px-3 py-2 min-h-40 d-flex justify-content-between gap-3 mt-3">
                                          <label class="title-color mb-0">{{ $categoryData['category'] }}</label>
                                          <label class="switcher">
                                            @if($categoryData['category_check'] == 1)
                                              <input class="switcher_input category" value="{{ $categoryData['category_id'] }}" type="checkbox" name="category[]" id="switcher_input_category" checked>
                                            @else
                                              <input class="switcher_input category" value="{{ $categoryData['category_id'] }}" type="checkbox" name="category[]" id="switcher_input_category">
                                            @endif
                                            <span class="switcher_control"></span>
                                          </label>
                                        </div> 
                                        
                                        <div class="row">
                                            @foreach ($categoryData['subcategories'] as $subcategoryData)
                                                <div class="col-12 category_{{ $categoryData['category_id'] }} pl-5">
                                                    <div class="form-group m-0">
                                                        <div class="border rounded px-3 py-2 min-h-20 d-flex justify-content-between gap-3 mt-2">
                                                            <label class="title-color mb-1">{{ $subcategoryData['subcategory'] }}</label>
                                                            <label class="switcher">
                                                            @if($subcategoryData['subcategory_check'] == 1)
                                                              <input class="switcher_input category" value="{{ $subcategoryData['subcategory_id'] }}" type="checkbox" name="sub_category[]" id="switcher_input_subcategory" checked>
                                                            @else
                                                              <input class="switcher_input category" value="{{ $subcategoryData['subcategory_id'] }}" type="checkbox" name="sub_category[]" id="switcher_input_subcategory">                                                            
                                                            @endif
                                                              <span class="switcher_control"></span>
                                                            </label>
                                                        </div> 
                                                        <div class="row">
                                                            @foreach ($subcategoryData['childcategories'] as $childCategory => $cat)
                                                                <div class="col-12 subcategory_{{ $subcategoryData['subcategory_id'] }} pl-5">
                                                                    <div class="form-group m-0">
                                                                        <div class="border rounded px-3 py-2 min-h-20 d-flex justify-content-between gap-3 mt-1">
                                                                            <label class="title-color mb-1">{{ $cat }}</label>
                                                                            <label class="switcher">
                                                                                @php
                                                                                    $child_att = ChildCategoryAttribute::where('category', $childCategory)->where('attribute', $attribute->id)->first();
                                                                                    if($child_att){
                                                                                        echo "<input class='switcher_input category' value='". $childCategory ."' type='checkbox' name='child_category[]' id='switcher_input_childcategory' checked>";
                                                                                    }
                                                                                    else{ 
                                                                                        echo "<input class='switcher_input category' value='". $childCategory ."' type='checkbox' name='child_category[]' id='switcher_input_childcategory'>";
                                                                                    }
                                                                                @endphp
                                                                                <span class="switcher_control"></span>
                                                                            </label>
                                                                        </div> 
                                                                    </div>
                                                                </div>
                                                        @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        </div>
                                </div>
                                @endforeach
                            </div>
                            
                            <div class="row">
                                <div class="col-12">
                                    <button class="btn btn-primary" style="float: right;">{{__('admin.Save')}}</button>
                                </div>
                            </div>

                        </form>
                        </div>
                    </div>


                      
                </div>
              </div>
          </div>
        </section>

        <!-- Modal -->
        <div class="modal fade" id="canNotDeleteModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-body">
                  {{__('admin.You can not delete this attribute.')}}
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">{{__('admin.Close')}}</button>
              </div>
            </div>
          </div>
        </div>

      </div>
     
      

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js"></script>
  <script>
  (function($) {
        "use strict";
        var required = true;
        
            $("#manageRequiredBox").on("click",function(){
                if(required){
                  required = false;
                    $("#required-box").addClass('d-none');
                }else{
                  required = true;
                    $("#required-box").removeClass('d-none');
                }


            })
    });(jQuery)

    $('#attribute_value').tagsinput();


    $(document).on('click', '#allcats', function () {
			var checked=$(this).is(':checked');
			if(checked){
				$(".col-4.category").addClass('d-none');
			}else{
				$(".col-4.category").removeClass('d-none');
			}
		});
		

    $(document).on('click', '#switcher_input_category', function () {
      var checked=$(this).is(':checked');
      var catid=$(this).val();
      //alert(catid);
      if(checked){
        $(".col-12.category_"+catid).addClass('d-none');
      }else{
        $(".col-12.category_"+catid).removeClass('d-none');
      }
    });

    $(document).on('click', '#switcher_input_subcategory', function () {
      var checked=$(this).is(':checked');
      var catid=$(this).val();
      //alert(catid);
      if(checked){
        $(".col-12.subcategory_"+catid).addClass('d-none');
      }else{
        $(".col-12.subcategory_"+catid).removeClass('d-none');
      }
    });

    function deleteData(id){
        $("#deleteForm").attr("action",'{{ url("admin/delete-attribute/") }}'+"/"+id)
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

    var preSelectedTags = <?php echo $attribute_values; ?>;  // Get data from PHP

    $(document).ready(function() {
      var tagsInput = $('#attribute_value');

      // Add pre-selected tags from the PHP array
      preSelectedTags.forEach(function(tag) {
        tagsInput.tagsinput('add', tag);
      });
    });
</script>

@endsection
