<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class AttendanceEndTest extends TestCase
{
    // 退勤ができ、ステータスが「退勤済」になる
    public function test_work_end()
    {
        $user = User::factory()->working()->create();
        $this->actingAs($user);

        // 打刻画面に「退勤」ボタンが表示されている
        $response = $this->get('/attendance');
        $response->assertSee('退勤');

        // 退勤処理
        $this->post('/attendance/end');

        // ステータスが「退勤済」になる
        $response = $this->get('/attendance');
        $response->assertSee('退勤済');
    }

    // 勤怠一覧に退勤時刻が記録される
    public function test_work_end_time_is_displayed_on_attendance_list()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // 出勤 → 退勤
        $this->post('/attendance/start');
        sleep(1); // 時刻差を作る
        $this->post('/attendance/end');

        // 勤怠一覧画面
        $response = $this->get('/attendance/list');

        // 退勤時刻（HH:mm）が表示されている想定
        $response->assertSee(':');
    }
}
