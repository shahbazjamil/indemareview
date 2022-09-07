@extends('layouts.app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="border-bottom col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ isset($emailAutomation) ? 'Edit Automation' : __($pageTitle) }}</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                @if(isset($emailAutomation))
                    <li class="active">{{ __('Edit Automation') }}</li>
                @else
                    <li class="active">{{ __($pageTitle) }}</li>
                @endif
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/smartwizard@5/dist/css/smart_wizard_all.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">
@endpush

@section('content')

    <div class="row dashboard-stats front-dashboard">
        @if ($message = Session::get('error'))
            <div class="alert alert-danger"> {!! $message !!}</div>
            <?php Session::forget('error');?>
        @endif
        <div class="col-md-12 border-bottom m-b-10 p-b-10">
            <div class="automation-box-shadow">
                <div class="automation-header" style="border-radius: 10px 10px 0 0;">
                    <div class="" style="display: flex;align-items: center;">
                        <a href="javascript:;" class="back-to-automation-screen">
                                <i class="fa fa-angle-left fa-fw" style="font-size:24px;"></i></a>
                        <h4 style="padding: 0!important;">
                            <input type="text" name="automation-header-name" class="automation-header-name" value="{{ isset($emailAutomation) ? $emailAutomation->name : 'Untitled Automation' }}" style="border: none;">
                        </h4>
                    </div>
                    <div class="" style="display: flex;align-items: center;">
                        <div class="automation-header-dropdown">
                            <i class="fa fa-ellipsis-v fa-fw automation-header-dropdown-icon"></i>
                            <div id="automationHeaderDropdown" class="automation-header-dropdown-content" style="margin-left: -88px;top: 25px;">
                                <div class="automation-header-dropdown-option rename-automation-header-name">
                                    <div class="" style="height: 16px;">
                                        <i class="fa fa-pencil fa-fw"></i>
                                    </div>
                                    <span class="automation-header-dropdown-option-text">Rename automation</span>
                                </div>
                                <div class="automation-header-dropdown-option {{ isset($emailAutomation) ? 'edit-time-automation-header-dropdown-delete-option' : 'automation-header-dropdown-delete-option' }}">
                                    <div class="">
                                        <i class="fa fa-trash fa-fw" style="color: rgb(255, 97, 97);"></i>
                                    </div>
                                    <span class="automation-header-dropdown-option-text" style="color: rgb(255, 97, 97);">Delete</span>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-primary save-automation" {{ isset($emailAutomation) ? '' : 'disabled' }}>
                            {{ isset($emailAutomation) ? 'UPDATE' : 'SAVE' }}
                        </button>
                    </div>
                </div>
                <div class="" style="padding: 30px 12px;min-height: 100%;background: url({{ asset('img/automation-builder-bg-pattern.svg') }})">
                    <div class="step-1-div {{ isset($emailAutomation)  ? 'display-none' : '' }}">
                        {!! Form::open(['id'=>'addFirstStep', 'class'=>'ajax-form', 'method'=>'POST']) !!}
                            <div class="text-center margin-bottom-10px">
                                <button type="button" class="btn btn-outline btn-success btn-sm next-1" name="step_1">+ ADD FIRST ACTION</button>

                                <input type="hidden" name="step" value="0">
                            </div>
                        {!! Form::close() !!}
                    </div>
                    <div class="step-2-div display-none {{ isset($emailAutomation)  ? 'display-none' : '' }}">
                        {!! Form::open(['id'=>'addSecondStep', 'class'=>'ajax-form', 'method'=>'POST']) !!}
                        <div class="text-center margin-bottom-10px">
                            @include('admin.automation.option')

                            <input type="hidden" name="step" value="1">
                        </div>
                        {!! Form::close() !!}
                    </div>
                    <div class="step-3-div step-3-4-div {{ isset($emailAutomation) ?  '' : 'display-none'  }}">
                        {!! Form::open(['id'=>'createAutomationForm', 'class'=>'ajax-form', 'method'=>'POST']) !!}
                        <div class="text-center margin-bottom-10px">
                            @include('admin.automation.action')

                            <input type="hidden" name="step" class="email-automation-step" value="0">
                            <input type="hidden" name="automation_name" class="automation_name" value="">
                            <input type="hidden" name="edit_automation_master_id" value="{{ isset($emailAutomation) ? $emailAutomation->id : null }}">
                            {{-- Go to first step --}}
                            <span class="previous-1"></span>
                        </div>
                        {!! Form::close() !!}
                    </div>
                    <div class="step-4-div step-3-4-div display-none">
                        {!! Form::open(['id'=>'createAutomationForm', 'class'=>'ajax-form', 'method'=>'POST']) !!}
                        <div class="text-center margin-bottom-10px">
{{--                            @include('admin.automation.action')--}}

                            <input type="hidden" name="step" value="3">
                            <input type="hidden" name="automation_name" class="automation_name" value="">
                            <input type="hidden" name="edit_automation_master_id" value="{{ isset($emailAutomation) ? $emailAutomation->id : null }}">
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
            @include('admin.automation.email_preview')
            @include('admin.automation.edit_email')
            @include('admin.automation.create_email_template')
    </div>
@endsection
<style>
    /* Automation Header Dropdown */
    .automation-header-dropdown-icon{
        font-size: 16px;
        cursor: pointer;
    }
    .automation-header-dropdown {
        position: relative;
        display: inline-block;
    }
    .automation-header-dropdown-content {
        display: none;
        position: absolute;
        background-color: #f9f9f9;
        min-width: 190px;
        box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    }
    .show {
        display:block;
    }
    .automation-header-dropdown-option{
        display: flex;
        -webkit-box-align: center;
        align-items: center;
        height: 42px;
        padding: 0px 20px;
        cursor: pointer;
    }
    .automation-header-dropdown-option:hover{
        background-color: #eee;
    }
    .automation-header-dropdown-option-text{
        font-size: 14px;
        line-height: 1.29;
        color: rgb(36, 39, 43);
    }

    .box-shadow{
        padding: 30px 12px;
        background: #FAFAFA;
        border: 1px solid rgb(227, 227, 227);
        border-radius: 10px;
    }
    .automation-box-shadow{
        background: #FAFAFA;
        border: 1px solid rgb(227, 227, 227);
        border-radius: 10px;
    }
    .automation-header{
        display: flex;
        -webkit-box-align: center;
        align-items: center;
        -webkit-box-pack: justify;
        justify-content: space-between;
        padding: 12px;
        height: 64px;
        box-shadow: rgb(0 18 71 / 8%) 0px 0px 6px 0px;
        background-color: rgb(255, 255, 255);
        position: sticky;
        top: 0px;
        z-index: 100;
    }
    .display-none{
        display: none !important;
    }
    .margin-bottom-10px{
        margin-bottom: 10px;
    }
     .step-2-card{
        padding: 24px;
        border-radius: 8px;
        box-shadow: rgb(0 18 71 / 16%) 0px 1px 3px 0px;
        background-color: rgb(255, 255, 255);
        align-items: center;
        position: relative;
        display: flex;
        flex-direction: column;
        min-width: 0;
        word-wrap: break-word;
        background-clip: border-box;
    }
     .step-2-card-body{
        padding: 0px 25px 25px 25px;
    }
     .step-2-card-header{
        font-size: 14px;
        text-align: start;
        padding-bottom: 8px;
        font-weight: 600;
        margin: 0 !important;
        color: rgb(168, 173, 184);
    }
     .step-2-card-body-email-section{
        flex-direction: column;
        cursor: pointer;
        margin-top: 8px;
        border-radius: 6px;
        padding: 12px;
    }
    .step-2-card-body-email-section-icon{
        margin: 0;
        text-align: start;
        font-size: 18px;
    }
     .step-2-card-body-email-section-text{
        text-align: start;
        margin-top: 0px;
        font-size: 16px;
    }
     .step-2-card-body-email-section.selected{
        background: #e9e9e9;
    }
     .step-2-card-body-email-section:hover{
        background-color: rgba(89, 126, 255, 0.08);
    }
    .step-3-4-div .step-3-4-card{
        /*position: relative;*/
        /*border-radius: 8px;*/
        /*background-color: rgb(255, 255, 255);*/
        /*transition: box-shadow 150ms ease 0s, background-color 300ms ease 0s;*/
        /*padding: 24px;*/
        /*box-shadow: rgb(0 18 71 / 15%) 0px 10px 20px 0px;*/
        /*align-items: center;*/
        /*display: flex;*/
        /*flex-direction: column;*/
        /*min-width: 0;*/
        /*word-wrap: break-word;*/
        /*background-clip: border-box;*/
        /*border: 2px solid #000;*/
        /*margin-bottom: 12px;*/
    }
    .step-3-4-card.step-3-4-card-without-border:hover{
        border-color: rgba(89, 126, 255, 0.2) !important;
    }
    .delete-automation-email-card, .edit-delete-automation-email-card{
        display:none;
    }
    .step-3-4-card-with-border .delete-automation-email-card, .step-3-4-card-with-border .edit-delete-automation-email-card{
        position: absolute;
        font-size: 20px;
        display: flex;
        -webkit-box-align: center;
        align-items: center;
        -webkit-box-pack: center;
        justify-content: center;
        flex-direction: column;
        top: 34px;
        right: -50px;
        height: calc(100% - 64px);
        cursor: pointer;
    }
    /*.step-3-4-card-with-border .delete-automation-email-card.delete-automation-btn, .step-3-4-card-with-border .edit-delete-automation-email-card.delete-automation-btn{*/
    /*    top: 65px;*/
    /*    height: inherit;*/
    /*}*/
    .checkbox-automation-email-card{
        position: absolute;
        font-size: 20px;
        display: flex;
        /*top: 90px;*/
        /*right: -63px;*/
        margin-top: -40px;
        right: 36%;
        cursor: pointer;
    }
    .checkbox-automation-email-card .checkbox-automation-email-card-label{
        display: flex;
    }
    .checkbox-automation-email-card-label .store-edit-is-manual{
        margin: 0;
    }
    .checkbox-automation-email-card-label .checkbox-label{
        font-size: 14px;
    }
    .checkbox-automation-email-card-label .tooltip-inner2{
        font-size: 14px;
    }
    .step-3-4-div .step-3-4-card-with-border{
        position: relative;
        border-radius: 8px;
        background-color: rgb(255, 255, 255);
        transition: box-shadow 150ms ease 0s, background-color 300ms ease 0s;
        padding: 28px;
        box-shadow: rgb(0 18 71 / 15%) 0px 10px 20px 0px;
        align-items: center;
        display: flex;
        flex-direction: column;
        min-width: 0;
        word-wrap: break-word;
        background-clip: border-box;
        border: 2px solid #000;
        margin-bottom: 12px;
    }
    .step-3-4-div .step-3-4-card-without-border{
        position: relative;
        border-radius: 8px;
        background-color: rgb(255, 255, 255);
        transition: box-shadow 150ms ease 0s, background-color 300ms ease 0s;
        padding: 28px;
        align-items: center;
        display: flex;
        flex-direction: column;
        min-width: 0;
        word-wrap: break-word;
        margin-bottom: 12px;
        border: 2px solid rgb(255, 255, 255);
        cursor: pointer;
        box-shadow: rgb(0 18 71 / 16%) 0px 1px 3px 0px;
    }
    .step-3-4-div .start-btn{
        margin-bottom: 14px;
    }
    .step-3-4-div .plus-btn{
        margin-top: 14px;
    }
    .step-3-4-div .plus-btn .plus-btn-span{
        color: black;
        height: 38px;
        width: 38px;
        background: white;
        border-radius: 50%;
        display: inline-block;
        font-weight: 300;
        line-height: 0.8;
        margin: 0;
        vertical-align: middle;
        font-size: 32px;
        border: 1px solid #eee;
        border-width: initial;
        top: 2px;
        cursor: pointer;
    }
    .step-3-4-div .step-3-4-card-email-div .step-3-4-card-email-div-icon{
        font-size: 30px;
        font-weight: 700;
        margin-right: 22px;
    }
    .step-3-4-div .step-3-4-card-email-div .step-3-4-card-email-div-text{
        font-weight: 600;
    }
    .step-3-4-div .start-btn-arrow{
        transform: rotate(90deg);
        border: 1px solid gray;
        font-size: 60px;
        opacity: 0.5;
    }
    .step-3-4-div .plus-btn-arrow{
        transform: rotate(90deg);
        border: 1px solid gray;
        font-size: 60px;
        opacity: 0.5;
    }
    .step-3-4-div .email-sidebar-section{
        display: flex;
        flex-direction: column;
        right: 0px;
        width: 300px;
        background-color: rgb(255, 255, 255);
        box-shadow: rgb(0 18 71 / 8%) 0px 10px 20px 0px, rgb(0 18 71 / 5%) 0px 1px 4px 0px;
        border-top-left-radius: 6px;
        border-bottom-left-radius: 6px;
        padding: 0;
    }
    .step-3-4-div .email-sidebar-section .email-sidebar-section-header{
        display: flex;
        -webkit-box-align: center;
        align-items: center;
        border-bottom: 1px solid rgb(238, 239, 241);
        padding: 20px 20px 16px;
    }
    .step-3-4-div .email-sidebar-section  .email-sidebar-section-header .email-sidebar-section-header-text{
        font-size: 16px;
        font-weight: bold;
        margin-left: 50px;
    }
    .step-3-4-div .list-of-templates{
        padding: 12px 0px;
    }
    .step-3-4-div .email-sidebar-section__list-template{
        width: 100%;
        height: 46px;
        display: flex;
        -webkit-box-align: center;
        align-items: center;
        gap: 6px;
        padding: 7px 12px 7px 20px;
        cursor: pointer;
        transition: background-color 150ms ease 0s;
    }
    .step-3-4-div .email-sidebar-section__list-template-name{
        font-size: 16px;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }
    .step-3-4-div .email-sidebar-section__list-template.selected{
        background: #e9e9e9;
    }
    .step-3-4-div .email-sidebar-section__list-template:hover{
        background-color: rgb(246, 247, 248);
    }
    .step-3-4-div .email-sidebar-section .hr{
        margin-top: 20px;
        margin-bottom: 20px;
    }
    .step-3-4-div .email-sidebar-section__dropdown{
        padding: 12px 20px 20px;
        border-bottom: 1px solid rgb(238, 239, 241);
    }
    .step-3-4-div .email-sidebar-section__dropdown-div{
        padding: 12px 0px;
        padding-bottom: 4px;
    }
    .step-3-4-div .email-sidebar-section__dropdown-div-span{
        display: block;
        text-align: start;
        padding-bottom: 8px;
        font-size: 14px;
        line-height: 1.29;
        color: rgb(36, 39, 43);
    }

    .email-modal{
        background-color: #fff;
        background-clip: border-box;
        border: 1px solid #0000001f;
    }
    .edit-email-modal{
        background-color: #fff;
        background-clip: border-box;
        border: 1px solid #0000001f;
    }

    /* Validation */
    .step-3-in-valid{
        border: 2px solid red !important;
    }

    /* Edit Modal */
    .edit-email-modal-mail-icon{
        margin-right: 13px;
        margin-top: -1px;
        display: flex;
        -webkit-box-align: center;
        align-items: center;
        -webkit-box-pack: center;
        justify-content: center;
    }

    /* Card 4 css */
    .step-4-card-div{
        border: 2px solid rgb(255, 255, 255);
        background-color: rgb(255, 255, 255);
        cursor: pointer;
        transition: box-shadow 150ms ease 0s, background-color 300ms ease 0s;
        box-shadow: rgb(0 18 71 / 16%) 0px 1px 3px 0px;
        position: relative;
        border-radius: 8px;
        padding: 24px;
        align-items: center;
        display: flex;
        flex-direction: column;
        min-width: 0;
        word-wrap: break-word;
        background-clip: border-box;
        margin-bottom: 12px;
    }
</style>
@push('footer-script')
    <script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>

    <script src="{{ asset('plugins/bower_components/waypoints/lib/jquery.waypoints.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/counterup/jquery.counterup.min.js') }}"></script>
    <script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
    <script src="{{ asset('js/datatables/buttons.server-side.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/smartwizard@5/dist/js/jquery.smartWizard.min.js" type="text/javascript"></script>
    <script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>

    <script>
        let pdfIcon = "{{ asset('img/pdf.png') }}";
        let editEmailAutomation = JSON.parse(JSON.stringify(@JSON(isset($emailAutomation) ? $emailAutomation : null)));
        let emailTemplateIds = null;
        let editProjectId = null;
        $(function () {


            // Header Dropdown
            $(document).on('click', '.automation-header-dropdown-icon', function () {
                document.getElementById("automationHeaderDropdown").classList.toggle("show");
            });

            // Close the dropdown if the user clicks outside of it
            window.onclick = function(event) {
                if (!event.target.matches('.automation-header-dropdown-icon')) {

                    let dropdowns = document.getElementsByClassName("automation-header-dropdown-content");
                    let i;
                    for (i = 0; i < dropdowns.length; i++) {
                        let openDropdown = dropdowns[i];
                        if (openDropdown.classList.contains('show')) {
                            openDropdown.classList.remove('show');
                        }
                    }
                }
            }


            function changeTemplateNameInTemplateCard()
            {
                if ($('.step-4-div-email-sidebar-section').css('display') == 'flex'){
                    let eleText = $('.email-preview_email-template').find(":selected").text();
                    $('.step-3-4-card.selected').find('.select-email-template-card-email-template-name').text(eleText);
                    // STEP 3 email template value
                    $('.step-3-4-card.selected').find('.email-template-card-validation').val(eleText);
                }
            }

            function addValueInForm()
            {
                let actionTypeId =  $('.action-type-dropdown').val();
                let emailTemplateId =  $('.email-preview_email-template').find(':selected').val();
                let clientId =  $('.client-dropdown').find(':selected').val();
                let projectId =  $('.project-dropdown').find(':selected').val();
                let timePeriod =  $('.automation-time-period').val();
                let timeUnitId =  $('.automation-time-unit').val();
                let timeTypeId =  $('.automation-time-type').val();
                let eventName =  $('.automation-event-name').val();

                $('.step-3-4-card.selected').find('.store-edit-action-type').val(actionTypeId);
                $('.step-3-4-card.selected').find('.store-edit-email-template-id').val(emailTemplateId);
                $('.step-3-4-card.selected').find('.store-edit-time-period').val(timePeriod);
                $('.step-3-4-card.selected').find('.store-edit-time-unit').val(timeUnitId);
                $('.step-3-4-card.selected').find('.store-edit-time-type').val(timeTypeId);
                $('.step-3-4-card.selected').find('.store-edit-automation-event').val(eventName);
                $('.step-3-4-card.selected').find('.store-edit-client-id').val(clientId);
                $('.step-3-4-card.selected').find('.store-edit-project-id').val(projectId);

                setSendManuallyButtonValue(clientId, projectId)
            }

            function setSendManuallyButtonValue(clientId, projectId)
            {
                if (clientId && projectId){
                    $('.step-3-4-card.selected').next('.checkbox-automation-email-card').removeClass('display-none');
                }else {
                    $('.step-3-4-card.selected').next('.checkbox-automation-email-card').addClass('display-none');
                    $('.step-3-4-card.selected').find('.store-edit-is-manual').val(0);
                }
            }

            $(document).on('click', '.add-new-email-automation-card', function (){
                // Hide Plus Button
                $('.first-automation-card-plus-btn').hide();

               $('.display-all-automation-card').append(`
                    <div class="add-new-automation-card">
                        <span class="plus-btn-arrow"></span>
                        <div class="row d-flex" style="justify-content: center;">
                              <div class="card step-2-card">
                                    <div class="card-body">
                                        <div class="d-flex" style="justify-content: space-between;">
                                            <h4 class="step-2-card-header">ADD YOUR FIRST ACTION</h4>
                                            <button type="button" class="close remove-automation-card" style="color: #000;opacity: .5;"><i class="fa fa-times"></i></button>
                                        </div>
                                        <div class="d-flex step-2-card-body-email-section new-automation-select-email-option">
                                            <h3 class="step-2-card-body-email-section-icon"><i class="icon-envelope fa-fw"></i> Send Email</h3>
                                            <span class="text-muted step-2-card-body-email-section-text email-template-type" data-id="1">Send an email to your leads or clients automatically.</span>
                                        </div>
                                        <div class="d-flex step-2-card-body-email-section new-automation-select-email-option">
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
                   </div>`);
            });

            $(document).on('click', '.remove-automation-card', function(){
                $(this).closest('.add-new-automation-card').remove();
                // Display Plus Button
                $('.first-automation-card-plus-btn').show();
            });

            // Delete Email Automation card
            $(document).on('click', '.delete-automation-email-card', function (){
                let cardLength = $('.step-3-4-card').length;
                if(cardLength == 1){
                    $('.display-all-select-email-template-automation').empty();
                    $('.display-all-select-email-template-automation').append(`
                        <div class="card step-3-4-card-with-border step-3-4-card selected">
                            <div class="card-body step-3-card-body">
                                <div class="" style="margin-bottom: 10px;">
                                    <span class="text-muted select-email-template-card-email-template-event-name">Immediately after automation is activated...</span>
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
                                <input type="hidden" class="email-template-card-validation">
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
                        `);
                    $('.automation-time-period').val(0);
                    $('.previous-3').click();
                }else{
                    if($(this).parent('.step-3-4-card').prev('.plus-btn-arrow').length == 0){
                        $('.start-btn-arrow').addClass('display-none');
                        $(this).parent('.step-3-4-card').remove();
                    }else{
                        $(this).parent('.step-3-4-card').prev('.plus-btn-arrow').remove();
                        $(this).parent('.step-3-4-card').remove();
                    }
                }
            });

            // Edit Delete Email Automation card
            $(document).on('click', '.edit-delete-automation-email-card', function (){
                let cardLength = $('.step-3-4-card').length;
                if(cardLength == 1){
                    $('.display-all-select-email-template-automation').empty();
                    $('.display-all-select-email-template-automation').append(`
                        <div class="card step-3-4-card-with-border step-3-4-card selected">
                            <div class="card-body step-3-card-body">
                                <div class="" style="margin-bottom: 10px;">
                                    <span class="text-muted select-email-template-card-email-template-event-name">Immediately after automation is activated...</span>
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
                                <input type="hidden" class="email-template-card-validation">
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
                        `);
                    $('.automation-time-period').val(0);
                    $('.previous-1').click();
                }else{
                    if($(this).parent('.step-3-4-card').prev('.plus-btn-arrow').length == 0){
                        $('.start-btn-arrow').addClass('display-none');
                        $(this).parent('.step-3-4-card').remove();
                    }else{
                        $(this).parent('.step-3-4-card').prev('.plus-btn-arrow').remove();
                        $(this).parent('.step-3-4-card').remove();
                    }
                }
            });

            $(document).on('click', '.new-automation-select-email-option', function (){
                let value = $(this).find('.email-template-type').data('id');
                $.ajax({
                    url: '{{ route('admin.email-automation.get.email.files') }}',
                    type: "POST",
                    data: {'email_type' : value, '_token': "{{ csrf_token() }}"},
                    success: function (data) {
                        $('.list-of-templates').empty();
                        $.map(data.emailTemplates, function (v, i){
                            $('.list-of-templates').append(`
                                    <div class="email-sidebar-section__list-template next-4">
                                        <span class="email-sidebar-section__list-template-name list-email-template-name" data-id="${v.id}">
                                            ${v.template_name}
                                        </span>
                                    </div>`);
                        });

                        // Display Plus Button
                        $('.first-automation-card-plus-btn').show();

                        // Empty Automation Card
                        $('.display-all-automation-card').empty();

                        $('.step-3-4-card').removeClass('selected');
                        $('.step-3-4-card').removeClass('step-3-4-card-with-border');
                        $('.step-3-4-card').addClass('step-3-4-card-without-border');
                        if(value == '1'){
                            $('.display-all-select-email-template-automation').append(`
                        <span class="plus-btn-arrow"></span>
                        <div class="card step-3-4-card-with-border step-3-4-card selected">
                            <div class="card-body step-3-card-body">
                                <div class="" style="margin-bottom: 10px;">
                                    <span class="text-muted select-email-template-card-email-template-event-name">Immediately after automation is activated...</span>
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
                                <input type="hidden" class="email-template-card-validation">
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
                        `);
                            $('.action-type-dropdown').val(1).trigger('change');
                        }else {
                            $('.display-all-select-email-template-automation').append(`
                                <span class="plus-btn-arrow"></span>
                                <div class="card step-3-4-card-with-border step-3-4-card selected">
                                    <div class="card-body step-3-card-body">
                                        <div class="" style="margin-bottom: 10px;">
                                            <span class="text-muted select-email-template-card-email-template-event-name">Immediately after automation is activated...</span>
                                        </div>
                                        <div class="step-3-4-card-email-div select-email-template-card-email-div">
                                            <i class="icon-docs fa-fw step-3-4-card-email-div-icon"></i>
                                            <h3 class="step-3-4-card-email-div-text" data-id="2"> Send File via Email</h3>
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
                                        <input type="hidden" class="email-template-card-validation">
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
                                `);
                            $('.action-type-dropdown').val(2).trigger('change');
                        }

                        // Unable save button
                        $('.save-automation').attr('disabled', false);

                        // Add value in form
                        setTimeout(function(){
                            addValueInForm();
                        },500);
                    },
                    error: function (data){

                    }
                });
            });

            // Step 3-4 Card
            window.addEventListener("DOMContentLoaded", function(){
                let isEdit = $('.step-3-4-card.selected').hasClass('edit-step-3-4-card-email-automation');
                if (isEdit && editEmailAutomation != null && editEmailAutomation) {
                    let automationId = $('.step-3-4-card.selected').data('id');
                    $.map(editEmailAutomation.email_automations, function (v, i) {
                        if (v.id == automationId) {
                            // Edit value in form
                            emailTemplateIds = v.email_template_id;
                            editProjectId = v.project_id;
                            $('.action-type-dropdown').val(v.email_type).trigger('change');
                            $('.email-preview_email-template').val(v.email_template_id).trigger('change');
                            $('.automation-time-period').val(v.time_period);
                            $('.automation-time-unit').val(v.time_unit).trigger('change');
                            $('.automation-time-type').val(v.time_type).trigger('change');
                            $('.automation-event-name').val(v.automation_event).trigger('change');
                            $('.client-dropdown').val(v.client_id).trigger('change');
                            $('.project-dropdown').val(v.project_id).trigger('change');

                            $('.step-3-4-card.selected').find('.store-edit-action-type').val(v.email_type);
                            $('.step-3-4-card.selected').find('.store-edit-email-template-id').val(v.email_template_id);
                            $('.step-3-4-card.selected').find('.store-edit-time-period').val(v.time_period);
                            $('.step-3-4-card.selected').find('.store-edit-time-unit').val(v.time_unit);
                            $('.step-3-4-card.selected').find('.store-edit-time-type').val(v.time_type);
                            $('.step-3-4-card.selected').find('.store-edit-automation-event').val(v.automation_event);
                            $('.step-3-4-card.selected').find('.store-edit-client-id').val(v.client_id);
                            $('.step-3-4-card.selected').find('.store-edit-project-id').val(v.project_id);
                            if (v.is_manual == 1){
                                $('.step-3-4-card.selected').next('.checkbox-automation-email-card').removeClass('display-none');
                                $('.step-3-4-card.selected').find('.store-edit-is-manual').val(1);
                            }else {
                                $('.step-3-4-card.selected').next('.checkbox-automation-email-card').addClass('display-none');
                                $('.step-3-4-card.selected').find('.store-edit-is-manual').val(0);
                            }
                        }
                    });
                }
            });

            // Step 3-4 card
            $(document).on('click', '.step-3-4-card', function (){
                $('.step-3-4-card').removeClass('step-3-4-card-with-border selected');
                $('.step-3-4-card').addClass('step-3-4-card-without-border');
                $(this).removeClass('step-3-4-card-without-border');
                $(this).addClass('step-3-4-card-with-border selected');

                let emailType = $(this).find('.select-email-template-card-email-div').find('.step-3-4-card-email-div-text').data('id');
                $.ajax({
                    url: '{{ route('admin.email-automation.get.email.files') }}',
                    type: "POST",
                    data: {'email_type' : emailType, '_token': "{{ csrf_token() }}"},
                    success: function (data) {
                        $('.list-of-templates').empty();
                        $.map(data.emailTemplates, function (v, i){
                            $('.list-of-templates').append(`
                                <div class="email-sidebar-section__list-template next-4">
                                    <span class="email-sidebar-section__list-template-name list-email-template-name" data-id="${v.id}">
                                        ${v.template_name}
                                    </span>
                                </div>`);
                        });
                        if (!isEdit && editEmailAutomation == null) {
                            if (emailType == '1') {
                                $('.action-type-dropdown').val(1).trigger('change');
                            } else {
                                $('.action-type-dropdown').val(2).trigger('change');
                            }
                        }
                    },
                    error: function (data){

                    }
                });

                let isEdit = $(this).hasClass('edit-step-3-4-card-email-automation');
                if (isEdit && editEmailAutomation != null && editEmailAutomation) {
                    let automationId = $(this).data('id');
                    $.map(editEmailAutomation.email_automations, function (v, i){
                        if (v.id == automationId){

                            // Edit value in form
                            emailTemplateIds = v.email_template_id;
                            editProjectId = v.project_id;
                            $('.action-type-dropdown').val(v.email_type).trigger('change');
                            $('.email-preview_email-template').val(v.email_template_id).trigger('change');
                            $('.automation-time-period').val(v.time_period);
                            $('.automation-time-unit').val(v.time_unit).trigger('change');
                            $('.automation-time-type').val(v.time_type).trigger('change');
                            $('.automation-event-name').val(v.automation_event).trigger('change');
                            $('.client-dropdown').val(v.client_id).trigger('change');
                            $('.project-dropdown').val(v.project_id).trigger('change');

                            $('.step-3-4-card.selected').find('.store-edit-action-type').val(v.email_type);
                            $('.step-3-4-card.selected').find('.store-edit-email-template-id').val(v.email_template_id);
                            $('.step-3-4-card.selected').find('.store-edit-time-period').val(v.time_period);
                            $('.step-3-4-card.selected').find('.store-edit-time-unit').val(v.time_unit);
                            $('.step-3-4-card.selected').find('.store-edit-time-type').val(v.time_type);
                            $('.step-3-4-card.selected').find('.store-edit-automation-event').val(v.automation_event);
                            $('.step-3-4-card.selected').find('.store-edit-client-id').val(v.client_id);
                            $('.step-3-4-card.selected').find('.store-edit-project-id').val(v.project_id);
                            if (v.is_manual == 1){
                                $('.step-3-4-card.selected').next('.checkbox-automation-email-card').removeClass('display-none');
                                $('.step-3-4-card.selected').find('.store-edit-is-manual').val(1);
                            }else {
                                $('.step-3-4-card.selected').next('.checkbox-automation-email-card').addClass('display-none');
                                $('.step-3-4-card.selected').find('.store-edit-is-manual').val(0);
                            }
                        }
                    });
                }else{
                    setTimeout(function() {
                        let actionTypeId = $('.action-type-dropdown').val();
                        let emailTemplateId = $('.email-preview_email-template').find(":selected").val();
                        let clientId =  $('.client-dropdown').find(":selected").val();
                        let projectId = $('.project-dropdown').find(":selected").val();
                        let timePeriod = $('.automation-time-period').val();
                        let timeUnitId = $('.automation-time-unit').val();
                        let timeTypeId = $('.automation-time-type').val();
                        let eventName = $('.automation-event-name').val();

                        $('.step-3-4-card.selected').find('.store-edit-action-type').val(actionTypeId);
                        $('.step-3-4-card.selected').find('.store-edit-email-template-id').val(emailTemplateId);
                        $('.step-3-4-card.selected').find('.store-edit-time-period').val(timePeriod);
                        $('.step-3-4-card.selected').find('.store-edit-time-unit').val(timeUnitId);
                        $('.step-3-4-card.selected').find('.store-edit-time-type').val(timeTypeId);
                        $('.step-3-4-card.selected').find('.store-edit-automation-event').val(eventName);
                        $('.step-3-4-card.selected').find('.store-edit-client-id').val(clientId);
                        $('.step-3-4-card.selected').find('.store-edit-project-id').val(projectId);
                        setSendManuallyButtonValue(clientId, projectId)
                    }, 1000);
                }

                // Email template template name display in email template card
                // changeTemplateNameInTemplateCard();
            });

            // Rename Automation Header name
            $(document).on('click', '.rename-automation-header-name', function (){
                $('.automation-header-name').focus();
            });
            // Header Dropdown in Delete option
            $(document).on('click', '.automation-header-dropdown-delete-option', function (){
                swal({
                    title: "Are you sure?",
                    text: "You have unsaved changes and will lose all edits to this automation.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, delete it!",
                    cancelButtonText: "No, cancel please!",
                    closeOnConfirm: true,
                    closeOnCancel: true
                }, function (isConfirm) {
                    if (isConfirm) {
                        window.location.href = "{{ route('admin.email-automation.index') }}";
                    }
                });
            });

            $(document).on('click', '.edit-time-automation-header-dropdown-delete-option', function (){
                swal({
                    title: "Are you sure?",
                    text: "You have unsaved changes and will lose all edits to this automation.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, delete it!",
                    cancelButtonText: "No, cancel please!",
                    closeOnConfirm: true,
                    closeOnCancel: true
                }, function (isConfirm) {
                    if (isConfirm) {
                        if (editEmailAutomation != null && editEmailAutomation){
                            let id = editEmailAutomation.id;
                            let url = "{{ route('admin.email-automation.destroy',':id') }}";
                            url = url.replace(':id', id);

                            let token = "{{ csrf_token() }}";

                            $.easyAjax({
                                type: 'DELETE',
                                url: url,
                                data: {'_token': token, '_method': 'DELETE', 'id': id},
                                success: function (response) {
                                    if (response.status === "success") {
                                        $.unblockUI();
                                        window.location.href = "{{ route('admin.email-automation.index') }}";
                                    }
                                }
                            });
                        }
                    }
                });
            });


            $('.summernote').summernote({
                height: 200,                 // set editor height
                minHeight: null,             // set minimum height of editor
                maxHeight: null,             // set maximum height of editor
                focus: false,
                toolbar: [
                    // [groupName, [list of button]]
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough']],
                    ['fontsize', ['fontsize']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['link', ['linkDialogShow', 'unlink']],
                    ["view", ["fullscreen"]],
                ]
            });

            $(document).ready(function(){

                function actionTypeDropdown(id) {
                    $.ajax({
                        url: '{{ route('admin.email-automation.get.email.files') }}',
                        type: "POST",
                        data: {'email_type': id, '_token': "{{ csrf_token() }}"},
                        success: function (data) {
                            $('.email-preview_email-template').empty();
                            $.map(data.emailTemplates, function (v, i) {
                                $('.email-preview_email-template').append(`<option value="${v.id}" ${i == 0 ? "selected" : ""}>${v.template_name}</option>`);
                            });
                        },
                        error: function (data) {

                        }
                    });
                }

                function editEmailPreviewModal(id) {
                    $.ajax({
                        url: '{{ route('admin.email-automation.get.email.template.detail') }}',
                        type: "POST",
                        data: {'emailTemplate' : id, '_token': "{{ csrf_token() }}"},
                        success: function (data) {
                            if(data.emailTemplate != null) {
                                $('.edit-email-preview-subject').val('');
                                $('.edit-email-template-link-tag').attr('href', '#');
                                $('.edit-email-template-img-preview').attr('src', '');
                                $('.edit-email-preview-body').summernote('code', '');
                                $('.edit-email-preview-subject').val(data.emailTemplate.subject);
                                $('.edit-email-preview-body').summernote('code', data.emailTemplate.body);
                                if (data.emailTemplate.email_type == '2') {
                                    $('.edit-email-template-image-upload-div').removeClass('display-none');
                                    $('.edit-email-template-link-tag').attr('href', data.emailTemplate.file_url);
                                    if (data.emailTemplate.file_extension == 'pdf') {
                                        $('.edit-email-template-img-preview').attr('src', pdfIcon);
                                    } else {
                                        $('.edit-email-template-img-preview').attr('src', data.emailTemplate.file_url);
                                    }
                                } else {
                                    $('.edit-email-template-image-upload-div').addClass('display-none');
                                }
                                $('.edit-email-preview-subject').attr('data-id', id);
                                $('.edit-email-template-id').val(data.emailTemplate.id);
                            }
                        },
                        error: function (data){

                        }
                    });
                }

                function displayImg(input,$ele) {
                    if (input.files && input.files[0]) {
                        let ext = $(input).val().split('.').pop().toLowerCase();
                        var reader = new FileReader();
                        reader.onload = function(event) {
                            if(ext == 'pdf'){
                                $ele.attr('src', pdfIcon);
                            }else {
                                $ele.attr('src', event.target.result);
                            }
                        }
                        reader.readAsDataURL(input.files[0]);
                    }
                }

                // back to automation home screen
                $(document).on('click', '.back-to-automation-screen', function(){
                    let checkedThirdValidation =  $('.email-template-card-validation').val();
                    if($('.step-3-div').css('display') == 'block' || $('.step-4-div').css('display') == 'block'){
                        if(checkedThirdValidation == null || checkedThirdValidation == ''){
                            $.showToastr('Please choose template', 'error');
                            $('.step-3-div').find('.card.step-3-4-card').addClass('step-3-in-valid');
                            setTimeout(function (){
                                $('.step-3-div').find('.card.step-3-4-card').removeClass('step-3-in-valid');
                            }, 5000);

                            return false;
                        }else{
                            swal({
                                title: "Are you sure?",
                                text: "You have unsaved changes and will lose all edits to this automation.",
                                type: "warning",
                                showCancelButton: true,
                                confirmButtonColor: "#DD6B55",
                                confirmButtonText: "Save and Exit",
                                cancelButtonText: "Discard changes and Exit",
                                closeOnConfirm: true,
                                closeOnCancel: true
                            }, function (isConfirm) {
                                if (isConfirm) {
                                    $('.save-automation').click();
                                }else {
                                    window.location.href = "{{ route('admin.email-automation.index') }}";
                                }
                            });
                        }
                    }else{
                        window.location.href = "{{ route('admin.email-automation.index') }}";
                    }
                });

                // Create New Mail Template
                $(document).on('click', '.create-new-email-template', function (){
                    $('.createEmailTemplate').modal('show');
                });

                if ($('.select-email').val() == '2'){
                    $('.email-file-div').removeClass('display-none');
                }
                $(document).on('change', '.select-email', function (){
                    if ($(this).val() == '2'){
                        $('.email-file-div').removeClass('display-none');
                    }else {
                        $('.email-file-div').addClass('display-none');
                    }
                });

                $(document).on('click', '#creatEmailTemplateBtn', function (){
                    let form = $('#createEmailTemplateForm');
                    $.ajax({
                        url: '{{route('admin.email-template.store')}}',
                        container: '#createEmailTemplateForm',
                        type: "POST",
                        data: new FormData(form[0]),
                        processData: false,
                        contentType: false,
                        success: function (data) {
                            if (data.status == 'success') {
                                $.showToastr(data.message, 'success');
                                $('.createEmailTemplate').modal('hide');
                                $('.createEmailTemplate').on('hidden.bs.modal', function () {
                                    $(this).find('form').trigger('reset');
                                    $(".create-email-template-body").summernote("code", "");
                                });
                            }
                            if(data.status == 'fail'){
                                $.showToastr(data.message, 'error');
                            }
                        },
                        error:function (data){
                            $.showToastr(data.responseJSON.message, 'error');
                        },
                    });
                });

                $(document).on('change','.file',function (){
                    let $ele = $('.display-file-preview');
                    displayImg(this,$ele);
                });

                // Preview and Edit Button
                $('#emailPreviewBtn').click(function(){
                    let emailTemplate = $('.email-preview_email-template').val();
                    $.ajax({
                        url: '{{ route('admin.email-automation.get.email.template.detail') }}',
                        type: "POST",
                        data: {'emailTemplate' : emailTemplate, '_token': "{{ csrf_token() }}"},
                        success: function (data) {
                            $('.email-preview-subject').html('');
                            $('.email-preview-body').html('');
                            $('.email-preview-heading').html('');
                            $('.email-preview-heading').html(data.emailTemplate.template_name +' '+ 'email preview');
                            $('.email-preview-subject').html(data.emailTemplate.subject);
                            $('.email-preview-body').html(data.emailTemplate.body);
                            $('.email-preview-subject').attr('data-id', emailTemplate);
                            $('.emailPreview').modal('show');

                            // Edit Email Template Modal
                            $('.edit-email-preview_email-template').val(data.emailTemplate.id).trigger('change');
                        },
                        error: function (data){

                        }
                    });
                });

                $(document).on('change','.edit-email-template-file',function (){
                    let $ele = $('.edit-email-template-img-preview');
                    displayImg(this,$ele);
                });

                $('#editEmailPreviewBtn').click(function(){
                    $('.emailPreview').modal('hide');
                    let emailTemplateId = $('.email-preview_email-template').val();
                    editEmailPreviewModal(emailTemplateId);
                    $('.editEmailTemplate').modal('show');
                });

                $(document).on('click', '.previous-1', function (){
                    $('.step-1-div').removeClass('display-none');
                    $('.step-2-div').addClass('display-none');
                    $('.step-3-div').addClass('display-none');
                    $('.step-3-div').find('.step-3-div-email-sidebar-section').addClass('display-none');
                    $('.step-3-div').find('.step-4-div-email-sidebar-section').addClass('display-none');
                });

                $(document).on('click', '.next-1', function (){
                    $('.step-1-div').addClass('display-none');
                    $('.step-2-div').removeClass('display-none');
                    $('.step-3-div').addClass('display-none');
                    $('.step-3-div').find('.step-3-div-email-sidebar-section').addClass('display-none');
                });

                $(document).on('click', '.next-2', function (){
                    $('.step-1-div').addClass('display-none');
                    $('.step-2-div').addClass('display-none');
                    if ($('.step-3-4-card').length == 1){
                        if ($('.step-3-4-card').prev('.plus-btn-arrow').length == 0){
                            $('.start-btn-arrow').removeClass('display-none');
                        }
                    }
                    $('.step-3-div').removeClass('display-none');
                    $('.step-3-div').find('.step-3-div-email-sidebar-section').removeClass('display-none');
                });

                // $(document).on('click', '.previous-2', function (){
                //     $('.step-1-div').removeClass('display-none');
                //     $('.step-2-div').addClass('display-none');
                //     $('.step-3-div').addClass('display-none');
                // });

                // selected email option
                $(document).on('click', '.step-2-card-body-email-section', function (){
                   $('.step-2-card-body-email-section').removeClass('selected');
                   $(this).addClass('selected');
                });

                // select email option and get template
                $(document).on('click', '.select-email-option', function (){
                    let value = $(this).find('.email-template-type').data('id');
                    $.ajax({
                        url: '{{ route('admin.email-automation.get.email.files') }}',
                        type: "POST",
                        data: {'email_type' : value, '_token': "{{ csrf_token() }}"},
                        success: function (data) {
                            $('.list-of-templates').empty();
                            $.map(data.emailTemplates, function (v, i){
                                $('.list-of-templates').append(`
                                    <div class="email-sidebar-section__list-template next-4">
                                        <span class="email-sidebar-section__list-template-name list-email-template-name" data-id="${v.id}">
                                            ${v.template_name}
                                        </span>
                                    </div>`);
                            });
                            $('.step-3-4-card.selected').find('.select-email-template-card-email-div').empty();
                            if(value == '1'){
                                $('.step-3-4-card.selected').find('.select-email-template-card-email-div').append(`
                                    <i class="icon-envelope fa-fw step-3-4-card-email-div-icon"></i>
                                   <h3 class="step-3-4-card-email-div-text" data-id="1"> Send an Email</h3>`);
                                $('.action-type-dropdown').val(1).trigger('change');
                                // actionTypeDropdown(1);
                            }else {
                                $('.step-3-4-card.selected').find('.select-email-template-card-email-div').append(`<i class="icon-docs fa-fw step-3-4-card-email-div-icon"></i>
                                <h3 class="step-3-4-card-email-div-text" data-id="2"> Send File via Email</h3>`);
                                $('.action-type-dropdown').val(2).trigger('change');
                                // actionTypeDropdown(2);
                            }

                            // Unable save button
                            $('.save-automation').attr('disabled', false);
                        },
                        error: function (data){

                        }
                    });
                });


                $(document).on('click', '.next-3', function (){
                    $('.step-1-div').addClass('display-none');
                    $('.step-2-div').addClass('display-none');
                    $('.step-3-div').addClass('display-none');
                    // $('.step-4-div').removeClass('display-none');

                    $('.step-3-div').find('.step-3-div-email-sidebar-section').addClass('display-none');
                    $('.step-3-div').find('.step-4-div-email-sidebar-section').removeClass('display-none');
                });

                $(document).on('click', '.previous-3', function (){
                    $('.step-1-div').addClass('display-none');
                    $('.step-2-div').removeClass('display-none');
                    $('.step-3-div').addClass('display-none');
                    // $('.step-4-div').addClass('display-none');

                    $('.step-3-div').find('.step-3-div-email-sidebar-section').addClass('display-none');
                    $('.step-3-div').find('.step-4-div-email-sidebar-section').addClass('display-none');
                });

                // Selected Email Template
                $(document).on('click', '.email-sidebar-section__list-template', function (){
                    $('.email-sidebar-section__list-template').removeClass('selected');
                    $(this).addClass('selected');

                    // STEP 3 email template value
                    $('.step-3-4-card.selected').find('.email-template-card-validation').val($(this).find('.list-email-template-name').data('id'));

                    // Email template template name display in email template card
                    $('.step-3-4-card.selected').find('.select-email-template-card-email-template-name').text($(this).find('.list-email-template-name').text());

                    //Email template dropdown value change
                    $('.email-preview_email-template').val($(this).find('.list-email-template-name').data('id')).trigger('change');
                    $('.edit-email-preview_email-template').val($(this).find('.list-email-template-name').data('id')).trigger('change');
                });


                $(document).on('click', '.next-4', function (){
                    $('.step-1-div').addClass('display-none');
                    $('.step-2-div').addClass('display-none');
                    // $('.step-3-div').addClass('display-none');
                    $('.step-3-div').find('.step-3-div-email-sidebar-section').addClass('display-none');
                    $('.step-3-div').find('.step-4-div-email-sidebar-section').removeClass('display-none');
                });

                $(document).on('click', '.previous-4', function (){
                    $('.step-1-div').addClass('display-none');
                    $('.step-2-div').addClass('display-none');
                    $('.step-3-div').removeClass('display-none');
                    $('.step-3-div').find('.step-3-div-email-sidebar-section').removeClass('display-none');
                    $('.step-3-div').find('.step-4-div-email-sidebar-section').addClass('display-none');

                    // $('.step-4-div').addClass('display-none');
                });

                // Email Preview modal
                $(document).on('change', '.email-preview_email-template', function (){
                    let Ele = $(this);
                    let eleText = Ele.find(":selected").text();
                    $('.email-preview-subject').text(eleText);
                    $('.email-preview-subject').attr('data-id', Ele.val());

                    // Email template template name display in email template card
                    $('.step-3-4-card.selected').find('.select-email-template-card-email-template-name').text(eleText);

                    // STEP 3 email template value
                    $('.step-3-4-card.selected').find('.email-template-card-validation').val(Ele.val());

                    // Add value in form
                    setTimeout(function(){
                        addValueInForm();
                    },500);
                });

                // Action type dropdown
                $(document).on('change', '.action-type-dropdown', function (){
                    let value = $(this).val();
                    $.ajax({
                        url: '{{ route('admin.email-automation.get.email.files') }}',
                        type: "POST",
                        data: {'email_type' : value, '_token': "{{ csrf_token() }}"},
                        success: function (data) {
                            $('.email-preview_email-template').empty();
                            $.map(data.emailTemplates, function (v, i){
                                if (emailTemplateIds && emailTemplateIds != null){
                                    $('.email-preview_email-template').append(`<option value="${v.id}" ${v.id == emailTemplateIds ? "selected" : ""}>${v.template_name}</option>`);
                                }else {
                                    $('.email-preview_email-template').append(`<option value="${v.id}" ${i == 0 ? "selected" : ""}>${v.template_name}</option>`);
                                }
                            });
                            emailTemplateIds = null;

                            // Edit Modal in email template dropdown.
                            $('.edit-email-preview_email-template').empty();
                            $.map(data.emailTemplates, function (v, i){
                                $('.edit-email-preview_email-template').append(`<option value="${v.id}" ${i == 0 ? "selected" : ""}>${v.template_name}</option>`);
                            });

                            // Selected Email card in text change
                            $('.step-3-4-card.selected').find('.select-email-template-card-email-div').empty();
                            if(value == '1'){
                                $('.step-3-4-card.selected').find('.select-email-template-card-email-div').append(`
                                    <i class="icon-envelope fa-fw step-3-4-card-email-div-icon"></i>
                                   <h3 class="step-3-4-card-email-div-text" data-id="1"> Send an Email</h3>`);
                            }else {
                                $('.step-3-4-card.selected').find('.select-email-template-card-email-div').append(`<i class="icon-docs fa-fw step-3-4-card-email-div-icon"></i>
                                <h3 class="step-3-4-card-email-div-text" data-id="2"> Send File via Email</h3>`);
                            }

                            // Email template template name display in email template card
                            changeTemplateNameInTemplateCard();

                            // Add value in form
                            setTimeout(function(){
                                addValueInForm();
                            },500);
                        },
                        error: function (data){

                        }
                    });
                });

                // Client Dropdown and get project data
                $(document).on('change', '.client-dropdown', function (){
                    let clientId = $(this).val();
                    if(clientId != null && clientId != ''){
                        $.ajax({
                            url: '{{ route('admin.email-automation.get-projects') }}',
                            type: "POST",
                            data: {'client_id' : clientId, '_token': "{{ csrf_token() }}"},
                            success: function (data) {
                                if (data.status == 'success') {
                                    $('.project-dropdown').empty();
                                    $('.project-dropdown').append(`<option value="" selected>Select Project</option>`);
                                    if(data.projects) {
                                        $.map(data.projects, function (v, i) {
                                            if (editProjectId && editProjectId != null){
                                                $('.project-dropdown').append(`<option value="${i}" ${i == editProjectId ? "selected" : ""}>${v}</option>`);
                                            }else {
                                                $('.project-dropdown').append(`<option value="${i}">${v}</option>`);
                                            }
                                        });
                                        editProjectId = null;
                                    }
                                    let projectId = $('.project-dropdown').find(":selected").val();
                                    $('.step-3-4-card.selected').find('.store-edit-project-id').val(projectId);
                                }
                                if(data.status == 'fail'){
                                    $.showToastr(data.message, 'error');
                                }
                            },
                            error:function (data){
                                $.showToastr(data.responseJSON.message, 'error');
                            },
                        });
                    }else {
                        $('.project-dropdown').empty();
                        $('.project-dropdown').append(`<option value="" selected>Select Project</option>`);

                        let projectId = $('.project-dropdown').find(":selected").val();
                        $('.step-3-4-card.selected').find('.store-edit-project-id').val(projectId);

                        // Unchecked send manually button
                        $('.step-3-4-card.selected').next('.checkbox-automation-email-card').find('.get-store-edit-is-manual').attr('checked', false);
                    }
                    let getClientId = $('.step-3-4-card.selected').find('.store-edit-client-id').val(clientId);
                    let getProjectId = $('.step-3-4-card.selected').find('.store-edit-project-id').val();
                    setSendManuallyButtonValue(getClientId, getProjectId)
                });

                // Project dropdown
                $(document).on('change', '.project-dropdown', function (){
                    let projectId = $(this).val();
                    $('.step-3-4-card.selected').find('.store-edit-project-id').val(projectId);
                    let getClientId = $('.step-3-4-card.selected').find('.store-edit-client-id').val();
                    setSendManuallyButtonValue(getClientId, projectId)
                });

                $(document).on('change', '.get-store-edit-is-manual', function (){
                    if($(this).is(':checked')){
                        $('.step-3-4-card.selected').find('.store-edit-is-manual').val(1);
                    }else {
                        $('.step-3-4-card.selected').find('.store-edit-is-manual').val(0);
                    }
                });

                // Edit Modal in Email template dropdown
                $(document).on('change', '.edit-email-preview_email-template', function (){
                    let emailTemplateId = $(this).val();
                    editEmailPreviewModal(emailTemplateId);
                });

                $(document).on('click', '.edit-email-template-btn', function (){
                    let form = $('#editEmailTemplateForm');
                    $.ajax({
                        url: '{{ route('admin.email-automation.update.email.template') }}',
                        container: '#editEmailTemplateForm',
                        type: "POST",
                        data: new FormData(form[0]),
                        processData: false,
                        contentType: false,
                        success: function (data) {
                            if (data.status == 'success') {
                                $.showToastr(data.message, 'success');
                                $('.editEmailTemplate').modal('hide');
                                $('.editEmailTemplate').on('hidden.bs.modal', function () {
                                    $(this).find('form').trigger('reset');
                                    $(".edit-email-preview-body").summernote("code", "");
                                });
                            }
                            if(data.status == 'fail'){
                                $.showToastr(data.message, 'error');
                            }
                        },
                        error:function (data){
                            $.showToastr(data.responseJSON.message, 'error');
                        },
                    });
                });

                $(document).on('change', '.automation-event-name', function (){
                    let eventNameEle = $(this);
                    let timePeriodEle = $('.automation-time-period');
                    let timeUnitEle = $('.automation-time-unit');
                    let timeTypeEle = $('.automation-time-type');
                    let timePeriodEleVal = timePeriodEle.val();
                    let timeUnitEleVal = timeUnitEle.val();

                    if (timePeriodEle.val() == 0 || timePeriodEle.val() == ''){
                        timePeriodEle = 'Immediately ';
                        timeUnitEle = '';
                    }else {
                        timePeriodEle = 'Wait ' + timePeriodEle.val();
                        timeUnitEle = timeUnitEle.find(":selected").text();
                    }
                    $('.step-3-4-card.selected').find('.select-email-template-card-email-template-event-name').text(timePeriodEle +' '+ timeUnitEle +' '+ timeTypeEle.find(":selected").text() +' '+ eventNameEle.find(":selected").text());

                    // Add value in form
                    let actionTypeId =  $('.action-type-dropdown').val();
                    let emailTemplateId =  $('.email-preview_email-template').val();

                    $('.step-3-4-card.selected').find('.store-edit-action-type').val(actionTypeId);
                    $('.step-3-4-card.selected').find('.store-edit-email-template-id').val(emailTemplateId);
                    $('.step-3-4-card.selected').find('.store-edit-time-period').val(timePeriodEleVal);
                    $('.step-3-4-card.selected').find('.store-edit-time-unit').val(timeUnitEleVal);
                    $('.step-3-4-card.selected').find('.store-edit-time-type').val(timeTypeEle.val());
                    $('.step-3-4-card.selected').find('.store-edit-automation-event').val(eventNameEle.val());
                });


                $(document).on('change', '.automation-time-period', function (){
                    let eventNameEle = $('.automation-event-name');
                    let timePeriodEle = $(this);
                    let timeUnitEle = $('.automation-time-unit');
                    let timeTypeEle = $('.automation-time-type');
                    let timePeriodEleVal = timePeriodEle.val();
                    let timeUnitEleVal = timeUnitEle.val();

                    if (timePeriodEle.val() == 0 || timePeriodEle.val() == ''){
                        timePeriodEle = 'Immediately ';
                        timeUnitEle = '';
                    }else {
                        timePeriodEle = 'Wait ' + timePeriodEle.val();
                        timeUnitEle = timeUnitEle.find(":selected").text();
                    }

                    $('.step-3-4-card.selected').find('.select-email-template-card-email-template-event-name').text(timePeriodEle +' '+ timeUnitEle +' '+ timeTypeEle.find(":selected").text() +' '+ eventNameEle.find(":selected").text());

                    // Add value in form
                    let actionTypeId =  $('.action-type-dropdown').val();
                    let emailTemplateId =  $('.email-preview_email-template').val();

                    $('.step-3-4-card.selected').find('.store-edit-action-type').val(actionTypeId);
                    $('.step-3-4-card.selected').find('.store-edit-email-template-id').val(emailTemplateId);
                    $('.step-3-4-card.selected').find('.store-edit-time-period').val(timePeriodEleVal);
                    $('.step-3-4-card.selected').find('.store-edit-time-unit').val(timeUnitEleVal);
                    $('.step-3-4-card.selected').find('.store-edit-time-type').val(timeTypeEle.val());
                    $('.step-3-4-card.selected').find('.store-edit-automation-event').val(eventNameEle.val());
                });

                $(document).on('change', '.automation-time-unit', function (){
                    let eventNameEle = $('.automation-event-name');
                    let timePeriodEle = $('.automation-time-period');
                    let timeUnitEle = $(this);
                    let timeTypeEle = $('.automation-time-type');
                    let timePeriodEleVal = timePeriodEle.val();
                    let timeUnitEleVal = timeUnitEle.val();

                    if (timePeriodEle.val() == 0 || timePeriodEle.val() == ''){
                        timePeriodEle = 'Immediately ';
                        timeUnitEle = '';
                    }else {
                        timePeriodEle = 'Wait ' + timePeriodEle.val();
                        timeUnitEle = timeUnitEle.find(":selected").text();
                    }

                    $('.step-3-4-card.selected').find('.select-email-template-card-email-template-event-name').text(timePeriodEle +' '+ timeUnitEle +' '+ timeTypeEle.find(":selected").text() +' '+ eventNameEle.find(":selected").text());

                    // Add value in form
                    let actionTypeId =  $('.action-type-dropdown').val();
                    let emailTemplateId =  $('.email-preview_email-template').val();

                    $('.step-3-4-card.selected').find('.store-edit-action-type').val(actionTypeId);
                    $('.step-3-4-card.selected').find('.store-edit-email-template-id').val(emailTemplateId);
                    $('.step-3-4-card.selected').find('.store-edit-time-period').val(timePeriodEleVal);
                    $('.step-3-4-card.selected').find('.store-edit-time-unit').val(timeUnitEleVal);
                    $('.step-3-4-card.selected').find('.store-edit-time-type').val(timeTypeEle.val());
                    $('.step-3-4-card.selected').find('.store-edit-automation-event').val(eventNameEle.val());
                });

                $(document).on('change', '.automation-time-type', function (){
                    let eventNameEle = $('.automation-event-name');
                    let timePeriodEle = $('.automation-time-period');
                    let timeUnitEle = $('.automation-time-unit');
                    let timeTypeEle = $(this);
                    let timePeriodEleVal = timePeriodEle.val();
                    let timeUnitEleVal = timeUnitEle.val();

                    if (timePeriodEle.val() == 0 || timePeriodEle.val() == ''){
                        timePeriodEle = 'Immediately ';
                        timeUnitEle = '';
                    }else {
                        timePeriodEle = 'Wait ' + timePeriodEle.val();
                        timeUnitEle = timeUnitEle.find(":selected").text();
                    }

                    $('.step-3-4-card.selected').find('.select-email-template-card-email-template-event-name').text(timePeriodEle +' '+ timeUnitEle +' '+ timeTypeEle.find(":selected").text() +' '+ eventNameEle.find(":selected").text());

                    // Add value in form
                    let actionTypeId =  $('.action-type-dropdown').val();
                    let emailTemplateId =  $('.email-preview_email-template').val();

                    $('.step-3-4-card.selected').find('.store-edit-action-type').val(actionTypeId);
                    $('.step-3-4-card.selected').find('.store-edit-email-template-id').val(emailTemplateId);
                    $('.step-3-4-card.selected').find('.store-edit-time-period').val(timePeriodEleVal);
                    $('.step-3-4-card.selected').find('.store-edit-time-unit').val(timeUnitEleVal);
                    $('.step-3-4-card.selected').find('.store-edit-time-type').val(timeTypeEle.val());
                    $('.step-3-4-card.selected').find('.store-edit-automation-event').val(eventNameEle.val());
                });

                // Save automation form
                $(document).on('click', '.save-automation', function (){
                    let form = $('#createAutomationForm');
                    // Step Value
                    $('.email-automation-step').val($('.step-3-4-card').length);

                    let flag = false;
                    $('.step-3-4-card').each(function (){
                       let checkedValidation = $(this).find('.email-template-card-validation').val();
                        if(checkedValidation == null || checkedValidation == ''){
                            $.showToastr('Please choose template', 'error');
                            $(this).addClass('step-3-in-valid');
                            flag = false;
                        }else {
                            flag = true;
                        }
                    });

                    // Client dropdown
                    let projectFlag = false;
                    $('.step-3-4-card').each(function (){
                       let clientIdSelected = $(this).find('.store-edit-client-id').val();
                        if(clientIdSelected){
                            let projectId = $(this).find('.store-edit-project-id').val();
                            if(projectId == null || projectId == ''){
                                $.showToastr('Please select project', 'error');
                                $(this).addClass('step-3-in-valid');
                                projectFlag = false;
                            } else {
                                projectFlag = true;
                            }
                        }else {
                            projectFlag = true;
                        }
                    });
                    setTimeout(function (){
                        $('.step-3-4-card').removeClass('step-3-in-valid');
                    }, 5000);
                    if(flag == false || projectFlag == false){
                        return  false;
                    }

                    // Email Template
                    let eleText = $('.email-preview_email-template').find(":selected").text();
                    if(eleText == null || eleText == ''){
                        $.showToastr('Email template field is required', 'error');
                        return false;
                    }

                    let getAutomationName  = $('.automation-header-name').val();
                    if(getAutomationName == null || getAutomationName == ''){
                        $.showToastr('Please enter automation name', 'error');
                        $('.automation-header-name').focus();

                        return false;
                    }else {
                        $('.automation_name').val(getAutomationName);
                    }
                    $.ajax({
                        url: '{{ route('admin.email-automation.store') }}',
                        container: '#createAutomationForm',
                        type: "POST",
                        data: new FormData(form[0]),
                        processData: false,
                        contentType: false,
                        success: function (data) {
                            if (data.status == 'success') {
                                $.showToastr(data.message, 'success');
                                setTimeout( function (){
                                    window.location.href = '{{ route('admin.email-automation.index') }}';
                                }, 2000);
                            }
                            if(data.status == 'fail'){
                                $.showToastr(data.message, 'error');
                            }
                        },
                        error:function (data){
                            $.showToastr(data.responseJSON.message, 'error');
                        },
                    });
                });
            });
        });

    </script>
@endpush