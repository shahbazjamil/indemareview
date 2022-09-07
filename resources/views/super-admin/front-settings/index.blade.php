@extends('layouts.super-admin')

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
                <li><a href="{{ route('super-admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li class="active">{{ __($pageTitle) }}</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/switchery/dist/switchery.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">
@endpush

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-inverse">
                <div class="panel-heading">@lang('modules.frontCms.updateTitle')</div>

                <div class="vtabs customvtab m-t-10">
                    @include('sections.front_setting_menu')

                    <div class="tab-content">
                        <div id="vhome3" class="tab-pane active">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="white-box">
                                        <h3 class="box-title m-b-0"> @lang("modules.frontSettings.title")</h3>

                                        <div class="row">
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Form::open(['id'=>'editSettings','class'=>'ajax-form','method'=>'PUT']) !!}
                                                <h4>@lang('modules.frontCms.frontDetail')</h4>
                                                <hr>
                                                <div class="row">
                                                    <div class="col-sm-12 col-md-6 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="company_name">@lang('modules.frontCms.headerTitle')</label>
                                                            <input type="text" class="form-control" id="header_title" name="header_title"
                                                                   value="{{ $frontDetail->header_title }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="address">@lang('modules.frontCms.headerDescription')</label>
                                                            <textarea class="form-control summernote" id="header_description" rows="5"
                                                                      name="header_description">{{ $frontDetail->header_description }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="address">@lang('modules.frontCms.defaultLanguage')</label>
                                                            <select name="default_language" id="default_language" class="form-control select2">
                                                                <option @if($frontDetail->locale == "en") selected @endif value="en">English
                                                                </option>
                                                                @foreach($languageSettings as $language)
                                                                    <option value="{{ $language->language_code }}" @if($frontDetail->locale == $language->language_code) selected @endif >{{ $language->language_name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12 col-md-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="custom_css" class="d-block">@lang('modules.frontCms.customCss')</label>
                                                            <textarea name="custom_css" class="my-code-area" rows="20" style="width: 100%">@if(is_null($frontDetail->custom_css))/*Enter your auth css after this line*/ @else {!! $frontDetail->custom_css !!} @endif</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12 col-md-6 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="exampleInputPassword1">@lang('modules.frontCms.mainImage')</label>
                                                            <div class="col-md-12">
                                                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                                                    <div class="fileinput-new thumbnail"
                                                                         style="width: 200px; height: 150px;">
                                                                        <img src="{{ $frontDetail->image_url }}" alt=""/>
                                                                    </div>
                                                                    <div class="fileinput-preview fileinput-exists thumbnail"
                                                                         style="max-width: 200px; max-height: 150px;"></div>
                                                                    <div>
                                <span class="btn btn-info btn-file">
                                    <span class="fileinput-new"> @lang('app.selectImage') </span>
                                    <span class="fileinput-exists"> @lang('app.change') </span>
                                    <input type="file" name="image" id="image"> </span>
                                                                        <a href="javascript:;" class="btn btn-danger fileinput-exists"
                                                                           data-dismiss="fileinput"> @lang('app.remove') </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <div class="checkbox checkbox-info  col-md-10">
                                                                <input id="get_started_show" name="get_started_show" value="yes"
                                                                       @if($frontDetail->get_started_show == "yes") checked
                                                                       @endif
                                                                       type="checkbox">
                                                                <label for="get_started_show">@lang('modules.frontCms.getStartedButtonShow')</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <div class="checkbox checkbox-info  col-md-10">
                                                                <input id="sign_in_show" name="sign_in_show" value="yes"
                                                                       @if($frontDetail->sign_in_show == "yes") checked
                                                                       @endif
                                                                       type="checkbox">
                                                                <label for="sign_in_show">@lang('modules.frontCms.singInButtonShow')</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="alert alert-info"><i class="fa fa-info-circle"></i> @lang('messages.headerImageSizeMessage')</div>
                                                    </div>
                                                </div>
                                                <h4>@lang('modules.frontCms.featureDetail')</h4>
                                                <hr>
                                                <div class="row">
                                                    <div class="col-sm-12 col-md-6 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="feature_title">@lang('modules.frontCms.featureTitle')</label>
                                                            <input type="tel" class="form-control" id="feature_title" name="feature_title"
                                                                   value="{{ $frontDetail->feature_title }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="feature_description">@lang('modules.frontCms.featureDescription')</label>
                                                            <textarea class="form-control" id="feature_description" rows="5"
                                                                      name="feature_description">{{ $frontDetail->feature_description }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <h4>@lang('modules.frontCms.priceDetail')</h4>
                                                <hr>
                                                <div class="row">
                                                    <div class="col-sm-12 col-md-6 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="price_title">@lang('modules.frontCms.priceTitle')</label>
                                                            <input type="tel" class="form-control" id="price_title" name="price_title"
                                                                   value="{{ $frontDetail->price_title }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="price_description">@lang('modules.frontCms.priceDescription')</label>
                                                            <textarea class="form-control" id="price_description" rows="5"
                                                                      name="price_description">{{ $frontDetail->price_description }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <h4>@lang('modules.frontCms.contactDetail')</h4>
                                                <hr>
                                                <div class="row">
                                                    <div class="col-sm-12 col-md-6 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="email">@lang('app.email')</label>
                                                            <input type="email" class="form-control" id="email" name="email"
                                                                   value="{{ $frontDetail->email }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-12 col-md-6 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="phone">@lang('modules.accountSettings.companyPhone')</label>
                                                            <input type="tel" class="form-control" id="phone" name="phone"
                                                                   value="{{ $frontDetail->phone }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="address">@lang('modules.accountSettings.companyAddress')</label>
                                                            <textarea class="form-control" id="address" rows="5"
                                                                      name="address">{{ $frontDetail->address }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="address">@lang('modules.accountSettings.htmlOrEmbeded')</label>
                                                            <textarea class="form-control" id="contact_html" rows="10"
                                                                      name="contact_html"> {!! $frontDetail->contact_html !!} </textarea>
                                                        </div>
                                                    </div>
                                                </div>

                                                <h4 id="social-links">@lang('modules.frontCms.socialLinks')</h4>
                                                <hr>
                                                <span class="text-danger">@lang('modules.frontCms.socialLinksNote')</span><br><br>
                                                @forelse(json_decode($frontDetail->social_links) as $link)
                                                    <div class="row">
                                                        <div class="col-sm-12 col-xs-12">
                                                            <div class="form-group">
                                                                <label for="{{ $link->name }}">
                                                                    @lang('modules.frontCms.'.$link->name)
                                                                </label>
                                                                <input
                                                                        class="form-control"
                                                                        id="{{ $link->name }}"
                                                                        name="social_links[{{ $link->name }}]"
                                                                        type="url"
                                                                        value="{{ $link->link }}"
                                                                        placeholder="@lang('modules.frontCms.enter'.ucfirst($link->name).'Link')">
                                                            </div>
                                                        </div>
                                                    </div>
                                                @empty

                                                @endforelse
                                                <button type="submit" id="save-form"
                                                        class="btn btn-success waves-effect waves-light m-r-10">
                                                    @lang('app.update')
                                                </button>

                                                {!! Form::close() !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <!-- .row -->
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>


    </div>
    <!-- .row -->



@endsection

@push('footer-script')
    <script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>
<script src="{{ asset('plugins/ace/ace.js') }}"></script>
<script src="{{ asset('plugins/ace/theme-twilight.js') }}"></script>
<script src="{{ asset('plugins/ace/mode-css.js') }}"></script>
<script src="{{ asset('plugins/ace/jquery-ace.min.js') }}"></script>
<script>
    $('.my-code-area').ace({ theme: 'twilight', lang: 'css' })
    $('.summernote').summernote({
        height: 200,                 // set editor height
        minHeight: null,             // set minimum height of editor
        maxHeight: null,             // set maximum height of editor
        focus: false,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link',  'hr','video']],
            ['view', ['fullscreen', 'codeview']],
            ['help', ['help']]
        ]
    });

    $('#save-form').click(function () {
        $.easyAjax({
            url: '{{route('super-admin.front-settings.update', $frontDetail->id)}}',
            container: '#editSettings',
            type: "POST",
            redirect: true,
            file: true,
        })
    });

</script>
@endpush
