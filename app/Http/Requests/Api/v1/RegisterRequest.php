<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/10/19
 * Time: 15:02
 */
namespace App\Http\Requests\Api\v1;

class RegisterRequest extends Request
{

    public function rules()
    {
        return [
            'user_name' => 'required|alpha_num|between:4,16|unique:user',
            'password' => 'required|min:6|confirmed',
            'type' => 'required|in:1,2,3',
            'logo' => 'sometimes|required',
            'name' => 'required|unique:shop',
            'contact_person' => 'required',
            'contact_info' => 'required',
            'spreading_code'=> 'alpha_num|max:20',
            'address' => 'required|max:60',
            'area' => 'sometimes|required|max:200',
            'license' => 'sometimes|required',
            'license_num' => 'required|numeric',
            'business_license' => 'sometimes|required',
            'agency_contract' => 'sometimes|required'
        ];
    }
}