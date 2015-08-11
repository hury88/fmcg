<?php

namespace App\Http\Requests\Admin;


class CreateAdminRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_name'=>'required|max:20|unique:admin',
            'real_name'=>'required|max:32|min:2',
            'password'=>'required|min:8|confirmed',
            'role_id'=>'required|exists:role,id'
        ];
    }
}