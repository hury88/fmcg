<?php

namespace App\Http\Requests\Admin;


class UpdatePasswordRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'old_password'=>'required|min:8',
            'new_password'=>'required|min:8|confirmed'
        ];
    }
}
