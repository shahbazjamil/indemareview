<?php namespace Modules\RestAPI\Http\Requests\Lead;

use Froiden\RestAPI\ApiResponse;
use Modules\RestAPI\Http\Requests\BaseRequest;

class IndexRequest extends BaseRequest
{

    /**
     * @return bool
     * @throws \Froiden\RestAPI\Exceptions\UnauthorizedException
     */
    public function authorize()
    {
        $user = api_user();
        return in_array('leads', $user->modules)
            && ($user->hasRole('admin') || $user->cans('view_lead') || $user->user_other_role ==='employee');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [

        ];
    }
}
