<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceStartTest extends TestCase
{
    use RefreshDatabase;

    // 出勤ボタンが表示され、出勤後に勤務中になる
    public function test_can_start_work()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get('/attendance');
        $response->assertSee('出勤');

        $this->post('/attendance/start');

        $response = $this->get('/attendance');
        $response->assertSee('出勤中');
    }

    // 退勤済の場合、出勤ボタンが表示されない
    public function test_cannot_start_work_twice_in_a_day()
    {
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => Carbon::today(),
            'start_time' => Carbon::now()->subHours(8),
            'end_time' => Carbon::now(),
        ]);

        $this->actingAs($user);

        $response = $this->get('/attendance');

        $response->assertDontSee('出勤');
    }

    // 出勤時刻が勤怠一覧画面に表示される
    public function test_start_time_is_shown_in_attendance_list()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $this->post('/attendance/start');

        $attendance = Attendance::first();

        $response = $this->get('/attendance/list');

        $response->assertSee(
            Carbon::parse($attendance->start_time)->format('H:i')
        );
    }
}
