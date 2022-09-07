<?php

namespace App\Http\Controllers\Admin;


use App\InvoiceItems;
use App\VendorInvoice;
use App\VendorInvoiceItem;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Helper\Reply;


class VendorInvoiceController extends AdminBaseController
{
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
//         dd($request->all());
        $items = $request->input('item_name');
        $itemsSummary = $request->input('item_summary');
        $cost_per_item = $request->input('cost_per_item');
        $quantity = $request->input('quantity');
        $amount = $request->input('amount');
        $tax = $request->input('taxes');

        foreach ($quantity as $qty) {
            if (!is_numeric($qty) && (intval($qty) < 1)) {
                return Reply::error(__('messages.quantityNumber'));
            }
        }

        foreach ($cost_per_item as $rate) {
            if (!is_numeric($rate)) {
                return Reply::error(__('messages.unitPriceNumber'));
            }
        }

        foreach ($amount as $amt) {
            if (!is_numeric($amt)) {
                return Reply::error(__('messages.amountNumber'));
            }
        }

        foreach ($items as $itm) {
            if (is_null($itm)) {
                return Reply::error(__('messages.itemBlank'));
            }
        }

        $vendorinvoice = new VendorInvoice();
//        $vendorinvoice->project_id = $request->project_id ?? null;
//        $vendorinvoice->client_id = $request->project_id == '' && $request->has('client_id') ? $request->client_id : null;
        $vendorinvoice->invoice_number = VendorInvoice::count() + 1;
        $vendorinvoice->issue_date = Carbon::createFromFormat($this->global->date_format, $request->issue_date)->format('Y-m-d');
        $vendorinvoice->due_date = Carbon::createFromFormat($this->global->date_format, $request->due_date)->format('Y-m-d');
        $vendorinvoice->sub_total = round($request->sub_total, 2);
//        $vendorinvoice->discount = round($request->discount_value, 2);
//        $vendorinvoice->discount_type = $request->discount_type;
        $vendorinvoice->total = round($request->total, 2);
        $vendorinvoice->currency_id = $request->currency_id;
        $vendorinvoice->recurring = $request->recurring_payment;
        $vendorinvoice->billing_frequency = $request->recurring_payment == 'yes' ? $request->billing_frequency : null;
        $vendorinvoice->billing_interval = $request->recurring_payment == 'yes' ? $request->billing_interval : null;
        $vendorinvoice->billing_cycle = $request->recurring_payment == 'yes' ? $request->billing_cycle : null;
        $vendorinvoice->note = $request->note;
//        $vendorinvoice->show_shipping_address = $request->show_shipping_address;
        $vendorinvoice->save();

//        if ($request->estimate_id) {
//            $estimate = Estimate::findOrFail($request->estimate_id);
//            $estimate->status = 'accepted';
//            $estimate->save();
//        }
        if ($request->proposal_id) {
            $proposal = Proposal::findOrFail($request->proposal_id);
            $proposal->invoice_convert = 1;
            $proposal->save();
        }


//        if ($request->has('shipping_address')) {
//            if ($vendorinvoice->project_id != null && $vendorinvoice->project_id != '') {
//                $client = $vendorinvoice->project->clientdetails;
//            } elseif ($vendorinvoice->client_id != null && $vendorinvoice->client_id != '') {
//                $client = $vendorinvoice->clientdetails;
//            }
//            $client->shipping_address = $request->shipping_address;
//
//            $client->save();
//        }

        //set milestone paid if converted milestone to invoice
//        if ($request->milestone_id != '') {
//            $milestone = ProjectMilestone::findOrFail($request->milestone_id);
//            $milestone->invoice_created = 1;
//            $milestone->invoice_id = $vendorinvoice->id;
//            $milestone->save();
//        }

        //log search
//        $this->logSearchEntry($invoice->id, 'Invoice ' . $invoice->invoice_number, 'admin.all-invoices.show', 'invoice');

        InvoiceItems::where('invoice_id', $vendorinvoice->id)->delete();

        foreach ($items as $key => $item) :
            VendorInvoiceItem::create(
                [
                    'invoice_id' => $vendorinvoice->id,
                    'item_name' => $item,
                    'item_summary' => $itemsSummary[$key],
                    'type' => 'item',
                    'quantity' => $quantity[$key],
                    'unit_price' => round($cost_per_item[$key], 2),
                    'amount' => round($amount[$key], 2),
                    'taxes' => $tax ? array_key_exists($key, $tax) ? json_encode($tax[$key]) : null : null
                ]
            );
        endforeach;
        return Reply::redirect(route('admin.all-invoices.index'), __('messages.invoiceCreated'));
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\VendorInvoice  $vendorInvoice
     * @return \Illuminate\Http\Response
     */
    public function show(VendorInvoice $vendorInvoice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\VendorInvoice  $vendorInvoice
     * @return \Illuminate\Http\Response
     */
    public function edit(VendorInvoice $vendorInvoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\VendorInvoice  $vendorInvoice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, VendorInvoice $vendorInvoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\VendorInvoice  $vendorInvoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(VendorInvoice $vendorInvoice)
    {
        //
    }
}
