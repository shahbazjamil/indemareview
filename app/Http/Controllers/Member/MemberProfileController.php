<?php

namespace App\Http\Controllers\Member;

use App\EmployeeDetails;
use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Requests\User\UpdateProfile;
use App\User;
use App\ClientDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\DB;

class MemberProfileController extends MemberBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.profileSettings';
        $this->pageIcon = 'icon-user';
    }

    public function index()
    {
        $this->mixPanelTrackEvent('view_page', array('page_path' => '/member/profile'));
        $this->userDetail = auth()->user();
        $this->employeeDetail = EmployeeDetails::where('user_id', '=', $this->userDetail->id)->first();
        return view('member.profile.edit', $this->data);
    }

    public function update(UpdateProfile $request, $id)
    {
        config(['filesystems.default' => 'user-uploads']);
        
        $company = company();
        $user = User::withoutGlobalScope('active')->findOrFail($id);
        
        $is_main = 0;
        if($user->email == $company->company_email) {
            $is_main = 1;
        }
        
        
        
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->gender = $request->input('gender');
        if ($request->password != '') {
            $user->password = Hash::make($request->input('password'));
        }
        $user->mobile = $request->input('mobile');
        $user->email_notifications = $request->email_notifications;

        
        if ($request->hasFile('image')) {
            Files::deleteFile($user->image, 'avatar');
            $user->image = Files::upload($request->image, 'avatar', 300);
        }
        
        // added by PM
//        if ($request->hasFile('image')) {
//            $directory = 'user-uploads/avatar';
//            $fileName = time().".jpg";
//            $imageFilePath = "$directory/$fileName";
//            File::move($request->image, public_path($imageFilePath));
//            $user->image = $fileName;
//        }

        $user->save();

        $validate = Validator::make(['address' => $request->address], [
            'address' => 'required'
        ]);

        if ($validate->fails()) {
            return Reply::formErrors($validate);
        }

        $employee = EmployeeDetails::where('user_id', '=', $user->id)->first();
        if (empty($employee)) {
            $employee = new EmployeeDetails();
            $employee->user_id = $user->id;
        }
        $employee->address = $request->address;
        $employee->save();
        
        
        $client = ClientDetails::where('user_id', '=', $user->id)->first();
        if($client) {
            $client->name = $request->input('name');
            $client->save();
        }
        
        //echo $is_main;exit;
        if($is_main == 1) {
            DB::table('companies')->where('id', $company->id)->update(array('company_email' => $request->input('email')));
            session()->forget('company_setting');
            session()->forget('company');
        }

        session()->forget('user');
        $this->logUserActivity($user->id, __('messages.updatedProfile'));
        return Reply::redirect(route('member.profile.index'), __('messages.profileUpdated'));
    }

    public function updateOneSignalId(Request $request)
    {
        $user = User::find($this->user->id);
        $user->onesignal_player_id = $request->userId;
        $user->save();
    }

    public function changeLanguage(Request $request) {
        $setting = User::findOrFail($this->user->id);
        $setting->locale = $request->input('lang');
        $setting->save();
        session()->forget('user');
        return Reply::success('Language changed successfully.');
    }
}
