<?php

namespace App\Http\Requests\Api\v1;


class CreateSalesmanCustomerRequest extends SalesmanRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
           // 'account' => 'string|exists:user,user_name',
            'contact' => 'required',
            'contact_information' => 'required',
            'business_area' => 'required',
            'display_type' => 'required',
            'display_start_month' => 'sometimes|required_with:display_end_month',
            'display_end_month' => 'sometimes|required_with:display_start_month',
            'display_fee' => 'sometimes|required|numeric|min:0',
            'salesman_id' => 'sometimes|required|integer'
        ];
    }
    /**
     * 自定义验证
     *
     * @param \Illuminate\Contracts\Validation\Factory $factory
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator($factory)
    {
        return $this->defaultValidator($factory)->after(function ($validator) {
            if ($businessAddress= $this->input('business_address')) {
                if (!$businessAddress['address']) {
                    $validator->errors()->add('business_address[address]', '详细地址 不能为空');
                }
                if (!$businessAddress['city_id']) {
                    $validator->errors()->add('business_address[city_id]', '市 不能为空');
                }
            }
            if ($shippingAddress= $this->input('shipping_address')) {
                if (!$shippingAddress['address']) {
                    $validator->errors()->add('shipping_address[address]', '详细地址 不能为空');
                }
                if (!$shippingAddress['city_id']) {
                    $validator->errors()->add('shipping_address[city_id]', '市 不能为空');
                }
            }
        });
    }
}
