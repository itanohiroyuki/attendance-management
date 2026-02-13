<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AdminDailyAttendanceTest extends TestCase
{
    use RefreshDatabase;

    // その日の全ユーザーの勤怠情報が確認できる
    public function test_admin_can_see_all_users_attendance_for_today()
    {
        $admin = User::factory()->admin()->create();

        $user1 = User::factory()->create(['name' => 'ユーザー1']);
        $user2 = User::factory()->create(['name' => 'ユーザー2']);

        Attendance::factory()->create([
            'user_id' => $user1->id,
            'work_date' => now()->toDateString(),
        ]);

        Attendance::factory()->create([
            'user_id' => $user2->id,
            'work_date' => now()->toDateString(),
        ]);

        $this->actingAs($admin);

        $response = $this->get('/admin/attendance/list');

        $response->assertStatus(200);
        $response->assertSee('ユーザー1');
        $response->assertSee('ユーザー2');
    }

    // 遷移時に現在の日付が表示される
    public function test_today_date_is_displayed_on_admin_attendance_page()
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin);

        $today = now()->format('Y-m-d');

        $response = $this->get('/admin/attendance/list');

        $response->assertSee($today);
    }

    // 「前日」を押すと前日の勤怠情報が表示される
    public function test_previous_day_attendance_is_displayed()
    {
        $admin = User::factory()->admin()->create();

        $yesterday = Carbon::yesterday()->toDateString();

        $user = User::factory()->create(['name' => '前日ユーザー']);

        Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => $yesterday,
        ]);

        $this->actingAs($admin);

        $response = $this->get('/admin/attendance/list?date=' . $yesterday);

        $response->assertSee($yesterday);
        $response->assertSee('前日ユーザー');
    }

    // 「翌日」を押すと翌日の勤怠情報が表示される
    public function test_next_day_attendance_is_displayed()
    {
        $admin = User::factory()->admin()->create();

        $tomorrow = Carbon::tomorrow()->toDateString();

        $user = User::factory()->create(['name' => '翌日ユーザー']);

        Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => $tomorrow,
        ]);

        $this->actingAs($admin);

        $response = $this->get('/admin/attendance/list?date=' . $tomorrow);

        $response->assertSee($tomorrow);
        $response->assertSee('翌日ユーザー');
    }
}
