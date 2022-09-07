<?php

namespace App\Queries;

use App\EmailTemplate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Class EmailTemplateDataTable
 */
class EmailTemplateDataTable
{
    /**
     * @return Builder
     */
    public function get(): Builder
    {
        $company = company();
        $companyId = !empty($company) ? $company->id : Auth::user()->company->id;

        return EmailTemplate::where('company_id', $companyId)->select('email_templates.*');
    }
}
