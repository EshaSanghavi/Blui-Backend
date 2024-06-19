@php
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\ChildCategory;
@endphp

@extends('seller.master_layout')
@section('title')
<title>{{ __('admin.Select Product Category')}}</title>
@endsection
@section('seller-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{ __('admin.Select Product Category') }}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('seller.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{ __('admin.Create Product') }}</div>
            </div>
          </div>
             
          <div class="section-body">
            <a href="{{ route('seller.product.index') }}" class="btn btn-primary"><i class="fas fa-list"></i> {{__('admin.Products')}}</a>
            <div class="row mt-4">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                    <form action="{{ route('seller.create-product') }}" method="POST" enctype="multipart/form-data">
                            @csrf
              

                        <div class="row">
                            <div class="col-4">
                                <h6>{{__('admin.Select Category')}}</h6>
                                @foreach($data as $categoryData)
                                    <div class="border rounded px-3 py-2 min-h-20 d-flex justify-content-between gap-3 mt-2" style="cursor: pointer;" id="switcher_input_category" name ="switcher_input_category">
                                        <input type="checkbox"  value="{{ $categoryData['category_id'] }}" name="category" visibility: hidden>
                                          <span>{{ $categoryData['category'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        
                                @foreach($data as $categoryData)
                                <div class="col-4 subcategory category_{{ $categoryData['category_id'] }} d-none">
                                    @if(!empty($categoryData['subcategories']))
                                        <h6>{{__('admin.Select Sub Category')}}</h6>
                                        @foreach ($categoryData['subcategories'] as $subcategoryData)
                                            <div class="border rounded px-3 py-2 min-h-20 d-flex justify-content-between gap-3 mt-2" style="cursor: pointer;" id="switcher_input_subcategory" name ="switcher_input_subcategory">
                                                <input type="checkbox" value="{{ $subcategoryData['subcategory_id'] }}" name="sub_category" visibility: hidden>
                                                  <span>{{ $subcategoryData['subcategory'] }}</span>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                @endforeach
                        
                        
                                @foreach($data as $categoryData)
                                    @foreach ($categoryData['subcategories'] as $subcategoryData)
                                    <div class="col-4 childcategory subcategory_{{ $subcategoryData['subcategory_id'] }} d-none">
                                        @if(!empty($subcategoryData['childcategories']))
                                            <h6>{{__('admin.Select Child Category')}}</h6>
                                            @foreach($subcategoryData['childcategories'] as $childCategory)
                                                <div class="border rounded px-3 py-2 min-h-20 d-flex justify-content-between gap-3 mt-2" style="cursor: pointer;" id="switcher_input_childcategory" name="switcher_input_childcategory">
                                                    <input type="checkbox" value="{{ $childCategory }}" name="child_category" visibility: hidden>
                                                      <span>{{ $childCategory }}</span>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    @endforeach
                                @endforeach
                            </div>
                        
                            <br>
                            <div class="row">
                                <div class="col-12">
                                    <button class="btn btn-primary" style="float: right;">{{__('admin.Proceed')}}</button>
                                </div>
                            </div>
                        </form>
                        </div>
          </div>
        </section>
      </div>

      

     

      

<script>
		

    $(document).on('click', '#switcher_input_category', function () {
        var elems = document.getElementsByName('switcher_input_category');
        for (var i = 0; i<elems.length; i++) {
            if(elems[i] == this)
                continue;
            elems[i].style.background = "white";
            elems[i].style.color = "#6c757d";
            checkbox = elems[i].children[0];
            checkbox.checked = false;
        }
        var elems = document.getElementsByName('switcher_input_subcategory');
        for (var i = 0; i<elems.length; i++) {
            elems[i].style.background = "white";
            elems[i].style.color = "#6c757d";
            checkbox = elems[i].children[0];
            checkbox.checked = false;
        }
        var elems = document.getElementsByName('switcher_input_childcategory');
        for (var i = 0; i<elems.length; i++) {
            elems[i].style.background = "white";
            elems[i].style.color = "#6c757d";
            checkbox = elems[i].children[0];
            checkbox.checked = false;
        }
        
        
        $(".col-4.subcategory").addClass('d-none');
        $(".col-4.childcategory").addClass('d-none');
        checkbox = this.children[0];
        var checked=$(checkbox).is(':checked');
        var catid=$(checkbox).val();
        //alert(catid);
        if(!checked){
            checkbox.checked = true;
            $(".col-4.subcategory.category_"+catid).removeClass('d-none');
            this.style.background = "SlateBlue";
            this.style.color = "white";
        }else{
            checkbox.checked = false;
            this.style.background = "white";
            this.style.color = "#6c757d";
        }
    });

    $(document).on('click', '#switcher_input_subcategory', function () {
        
        var elems = document.getElementsByName('switcher_input_subcategory');
        for (var i = 0; i<elems.length; i++) {
            if(elems[i] == this)
                continue;
            elems[i].style.background = "white";
            elems[i].style.color = "#6c757d";
            checkbox = elems[i].children[0];
            checkbox.checked = false;
        }
        var elems = document.getElementsByName('switcher_input_childcategory');
        for (var i = 0; i<elems.length; i++) {
            elems[i].style.background = "white";
            elems[i].style.color = "#6c757d";
            checkbox = elems[i].children[0];
            checkbox.checked = false;
        }
        
        
        $(".col-4.childcategory").addClass('d-none');
        checkbox = this.children[0];
        var checked=$(checkbox).is(':checked');
        var catid=$(checkbox).val();
        //alert(catid);
        if(!checked){
            checkbox.checked = true;
            $(".col-4.childcategory.subcategory_"+catid).removeClass('d-none');
            this.style.background = "SlateBlue";
            this.style.color = "white";
        }else{
            checkbox.checked = false;
            this.style.background = "white";
            this.style.color = "#6c757d";
        }
    });
    
    
    $(document).on('click', '#switcher_input_childcategory', function () {
        
        var elems = document.getElementsByName('switcher_input_childcategory');
        for (var i = 0; i<elems.length; i++) {
            if(elems[i] == this)
                continue;
            elems[i].style.background = "white";
            elems[i].style.color = "#6c757d";
            checkbox = elems[i].children[0];
            checkbox.checked = false;
        }
        
        
        checkbox = this.children[0];
        var checked=$(checkbox).is(':checked');
        var catid=$(checkbox).val();
        //alert(catid);
        if(!checked){
            checkbox.checked = true;
            this.style.background = "SlateBlue";
            this.style.color = "white";
        }else{
            checkbox.checked = false;
            this.style.background = "white";
            this.style.color = "#6c757d";
        }
        
    });
</script>
@endsection
