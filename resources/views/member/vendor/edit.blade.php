@extends('layouts.member-app')

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
                <div class="panel-heading"> UPDATE VENDOR INFO
                    {{ $vendorDetail->vendor_name }}
                    @php($class = ($vendorDetail->status == 'active') ? 'label-custom' : 'label-danger')
                    <span class="label {{$class}}">{{ucfirst($vendorDetail->status)}}</span>
                </div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body p-0">
                        {!! Form::open(['id'=>'updateClient','class'=>'ajax-form','method'=>'PUT']) !!}
                        <div class="form-body">

                            <h3 class="box-title ">Vendor Details</h3>
                            <hr>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="">Vendor Name</label>
                                        <input type="text" name="vendor_name" id="vendor_name" class="form-control" value="{{ $vendorDetail->vendor_name }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="">Vendor Rep Name</label>
                                        <input type="text" name="vendor_rep_name" id="vendor_rep_name" class="form-control" value="{{ $vendorDetail->vendor_rep_name }}">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="">Rep Email Address</label>
                                        <input type="email" name="rep_email" id="rep_email" class="form-control" value="{{ $vendorDetail->rep_email }}">
                                        <span style="display: none" class="help-block">Vendor will login using this email.</span>
                                    </div>
                                </div>
                                <!--/span-->
                            </div>

                            <h3 class="box-title m-t-20">COMPANY OTHER DETAILS</h3>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label required">Company Name</label>
                                        <input type="text" id="company_name" name="company_name" class="form-control"  value="{{ $vendorDetail->company_name ?? '' }}">
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Website</label>
                                        <input type="text" id="company_website" name="company_website" class="form-control" value="{{ $vendorDetail->company_website ?? '' }}" >
                                    </div>
                                </div>
                                <!--/span-->
                            </div>
                            <!--/row-->
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label class="control-label ">Address</label>
                                        <textarea name="company_address" id="company_address"  rows="5" class="form-control">{{ $vendorDetail->company_address ?? '' }}</textarea>
                                    </div>
                                </div>
                                <!--/span-->

                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">Tags</label>
                                    <select multiple data-role="tagsinput" name="tags[]" id="tags">
                                        @if(!empty($vendorDetail->tags))
                                            @foreach($vendorDetail->tags as $tag)
                                                <option value="{{ $tag }}">{{ $tag }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>   
                            <!--/row-->
                            <h3 class="box-title m-t-20">VENDOR OTHER DETAILS</h3>
                            <hr>

                            <!--/row-->

                            <div class="row" style="display: none;">

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Skype</label>
                                        <input type="text" name="skype" id="skype" class="form-control" value="{{ $vendorDetail->vendor_skype ?? '' }}">
                                    </div>
                                </div>
                                <!--/span-->

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Linkedin</label>
                                        <input type="text" name="linkedin" id="linkedin" class="form-control" value="{{ $vendorDetail->vendor_linkedIn ?? '' }}">
                                    </div>
                                </div>
                                <!--/span-->

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Twitter</label>
                                        <input type="text" name="twitter" id="twitter" class="form-control" value="{{ $vendorDetail->vendor_twitter ?? '' }}">
                                    </div>
                                </div>
                                <!--/span-->

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Facebook</label>
                                        <input type="text" name="facebook" id="facebook" class="form-control" value="{{ $vendorDetail->vendor_facebook ?? '' }}">
                                    </div>
                                </div>
                                <!--/span-->
                            </div>
                            <!--/row-->
                            <!--row gst number-->
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="gst_number">GST Number</label>
                                        <input type="text" id="gst_number" name="gst_number" class="form-control" value="{{ $vendorDetail->vendor_gst_number ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Phone Number</label>
                                        <input type="tel" name="rep_phone" id="rep_phone" class="form-control" value="{{ $vendorDetail->rep_phone }}">
                                    </div>
                                </div>
                                <!--/span-->

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Vendor Status</label>
                                        <select name="status" id="status" class="form-control">
                                            <option @if($vendorDetail->status == 'active') selected
                                                    @endif value="active">@lang('app.active')</option>
                                            <option @if($vendorDetail->status == 'deactive') selected
                                                    @endif value="deactive">@lang('app.deactive')</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="" for="url">URL</label>
                                        <input type="text"  name="url" id="url" value="{{ $vendorDetail->url ?? '' }}"
                                               class="form-control">
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="" for="user">User</label>
                                        <input type="text" name="user" id="user" value="{{ $vendorDetail->user ?? '' }}"
                                               class="form-control">
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="password">Password</label>
                                        <input type="text" name="password" id="password" value="{{ $vendorDetail->password ?? '' }}"
                                               class="form-control">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="vendor_category">Category</label>
                                        <input type="text" id="vendor_category" name="vendor_category" class="form-control" value="{{ $vendorDetail->vendor_category ?? '' }}">
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="vendor_markup">Markup</label>
                                        <input type="text" id="vendor_markup" name="vendor_markup" class="form-control" value="{{ $vendorDetail->vendor_markup ?? 0 }}">
                                    </div>
                                </div>
                                
                            </div>
                            
                            
                            <!--/row-->

                            <div class="row">
                                <div class="col-md-12">
                                    <label>Shipping Address</label>
                                    <div class="form-group">
                                        <textarea name="shipping_address" id="shipping_address" class="form-control" rows="4">{{$vendorDetail->vendor_shipping_address ?? ''}}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label>Note</label>
                                    <div class="form-group">
                                        <textarea name="note" id="note" class="form-control" rows="5">{{ $vendorDetail->vendor_note ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" id="save-form" class="btn btn-success"> <i class="fa fa-check"></i> @lang('app.update')</button>
                            <a href="{{ route('member.vendor.index') }}" class="btn btn-default">@lang('app.back')</a>
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
            url: '{{route('member.vendor.update', [$vendorDetail->id])}}',
            container: '#updateClient',
            type: "POST",
            redirect: true,
            data: $('#updateClient').serialize()
        })
    });
</script>
@endpush
