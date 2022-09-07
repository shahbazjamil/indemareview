@extends('layouts.client-app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="border-bottom col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }} #{{ $project->id }} - <span
                        class="font-bold">{{ ucwords($project->project_name) }}</span></h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('client.dashboard.index') }}">@lang('app.menu.home')</a></li>
                <li><a href="{{ route('client.projects.index') }}">{{ __($pageTitle) }}</a></li>
                <li class="active">@lang('app.menu.invoices')</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection
@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/multiselect/css/multi-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
<style>
    .swal-footer {
        text-align: center !important;
    }
</style>
@endpush
@section('content')

    <div class="row">
        <div class="col-md-12">

            <section>
                <div class="sttabs tabs-style-line client-ptabs">

                    <div class="white-box p-0">
                        <nav>
                            <ul class="showProjectTabs">
                                <li><a href="{{ route('client.projects.show', $project->id) }}"><span>@lang('modules.projects.overview')</span></a>
                                </li>
                                @if(in_array('employees',$modules))
                                    <li><a href="{{ route('client.project-members.show', $project->id) }}"><span>@lang('modules.projects.members')</span></a></li>
                                @endif

                                @if(in_array('tasks',$modules))
                                    <li c><a href="{{ route('client.tasks.edit', $project->id) }}"><span>@lang('app.menu.tasks')</span></a></li>
                                @endif

                                <li><a href="{{ route('client.files.show', $project->id) }}"><span>@lang('modules.projects.files')</span></a></li>
                                @if(in_array('timelogs',$modules))
<!--                                    <li><a href="{{ route('client.time-log.show', $project->id) }}"><span>@lang('app.menu.timeLogs')</span></a></li>-->
                                @endif
                                
                                <li class="tab-current" ><a href="{{ route('client.product-review-project.show', $project->id) }}"><span>Product Review</span></a></li>

                                @if(in_array('invoices',$modules))
                                    <li ><a href="{{ route('client.project-invoice.show', $project->id) }}"><span>@lang('app.menu.invoices')</span></a></li>
                                @endif
                                <li>
                                    <a href="{{ route('client.projects.discussion', $project->id) }}">
                                        <span>@lang('modules.projects.discussion')</span></a>
                                </li>

                            </ul>
                        </nav>
                    </div>

                    <div class="content-wrap">
                        <section id="section-line-3" class="show">
                            <div class="row">
                                <div class="col-md-3 product-sidebar" id="issues-list-panel">
                                    
                                    <div class="category-box location-code loc-active" data-location-id="location-code">
                                            <a href="javascript:void(0)"><h3>All</h3></a>
                                            <div class="summary-area">
                                                    <span>
                                                        &nbsp;
                                                            
                                                    </span>
                                                    <span>
                                                            &nbsp;
                                                    </span>
                                            </div>
                                    </div>
                                    
                                    @if($locationCodesData)
                                        @foreach ($locationCodesData as $key=>$locationCode)
                                        
                                        
                                            <div class="category-box location-code" data-location-id="{{ $key }}">
                                                <a href="javascript:void(0)"><h3>{{$locationCode['name']}}</h3></a>
                                                    <div class="summary-area">
                                                            <span>
                                                                    
                                                            </span>
                                                            <span>
                                                                <a href="javascript:void(0)" class="add-location-notes" data-code-type="{{$locationCode['id']}}"><img src="{{ asset('img/commets-icon.png') }}" alt=""></a> {{$locationCode['total_notes']}}
                                                            </span>
                                                    </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    <div class="category-box location-code" data-location-id="uncategorized">
                                            <a href="javascript:void(0)"><h3>Uncategorized</h3></a>
                                            <div class="summary-area">
                                                    <span>
                                                            &nbsp;
                                                    </span>
                                                    <span>
                                                            &nbsp;
                                                    </span>
                                            </div>
                                    </div>	
									
                                </div><!--end of product-sidebar-->
                                
                                    <div class="product-con-area col-md-9">
                                            <div class="w-100 product-review-f">
                                                    <div>
                                                            <label>Select Status</label>
                                                            <select class="select-status" name="select_status" id="select_status">
                                                                    <option value="location-code">All</option>
                                                                    <option value="approved">Approved</option>
                                                                    <option value="declined">Declined</option>
                                                            </select>
                                                    </div>
                                                    <div>
                                                            <a href="javascript:void(0);" class="btn btn-outline btn-success btn-sm update-product-lock" data-lock-flag="1"><i class="fa fa-check-circle"></i> Approve Selected</a>
                                                            <a href="javascript:void(0);" class="btn btn-outline btn-success btn-sm update-product-lock" data-lock-flag="0"><i class="fa fa-times-circle"></i> Decline Selected</a>
                                                            
                                                    </div>
                                            </div>
                                            
                                            <div class="row">
                                                
                                                @if($products)
                                                    @foreach ($products as $key=>$product)
                                                    
                                                <?php
                                                    $picture = 'img/default-product.png';
                                                    if(!empty($product->picture)) {
                                                        $pictures = json_decode($product->picture);
                                                        if(isset($pictures[0])) {
                                                            $picture = 'user-uploads/products/'.$product->id.'/'.$pictures[0];
                                                        }
                                                    }
                                                    
                                                    $itemObj = json_decode($product->item);
                                                    $locationCodeCLS = 'uncategorized-cls';
                                                    if(isset($itemObj->locationCode) && !empty($itemObj->locationCode)) {
                                                        $locationCodeCLS = $itemObj->locationCode.'-cls';
                                                    }
                                                    
                                                     $approve_cls = 'declined-cls';
                                                    if($product->is_approved == 1){
                                                        $approve_cls = 'approved-cls';
                                                    }
                                                    
                                                    $total_notes = 0;
                                                    if($product->comments){
                                                        $total_notes = $product->comments->count();
                                                    }
                                                    
                                                   
                                                    
                                                ?>
                                                    @if($product->is_locked == 1)
                                                        <div id="location-code-wrp{{$product->id}}" class="col-md-3  location-code-cls {{$locationCodeCLS}} {{$approve_cls}}">
                                                                <div class="category-box">
                                                                        <div>
                                                                            <a href="javascript:void(0)" ><img id="{{$product->id}}" data-product-id="{{ $product->id }}"  src="{{ asset($picture) }}" class="img-responsive p-image" alt="{{$product->name}}"></a>
                                                                        </div>
                                                                        <div class="summary-area">
                                                                                <span>
                                                                                    <input id="product_id_{{$product->id}}" name="product_id" value="{{$product->id}}" type="checkbox" />
                                                                                </span>
                                                                                <span>
                                                                                    @if(!is_null($product->is_approved) && $product->is_approved == 1)
                                                                                    <a href="javascript:void(0)" class="decline-clicked" data-pp-id="{{$product->id}}"><i id="p-lock-c{{$product->id}}" class="fa fa-times-circle disable"></i></a>
                                                                                    <a href="javascript:void(0)" class="approve-clicked" data-pp-id="{{$product->id}}"><i id="p-lock-a{{$product->id}}" class="fa fa-check-circle"></i></a>
                                                                                    @elseif(!is_null($product->is_approved) && $product->is_approved == 0)
                                                                                        <a href="javascript:void(0)" class="decline-clicked" data-pp-id="{{$product->id}}"><i id="p-lock-c{{$product->id}}" class="fa fa-times-circle"></i></a>
                                                                                        <a href="javascript:void(0)" class="approve-clicked" data-pp-id="{{$product->id}}"><i id="p-lock-a{{$product->id}}" class="fa fa-check-circle disable"></i></a>
                                                                                    @else
                                                                                        <a href="javascript:void(0)" class="decline-clicked" data-pp-id="{{$product->id}}"><i id="p-lock-c{{$product->id}}" class="fa fa-times-circle disable"></i></a>
                                                                                        <a href="javascript:void(0)" class="approve-clicked" data-pp-id="{{$product->id}}"><i id="p-lock-a{{$product->id}}" class="fa fa-check-circle disable"></i></a>
                                                                                    @endif
                                                                                        
                                                                                    <a href="javascript:void(0)" class="add-product-notes" data-npp-id="{{$product->id}}"><img src="{{ asset('img/commets-icon.png') }}" alt=""></a> {{$total_notes}}
                                                                                </span>
                                                                                @if ($invoiceSetting->hide_sale_cost == 'no' && $product->total_sale) 
                                                                                        <span class="cost">Cost: {{ currency_position($product->total_sale,$global->currency->currency_symbol) }} </span>
                                                                                @else
                                                                                <span class="cost">&nbsp;</span>
                                                                                @endif
                                                                        </div>
                                                                </div>
                                                        </div>
                                                    @endif
                                                    
                                                    @endforeach
                                                @endif
                                                
                                                    
                                                    
                                                   
                                                    
                                            </div>
                                    </div>
                            </div>
                        </section>

                    </div><!-- /content -->
                </div><!-- /tabs -->
            </section>
        </div>


    </div>
    <!-- .row -->

    {{--Ajax Modal--}}
     <div class="modal fade bs-modal-md in" id="myModalImagePreview" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
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
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    
    <div class="modal fade bs-modal-md in" id="addNotesModal" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" id="modal-data-application">
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
<script src="{{ asset('js/cbpFWTabs.js') }}"></script>
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<script type="text/javascript">
    
    var token = "{{ csrf_token() }}";
    
    $('.location-code').click(function () {
        
        $('.location-code').removeClass("loc-active");
        $(this).addClass("loc-active");
        
        var location_id = $(this).data('location-id');
        $('.location-code-cls').hide();
        $('.'+location_id+'-cls').show();
        $("#select_status").val('all');
        $("input[name='product_id']:checkbox").prop('checked',false);
        
    });
    
    $("#select_status").change(function(){
        var status_val = $("#select_status").val();
        
        if(status_val == 'approved'){
            $('.approved-cls').show();
            $('.declined-cls').hide();
        } else if(status_val == 'declined') {
            $('.declined-cls').show();
            $('.approved-cls').hide();
        } else {
            $('.declined-cls').show();
            $('.approved-cls').show();
        }
        
        $("input[name='product_id']:checkbox").prop('checked',false);
    });
    
    
//     $("#select_status").change(function(){
//        var status_val = $("#select_status").val();
//        $('.location-code-cls').hide();
//        $('.'+status_val+'-cls').show();
//        
//        $("input[name='product_id']:checkbox").prop('checked',false);
//    });
    
    $('.decline-clicked').click(function () {
        var is_approved = 0;
        var pp_id = $(this).data('pp-id');
        var productIDS = [];
        productIDS.push(pp_id);
        
        updateStatus(productIDS, is_approved)
        
    });
    $('.approve-clicked').click(function () {
        var is_approved = 1;
        var pp_id = $(this).data('pp-id');
        var productIDS = [];
        productIDS.push(pp_id);
        updateStatus(productIDS, is_approved)
        
    });
     
     $('body').on('click', '.p-image', function () {
            var projectID = '<?php echo $project->id; ?>';
            var id = $(this).data('product-id');
            var url = "{{ route('client.product-review-project.detail',[':id',':pid']) }}";
            url = url.replace(':id', id);
            url = url.replace(':pid', projectID);
            $.ajaxModal('#myModalImagePreview', url);
            
    });
     
     $('.update-product-lock').click(function () {
         var is_approved = $(this).data('lock-flag');
         var productIDS = [];
                 
        $('input[name="product_id"]:checked').each(function() {
           productIDS.push(this.value);
        });
        
        updateStatus(productIDS, is_approved)
        
    });
    
    
    $('body').on('click', '.add-product-notes', function () {
        var npp_id = $(this).data('npp-id');
        var url = "{{ route('client.product-review-project.create-product-notes',':id') }}";
        url = url.replace(':id', npp_id);
        
        $('#modelHeading').html('Product Notes');
        $.ajaxModal('#addNotesModal', url);

    });
    
    $('body').on('click', '.add-location-notes', function () {
        var code_type = $(this).data('code-type');
        var url = "{{ route('client.product-review-project.create-location-notes',':id') }}";
        url = url.replace(':id', code_type);
        
        $('#modelHeading').html('Location Notes');
        $.ajaxModal('#addNotesModal', url);

    });
    
    function updateStatus(productIDS, is_approved){
        
        $.easyAjax({
            url: '{{route('client.product-review-project.update', '1')}}',
            type: "PUT",
            data: {'_token': token,'productIDS': productIDS, 'is_approved' : is_approved},
            success: function(response){
                if(response.status == 'success'){
                    var res =  response.productIDS
                    res.forEach(function(id) {
                        if(response.is_approved == 1) {
                           $('#p-lock-c'+id).addClass('disable');
                           $('#p-lock-a'+id).removeClass('disable');
                           $('#location-code-wrp'+id).addClass('approved-cls').removeClass('declined-cls');
                           
                        } else {
                           $('#p-lock-c'+id).removeClass('disable');
                           $('#p-lock-a'+id).addClass('disable');
                           $('#location-code-wrp'+id).addClass('declined-cls').removeClass('approved-cls');
                        }
                    });
                }
            }
        })
        
    }
</script>
@endpush
