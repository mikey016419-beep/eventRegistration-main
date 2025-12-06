<?php

namespace App\Exports;

use App\Models\Event;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

/**
 * 活动报名数据导出类
 * 用于将活动的报名记录导出为Excel文件
 */
class EventRegistrationsExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize
{
    /**
     * 活动实例
     * 
     * @var \App\Models\Event
     */
    protected $event;

    /**
     * 创建导出实例
     * 
     * @param \App\Models\Event $event
     */
    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    /**
     * 获取要导出的数据集合
     * 
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // 获取该活动的所有已确认的报名记录，并加载用户信息
        return $this->event->registrations()
            ->where('status', 'confirmed')
            ->with('user')
            ->get();
    }

    /**
     * 定义Excel表头
     * 
     * @return array
     */
    public function headings(): array
    {
        return [
            '序号',
            '姓名',
            '邮箱',
            '联系电话',
            '报名时间',
            '报名状态',
        ];
    }

    /**
     * 映射每条记录到Excel行
     * 
     * @param mixed $registration
     * @return array
     */
    public function map($registration): array
    {
        return [
            $registration->id,
            $registration->user->name,
            $registration->user->email,
            $registration->phone,
            $registration->created_at->format('Y-m-d H:i:s'),
            $registration->status === 'confirmed' ? '已确认' : ($registration->status === 'pending' ? '待确认' : '已取消'),
        ];
    }

    /**
     * 设置工作表标题
     * 
     * @return string
     */
    public function title(): string
    {
        return $this->event->name;
    }
}
