<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmailConnectionSettingRequest extends FormRequest
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
         'driver' => 'required',
         'host' => 'required',
         'port' => 'required',
         'encryption' => 'required',
         'user_name' => 'required',
         'password' => 'required',
         'sender_name' => 'required',
         'sender_email' => 'required|email',
        ];
    }

    public function messages(): array
    {
        return [
            'driver.required' => 'Mail driver field is required',
            'host.required' => 'Mail host field is required',
            'port.required' => 'Mail port field is required',
            'encryption.required' => 'Mail encryption field is required',
            'user_name.required' => 'Mail Username field is required',
            'password.required' => 'Mail password field is required',
            'sender_name.required' => 'Mail from name field is required',
            'sender_email.required' => 'Mail from address field is required',
        ];
    }
}
