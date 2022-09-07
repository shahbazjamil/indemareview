<?php

namespace App\Http\Requests\DiscussionCategory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
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
            //'name' => 'required|unique:discussion_categories',
            "name" => [
                'required',
                Rule::unique('discussion_categories')->where(function($query) {
                    $query->where('company_id', company()->id);
                })
            ],
            'color' => 'required'
        ];
    }
}
