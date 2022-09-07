<?php

namespace Modules\Zoom\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Zoom\Http\Requests\ZoomMeeting\UpdateSetting;
use App\Helper\Reply;
use Modules\Zoom\Entities\ZoomSetting;
use App\Http\Controllers\Admin\AdminBaseController;

class ZoomMeetingSettingController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('zoom::app.menu.zoomSetting');;
        $this->pageIcon = 'fa fa-video-camera';

        $this->middleware(function ($request, $next) {
            ZoomSetting::setZoom();
            if (!in_array('Zoom', $this->user->modules)) {
                abort(403);
            }
            return $next($request);
        });
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $this->zoom = ZoomSetting::first();

        return view('zoom::index', $this->data);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(UpdateSetting $request, $id)
    {
        $setting = ZoomSetting::findOrFail($id);

        $setting->update($request->all());

        return Reply::success(__('messages.updatedSuccessfully'));
    }
}
