<?php

namespace Database\Factories;

use App\Models\AttendanceCorrection;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceCorrectionFactory extends Factory
{
    protected $model = AttendanceCorrection::class;

    public function definition()
    {
        return [
            'attendance_id' => Attendance::factory(),
            'user_id'       => User::factory(),
            'status'        => 'pending', // pending / approved
            'reason'        => '修正申請',
        ];
    }

    public function approved()
    {
        return $this->state(fn() => [
            'status' => 'approved',
        ]);
    }
}
