<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceCorrection;

class AdminAttendanceRequestTest extends TestCase
{
    use RefreshDatabase;

    // 承認待ちの修正申請が全て表示される
    public function test_pending_requests_are_displayed()
    {
        $admin = User::factory()->admin()->create();

        $requests = AttendanceCorrection::factory()->count(2)->create([
            'status' => 'pending',
        ]);

        $this->actingAs($admin);

        $response = $this->get('/admin/requests?status=pending');

        $response->assertStatus(200);

        foreach ($requests as $request) {
            $response->assertSee($request->id);
        }
    }

    // 承認済みの修正申請が全て表示される
    public function test_approved_requests_are_displayed()
    {
        $admin = User::factory()->admin()->create();

        $requests = AttendanceCorrection::factory()->count(2)->create([
            'status' => 'approved',
        ]);

        $this->actingAs($admin);

        $response = $this->get('/admin/requests?status=approved');

        $response->assertStatus(200);

        foreach ($requests as $request) {
            $response->assertSee($request->id);
        }
    }

    // 修正申請の詳細内容が正しく表示される
    public function test_request_detail_is_displayed_correctly()
    {
        $admin = User::factory()->admin()->create();

        $request = AttendanceCorrection::factory()->create([
            'reason' => '出勤時間の修正',
        ]);

        $this->actingAs($admin);

        $response = $this->get('/admin/requests/' . $request->id);

        $response->assertStatus(200);
        $response->assertSee('出勤時間の修正');
    }

    // 修正申請の承認処理が正しく行われる
    public function test_request_can_be_approved()
    {
        $admin = User::factory()->admin()->create();

        $attendance = Attendance::factory()->create([
            'start_time' => '09:00',
        ]);

        $request = AttendanceCorrection::factory()->create([
            'attendance_id' => $attendance->id,
            'new_start_time' => '10:00',
            'status' => 'pending',
        ]);

        $this->actingAs($admin);

        $response = $this->post('/admin/requests/' . $request->id . '/approve');

        $response->assertStatus(302);

        // 申請が承認済みになっている
        $this->assertDatabaseHas('attendance_requests', [
            'id' => $request->id,
            'status' => 'approved',
        ]);

        // 勤怠情報が更新されている
        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'start_time' => '10:00',
        ]);
    }
}
