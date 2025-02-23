<?php

namespace Modules\Taxido\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateVehicleTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'unique:vehicle_types,name,NULL,id,deleted_at,NULL'],
            'vehicle_image_id' => ['required','exists:media,id,deleted_at,NULL'],
            'cancellation_charge' => ['required','min:1'],
            'waiting_time_charge' => ['required','min:1'],
            'tax_id' => ['required'],
            'base_amount' => ['required'],
           // 'zones' => ['required','exists:zones,id,deleted_at,NULL'],
            'services' => ['required','exists:services,id,deleted_at,NULL'],
            'serviceCategories' => ['required','exists:service_categories,id,deleted_at,NULL'],
            'status' => ['required','min:0','max:1'],
        ];
    }
}
