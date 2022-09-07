<?php

namespace App\Jobs;

use App\AuditTrail;
use App\EmailConfiguration;
use App\Mail\AutomationStepNeedAttention;
use App\Mail\ClientCreated;
use App\Mail\LastStepAutomation;
use App\Mail\LeadCreated;
use App\Mail\PaymentReceived;
use App\Mail\ProjectEndDate;
use App\Mail\ProjectStartDate;
use App\Mail\TestEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $email;
    public $emailAutomation;
    public $companyId;
    private $type;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->email = $data['email'];
        $this->emailAutomation = $data['emailAutomation'];
        $this->companyId = $data['companyId'];
        $this->type = $data['type'];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $configuration = EmailConfiguration::where('company_id', $this->companyId)->first();
        $settings = global_settings();
        if ($configuration) {
            $config = array(
                'driver' => $configuration->driver,
                'host' => $configuration->host,
                'port' => $configuration->port,
                'username' => $configuration->user_name,
                'password' => $configuration->password,
                'encryption' => $configuration->encryption,
                'from' => array('address' => $configuration->sender_email, 'name' => $configuration->sender_name),
                'markdown' => [
                    'theme' => 'default',
                    'default' => 'markdown',
                    'paths' => [resource_path('views/vendor/mail')]
                ]
            );

            if ($settings) {
                Config::set('app.logo', $settings->logo_url);
            }

            Config::set('mail', $config);
        }

        switch ($this->type) {
            case 'lead':
                Mail::to($this->email)->send(new LeadCreated($this->emailAutomation));
                break;

            case 'client':
                Mail::to($this->email)->send(new ClientCreated($this->emailAutomation));
                break;

            case 'project_start':
                Mail::to($this->email)->send(new ProjectStartDate($this->emailAutomation));
                break;

            case 'project_end':
                Mail::to($this->email)->send(new ProjectEndDate($this->emailAutomation));
                break;

            case 'last_step':
                Mail::to($this->email)->send(new LastStepAutomation($this->emailAutomation));
                break;

            case 'payment_received':
                Mail::to($this->email)->send(new PaymentReceived($this->emailAutomation));
                break;

            case 'step_attention':
                Mail::to($this->email)->send(new AutomationStepNeedAttention($this->emailAutomation));
                break;

            case 'test_mail':
                Mail::to($this->email)->send(new TestEmail());
                break;
        }

    }

    /**
     * @param \Exception $exception
     */
    public function failed(\Exception $exception)
    {
        $auditTrailId = isset($this->emailAutomation['audit_id']) && !empty($this->emailAutomation['audit_id']) ? $this->emailAutomation['audit_id'] : null;
        if ($auditTrailId && !is_null($auditTrailId)){
            $auditTrail = AuditTrail::find($auditTrailId);
            $title = AuditTrail::TITLE[AuditTrail::ERROR_MESSAGE];
            $auditTrail->update([
                'title' => $title,
                'icon' => 'exclamation-triangle orange',
            ]);
        }
    }
}
