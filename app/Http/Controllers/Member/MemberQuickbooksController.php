<?php

namespace App\Http\Controllers\Member;

use App\EmployeeDetails;
use App\Helper\Files;
use App\Helper\Reply;
//use App\Http\Requests\Quickbooks\UpdateQuickBookSettings;
use App\User;
use App\QuickbooksSettings;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class MemberQuickbooksController extends MemberBaseController
{
      public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.quickbooksSettings';
        $this->pageIcon = 'icon-settings';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validate = Validator::make(['client_secret' => $request->client_secret], [
            'client_secret' => 'required'
        ]);

        if ($validate->fails()) {
            return Reply::formErrors($validate);
        }
        $user =  new QuickbooksSettings();
        $user->client_id = $request->input('client_id');
        $user->client_secret = $request->input('client_secret');
        $user->redirect_url = $request->input('redirect_url');
        $user->save();



        return Reply::redirect(route('admin.quickbooks-settings.index'), __('messages.quickbooksCreated'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {   

        $user = QuickbooksSettings::withoutGlobalScope('active')->findOrFail($id);
        $user->client_id = $request->input('client_id');
        $user->client_secret = $request->input('client_secret');
        $user->redirect_url = $request->input('redirect_url');

        $user->save();

        $validate = Validator::make(['client_secret' => $request->client_secret], [
            'client_secret' => 'required'
        ]);

        if ($validate->fails()) {
            return Reply::formErrors($validate);
        }

        return Reply::redirect(route('admin.quickbooks-settings.index'), __('messages.quickbooksUpdated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
