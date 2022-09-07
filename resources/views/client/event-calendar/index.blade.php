@extends('layouts.client-app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="border-bottom col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-xs-12">
            <div class="col-md-12 pull-right hidden-xs hidden-sm">
                @if ($company_details->count() > 1)
                    <select class="selectpicker company-switcher margin-right-auto" data-width="fit" name="companies" id="companies">
                        @foreach ($company_details as $company_detail)
                            <option {{ $company_detail->company->id === $global->id ? 'selected' : '' }} value="{{ $company_detail->company->id }}">{{ ucfirst($company_detail->company->company_name) }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
            <ol class="breadcrumb">
                <li><a href="{{ route('client.dashboard.index') }}">@lang('app.menu.home')</a></li>
                <li class="active">{{ __($pageTitle) }}</li>
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
            <div class="white-box p-0">

                <div id="calendar"></div>
            </div>
        </div>
    </div>
    <!-- .row -->

    {{--Ajax Modal--}}
    <div class="modal fade bs-modal-md in" id="eventDetailModal" role="dialog" aria-labelledby="myModalLabel"
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
    var taskEvents = [];
    let events = {!! $events !!};

    events.forEach(event => {
        let taskEvent = {
            id: event.id,
            title: event.event_name+' '+event.start_time+'-'+event.end_time,
            start:  event.start_date_time,
            end:  event.end_date_time,
            className: event.label_color,
            repeat: event.repeat,
            repeat_time: event.repeat_every,
            repeat_type: event.repeat_type,
            repeat_cycles: event.repeat_cycles
        };
        taskEvents.push(taskEvent);
    });

    
    var options = {
        dayRender: function( date, cell ) {
            // Get all events
            // var events = $('#calendar').fullCalendar('clientEvents').length ? $('#calendar').fullCalendar('clientEvents') : taskEvents;
            var events = taskEvents;
                // Start of a day timestamp
            var dateTimestamp = date.startOf('day');
            var recurringEvents = new Array();
            
            // find all events with monthly repeating flag, having id, repeating at that day few months ago  
            var dailyEvents = events.filter(function (event) {
            return event.repeat === 'yes' && event.repeat_type === 'day' &&
                event.id &&
                moment(event.start).hour(0).minutes(0).diff(dateTimestamp, 'days', true) % event.repeat_time == 0
                && moment(event.start).startOf('day').isSameOrBefore(dateTimestamp);
            });

            // find all events with monthly repeating flag, having id, repeating at that day few months ago  
            var weeklyEvents = events.filter(function (event) {
            return event.repeat === 'yes' && event.repeat_type === 'week' &&
                event.id &&
                moment(event.start).hour(0).minutes(0).diff(dateTimestamp, 'weeks', true) % event.repeat_time == 0
                && moment(event.start).startOf('day').isSameOrBefore(dateTimestamp);
            });

            // find all events with monthly repeating flag, having id, repeating at that day few months ago  
            var monthlyEvents = events.filter(function (event) {
            return event.repeat === 'yes' && event.repeat_type === 'month' &&
                event.id &&
                moment(event.start).hour(0).minutes(0).diff(dateTimestamp, 'months', true) % event.repeat_time == 0
                && moment(event.start).startOf('day').isSameOrBefore(dateTimestamp);
            });
            
            // find all events with monthly repeating flag, having id, repeating at that day few years ago  
            var yearlyEvents = events.filter(function (event) {
            return event.repeat === 'yes' && event.repeat_type === 'year' &&
                event.id &&
                moment(event.start).hour(0).minutes(0).diff(dateTimestamp, 'years', true) % event.repeat_time == 0
                && moment(event.start).startOf('day').isSameOrBefore(dateTimestamp);
            });
            recurringEvents = [ ...monthlyEvents, ...yearlyEvents, ...weeklyEvents, ...dailyEvents ];

            $.each(recurringEvents, function(key, event) {
                if (event.repeat_cycles !== null) {
                    if(event.repeat_cycles > 0) {
                        event.repeat_cycles--;
                    }else {
                        return false;
                    }
                }
                var timeStart = moment(event.start).utc();
                var timeEnd = moment(event.end).utc();
                var diff = timeEnd.diff(timeStart, 'days', true);

                // Refething event fields for event rendering 
                var eventData = {
                    id: event.id,
                    title: event.title,
                    start: date.hour(timeStart.hour()).minutes(timeStart.minutes()).format("YYYY-MM-DD HH:mm:ss"),
                    end: event.end && diff >= 1 ? date.clone().add(diff, 'days').hour(timeEnd.hour()).minutes(timeEnd.minutes()).format("YYYY-MM-DD HH:mm:ss") : date.hour(timeEnd.hour()).minutes(timeEnd.minutes()).format("YYYY-MM-DD HH:mm:ss"),
                    className: event.className,
                    repeat: event.repeat,
                    repeat_time: event.repeat_time,
                    repeat_type: event.repeat_type,
                    repeat_cycles: event.repeat_cycles
                };
                
                // Removing events to avoid duplication
                $('#calendar').fullCalendar( 'removeEvents', function (event) {
                    return eventData.id === event.id &&
                    moment(event.start).isSame(date, 'day');      
                });
                // Render event
                $('#calendar').fullCalendar('renderEvent', eventData, true);
            });
        }
    }

    var getEventDetail = function (id, duration) {
        var url = `{{ route('client.events.show', ':id')}}?start=${duration.start.format('YYYY-MM-DD+HH:mm:ss')}&end=${duration.end.format('YYYY-MM-DD+HH:mm:ss')}`;

        url = url.replace(':id', id);

        $('#modelHeading').html('Event');
        $.ajaxModal('#eventDetailModal', url);
    }

    var calendarLocale = '{{ $global->locale }}';
    var firstDay = '{{ $global->week_start }}';
</script>

<script src="{{ asset('plugins/bower_components/calendar/jquery-ui.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/moment/moment.js') }}"></script>
<script src="{{ asset('plugins/bower_components/calendar/dist/fullcalendar.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/calendar/dist/jquery.fullcalendar.js') }}"></script>
<script src="{{ asset('plugins/bower_components/calendar/dist/locale-all.js') }}"></script>
<script src="{{ asset('js/event-calendar.js') }}"></script>

@endpush
