@if(in_array('Zoom', $modules))
<li>
    <a href="{{ route('admin.zoom-setting.store') }}" class="waves-effect">
        <i class="fa fa-video-camera"></i>
        <span class="hide-menu">
            @lang('zoom::app.menu.zoom') <span class="fa arrow"></span>
        </span>
    </a>
    <ul class="nav nav-second-level">
        <li>
            <a href="{{ route('admin.zoom-meeting.table-view') }}">
                @lang('zoom::app.menu.zoomMeeting')
            </a>
        </li>
        <li>
            <a href="{{ route('admin.zoom-setting.store') }}">
                @lang('zoom::app.menu.zoomSetting')
            </a>
        </li>
    </ul>
</li>
@endif