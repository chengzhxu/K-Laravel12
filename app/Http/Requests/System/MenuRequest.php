<?php

namespace App\Http\Requests\System;

use App\Models\Menu;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class MenuRequest extends FormRequest
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
//            'parent_id' => 'nullable|integer|exists:menus,id',
            'parent_id' => [
                'nullable',
                'integer',
                'min:0',
                function ($attribute, $value, $fail) {
                    if ($value > 0 && !Menu::where('id', $value)->exists()) {
                        $fail('选择的父级菜单不存在');
                    }
                }
            ],
            'name' => 'required|string|max:255',
            'path' => 'nullable|string|max:255',
            'component' => 'nullable|string|max:255',
            'permission' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'redirect' => 'nullable|string|max:255',
            'type' => 'required|integer|in:0,1',
            'hidden' => 'nullable|integer|in:0,1',
            'status' => 'nullable|integer|in:0,1',
            'sort' => 'nullable|integer',
        ];
        $id = (int)($this->route('id') ?? 0);
        if ($id) {
            $rules['name'] = 'sometimes|required|string|max:255';
            $rules['permission'] = 'sometimes|required|string|max:255';
            $rules['type'] = 'sometimes|required|integer|in:0,1';
            $rules['hidden'] = 'sometimes|required|integer|in:0,1';
            $rules['status'] = 'sometimes|required|integer|in:0,1';
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
            'name.required' => '菜单名称不能为空',
            'type.required' => '请选择菜单类型',
            'permission.required' => '权限标识不能为空'
        ];
    }
}
