@if(in_array('Zoom', $modules))
    <li>
        <a href="{{ route('client.zoom-meeting.index') }}">
            <i class="fa fa-video-camera"></i> @lang('zoom::app.menu.zoomMeeting')
        </a>
    </li>
@endif