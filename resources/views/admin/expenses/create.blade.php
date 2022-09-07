@extends('layouts.app')

@section('page-title')
<div class="row bg-title">
    <!-- .page title -->
    <div class="border-bottom col-xs-12">
        <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}</h4>
    </div>
    <!-- /.page title -->
    <!-- .breadcrumb -->
    <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
            <li><a href="{{ route('admin.expenses.index') }}">{{ __($pageTitle) }}</a></li>
            <li class="active">@lang('app.addNew')</li>
        </ol>
    </div>
    <!-- /.breadcrumb -->
</div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
@endpush

@section('content')

<div class="row">
    <div class="col-md-12">

        <div class="panel panel-inverse">
            <div class="panel-heading p-b-10 m-b-20"> @lang('modules.expenses.addExpense')</div>
            <div class="panel-wrapper collapse in" aria-expanded="true">
                <div class="panel-body p-0">
                    {!! Form::open(['id'=>'createExpense','class'=>'ajax-form','method'=>'POST']) !!}
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-12 ">
                                <div class="form-group">
                                    <label>@lang('modules.messages.chooseMember')</label>
                                    <select id="user_id" class="select2 form-control"
                                        data-placeholder="@lang('modules.messages.chooseMember')" name="user_id">
                                        @foreach($employees as $employee)
                                        <option value="{{ $employee['user']['id'] }}">
                                            {{ ucwords($employee['user']['name']) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!--/span-->
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-xs-12">
                                <div class="form-group">
                                    <label for="project_id">@lang('modules.invoices.project')</label>
                                    <select class="select2 form-control" id="project_id" name="project_id">
                                        <option value="0">@lang('app.selectProject')</option>
                                        @if($employees)
                                        @forelse($employees[0]['user']['projects'] as $project)
                                        <option value="{{ $project['id'] }}">
                                            {{ $project['project_name'] }}
                                        </option>
                                        @empty
                                        @endforelse
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-xs-12">
                                <div class="form-group">
                                    <label for="company_phone">@lang('modules.invoices.currency')</label>
                                    <select class="form-control" id="currency_id" name="currency_id">
                                        @forelse($currencies as $currency)
                                        <option @if($currency->id == $global->currency_id) selected @endif
                                            value="{{ $currency->id }}">
                                            {{ $currency->currency_name }} - ({{ $currency->currency_symbol }})
                                        </option>
                                        @empty
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="required">@lang('modules.expenses.itemName')</label>
                                    <input type="text" name="item_name" id="item_name" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="required">@lang('app.price')</label>
                                    <input type="text" name="price" id="price" class="form-control">
                                </div>
                            </div>
                            <!--/span-->

                            {{-- Added By Adil. --}}
<!--                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Purchased Type</label>
                                    <select class="form-control select2" name="purchase_type" id="purchaseType"
                                        data-style="form-control">
                                        <option value="normal">Normal</option>
                                        <option value="vendor">Vendor</option>
                                    </select>
                                </div>
                            </div>-->

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Vendor Invoices</label>
                                    <select class="form-control select2" name="vendor_invoices" id="vendor_invoices"
                                            data-style="form-control">
                                        @foreach($vendorInvoices as $vendorInvoice)
                                        <option value="{{$vendorInvoice->id}}">{{$vendorInvoice->company_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>



                            {{-- Added By Adil. --}}
                            <div class="col-md-12" id="vendorTypeDiv" hidden>
                                <div class="form-group">
                                    <label>@lang('modules.expenses.purchaseFrom')</label>
                                    <select class="form-control select2" name="purchase_from" id="purchaseID"
                                        data-style="form-control"></select>
                                </div>
                            </div>
                            {{-- <div class="col-md-12">
                                    <div class="form-group">
                                        <label>@lang('modules.expenses.purchaseFrom')</label>
                                        <input type="text" name="purchase_from" id="purchase_from" class="form-control">
                                    </div>
                                </div> --}}
                            <!--/span-->

                            <div class="col-md-6" id="normalTypeDiv">
                                <div class="form-group">
                                    <label>@lang('modules.expenses.purchaseFrom')</label>
                                    <input type="text" name="purchase_from" id="purchase_from" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6" id="expenses_type">
                                <div class="form-group">
                                    <label>Purchase Type</label>
                                    <input type="text" name="expenses_type" id="expenses_type" class="form-control">
                                </div>
                            </div>
                        </div>
                        <!--/row-->

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">@lang('modules.expenses.purchaseDate')*</label>
                                    <input type="text" class="form-control" name="purchase_date" id="purchase_date"
                                        value="{{ Carbon\Carbon::today()->format($global->date_format) }}">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">@lang('app.invoice')</label>
                                    <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                        <div class="form-control" data-trigger="fileinput"> <i
                                                class="glyphicon glyphicon-file fileinput-exists"></i> <span
                                                class="fileinput-filename"></span></div>
                                        <span class="input-group-addon btn btn-default btn-file"> <span
                                                class="fileinput-new">@lang('app.selectFile')</span> <span
                                                class="fileinput-exists">@lang('app.change')</span>
                                            <input type="file" name="bill" id="bill">
                                        </span> <a href="#" class="input-group-addon btn btn-default fileinput-exists"
                                            data-dismiss="fileinput">@lang('app.remove')</a>
                                    </div>
                                </div>
                            </div>

                        </div>


                        <!--/span-->


                    </div>
                    <div class="form-actions">
                        <button type="submit" id="save-form-2" class="btn btn-success"><i class="fa fa-check"></i>
                            @lang('app.save')
                        </button>

                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div> <!-- .row -->

@endsection

@push('footer-script')
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script>
    var employees = @json($employees);

    $('#user_id').change(function (e) {
        // get projects of selected users
        var opts = '';

        var employee = employees.filter(function (item) {
            return item.id == e.target.value
        });

        employee[0].user.projects.forEach(project => {
            opts += `<option value='${project.id}'>${project.project_name}</option>`
        })

        $('#project_id').html('<option value="0">Select Project...</option>'+opts)
    });

    $(".select2").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });

    jQuery('#purchase_date').datepicker({
        autoclose: true,
        todayHighlight: true,
        weekStart:'{{ $global->week_start }}',
        format: '{{ $global->date_picker_format }}',
    });

    $('#save-form-2').click(function () {
        $.easyAjax({
            url: '{{route('admin.expenses.store')}}',
            container: '#createExpense',
            type: "POST",
            redirect: true,
            file: (document.getElementById("bill").files.length == 0) ? false : true,
            data: $('#createExpense').serialize()
        })
    });

    $("#purchaseType").change(function () {
        var val = $('#purchaseType').val();
        if(val == 'normal')
        {
            $("#normalTypeDiv").show();
            $("#vendorTypeDiv").hide();
        }
        else
        {
            $("#purchaseID").empty();
            fillVendorDropdown();
            $("#normalTypeDiv").hide();
            $("#vendorTypeDiv").show();
        }
    });

    function fillVendorDropdown()
    {
        $.ajax({
            type: "GET",
            url: "fill",
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data)
                    {
                        $.each(data, function (key, value){
                            $("#purchaseID").append($("<option     />").val(value.id).text(value.name));
                        });
                    },
            failure: function () {
                alert("Failed!");
            }
        });
   }
</script>
@endpush
