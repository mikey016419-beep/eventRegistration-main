<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * 活动数据填充器
 * 用于填充测试数据
 */
class EventSeeder extends Seeder
{
    /**
     * 运行数据填充
     */
    public function run(): void
    {
        // 创建1个管理员用户
        $admin = User::factory()->admin()->create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
        ]);

        // 创建10个普通用户
        $users = User::factory()->count(10)->create();

        // 创建5个未来活动（由管理员创建）
        $futureEvents = Event::factory()
            ->count(5)
            ->create([
                'user_id' => $admin->id,
            ]);

        // 创建2个已开始的活动
        $startedEvents = Event::factory()
            ->count(2)
            ->started()
            ->create([
                'user_id' => $admin->id,
            ]);

        // 创建1个已结束的活动
        $endedEvent = Event::factory()
            ->count(1)
            ->ended()
            ->create([
                'user_id' => $admin->id,
            ]);

        // 为每个活动创建一些报名记录（2-5个报名）
        $allEvents = $futureEvents->merge($startedEvents)->merge($endedEvent->take(1));
        
        foreach ($allEvents as $event) {
            $registrationCount = rand(2, min(5, $event->max_participants));
            // 随机选择不同的用户
            $selectedUsers = $users->random(min($registrationCount, $users->count()));
            
            foreach ($selectedUsers as $user) {
                Registration::factory()
                    ->confirmed()
                    ->create([
                        'event_id' => $event->id,
                        'user_id' => $user->id,
                    ]);
            }
        }
    }
}
