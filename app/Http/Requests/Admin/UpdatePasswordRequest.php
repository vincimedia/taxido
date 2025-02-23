<?php

namespace App\Http\Requests\Admin;

use App\Rules\MatchCurrentPassword;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
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
        return [
            'current_password' => ['required','string', new MatchCurrentPassword],
            'new_password' => ['required','string','min:8'],
            'confirm_password' => ['required','min:8','same:new_password'],
        ];
    }
}
