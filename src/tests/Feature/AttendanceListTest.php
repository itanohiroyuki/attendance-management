<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function only_my_attendance_is_displayed()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Attendance::factory()->for($user)->create([
            'work_date' => today(),
        ]);

        Attendance::factory()->for($otherUser)->create([
            'work_date' => today(),
        ]);

        $this->actingAs($user);

        $response = $this->get('/attendance/list');

        $response->assertSee($user->name);
        $response->assertDontSee($otherUser->name);
    }

    /** @test */
    public function current_month_is_displayed()
    {
        Carbon::setTestNow(Carbon::create(2026, 2, 1));

        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/attendance/list');

        $response->assertSee('2026/02');
    }

    /** @test */
    public function previous_month_attendance_is_displayed()
    {
        Carbon::setTestNow(Carbon::create(2026, 2, 1));

        $user = User::factory()->create();

        Attendance::factory()->for($user)->create([
            'work_date' => '2026-01-01',
        ]);

        $this->actingAs($user);

        $response = $this->get('/attendance/list?month=prev');

        $response->assertSee('2026年1月');
    }

    /** @test */
    public function next_month_attendance_is_displayed()
    {
        Carbon::setTestNow(Carbon::create(2026, 2, 1));

        $user = User::factory()->create();

        Attendance::factory()->for($user)->create([
            'work_date' => '2026-03-01',
        ]);

        $this->actingAs($user);

        $response = $this->get('/attendance/list?month=next');

        $response->assertSee('2026年3月');
    }

    /** @test */
    public function attendance_detail_page_is_displayed()
    {
        $user = User::factory()->create();

        $attendance = Attendance::factory()->for($user)->create([
            'work_date' => today()->toDateString(),
        ]);

        $this->actingAs($user);
        $response = $this->get('/attendance/list');
        $response->assertSee('詳細');

        $response = $this->get('/attendance/detail/' . $attendance->work_date);
        $response->assertStatus(200);
        $response->assertSee('勤怠詳細');
    }
}
