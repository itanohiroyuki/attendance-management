<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'work_date' => now()->toDateString(),
            'start_time' => now()->setTime(9, 0),
            'end_time' => now()->setTime(18, 0),

            'break1_start_time' => '12:00:00',
            'break1_end_time' => '13:00:00',
            'break2_start_time' => null,
            'break2_end_time' => null,

            'break_minutes' => 60,
            'work_minutes' => 480,
            'note' => null,
        ];
    }

    /**
     * 勤務中
     */
    public function working()
    {
        return $this->state(function () {
            return [
                'end_time' => null,
            ];
        });
    }

    /**
     * 休憩中（1回目の休憩）
     */
    public function onBreak()
    {
        return $this->state(function () {
            return [
                'break1_start_time' => '12:00:00',
                'break1_end_time'   => null,
            ];
        });
    }

    /**
     * 退勤済み
     */
    public function finished()
    {
        return $this->state(function () {
            return [
                'end_time' => now()->setTime(18, 0),
                'break1_start_time' => '12:00:00',
                'break1_end_time'   => '13:00:00',
                'break_minutes' => 60,
                'work_minutes'  => 8 * 60,
            ];
        });
    }
}
