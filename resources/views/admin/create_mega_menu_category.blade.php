@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Mega Menu Category')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Create Mega Menu Category')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Create Mega Menu Category')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="{{ route('admin.mega-menu-category.index') }}" class="btn btn-primary"><i class="fas fa-list"></i> {{__('admin.Mega Menu Category')}}</a>
            <div class="row mt-4">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.mega-menu-category.store') }}" method="POST">
                            @csrf
                            <div class="row">

                                <div class="form-group col-12">
                                    <label>{{__('admin.Category')}} <span class="text-danger">*</span></label>
                                    <select name="category" id="" class="form-control select2">
                                        <option value="">{{__('admin.Select Category')}}</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Priority')}} <span class="text-danger">*</span></label>
                                    <select name="priority" id="priority" class="form-control" value="{{$category->priority}}">
                                        <option value="">{{__('admin.Select Priority')}}</option>   
                                    </select>
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
