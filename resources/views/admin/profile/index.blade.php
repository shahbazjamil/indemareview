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
                <div class="panel-heading p-t-10 p-b-10">@lang('modules.profile.updateTitle')</div>

                <div class="vtabs customvtab m-t-10">
                    @include('sections.admin_setting_menu')

                    <div class="tab-content p-0 p-t-20">
                        <div id="vhome3" class="tab-pane active">
                            <div class="row">
                                <div class="col-sm-12 col-xs-12">
                                    {!! Form::open(['id'=>'updateProfile','class'=>'ajax-form','method'=>'PUT']) !!}
                                    <div class="form-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>@lang('modules.profile.yourName')</label>
                                                    <input type="text" name="name" id="name"
                                                           class="form-control" value="{{ $userDetail->name }}">
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>@lang('modules.profile.yourEmail')</label>
                                                    <input  type="email" name="email" id="email"
                                                           class="form-control" value="{{ $userDetail->email }}">
                                                </div>
                                            </div>
                                            <!--/span-->
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>@lang('modules.profile.yourPassword')</label>
                                                    <input type="password" name="password" id="password"
                                                           class="form-control">
                                                    <span class="fa fa-fw fa-eye field-icon toggle-password"></span>
                                                    <span class="help-block"> @lang('modules.profile.passwordNote')</span>
                                                </div>
                                            </div>
                                        </div>
{{--                                        <div class="row">--}}


{{--                                            <!--/span-->--}}

{{--                                            <div class="col-md-4">--}}
{{--                                                <div class="form-group">--}}
{{--                                                    <label>@lang('modules.profile.yourMobileNumber')</label>--}}
{{--                                                    <input type="tel" name="mobile" id="mobile" class="form-control"--}}
{{--                                                           value="{{ $userDetail->mobile }}">--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                            <!--/span-->--}}

{{--                                            <div class="col-md-4">--}}
{{--                                                <div class="form-group">--}}
{{--                                                    <label>@lang('modules.employees.gender')</label>--}}
{{--                                                    <select name="gender" id="gender" class="form-control">--}}
{{--                                                        <option @if($userDetail->gender == 'male') selected @endif value="male">@lang('app.male')</option>--}}
{{--                                                        <option @if($userDetail->gender == 'female') selected @endif value="female">@lang('app.female')</option>--}}
{{--                                                        <option @if($userDetail->gender == 'others') selected @endif value="others">@lang('app.others')</option>--}}
{{--                                                    </select>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                            <!--/span-->--}}
{{--                                        </div>--}}
                                        <!--/row-->

                                        <div class="row">
                                            
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="control-label">@lang('modules.profile.yourAddress')</label>
                                        <textarea name="address" id="address" rows="3"
                                                  class="form-control">@if(!empty($employeeDetail)){{ $employeeDetail->address }}@endif</textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <div class="m-b-10">
                                                        <label class="control-label">@lang('modules.emailSettings.emailNotifications')</label>
                                                    </div>
                                                    <div class="radio radio-inline">
                                                        <input type="radio" 
                                                        @if ($userDetail->email_notifications)
                                                            checked
                                                        @endif
                                                        name="email_notifications" id="email_notifications1" value="1">
                                                        <label for="email_notifications1" class="">
                                                            @lang('app.enable') </label>
            
                                                    </div>
                                                    <div class="radio radio-inline ">
                                                        <input type="radio" name="email_notifications"
                                                        @if (!$userDetail->email_notifications)
                                                            checked
                                                        @endif
            
                                                               id="email_notifications2" value="0">
                                                        <label for="email_notifications2" class="">
                                                            @lang('app.disable') </label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <label>@lang('modules.profile.profilePicture')</label>

                                                <div class="form-group">
                                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                                        <div class="fileinput-new thumbnail"
                                                             style="width: 200px; height: 150px;">
                                                            <img src="{{ $userDetail->image_url  }}" alt=""/>
                                                        </div>
                                                        <div class="fileinput-preview fileinput-exists thumbnail"
                                                             style="max-width: 200px; max-height: 150px;"></div>
                                                        <div>
                                                        <span class="btn btn-info btn-file">
                                                            <span class="fileinput-new"> @lang('app.selectImage') </span>
                                                            <span class="fileinput-exists"> @lang('app.change') </span>
                                                            <input type="file" name="image" id="image"> </span>
                                                            <a href="javascript:;"
                                                               class="btn btn-danger fileinput-exists"
                                                               data-dismiss="fileinput"> @lang('app.remove') </a>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>


                                        </div>
                                        <!--/span-->


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
            url: '{{route('member.profile.update', [$userDetail->id])}}',
            container: '#updateProfile',
            type: "POST",
            redirect: true,
            file: (document.getElementById("image").files.length == 0) ? false : true,
            data: $('#updateProfile').serialize(),
            success: function (data) {
                if (data.status == 'success') {
                    window.location.reload();
                }
            }
        })
    });
</script>
@endpush
