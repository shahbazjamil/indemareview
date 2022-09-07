<li class="top-notifications">
    <div class="message-center">
        <a href="{{ route('member.zoom-meeting.index') }}">
            <div class="user-img">
                <span class="btn btn-circle btn-inverse"><i class="fa fa-video-camera fa-fw"></i></span>
            </div>
            <div class="mail-contnet">
                <span
                    class="mail-desc m-0">{{ __('zoom::email.newMeeting.subject') }}</span>
                    <small>{{ $notification->data['meeting_name'] }}</small>
                <span class="time">{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $notification->created_at)->diffForHumans() }}</span>
            </div>
        </a>
    </div>
</li>