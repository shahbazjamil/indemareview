@extends('layouts.app')

@section('page-title')
    <div class="row bg-title p-b-0">
        <!-- .page title -->
        <div class="border-bottom col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li class="active">{{ __($pageTitle) }}</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
@endpush

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-inverse">
                <div class="panel-heading p-t-10 p-b-10">@lang('modules.accountSettings.updateTitle')</div>

                <div class="vtabs customvtab m-t-10">

                    @include('sections.admin_setting_menu')

                    <div class="tab-content p-0 p-t-20">
                        <div id="vhome3" class="tab-pane active">
                            <div class="row">
                                <div class="col-sm-12 col-xs-12">
                                    {!! Form::open(['id'=>'editSettings','class'=>'ajax-form','method'=>'PUT']) !!}
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="company_name">@lang('modules.accountSettings.companyName')</label>
                                                <input type="text" class="form-control" id="company_name" name="company_name"
                                                       value="{{ $global->company_name }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="company_email">@lang('modules.accountSettings.companyEmail')</label>
                                                <input readonly="" type="email" class="form-control" id="company_email" name="company_email"
                                                       value="{{ $global->company_email }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="company_phone">@lang('modules.accountSettings.companyPhone')</label>
                                                <input type="tel" class="form-control" id="company_phone" name="company_phone"
                                                       value="{{ $global->company_phone }}">
                                            </div>
                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="exampleInputPassword1">@lang('modules.accountSettings.companyLogo')</label>

                                                <div class="col-md-12">
                                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                                        <div class="fileinput-new thumbnail"
                                                             style="width: 150px; height: 100px;">
                                                            <img src="{{ $global->logo_url }}"
                                                                 alt=""/>
                                                        </div>
                                                        <div class="fileinput-preview fileinput-exists thumbnail"
                                                             style="max-width: 150px; max-height: 100px;"></div>
                                                        <div>
                                <span class="btn btn-info  btn-file">
                                    <span class="fileinput-new"> @lang('app.selectImage') </span>
                                    <span class="fileinput-exists"> @lang('app.change') </span>
                                    <input type="file" name="logo" id="logo"> </span>
                                                            <a href="javascript:;" class="btn btn-danger fileinput-exists"
                                                               data-dismiss="fileinput"> @lang('app.remove') </a>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="exampleInputPassword1">@lang('modules.accountSettings.companyWebsite')</label>
                                                <input type="text" class="form-control" id="website" name="website"
                                                       value="{{ $global->website }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="address">@lang('modules.accountSettings.companyAddress')</label>
                                                <textarea class="form-control" id="address" rows="5"
                                                          name="address">{{ $global->address }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    @if(module_enabled('Subdomain'))
                                        <div class="row">
                                        <div class="col-sm-12 col-md-4 col-xs-12">
                                            <div class="form-group">
                                                <label>@lang('modules.themeSettings.loginScreenBackground')</label>

                                                <div class="col-md-12 m-b-20">
                                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                                        <div class="fileinput-new thumbnail"
                                                             style="width: 200px; height: 150px;">
                                                            <img src="{{ $company->login_background_url }}" alt=""/>
                                                        </div>
                                                        <div class="fileinput-preview fileinput-exists thumbnail"
                                                             style="max-width: 200px; max-height: 150px;"></div>
                                                        <div>
                                    <span class="btn btn-info btn-file">
                                    <span class="fileinput-new"> @lang('app.selectImage') </span>
                                    <span class="fileinput-exists"> @lang('app.change') </span>
                                    <input type="file" name="login_background" id="login_background"> </span>
                                                            <a href="javascript:;" class="btn btn-danger fileinput-exists"
                                                               data-dismiss="fileinput"> @lang('app.remove') </a>
                                                        </div>
                                                    </div>
                                                    <div class="note">Recommended size: 1500 X 1056 (Pixels)</div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @endif
                                    <div class="row m-t-40">
                                        <hr>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="address">@lang('modules.accountSettings.defaultCurrency')</label>
                                                <select name="currency_id" id="currency_id" class="form-control">
                                                    @foreach($currencies as $currency)
                                                        <option
                                                                @if($currency->id == $global->currency_id) selected @endif
                                                        value="{{ $currency->id }}">{{ $currency->currency_symbol.' ('.$currency->currency_code.')' }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="address">@lang('modules.accountSettings.defaultTimezone')</label>
                                                <select name="timezone" id="timezone" class="form-control select2">
                                                    @foreach($timezones as $tz)
                                                        <option @if($global->timezone == $tz) selected @endif>{{ $tz }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div style="display: none;" class="col-md-3">
                                            <div class="form-group">
                                                <label for="address">@lang('modules.accountSettings.dateFormat')</label>
                                                <select name="date_format" id="date_format" class="form-control select2">
                                                    <option value="d, M Y" @if($global->date_format == 'd, M Y') selected @endif >d, M Y ({{ $dateObject->format('d, M Y') }}) </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="address">@lang('modules.accountSettings.timeFormat')</label>
                                                <select name="time_format" id="time_format" class="form-control select2">
                                                    <option value="h:i A" @if($global->time_format == 'H:i A') selected @endif >12 Hour  (6:20 PM) </option>
                                                    <option value="h:i a" @if($global->time_format == 'H:i a') selected @endif >12 Hour  (6:20 pm) </option>
                                                    <option value="H:i" @if($global->time_format == 'H:i') selected @endif >24 Hour  (18:20) </option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="address">@lang('modules.accountSettings.weekStartFrom')</label>
                                                <select name="week_start" id="week_start" class="form-control select2">
                                                    <option value="0" @if($global->week_start == '0') selected @endif >Sunday</option>
                                                    <option value="1" @if($global->week_start == '1') selected @endif>Monday </option>
                                                    <option value="2" @if($global->week_start == '2') selected @endif>Tuesday</option>
                                                    <option value="3" @if($global->week_start == '3') selected @endif>Wednesday</option>
                                                    <option value="4" @if($global->week_start == '4') selected @endif>Thursday</option>
                                                    <option value="5" @if($global->week_start == '5') selected @endif>Friday</option>
                                                    <option value="6" @if($global->week_start == '6') selected @endif>Saturday</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="address">@lang('modules.accountSettings.changeLanguage')</label>
                                                <select name="locale" id="locale" class="form-control select2">
                                                    <option @if($global->locale == "en") selected @endif value="en">English
                                                    </option>
                                                    @foreach($languageSettings as $language)
                                                        <option value="{{ $language->language_code }}" @if($global->locale == $language->language_code) selected @endif >{{ $language->language_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="show_filters">Show Filters <a href='javascript:void(0)' class=""
                      data-toggle="tooltip" data-original-title="Show or hide page filters throughout"><i class="fa fa-question" aria-hidden="true"></i></a></label>
                                                <select name="show_filters" id="show_filters" class="form-control">
                                                    <option @if($global->show_filters == "0") selected @endif value="0">No</option>
                                                    <option @if($global->show_filters == "1") selected @endif value="1">Yes</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div style="display: none;" class="col-md-3">
                                            <div class="form-group">
                                                <label for="latitude">@lang('app.latitude')</label>
                                                <input type="text" class="form-control" id="latitude" name="latitude"
                                                       value="{{ $global->latitude }}">
                                            </div>
                                        </div>
                                        <div style="display: none;" class="col-md-3">
                                            <div class="form-group">
                                                <label for="longitude">@lang('app.longitude')</label>
                                                <input type="text" class="form-control" id="longitude" name="longitude"
                                                       value="{{ $global->longitude }}">
                                            </div>
                                        </div>
                                    </div>
                                    




                                    <button type="submit" id="save-form"
                                            class="btn btn-success waves-effect waves-light m-r-10">
                                        @lang('app.update')
                                    </button>

                                    {!! Form::close() !!}
                                </div>
                            </div>

                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>


    </div>
    <!-- .row -->
    
    
<!--<option value="d-m-Y" @if($global->date_format == 'd-m-Y') selected @endif >d-m-Y ({{ $dateObject->format('d-m-Y') }}) </option>
<option value="m-d-Y" @if($global->date_format == 'm-d-Y') selected @endif >m-d-Y ({{ $dateObject->format('m-d-Y') }}) </option>
<option value="Y-m-d" @if($global->date_format == 'Y-m-d') selected @endif >Y-m-d ({{ $dateObject->format('Y-m-d') }}) </option>
<option value="d.m.Y" @if($global->date_format == 'd.m.Y') selected @endif >d.m.Y ({{ $dateObject->format('d.m.Y') }}) </option>
<option value="m.d.Y" @if($global->date_format == 'm.d.Y') selected @endif >m.d.Y ({{ $dateObject->format('m.d.Y') }}) </option>
<option value="Y.m.d" @if($global->date_format == 'Y.m.d') selected @endif >Y.m.d ({{ $dateObject->format('Y.m.d') }}) </option>
<option value="d/m/Y" @if($global->date_format == 'd/m/Y') selected @endif >d/m/Y ({{ $dateObject->format('d/m/Y') }}) </option>
<option value="m/d/Y" @if($global->date_format == 'm/d/Y') selected @endif >m/d/Y ({{ $dateObject->format('m/d/Y') }}) </option>
<option value="Y/m/d" @if($global->date_format == 'Y/m/d') selected @endif >Y/m/d ({{ $dateObject->format('Y/m/d') }}) </option>
<option value="d-M-Y" @if($global->date_format == 'd-M-Y') selected @endif >d-M-Y ({{ $dateObject->format('d-M-Y') }}) </option>
<option value="d/M/Y" @if($global->date_format == 'd/M/Y') selected @endif >d/M/Y ({{ $dateObject->format('d/M/Y') }}) </option>
<option value="d.M.Y" @if($global->date_format == 'd.M.Y') selected @endif >d.M.Y ({{ $dateObject->format('d.M.Y') }}) </option>
<option value="d-M-Y" @if($global->date_format == 'd-M-Y') selected @endif >d-M-Y ({{ $dateObject->format('d-M-Y') }}) </option>
<option value="d M Y" @if($global->date_format == 'd M Y') selected @endif >d M Y ({{ $dateObject->format('d M Y') }}) </option>=
<option value="d F, Y" @if($global->date_format == 'd F, Y') selected @endif >d F, Y ({{ $dateObject->format('d F, Y') }}) </option>
<option value="D/M/Y" @if($global->date_format == 'D/M/Y') selected @endif >D/M/Y ({{ $dateObject->format('D/M/Y') }}) </option>
<option value="D.M.Y" @if($global->date_format == 'D.M.Y') selected @endif >D.M.Y ({{ $dateObject->format('D.M.Y') }}) </option>
<option value="D-M-Y" @if($global->date_format == 'D-M-Y') selected @endif >D-M-Y ({{ $dateObject->format('D-M-Y') }}) </option>
<option value="D M Y" @if($global->date_format == 'D M Y') selected @endif >D M Y ({{ $dateObject->format('D M Y') }}) </option>
<option value="d D M Y" @if($global->date_format == 'd D M Y') selected @endif >d D M Y ({{ $dateObject->format('d D M Y') }}) </option>
<option value="D d M Y" @if($global->date_format == 'D d M Y') selected @endif >D d M Y ({{ $dateObject->format('D d M Y') }}) </option>
<option value="dS M Y" @if($global->date_format == 'dS M Y') selected @endif >dS M Y ({{ $dateObject->format('dS M Y') }}) </option>-->

@endsection




@push('footer-script')
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>


<script>
    $(".select2").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });

    $('#save-form').click(function () {
        $.easyAjax({
            url: '{{route('admin.settings.update', [$global->id])}}',
            container: '#editSettings',
            type: "POST",
            redirect: true,
            file: true
        })
    });

</script>

@endpush

