<?php namespace Modules\RestAPI\Http\Requests\Client;

use App\Scopes\CompanyScope;
use Modules\RestAPI\Entities\User;
use Modules\RestAPI\Http\Requests\BaseRequest;

class UpdateRequest extends BaseRequest
{

    /**
     * @return bool
     * @throws \Froiden\RestAPI\Exceptions\UnauthorizedException
     */
    public function authorize()
    {
        $user = api_user();
        $client = User::withoutGlobalScopes([CompanyScope::class, 'active'])
            ->where('id', $this->route('client'))
            ->first();
        $client = $client->client_detail()->exists();
        return in_array('clients', $user->modules)
            &&
            $client
            &&
            ($user->hasRole('admin') || $user->cans('edit_clients'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'sometimes|required',
            'email' => 'sometimes|required|email',
            'company_name' => 'sometimes|required|email',
        ];
    }

    public function messages()
    {
        return [
            //
        ];
    }
}
