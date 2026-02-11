<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Models\Attendance;

class AttendancesTableSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();

        $workDates = [
            '2023-06-01',
            '2023-06-02',
            '2023-06-03',
            '2023-06-05',
            '2023-06-06',
            '2023-06-08',
            '2023-06-09',
            '2023-06-10',
            '2023-06-11',
            '2023-06-12',
            '2023-06-13',
            '2023-06-14',
            '2023-06-15',
            '2023-06-16',
            '2023-06-18',
            '2023-06-19',
            '2023-06-20',
            '2023-06-21',
            '2023-06-22',
            '2023-06-23',
            '2023-06-24',
            '2023-06-26',
            '2023-06-27',
            '2023-06-28',
            '2023-06-29',
            '2023-06-30',
        ];

        foreach ($users as $user) {
            foreach ($workDates as $date) {
                $start = Carbon::parse($date . ' 09:00');
                $end   = Carbon::parse($date . ' 18:00');
                $break1Start = Carbon::parse($date . ' 12:00');
                $break1End   = Carbon::parse($date . ' 13:00');

                $breakMinutes = $break1Start->diffInMinutes($break1End);

                $workMinutes = $start->diffInMinutes($end) - $breakMinutes;

                Attendance::create([
                    'user_id' => $user->id,
                    'work_date' => $date,
                    'start_time' => $start,
                    'end_time'   => $end,
                    'break1_start_time' => '12:00',
                    'break1_end_time'   => '13:00',
                    'break2_start_time' => null,
                    'break2_end_time'   => null,
                    'break_minutes' => $breakMinutes,
                    'work_minutes' => $workMinutes,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
