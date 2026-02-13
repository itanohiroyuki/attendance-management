<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;

class AttendanceBreakTest extends TestCase
{
    use RefreshDatabase;

    // 休憩入ができ、ステータスが「休憩中」になる
    public function test_break_start()
    {
        $user = User::factory()->working()->create();
        $this->actingAs($user);

        $this->post('/attendance/break/start');

        $response = $this->get('/attendance');
        $response->assertSee('休憩中');
    }

    // 休憩入 → 休憩戻 → 再度休憩入ができる
    public function test_break_can_be_taken_multiple_times()
    {
        $user = User::factory()->working()->create();
        $this->actingAs($user);

        $this->post('/attendance/break/start');
        $this->post('/attendance/break/end');

        $response = $this->get('/attendance');
        $response->assertSee('休憩入');
    }

    // 休憩戻でステータスが「出勤中」に戻る
    public function test_break_end()
    {
        $user = User::factory()->working()->create();
        $this->actingAs($user);

        $this->post('/attendance/break/start');
        $this->post('/attendance/break/end');

        $response = $this->get('/attendance');
        $response->assertSee('出勤中');
    }

    // 休憩入 → 休憩戻 → 再度休憩入後、休憩戻ボタンが表示される
    public function test_break_end_can_be_used_multiple_times()
    {
        $user = User::factory()->working()->create();
        $this->actingAs($user);

        $this->post('/attendance/break/start');
        $this->post('/attendance/break/end');
        $this->post('/attendance/break/start');

        $response = $this->get('/attendance');
        $response->assertSee('休憩戻');
    }

    // 勤怠一覧に休憩時刻が記録される
    public function test_break_time_is_displayed_on_attendance_list()
    {
        $user = User::factory()->working()->create();
        $this->actingAs($user);

        $this->post('/attendance/break/start');
        sleep(1);
        $this->post('/attendance/break/end');

        $response = $this->get('/attendance/list');
        $response->assertSee(':');
    }
}
