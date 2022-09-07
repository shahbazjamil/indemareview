<?php

namespace App;

use App\Observers\InvoiceObserver;
use App\Scopes\CompanyScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Invoice extends BaseModel
{
    use Notifiable;
    protected $fillable = ['vendor_id','invoice_type'];

    protected $dates = ['issue_date', 'due_date'];
    protected $appends = ['total_amount', 'issue_on', 'invoice_number', 'original_invoice_number'];

    protected static function boot()
    {
        parent::boot();

        static::observe(InvoiceObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

    public function project(){
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id')->withoutGlobalScopes(['active']);
    }
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }
    public function withoutGlobalScopeCompanyClient()
    {
        return $this->belongsTo(User::class, 'client_id')->withoutGlobalScopes([CompanyScope::class, 'active']);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function clientdetails()
    {
        return $this->belongsTo(ClientDetails::class, 'client_id', 'user_id');
    }

    public function credit_notes()
    {
        return $this->belongsToMany(CreditNotes::class)->withPivot('id', 'date', 'credit_amount')->withTimestamps();
    }

    public function items(){
        return $this->hasMany(InvoiceItems::class, 'invoice_id');
    }

    public function payment(){
        return $this->hasMany(Payment::class, 'invoice_id')->where('status', 'complete')->where('payment_type', 'paid')->orderBy('paid_on', 'desc');
    }
    
    public function paymentRefund(){
        return $this->hasMany(Payment::class, 'invoice_id')->where('status', 'complete')->where('payment_type', 'refund')->orderBy('paid_on', 'desc');
    }

    public function currency() {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function offline_invoice_payment() {
        return $this->hasMany(OfflineInvoicePayment::class, 'invoice_id');
    }
    public function approved_offline_invoice_payment() {
        return $this->hasOne(OfflineInvoicePayment::class, 'invoice_id')->where('status', 'approve');
    }

    public static function clientInvoices($clientId) {
        return Invoice::join('projects', 'projects.id', '=', 'invoices.project_id')
            ->select('projects.project_name', 'invoices.*')
            ->where('projects.client_id', $clientId)
            ->get();
    }

    public function appliedCredits()
    {
        return $this->credit_notes()->sum('credit_amount');
    }

    public function amountDue()
    {
        $due = $this->total - (number_format((float)$this->amountPaid(), 2, '.', ''));
        if ($due < 0) {
            return 0;
        }
        return $due;
    }

    public function amountPaid()
    {
        return $this->payment->sum('amount') + $this->credit_notes()->sum('credit_amount');
    }
    public function amountRefund()
    {
        return $this->paymentRefund->sum('amount');
    }

    public function getPaidAmount(){
        return Payment::where('invoice_id', $this->id)->where('payment_type', 'paid')->sum('amount');
    }

    public function getTotalAmountAttribute(){

        if(!is_null($this->total) && !is_null($this->currency_symbol)){
            return $this->currency_symbol . $this->total;
        }

        return "";
    }

    public function getIssueOnAttribute(){
        if(!is_null($this->issue_date)){
            return Carbon::parse($this->issue_date)->format('d F, Y');
        }
        return "";
    }

    public function getOriginalInvoiceNumberAttribute(){
        $invoiceSettings = InvoiceSetting::select('invoice_digit')->first();
        $zero = '';
        if (strlen($this->attributes['invoice_number']) < $invoiceSettings->invoice_digit){
            for ($i=0; $i<$invoiceSettings->invoice_digit-strlen($this->attributes['invoice_number']); $i++){
                $zero = '0'.$zero;
            }
        }
        $zero = '#'.$zero.$this->attributes['invoice_number'];
        return $zero;
    }

    public function getInvoiceNumberAttribute($value){
        if(!is_null($value)){
            $invoiceSettings = InvoiceSetting::select('invoice_prefix', 'invoice_digit')->first();
            $zero = '';
            if (strlen($value) < $invoiceSettings->invoice_digit){
                for ($i=0; $i<$invoiceSettings->invoice_digit-strlen($value); $i++){
                    $zero = '0'.$zero;
                }
            }
            $zero = $invoiceSettings->invoice_prefix.'#'.$zero.$value;
            return $zero;
        }
        return "";
    }
}
