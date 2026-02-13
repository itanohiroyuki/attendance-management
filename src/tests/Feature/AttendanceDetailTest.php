<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    // 勤怠詳細画面の「名前」がログインユーザーの氏名になっている
    public function test_user_name_is_displayed_on_attendance_detail()
    {
        $user = User::factory()->create([
            'name' => '山田 太郎',
        ]);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);

        $response = $this->get("/attendance/{$attendance->id}");

        $response->assertSee('山田 太郎');
    }

    // 勤怠詳細画面の「日付」が選択した日付になっている
    public function test_work_date_is_displayed_on_attendance_detail()
    {
        $user = User::factory()->create();

        $date = Carbon::create(2025, 2, 1);

        $attendance = Attendance::factory()->create([
            'user_id'   => $user->id,
            'work_date' => $date->toDateString(),
        ]);

        $this->actingAs($user);

        $response = $this->get("/attendance/{$attendance->id}");

        $response->assertSee('2025年');
        $response->assertSee('2月1日');
    }

    // 出勤・退勤時間がログインユーザーの打刻と一致している
    public function test_start_and_end_time_is_displayed_correctly()
    {
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id'    => $user->id,
            'start_time' => '2025-02-01 09:00:00',
            'end_time'   => '2025-02-01 18:00:00',
        ]);

        $this->actingAs($user);

        $response = $this->get("/attendance/{$attendance->id}");

        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    // 休憩時間がログインユーザーの打刻と一致している
    public function test_break_time_is_displayed_correctly()
    {
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id'                => $user->id,
            'break1_start_time'      => '12:00:00',
            'break1_end_time'        => '13:00:00',
        ]);

        $this->actingAs($user);

        $response = $this->get("/attendance/{$attendance->id}");

        $response->assertSee('12:00');
        $response->assertSee('13:00');
    }
}
