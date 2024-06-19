<?php

namespace App\Http\Controllers\WEB\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\SupportTicket;
use App\Models\SupportTicketType;
use Auth;
use Str;

class SupportTicketController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function sellerTicketsRaised()
    {
        $support_tickets = SupportTicket::leftjoin('support_ticket_types', 'support_tickets.type', '=', 'support_ticket_types.id')
            ->leftjoin('vendors', 'support_tickets.user_id', '=', 'vendors.id')
            ->where('support_tickets.status', '=', '0')
            ->select('support_tickets.*', 'support_ticket_types.type as ticket_type')
            ->orderBy('support_tickets.id')
            ->get();

        $title = trans('admin.Seller Raised Support Tickets');
        return view('admin.support_ticket', compact('support_tickets','title'));   
    }

    public function sellerTicketsResolved()
    {
        $support_tickets = SupportTicket::leftjoin('support_ticket_types', 'support_tickets.type', '=', 'support_ticket_types.id')
            ->leftjoin('vendors', 'support_tickets.user_id', '=', 'vendors.id')
            ->where('support_tickets.status', '=', '1')
            ->select('support_tickets.*', 'support_ticket_types.type as ticket_type')
            ->orderBy('support_tickets.id')
            ->get();
        $title = trans('admin.Seller Resolved Support Tickets');
        return view('admin.support_ticket', compact('support_tickets','title')); 
    }


    public function opaTicketsRaised()
    {
        $support_tickets = SupportTicket::leftjoin('support_ticket_types', 'support_tickets.type', '=', 'support_ticket_types.id')
            ->leftjoin('opas', 'support_tickets.user_id', '=', 'opas.id')
            ->where('support_tickets.status', '=', '0')
            ->select('support_tickets.*', 'support_ticket_types.type as ticket_type')
            ->orderBy('support_tickets.id')
            ->get();
        $title = trans('admin.OPA Raised Support Tickets');
        return view('admin.support_ticket', compact('support_tickets','title'));   
    }

    public function opaTicketsResolved()
    {
        $support_tickets = SupportTicket::leftjoin('support_ticket_types', 'support_tickets.type', '=', 'support_ticket_types.id')
            ->leftjoin('opas', 'support_tickets.user_id', '=', 'opas.id')
            ->where('support_tickets.status', '=', '1')
            ->select('support_tickets.*', 'support_ticket_types.type as ticket_type')
            ->orderBy('support_tickets.id')
            ->get();
        $title = trans('admin.OPA Resolved Support Tickets');
        return view('admin.support_ticket', compact('support_tickets','title')); 
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
