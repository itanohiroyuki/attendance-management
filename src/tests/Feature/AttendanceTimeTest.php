<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class AttendanceTimeTest extends TestCase
{
    use RefreshDatabase;

    // 現在日時が画面に正しく表示される
    public function test_current_datetime_is_displayed_correctly()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/attendance');

        $response->assertSee('日付');
        $response->assertSee('時間');
    }
}
