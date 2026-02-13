<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminAttendanceRequestTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // 管理者ユーザー（admin ミドルウェア想定）
        $this->admin = User::factory()->create([
            'is_admin' => true,
        ]);
    }

    /** @test */
    public function pending_requests_are_displayed()
    {
        $attendance = Attendance::factory()->create();

        AttendanceCorrection::factory()->create([
            'attendance_id' => $attendance->id,
            'status' => AttendanceCorrection::STATUS_PENDING,
            'reason' => '未承認申請',
        ]);

        $response = $this->actingAs($this->admin)->get(
            '/admin/stamp_correction_request/list?status=pending'
        );

        $response->assertStatus(200);
        $response->assertSee('未承認申請');
    }

    /** @test */
    public function approved_requests_are_displayed()
    {
        $attendance = Attendance::factory()->create();

        AttendanceCorrection::factory()->create([
            'attendance_id' => $attendance->id,
            'status' => AttendanceCorrection::STATUS_APPROVED,
            'reason' => '承認済み申請',
        ]);

        $response = $this->actingAs($this->admin)->get(
            '/admin/stamp_correction_request/list?status=approved'
        );

        $response->assertStatus(200);
        $response->assertSee('承認済み申請');
    }

    /** @test */
    public function request_detail_is_displayed_correctly()
    {
        $attendance = Attendance::factory()->create([
            'start_time' => '09:00',
            'end_time' => '18:00',
        ]);

        $correction = AttendanceCorrection::factory()->create([
            'attendance_id' => $attendance->id,
            'requested_start_time' => '10:00',
            'requested_end_time' => '19:00',
            'status' => AttendanceCorrection::STATUS_PENDING,
        ]);

        $response = $this->actingAs($this->admin)->get(
            '/admin/stamp_correction_request/approve/' . $correction->id
        );

        $response->assertStatus(200);
        $response->assertSee('10:00');
        $response->assertSee('19:00');
    }

    /** @test */
    public function request_can_be_approved()
    {
        $attendance = Attendance::factory()->create([
            'start_time' => '09:00',
            'end_time' => '18:00',
        ]);

        $correction = AttendanceCorrection::factory()->create([
            'attendance_id' => $attendance->id,
            'requested_start_time' => '10:00',
            'requested_end_time' => '19:00',
            'requested_break1_start_time' => '12:00',
            'requested_break1_end_time' => '13:00',
            'status' => AttendanceCorrection::STATUS_PENDING,
        ]);

        $response = $this->actingAs($this->admin)->post(
            '/admin/stamp_correction_request/' . $correction->id . '/approve'
        );

        $response->assertRedirect();
        $response->assertSessionHas('message', '申請を承認しました');

        $this->assertDatabaseHas('attendance_corrections', [
            'id' => $correction->id,
            'status' => AttendanceCorrection::STATUS_APPROVED,
            'approved_by' => $this->admin->id,
        ]);

        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'start_time' => $attendance->work_date->format('Y-m-d') . ' 10:00:00',
            'end_time'   => $attendance->work_date->format('Y-m-d') . ' 19:00:00',
        ]);
    }
}
