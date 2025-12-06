<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * 数据库填充器
 * 主填充器，调用其他填充器
 */
class DatabaseSeeder extends Seeder
{
    /**
     * 运行数据填充
     */
    public function run(): void
    {
        // 调用活动填充器（会自动创建用户和报名记录）
        $this->call([
            EventSeeder::class,
        ]);
    }
}
