<?php

namespace App\Http\Requests\Admin\Vendor;

use Froiden\LaravelInstaller\Request\CoreRequest;

class StoreClientVendorRequest extends CoreRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => [
                'required',
//                Rule::unique('client_vendor_details')->where(function($query) {
//                    $query->where(['email' => $this->request->get('email'), 'company_id' => company()->id]);
//                })->ignore($this->route('client'), 'id')
            ],
            'name'  => 'required',
            'website' => 'nullable|url',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'Email is required!',
            'name.required' => 'Name is required!',

        ];
    }
}
