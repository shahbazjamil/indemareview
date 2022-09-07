<?php namespace Modules\RestAPI\Http\Requests\Lead;

use Modules\RestAPI\Http\Requests\BaseRequest;

class CreateRequest extends BaseRequest
{

    /**
     * @return bool
     * @throws \Froiden\RestAPI\Exceptions\UnauthorizedException
     */
    public function authorize()
    {
        $user = api_user();
        return in_array('leads', $user->modules) && ($user->hasRole('admin') || $user->cans('add_lead'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            'client_name' => 'required',
            'client_email' => 'required|email|unique:leads|unique:users,email',
            'website' => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            //
        ];
    }
}
