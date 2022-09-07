<?php

namespace App\Observers;


use App\Invoice;
use App\InvoiceItems;
use App\Notifications\InvoicePaymentReceived;
use App\Notifications\NewInvoice;
use App\Scopes\CompanyScope;
use App\UniversalSearch;
use App\User;
use Illuminate\Support\Facades\Notification;

class InvoiceObserver
{

    public function creating(Invoice $invoice)
    {
        if (request()->type && request()->type == "send") {
            $invoice->send_status = 1;
        } else {
            $invoice->send_status = 0;
        }

        if (request()->type && request()->type == "draft") {
            $invoice->status = 'draft';
        }
    }

    public function saving(Invoice $invoice)
    {
        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if (company()) {
            $invoice->company_id = company()->id;
        }
    }

    public function created(Invoice $invoice)
    {
        if (!isRunningInConsoleOrSeeding()) {

            // if (!empty(request()->item_name)) {

            //     $itemsSummary = request()->input('item_summary');
            //     $cost_per_item = request()->input('cost_per_item');
            //     $quantity = request()->input('quantity');
            //     $amount = request()->input('amount');
            //     $tax = request()->input('taxes');

            //     foreach (request()->item_name as $key => $item) :
            //         if (!is_null($item)) {
            //             InvoiceItems::create(
            //                 [
            //                     'invoice_id' => $invoice->id,
            //                     'item_name' => $item,
            //                     'item_summary' => $itemsSummary[$key] ? $itemsSummary[$key] : '',
            //                     'type' => 'item',
            //                     'quantity' => $quantity[$key],
            //                     'unit_price' => round($cost_per_item[$key], 2),
            //                     'amount' => round($amount[$key], 2),
            //                     'taxes' => $tax ? array_key_exists($key, $tax) ? json_encode($tax[$key]) : null : null
            //                 ]
            //             );
            //         }
            //     endforeach;
            // }


            if (request()->type && request()->type == "send") {

                if (($invoice->project && $invoice->project->client_id != null) || $invoice->client_id != null) {
                    $clientId = ($invoice->project && $invoice->project->client_id != null) ? $invoice->project->client_id : $invoice->client_id;
                    // Notify client
                    $notifyUser = User::withoutGlobalScopes([CompanyScope::class, 'active'])->findOrFail($clientId);

                    $notifyUser->notify(new NewInvoice($invoice));
                }
            }
        }
    }

    public function updated(Invoice $invoice)
    {
        if (!isRunningInConsoleOrSeeding()) {
            if ($invoice->isDirty('status') && in_array($invoice->status, ['paid', 'partial'])) {
                //$admins = User::allAdmins(); // SB commentted
                // SB added 
                 $admins = User::join('role_user', 'role_user.user_id', '=', 'users.id')
                            ->join('roles', 'roles.id', '=', 'role_user.role_id')
                            ->select('users.id', 'users.name', 'users.email', 'users.created_at')
                            ->where('roles.name', 'admin')
                            ->where('roles.company_id', $invoice->company_id)
                            ->orderBy('users.id', 'asc')
                            ->first();
                
                Notification::send($admins, new InvoicePaymentReceived($invoice));
            }
        }
    }

    public function deleting(Invoice $invoice)
    {
        $universalSearches = UniversalSearch::where('searchable_id', $invoice->id)->where('module_type', 'invoice')->get();
        if ($universalSearches) {
            foreach ($universalSearches as $universalSearch) {
                UniversalSearch::destroy($universalSearch->id);
            }
        }
    }
}
