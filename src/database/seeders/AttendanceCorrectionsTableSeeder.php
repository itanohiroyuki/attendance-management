<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use Carbon\Carbon;

class AttendanceCorrectionsTableSeeder extends Seeder
{
    public function run(): void
    {
        $targets = [
            [
                'email' => 'reina.n@coachtech.com',
                'work_date' => '2023-06-01',
                'created_at' => '2023-06-02',
            ],
            [
                'email' => 'taro.y@coachtech.com',
                'work_date' => '2023-06-01',
                'created_at' => '2023-06-02',
            ],
            [
                'email' => 'issei.m@coachtech.com',
                'work_date' => '2023-06-01',
                'created_at' => '2023-06-02',
            ],
        ];

        foreach ($targets as $target) {

            $user = User::where('email', $target['email'])->first();
            if (! $user) {
                continue;
            }

            $attendance = Attendance::where('user_id', $user->id)
                ->whereDate('work_date', $target['work_date'])
                ->first();

            if (! $attendance) {
                continue;
            }

            // ★ 各ユーザー 9件ずつ作成
            for ($i = 1; $i <= 9; $i++) {
                AttendanceCorrection::create([
                    'user_id' => $user->id,
                    'attendance_id' => $attendance->id,
                    'status' => AttendanceCorrection::STATUS_PENDING,
                    'reason' => '遅延のため',
                    'requested_start_time' => '09:00',
                    'requested_end_time'   => '18:00',
                    'requested_break1_start_time' => '12:00',
                    'requested_break1_end_time'   => '13:00',
                    'requested_break2_start_time' => null,
                    'requested_break2_end_time'   => null,
                    'created_at' => Carbon::parse($target['created_at'])->addMinutes($i),
                    'updated_at' => Carbon::parse($target['created_at'])->addMinutes($i),
                ]);
            }
        }
    }
}
