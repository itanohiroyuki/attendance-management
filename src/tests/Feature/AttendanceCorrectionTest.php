<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;

class AttendanceCorrectionTest extends TestCase
{
    use RefreshDatabase;

    // 出勤時間が退勤時間より後の場合、エラーになる
    public function test_start_time_after_end_time_is_invalid()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->post("/attendance/{$attendance->id}/update", [
            'start_time' => '18:00',
            'end_time'   => '09:00',
            'remarks'    => '修正理由',
        ]);

        $response->assertSessionHasErrors([
            'start_time' => '出勤時間が不適切な値です',
        ]);
    }

    // 休憩開始時間が退勤時間より後の場合、エラーになる
    public function test_break_start_after_end_time_is_invalid()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->post("/attendance/{$attendance->id}/update", [
            'break1_start_time' => '19:00',
            'end_time'          => '18:00',
            'remarks'           => '修正理由',
        ]);

        $response->assertSessionHasErrors([
            'break1_start_time' => '休憩時間が不適切な値です',
        ]);
    }

    // 休憩終了時間が退勤時間より後の場合、エラーになる
    public function test_break_end_after_end_time_is_invalid()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->post("/attendance/{$attendance->id}/update", [
            'break1_end_time' => '19:00',
            'end_time'        => '18:00',
            'remarks'         => '修正理由',
        ]);

        $response->assertSessionHasErrors([
            'break1_end_time' => '休憩時間もしくは退勤時間が不適切な値です',
        ]);
    }

    // 備考が未入力の場合、エラーになる
    public function test_remarks_is_required()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->post("/attendance/{$attendance->id}/update", [
            'start_time' => '09:00',
            'end_time'   => '18:00',
            'remarks'    => '',
        ]);

        $response->assertSessionHasErrors([
            'remarks' => '備考を記入してください',
        ]);
    }

    // 修正申請が実行され、管理者画面に表示される
    public function test_correction_request_is_created()
    {
        $user  = User::factory()->create();
        $admin = User::factory()->admin()->create();

        $attendance = Attendance::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $this->post("/attendance/{$attendance->id}/update", [
            'start_time' => '10:00',
            'end_time'   => '19:00',
            'remarks'    => '修正申請',
        ]);

        $this->actingAs($admin);

        $response = $this->get('/admin/attendance/requests');

        $response->assertSee('修正申請');
    }

    // 承認待ちに自分の申請が全て表示される
    public function test_pending_requests_are_displayed()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $this->post("/attendance/{$attendance->id}/update", [
            'start_time' => '10:00',
            'end_time'   => '19:00',
            'remarks'    => '申請確認',
        ]);

        $response = $this->get('/attendance/requests');

        $response->assertSee('承認待ち');
    }

    // 承認済みに管理者が承認した申請が表示される
    public function test_approved_requests_are_displayed()
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin);

        $response = $this->get('/attendance/requests?status=approved');

        $response->assertSee('承認済み');
    }

    // 申請一覧の「詳細」から勤怠詳細画面に遷移できる
    public function test_request_detail_redirects_to_attendance_detail()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->get("/attendance/requests/{$attendance->id}");

        $response->assertStatus(200);
    }
}
