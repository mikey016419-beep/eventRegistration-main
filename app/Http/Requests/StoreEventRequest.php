<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class StoreEventRequest extends FormRequest
{
    /**
     * 判断用户是否有权限创建活动
     * 只有管理员可以创建活动
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    /**
     * 获取验证规则
     * 活动创建的验证规则
     */
    public function rules(): array
    {
        // 计算最小允许时间：当前时间 + 1天（活动时间必须大于这个值）
        // 因为报名截止时间为活动开始前一天，所以活动时间至少要是后天
        $minDateTime = Carbon::now()->addDay();
        
        return [
            'name' => ['required', 'string', 'max:50'],                          // 活动名称：必填，字符串，最大50字符
            'event_time' => [
                'required', 
                'date',
                function ($attribute, $value, $fail) use ($minDateTime) {
                    $eventTime = Carbon::parse($value);
                    // 活动时间必须大于（当前时间 + 1天）
                    if ($eventTime <= $minDateTime) {
                        $fail('Event time must be at least 1 day after the current time (at least the day after tomorrow), because registration closes one day before the event starts');
                    }
                }
            ],
            'location' => ['required', 'string', 'max:100'],                    // 活动地点：必填，字符串，最大100字符
            'max_participants' => ['required', 'integer', 'min:1'],             // 最大参与人数：必填，整数，最小值为1
            'description' => ['nullable', 'string'],                            // 活动简介：可选，字符串
        ];
    }

    /**
     * 自定义验证错误消息
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Event name is required',
            'name.max' => 'Event name cannot exceed 50 characters',
            'event_time.required' => 'Event time is required',
            'event_time.date' => 'Event time format is incorrect',
            'location.required' => 'Location is required',
            'location.max' => 'Location cannot exceed 100 characters',
            'max_participants.required' => 'Max participants is required',
            'max_participants.integer' => 'Max participants must be an integer',
            'max_participants.min' => 'Max participants must be at least 1',
        ];
    }
}
