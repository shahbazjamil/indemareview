<div class="row d-flex">
    <div class="col-sm-10">
        <div class="row d-flex" style="justify-content: center;">
            <div class="col-sm-4">
                <div class="start-btn">
                    <button type="button" class="btn btn-primary" style="pointer-events: none;">
                        START
                    </button>
                </div>
                <span class="start-btn-arrow"></span>
                <div class="display-all-select-email-template-automation">
                    @if(isset($emailAutomation))
                        @foreach($emailAutomation->emailAutomations as $value)
                            <div class="card step-3-4-card edit-step-3-4-card-email-automation {{ $loop->first ? 'step-3-4-card-with-border selected' : 'step-3-4-card-without-border' }}" data-id="{{ $value->id }}">
                                <div class="card-body step-3-card-body">
                                    <div class="" style="margin-bottom: 10px;">
                                        <span class="text-muted select-email-template-card-email-template-event-name">
                                            @php
                                                $eventName = isset($value) && !empty($value->automation_event) ? $value->automation_event : '';
                                                $timePeriod = isset($value) && !empty($value->time_period) ? $value->time_period : '';
                                                $timeUnit = isset($value) && !empty($value->time_unit) ? $value->time_unit : '';
                                                $timeType = isset($value) && !empty($value->time_type) ? $value->time_type : '';
                                                if(empty($value->time_period) || $value->time_period == 0){
                                                    $timePeriod = 'Immediately ';
                                                    $timeUnit = '';
                                                }else{
                                                     $timePeriod = 'Wait '. $timePeriod;
                                                }
                                            @endphp
                                            {{ $timePeriod.' '.$timeUnit.' '.$timeType.' '.\App\EmailAutomation::AUTOMATION_EVENTS[$eventName] }}
                                        </span>
                                    </div>
                                    <div class="step-3-4-card-email-div select-email-template-card-email-div">
                                        @if(isset($value) && !empty($value->email_type) && $value->email_type == '1')
                                            <i class="icon-envelope fa-fw step-3-4-card-email-div-icon"></i>
                                            <h3 class="step-3-4-card-email-div-text" data-id="1"> Send an Email</h3>
                                        @else
                                            <i class="icon-docs fa-fw step-3-4-card-email-div-icon"></i>
                                            <h3 class="step-3-4-card-email-div-text" data-id="2"> Send File via Email</h3>
                                        @endif
                                    </div>
                                    <div class="">
                                        <span class="text-muted select-email-template-card-email-template-name">{{ isset($value) && !empty($value->email_template_id) ? $emailTemplatesNames[$value->email_template_id] : 'Choose template on the right' }}</span>
                                    </div>
                                    <input type="hidden" name="edit_automation_id[]" value="{{ isset($value) && !empty($value->id) ? $value->id : '' }}">
                                    <input type="hidden" class="store-edit-action-type" name="edit_action_type[]" value="{{ isset($value) && !empty($value->email_type) ? $value->email_type : '' }}">
                                    <input type="hidden" class="store-edit-email-template-id" name="edit_email_template_id[]" value="{{ isset($value) && !empty($value->email_template_id) ? $value->email_template_id : '' }}">
                                    <input type="hidden" class="store-edit-time-period" name="edit_time_period[]" value="{{ isset($value) && !empty($value->time_period) ? $value->time_period : '' }}">
                                    <input type="hidden" class="store-edit-time-unit" name="edit_time_unit[]" value="{{ isset($value) && !empty($value->time_unit) ? $value->time_unit : '' }}">
                                    <input type="hidden" class="store-edit-time-type" name="edit_time_type[]" value="{{ isset($value) && !empty($value->time_type) ? $value->time_type : '' }}">
                                    <input type="hidden" class="store-edit-automation-event" name="edit_automation_event[]" value="{{ isset($value) && !empty($value->automation_event) ? $value->automation_event : '' }}">
                                    <input type="hidden" class="store-edit-client-id" name="edit_client_id[]" value="{{ isset($value) && !empty($value->client_id) ? $value->client_id : '' }}">
                                    <input type="hidden" class="store-edit-project-id" name="edit_project_id[]" value="{{ isset($value) && !empty($value->project_id) ? $value->project_id : '' }}">
                                    <input type="hidden" class="store-edit-is-manual" name="edit_is_manual[]" value="{{ isset($value) && !empty($value->is_manual) ? ($value->is_manual == \App\EmailAutomation::IS_MANUAL ? 1 : 0): 0 }}">

                                    <input type="hidden" value="{{ isset($value) && !empty($value->id) ? $value->id : '' }}" class="email-template-card-validation">
                                </div>
                                <div class="edit-delete-automation-email-card" data-id="{{ isset($value) && !empty($value->id) ? $value->id : '' }}">
                                    <i class="fa fa-trash fa-fw" style="color: rgb(255, 97, 97);"></i>
                                </div>
                            </div>
                            <div class="checkbox-automation-email-card {{ isset($value) && !empty($value->is_manual) && !empty($value->client_id) && !empty($value->project_id) ?  '' : 'display-none' }}">
                                <label class="mt-checkbox mt-checkbox-outline checkbox-automation-email-card-label">
                                    <input {{ isset($value) && !empty($value->is_manual) ? ($value->is_manual == \App\EmailAutomation::IS_MANUAL ? 'checked' : '') : '' }} class="get-store-edit-is-manual m-r-5" type="checkbox" value="{{ isset($value) && !empty($value->is_manual)  ? ($value->is_manual == \App\EmailAutomation::IS_MANUAL ? 1 : 0) : 0 }}">
                                    <span class="checkbox-label">Send Manually</span>
                                </label>
                            </div>
                            <span class="{{ $loop->last ? '' : 'plus-btn-arrow' }}"></span>
                        @endforeach
                    @else
                        <div class="card step-3-4-card-with-border step-3-4-card selected">
                            <div class="card-body">
                                <div class="" style="margin-bottom: 10px;">
                                    <span class="text-muted select-email-template-card-email-template-event-name">Immediately after automation is activated..</span>
                                </div>
                                <div class="step-3-4-card-email-div select-email-template-card-email-div">
                                    <i class="icon-envelope fa-fw step-3-4-card-email-div-icon"></i>
                                    <h3 class="step-3-4-card-email-div-text" data-id="1"> Send an Email</h3>
                                </div>
                                <div class="">
                                    <span class="text-muted select-email-template-card-email-template-name">Choose template on the right</span>
                                </div>
                                <input type="hidden" class="store-edit-action-type" name="action_type[]">
                                <input type="hidden" class="store-edit-email-template-id" name="email_template_id[]">
                                <input type="hidden" class="store-edit-time-period" name="time_period[]">
                                <input type="hidden" class="store-edit-time-unit" name="time_unit[]">
                                <input type="hidden" class="store-edit-time-type" name="time_type[]">
                                <input type="hidden" class="store-edit-automation-event" name="automation_event[]">
                                <input type="hidden" class="store-edit-client-id" name="client_id[]">
                                <input type="hidden" class="store-edit-project-id" name="project_id[]">
                                <input type="hidden" class="store-edit-is-manual" name="is_manual[]" value="0">

                                <input type="hidden" value="" class="email-template-card-validation">
                            </div>
                            <div class="delete-automation-email-card">
                                <i class="fa fa-trash fa-fw" style="color: rgb(255, 97, 97);"></i>
                            </div>
                        </div>
                        <div class="checkbox-automation-email-card display-none">
                            <label class="mt-checkbox mt-checkbox-outline checkbox-automation-email-card-label">
                                <input class="get-store-edit-is-manual m-r-5" type="checkbox" value="0">
                                <span class="checkbox-label">Send Manually</span>
                            </label>
                        </div>
                    @endif
                </div>
                <div class="display-all-automation-card">

                </div>
                <span class="plus-btn-arrow first-automation-card-plus-btn"></span>
                <div class="plus-btn first-automation-card-plus-btn">
                    <span class="plus-btn-span add-new-email-automation-card">+</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Select Email Template --}}
    <div class="col-sm-2 email-sidebar-section step-3-div-email-sidebar-section {{ isset($emailAutomation) ? 'display-none' : '' }}">
        <div class="">
            <div class="d-flex email-sidebar-section-header">
                <i class="fa fa-arrow-left {{ isset($emailAutomation) ? '' : 'previous-3' }}" style="cursor: pointer;"></i>
                <span class="email-sidebar-section-header-text">Email Template</span>
            </div>
            <div class="list-of-templates" style="max-height: 530px;overflow-y: auto;">
                @foreach($emailTemplates as $emailTemplate)
                    <div class="email-sidebar-section__list-template {{ isset($emailAutomation) ? $emailAutomation->email_template_id == $emailTemplate->id ? 'selected' : '' : '' }} next-4">
                         <span class="email-sidebar-section__list-template-name list-email-template-name" data-id="{{ $emailTemplate->id }}">
                            {{ $emailTemplate->template_name }}
                         </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    {{-- End Select Email Template --}}

    {{-- Email Template Action Part --}}
    <div class="col-sm-2 email-sidebar-section step-4-div-email-sidebar-section {{ isset($emailAutomation) ? '' : 'display-none' }}">
        <div class="">
            <div class="d-flex email-sidebar-section-header" style="justify-content: center !important;">
                <span style="font-size: 20px;font-weight: bold;">Action</span>
            </div>
            <div class="email-sidebar-section__dropdown">
                <div class="email-sidebar-section__dropdown-div">
                    <span class="email-sidebar-section__dropdown-div-span">Action type</span>
                    <select class="form-control action-type-dropdown">
                        <option value="1">Send Email</option>
                        <option value="2">Send email with file</option>
                    </select>
                </div>
            </div>
            <div class="email-sidebar-section__dropdown">
                <div class="email-sidebar-section__dropdown-div">
                    <div class="d-flex" style="justify-content: space-between">
                        <span class="email-sidebar-section__dropdown-div-span">Email template</span>
                        <a style="color: rgb(89, 126, 255) !important; font-size: 14px;font-weight: 600;" href="javascript:;" id="emailPreviewBtn">Preview & edit</a>
                    </div>
                    <select class="form-control email-preview_email-template">
                        @foreach($emailTemplates as $emailTemplate)
                            <option value="{{ $emailTemplate->id }}">{{ $emailTemplate->template_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="email-sidebar-section__dropdown">
                <div class="email-sidebar-section__dropdown-div">
                    <div class="d-flex">
                        <span class="email-sidebar-section__dropdown-div-span">@lang('modules.projects.selectClient')</span>
                    </div>
                    <select class="form-control client-dropdown" data-style="form-control">
                        <option value="">@lang('modules.projects.selectClient')</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ ucwords($client->name) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="email-sidebar-section__dropdown">
                <div class="email-sidebar-section__dropdown-div">
                    <div class="d-flex">
                        <span class="email-sidebar-section__dropdown-div-span">Select Project</span>
                    </div>
                    <select class="form-control project-dropdown" data-style="form-control">
                        <option value="">Select Project</option>
                    </select>
                </div>
            </div>
            <div class="email-sidebar-section__dropdown">
                <div class="email-sidebar-section__dropdown-div">
                    <span class="email-sidebar-section__dropdown-div-span" style="margin-bottom: 15px;">When</span>
                    <div class="row">
                        <div class="col-sm-3" style="padding-right: 0;">
                            <input type="text" value="0" class="form-control automation-time-period">
                        </div>
                        <div class="col-sm-5">
                            <select class="form-control automation-time-unit">
                                <option value="minute">minutes</option>
                                <option value="hour">hours</option>
                                <option value="day">days</option>
                                <option value="week">weeks</option>
                            </select>
                        </div>
                        <div class="col-sm-4" style="padding-left: 0;">
                            <select class="form-control automation-time-type">
                                <option value="after">after</option>
                                <option value="before">before</option>
                            </select>
                        </div>
                    </div>
                    <div class="" style="margin-top: 5px;margin-bottom: 20px;">
                        <select class="form-control automation-event-name">
                            @foreach(\App\EmailAutomation::AUTOMATION_EVENTS as $key => $automationEvent)
                                <option value="{{ $key }}">{{ $automationEvent }}</option>
                            @endforeach
                        </select>
                    </div>
                    <span style="text-align: start;display: flex;">Want to trigger an action immediately after something else occurs? Set it's timing to 0 minutes, days, etc.</span>
                </div>
            </div>
        </div>
    </div>
    {{-- End Email Template Action Part --}}
</div>
