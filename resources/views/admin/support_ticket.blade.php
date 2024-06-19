@extends('admin.master_layout')
@section('title')
<title>{{ $title }}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{ $title }}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard') }}</a></div>
              <div class="breadcrumb-item">{{ $title }}</div>
            </div>
          </div>

          <div class="section-body">
            @if($title == "Seller Raised Support Tickets")
              <a href="{{route('admin.seller-raised-support-ticket')}}" class="btn btn-info">{{__('admin.Raised Support Tickets')}}</a>
              <a href="{{route('admin.seller-resolved-support-ticket')}}" class="btn btn-white">{{__('admin.Resolved Support Tickets')}}</a>
            @elseif($title == "Seller Resolved Support Tickets")
              <a href="{{route('admin.seller-raised-support-ticket')}}" class="btn btn-white">{{__('admin.Raised Support Tickets')}}</a>
              <a href="{{route('admin.seller-resolved-support-ticket')}}" class="btn btn-info">{{__('admin.Resolved Support Tickets')}}</a>
            @elseif($title == "OPA Raised Support Tickets")
              <a href="{{route('admin.opa-raised-support-ticket')}}" class="btn btn-info">{{__('admin.Raised Support Tickets')}}</a>
              <a href="{{route('admin.opa-resolved-support-ticket')}}" class="btn btn-white">{{__('admin.Resolved Support Tickets')}}</a>
            @elseif($title == "OPA Resolved Support Tickets")
              <a href="{{route('admin.opa-raised-support-ticket')}}" class="btn btn-white">{{__('admin.Raised Support Tickets')}}</a>
              <a href="{{route('admin.opa-resolved-support-ticket')}}" class="btn btn-info">{{__('admin.Resolved Support Tickets')}}</a>
            @endif
            <div class="row mt-4">
                <div class="col">
                  <div class="card">
                    <div class="card-body">
                      <div class="table-responsive table-invoice">
                        <table class="table table-striped" id="dataTable">
                            <thead>
                                <tr>
                                    <th width="5%">{{__('admin.SN')}}</th>
                                    <th width="5%">{{__('admin.Seller')}}</th>
                                    <th width="10%">{{__('admin.Phone')}}</th>
                                    <th width="10%">{{__('admin.Issue')}}</th>
                                    <th width="10%">{{__('admin.Type')}}</th>
                                    <th width="10%">{{__('admin.Description')}}</th>
                                    <th width="15%">{{__('admin.Status')}}</th>
                                    <th width="15%">{{__('admin.Date')}}</th>
                                    <th width="5%">{{__('admin.Action')}}</th>
                                  </tr>
                            </thead>
                            <tbody>
                                @foreach ($support_tickets as $index => $support_ticket)
                                    <tr>
                                        <td>{{ ++$index }}</td>
                                        <td>{{ $support_ticket->user_id }}</td>
                                        <td>{{ $support_ticket->phone }}</td>
                                        <td>{{ $support_ticket->issue }}</td>
                                        <td>{{ $support_ticket->ticket_type }}</td>
                                        <td>{{ $support_ticket->description }}</td>
                                        <td>
                                            @if($support_ticket->status == 1)
                                            <span class="badge badge-success">{{__('admin.success')}} </span>
                                            @else
                                            <span class="badge badge-danger">{{__('admin.Pending')}}</span>
                                            @endif
                                        </td>
                                        <td>{{ $support_ticket->created_at->format('d F, Y') }}</td>
                                        <td>
                                        <a href="" class="btn btn-primary btn-sm"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                        
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
      </div>

      

    <script>
      function checkAll(ele) {
        var checkboxes = document.getElementsByTagName('input');
        if (ele.checked) {
            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].type == 'checkbox') {
                    checkboxes[i].checked = true;
                }
            }
        } 
        else {
            for (var i = 0; i < checkboxes.length; i++) {
                console.log(i)
                if (checkboxes[i].type == 'checkbox') {
                    checkboxes[i].checked = false;
                }
            }
        }
    }
 
 
    function checkThis() 
    {
        var checkboxes = document.getElementsByName('check-order');
        var orderIds = "";
        for(var i=0; i<checkboxes.length; i++)
            {
                if(checkboxes[i].checked==true)
                {
                    orderIds = orderIds + " " + checkboxes[i].value;
                }
            }
        document.getElementById("orderIds").value = orderIds;
    }

        function deleteData(id){
            $("#deleteForm").attr("action",'{{ url("admin/delete-order/") }}'+"/"+id)
        }
    </script>
@endsection

