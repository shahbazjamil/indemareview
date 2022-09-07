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
                <li><a href="{{ route('admin.products.index') }}">{{ __($pageTitle) }}</a></li>
                <li class="active">@lang('app.add') @lang('app.menu.products')</li>
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

    <div class="row wa-new-changes">
        <div class="col-md-12">

            <div class="panel panel-inverse">
                <div class="panel-heading"> @lang('app.add') @lang('app.menu.products')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body p-0">
                        {!! Form::open(['id'=>'tab-item','class'=>'ajax-form','method'=>'POST', 'files' => true]) !!}
                            <div class="panel-tab">
                                <div class="panel-tab-header m-b-2">
                                    <button class="btn btn-default active" tab="item">@lang('app.product.tabs.item')</button>
                                    <button class="btn btn-default" tab="specification">@lang('app.product.tabs.specification')</button>
                                    <button class="btn btn-default" tab="pricing">@lang('app.product.tabs.pricing')</button>
                                    <button style="display: none;" class="btn btn-default" tab="workroom">@lang('app.product.tabs.workroom')</button>
                                </div>
                                <div class="panel-tab-body">
                                    <div class="panel-tab-item active" tab="item">@include('admin.products.tabs.item')</div>
                                    
                                    <div class="panel-tab-item" tab="pricing">@include('admin.products.tabs.pricing')</div>
                                    <div class="panel-tab-item" tab="Additional Product Information">@include('admin.products.tabs.specification')</div>
                                    <div style="display: none;" class="panel-tab-item" tab="workroom">@include('admin.products.tabs.workroom')</div>
                                </div>
                            </div>
                            <div class="form-actions text-right">
                                <button style="display: none;" id="add-item" class="btn btn-default"> <i class="fa fa-plus"></i> @lang('app.add')</button>
                                <button type="submit" id="save-form" class="btn btn-default">@lang('app.save')</button>
                                <a href="{{route('admin.products.index')}}" class="btn btn-default">@lang('app.cancel')</a>
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
    
    @if($product->id != 0)
    @include('admin.products.tabs.purchase-order')
    @endif

@endsection

@push('footer-script')
    <script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
    <script>
        var tabId = "item";
        $('#save-form').click(function () {
            saveForm(tabId);
        });

        const saveForm = (tabId) => {
            const tab = `#tab-${tabId}`;
            var copy = "{{$copy}}";
            //var redirectJS = {{$product->id == 0 ? 'true' : 'false'}};
            
            var redirectJS = 'true';
            
            var file_up = true;
            
//            if(document.getElementById("attachment1").files.length == 0)) {
//                 file_up = true;
//            }
//            if(document.getElementById("attachment2").files.length == 0)) {
//                 file_up = true;
//            }
//            if(document.getElementById("attachment3").files.length == 0)) {
//                 file_up = true;
//            }
            
            var urlJS = "{{$product->id == 0 ? route('admin.products.store') : route('admin.products.product-update', [$product->id])}}";
            var typeJS = "{{$product->id == 0 ? 'POST' : 'POST'}}";
            
            // for copy
            if(copy == 1) {
                urlJS = "{{route('admin.products.store')}}";
                typeJS = "{{'POST'}}";
                redirectJS = 'true';
            }
            
            $.easyAjax({
                url: urlJS,
                container: tab,
                type: typeJS,
                redirect: redirectJS,
                file : file_up,
                data: $(tab).serialize()
            })
        }

        $('.panel-tab-header button').click((event) => {
            if ({{$product->id}} == 0) {
                toastr.warning("@lang('app.product.messages.askCreateFirst')");
                return;
            }
            // saveForm(tabId);
            const {currentTarget} = event;
            tabId = $(currentTarget).attr('tab');
            $('.panel-tab-header button').removeClass('active');
            $('.panel-tab-body > div').removeClass('active');
            $(currentTarget).addClass('active');
            $(`.panel-tab-body > div[tab=${tabId}]`).addClass('active');

            if (tabId != 'item') $("#add-item").addClass('hide');
            else $("#add-item").removeClass('hide');
            
            if (tabId == 'picture') $(".form-actions").addClass('hide');
            else $(".form-actions").removeClass('hide');
        });

        $("#add-item").click(() => {
            if ({{$product->id}} == 0) {
                toastr.warning("@lang('app.product.messages.askCreateFirst')");
                return;
            }
            $('#item-purchase-order').modal('show');
        });

        $("#submit-modal").click(function() {
            $.easyAjax({
                url: "{{route('admin.products.update', [$product->id])}}",
                container: '#tab-purchaseOrder',
                type: 'PUT',
                redirect: false,
                data: $('#tab-purchaseOrder').serialize()
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
        
        $(document).ready(function(){
			$("#calculate-markup .close").click(function(){
				$("#calculate-markup").removeClass("in");
			});
			$("#calculate-markup .btn-primary").click(function(){
				$("#default_markup").val($("#markup-price").val());
				$("#calculate-markup").removeClass("in");
                                
                                var markup = Number($("#default_markup") .val());
                                var cost = Number($("#cost_per_unit") .val());
                                var msrp = 0;
                                msrp = Number(cost+((markup/100)*cost)).toFixed(2);
                                $("#msrp") .val(msrp);
                            
			});
                        
    });
    
    
    
    
    
    </script>
    
    @include('admin.products.tabs.picture-script')
    @include('admin.products.tabs.pricing-script')
@endpush

