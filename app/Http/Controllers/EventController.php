<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Exports\EventRegistrationsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * 活动控制器
 * 处理活动的 CRUD 操作（仅管理员可操作）
 */
class EventController extends Controller
{
    /**
     * 显示活动列表
     * 所有用户都可以查看活动列表
     * 支持条件查询：搜索名称、地点、时间范围、状态筛选
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // 构建查询
        $query = Event::with(['creator', 'confirmedRegistrations']);

        // 搜索活动名称
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // 搜索地点
        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        // 筛选开始时间（从某个时间开始）
        if ($request->filled('start_date')) {
            $query->where('event_time', '>=', $request->start_date);
        }

        // 筛选结束时间（到某个时间结束）
        if ($request->filled('end_date')) {
            $query->where('event_time', '<=', $request->end_date);
        }

        // 状态筛选
        if ($request->filled('status')) {
            $now = now();
            switch ($request->status) {
                case 'upcoming': // 未开始
                    $query->where('event_time', '>', $now);
                    break;
                case 'ongoing': // 进行中
                    $query->where('event_time', '<=', $now)
                          ->whereRaw('event_time >= DATE_SUB(NOW(), INTERVAL 1 DAY)'); // 假设活动持续1天
                    break;
                case 'ended': // 已结束
                    $query->whereRaw('event_time < DATE_SUB(NOW(), INTERVAL 1 DAY)');
                    break;
                case 'full': // 已满员
                    // 需要在查询后筛选，因为涉及计算字段
                    break;
            }
        }

        // 排序
        $sortBy = $request->get('sort_by', 'event_time'); // 默认按时间排序
        $sortOrder = $request->get('sort_order', 'asc'); // 默认升序
        $query->orderBy($sortBy, $sortOrder);

        // 如果有满员筛选，需要先获取数据再过滤
        if ($request->filled('status') && $request->status === 'full') {
            // 获取所有活动（不分页），过滤出满员的活动
            $allEvents = $query->get()->filter(function ($event) {
                return $event->isFull();
            });
            
            // 手动分页
            $perPage = 12;
            $currentPage = $request->get('page', 1);
            $items = $allEvents->slice(($currentPage - 1) * $perPage, $perPage)->values();
            
            // 创建自定义分页器
            $events = new LengthAwarePaginator(
                $items,
                $allEvents->count(),
                $perPage,
                $currentPage,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        } else {
            // 分页（每页6条，保持查询参数）
            // 注意：如果数据量少，分页器可能不显示（Laravel 默认只有多页时才显示）
            $events = $query->paginate(6)->withQueryString();
        }

        return view('events.index', compact('events'));
    }

    /**
     * 显示创建活动表单
     * 仅管理员可以创建活动
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('events.create');
    }

    /**
     * 存储新创建的活动
     * 仅管理员可以创建活动
     * 
     * @param \App\Http\Requests\StoreEventRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreEventRequest $request)
    {
        // 创建活动（已验证通过）
        $event = Event::create([
            'user_id' => auth()->id(), // 当前登录的管理员ID
            'name' => $request->name,
            'event_time' => $request->event_time,
            'location' => $request->location,
            'max_participants' => $request->max_participants,
            'description' => $request->description,
        ]);

        return redirect()->route('events.show', $event)
            ->with('success', 'Event created successfully!');
    }

    /**
     * 显示指定活动的详细信息
     * 所有用户都可以查看活动详情
     * 
     * @param \App\Models\Event $event
     * @return \Illuminate\View\View
     */
    public function show(Event $event)
    {
        // 预加载关联数据，减少查询次数
        $event->load(['creator', 'registrations.user']);
        
        // 检查当前用户是否已报名
        $userRegistration = null;
        if (auth()->check()) {
            $userRegistration = $event->registrations()
                ->where('user_id', auth()->id())
                ->whereIn('status', ['pending', 'confirmed'])
                ->first();
        }

        return view('events.show', compact('event', 'userRegistration'));
    }

    /**
     * 显示编辑活动表单
     * 仅管理员可以编辑活动
     * 
     * @param \App\Models\Event $event
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit(Event $event)
    {
        // 检查活动是否可以编辑（活动开始后不能编辑）
        if (!$event->canEdit()) {
            return redirect()->route('events.show', $event)
                ->with('error', 'Event has started, cannot edit');
        }

        return view('events.edit', compact('event'));
    }

    /**
     * 更新指定的活动
     * 仅管理员可以更新活动
     * 
     * @param \App\Http\Requests\UpdateEventRequest $request
     * @param \App\Models\Event $event
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateEventRequest $request, Event $event)
    {
        // 检查活动是否可以编辑（活动开始后不能编辑）
        if (!$event->canEdit()) {
            return redirect()->route('events.show', $event)
                ->with('error', 'Event has started, cannot edit');
        }

        // 更新活动信息（已验证通过）
        $event->update([
            'name' => $request->name,
            'event_time' => $request->event_time,
            'location' => $request->location,
            'max_participants' => $request->max_participants,
            'description' => $request->description,
        ]);

        // 如果是从管理页面编辑，返回管理页面；否则返回活动详情
        $redirectRoute = $request->has('from') && $request->from === 'manage' 
            ? 'events.manage' 
            : 'events.show';

        return redirect()->route($redirectRoute, $event)
            ->with('success', 'Event updated successfully!');
    }

    /**
     * 删除指定的活动
     * 仅管理员可以删除活动，且只能删除未开始的活动
     * 
     * @param \App\Models\Event $event
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Event $event, Request $request)
    {
        // 检查活动是否可以删除（活动开始后不能删除）
        if (!$event->canDelete()) {
            $redirectRoute = $request->has('from') && $request->from === 'manage' 
                ? 'events.manage' 
                : 'events.show';
            return redirect()->route($redirectRoute, $event)
                ->with('error', 'Event has started, cannot delete');
        }

        // 删除活动（会级联删除相关的报名记录）
        $event->delete();

        // 如果是从管理页面删除，返回管理页面；否则返回活动列表
        $redirectRoute = $request->has('from') && $request->from === 'manage' 
            ? 'events.manage' 
            : 'events.index';

        return redirect()->route($redirectRoute)
            ->with('success', 'Event deleted successfully!');
    }

    /**
     * 显示活动管理页面（仅管理员）
     * 以表格形式显示所有活动，提供编辑和删除功能
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function manage(Request $request)
    {
        // 构建查询
        $query = Event::with(['creator', 'confirmedRegistrations']);

        // 搜索活动名称
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // 搜索地点
        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        // 筛选开始时间
        if ($request->filled('start_date')) {
            $query->where('event_time', '>=', $request->start_date);
        }

        // 筛选结束时间
        if ($request->filled('end_date')) {
            $query->where('event_time', '<=', $request->end_date);
        }

        // 状态筛选
        if ($request->filled('status')) {
            $now = now();
            switch ($request->status) {
                case 'upcoming':
                    $query->where('event_time', '>', $now);
                    break;
                case 'ongoing':
                    $query->where('event_time', '<=', $now)
                          ->whereRaw('event_time >= DATE_SUB(NOW(), INTERVAL 1 DAY)');
                    break;
                case 'ended':
                    $query->whereRaw('event_time < DATE_SUB(NOW(), INTERVAL 1 DAY)');
                    break;
            }
        }

        // 排序
        $sortBy = $request->get('sort_by', 'event_time');
        $sortOrder = $request->get('sort_order', 'desc'); // 管理页面默认降序（最新的在前）
        $query->orderBy($sortBy, $sortOrder);

        // 分页（每页8条，保持查询参数）
        $events = $query->paginate(8)->withQueryString();

        return view('events.manage', compact('events'));
    }

    /**
     * 导出活动的报名数据为Excel
     * 仅管理员可以导出数据
     * 
     * @param \App\Models\Event $event
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Event $event)
    {
        // 检查用户是否有权限（管理员）
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Only administrators can export data');
        }

        // 生成文件名（活动名称_日期时间）
        $fileName = $event->name . '_' . now()->format('YmdHis') . '.xlsx';

        // 导出Excel文件
        return Excel::download(new EventRegistrationsExport($event), $fileName);
    }
}
