<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\CoreRequest;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductNote extends CoreRequest
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
            'note' => [
                'required',
                function ($attribute, $value, $fail) {
                    $commnet = trim(str_replace('<p><br></p>', '', $value));

                    if ($commnet == '') {
                        $fail($attribute . ' is required');
                    }
                }
            ]
        ];
    }
}
