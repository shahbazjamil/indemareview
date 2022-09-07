<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/verify-ipn',
        '/verify-webhook',
        '/save-invoices',
        '/save-razorpay-invoices',
        '/save-paystack-invoices',
        'admin/leads_google',
        'admin/tasks_google',
        'admin/task_form',
        'admin/type_form',
        'admin/trello_board',
        'admin/lead_dubsado',
    ];
}
