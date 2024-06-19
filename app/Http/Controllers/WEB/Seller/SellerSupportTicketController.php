<?php

namespace App\Http\Controllers\WEB\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\SupportTicket;
use App\Models\SupportTicketType;
use Auth;
use Str;

class SellerSupportTicketController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web');
    }

    public function index()
    {
        $seller = Auth::guard('web')->user()->seller;
        $support_tickets = SupportTicket::where('user_id', $seller->id)
            ->leftjoin('support_ticket_types', 'support_tickets.type', '=', 'support_ticket_types.id')
            ->select('support_tickets.*', 'support_ticket_types.type as ticket_type')
            ->orderBy('support_tickets.id')
            ->get();
        $title = trans('admin.My Tickets');
        $setting = Setting::first();
        return view('seller.support_ticket', compact('support_tickets','title','setting'));   
    }

    public function create()
    {
        $seller = Auth::guard('web')->user()->seller;
        $support_ticket_types = SupportTicketType::all();
        $title = trans('admin.New Support Ticket');
        return view('seller.raise_support_ticket',compact('title', 'seller', 'support_ticket_types'));
    }

    public function store(Request $request)
    {
        $rules = [
            'issue' => 'required',
            'support_ticket_type' => 'required',
            'phone' => 'required',
            'description' => 'required',
        ];
        $customMessages = [
            'issue.required' => 'admin_validation.Support Ticket Issue is required',
            'support_ticket_type.required' => 'admin_validation.Support Ticket Type is required',
            'phone.required' => 'admin_validation.Phone Number is required',
            'description.required' => 'admin_validation.Support Ticket Descrition is required',
        ];
        $this->validate($request, $rules,$customMessages);

        $seller = Auth::guard('web')->user()->seller;
        $support_ticket = new SupportTicket();
        $support_ticket->user_id = $seller->id;
        $support_ticket->issue = $request->issue;
        $support_ticket->type = $request->support_ticket_type;
        $support_ticket->phone = $request->phone;
        $support_ticket->description = $request->description;
        $support_ticket->status = 0;
        $support_ticket->save();

        $notification = trans('admin_validation.Created Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('seller.support-ticket.index')->with($notification);
    }


    public function edit($id)
    {
        
    }


    public function update(Request $request, $id)
    {

        
    }

    public function destroy($id)
    {
        
    }

    public function changeStatus($id){
        
    }


}
