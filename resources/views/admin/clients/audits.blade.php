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
                <li><a href="{{ route('admin.clients.index') }}">{{ __($pageTitle) }}</a></li>
                <li class="active">@lang('app.menu.invoices')</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection
<style>
    .activities .activity .activity-detail{
        padding: 6px 12px;
        background: #FAFAFA;
        border: 1px solid rgb(227, 227, 227);
        border-radius: 10px;
        margin-bottom: 20px;
        position: relative;
    }
    /*.activities .activity .activity-detail:before{*/
    /*    content: ' ';*/
    /*    position: absolute;*/
    /*    left: 296px;*/
    /*    top: 63px;*/
    /*    width: 2px;*/
    /*    height: 33%;*/
    /*    background-color: #ccc !important;*/
    /*}*/
    .activities .activity .activity-col{
        display: flex;
        align-items: center;
    }
    .activities .activity .activity-time-div{
        padding: 5px;
        padding-left: 30px;
    }
    .activities .activity .activity-icon-div .activity-icon i{
        font-size: 30px;
    }
    .activities .activity .activity-icon-div-arrow{
        border: 1px solid #ccc;
        width: 22px;
        transform: rotate(90deg);
        position: absolute;
        margin-top: 83px;
        margin-left: 3px;
    }
    .activities .activity .green{
        color: #34d517;
    }
    .activities .activity .orange{
        color: orange;
    }
</style>

@section('content')

    <div class="row">
        @include('admin.clients.client_header')
        <div class="col-md-12">
            <section>
                <div class="sttabs tabs-style-line">
                    @include('admin.clients.tabs')
                    <div class="content-wrap">
                        <section id="section-line-1" class="show">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="white-box p-0">
                                        <div class="row justify-content-start">
                                            <div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                                <div class="activities">
                                                    <div class="activity-div">
                                                        <div class="activity">
                                                            @forelse($audits as $audit)
                                                                <div class="activity-detail d-flex">
                                                                    <div class="col-xs-4 col-sm-4 col-md-2 col-lg-2 d-flex">
                                                                        <div class="activity-time-div">
                                                                            @php
                                                                                $deliverAt = new \DateTime($audit->deliver_at, new \DateTimeZone("UTC"));
                                                                                $deliverAt->setTimezone(new \DateTimeZone("Asia/Kolkata"));
                                                                                $deliverAt = $deliverAt->format("Y-m-d H:i:s");
                                                                                $deliverAt = \Carbon\Carbon::parse($deliverAt);
                                                                                $convertDeliverAt = date("g:i:s A", strtotime($deliverAt));
                                                                            @endphp
                                                                            <span>{{ \Carbon\Carbon::parse($audit->deliver_at)->format('l, F d') }},</span><br>
                                                                            <span>{{ \Carbon\Carbon::parse($audit->deliver_at)->format('Y') }}, {{ $convertDeliverAt }}</span>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-xs-2 col-sm-2 col-md-1 col-lg-1 activity-col">
                                                                        <div class="activity-icon-div">
                                                                            <span class="activity-icon"><i class="fa fa-{{ $audit->icon == 'hourglass-start' ? 'hourglass-start orange' : 'envelope green' }}"></i></span>
                                                                        </div>
                                                                        @if(!$loop->last)
                                                                            <div class="activity-icon-div-arrow"></div>
                                                                        @endif
                                                                    </div>
                                                                    <div class="col-xs-6 col-sm-6 col-md-9 col-lg-9 activity-col">
                                                                        <div class="activity-title-div">
                                                                            <span class="activity-title">{!! $audit->title !!}</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @empty
                                                                <div class="text-center">
                                                                    <h3>No audit yet...</h3>
                                                                </div>
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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

@endsection

@push('footer-script')
    <script>
        $('ul.showClientTabs .clientPayments').addClass('tab-current');
    </script>
@endpush