@php
use App\Models\CategoryAttribute;
use App\Models\SubCategoryAttribute;
use App\Models\ChildCategoryAttribute;
@endphp

@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Product Attributes')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Product Attributes')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Product Attributes')}}</div>
            </div>
          </div>

          <div class="card">
            <div class="card-body">
                <form action="{{ url()->current() }}" method="GET">
                    <div class="row">
                      <div class="col-12">
                          <h4 class="mb-3">{{ __('admin.Filter Attributes') }}</h4>
                      </div>
                    </div>
                    
                    <div class="row">
                      <div class="form-group col-4">
                            <label>{{__('admin.Category')}} <span class="text-danger">*</span></label>
                            <select name="category" id="category" class="form-control">
                                <option value="">{{__('admin.Select Category')}}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{request('category')==$category->id ? 'selected' :''}}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                      </div>
                      
                      <div class="form-group col-4">
                            <label>{{__('admin.Sub Category')}} <span class="text-danger">*</span></label>
                            <select name="sub_category" id="sub_category" class="form-control">
                              <option value="{{$subCategory != null ? $subCategory->id : null}}"
                                selected {{$subCategory != null ? '' : 'disabled'}}>{{$subCategory != null ? $subCategory->name : __('admin.Select Sub Category') }}</option>
                            </select>
                      </div>
                      
                      <div class="form-group col-4">
                            <label>{{__('admin.Child Category')}} <span class="text-danger">*</span></label>
                            <select name="child_category" id="child_category" class="form-control">
                            <option  value="{{$childCategory != null ? $childCategory->id : null}}"
                                selected {{$childCategory != null ? '' : 'disabled'}}>{{$childCategory != null ? $childCategory->name : __('admin.Select Child Category') }}</option>
                            </select>
                      </div>
                    </div>

                    <div class="row">
                      <div class="form-group col-12">
                        <div class="d-flex gap-3 justify-content-end">
                           <a href="{{ route('admin.product-attribute') }}"
                                  class="btn btn-secondary px-5">
                                  {{ __('admin.Reset') }}
                            </a>
                            <button type="submit" class="btn btn--primary px-5 action-get-element-type">
                                {{ __('admin.Filter') }}
                            </button>
                        </div>
                      </div>
                    </div>

                </form>
            </div>
        </div>


          <div class="section-body">
          <a href="{{ route('admin.create-product-attribute') }}" class="btn btn-primary"><i class="fas fa-plus"></i> {{__('admin.Add New')}}</a>
            <div class="row mt-4">
                <div class="col">
                
                      <div class="card">
                        <div class="card-body">
                          <div class="table-responsive table-invoice">
                            <table class="table table-striped" id="dataTable">
                                <thead>
                                    <tr>
                                        <th width="5%">{{__('admin.SN')}}</th>
                                        <th width="15%">{{__('admin.Attribute Name')}}</th>
                                        <th>{{__('admin.Priority')}}</th>
                                        <th width="15%">{{__('admin.Type')}}</th>
                                        <th width="5%">{{__('admin.Required')}}</th>
                                        <th width="40%">{{__('admin.Categories')}}</th>
                                        <th width="10%">{{__('admin.Action')}}</th>
                                      </tr>
                                </thead>
                                <tbody>
                                    @foreach ($attributes as $index => $attribute)
                                        <tr>
                                          <td>{{ ++$index }}</td>
                                            <td>{{ $attribute->name }}</a></td>
                                            <td>{{ $attribute->priority }}</td>
                                            <td>{{ $attribute->types }}</td>
                                            <td>
                                              @if($attribute->is_required == 1)
                                                Yes
                                              @else
                                                No
                                              @endif
                                            </td>
                                            <td>
                                              @if($attribute->categories == 'ALL')
                                                @foreach($categories as $category)
                                                  <span class="badge badge-danger p-1">{{ $category->name }}</span>
                                                @endforeach
                                              @else
                                                @php
                                                  $cats = CategoryAttribute::where('attribute', $attribute->id)
                                                    ->leftjoin('categories', 'category_attributes.category', '=', 'categories.id')
                                                    ->select('categories.name as name')
                                                    ->get();
                                                  // Loop through the array and display each item
                                                  foreach ($cats as $cat) {
                                                      echo '<span class="badge badge-danger p-1 mr-1">'.$cat->name.'</span>';
                                                  }

                                                  $cats = SubCategoryAttribute::where('attribute', $attribute->id)
                                                    ->leftjoin('sub_categories', 'sub_category_attributes.category', '=', 'sub_categories.id')
                                                    ->select('sub_categories.name as name')
                                                    ->get();
                                                  // Loop through the array and display each item
                                                  foreach ($cats as $cat) {
                                                      echo '<span class="badge badge-warning p-1 mr-1">'.$cat->name.'</span>';
                                                  }

                                                  $cats = ChildCategoryAttribute::where('attribute', $attribute->id)
                                                    ->leftjoin('child_categories', 'child_category_attributes.category', '=', 'child_categories.id')
                                                    ->select('child_categories.name as name')
                                                    ->get();
                                                  // Loop through the array and display each item
                                                  foreach ($cats as $cat) {
                                                      echo '<span class="badge badge-info p-1 mr-1">'.$cat->name.'</span>';
                                                  }
                                                @endphp
                                              @endif
                                            </td>
                                            <td>
                                              <a href="{{ route('admin.product_attributes.edit', $attribute->id) }}" class="btn btn-primary btn-sm"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                              <a href="javascript:;" data-toggle="modal" data-target="#deleteModal" class="btn btn-danger btn-sm" onclick="deleteData({{ $attribute->id }})"><i class="fa fa-trash" aria-hidden="true"></i></a>
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
        

        $(document).ready(function () {
    
           var categoryId = $("#category").val();
            if(categoryId){
                $.ajax({
                    type:"get",
                    url:"{{url('/admin/subcategory-by-category/')}}"+"/"+categoryId,
                    success:function(response){
                        $("#sub_category").append(response.subCategories);
                    },
                    error:function(err){
                    }
                })
            }
            
            var categoryId = $("#sub_category").val();
                if(categoryId){
                    $.ajax({
                        type:"get",
                        url:"{{url('/admin/childcategory-by-subcategory/')}}"+"/"+categoryId,
                        success:function(response){
                            $("#child_category").append(response.childCategories);
                        },
                        error:function(err){
                        }
                    })
                }
    
            $("#category").on("change",function(){
                var categoryId = $("#category").val();
                if(categoryId){
                    $.ajax({
                        type:"get",
                        url:"{{url('/admin/subcategory-by-category/')}}"+"/"+categoryId,
                        success:function(response){
                            $("#sub_category").html(response.subCategories);
                        },
                        error:function(err){
                        }
                    })
                }
            })
                
            $("#sub_category").on("change",function(){
                var categoryId = $("#sub_category").val();
                if(categoryId){
                    $.ajax({
                        type:"get",
                        url:"{{url('/admin/childcategory-by-subcategory/')}}"+"/"+categoryId,
                        success:function(response){
                            $("#child_category").html(response.childCategories);
                        },
                        error:function(err){
                        }
                    })
                }
            })
        });
    })(jQuery);
    

    function deleteData(id){
        $("#deleteForm").attr("action",'{{ url("admin/delete-attribute/") }}'+"/"+id)
    }
</script>

@endsection
