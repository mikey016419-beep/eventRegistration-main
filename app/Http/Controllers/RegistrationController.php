<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use App\Http\Requests\StoreRegistrationRequest;
use App\Notifications\RegistrationConfirmed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * 报名控制器
 * 处理用户的报名、取消报名和查看报名记录
 */
class RegistrationController extends Controller
{

    /**
     * 处理用户报名
     * 
     * @param \App\Http\Requests\StoreRegistrationRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreRegistrationRequest $request)
    {
        // 获取活动ID
        $eventId = $request->event_id;
        $event = Event::findOrFail($eventId);
        
        // 检查活动是否可以报名（活动已开始或已满员不能报名）
        if (!$event->canRegister()) {
            return back()->with('error', $event->isStarted() 
                ? 'Event has started, registration is closed' 
                : 'Event is full, registration is closed');
        }

        // 创建报名记录（状态为 confirmed，表示已确认）
        $registration = Registration::create([
            'user_id' => auth()->id(),
            'event_id' => $event->id,
            'phone' => $request->phone,
            'status' => 'confirmed', // 直接确认为已确认状态
        ]);

        return redirect()->route('registrations.my')
            ->with('success', 'Registration successful!');
    }

    /**
     * 取消报名
     * 
     * @param \App\Models\Registration $registration
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel(Registration $registration)
    {
        // 检查是否为当前用户的报名记录
        if ($registration->user_id !== auth()->id()) {
            abort(403, 'You do not have permission to operate this registration');
        }

        // 检查是否可以取消报名（活动开始前可以取消）
        if (!$registration->canCancel()) {
            return back()->with('error', 'Event has started, cannot cancel registration');
        }

        // 取消报名
        $registration->cancel();

        return redirect()->route('registrations.my')
            ->with('success', 'Registration cancelled');
    }

    /**
     * 显示当前用户的报名记录
     * 
     * @return \Illuminate\View\View
     */
    public function my()
    {
        // 获取当前用户的所有报名记录，按创建时间倒序排列
        $registrations = Registration::where('user_id', auth()->id())
            ->with(['event.creator'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('registrations.my', compact('registrations'));
    }
}
