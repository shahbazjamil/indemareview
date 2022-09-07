<?php

namespace App\Observers;

use App\ClientPayment;
use App\Invoice;
use App\Notifications\InvoicePaymentReceived;
use App\User;
use Illuminate\Support\Facades\Notification;

class InvoicePaymentReceivedObserver
{
    public function created(ClientPayment $payment)
    {
        try{
            if (!isRunningInConsoleOrSeeding()) {
                //$admins = User::allAdmins();
                $invoice = Invoice::findOrFail($payment->invoice_id);
                if($invoice){
                    //Notification::send($admins, new InvoicePaymentReceived($invoice));
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
        }catch (\Exception $e){

        }
    }
}
