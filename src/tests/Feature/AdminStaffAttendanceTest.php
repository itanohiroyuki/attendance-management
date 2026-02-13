<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;

class AdminStaffAttendanceTest extends TestCase
{
    use RefreshDatabase;

    // 管理者が全一般ユーザーの氏名・メールアドレスを確認できる
    public function test_admin_can_see_all_users_name_and_email()
    {
        $admin = User::factory()->admin()->create();

        $user = User::factory()->create();

        $this->actingAs($admin);

        $response = $this->get('/admin/attendance/staff');

        $response->assertStatus(200);

        foreach ($users as $user) {
            $response->assertSee($user->name);
            $response->assertSee($user->email);
        }
    }

    // 選択したユーザーの勤怠情報が正しく表示される
    public function test_admin_can_see_selected_user_attendance_list()
    {
        $admin = User::factory()->admin()->create();

        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'start_time' => '09:00',
            'end_time'   => '18:00',
        ]);

        $this->actingAs($admin);

        $response = $this->get('/admin/attendance/staff/' . $user->id);

        $response->assertStatus(200);
        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    // 前月ボタンで前月の勤怠情報が表示される
    public function test_previous_month_attendance_is_displayed()
    {
        $admin = User::factory()->admin()->create();
        $user  = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => now()->subMonth()->format('Y-m-d'),
        ]);

        $this->actingAs($admin);

        $response = $this->get('/admin/attendance/staff/' . $user->id . '?month=prev');

        $response->assertStatus(200);
        $response->assertSee(now()->subMonth()->format('Y-m'));
    }

    // 翌月ボタンで翌月の勤怠情報が表示される
    public function test_next_month_attendance_is_displayed()
    {
        $admin = User::factory()->admin()->create();
        $user  = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => now()->addMonth()->format('Y-m-d'),
        ]);

        $this->actingAs($admin);

        $response = $this->get('/admin/attendance/staff/' . $user->id . '?month=next');

        $response->assertStatus(200);
        $response->assertSee(now()->addMonth()->format('Y-m'));
    }

    // 詳細ボタンで勤怠詳細画面に遷移できる
    public function test_admin_can_move_to_attendance_detail_page()
    {
        $admin = User::factory()->admin()->create();

        $attendance = Attendance::factory()->create();

        $this->actingAs($admin);

        $response = $this->get('/admin/attendance/detail/' . $attendance->id);

        $response->assertStatus(200);
        $response->assertSee('勤怠詳細');
    }
}
