<?php

namespace App\Http\Requests\System;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * 权限已经在中间件中校验
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ];
        $id = (int)($this->route('id') ?? 0);
        if ($id) {
            $rules['name'] = 'sometimes|required|string|max:255';
            $rules['email'] = 'sometimes|required|string|email|max:255|unique:users,email,' . $id;
            $rules['password'] = 'sometimes|nullable|string|min:6';
        }
        return $rules;
    }

    /**
     * 获取定义的验证规则的错误消息。
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => '名称不能为空',
            'email.required' => '邮箱不能为空',
            'password.required' => '密码不能为空',
            'password.min' => '密码至少需要 :min 个字符',
            'email.unique' => '邮箱不能重复'
        ];
    }
}
