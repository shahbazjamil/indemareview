<div class="row d-flex" style="justify-content: center;">
    <div class="col-md-3 col-lg-3 col-xs-12">
        <div class="card step-2-card">
            <div class="card-body">
                <h4 class="step-2-card-header">ADD YOUR FIRST ACTION</h4>
                <div class="d-flex step-2-card-body-email-section select-email-option {{ isset($emailAutomation) ? $emailAutomation->email_type == 1 ? 'selected' : '' : '' }} next-2">
                    <h3 class="step-2-card-body-email-section-icon"><i class="icon-envelope fa-fw"></i> Send Email</h3>
                    <span class="text-muted step-2-card-body-email-section-text email-template-type" data-id="1">Send an email to your leads or clients automatically.</span>
                </div>
                <div class="d-flex step-2-card-body-email-section select-email-option {{ isset($emailAutomation) ? $emailAutomation->email_type == 2 ? 'selected' : '' : '' }} next-2">
                    <h3 class="step-2-card-body-email-section-icon"><i class="icon-docs fa-fw"></i> Send File via Email</h3>
                    <span class="text-muted step-2-card-body-email-section-text email-template-type" data-id="2">Showcase your services or send your leads questionnaires or information.</span>
                </div>
                <div class="d-flex step-2-card-body-email-section create-new-email-template">
                    <h3 class="step-2-card-body-email-section-icon"><i class="fa fa-list-alt fa-fw"></i> Create a Template</h3>
                    <span class="text-muted step-2-card-body-email-section-text">Create a template to use in your automation.</span>
                </div>
            </div>
        </div>
    </div>
</div>