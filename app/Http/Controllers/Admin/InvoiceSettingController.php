<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Requests\UpdateInvoiceSetting;
use App\InvoiceSetting;
use App\Company;

class InvoiceSettingController extends AdminBaseController
{
    public function __construct() {
        parent::__construct();
        $this->pageTitle = 'app.menu.financeSettings';
        $this->pageIcon = 'icon-gear';
    }

    public function index() {
        
        $company = Company::findOrFail(company()->id);
        $this->company = $company;
        $this->invoiceSetting = InvoiceSetting::first();
        
        
        return view('admin.invoice-settings.edit', $this->data);
    }

    public function update(UpdateInvoiceSetting $request) {
        
        
        $setting = InvoiceSetting::first();
        $setting->invoice_prefix = $request->invoice_prefix;
        $setting->invoice_digit = $request->invoice_digit;
        $setting->estimate_prefix = $request->estimate_prefix;
        $setting->estimate_digit = $request->estimate_digit;
        $setting->credit_note_prefix = $request->credit_note_prefix;
        $setting->credit_note_digit = $request->credit_note_digit;
        $setting->template       = $request->template;
        $setting->due_after      = $request->due_after;
        $setting->invoice_terms  = $request->invoice_terms;
        $setting->gst_number     = $request->gst_number;
        $setting->show_gst       = $request->has('show_gst') ? 'yes' : 'no';
        
        $setting->vat_number     = $request->vat_number;
        $setting->show_vat       = $request->has('show_vat') ? 'yes' : 'no';
        
        $setting->line_item_approval       = $request->has('line_item_approval') ? 'yes' : 'no';
        $setting->hide_amount_per_hour       = $request->has('hide_amount_per_hour') ? 'yes' : 'no';
        $setting->hide_sale_cost       = $request->has('hide_sale_cost') ? 'yes' : 'no';
        $setting->hide_company_address       = $request->has('hide_company_address') ? 'yes' : 'no'; // hide compnay address in product PDF.
        $setting->hide_product_footer       = $request->has('hide_product_footer') ? 'yes' : 'no'; // hide footer product PDF.
        $setting->hide_signature_pdf       = $request->has('hide_signature_pdf') ? 'yes' : 'no'; // hide footer product PDF.
        $setting->shipping_taxed       = $request->has('shipping_taxed') ? 'yes' : 'no'; // shipping tax or not on estomates and invoices
        $setting->estimate_to_invoice       = $request->has('estimate_to_invoice') ? 'yes' : 'no'; // ability to turn off the estimate turning into invoice automatically
        
        $company = Company::findOrFail(company()->id);
        $company->is_finance =  $request->has('is_finance') ? 1 : 0;
        $company->save();
        
        if ($request->hasFile('logo')) {
            Files::deleteFile($setting->logo,'app-logo');
            $setting->logo = Files::upload($request->logo, 'app-logo');
        }
        $setting->save();

        
        session()->forget('invoice_setting');

        return Reply::success(__('messages.settingsUpdated'));
    }
}
