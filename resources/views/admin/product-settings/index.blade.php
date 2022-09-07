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

@section('content')

    <div class="row">
        <div class="col-md-12">

            <div class="panel panel-inverse">
                <div class="panel-heading p-t-10 p-b-10">@lang('app.menu.productSettings')</div>
                
                <h5 class="page-title p-t-10">The below settings are to permanently hide or show the columns within the products section of indema, which will also reflect the project-side products.</h5>

                <div class="vtabs customvtab m-t-10">
                    @include('sections.admin_setting_menu')

                    <div class="tab-content p-0 p-t-20">
                        <div id="vhome3" class="tab-pane active">
                            <div class="row">
                                <div class="col-sm-12 col-xs-12">
                                    {!! Form::open(['id'=>'updateProfile','class'=>'ajax-form','method'=>'PUT']) !!}
                                    <div class="form-body">
                                        <div class="row">

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="checkbox checkbox-info  col-md-10">
                                                        <input id="show_spec_number" name="show_spec_number" value="yes" 
                                                               @if($productSettings->show_spec_number == 'yes') checked @endif
                                                               type="checkbox">
                                                        <label for="show_spec_number">Show Spec #</label>
                                                    </div>
                                                </div>
                                            </div>
                                            
<!--                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <div class="checkbox checkbox-info  col-md-10">
                                                        <input id="show_name" name="show_name" value="yes" 
                                                               @if($productSettings->show_name == 'yes') checked @endif
                                                               type="checkbox">
                                                        <label for="show_name">Show Name</label>
                                                    </div>
                                                </div>
                                            </div>-->
                                            
<!--                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <div class="checkbox checkbox-info  col-md-10">
                                                        <input id="show_picture" name="show_picture" value="yes" 
                                                               @if($productSettings->show_picture == 'yes') checked @endif
                                                               type="checkbox">
                                                        <label for="show_picture">Show Picture</label>
                                                    </div>
                                                </div>
                                            </div>-->
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="checkbox checkbox-info  col-md-10">
                                                        <input id="show_project_name" name="show_project_name" value="yes" 
                                                               @if($productSettings->show_project_name == 'yes') checked @endif
                                                               type="checkbox">
                                                        <label for="show_project_name">Show Project Name</label>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="checkbox checkbox-info  col-md-10">
                                                        <input id="show_location" name="show_location" value="yes" 
                                                               @if($productSettings->show_location == 'yes') checked @endif
                                                               type="checkbox">
                                                        <label for="show_location">Show Location</label>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="checkbox checkbox-info  col-md-10">
                                                        <input id="show_category" name="show_category" value="yes" 
                                                               @if($productSettings->show_category == 'yes') checked @endif
                                                               type="checkbox">
                                                        <label for="show_category">Show Category</label>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="checkbox checkbox-info  col-md-10">
                                                        <input id="show_vendor" name="show_vendor" value="yes" 
                                                               @if($productSettings->show_vendor == 'yes') checked @endif
                                                               type="checkbox">
                                                        <label for="show_vendor">Show Vendor</label>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="checkbox checkbox-info  col-md-10">
                                                        <input id="show_manufacturer" name="show_manufacturer" value="yes" 
                                                               @if($productSettings->show_manufacturer == 'yes') checked @endif
                                                               type="checkbox">
                                                        <label for="show_manufacturer">Show Manufacturer</label>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="checkbox checkbox-info  col-md-10">
                                                        <input id="show_notes" name="show_notes" value="yes" 
                                                               @if($productSettings->show_notes == 'yes') checked @endif
                                                               type="checkbox">
                                                        <label for="show_notes">Show Notes</label>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="checkbox checkbox-info  col-md-10">
                                                        <input id="show_url" name="show_url" value="yes" 
                                                               @if($productSettings->show_url == 'yes') checked @endif
                                                               type="checkbox">
                                                        <label for="show_url">Show URL</label>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="checkbox checkbox-info  col-md-10">
                                                        <input id="show_dimensions" name="show_dimensions" value="yes" 
                                                               @if($productSettings->show_dimensions == 'yes') checked @endif
                                                               type="checkbox">
                                                        <label for="show_dimensions">Show Dimensions</label>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="checkbox checkbox-info  col-md-10">
                                                        <input id="show_materials" name="show_materials" value="yes" 
                                                               @if($productSettings->show_materials == 'yes') checked @endif
                                                               type="checkbox">
                                                        <label for="show_materials">Show Materials</label>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="checkbox checkbox-info  col-md-10">
                                                        <input id="show_quantity" name="show_quantity" value="yes" 
                                                               @if($productSettings->show_quantity == 'yes') checked @endif
                                                               type="checkbox">
                                                        <label for="show_quantity">Show Quantity</label>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="checkbox checkbox-info  col-md-10">
                                                        <input id="show_cost_per_unit" name="show_cost_per_unit" value="yes" 
                                                               @if($productSettings->show_cost_per_unit == 'yes') checked @endif
                                                               type="checkbox">
                                                        <label for="show_cost_per_unit">Show Cost Per Unit</label>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="checkbox checkbox-info  col-md-10">
                                                        <input id="show_markup_fix" name="show_markup_fix" value="yes" 
                                                               @if($productSettings->show_markup_fix == 'yes') checked @endif
                                                               type="checkbox">
                                                        <label for="show_markup_fix">Show Markup $</label>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="checkbox checkbox-info  col-md-10">
                                                        <input id="show_markup_per" name="show_markup_per" value="yes" 
                                                               @if($productSettings->show_markup_per == 'yes') checked @endif
                                                               type="checkbox">
                                                        <label for="show_markup_per">Show Markup %</label>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="checkbox checkbox-info  col-md-10">
                                                        <input id="show_freight" name="show_freight" value="yes" 
                                                               @if($productSettings->show_freight == 'yes') checked @endif
                                                               type="checkbox">
                                                        <label for="show_freight">Show Freight</label>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="checkbox checkbox-info  col-md-10">
                                                        <input id="show_total_sale" name="show_total_sale" value="yes" 
                                                               @if($productSettings->show_total_sale == 'yes') checked @endif
                                                               type="checkbox">
                                                        <label for="show_total_sale">Show Total Sale</label>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="checkbox checkbox-info  col-md-10">
                                                        <input id="show_msrp" name="show_msrp" value="yes" 
                                                               @if($productSettings->show_msrp == 'yes') checked @endif
                                                               type="checkbox">
                                                        <label for="show_msrp">Show MSRP</label>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="checkbox checkbox-info  col-md-10">
                                                        <input id="show_acknowledgement" name="show_acknowledgement" value="yes" 
                                                               @if($productSettings->show_acknowledgement == 'yes') checked @endif
                                                               type="checkbox">
                                                        <label for="show_acknowledgement">Show Acknowledgement</label>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="checkbox checkbox-info  col-md-10">
                                                        <input id="show_est_ship_date" name="show_est_ship_date" value="yes" 
                                                               @if($productSettings->show_est_ship_date == 'yes') checked @endif
                                                               type="checkbox">
                                                        <label for="show_est_ship_date">Show Est Ship Date</label>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="checkbox checkbox-info  col-md-10">
                                                        <input id="show_act_ship_date" name="show_act_ship_date" value="yes" 
                                                               @if($productSettings->show_act_ship_date == 'yes') checked @endif
                                                               type="checkbox">
                                                        <label for="show_act_ship_date">Show Act Ship Date</label>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="checkbox checkbox-info  col-md-10">
                                                        <input id="show_est_receive_date" name="show_est_receive_date" value="yes" 
                                                               @if($productSettings->show_est_receive_date == 'yes') checked @endif
                                                               type="checkbox">
                                                        <label for="show_est_receive_date">Show Est Receive Date</label>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="checkbox checkbox-info  col-md-10">
                                                        <input id="show_act_receive_date" name="show_act_receive_date" value="yes" 
                                                               @if($productSettings->show_act_receive_date == 'yes') checked @endif
                                                               type="checkbox">
                                                        <label for="show_act_receive_date">Show Act Receive Date</label>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="checkbox checkbox-info  col-md-10">
                                                        <input id="show_received_by" name="show_received_by" value="yes" 
                                                               @if($productSettings->show_received_by == 'yes') checked @endif
                                                               type="checkbox">
                                                        <label for="show_received_by">Show Received By</label>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="checkbox checkbox-info  col-md-10">
                                                        <input id="show_est_install_date" name="show_est_install_date" value="yes" 
                                                               @if($productSettings->show_est_install_date == 'yes') checked @endif
                                                               type="checkbox">
                                                        <label for="show_est_install_date">Show Est Install Date</label>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="checkbox checkbox-info  col-md-10">
                                                        <input id="show_act_install_date" name="show_act_install_date" value="yes" 
                                                               @if($productSettings->show_act_install_date == 'yes') checked @endif
                                                               type="checkbox">
                                                        <label for="show_act_install_date">Show Act Install Date</label>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="checkbox checkbox-info  col-md-10">
                                                        <input id="show_product_number" name="show_product_number" value="yes" 
                                                               @if($productSettings->show_product_number == 'yes') checked @endif
                                                               type="checkbox">
                                                        <label for="show_product_number">Show Product Number</label>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="checkbox checkbox-info  col-md-10">
                                                        <input id="show_finish_color" name="show_finish_color" value="yes" 
                                                               @if($productSettings->show_finish_color == 'yes') checked @endif
                                                               type="checkbox">
                                                        <label for="show_finish_color">Show Finish/Color</label>
                                                    </div>
                                                </div>
                                            </div>
                                            

                            
                                        </div>
                                        <!--/row-->
                                    </div>
                                    <div class="form-actions">
                                        <button type="submit" id="save-form-2" class="btn btn-success"><i
                                                    class="fa fa-check"></i>
                                            @lang('app.update')
                                        </button>

                                    </div>
                                    {!! Form::close() !!}
                                </div>
                            </div>

                            <div class="clearfix"></div>
                        </div>
                    </div>

                </div>
            </div>    <!-- .row -->
        </div>
    </div>

@endsection

@push('footer-script')
<script>
    $('#save-form-2').click(function () {
        $.easyAjax({
            url: '{{route('admin.product-settings.update', [1])}}',
            container: '#updateProfile',
            type: "POST",
            data: $('#updateProfile').serialize()
        })
    });
</script>
@endpush
