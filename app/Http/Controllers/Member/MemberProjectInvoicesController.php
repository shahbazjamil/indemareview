<?php

namespace App\Http\Controllers\Member;

use App\Project;
use App\Helper\Reply;
use Yajra\DataTables\Facades\DataTables;
use App\Currency;
use App\PurchaseOrder;
use App\ClientVendorDetails;
use App\Invoice;
use App\InvoiceSetting;
use Carbon\Carbon;

class MemberProjectInvoicesController extends MemberBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Client Invoice';
        $this->pageIcon = 'ti-receipt';
        $this->middleware(function ($request, $next) {
            if (!in_array('invoices', $this->user->modules)) {
                abort(403);
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->project = Project::findorFail($id);
        $this->currencies = Currency::all();
        return view('member.projects.invoices.show', $this->data);
    }

    public function data($id)
    {
        
        $firstInvoice = Invoice::orderBy('id', 'desc')->first();
        $invoiceSettings = InvoiceSetting::select('invoice_prefix', 'invoice_digit')->first();
        $invoices = Invoice::where('project_id', $id)->get();

        return DataTables::of($invoices)
             ->addColumn('action', function ($row) use ($firstInvoice) {
                $action = '<div class="btn-group m-r-10">
                <button aria-expanded="false" data-toggle="dropdown" class="btn btn-info btn-outline  dropdown-toggle waves-effect waves-light" type="button">' . __('app.action') . ' <span class="caret"></span></button>
                <ul role="menu" class="dropdown-menu">';

                if ($this->user->can('view_invoices')) {
                    $action .= '<li><a href="' . route("member.all-invoices.download", $row->id) . '"><i class="fa fa-download"></i> ' . __('app.download') . '</a></li>';
                }
                if ($row->status == 'paid') {
                    $action .= ' <li><a href="javascript:" data-invoice-id="' . $row->id . '" class="invoice-upload" data-toggle="modal" data-target="#invoiceUploadModal"><i class="fa fa-upload"></i> ' . __('app.upload') . ' </a></li>';
                }

                if ($row->status != 'draft') {
                    $action .= '<li><a href="javascript:;" data-toggle="tooltip"  data-invoice-id="' . $row->id . '" class="sendButton"><i class="fa fa-send"></i> ' . __('app.send') . '</a></li>';
                }

                if (($row->status == 'unpaid' || $row->status == 'draft') && $this->user->can('edit_invoices')) {
                    $action .= '<li><a href="' . route("member.all-invoices.edit", $row->id) . '"><i class="fa fa-pencil"></i> ' . __('app.edit') . '</a></li>';
                }

                if ($row->status != 'paid') {
                    if (in_array('payments', $this->user->modules)  && $this->user->can('add_payments') && $row->status != 'draft') {
                        $action .= '<li><a href="' . route("member.payments.payInvoice", [$row->id]) . '" data-toggle="tooltip" ><i class="fa fa-plus"></i> ' . __('modules.payments.addPayment') . '</a></li>';
                    }
                }

                if ($row->clientdetails) {
                    if (!is_null($row->clientdetails->shipping_address)) {
                        if ($row->show_shipping_address === 'yes') {
                            $action .= '<li><a href="javascript:toggleShippingAddress(' . $row->id . ');"><i class="fa fa-eye-slash"></i> ' . __('app.hideShippingAddress') . '</a></li>';
                        } else {
                            $action .= '<li><a href="javascript:toggleShippingAddress(' . $row->id . ');"><i class="fa fa-eye"></i> ' . __('app.showShippingAddress') . '</a></li>';
                        }
                    } else {
                        $action .= '<li><a href="javascript:addShippingAddress(' . $row->id . ');"><i class="fa fa-plus"></i> ' . __('app.addShippingAddress') . '</a></li>';
                    }
                } else {
                    if ($row->project->clientdetails) {
                        if (!is_null($row->project->clientdetails->shipping_address)) {
                            if ($row->show_shipping_address === 'yes') {
                                $action .= '<li><a href="javascript:toggleShippingAddress(' . $row->id . ');"><i class="fa fa-eye-slash"></i> ' . __('app.hideShippingAddress') . '</a></li>';
                            } else {
                                $action .= '<li><a href="javascript:toggleShippingAddress(' . $row->id . ');"><i class="fa fa-eye"></i> ' . __('app.showShippingAddress') . '</a></li>';
                            }
                        } else {
                            $action .= '<li><a href="javascript:addShippingAddress(' . $row->id . ');"><i class="fa fa-plus"></i> ' . __('app.addShippingAddress') . '</a></li>';
                        }
                    }
                }

                if ($this->user->can('delete_invoices') && $firstInvoice->id == $row->id) {
                    $action .= '<li><a href="javascript:;" data-toggle="tooltip"  data-invoice-id="' . $row->id . '" class="sa-params"><i class="fa fa-times"></i> ' . __('app.delete') . '</a></li>';
                }
                if ($firstInvoice->id != $row->id && $row->status == 'unpaid' && $this->user->can('edit_invoices')) {
                    $action .= '<li><a href="javascript:;" data-toggle="tooltip" title="' . __('app.cancel') . '"  data-invoice-id="' . $row->id . '" class="sa-cancel"><i class="fa fa-times"></i> ' . __('app.cancel') . '</a></li>';
                }
                
                if(in_array('invoicesrefund',$this->user->modules)){
                    if ($row->status == 'paid') {
                        $action .= '<li><a href="javascript:;" class="invoice-refund" data-toggle="tooltip" data-invoice-id="' . $row->id . '"  data-original-title="Refund"><i class="fa fa-undo" aria-hidden="true"></i> Refund </a></li>';
                    }
                }
                
                $action .= '</ul>
              </div>
              ';

                return $action;
            })
            
             ->editColumn('project_name', function ($row) {
                if ($row->project_id != null) {
                    return '<a href="' . route('member.projects.show', $row->project_id) . '">' . ucfirst($row->project->project_name) . '</a>';
                }

                return '--';
            })
            ->editColumn('name', function ($row) {
                if ($row->client_id){
                    if($row->client->image) {
                        $image = '<img src="' . $row->client->image_url . '"alt="user" class="img-circle" width="30" height="30"> ';
                    } else {
                        $image = '<span class="nameletter">'.company_initials().'</span>';
                    }
                    
                    return  '<div class="row truncate"><div class="col-sm-3 col-xs-4">' . $image . '</div><div class="col-sm-9 col-xs-8">' . ucwords($row->client->name) . '</div></div>';
                }
                return '--';
            })
            ->editColumn('vendor_name', function ($row) {
                if ($row->vendor_id) {
                    return $row->vendor->name;
                }
                return '--';
            })
            
            ->editColumn('tags', function ($row) {
                $tags = '';
                if($row->tags) {
                    $tags = $row->tags ? json_decode($row->tags) : array();
                    if($tags) {
                        $tags = implode(', ', $tags);
                    }
                    
                }
                return $tags;
            })

            ->editColumn('invoice_number', function ($row) {
                return '<a href="' . route('member.all-invoices.show', $row->id) . '">' . ucfirst($row->invoice_number) . '</a>';
            })
            ->editColumn('status', function ($row) {
                $status = '';
                if ($row->credit_note) {
                    $status.= '<label class="label label-warning">' . strtoupper(__('app.credit-note')) . '</label>';
                } else {
                    if ($row->status == 'unpaid') {
                        $status.= '<label class="label label-danger">' . __('app.'.$row->status) . '</label>';
                    } elseif ($row->status == 'paid') {
                        $status.= '<label class="label label-success">' . __('app.'.$row->status) . '</label>';
                    } elseif ($row->status == 'draft') {
                        $status.= '<label class="label label-primary">' . __('app.'.$row->status) . '</label>';
                    } elseif ($row->status == 'canceled') {
                        $status.= '<label class="label label-danger">' . __('app.'.$row->status) . '</label>';
                    } elseif ($row->status == 'review') {
                        return '<label class="label label-warning">' . __('app.'.$row->status) . '</label>';
                    } else {
                        $status.= '<label class="label label-info">' . strtoupper(__('modules.invoices.partial')) . '</label>';
                    }
                }
                if (!$row->send_status && $row->status != 'draft') {
                    $status.= '<br><br><label class="label label-inverse">' . strtoupper(__('modules.invoices.notSent')) . '</label>';
                }
                
                if ($row->refund_status == 'refund') {
                    $status.= '<label class="label label-success">Refund</label>';
                } else if($row->refund_status == 'partial_refund'){
                     $status.= '<label class="label label-success">Partial Refund</label>';
                }
                
                return $status;
            })
            ->editColumn('total', function ($row) {
                $currencyCode = ' (' . $row->currency->currency_code . ') ';
                $currencySymbol = $row->currency->currency_symbol;
                
                return '<div class="text-right">' . __('app.total') . ': ' . currency_position($row->total, $currencySymbol). '<br><span class="text-danger"> Deposit Request :</span> ' . currency_position($row->deposit_req, $currencySymbol) . '<br><span class="text-success">' . __('app.paid') . ':</span> ' . currency_position($row->amountPaid(), $currencySymbol)  . '<br><span class="text-danger">' . __('app.unpaid') . ':</span> ' . currency_position($row->amountDue(), $currencySymbol) . '<br><span class="text-danger"> Refund :</span> ' . currency_position($row->amountRefund(), $currencySymbol) . '</div>';

                //return '<div class="text-right">' . __('app.total') . ': ' . currency_position($row->total, $currencySymbol) . '<br><span class="text-success">' . __('app.paid') . ':</span> ' . currency_position($row->amountPaid(), $currencySymbol)  . '<br><span class="text-danger">' . __('app.unpaid') . ':</span> ' . currency_position($row->amountDue(), $currencySymbol) . '</div>';
            })
            ->editColumn(
                'issue_date',
                function ($row) {
                    return $row->issue_date->timezone($this->global->timezone)->format($this->global->date_format);
                }
            )
            ->editColumn('invoice_number', function($row) use($invoiceSettings) {
                   $string = $row->invoice_number;
                   return $string;
                }
            )
            ->addIndexColumn()
           ->rawColumns(['name', 'project_name', 'action', 'status', 'invoice_number', 'total'])
            ->removeColumn('currency_symbol')
             ->removeColumn('currency_code')
            ->removeColumn('project_id')
            ->make(true);
    }
}
