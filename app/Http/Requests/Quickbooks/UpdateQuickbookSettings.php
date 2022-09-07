<?php

namespace App\Http\Requests\Quickbooks;

use App\Http\Requests\CoreRequest;
use Illuminate\Foundation\Http\FormRequest;

class UpdateQuickBookSettings extends CoreRequest
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
            "client_id" => "required",
            "client_secret" => "required",
            "redirect_url" => "nullable|url",
        ];
    }

    public function messages()
    {
        return [
            'logo.uploaded' => trans('messages.fileSize'),
        ];
    }
}
