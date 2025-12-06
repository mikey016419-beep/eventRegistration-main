<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * 活动工厂类
 * 用于生成测试活动数据
 * 
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * 定义模型的默认状态
     * 
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // 生成未来7-30天的活动时间
        $eventTime = Carbon::now()->addDays(rand(7, 30))->setTime(rand(9, 18), rand(0, 59));
        
        return [
            'user_id' => User::factory(), // 自动创建关联用户
            'name' => fake()->randomElement([
                'Weekend gathering', 'Mountain climbing activities', 'handicraft class', 'book club', 'Movie Watching', 
                'Board game party', 'badminton match', 'K-song activity', 'Baking class', 'Photography collection style'
            ]),
            'event_time' => $eventTime,
            'location' => fake()->city() . fake()->streetAddress(),
            'max_participants' => rand(5, 50),
            'description' => fake()->optional()->text(200),
        ];
    }

    /**
     * 指定活动已开始
     * 
     * @return static
     */
    public function started(): static
    {
        return $this->state(fn (array $attributes) => [
            'event_time' => Carbon::now()->subHours(rand(1, 48)),
        ]);
    }

    /**
     * 指定活动已结束
     * 
     * @return static
     */
    public function ended(): static
    {
        return $this->state(fn (array $attributes) => [
            'event_time' => Carbon::now()->subDays(rand(2, 7)),
        ]);
    }
}
