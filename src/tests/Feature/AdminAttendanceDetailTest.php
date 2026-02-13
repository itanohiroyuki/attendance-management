<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;

class AdminAttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    // 勤怠詳細画面に選択した勤怠情報が表示される
    public function test_attendance_detail_shows_selected_data()
    {
        $admin = User::factory()->admin()->create();

        $attendance = Attendance::factory()->create([
            'start_time' => '09:00',
            'end_time'   => '18:00',
            'note'       => 'テスト備考',
        ]);

        $this->actingAs($admin);

        $response = $this->get('/admin/attendance/detail/' . $attendance->id);

        $response->assertStatus(200);
        $response->assertSee('09:00');
        $response->assertSee('18:00');
        $response->assertSee('テスト備考');
    }

    // 出勤時間が退勤時間より後の場合エラーになる
    public function test_start_time_after_end_time_validation_error()
    {
        $admin = User::factory()->admin()->create();

        $attendance = Attendance::factory()->create();

        $this->actingAs($admin);

        $response = $this->put("/admin/attendance/detail/{$attendance->id}", [
            'start_time' => '18:00',
            'end_time'   => '09:00',
            'note'       => '修正',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'start_time' => '出勤時間もしくは退勤時間が不適切な値です',
        ]);
    }

    // 休憩開始時間が退勤時間より後の場合エラーになる
    public function test_break_start_after_end_time_validation_error()
    {
        $admin = User::factory()->admin()->create();

        $attendance = Attendance::factory()->create([
            'end_time' => '18:00',
        ]);

        $this->actingAs($admin);

        $response = $this->put("/admin/attendance/detail/{$attendance->id}", [
            'start_time' => '09:00',
            'end_time'   => '18:00',
            'break1_start_time' => '19:00',
            'note'       => '修正',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'break1_start_time' => '休憩時間が不適切な値です',
        ]);
    }

    // 休憩終了時間が退勤時間より後の場合エラーになる
    public function test_break_end_after_end_time_validation_error()
    {
        $admin = User::factory()->admin()->create();

        $attendance = Attendance::factory()->create([
            'end_time' => '18:00',
        ]);

        $this->actingAs($admin);

        $response = $this->put("/admin/attendance/detail/{$attendance->id}", [
            'start_time' => '09:00',
            'end_time'   => '18:00',
            'break1_end_time' => '19:00',
            'note'       => '修正',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'break1_end_time' => '休憩時間もしくは退勤時間が不適切な値です',
        ]);
    }

    // 備考が未入力の場合エラーになる
    public function test_note_is_required()
    {
        $admin = User::factory()->admin()->create();

        $attendance = Attendance::factory()->create();

        $this->actingAs($admin);

        $response = $this->put("/admin/attendance/detail/{$attendance->id}", [
            'start_time' => '09:00',
            'end_time'   => '18:00',
            'note'       => '',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'note' => '備考を記入してください',
        ]);
    }
}
