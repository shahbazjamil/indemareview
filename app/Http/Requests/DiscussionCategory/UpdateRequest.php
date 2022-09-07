<?php

namespace App\Http\Requests\DiscussionCategory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\DiscussionCategory;

class UpdateRequest extends FormRequest
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
        $detailID = DiscussionCategory::where('id', $this->route('discussion_category'))->first();
        return [
            //'name' => 'required|unique:discussion_categories,name,'.$this->route('discussion_category'),
             "name" => [
                'required',
                Rule::unique('discussion_categories')->where(function($query) use($detailID) {
                    $query->where('company_id', company()->id);
                    $query->where('id', '<>' ,$detailID->id);
                })
            ],
            'color' => 'required'
        ];
    }
}
