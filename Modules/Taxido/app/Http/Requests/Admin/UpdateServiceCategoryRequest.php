<?php

namespace Modules\Taxido\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceCategoryRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        $id = $this->route('service_category') ? $this->route('service_category')?->id : $this?->id;
        return [
            'name' => ['string', 'max:255', 'unique:service_categories,name,' . $id . ',id,deleted_at,NULL'],
            'service_category_image_id' => ['required', 'exists:media,id,deleted_at,NULL'],
        ];
    }
}
