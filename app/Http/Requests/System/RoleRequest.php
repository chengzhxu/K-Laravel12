<?php

namespace App\Http\Requests\System;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255|unique:roles',
            'role_name' => 'required|string|max:255',
            'guard_name' => 'sometimes|string|max:255',
            'status' => 'sometimes|integer|in:0,1',
            'sort' => 'sometimes|integer',
            'remark' => 'sometimes|nullable|string|max:255',
            'permissions' => 'sometimes|array',
            'permissions.*' => 'exists:permissions,name',
        ];
        $id = (int)($this->route('id') ?? 0);
        if ($id) {
            $rules = [
                'name' => 'sometimes|required|string|max:255|unique:roles,name,' . $id,
                'role_name' => 'sometimes|required|string|max:255',
                'status' => 'sometimes|integer|in:0,1',
                'sort' => 'sometimes|integer',
                'remark' => 'sometimes|nullable|string|max:255',
                'permissions' => 'sometimes|array',
                'permissions.*' => 'exists:permissions,name',
            ];
        }
        return $rules;
    }

    /**
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => '角色标识不能为空',
            'role_name.required' => '角色名称不能为空',
        ];
    }
}
