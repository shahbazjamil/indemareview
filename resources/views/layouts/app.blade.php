<!DOCTYPE html>

<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('favicon/apple-icon-57x57.png') }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('favicon/apple-icon-60x60.png') }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('favicon/apple-icon-72x72.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('favicon/apple-icon-76x76.png') }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('favicon/apple-icon-114x114.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('favicon/apple-icon-120x120.png') }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('favicon/apple-icon-144x144.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('favicon/apple-icon-152x152.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon/apple-icon-180x180.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('favicon/android-icon-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicon/favicon-96x96.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('favicon/manifest.json') }}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ asset('favicon/ms-icon-144x144.png') }}">
    <meta name="theme-color" content="#ffffff">

    <title>@lang('app.adminPanel') | {{ __($pageTitle) }}</title>
    <!-- Bootstrap Core CSS -->
    <link href="{{ asset('bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel='stylesheet prefetch'
          href='https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/0.8.2/css/flag-icon.min.css'>
    <link rel='stylesheet prefetch'
          href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.2/css/bootstrap-select.min.css'>

    <!-- This is Sidebar menu CSS -->
    <link href="{{ asset('plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css') }}" rel="stylesheet">

    <link href="{{ asset('plugins/bower_components/toast-master/css/jquery.toast.css') }}" rel="stylesheet">
    <link href="{{ asset('plugins/bower_components/sweetalert/sweetalert.css') }}" rel="stylesheet">

    <!-- This is a Animation CSS -->
    <link href="{{ asset('css/animate.css') }}" rel="stylesheet">

    @stack('head-script')

            <!-- This is a Custom CSS -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <!-- color CSS you can use different color css from css/colors folder -->
    <!-- We have chosen the skin-blue (default.css) for this starter
       page. However, you can choose any other skin from folder css / colors .
       -->
    <link href="{{ asset('css/colors/default.css') }}" id="theme" rel="stylesheet">
    <link href="{{ asset('plugins/froiden-helper/helper.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/magnific-popup.css') }}">
    <link href="{{ asset('css/custom-new.css?v=124') }}" rel="stylesheet">

    @if($global->rounded_theme)
    <link href="{{ asset('css/rounded.css') }}" rel="stylesheet">
    @endif

    @if(file_exists(public_path().'/css/admin-custom.css'))
    <link href="{{ asset('css/admin-custom.css') }}" rel="stylesheet">
    @endif


    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
<script>
//jquery(document).ready(function(){
//window.onload=function(){
   var theme = localStorage.getItem('data-theme');
   if(theme=='light'){
     document.documentElement.setAttribute('data-theme', 'light');
   }else if(theme==''){
     document.documentElement.setAttribute('data-theme', 'light');
   }else if(theme=='dark'){
     document.documentElement.setAttribute('data-theme' , 'dark');
     document.getElementById("checkbox").checked = true;
   }
 //}
// });
 function toggle(a){
   if(a.checkbox.checked==true){
     document.documentElement.classList.add('transition');
     document.documentElement.setAttribute('data-theme', 'dark');
     localStorage.setItem('data-theme','dark');
   }
   else if(a.checkbox.checked==false){
     document.documentElement.classList.add('transition');
     document.documentElement.setAttribute('data-theme', 'light');
     localStorage.setItem('data-theme','light');
   }
 };
</script>
    @if($pushSetting->status == 'active')
    <link rel="manifest" href="{{ asset('manifest.json') }}" />
    <script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async='async'></script>
    <script>
        var OneSignal = window.OneSignal || [];
        OneSignal.push(function() {
            OneSignal.init({
                appId: "{{ $pushSetting->onesignal_app_id }}",
                autoRegister: false,
                notifyButton: {
                    enable: false,
                },
                promptOptions: {
                    /* actionMessage limited to 90 characters */
                    actionMessage: "We'd like to show you notifications for the latest news and updates.",
                    /* acceptButtonText limited to 15 characters */
                    acceptButtonText: "ALLOW",
                    /* cancelButtonText limited to 15 characters */
                    cancelButtonText: "NO THANKS"
                }
            });
            OneSignal.on('subscriptionChange', function (isSubscribed) {
                console.log("The user's subscription state is now:", isSubscribed);
            });


            if (Notification.permission === "granted") {
                // Automatically subscribe user if deleted cookies and browser shows "Allow"
                OneSignal.getUserId()
                    .then(function(userId) {
                        if (!userId) {
                            OneSignal.registerForPushNotifications();
                        }
                        else{
                            let db_onesignal_id = '{{ $user->onesignal_player_id }}';

                            if(db_onesignal_id == null || db_onesignal_id !== userId){ //update onesignal ID if it is new
                                updateOnesignalPlayerId(userId);
                            }
                        }
                    })
            } else {
                OneSignal.isPushNotificationsEnabled(function(isEnabled) {
                    if (isEnabled){
                        console.log("Push notifications are enabled! - 2    ");
                        // console.log("unsubscribe");
                        // OneSignal.setSubscription(false);
                    }
                    else{
                        console.log("Push notifications are not enabled yet. - 2");
                        // OneSignal.showHttpPrompt();
                        // OneSignal.registerForPushNotifications({
                        //         modalPrompt: true
                        // });
                    }

                    OneSignal.getUserId(function(userId) {
                        console.log("OneSignal User ID:", userId);
                        // (Output) OneSignal User ID: 270a35cd-4dda-4b3f-b04e-41d7463a2316
                        let db_onesignal_id = '{{ $user->onesignal_player_id }}';
                        console.log('database id : '+db_onesignal_id);

                        if(db_onesignal_id == null || db_onesignal_id !== userId){ //update onesignal ID if it is new
                           updateOnesignalPlayerId(userId);
                        }


                    });


                    OneSignal.showHttpPrompt();
                });

            }
        });
    </script>
    @endif

    @if($global->active_theme == 'custom')
    {{--Custom theme styles--}}
    <style>
        :root {
            --header_color: {{ $adminTheme->header_color }};
            --sidebar_color: {{ $adminTheme->sidebar_color }};
            --link_color: {{ $adminTheme->link_color }};
            --sidebar_text_color: {{ $adminTheme->sidebar_text_color }};
        }

        .pace .pace-progress {
            background: var(--header_color);
        }

        .menu-footer,.menu-copy-right{
            border-top: 1px solid #2f3544;
            background: var(--sidebar_color);
        }
        .navbar-header {
            background: var(--header_color);
        }
        .content-wrapper .sidebar #side-menu>li:hover{
            background: var(--sidebar_color);
        }
        .sidebar-nav .notify {
            margin: 0 !important;
        }
        .sidebar .notify .heartbit {
            border: 5px solid var(--header_color) !important;
            top: -23px !important;
            right: -15px !important;
        }
        .sidebar .notify .point {
            background-color: var(--header_color) !important;
            top: -13px !important;
        }

        .navbar-top-links > li > a {
            color: var(--link_color);
        }
        /*Right panel*/
        .right-sidebar .rpanel-title {
            background: var(--header_color);
        }
        /*Bread Crumb*/
        .bg-title .breadcrumb .active {
            color: var(--header_color);
        }
        /*Sidebar*/
        .sidebar {
            background: var(--sidebar_color);
            box-shadow: 1px 0px 20px rgba(0, 0, 0, 0.08);
        }
        .sidebar .label-custom {
            background: var(--header_color);
        }
        #side-menu li a, #side-menu > li:not(.user-pro) > a {
            color: var(--sidebar_text_color);
            border-left: 0 solid var(--sidebar_color);
        }
        #side-menu > li > a:hover,
        #side-menu > li > a:focus {
            background: rgba(0, 0, 0, 0.07);
        }
        #side-menu > li > a.active {
            /* border-left: 3px solid var(--header_color); */
            color: var(--link_color);
            background: var(--header_color);
        }
        #side-menu > li > a.active i {
            color: var(--link_color);
        }
        #side-menu ul > li > a:hover {
            color: var(--link_color);
        }
        #side-menu ul > li > a.active, #side-menu ul > li > a:hover {
            color: var(--header_color);
        }
        .sidebar #side-menu .user-pro .nav-second-level a:hover {
            color: var(--header_color);
        }
        .nav-small-cap {
            color: var(--sidebar_text_color);
        }
        /* .content-wrapper .sidebar .nav-second-level li {
            background: #444859;
        }
        @media (min-width: 768px) {
            .content-wrapper #side-menu ul,
            .content-wrapper .sidebar #side-menu > li:hover,
            .content-wrapper .sidebar .nav-second-level > li > a {
                background: #444859;
            }
        } */

        /*themecolor*/
        .bg-theme {
            background-color: var(--header_color) !important;
        }
        .bg-theme-dark {
            background-color: var(--sidebar_color) !important;
        }
        /*Chat widget*/
        .chat-list .odd .chat-text {
            background: var(--header_color);
        }
        /*Button*/
        .btn-custom {
            background: var(--header_color);
            border: 1px solid var(--header_color);
            color: var(--link_color);
        }
        .btn-custom:hover {
            background: var(--header_color);
            border: 1px solid var(--header_color);
        }
        /*Custom tab*/
        .customtab li.active a,
        .customtab li.active a:hover,
        .customtab li.active a:focus {
            border-bottom: 2px solid var(--header_color);
            color: var(--header_color);
        }
        .tabs-vertical li.active a,
        .tabs-vertical li.active a:hover,
        .tabs-vertical li.active a:focus {
            background: var(--header_color);
            border-right: 2px solid var(--header_color);
        }
        /*Nav-pills*/
        .nav-pills > li.active > a,
        .nav-pills > li.active > a:focus,
        .nav-pills > li.active > a:hover {
            background: var(--header_color);
            color: var(--link_color);
        }

        .admin-panel-name{
            background: var(--header_color);
        }

        /*fullcalendar css*/
        .fc th.fc-widget-header{
            background: var(--sidebar_color);
        }

        .fc-button{
            background: var(--header_color);
            color: var(--link_color);
            margin-left: 2px !important;
        }

        .fc-unthemed .fc-today{
            color: #757575 !important;
        }

        .user-pro{
            background-color: var(--sidebar_color);
        }


        .top-left-part{
            background: var(--sidebar_color);
        }

        .notify .heartbit{
            border: 5px solid var(--sidebar_color);
        }

        .notify .point{
            background-color: var(--sidebar_color);
        }
        .dropdown-menu.mailbox{
            padding-top: 0;
        }
    </style>

    <style>
        {!! $adminTheme->user_css !!}
    </style>
    {{--Custom theme styles end--}}
    @endif

    <style>
        .sidebar .notify  {
        margin: 0 !important;
        }
        .sidebar .notify .heartbit {
        top: -23px !important;
        right: -15px !important;
        }
        .sidebar .notify .point {
        top: -13px !important;
        }
        /* .content-wrapper .sidebar #side-menu>li>.active {
            background: transparent;
        }
        .content-wrapper .sidebar #side-menu>li>.active:hover {
            background: #272d36;
        } */
		
		.menu-footer{display:none !important;}
		.nav-bar-new{
	margin:0 -45px;background:#E4E2E3;padding:5px 15px;border-bottom:1px solid #cdcdcd;display:flex;align-items:center;
}
.nav-bar-new .search{position:relative;z-index:9}
.nav-bar-new .search>div.srh{background: #fff;border-radius: 4px;padding: 5px;position: relative;width: 200px;}
.nav-bar-new .search>div.srh .fa{color: #999;position: absolute;left: 5px;top: 9px;}
.nav-bar-new .search>div.srh input{background: none;border: none;width: 100%;padding:0 10px 0 20px}
.nav-bar-new>ul {
    list-style-type: none;
    display: inline-flex;
    justify-content: flex-end;
    width: calc(100% - 50px);
    margin: 0;
}
.nav-bar-new li {
    padding: 0 10px;
}
.search-dropdown{
	position: absolute;
    background: #FFF;
    padding: 20px;
    width: 500px;
    border-radius: 0 0 5px 5px;
    box-shadow: 0 8px 10px #ccc;
    max-height: 531px;
    overflow-y: auto;
}
.ui-widget-content{
	background: #FFF;
    padding: 20px;
    width: 500px;
    border-radius: 0 0 5px 5px;
    box-shadow: 0 8px 10px #ccc;
	list-style-type:none;
}
.ui-widget-content li {
    padding: 5px 0;
}
.list-row {
    display: flex;
    justify-content: space-between;
    padding: 10px;
    border-radius: 10px;
    align-items: center;
}
.list-row:hover {
    background: #EEEFF3;
}
.list-row .label-info {
    background-color: #03a9f3 !important;
    color: #FFF !important;
}
.list-row .label-default {
    background-color: #777 !important;
    color: #fff !important;
}
.list-row .label-success {
    background-color: #00c292 !important;
    color: #FFF !important;
}
.list-row a, .link-text {
    color: #b2b4bf !important;
}
.list-row a img {
    border-radius: 4px;
}
    </style>

    <link rel="manifest" href="" />
    <script src="{{ asset('js/lm.js') }}" async='async'></script>
    
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-W3WDBV4');</script>
    <!-- End Google Tag Manager -->

</head>


<body class="fix-sidebar @if(request()->route()->getName() == 'admin.account-setup.index' ) account-layout @endif ">
    
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-W3WDBV4"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    
    
<!-- Preloader -->
<div class="preloader">
    <div class="cssload-speeding-wheel"></div>
</div>
<div id="wrapper" class="wc-change-layout">
    <!-- Left navbar-header -->
    @include('sections.left_sidebar')
            <!-- Left navbar-header end -->
    <!-- Page Content -->
    <div id="page-wrapper" class="row">
        <div class="container-fluid">

            @if (!empty($__env->yieldContent('filter-section')))
                <div class="col-md-3 filter-section ppp">
                    <h5 class=""><i class="fa fa-sliders"></i> @lang('app.filterResults')</h5>
                    <h5 class="pull-right hidden-sm hidden-md hidden-xs">
                        <button class="btn btn-default btn-xs btn-circle btn-outline filter-section-close" ><i class="fa fa-chevron-left"></i></button>
                    </h5>

                    @yield('filter-section')
                </div>
             @endif

             @if (!empty($__env->yieldContent('other-section')))
                <div class="col-md-3 filter-section pp">
                    @yield('other-section')
                </div>
             @endif


            <div class="
            @if (!empty($__env->yieldContent('filter-section')) || !empty($__env->yieldContent('other-section')))
            col-md-9
            @else
            col-md-12
            @endif
            data-section">
                <button class="btn btn-default btn-xs btn-outline btn-circle m-t-5 filter-section-show hidden-sm hidden-md" style="display:none"><i class="fa fa-chevron-right"></i></button>
                @if (!empty($__env->yieldContent('filter-section')) || !empty($__env->yieldContent('other-section')))
                    <div class="row hidden-md hidden-lg">
                        <div class="col-xs-12 p-l-25 m-t-10">
                            <button class="btn btn-inverse btn-outline" id="mobile-filter-toggle"><i class="fa fa-sliders"></i></button>
                        </div>
                    </div>
                @endif

<div class="nav-bar-new">
	<div class="search">
                  <div class="srh search-input">
                    <i class="fa fa-search"></i>
                    <input type="text" placeholder="indema spotlight">
                    <a href="" target="_blank" hidden></a>
                    <div style="display:none;" class="autocom-box search-dropdown">
                      <!-- here list are inserted from javascript -->
                    </div>
                  </div>
	</div>
	<ul>
		<li><a href="{{ route('admin.profile-settings.index') }}">My Account</a></li>
                <li><a target="_blank" href="https://indema.co/gethelp">Support</a></li>
                <li><a target="_blank" href="https://feedback.indema.co/">Feedback</a></li>
		<li><a href="{{ route('admin.billing') }}">Billing</a></li>
		<li><a href="{{ route('admin.settings.index') }}">Settings</a></li>
                <li><a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" >@lang('app.logout')</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </li>
                
                
	</ul>
    
        <div class="btn-group dropup notification-dropdown">
            <a class="dropdown-toggle show-user-notifications" data-toggle="dropdown" href="#">
                <i class="fa fa-bell"></i>
                @if($unreadNotificationCount > 0)

                    <div class="notify"><span class="heartbit"></span><span class="point"></span></div>
                @endif
            </a>
            <ul class="dropdown-menu mailbox ">
                <li>
                    <a href="javascript:;">...</a>
                </li>
            </ul>
        </div>
    
    
</div>
                @yield('page-title')

                        <!-- .row -->
                @yield('content')

                @include('sections.right_sidebar')

            </div>
        </div>
        <!-- /.container-fluid -->
    </div>
    <!-- /#page-wrapper -->
</div>
<!-- /#wrapper -->

{{--Footer sticky notes--}}
<div id="footer-sticky-notes" class="row hidden-xs hidden-sm">
    <div class="col-md-12" id="sticky-note-header">
        <div class="col-xs-10" style="line-height: 30px">
        @lang('app.menu.stickyNotes') <a href="javascript:;" onclick="showCreateNoteModal()" class="btn btn-success btn-outline btn-xs m-l-10"><i class="fa fa-plus"></i> @lang("modules.sticky.addNote")</a>
            </div>
        <div class="col-xs-2">
            <a href="javascript:;" class="btn btn-default btn-circle pull-right" id="open-sticky-bar"><i class="fa fa-chevron-up"></i></a>
            <a style="display: none;" class="btn btn-default btn-circle pull-right" href="javascript:;" id="close-sticky-bar"><i class="fa fa-chevron-down"></i></a>
        </div>

    </div>

    <div id="sticky-note-list" style="display: none">

        @foreach($stickyNotes as $note)
            <div class="col-md-12 sticky-note" id="stickyBox_{{$note->id}}">
            <div class="well
             @if($note->colour == 'red')
                bg-danger
             @endif
             @if($note->colour == 'green')
                bg-success
             @endif
             @if($note->colour == 'yellow')
                bg-warning
             @endif
             @if($note->colour == 'blue')
                bg-info
             @endif
             @if($note->colour == 'purple')
                bg-purple
             @endif
             b-none">
                <p>{!! nl2br($note->note_text)  !!}</p>
                <hr>
                <div class="row font-12">
                    <div class="col-xs-9">
                        @lang("modules.sticky.lastUpdated"): {{ $note->updated_at->diffForHumans() }}
                    </div>
                    <div class="col-xs-3">
                        <a href="javascript:;"  onclick="showEditNoteModal({{$note->id}})"><i class="ti-pencil-alt text-white"></i></a>
                        <a href="javascript:;" class="m-l-5" onclick="deleteSticky({{$note->id}})" ><i class="ti-close text-white"></i></a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach

    </div>
</div>

<a href="javascript:;" id="sticky-note-toggle"><i class="icon-note"></i></a>

{{--sticky note end--}}

{{--Timer Modal--}}
<div class="modal fade bs-modal-md in" id="projectTimerModal" role="dialog" aria-labelledby="myModalLabel"
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
{{--Timer Modal Ends--}}

{{--sticky note modal--}}
<div id="responsive-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            Loading ...
        </div>
    </div>
</div>
{{--sticky note modal ends--}}
{{--Timer Modal--}}
<div class="modal fade bs-modal-md in" id="projectTimerModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" id="modal-data-application">
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
{{--Timer Modal Ends--}}

{{--Ajax Modal--}}
<div class="modal fade bs-modal-md in"  id="subTaskModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" id="modal-data-application">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <span class="caption-subject font-red-sunglo bold uppercase" id="subTaskModelHeading">Sub Task e</span>
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

<!-- jQuery -->
<script src="{{ asset('plugins/bower_components/jquery/dist/jquery.min.js') }}"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

<!-- Bootstrap Core JavaScript -->
<script src="{{ asset('bootstrap/dist/js/bootstrap.min.js') }}"></script>
<script src='//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.2/js/bootstrap-select.min.js'></script>

<!-- Sidebar menu plugin JavaScript -->
<script src="{{ asset('plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.js') }}"></script>
<!--Slimscroll JavaScript For custom scroll-->
<script src="{{ asset('js/jquery.slimscroll.js') }}"></script>
<!--Wave Effects -->
<script src="{{ asset('js/waves.js') }}"></script>
<!-- Custom Theme JavaScript -->
<script src="{{ asset('plugins/bower_components/sweetalert/sweetalert.min.js') }}"></script>
<script src="{{ asset('js/custom.js') }}"></script>
<script src="{{ asset('js/jasny-bootstrap.js') }}"></script>
<script src="{{ asset('plugins/froiden-helper/helper.js') }}"></script>
<script src="{{ asset('plugins/bower_components/toast-master/js/jquery.toast.js') }}"></script>

{{--sticky note script--}}
<script src="{{ asset('js/cbpFWTabs.js') }}"></script>
<script src="{{ asset('plugins/bower_components/icheck/icheck.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/icheck/icheck.init.js') }}"></script>
<script src="{{ asset('js/jquery.magnific-popup.min.js') }}"></script>
<script src="{{ asset('js/jquery.magnific-popup-init.js') }}"></script>


<script>
    $('.notificationSlimScroll').slimScroll({
        height: '250',
        position: 'right',
        color: '#dcdcdc'
    });
    $('body').on('click', '.timer-logs-modal', function(){
        var url = '{{ route('admin.all-time-logs.show-active-timer')}}';
        $('#modelHeading').html('Active Timer');
        $.ajaxModal('#projectTimerModal',url);
    });

    $('body').on('click', '.active-timer-modal', function(){
        var url = '{{ route('admin.all-time-logs.show-active-timer')}}';
        $('#modelHeading').html('Active Timer');
        $.ajaxModal('#projectTimerModal',url);
    });
    
    
    $('.datepicker, #start-date, #end-date').on('click', function(e) {
        e.preventDefault();
        $(this).attr("autocomplete", "off");
    });

    function addOrEditStickyNote(id)
    {
        var url = '';
        var method = 'POST';
        if(id === undefined || id == "" || id == null) {
            url =  '{{ route('admin.sticky-note.store') }}'
        } else{

            url = "{{ route('admin.sticky-note.update',':id') }}";
            url = url.replace(':id', id);
            var stickyID = $('#stickyID').val();
            method = 'PUT'
        }

        var noteText = $('#notetext').val();
        var stickyColor = $('#stickyColor').val();
        $.easyAjax({
            url: url,
            container: '#responsive-modal',
            type: method,
            data:{'notetext':noteText,'stickyColor':stickyColor,'_token':'{{ csrf_token() }}'},
            success: function (response) {
                $("#responsive-modal").modal('hide');
                getNoteData();
            }
        })
    }

    // FOR SHOWING FEEDBACK DETAIL IN MODEL
    function showCreateNoteModal(){
        var url = '{{ route('admin.sticky-note.create') }}';

        $("#responsive-modal").removeData('bs.modal').modal({
            remote: url,
            show: true
        });

        $('#responsive-modal').on('hidden.bs.modal', function () {
            $(this).find('.modal-body').html('Loading...');
            $(this).data('bs.modal', null);
        });

        return false;
    }

    // FOR SHOWING FEEDBACK DETAIL IN MODEL
    function showEditNoteModal(id){
        var url = '{{ route('admin.sticky-note.edit',':id') }}';
        url  = url.replace(':id',id);

        $("#responsive-modal").removeData('bs.modal').modal({
            remote: url,
            show: true
        });

        $('#responsive-modal').on('hidden.bs.modal', function () {
            $(this).find('.modal-body').html('Loading...');
            $(this).data('bs.modal', null);
        });

        return false;
    }

    function selectColor(id){
        $('.icolors li.active ').removeClass('active');
        $('#'+id).addClass('active');
        $('#stickyColor').val(id);

    }


    function deleteSticky(id){

        swal({
            title: "Are you sure?",
            text: "You will not be able to recover the deleted Sticky Note!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel please!",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function(isConfirm){
            if (isConfirm) {

                var url = "{{ route('admin.sticky-note.destroy',':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: {'_token': token, '_method': 'DELETE'},
                    success: function (response) {
                        $('#stickyBox_'+id).hide('slow');
                        $("#responsive-modal").modal('hide');
                        getNoteData();
                    }
                });
            }
        });
    }


    //getting all chat data according to user
    function getNoteData(){

        var url = "{{ route('admin.sticky-note.index') }}";

        $.easyAjax({
            type: 'GET',
            url: url,
            messagePosition: '',
            data:  {},
            container: ".noteBox",
            error: function (response) {

                //set notes in box
                $('#sticky-note-list').html(response.responseText);
            }
        });
    }
</script>

<script>

$('body').on('click', '.timer-modal', function(){
        var url = '{{ route('member.time-log.create')}}';
        $('#modelHeading').html('Start Timer For a Project');
        $.ajaxModal('#projectTimerModal',url);
    });

    $('body').on('click', '.stop-timer-modal', function(){
        var url = '{{ route('member.time-log.show', ':id')}}';
        url = url.replace(':id', $(this).data('timer-id'));

        $('#modelHeading').html('Stop Timer');
        $.ajaxModal('#projectTimerModal',url);
    });


</script>

@if(isset($timer) && !is_null($timer))
    <script>
        $(document).ready(function(e) {
            var $worked = $("#active-timer");
            function updateTimer() {
                var myTime = $worked.html();
                var ss = myTime.split(":");
//            console.log(ss);

                var hours = ss[0];
                var mins = ss[1];
                var secs = ss[2];
                secs = parseInt(secs)+1;

                if(secs > 59){
                    secs = '00';
                    mins = parseInt(mins)+1;
                }

                if(mins > 59){
                    secs = '00';
                    mins = '00';
                    hours = parseInt(hours)+1;
                }

                if(hours.toString().length < 2) {
                    hours = '0'+hours;
                }
                if(mins.toString().length < 2) {
                    mins = '0'+mins;
                }
                if(secs.toString().length < 2) {
                    secs = '0'+secs;
                }
                var ts = hours+':'+mins+':'+secs;

//            var dt = new Date();
//            dt.setHours(ss[0]);
//            dt.setMinutes(ss[1]);
//            dt.setSeconds(ss[2]);
//            var dt2 = new Date(dt.valueOf() + 1000);
//            var ts = dt2.toTimeString().split(" ")[0];
                $worked.html(ts);
                setTimeout(updateTimer, 1000);
            }
            setTimeout(updateTimer, 1000);
        });

    </script>
@endif


<script>
    $('.show-user-notifications').click(function () {
        var token = '{{ csrf_token() }}';
        $.easyAjax({
            type: 'POST',
            url: '{{ route("show-admin-notifications") }}',
            data: {'_token': token},
            success: function (data) {
                if (data.status == 'success') {
                    $('.mailbox').html(data.html);
                }
            }
        });

    });

    $('.mailbox').on('click', '.mark-notification-read', function () {
        var token = '{{ csrf_token() }}';
        $.easyAjax({
            type: 'POST',
            url: '{{ route("mark-notification-read") }}',
            data: {'_token': token},
            success: function (data) {
                if (data.status == 'success') {
                    $('.top-notifications').remove();
                    $('.top-notification-count').html('0');
                    $('#top-notification-dropdown .notify').remove();
                    $('.notify').remove();
                }
            }
        });

    });

    $('.mailbox').on('click', '.show-all-notifications', function () {
        var url = '{{ route('show-all-member-notifications')}}';
        $('#modelHeading').html('View Unread Notifications');
        $.ajaxModal('#projectTimerModal', url);
    });

    $('.submit-search').click(function () {
        $(this).parent().submit();
    });

    $(function () {
        $('.selectpicker').selectpicker();
    });

    $('.language-switcher').change(function () {
        var lang = $(this).val();
        $.easyAjax({
            url: '{{ route("admin.settings.change-language") }}',
            data: {'lang': lang},
            success: function (data) {
                if (data.status == 'success') {
                    window.location.reload();
                }
            }
        });
    });

//    sticky notes script
    var stickyNoteOpen = $('#open-sticky-bar');
    var stickyNoteClose = $('#close-sticky-bar');
    var stickyNotes = $('#footer-sticky-notes');
    var viewportHeight = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);
    var stickyNoteHeaderHeight = stickyNotes.height();

    $('#sticky-note-list').css('max-height', viewportHeight-150);

    stickyNoteOpen.click(function () {
        $('#sticky-note-list').toggle(function () {
            $(this).animate({
                height: (viewportHeight-150)
            })
        });
        stickyNoteClose.toggle();
        stickyNoteOpen.toggle();
    })

    stickyNoteClose.click(function () {
        $('#sticky-note-list').toggle(function () {
            $(this).animate({
                height: 0
            })
        });
        stickyNoteOpen.toggle();
        stickyNoteClose.toggle();
    })



    $('body').on('click', '.right-side-toggle', function () {
        $(".right-sidebar").slideDown(50).removeClass("shw-rside");
    })


    function updateOnesignalPlayerId(userId) {
        $.easyAjax({
            url: '{{ route("member.profile.updateOneSignalId") }}',
            type: 'POST',
            data:{'userId':userId, '_token':'{{ csrf_token() }}'},
            success: function (response) {
            }
        })
    }

    $('.table-responsive').on('show.bs.dropdown', function () {
        $('.table-responsive').css( "overflow", "inherit" );
    });

    $('.table-responsive').on('hide.bs.dropdown', function () {
        $('.table-responsive').css( "overflow", "auto" );
    })

    $('#mobile-filter-toggle').click(function () {
        $('.filter-section').toggle();
    })

    $('#sticky-note-toggle').click(function () {
        $('#footer-sticky-notes').toggle();
        $('#sticky-note-toggle').hide();
    })

    $(document).ready(function () {
        //Side menu active hack
        setTimeout(function(){
            var getActiveMenu = $('#side-menu  li.active li a.active').length;
        // console.log(getActiveMenu);
            if(getActiveMenu > 0) {
                $('#side-menu  li.active li a.active').parent().parent().parent().find('a:first').addClass('active');
            }

         }, 200);
if ($('html[data-theme="dark"]').length) {
       $('#checkbox').prop('checked', true);
    }
    else {
        //alert('The name attribute does not exist');
    }
    })

    $('body').on('click', '.toggle-password', function() {
        var $selector = $(this).parent().find('input.form-control');
        $(this).toggleClass("fa-eye fa-eye-slash");
        var $type = $selector.attr("type") === "password" ? "text" : "password";
        $selector.attr("type", $type);
    });

    var currentUrl = '{{ request()->route()->getName() }}';
    $('body').on('click', '.filter-section-close', function() {
        localStorage.setItem('filter-'+currentUrl, 'hide');

        $('.filter-section').toggle();
        $('.filter-section-show').toggle();
        $('.data-section').toggleClass("col-md-9 col-md-12")
    });

    $('body').on('click', '.filter-section-show', function() {
        localStorage.setItem('filter-'+currentUrl, 'show');

        $('.filter-section-show').toggle();
        $('.data-section').toggleClass("col-md-9 col-md-12")
        $('.filter-section').toggle();
    });

    var currentUrl = '{{ request()->route()->getName() }}';
    var checkCurrentUrl = localStorage.getItem('filter-'+currentUrl);
    
    //console.log('currentUrl : '+currentUrl);
    var allowedURL = ["admin.client-invoice.index", "Volvo", "BMW"];
    
    // added by SB show filter acconding to settings
    var show_filters_js =  '<?php echo $global->show_filters; ?>';
    if(currentUrl == 'admin.projects.free-flow-gantt' || currentUrl == 'admin.client-invoice.index' || currentUrl == 'admin.products.index' || currentUrl == 'admin.clients.index' || currentUrl == 'admin.leads.index' || currentUrl == 'admin.vendor.index' || currentUrl == 'admin.employees.index' || currentUrl == 'admin.contracts.index' 
            || currentUrl == 'admin.projects.index' || currentUrl == 'admin.all-tasks.index' || currentUrl == 'admin.all-time-logs.index' || currentUrl == 'admin.estimates.index' || currentUrl == 'admin.payments.index' || currentUrl == 'admin.expenses.index' ||  currentUrl == 'admin.all-credit-notes.index' 
            || currentUrl == 'admin.task-report.index' || currentUrl == 'admin.time-log-report.index' || currentUrl == 'admin.finance-report.index' || currentUrl == 'admin.income-expense-report.index' || currentUrl == 'admin.leave-report.index' || currentUrl == 'admin.attendance-report.index' 
            || currentUrl == 'admin.reports.index' || currentUrl == 'admin.products-project.show') {
        if(show_filters_js == '0') {
                 $('.filter-section-show').show();
                 $('.data-section').removeClass("col-md-9")
                 $('.data-section').addClass("col-md-12")
                 $('.filter-section').hide();
         } else if (show_filters_js == '1') {
                 $('.filter-section-show').hide();
                 $('.data-section').removeClass("col-md-12")
                 $('.data-section').addClass("col-md-9")
                 $('.filter-section').show();
         }
    }
    // added by SB end
    
    
    // SB commented 
//    if (checkCurrentUrl == "hide") {
//        $('.filter-section-show').show();
//        $('.data-section').removeClass("col-md-9")
//        $('.data-section').addClass("col-md-12")
//        $('.filter-section').hide();
//    } else if (checkCurrentUrl == "show") {
//        $('.filter-section-show').hide();
//        $('.data-section').removeClass("col-md-12")
//        $('.data-section').addClass("col-md-9")
//        $('.filter-section').show();
//    }
    // SB commented end
    
</script>



@stack('footer-script')
<script>
    var checkDatatable = $.fn.DataTable;
    if(checkDatatable != undefined){
        checkDatatable.ext.errMode = function (settings, tn, msg) {
            console.log(settings, tn, msg);
            if (settings && settings.jqXHR && settings.jqXHR.status == 401) {
                // Handling for 401 specifically
                window.location.reload();
            }
            else{
                alert(msg);
            }
        };
    }

</script>
<script>

$(document).ready(function(){
  $(".search-input input").focus(function(){
    $(".search-dropdown").css("display", "block");
  });
  $(".search-input input").focusout(function(){
    setTimeout(function () {
        $(".search-dropdown").css("display", "none");
        $(".search-dropdown").html('');
    },1000);

  });
});

//label-info
//label-default
//label-success

var suggestions = [
    {'heading' : 'Dashboard', cls:'label-info', label:'Dashboard', value:'dashboard', url : "{{ route('admin.dashboard') }}"},
    {'heading' : 'Contacts', cls:'label-success', label:'Client List', value:'Client List', url : "{{ route('admin.clients.index') }}"},
    {'heading' : 'Contacts', cls:'label-success', label:'Add Client', value:'Add Client', url : "{{ route('admin.clients.create') }}"},
    {'heading' : 'Contacts', cls:'label-success', label:'Lead List', value:'Lead List', url : "{{ route('admin.leads.index') }}"},
    {'heading' : 'Contacts', cls:'label-success', label:'Add Lead', value:'Add Lead', url : "{{ route('admin.leads.create') }}"},
    {'heading' : 'Contacts', cls:'label-success', label:'Vendor List', value:'Vendor List', url : "{{ route('admin.vendor.index') }}"},
    {'heading' : 'Contacts', cls:'label-success', label:'Add Vendor', value:'Add Vendor', url : "{{ route('admin.vendor.create') }}"},
    
    {'heading' : 'Projects', cls:'label-info', label:'Project List', value:'Project List', url : "{{ route('admin.projects.index') }}"},
    {'heading' : 'Projects', cls:'label-info', label:'Add Project', value:'Add Project', url : "{{ route('admin.projects.create') }}"},
    {'heading' : 'Contracts', cls:'label-default', label:'Contract List', value:'Contract List', url : "{{ route('admin.contracts.index') }}"},
    {'heading' : 'Contracts', cls:'label-default', label:'Add Contract', value:'Contract List', url : "{{ route('admin.contracts.create') }}"},
    
    {'heading' : 'Team', cls:'label-success', label:'Team', value:'Team', url : "{{ route('admin.employees.index') }}"},
    {'heading' : 'Team', cls:'label-success', label:'Vendor Team', value:'Vendor Team', url : "{{ route('admin.employees.create') }}"},
    {'heading' : 'Team', cls:'label-success', label:'Attendance', value:'Attendance', url : "{{ route('admin.attendances.summary') }}"},
    {'heading' : 'Team', cls:'label-success', label:'Holiday List', value:'Holiday List', url : "{{ route('admin.holidays.index') }}"},
    {'heading' : 'Team', cls:'label-success', label:'Leaves', value:'Leaves', url : "{{ route('admin.leaves.pending') }}"},
    
    {'heading' : 'Business', cls:'label-success', label:'Automation', value:'Automation', url : "{{ route('admin.email-automation.index') }}"},
    {'heading' : 'Business', cls:'label-success', label:'Add Automation', value:'Add ExpensesAutomation', url : "{{ route('admin.email-automation.create') }}"},
    {'heading' : 'Business', cls:'label-success', label:'Expenses List', value:'Expenses List', url : "{{ route('admin.expenses.index') }}"},
    {'heading' : 'Business', cls:'label-success', label:'Add Expense', value:'Add Expenset', url : "{{ route('admin.expenses.create') }}"},
    {'heading' : 'Business', cls:'label-success', label:'Schedules', value:'Schedules', url : "{{ route('admin.events.index') }}"},
    {'heading' : 'Business', cls:'label-success', label:'Add Schedules', value:'Add Schedules', url : "{{ route('admin.events.index') }}"},
    {'heading' : 'Business', cls:'label-success', label:'Zoom Meeting List', value:'Zoom Meeting List', url : "{{ route('admin.zoom-meeting.table-view') }}"},
    {'heading' : 'Business', cls:'label-success', label:'Add Zoom Meeting', value:'Add Zoom Meeting', url : "{{ route('admin.zoom-meeting.table-view') }}"},
    {'heading' : 'Business', cls:'label-success', label:'Visionboard', value:'Visionboard', url : "{{ route('admin.visionboard.index') }}"},
    {'heading' : 'Business', cls:'label-success', label:'File Manager', value:'File Manager', url : "{{ route('admin.view-file-manager') }}"},
    {'heading' : 'Business', cls:'label-success', label:'Freeflow Gantt', value:'Freeflow Gantt', url : "{{ route('admin.projects.free-flow-gantt') }}"},
    
    {'heading' : 'Reports', cls:'label-info', label:'Task Report', value:'Task Report', url : "{{ route('admin.task-report.index') }}"},
    {'heading' : 'Reports', cls:'label-info', label:'Time Log Report', value:'Time Log Report', url : "{{ route('admin.time-log-report.index') }}"},
    {'heading' : 'Reports', cls:'label-info', label:'Finance Report', value:'Finance Report', url : "{{ route('admin.finance-report.index') }}"},
    {'heading' : 'Reports', cls:'label-info', label:'Income Vs Expense Report', value:'Income Vs Expense Report', url : "{{ route('admin.income-expense-report.index') }}"},
    {'heading' : 'Reports', cls:'label-info', label:'Leave Report', value:'Leave Report', url : "{{ route('admin.leave-report.index') }}"},
    {'heading' : 'Reports', cls:'label-info', label:'Attendance Report', value:'Attendance Report', url : "{{ route('admin.attendance-report.index') }}"},
    {'heading' : 'Reports', cls:'label-info', label:'Project Status Report', value:'Project Status Report', url : "{{ route('admin.projectstatus.index') }}"},
    {'heading' : 'Products', cls:'label-info', label:'Product Listing', value:'Product Listing', url : "{{ route('admin.products.index') }}"},
    {'heading' : 'Products', cls:'label-info', label:'Product Add', value:'Product Add', url : "{{ route('admin.products.create') }}"},
    {'heading' : 'Messages', cls:'label-info', label:'Messages', value:'Messages', url : "{{ route('admin.user-chat.index') }}"},
];


// getting all required elements
const searchWrapper = document.querySelector(".search-input");
const inputBox = searchWrapper.querySelector("input");
const suggBox = searchWrapper.querySelector(".autocom-box");
// if user press any key and release
inputBox.onkeyup = (e)=>{
    let userData = e.target.value; //user enetered data
    let emptyArray = [];
    if(userData){
        emptyArray = suggestions.filter((data)=>{
            //filtering array value and user characters to lowercase and return only those words which are start with user enetered chars
            return data.label.toLocaleLowerCase().startsWith(userData.toLocaleLowerCase());
        });
        emptyArray = emptyArray.map((data)=>{
            var row = '<div class="list-row">';
            row += '<div><span class="label '+data.cls+'">'+data.heading+'</span> '+data.label+'</div>';
            row += '<a href="'+data.url+'">Select <img src="{{ asset('img/select-icon.png') }}" alt="" /></a>';
            row += '</div>';
            // passing return data inside li tag
            return data = `${row}`;
        });
        showSuggestions(emptyArray);
    }else{
        searchWrapper.classList.remove("active"); //hide autocomplete box
    }
}

function showSuggestions(list){
    let listData;
    if(!list.length){
            var row = '<div class="list-row">';
            row += '<div class="link-text">No result(s) found</div>';
            row += '<a href="javascript:void(0)">Select <img src="{{ asset('img/select-icon.png') }}" alt="" /></a>';
            row += '</div>';
           listData = `${row}`;
    }else{
      listData = list.join('');
    }
    suggBox.innerHTML = listData;
}

</script>
</body>
</html>
