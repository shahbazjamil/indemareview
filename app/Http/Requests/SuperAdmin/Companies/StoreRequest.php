<?php

namespace App\Http\Requests\SuperAdmin\Companies;

use App\Http\Requests\SuperAdmin\SuperAdminBaseRequest;

class StoreRequest extends SuperAdminBaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "company_name" => "required",
            "company_email" => "required|email|unique:companies",
            'sub_domain' => module_enabled('Subdomain') ?'required|min:4|unique:companies,sub_domain|max:50|sub_domain':'',
            "company_phone" => "required",
            "address" => "required",
            "status" => "required",
            'email' => 'required|unique:users',
            'password' => 'required|min:6'
        ];
    }

    public function prepareForValidation()
    {
        if (empty($this->sub_domain)) {
            return;
        }

        // Add servername domain suffix at the end
        $subdomain = trim($this->sub_domain, '.') . '.' . get_domain();
        $this->merge(['sub_domain' => $subdomain]);
        request()->merge(['sub_domain' => $subdomain]);
    }
}
