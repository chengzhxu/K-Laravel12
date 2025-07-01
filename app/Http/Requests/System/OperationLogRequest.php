<?php

namespace App\Http\Requests\System;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class OperationLogRequest extends FormRequest
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
        return [
            'user_id' => 'nullable|integer|exists:users,id',
            'route' => 'nullable|string|max:255',
            'method' => 'nullable|string|in:GET,POST',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
            'ids' => 'nullable|array',
            'ids.*' => 'integer|exists:operation_logs,id',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'user_id' => '用户ID',
            'route' => '请求路由',
            'method' => '请求方式',
            'start_date' => '开始日期',
            'end_date' => '结束日期',
            'per_page' => '每页数量',
            'page' => '页码',
            'ids' => '日志ID列表',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'user_id.exists' => '指定的用户不存在',
            'method.in' => '请求方式必须是GET、POST之一',
            'end_date.after_or_equal' => '结束日期必须大于或等于开始日期',
            'per_page.max' => '每页最多显示100条记录',
            'ids.*.exists' => '指定的日志记录不存在',
        ];
    }
}
