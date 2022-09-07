@extends('layouts.app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> @lang($pageTitle)</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12 text-right">
            <a href="#" data-toggle="modal" data-target="#my-meeting" class="btn btn-sm btn-success btn-outline waves-effect waves-light">
                <i class="ti-plus"></i> @lang('zoom::modules.zoommeeting.addMeeting')
            </a>
            <a href="{{ route('admin.zoom-meeting.table-view') }}" class="btn btn-sm btn-info btn-outline waves-effect waves-light">
                <i class="ti-list"></i> @lang('zoom::modules.zoommeeting.tableView')
            </a>
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li class="active">@lang($pageTitle)</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/calendar/dist/fullcalendar.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/timepicker/bootstrap-timepicker.min.css') }}">

<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/multiselect/css/multi-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bootstrap-colorselector/bootstrap-colorselector.min.css') }}">
@endpush

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="white-box">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
    <!-- .row -->

    <!-- BEGIN MODAL -->
    <div class="modal fade bs-modal-md in" id="my-meeting" role="dialog" aria-labelledby="myModalLabel"
       aria-hidden="true">
      <div class="modal-dialog modal-lg" id="modal-data-application">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title"><i class="icon-plus"></i> @lang('zoom::modules.zoommeeting.addMeeting')</h4>
              </div>
              <div class="modal-body">
                  {!! Form::open(['id'=>'createMeeting','class'=>'ajax-form','method'=>'POST']) !!}
                  <div class="form-body">
                      <div class="row">
                          <div class="col-md-10 ">
                              <div class="form-group">
                                  <label class="required">@lang('zoom::modules.zoommeeting.meetingName')</label>
                                  <input type="text" name="meeting_title" id="meeting_title" class="form-control">
                              </div>
                          </div>

                          <div class="col-md-2 ">
                              <div class="form-group">
                                  <label>@lang('modules.sticky.colors')</label>
                                  <select id="colorselector" name="label_color">
                                      <option value="bg-info" data-color="#5475ed" selected>Blue</option>
                                      <option value="bg-warning" data-color="#f1c411">Yellow</option>
                                      <option value="bg-purple" data-color="#ab8ce4">Purple</option>
                                      <option value="bg-danger" data-color="#ed4040">Red</option>
                                      <option value="bg-success" data-color="#00c292">Green</option>
                                      <option value="bg-inverse" data-color="#4c5667">Grey</option>
                                  </select>
                              </div>
                          </div>

                      </div>

                      <div class="row">
                          <div class="col-xs-12 ">
                              <div class="form-group">
                                  <label>@lang('zoom::modules.zoommeeting.description')</label>
                                  <textarea name="description" id="description" class="form-control"></textarea>
                              </div>
                          </div>
                      </div>
                      <div class="row">
                          <div class="col-xs-12 col-md-3 ">
                              <div class="form-group">
                                  <label class="required">@lang('zoom::modules.zoommeeting.startOn')</label>
                                  <input type="text" name="start_date" id="start_date" class="form-control" autocomplete="none">
                              </div>
                          </div>
                          <div class="col-xs-12 col-md-3">
                              <div class="input-group bootstrap-timepicker timepicker">
                                  <label>&nbsp;</label>
                                  <input type="text" name="start_time" id="start_time" class="form-control">
                              </div>
                          </div>

                          <div class="col-xs-12 col-md-3">
                              <div class="form-group">
                                  <label class="required">@lang('zoom::modules.zoommeeting.endOn')</label>
                                  <input type="text" name="end_date" id="end_date" class="form-control" autocomplete="none">
                              </div>
                          </div>
                          <div class="col-xs-12 col-md-3">
                              <div class="input-group bootstrap-timepicker timepicker">
                                  <label>&nbsp;</label>
                                  <input type="text" name="end_time" id="end_time" class="form-control">
                              </div>
                          </div>
                      </div>
                      <div class="row">
                          <div class="col-xs-12" id="member-attendees">
                              <div class="form-group">
                                  <label class="col-xs-3 m-t-10">@lang('zoom::modules.meetings.addEmployees')</label>
                                  <div class="col-xs-7">
                                      <div class="checkbox checkbox-info">
                                          <input id="all-employees" name="all_employees" value="true" type="checkbox">
                                          <label for="all-employees">@lang('zoom::modules.meetings.allEmployees')</label>
                                      </div>
                                  </div>
                              </div>
                              <div class="form-group">
                                  <select class="select2 m-b-10 select2-multiple " multiple="multiple"
                                          data-placeholder="@lang('zoom::modules.message.chooseMember')" name="employee_id[]">
                                      @foreach($employees as $emp)
                                          <option value="{{ $emp->id }}">{{ ucwords($emp->name) }} @if($emp->id == $user->id)
                                                  (YOU) @endif</option>
                                      @endforeach
                                  </select>
                              </div>
                          </div>
                      </div>
                      <div class="row">
                          <div class="col-xs-12"  id="client-attendees">
                              <div class="form-group">
                                  <label class="col-xs-3 m-t-10">@lang('zoom::modules.meetings.addClients')</label>
                                  <div class="col-xs-7">
                                      <div class="checkbox checkbox-info">
                                          <input id="all-clients" name="all_clients" value="true" type="checkbox">
                                          <label for="all-clients">@lang('zoom::modules.meetings.allClients')</label>
                                      </div>
                                  </div>
                              </div>
                              <div class="form-group">
                                  <select class="select2 m-b-10 select2-multiple " multiple="multiple"
                                          data-placeholder="@lang('zoom::modules.message.selectClient')" name="client_id[]">
                                      @foreach($clients as $client)
                                          <option value="{{ $client->id }}">{{ ucwords($client->name) }} @if($client->id == $user->id)
                                                  (YOU) @endif</option>
                                      @endforeach
                                  </select>
                              </div>
                          </div>
                      </div>

                      <div class="row">
                          <div class="col-md-6">
                              <div class="form-group">
                                  <div class="m-b-10">
                                      <label class="control-label">@lang('zoom::modules.zoommeeting.hostVideoStatus')</label>
                                  </div>
                                  <div class="radio radio-inline">
                                      <input type="radio" name="host_video" id="host_video1" value="1">
                                      <label for="host_video1" class=""> @lang('app.enable') </label>
                                  </div>
                                  <div class="radio radio-inline ">
                                      <input type="radio" name="host_video" id="host_video2" value="0" checked>
                                      <label for="host_video2" class=""> @lang('app.disable') </label>
                                  </div>
                              </div>
                          </div>
                          <div class="col-md-6">
                              <div class="form-group">
                                  <div class="m-b-10">
                                      <label class="control-label">@lang('zoom::modules.zoommeeting.participantVideoStatus')</label>
                                  </div>
                                  <div class="radio radio-inline">
                                      <input type="radio" name="participant_video" id="participant_video1" value="1">
                                      <label for="participant_video1" class=""> @lang('app.enable') </label>
                                  </div>
                                  <div class="radio radio-inline ">
                                      <input type="radio" name="participant_video" id="participant_video2" value="0" checked>
                                      <label for="participant_video2" class=""> @lang('app.disable') </label>
                                  </div>
                              </div>
                          </div>
                      </div>

                      <div class="row">
                          <div class="form-group">
                              <div class="col-xs-6">
                                  <div class="checkbox checkbox-info">
                                      <input id="repeat-meeting" name="repeat" value="1"
                                             type="checkbox">
                                      <label for="repeat-meeting">@lang('zoom::modules.zoommeeting.repeat')</label>
                                  </div>
                              </div>
                          </div>
                      </div>

                      <div class="row" id="repeat-fields" style="display: none">
                          <div class="col-xs-6 col-md-3 ">
                              <div class="form-group">
                                  <label>@lang('zoom::modules.zoommeeting.repeatEvery')</label>
                                  <input type="number" min="1" value="1" name="repeat_every" class="form-control">
                              </div>
                          </div>
                          <div class="col-xs-6 col-md-3">
                              <div class="form-group">
                                  <label>&nbsp;</label>
                                  <select name="repeat_type" id="" class="form-control">
                                      <option value="day">@lang('app.day')</option>
                                      <option value="week">@lang('app.week')</option>
                                      <option value="month">@lang('app.month')</option>
                                      <option value="year">@lang('app.year')</option>
                                  </select>
                              </div>
                          </div>

                          <div class="col-xs-6 col-md-3">
                              <div class="form-group">
                                  <label>@lang('zoom::modules.zoommeeting.cycles') <a class="mytooltip" href="javascript:void(0)"> <i class="fa fa-info-circle"></i><span class="tooltip-content5"><span class="tooltip-text3"><span class="tooltip-inner2">@lang('zoom::modules.zoommeeting.cyclesToolTip')</span></span></span></a></label>
                                  <input type="text" name="repeat_cycles" id="repeat_cycles" class="form-control">
                              </div>
                          </div>
                      </div>

                      <div class="row">
                          <div class="form-group">
                              <div class="col-xs-6">
                                  <div class="checkbox checkbox-info">
                                      <input id="send_reminder" name="send_reminder" value="1"
                                              type="checkbox">
                                      <label for="send_reminder">@lang('zoom::modules.zoommeeting.reminder')</label>
                                  </div>
                              </div>
                          </div>
                      </div>

                      <div class="row" id="reminder-fields" style="display: none;">
                          <div class="col-xs-6 col-md-3">
                              <div class="form-group">
                                  <label>@lang('zoom::modules.zoommeeting.remindBefore')</label>
                                  <input type="number" min="1" value="1" name="remind_time" class="form-control">
                              </div>
                          </div>
                          <div class="col-xs-6 col-md-3">
                              <div class="form-group">
                                  <label>&nbsp;</label>
                                  <select name="remind_type" id="" class="form-control">
                                      <option value="day">@lang('app.day')</option>
                                      <option value="hour">@lang('app.hour')</option>
                                      <option value="minute">@lang('app.minute')</option>
                                  </select>
                              </div>
                          </div>
                      </div>
                  </div>
                  {!! Form::close() !!}

              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-white waves-effect" data-dismiss="modal">@lang('app.close')</button>
                  <button type="button" class="btn btn-success save-meeting waves-effect waves-light">@lang('app.submit')</button>
              </div>
          </div>
      </div>
    </div>
    {{-- End  --}}

    {{--Ajax Modal--}}
    <div class="modal fade bs-modal-md in" id="meetingDetailModal" role="dialog" aria-labelledby="myModalLabel"
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

<script>
    var taskEvents = [
        @foreach($events as $event)
        {
            id: '{{ ucfirst($event->id) }}',
            title: '{{ ucfirst($event->meeting_name) }}',
            start: '{{ $event->start_date_time }}',
            end:  '{{ $event->end_date_time }}',
            className: '{{ $event->label_color }}'
        },
        @endforeach
    ];

    var getEventDetail = function (id) {
        var url = "{{ route('admin.zoom-meeting.show', ':id')}}";
        url = url.replace(':id', id);

        $('#modelHeading').html('Meeting');
        $.ajaxModal('#meetingDetailModal', url);
    }

    var calendarLocale = '{{ $global->locale }}';
</script>

<script src="{{ asset('plugins/bower_components/moment/moment.js') }}"></script>
<script src="{{ asset('plugins/bower_components/calendar/dist/fullcalendar.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/calendar/dist/jquery.fullcalendar.js') }}"></script>
<script src="{{ asset('plugins/bower_components/calendar/dist/locale-all.js') }}"></script>
<script src="{{ asset('js/meeting-calendar.js') }}"></script>

<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/timepicker/bootstrap-timepicker.min.js') }}"></script>

<script src="{{ asset('js/cbpFWTabs.js') }}"></script>
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/multiselect/js/jquery.multi-select.js') }}"></script>
<script src="{{ asset('plugins/bootstrap-colorselector/bootstrap-colorselector.min.js') }}"></script>

<script>
    $(".select2").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });
    
    jQuery('#start_date, #end_date').datepicker({
        autoclose: true,
        todayHighlight: true,
        format: '{{ $global->date_picker_format }}',
    })

    $('#colorselector').colorselector();

    $('#start_time, #end_time').timepicker({
        @if($global->time_format == 'H:i')
        showMeridian: false,
        @endif
    });

    function addEventModal(start, end, allDay){
        $('.modal').modal('hide');
        if(start){
            $('#start_date, #end_date').datepicker('destroy');
            jQuery('#start_date, #end_date').datepicker({
                autoclose: true,
                todayHighlight: true,
                format: '{{ $global->date_picker_format }}'
            })

            jQuery('#start_date').datepicker('setDate', new Date(start));
            jQuery('#end_date').datepicker('setDate', new Date(start));
        }
        $('#my-meeting').modal('show');
    }

    $('.save-meeting').click(function () {
        $.easyAjax({
            url: "{{ route('admin.zoom-meeting.store') }}",
            container: '#modal-data-application',
            type: "POST",
            data: $('#createMeeting').serialize(),
            success: function (response) {
                if(response.status == 'success'){
                    window.location.reload();
                }
            }
        })
    })

    $('#repeat-meeting-new').change(function () {
        if($(this).is(':checked')){
            $('#repeat-fields-new').show();
        }
        else{
            $('#repeat-fields-new').hide();
        }
    })

    $('#send_reminder_new').change(function () {
        console.log($(this).is(':checked'));
        if($(this).is(':checked')){
            $('#reminder-fields-new').show();
        }
        else{
            $('#reminder-fields-new').hide();
        }
    })

</script>

@endpush
