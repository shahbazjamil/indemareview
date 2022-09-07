<?php

namespace App\Http\Controllers;

use App\AuditTrail;
use App\EmailAutomation;
use App\Lead;
use App\User;
use Illuminate\Routing\Controller as BaseController;
use Carbon\Carbon;

class AuditController extends BaseController
{
    /**
     * @param $id
     */
    public function updateEmailStatus($id)
    {
        if (!empty($id)) {
            $auditTrail = AuditTrail::find($id);
            if ($auditTrail) {
                $companyId = $auditTrail->company_id;
                $emailAutomation = EmailAutomation::find($auditTrail->email_automation_id)->toArray();
                if (!$auditTrail->opens && $auditTrail->type == AuditTrail::LEAD) {
                    $lead = Lead::find($auditTrail->lead_id);
                    storeAuditTrail($emailAutomation, $companyId, $lead, AuditTrail::LEAD, Carbon::now(), AuditTrail::OPENED_STEP);
                }
                if (!$auditTrail->opens && $auditTrail->type == AuditTrail::CLIENT) {
                    $client = User::find($auditTrail->client_id);
                    storeAuditTrail($emailAutomation, $companyId, $client, AuditTrail::CLIENT, Carbon::now(), AuditTrail::OPENED_STEP);
                }
                $auditTrail->update([
                    'opens' => true
                ]);
            }
        }
    }
}
