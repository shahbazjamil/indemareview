@extends('layouts.app')
<style>
	.border-top {
		border-top: 1px solid rgb(227, 227, 227);
	}
	.bootstrap-tagsinput{min-height:40px;line-height:26px !important;display:flex !important;flex-wrap:wrap}.bootstrap-tagsinput input{height:26px}
	textarea#company_address{height:130px;}
</style>
@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="border-bottom col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> Vendors </h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('member.dashboard') }}">@lang('app.menu.home')</a></li>
                <li><a href="{{ route('member.clients.index') }}">{{ __($pageTitle) }}</a></li>
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
                @include('common.errors')
                <!--<div class="panel-heading p-b-10"> ADD VENDOR INFO</div>-->
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body p-0">
                        {!! Form::open(['id'=>'createClientVendor','class'=>'ajax-form']) !!}
                        {{--{{csrf_field()}}--}}
                        {{--{{method_field('PATCH')}}--}}
                        <div class="form-body">
							<div class="row">
								<div class="col-md-6">
									<h3 class="box-title border-bottom">COMPANY INFORMATION</h3>
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label class="control-label required">Company Name</label>
												<input type="text" required id="company_name" name="company_name"
													   class="form-control"
													   value="">
											</div>
										</div>
										<!--/span-->
										<div class="col-md-6">
											<div class="form-group">
												<label for="status">Vendor Status</label>
												<select class="form-control select2" name="status" id="status"
														data-style="form-control">
													<option value="active">Active</option>
													<option value="inactive">InActive</option>
												</select>
											</div>
											
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label for="vendor_category">Category of Goods</label>
												<input type="text" id="vendor_category" name="vendor_category" class="form-control" value="">
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label for="">PO Email <span>(If this goes to your rep, use the rep email)</span></label>
												<input type="email" id="po_email" name="po_email" class="form-control" value="">
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label for="status">Default Shipping Via</label>
												<input type="text" id="shipping_via" name="shipping_via" class="form-control" value="">
											</div>											
											<div class="form-group">
												<label for="">Account Number</label>
                                                                                                <input type="text" name="account_number" id="" account_number="" class="form-control" value="">
											</div>
										</div>										
										<div class="col-md-6">
											<div class="form-group">
												<label class="control-label">Billing @lang('app.address')</label>
												<textarea name="company_address" id="company_address" rows="5" class="form-control"></textarea>
											</div>
										</div>										
										<div class="col-md-6">
											<div class="form-group">
												<label for="vendor_markup">Default Markup %</label>
												<input type="text" id="vendor_markup" name="vendor_markup" class="form-control" value="">
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label class="control-label">Vendor Tags</label>
												<select multiple data-role="tagsinput" name="tags[]" id="tags">
												</select>
											</div>
										</div> 
                                                                                
                                                                                
										<!--/span-->
									</div>
								</div><!--end of col-6-->
								<div class="col-md-6">
									<h3 class="box-title border-bottom">VENDOR REP INFORMATION</h3>
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label class="">Rep Name</label>
												<input type="text"  name="vendor_rep_name" id="vendor_rep_name"
													   class="form-control" value="">
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label class="">Rep Email</label>
												<input type="email"  name="rep_email" id="rep_email" class="form-control"
													   value="">
												<span style="display: none;" class="help-block">Vendor will login using this email.</span>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label class="">Rep Phone Number</label>
												<input type="tel"  name="rep_phone" id="rep_phone" value=""
													   class="form-control">
											</div>
										</div>
									</div><!--end of row-->
									<h3 class="box-title border-bottom border-top">VENDOR WEBSITE LOGIN DETAILS</h3>
									<div class="row">										
										<div class="col-md-6">
											<div class="form-group">
												<label class="" for="url">URL of Sign In Page</label>
												<input type="text"  name="url" id="url" value=""
													   class="form-control">
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label class="" for="user">User Name</label>
												<input type="text" name="user" id="user" value=""
													   class="form-control">
											</div>
										</div>										
										<div class="col-md-6">
											<div class="form-group">
												<label for="password">Password</label>
												<input type="text" name="password" id="password" value=""
													   class="form-control">
											</div>
										</div>
									</div><!--end of row-->
								</div><!--end of col-6-->
                                <div class="col-md-12">
                                    <label>Vendor @lang('app.note')s</label>
                                    <div class="form-group">
                                        <textarea name="note" id="note" class="form-control" rows="5"></textarea>
                                    </div>
                                </div>
							</div><!--end of row-->
                            <!--/row-->
                            
                            
                            <!--/row-->

                            
                           
                            
                            
                            
                            
                            
                            <!--/row-->
                            
                            <!--/row-->
                            

                            <div class="row">
                                @if(isset($fields))
                                    @foreach($fields as $field)
                                        <div class="col-md-6">
                                            <label>{{ ucfirst($field->label) }}</label>
                                            <div class="form-group">
                                                @if( $field->type == 'text')
                                                    <input type="text"
                                                           name="custom_fields_data[{{$field->name.'_'.$field->id}}]"
                                                           class="form-control" placeholder="{{$field->label}}"
                                                           value="{{$editUser->custom_fields_data['field_'.$field->id] ?? ''}}">
                                                @elseif($field->type == 'password')
                                                    <input type="password"
                                                           name="custom_fields_data[{{$field->name.'_'.$field->id}}]"
                                                           class="form-control" placeholder="{{$field->label}}"
                                                           value="{{$editUser->custom_fields_data['field_'.$field->id] ?? ''}}">
                                                @elseif($field->type == 'number')
                                                    <input type="number"
                                                           name="custom_fields_data[{{$field->name.'_'.$field->id}}]"
                                                           class="form-control" placeholder="{{$field->label}}"
                                                           value="{{$editUser->custom_fields_data['field_'.$field->id] ?? ''}}">

                                                @elseif($field->type == 'textarea')
                                                    <textarea name="custom_fields_data[{{$field->name.'_'.$field->id}}]"
                                                              class="form-control" id="{{$field->name}}"
                                                              cols="3">{{$editUser->custom_fields_data['field_'.$field->id] ?? ''}}</textarea>

                                                @elseif($field->type == 'radio')
                                                    <div class="radio-list">
                                                        @foreach($field->values as $key=>$value)
                                                            <label class="radio-inline @if($key == 0) p-0 @endif">
                                                                <div class="radio radio-info">
                                                                    <input type="radio"
                                                                           name="custom_fields_data[{{$field->name.'_'.$field->id}}]"
                                                                           id="optionsRadios{{$key.$field->id}}"
                                                                           value="{{$value}}"
                                                                           @if(isset($clientDetail) &&
                                                                           $clientDetail->custom_fields_data['field_'.$field->id] == $value)
                                                                           checked @elseif($key==0) checked @endif>>
                                                                    <label for="optionsRadios{{$key.$field->id}}">{{$value}}</label>
                                                                </div>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                @elseif($field->type == 'select')
                                                    {!! Form::select('custom_fields_data['.$field->name.'_'.$field->id.']',
                                                    $field->values,
                                                    isset($editUser)?$editUser->custom_fields_data['field_'.$field->id]:'',['class' =>
                                                    'form-control gender'])
                                                    !!}

                                                @elseif($field->type == 'checkbox')
                                                    <div class="mt-checkbox-inline">
                                                        @foreach($field->values as $key => $value)
                                                            <label class="mt-checkbox mt-checkbox-outline">
                                                                <input name="custom_fields_data[{{$field->name.'_'.$field->id}}][]"
                                                                       type="checkbox" value="{{$key}}"> {{$value}}
                                                                <span></span>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                @elseif($field->type == 'date')
                                                    <input type="text"
                                                           class="form-control form-control-inline date-picker"
                                                           size="16"
                                                           name="custom_fields_data[{{$field->name.'_'.$field->id}}]"
                                                           value="{{ isset($editUser->dob)?Carbon\Carbon::parse($editUser->dob)->format('d, M Y'):Carbon\Carbon::now()->format($global->date_format)}}">
                                                @endif
                                                <div class="form-control-focus"></div>
                                                <span class="help-block"></span>

                                            </div>
                                        </div>
                                    @endforeach
                                @endif

                            </div>

                            
                            
                        </div>

                        <div id="alertBox" style="display:none" class="alert alert-success" data-alert="alert">Data
                            Succesfully Recorded.
                        </div>

                        <div class="form-actions">
                            <button type="submit" id="save-form" class="btn btn-success"><i class="fa fa-check"></i>
                                @lang('app.save')</button>

                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- .row -->

@endsection

@push('footer-script')
<script src="{{ asset('plugins/bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>

    <script>
        $(".date-picker").datepicker({
            todayHighlight: true,
            autoclose: true,
            weekStart: '{{ $global->week_start }}',
            format: '{{ $global->date_picker_format }}',
        });


        $('#save-form').click(function () {


            $.easyAjax({
                url: '{{route('admin.vendor.store')}}',
                container: '#createClientVendor',
                type: "POST",
                redirect: true,
                data: $('#createClientVendor').serialize(),
                // success: function(data) {
                //     $("#alertBox").fadeIn();
                //         closeAlertBox();
                //     }
            });

        });


        function closeAlertBox() {
            window.setTimeout(function () {
                $("#alertBox").fadeOut(300)
            }, 3000)
        }

    </script>
@endpush
