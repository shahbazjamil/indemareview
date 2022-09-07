<?php

namespace App\Observers;


use App\PurchaseOrder;
use App\InvoiceItems;
use App\PurchaseOrderItems;
use App\Scopes\CompanyScope;
use App\UniversalSearch;
use App\User;
use App\Notifications\InvoicePaymentReceived;
use Illuminate\Support\Facades\Notification;

class PurchaseOrderObserver
{

    public function creating(PurchaseOrder $po)
    {
        $po->status = 'open';
    }

    public function saving(PurchaseOrder $po)
    {
        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if (company()) {
            $po->company_id = company()->id;
        }
    }

    public function created(PurchaseOrder $po)
    {
        if (!isRunningInConsoleOrSeeding()) {

            if (!empty(request()->item_name)) {

                $product_ids = request()->input('product_id');
                $itemsSummary = request()->input('item_summary');
                $cost_per_item = request()->input('cost_per_item');
                $quantity = request()->input('quantity');
                $amount = request()->input('amount');
                $tax = request()->input('taxes');

                foreach (request()->item_name as $key => $item) :
                    if (!is_null($item)) {
                        PurchaseOrderItems::create(
                            [
                                'purchase_order_id' => $po->id,
                                'item_name' => $item,
                                'item_summary' => $itemsSummary[$key] ? $itemsSummary[$key] : '',
                                'product_id' => $product_ids[$key],
                                'type' => 'item',
                                'quantity' => $quantity[$key],
                                'unit_price' => round($cost_per_item[$key], 2),
                                'amount' => round($amount[$key], 2),
                                'taxes' => $tax ? array_key_exists($key, $tax) ? json_encode($tax[$key]) : null : null
                            ]
                        );
                    }
                endforeach;
            }
            
        }
    }

    public function updated(PurchaseOrder $po)
    {
        if (!isRunningInConsoleOrSeeding()) {
            if ($po->isDirty('status') && in_array($po->status, ['paid', 'partial'])) {
                //$admins = User::allAdmins(); // SB commentted
                // SB added
                 $admins = User::join('role_user', 'role_user.user_id', '=', 'users.id')
                            ->join('roles', 'roles.id', '=', 'role_user.role_id')
                            ->select('users.id', 'users.name', 'users.email', 'users.created_at')
                            ->where('roles.name', 'admin')
                            ->where('roles.company_id', $po->company_id)
                            ->orderBy('users.id', 'asc')
                            ->first();
                 
                Notification::send($admins, new InvoicePaymentReceived($po));
            }
        }
    }

//    public function deleting(PurchaseOrder $invoice)
//    {
//        $universalSearches = UniversalSearch::where('searchable_id', $invoice->id)->where('module_type', 'invoice')->get();
//        if ($universalSearches) {
//            foreach ($universalSearches as $universalSearch) {
//                UniversalSearch::destroy($universalSearch->id);
//            }
//        }
//    }
}
