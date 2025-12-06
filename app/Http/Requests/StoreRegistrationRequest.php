<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRegistrationRequest extends FormRequest
{
    /**
     * 判断用户是否有权限报名
     * 只有已登录的用户可以报名
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * 获取验证规则
     * 报名验证规则
     */
    public function rules(): array
    {
        $eventId = $this->route('event')->id ?? $this->input('event_id');
        
        return [
            'event_id' => ['required', 'exists:events,id'],                     // 活动ID：必填，必须存在于events表
            'phone' => ['required', 'string', 'regex:/^1[3-9]\d{9}$/'],        // 联系电话：必填，11位手机号格式（1开头，第二位3-9，共11位）
        ];
    }

    /**
     * 自定义验证错误消息
     */
    public function messages(): array
    {
        return [
            'event_id.required' => 'Please select an event to register',
            'event_id.exists' => 'The selected event does not exist',
            'phone.required' => 'Contact phone is required',
            'phone.regex' => 'Phone format is incorrect, please enter an 11-digit phone number',
        ];
    }

    /**
     * 配置验证器实例
     * 添加自定义验证：同一用户不能重复报名同一活动
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $eventId = $this->input('event_id');
            $userId = auth()->id();
            
            if ($eventId && $userId) {
                $existingRegistration = \App\Models\Registration::where('user_id', $userId)
                    ->where('event_id', $eventId)
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->exists();
                
                if ($existingRegistration) {
                    $validator->errors()->add('event_id', 'You have already registered for this event, duplicate registration is not allowed');
                }
            }
        });
    }
}
