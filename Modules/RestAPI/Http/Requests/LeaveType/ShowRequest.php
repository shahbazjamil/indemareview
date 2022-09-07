<?php

namespace Modules\RestAPI\Http\Requests\LeaveType;

use Modules\RestAPI\Entities\LeaveType;
use Modules\RestAPI\Http\Requests\BaseRequest;

class ShowRequest extends BaseRequest
{

    public function authorize()
    {
        $user = api_user();
        // Either user has role admin or has permission view_leave
        // Plus he needs to have leaves module enabled from settings
        $leave = LeaveType::find($this->route('leave_type'));
        return in_array('leaves', $user->modules) && $leave && $leave->visibleTo($user);
    }

    public function rules()
    {
        return [
            //
        ];
    }
}
