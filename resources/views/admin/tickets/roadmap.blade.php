@extends('layouts.app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i>Indema Roadmap</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12 text-right">
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li class="active">Indema Roadmap</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')


<style>

    .frill-embedded {
      width: 100%;
      height: 900px;
      margin: 0 auto;
      overflow: hidden;
      border: 1px solid #f0f0f0;
      border-radius: 5px;
    }
  </style>

@endpush

@section('content')

    

    <div class="row">
        <div class="col-md-12">
            <div class="white-box">
                <div class="row">
                    <div class="col-sm-12 m-t-20">
                        <div class="table-responsive">
                            
                            <!-- Frill Widget (https://frill.co) -->
                            <div data-frill-widget="1ab08e44-73df-462d-95b1-6d851e308fba" class="frill-embedded"></div>
<script async src="https://widget.frill.co/v2/widget.js"></script>
<!-- End Frill Widget --> 
                           
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!-- .row -->

@endsection

@push('footer-script')

@endpush
