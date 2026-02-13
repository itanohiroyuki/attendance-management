<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use App\Models\User;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    // 会員登録後、認証メールが送信される
    public function test_verification_email_is_sent_after_register()
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', 'test@example.com')->first();

        Notification::assertSentTo(
            $user,
            VerifyEmail::class
        );
    }

    // 「認証はこちらから」ボタンでメール認証画面に遷移する
    public function test_verify_notice_button_redirects_to_verify_page()
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user);

        $response = $this->get('/email/verify');

        $response->assertStatus(200);
        $response->assertSee('認証はこちらから');
    }

    // メール認証完了後、勤怠登録画面に遷移する
    public function test_verified_user_is_redirected_to_attendance_page()
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user);

        $verificationUrl = url('/email/verify/' . $user->id . '/' . sha1($user->email));

        $response = $this->get($verificationUrl);

        $response->assertRedirect('/attendance');
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }
}
