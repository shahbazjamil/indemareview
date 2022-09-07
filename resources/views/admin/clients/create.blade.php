@extends('layouts.app')
<style>
	.align-items-start{align-items:flex-start !important;}
	#shipping_address, #address, .bootstrap-tagsinput{height:130px;}
	span.tag.label.label-info {white-space: normal;max-width: 100%;}
	#note{height:220px;}
</style>
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
                <li><a href="{{ route('admin.clients.index') }}">{{ __($pageTitle) }}</a></li>
                <li class="active">@lang('app.addNew')</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
@endpush

@section('content')

    <div class="row">
        <div class="col-md-12">

            <div class="panel panel-inverse">
                <!--<div class="panel-heading p-t-10 p-b-10"> @lang('modules.client.createTitle')</div>-->
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body p-0">
                        {!! Form::open(['id'=>'createClient','class'=>'ajax-form','method'=>'POST']) !!}
                        @if(isset($leadDetail->id))
                            <input type="hidden" name="lead" value="{{ $leadDetail->id }}">
                        @endif
                            <div class="form-body">
                                <!--<h3 class="box-title border-bottom m-t-20">@lang('modules.client.clientDetails')</h3>-->
                                <h3 class="box-title border-bottom p-t-10 p-b-10">Account Information</h3>
                                <div class="row flex-row flex-wrap align-items-start">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="required">@lang('modules.client.clientName')</label>
                                            <input type="text" name="name" id="name"  value="{{ $leadDetail->client_name ?? '' }}"   class="form-control">
                                        </div>
                                    </div><!--end of col-3-->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="required">@lang('modules.client.clientEmail')</label>
                                            <input type="email" name="email" id="email" value="{{ $leadDetail->client_email ?? '' }}"  class="form-control">
                                            <span class="help-block d-none">@lang('modules.client.emailNote')</span>
                                        </div>
                                    </div><!--end of col-3-->
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <div style="margin-bottom: 10px;">
                                                <label class="control-label">@lang('modules.client.sendCredentials')</label>
                                                <a class="mytooltip" href="javascript:void(0)"> <i class="fa fa-info-circle"></i><span class="tooltip-content5"><span class="tooltip-text3"><span class="tooltip-inner2">@lang('modules.client.sendCredentialsMessage')</span></span></span></a>
                                            </div>
                                            <div class="radio radio-inline col-md-4">
                                                <input type="radio" name="sendMail" id="sendMail1"
                                                       value="yes">
                                                <label for="sendMail1" class="">
                                                    @lang('app.yes') </label>
                                            </div>
                                            <div class="radio radio-inline col-md-4">
                                                <input type="radio" name="sendMail"
                                                       id="sendMail2" checked value="no">
                                                <label for="sendMail2" class="">
                                                    @lang('app.no') </label>
                                            </div>
                                        </div>
                                    </div><!--end of col-2-->
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <div class="m-b-10">
                                                <label class="control-label">@lang('modules.emailSettings.emailNotifications')</label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <input type="radio"  name="email_notifications" id="email_notifications1" value="1">
                                                <label for="email_notifications1" class="">
                                                    @lang('app.enable') </label>
    
                                            </div>
                                            <div class="radio radio-inline ">
                                                <input checked="" type="radio" checked name="email_notifications"
                                                       id="email_notifications2" value="0">
                                                <label for="email_notifications2" class="">
                                                    @lang('app.disable') </label>
                                            </div>
                                        </div>
                                    </div><!--end of col-2-->
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <div class="m-b-10">
                                                <label class="control-label">CC Payments on Portal</label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <input type="radio"  name="payments_on_portal" id="payments_on_portal1" value="1">
                                                <label for="payments_on_portal1" class="">
                                                    @lang('app.enable') </label>
    
                                            </div>
                                            <div class="radio radio-inline ">
                                                <input type="radio" checked name="payments_on_portal"
                                                       id="payments_on_portal2" value="0">
                                                <label for="payments_on_portal2" class="">
                                                    @lang('app.disable') </label>
                                            </div>
                                        </div>
                                    </div><!--end of col-2-->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>@lang('modules.client.mobile')</label>
                                            <input type="tel" name="mobile" id="mobile" value="{{ $leadDetail->mobile ?? '' }}" class="form-control">
                                        </div><!--end of form-group-->
										<div class="form-group">
											<label>@lang('app.shippingAddress')</label>
											<textarea name="shipping_address" id="shipping_address" class="form-control" rows="4"></textarea>
										</div><!--end of form-group-->
                                    </div><!--end of col-3-->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="">Secondary Email</label>
                                            <input type="email" name="secondary_email" id="secondary_email" value=""  class="form-control">
                                            <span class="help-block d-none">@lang('modules.client.emailNote')</span>
                                        </div><!--end of form-group-->
										<div class="form-group">
                                            <label class="control-label">Billing @lang('app.address')</label>
                                            <textarea name="address"  id="address"  rows="5"  class="form-control">{{ $leadDetail->address ?? '' }}</textarea>
                                        </div><!--end of form-group-->
                                    </div><!--end of col-3-->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="">Sales Codes</label>
                                            <input type="text" name="sales_code" id="sales_code"  value=""   class="form-control">
                                            
                                        </div><!--end of form-group-->										
										<div class="form-group">
											<label class="control-label">Tags</label>
											<select multiple data-role="tagsinput" name="tags[]" id="tags">
											</select>
										</div><!--end of form-group-->
                                    </div><!--end of col-3-->
                                    <div class="col-md-3">
                                        <label>Preliminary @lang('app.note')s:</label>
                                        <div class="form-group">
                                            <textarea name="note" id="note" class="form-control" rows="5"></textarea>
                                        </div>
                                    </div>
                                </div>								
                                <h3 class="box-title border-bottom m-t-20">Additional Information</h3>
								<div  class="row">									
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="">Referred By</label>
                                            <input type="text" name="reffered_by" id="reffered_by"  value=""   class="form-control">
                                        </div>
                                    </div>
<!--                                                                    end of col-3-->
                                    <div class="col-md-3 d-none">
                                        <div class="form-group">
                                            <label class="required">Default Tax % For Products</label>
                                            <input type="text" name="product_default_tax" id="product_default_tax"  value=""   class="form-control">
                                        </div>
                                    </div>
<!--end of col-3-->
								</div>
<!--                                end of row-->
                               
                                
                                
                              
                                <!--/row-->

                                
                                
                                <!--/row-->
                                <div class="row" style="display:none;">

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Skype</label>
                                            <input type="text" name="skype" id="skype" class="form-control">
                                        </div>
                                    </div>
                                    <!--/span-->

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Linkedin</label>
                                            <input type="text" name="linkedin" id="linkedin" class="form-control">
                                        </div>
                                    </div>
                                    <!--/span-->

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Twitter</label>
                                            <input type="text" name="twitter" id="twitter" class="form-control">
                                        </div>
                                    </div>
                                    <!--/span-->

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Facebook</label>
                                            <input type="text" name="facebook" id="facebook" class="form-control">
                                        </div>
                                    </div>
                                    <!--/span-->
                                </div>
                               

                                <div class="row">
                                    @if(isset($fields))
                                        @foreach($fields as $field)
                                            <div class="col-md-6">
                                                <label>{{ ucfirst($field->label) }}</label>
                                                <div class="form-group">
                                                    @if( $field->type == 'text')
                                                        <input type="text" name="custom_fields_data[{{$field->name.'_'.$field->id}}]" class="form-control" placeholder="{{$field->label}}" value="{{$editUser->custom_fields_data['field_'.$field->id] ?? ''}}">
                                                    @elseif($field->type == 'password')
                                                        <input type="password" name="custom_fields_data[{{$field->name.'_'.$field->id}}]" class="form-control" placeholder="{{$field->label}}" value="{{$editUser->custom_fields_data['field_'.$field->id] ?? ''}}">
                                                    @elseif($field->type == 'number')
                                                        <input type="number" name="custom_fields_data[{{$field->name.'_'.$field->id}}]" class="form-control" placeholder="{{$field->label}}" value="{{$editUser->custom_fields_data['field_'.$field->id] ?? ''}}">

                                                    @elseif($field->type == 'textarea')
                                                        <textarea name="custom_fields_data[{{$field->name.'_'.$field->id}}]" class="form-control" id="{{$field->name}}" cols="3">{{$editUser->custom_fields_data['field_'.$field->id] ?? ''}}</textarea>

                                                    @elseif($field->type == 'radio')
                                                        <div class="radio-list">
                                                            @foreach($field->values as $key=>$value)
                                                                <label class="radio-inline @if($key == 0) p-0 @endif">
                                                                    <div class="radio radio-info">
                                                                        <input type="radio" name="custom_fields_data[{{$field->name.'_'.$field->id}}]" id="optionsRadios{{$key.$field->id}}" value="{{$value}}" @if(isset($clientDetail) && $clientDetail->custom_fields_data['field_'.$field->id] == $value) checked @elseif($key==0) checked @endif>>
                                                                        <label for="optionsRadios{{$key.$field->id}}">{{$value}}</label>
                                                                    </div>
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                    @elseif($field->type == 'select')
                                                        {!! Form::select('custom_fields_data['.$field->name.'_'.$field->id.']',
                                                                $field->values,
                                                                 isset($editUser)?$editUser->custom_fields_data['field_'.$field->id]:'',['class' => 'form-control gender'])
                                                         !!}

                                                    @elseif($field->type == 'checkbox')
                                                        <div class="mt-checkbox-inline">
                                                            @foreach($field->values as $key => $value)
                                                                <label class="mt-checkbox mt-checkbox-outline">
                                                                    <input name="custom_fields_data[{{$field->name.'_'.$field->id}}][]" type="checkbox" value="{{$key}}"> {{$value}}
                                                                    <span></span>
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                    @elseif($field->type == 'date')
                                                        <input type="text" class="form-control form-control-inline date-picker" size="16" name="custom_fields_data[{{$field->name.'_'.$field->id}}]"
                                                                value="{{ isset($editUser->dob)?Carbon\Carbon::parse($editUser->dob)->format('Y-m-d'):Carbon\Carbon::now()->format($global->date_format)}}">
                                                    @endif
                                                    <div class="form-control-focus"> </div>
                                                    <span class="help-block"></span>

                                                </div>
                                            </div>
                                        @endforeach
                                    @endif

                                </div>
                               
                                
                                
                                
                                
                                
                            </div>
                            <div class="form-actions">
                                <button type="submit" id="save-form" class="btn btn-success"> <i class="fa fa-check"></i> @lang('app.save')</button>

                            </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>    <!-- .row -->

@endsection

@push('footer-script')
<script src="{{ asset('plugins/bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>

<script>
    $(".date-picker").datepicker({
        todayHighlight: true,
        autoclose: true,
        weekStart:'{{ $global->week_start }}',
        format: '{{ $global->date_picker_format }}',
    });

    $('#save-form').click(function () {
        $.easyAjax({
            url: '{{route('admin.clients.store')}}',
            container: '#createClient',
            type: "POST",
            redirect: true,
            data: $('#createClient').serialize()
        })
    });
</script>
@endpush

