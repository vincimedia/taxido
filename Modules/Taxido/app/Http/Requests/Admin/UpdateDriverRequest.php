<?php

namespace Modules\Taxido\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDriverRequest extends FormRequest
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

        $id = $this->route('driver') ? $this->route('driver')->id : $this->id;
        return [
            'username' => ['required', 'string','unique:users,username,'.$id.',id,deleted_at,NULL'],
            'name' => ['required','max:255'],
            'email' => ['required','email', 'unique:users,email,'.$id.',id,deleted_at,NULL'],
            'phone' => ['required','numeric'],
            'profile_image_id' => ['nullable','exists:media,id,deleted_at,NULL'],
            'address.address' => ['required'],
            'address.country_id' => ['required','exists:countries,id'],
            'address.state_id' => ['required','exists:states,id'],
            'address.city' => ['required'],
            'address.postal_code' => ['required'],
            'vehicle_info.vehicle_type_id' => ['required','exists:vehicle_types,id,deleted_at,NULL'],
            'vehicle_info.model' => ['required'],
            'vehicle_info.plate_number' => ['required'],
            'vehicle_info.seat' => ['required'],
            'vehicle_info.color' => ['required'],
            'payment_account.bank_account_no' => ['required'],
            'payment_account.bank_name' => ['required'],
            'payment_account.bank_holder_name' => ['required'],
            'payment_account.swift' => ['required'],
            'payment_account.ifsc' => ['required'],
            'zones' => ['required','exists:zones,id,deleted_at,NULL'],
        ];
    }
}
