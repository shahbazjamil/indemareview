@extends('layouts.super-admin')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
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
                <div class="panel-heading">@lang('modules.quickbooks.updateTitle')</div>

                <div class="vtabs customvtab m-t-10">
                    @include('sections.super_admin_setting_menu')

                    <div class="tab-content">
                        <div id="vhome3" class="tab-pane active">
                            <div class="row">
                                <div class="col-sm-12 col-xs-12">
                                    <?php 
                                        if(isset($qbo)){ ?>
                                            {!! Form::open(['id'=>'UpdateQuickBookSettings','class'=>'ajax-form','method'=>'PUT']) !!}                                               
                                         <?php }else{ ?>
                                            {!! Form::open(['id'=>'StoreQuickBookSettings','class'=>'ajax-form','method'=>'POST']) !!}                                            
                                        <?php }
                                    ?>
                                    
                                    <div class="form-body">
                                        <div class="row">

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <?php $value = isset($qbo)? $qbo->client_id :'';?>
                                                    <label>@lang('modules.quickbooks.clientId')</label>
                                                    <input type="text" name="client_id" id="client_id"
                                                           class="form-control" value="{{$value}}">
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <?php $value = isset($qbo)? $qbo->client_secret :'';?>

                                                    <label>@lang('modules.quickbooks.clientSecret')</label>
                                                    <input type="text" name="client_secret" id="client_secret"
                                                           class="form-control" value="{{$value}}">
                                                </div>
                                            </div>
                                           
                                            <div class="col-md-12">
                                                <div class="form-group">
                                            
                                                   <?php 
                                                   $segment1 = ''; $segment2 = ''; $segment3 = '';
                                                   $segment1 =  Request::segment(0);
                                                   $segment2 =  Request::segment(1);
                                                   $segment3 =  Request::segment(2);
                                                   $host = Request::getSchemeAndHttpHost();
                                                   $url = 'https://app.indema.com/admin/settings/quickbooks-connect' ;
                                                   $value = isset($qbo) && !empty($qbo->client_secret) ? $url : $qbo->redirect_url;
                                            

                                                   ?>
                                                   
                                                    <input type="hidden" name="redirect_url" id="redirect_url"
                                                           class="form-control" value="{{$value}}">
                                                </div>
                                            </div>

                                            <!-- Connect button  -->
                                            <!-- if(!empty(qb->client_secret)){

                                        }

 -->                                    
                                           <!--  <?php if(isset($qbo)  && !empty($qbo->client_secret)){ ?>
                                                <div class="col-md-12 text-center">
                                                <a href="{{$url}}"><img src="{{ asset('img/qb.png') }}" alt="user-img"  width="150"></a>
                                            </div>

                                             <?php } ?> -->
                                            
                                        



                                        </div>

                                    </div>

                                    <div class="form-actions" style="margin-top: 25px;">
                                        <button type="submit" id="save-form-2" class="btn btn-success"><i
                                                    class="fa fa-check"></i>
                                            @lang('app.save')
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
            url: '{{route('super-admin.super-admin-quickbooks.update', [$qbo->id])}}',
            container: '#UpdateQuickBookSettings',
            type: "POST",
            redirect: true,
            
            data: $('#UpdateQuickBookSettings').serialize(),
            success: function (data) {
                if (data.status == 'success') {
                    window.location.reload();
                }
            }
        })
    });
</script>

@if(\Session::has('message'))
<script>
    toastr.success("{{  \Session::get('message') }}");
</script>
@endif
@endpush
