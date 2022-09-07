<div class="row d-flex">
    <div class="col-sm-10">
        <div class="row d-flex" style="justify-content: center;">
            <div class="col-sm-4">
                <div class="start-btn">
                    <div class="btn btn-primary">
                        START
                    </div>
                </div>
                <span class="start-btn-arrow"></span>
                <div class="display-all-select-email-template-automation">
                    <div class="card step-3-4-card-with-border step-3-4-card selected">
                        <div class="card-body step-3-card-body">
                            <div class="" style="margin-bottom: 10px;">
                                <span class="text-muted">Immediately after automation is activated...</span>
                            </div>
                            <div class="step-3-4-card-email-div select-email-template-card-email-div">
                                <i class="icon-envelope fa-fw step-3-4-card-email-div-icon"></i>
                                <h3 class="step-3-4-card-email-div-text" data-id="1"> Send an Email</h3>
                            </div>
                            <div class="">
                                <span class="text-muted">Choose template on the right</span>
                            </div>
                            <input type="hidden" class="store-edit-action-type" name="action_type[]">
                            <input type="hidden" class="store-edit-email-template-id" name="email_template_id[]">
                            <input type="hidden" class="store-edit-time-period" name="time_period[]">
                            <input type="hidden" class="store-edit-time-unit" name="time_unit[]">
                            <input type="hidden" class="store-edit-time-type" name="time_type[]">
                            <input type="hidden" class="store-edit-automation-event" name="automation_event[]">
                        </div>
                    </div>
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
    <div class="col-sm-2 email-sidebar-section">
        <div class="">
            <div class="d-flex email-sidebar-section-header">
                <i class="fa fa-arrow-left previous-3" style="cursor: pointer;"></i>
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
</div>


{{--<div class="row d-flex">--}}
{{--    <div class="col-sm-10">--}}
{{--        <div class="row d-flex" style="justify-content: center;">--}}
{{--            <div class="col-sm-4">--}}
{{--                <div class="start-btn">--}}
{{--                    <button type="button" class="btn btn-primary">--}}
{{--                        START--}}
{{--                    </button>--}}
{{--                </div>--}}
{{--                <span class="start-btn-arrow"></span>--}}
{{--                <div class="display-all-select-email-template-automation">--}}
{{--                    @if(isset($emailAutomation))--}}
{{--                        @foreach($emailAutomation->emailAutomations as $value)--}}
{{--                            <div class="card step-3-4-card {{ $loop->first ? 'step-3-4-card-with-border selected' : 'step-3-4-card-without-border' }}">--}}
{{--                                <div class="card-body step-3-card-body">--}}
{{--                                    <div class="" style="margin-bottom: 10px;">--}}
{{--                                        <span class="text-muted select-email-template-card-email-template-event-name">Immediately after automation is activated..</span>--}}
{{--                                    </div>--}}
{{--                                    <div class="step-3-4-card-email-div select-email-template-card-email-div">--}}
{{--                                        @if(isset($value) && !empty($value->email_type) && $value->email_type == '1')--}}
{{--                                            <i class="icon-envelope fa-fw step-3-4-card-email-div-icon"></i>--}}
{{--                                            <h3 class="step-3-4-card-email-div-text" data-id="1"> Send an Email</h3>--}}
{{--                                        @else--}}
{{--                                            <i class="icon-docs fa-fw step-3-4-card-email-div-icon"></i>--}}
{{--                                            <h3 class="step-3-4-card-email-div-text" data-id="2"> Send File via Email</h3>--}}
{{--                                        @endif--}}
{{--                                    </div>--}}
{{--                                    <div class="">--}}
{{--                                        <span class="text-muted select-email-template-card-email-template-name">Choose template on the right</span>--}}
{{--                                    </div>--}}
{{--                                    <input type="hidden" class="store-edit-action-type" name="action_type[]" value="{{ isset($value) && !empty($value->email_type) ? $value->email_type : '' }}">--}}
{{--                                    <input type="hidden" class="store-edit-email-template-id" name="email_template_id[]" value="{{ isset($value) && !empty($value->email_template_id) ? $value->email_template_id : '' }}">--}}
{{--                                    <input type="hidden" class="store-edit-time-period" name="time_period[]" value="{{ isset($value) && !empty($value->time_period) ? $value->time_period : '' }}">--}}
{{--                                    <input type="hidden" class="store-edit-time-unit" name="time_unit[]" value="{{ isset($value) && !empty($value->time_unit) ? $value->time_unit : '' }}">--}}
{{--                                    <input type="hidden" class="store-edit-time-type" name="time_type[]" value="{{ isset($value) && !empty($value->time_type) ? $value->time_type : '' }}">--}}
{{--                                    <input type="hidden" class="store-edit-automation-event" name="automation_event[]" value="{{ isset($value) && !empty($value->automation_event) ? $value->automation_event : '' }}">--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <span class="{{ $loop->last ? '' : 'plus-btn-arrow' }}"></span>--}}
{{--                        @endforeach--}}
{{--                    @else--}}
{{--                        <div class="card step-3-4-card-with-border step-3-4-card selected">--}}
{{--                            <div class="card-body">--}}
{{--                                <div class="" style="margin-bottom: 10px;">--}}
{{--                                    <span class="text-muted select-email-template-card-email-template-event-name">Immediately after automation is activated..</span>--}}
{{--                                </div>--}}
{{--                                <div class="step-3-4-card-email-div select-email-template-card-email-div">--}}
{{--                                    <i class="icon-envelope fa-fw step-3-4-card-email-div-icon"></i>--}}
{{--                                    <h3 class="step-3-4-card-email-div-text" data-id="1"> Send an Email</h3>--}}
{{--                                </div>--}}
{{--                                <div class="">--}}
{{--                                    <span class="text-muted select-email-template-card-email-template-name">Choose template on the right</span>--}}
{{--                                </div>--}}
{{--                                <input type="hidden" class="store-edit-action-type" name="action_type[]">--}}
{{--                                <input type="hidden" class="store-edit-email-template-id" name="email_template_id[]">--}}
{{--                                <input type="hidden" class="store-edit-time-period" name="time_period[]">--}}
{{--                                <input type="hidden" class="store-edit-time-unit" name="time_unit[]">--}}
{{--                                <input type="hidden" class="store-edit-time-type" name="time_type[]">--}}
{{--                                <input type="hidden" class="store-edit-automation-event" name="automation_event[]">--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    @endif--}}
{{--                </div>--}}
{{--                <div class="display-all-automation-card">--}}

{{--                </div>--}}
{{--                <span class="plus-btn-arrow first-automation-card-plus-btn"></span>--}}
{{--                <div class="plus-btn first-automation-card-plus-btn">--}}
{{--                    <span class="plus-btn-span add-new-email-automation-card">+</span>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}

{{--    --}}{{-- Select Email Template --}}
{{--    <div class="col-sm-2 email-sidebar-section step-3-div-email-sidebar-section">--}}
{{--        <div class="">--}}
{{--            <div class="d-flex email-sidebar-section-header">--}}
{{--                <i class="fa fa-arrow-left previous-3" style="cursor: pointer;"></i>--}}
{{--                <span class="email-sidebar-section-header-text">Email Template</span>--}}
{{--            </div>--}}
{{--            <div class="list-of-templates" style="max-height: 530px;overflow-y: auto;">--}}
{{--                @foreach($emailTemplates as $emailTemplate)--}}
{{--                    <div class="email-sidebar-section__list-template {{ isset($emailAutomation) ? $emailAutomation->email_template_id == $emailTemplate->id ? 'selected' : '' : '' }} next-4">--}}
{{--                         <span class="email-sidebar-section__list-template-name list-email-template-name" data-id="{{ $emailTemplate->id }}">--}}
{{--                            {{ $emailTemplate->template_name }}--}}
{{--                         </span>--}}
{{--                    </div>--}}
{{--                @endforeach--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--    --}}{{-- End Select Email Template --}}

{{--    --}}{{-- Email Template Action Part --}}
{{--    <div class="col-sm-2 email-sidebar-section step-4-div-email-sidebar-section display-none">--}}
{{--        <div class="">--}}
{{--            <div class="d-flex email-sidebar-section-header">--}}
{{--                <i class="fa fa-times"></i>--}}
{{--                <span class="email-sidebar-section-header-text">Action</span>--}}
{{--            </div>--}}
{{--            <div class="email-sidebar-section__dropdown">--}}
{{--                <div class="email-sidebar-section__dropdown-div">--}}
{{--                    <span class="email-sidebar-section__dropdown-div-span">Action type</span>--}}
{{--                    <select class="form-control action-type-dropdown">--}}
{{--                        <option value="1">Send Email</option>--}}
{{--                        <option value="2">Send email with file</option>--}}
{{--                    </select>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="email-sidebar-section__dropdown">--}}
{{--                <div class="email-sidebar-section__dropdown-div">--}}
{{--                    <div class="d-flex" style="justify-content: space-between">--}}
{{--                        <span class="email-sidebar-section__dropdown-div-span">Email template</span>--}}
{{--                        <a style="color: rgb(89, 126, 255) !important; font-size: 14px;font-weight: 600;" href="javascript:;" id="emailPreviewBtn">Preview & edit</a>--}}
{{--                    </div>--}}
{{--                    <select class="form-control email-preview_email-template">--}}
{{--                        @foreach($emailTemplates as $emailTemplate)--}}
{{--                            <option value="{{ $emailTemplate->id }}" {{ isset($emailAutomation) ? $emailAutomation->email_template_id == $emailTemplate->id ? 'selected' : '' : '' }}>{{ $emailTemplate->template_name }}</option>--}}
{{--                        @endforeach--}}
{{--                    </select>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="email-sidebar-section__dropdown">--}}
{{--                <div class="email-sidebar-section__dropdown-div">--}}
{{--                    <span class="email-sidebar-section__dropdown-div-span" style="margin-bottom: 15px;">When</span>--}}
{{--                    <div class="row">--}}
{{--                        <div class="col-sm-3" style="padding-right: 0;">--}}
{{--                            <input type="text" value="{{ isset($emailAutomation) ? $emailAutomation->time_period  : '0' }}" class="form-control automation-time-period">--}}
{{--                        </div>--}}
{{--                        <div class="col-sm-5">--}}
{{--                            <select class="form-control automation-time-unit">--}}
{{--                                <option value="minute" {{ isset($emailAutomation) ? $emailAutomation->time_unit == 'minute' ? 'selected' : '' : '' }}>minutes</option>--}}
{{--                                <option value="hour" {{ isset($emailAutomation) ? $emailAutomation->time_unit == 'hour' ? 'selected' : '' : '' }}>hours</option>--}}
{{--                                <option value="day" {{ isset($emailAutomation) ? $emailAutomation->time_unit == 'day' ? 'selected' : '' : '' }}>days</option>--}}
{{--                                <option value="week" {{ isset($emailAutomation) ? $emailAutomation->time_unit == 'week' ? 'selected' : '' : '' }}>weeks</option>--}}
{{--                            </select>--}}
{{--                        </div>--}}
{{--                        <div class="col-sm-4" style="padding-left: 0;">--}}
{{--                            <select class="form-control automation-time-type">--}}
{{--                                <option value="after" {{ isset($emailAutomation) ? $emailAutomation->time_type == 'after' ? 'selected' : '' : '' }}>after</option>--}}
{{--                                <option value="before" {{ isset($emailAutomation) ? $emailAutomation->time_type == 'before' ? 'selected' : '' : '' }}>before</option>--}}
{{--                            </select>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="" style="margin-top: 5px;margin-bottom: 20px;">--}}
{{--                        <select class="form-control automation-event-name">--}}
{{--                            @foreach(\App\EmailAutomation::AUTOMATION_EVENTS as $key => $automationEvent)--}}
{{--                                <option value="{{ $key }}" {{ isset($emailAutomation) ? $emailAutomation->automation_event == $key ? 'selected' : '' : '' }}>{{ $automationEvent }}</option>--}}
{{--                            @endforeach--}}
{{--                        </select>--}}
{{--                    </div>--}}
{{--                    <span style="text-align: start;display: flex;">Want to trigger an action immediately after something else occurs? Set it's timing to 0 minutes, days, etc.</span>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--    --}}{{-- End Email Template Action Part --}}
{{--</div>--}}
