@extends('layouts.app')
<style>
	textarea#address {
		height: 140px !important;
	}
	.border-top {
		border-top: 1px solid rgb(227, 227, 227);
	}
</style>
@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="border-bottom col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-6 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li><a href="{{ route('admin.leads.index') }}">{{ __($pageTitle) }}</a></li>
                <li class="active">@lang('app.addNew')</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.css') }}">

@endpush

@section('content')

    <div class="row">
        <div class="col-md-12">

            <div class="panel panel-inverse">
                <!--<div class="panel-heading"> @lang('modules.lead.createTitle')</div>-->
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body p-0">
                        {!! Form::open(['id'=>'createLead','class'=>'ajax-form','method'=>'POST']) !!}
                            <div class="form-body">
                                <div class="row">
									<div class="col-md-6">
										<h3 class="box-title border-bottom p-t-10 p-b-10">LEAD INFORMATION</h3>
										<div class="row">											
											<div class="col-md-6">
												<div class="form-group">
													<label>Current Lead Status</label>
													<input type="text" name="lead_status" id="lead_status"  class="form-control">
												</div>
											</div>											
											<div class="col-md-6">
												<div class="form-group">
													<label>Sales Code</label>
													<input type="text" name="sales_code" id="sales_code"  class="form-control">
												</div>
											</div>								
											<div class="col-md-6">
												<div class="form-group">
													<label for="">@lang('modules.tickets.chooseAgents') 
														<a href="javascript:;"  id="addLeadAgent"
																										   class="btn btn-sm btn-outline btn-success">+ @lang('app.add') @lang('app.leadAgent')</a></label>
													<select class="select2 form-control" data-placeholder="@lang('modules.tickets.chooseAgents')" id="agent_id" name="agent_id">
														<option value="">@lang('modules.tickets.chooseAgents')</option>
														@foreach($leadAgents as $emp)
															<option value="{{ $emp->id }}">{{ ucwords($emp->user->name) }} @if($emp->user->id == $user->id)
																	(YOU) @endif</option>
														@endforeach
													</select>
												</div>
											</div>										
											<div class="col-md-6">
												<div class="form-group">
													<label for="">@lang('modules.lead.leadSource')  <a href="javascript:;"
																									   id="addLeadsource"
																									   class="btn btn-sm btn-outline btn-success">+ @lang('app.add') @lang('modules.lead.leadSource')</a></label>
													<select class="select2 form-control" data-placeholder="@lang('modules.lead.leadSource')"  id="source_id" name="source_id">
														@foreach($sources as $source)
															<option value="{{ $source->id }}">{{ ucwords($source->type) }}</option>
														@endforeach
													</select>
												</div>
											</div>										
											<div class="col-md-6">
												<div class="form-group">
													<label>Lead Value</label>
													<input type="text" name="lead_value" id="lead_value"  class="form-control">
												</div>
											</div>
										</div><!--end of row-->
									</div><!--end of col-6-->
									<div class="col-md-6">
										<h3 class="box-title border-bottom p-t-10 p-b-10">Lead Address</h3>
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label class="control-label">Shipping @lang('app.address')</label>
													<textarea name="shipping_address"  id="shipping_address"  rows="5" class="form-control"></textarea>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label class="control-label">Billing @lang('app.address')</label>
													<textarea name="address"  id="address"  rows="5" class="form-control"></textarea>
												</div>
											</div>
										</div><!--end of row-->
									</div><!--end of col-6-->
								</div><!--end of row-->
								<div class="row">
									<div class="col-md-6">
										<h3 class="box-title border-bottom p-t-10 p-b-10 border-top">CONTACTS</h3>
										<div class="row flex-row flex-wrap">											
											<div class="col-md-6">
												<div class="form-group">
													<label>Person 1 Name</label>
													<input type="text" name="client_name" id="client_name"  class="form-control">
												</div>
											</div>

											<div class="col-md-6">
												<div class="form-group">
													<label>Person 1 Email</label>
													<input type="email" name="client_email" id="client_email"  class="form-control">
													
												</div>
											</div>

											<div class="col-md-6">
												<div class="form-group">
													<label>Person 1 Phone</label>
													<input type="tel" name="mobile" id="mobile" class="form-control">
												</div>
											</div>	
											<div class="col-md-6"></div>
											<div class="col-md-6">
												<div class="form-group">
													<label>Person 2 Name</label>
													<input type="text" name="person_name_2" id="person_name_2"  class="form-control">
												</div>
											</div>

											<div class="col-md-6">
												<div class="form-group">
													<label>Person 2 Email</label>
													<input type="email" name="person_email_2" id="person_email_2"  class="form-control">
													
												</div>
											</div>

											<div class="col-md-6">
												<div class="form-group">
													<label>Person 2 Phone</label>
													<input type="tel" name="person_mobile_2" id="person_mobile_2" class="form-control">
												</div>
											</div>			
										</div><!--end of row-->
									</div><!--end of col-6-->
									<div class="col-md-6">
										<h3 class="box-title border-bottom p-t-10 p-b-10 border-top">LOT INFORMATION</h3>
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label class="control-label">Lot Address</label>
													<textarea name="lot_address"  id="lot_address"  rows="5" class="form-control"></textarea>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label>Square Feet</label>
													<input type="text" name="square_feet" id="square_feet" class="form-control">
												</div>
												<div class="form-group">
													<label>Gate Code</label>
													<input type="text" name="gate_code" id="gate_code" class="form-control">
												</div>
											</div>	
										</div><!--end of row-->
									</div><!--end of col-6-->
								</div><!--end of row-->
                                                                
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
                                                                                            value="{{ \Carbon\Carbon::today()->format($global->date_format) }}">
                                                                                @endif
                                                                                <div class="form-control-focus"> </div>
                                                                                <span class="help-block"></span>

                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                @endif

                                                            </div>

                                

                                
                                
                                <div class="col-md-12 d-none">
                                    <div class="form-group">
                                        <label class="control-label">Tags</label>
                                        <select multiple data-role="tagsinput" name="tags[]" id="tags">
                                        </select>
                                    </div>
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
    {{--Ajax Modal--}}
    <div class="modal fade bs-modal-md in" id="projectCategoryModal" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-md" id="modal-data-application">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading"></span>
                </div>
                <div class="modal-body">
                    Loading...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn blue">Save changes</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    {{--Ajax Modal Ends--}}
@endsection

@push('footer-script')
<script src="{{ asset('plugins/bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script>
    $('#createLead').on('click', '#addLeadAgent', function () {
        var url = '{{ route('admin.lead-agent-settings.create')}}';
        $('#modelHeading').html('Manage Lead Agent');
        $.ajaxModal('#projectCategoryModal', url);
    })


    $(".select2").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });
    $(".date-picker").datepicker({
        todayHighlight: true,
        autoclose: true,
        weekStart:'{{ $global->week_start }}',
        format: '{{ $global->date_picker_format }}',
    });

    $('#createLead').on('click', '#addLeadsource', function () {
        var url = '{{ route('admin.lead-source-settings.create')}}';
        $('#modelHeading').html('Manage Lead Source');
        $.ajaxModal('#projectCategoryModal', url);
    })

    $('#save-form').click(function () {
        $.easyAjax({
            url: '{{route('admin.leads.store')}}',
            container: '#createLead',
            type: "POST",
            redirect: true,
            data: $('#createLead').serialize()
        })
    });

</script>
@endpush

