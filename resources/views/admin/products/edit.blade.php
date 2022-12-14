@extends('layouts.app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li><a href="{{ route('admin.products.index') }}">{{ __($pageTitle) }}</a></li>
                <li class="active">@lang('app.update') @lang('app.menu.products')</li>
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

    <div class="row wa-new-changes sffg">
        <div class="col-md-12">
            <div class="panel panel-inverse">
                <div class="panel-heading"> @lang('app.update') @lang('app.menu.products')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        {!! Form::open(['id'=>'updateProduct','class'=>'ajax-form']) !!}
                        <input name="_method" value="PUT" type="hidden">
                        <div class="form-body">								
<!--                            <div class="row d-none">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">@lang('app.name')</label>
                                        <input type="text" id="name" name="name" class="form-control" value="{{ $product->name }}">
                                    </div>
                                </div>
                                /span
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">@lang('app.price')</label>
                                        <input type="number" min="0" id="price" name="price" class="form-control" value="{{ $product->price }}">
                                        <span class="help-block"> @lang('messages.productPrice')</span>
                                    </div>
                                </div>
                                /span
                            </div>-->
                            <h3 class="box-title">@lang('app.menu.products') @lang('app.details')</h3>
                            <hr>
<!--                            <div class="row d-none">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">@lang('app.name')</label>
                                        <input type="text" id="name" name="name" class="form-control" value="{{ $product->name }}">
                                    </div>
                                </div>
                                /span
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">@lang('app.price')</label>
                                        <input type="number" min="0" id="price" name="price" class="form-control" value="{{ $product->price }}">
                                        <span class="help-block"> @lang('messages.productPrice')</span>
                                    </div>
                                </div>
                                /span
                            </div>-->
                            <!--/row-->

<!--                            <div class="row d-none">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">@lang('modules.invoices.tax') <a href="javascript:;" id="tax-settings" ><i class="ti-settings text-info"></i></a></label>
                                        <select id="multiselect" name="tax[]"  multiple="multiple" class="selectpicker form-control type">
                                            @foreach($taxes as $tax)
                                                <option @if (isset($product->taxes) && array_search($tax->id, json_decode($product->taxes)) !== false)
                                                        selected
                                                        @endif
                                                        value="{{ $tax->id }}">{{ $tax->tax_name }}: {{ $tax->rate_percent }}%</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">@lang('app.description')</label>
                                        <textarea name="description" id="" cols="30" rows="4" class="form-control">{{ $product->description }}</textarea>
                                    </div>
                                </div>
                            </div>-->
<!--                            <div class="row d-none">
                                <div class="col-md-12">
                                    <div class="form-group">

                                        <div class="checkbox checkbox-info">
                                            <input id="purchase_allow" name="purchase_allow" value="no"
                                                   type="checkbox" @if($product->allow_purchase == 1) checked @endif>
                                            <label for="purchase_allow">@lang('app.purchaseAllow')</label>
                                        </div>
                                    </div>
                                </div>
                            </div>-->
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
    <div class="modal fade bs-modal-md in" id="taxModal" role="dialog" aria-labelledby="myModalLabel"
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
    <script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
    <script>
        $(".select2").select2({
            formatNoMatches: function () {
                return "{{ __('messages.noRecordFound') }}";
            }
        });

        $('#tax-settings').on('click', function (event) {
            event.preventDefault();
            var url = '{{ route('admin.taxes.create')}}';
            $('#modelHeading').html('Manage Project Category');
            $.ajaxModal('#taxModal', url);
        });

        $('#save-form').click(function () {
            $.easyAjax({
                url: '{{route('admin.products.update', [$product->id])}}',
                container: '#updateProduct',
                type: "POST",
                redirect: true,
                data: $('#updateProduct').serialize()
            })
        });
        
        $(function() {
            var select_unit = $('#select_unit').val();
            $("#wrp-unit-"+select_unit).show();
            
        });
        
       $( "#select_unit" ).change(function() {
            $(".wrp-unit").hide();
            $(".unit-val").val('');
            var selected_unit = $('#select_unit').val();
            $("#wrp-unit-"+selected_unit).show();
        });
        
       
        
    </script>
@endpush

