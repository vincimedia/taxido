<?php

namespace Modules\Taxido\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateDriverRequest extends FormRequest
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
            'profile_image_id' => ['required','exists:media,id,deleted_at,NULL'],
            'username' => ['required', 'string', 'unique:users,username,NULL,id,deleted_at,NULL'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,NULL,id,deleted_at,NULL'],
            'phone' => ['required', 'digits_between:6,15','unique:users,phone,NULL,id,deleted_at,NULL'],
            'status' => ['required'],
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

    protected function prepareForValidation()
    {
        $this->merge([
            'notify' => (int) $this->notify,
            'status' => (int) $this->status,
            'phone' => (string) $this->phone,
        ]);
    }

    public function attributes()
    {
        return [
            'address.address' => 'address',
            'address.country_id' => 'country',
            'address.state_id' => 'state',
            'address.city' => 'city',
            'address.postal_code' => 'postal code',
            // 'vehicle_info.vehicle_type_id' => 'vehicle type',
            'vehicle_info.model' => 'model',
            'vehicle_info.plate_number' => 'plate number',
            'vehicle_info.seat' => 'city',
            'vehicle_info.color' => 'postal code',
            'payment_account.bank_account_no' => 'bank account no',
            'payment_account.bank_name' => 'bank name',
            'payment_account.bank_holder_name' => 'bank holder name',
            'payment_account.swift' => 'swift',
            'payment_account.ifsc' => 'ifsc',
        ];
    }
}
