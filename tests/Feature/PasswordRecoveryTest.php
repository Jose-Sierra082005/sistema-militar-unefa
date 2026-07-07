<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PasswordRecoveryTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_view_can_be_rendered(): void
    {
        $response = $this->get(route('password.forgot'));

        $response->assertStatus(200);
        $response->assertSee('Recuperar Clave');
    }

    public function test_cannot_request_otp_for_non_existent_email(): void
    {
        $response = $this->post('/password/forgot', [
            'email' => 'unknown@unefa.edu.ve',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_can_request_otp_and_send_email(): void
    {
        // Fake Resend API calls
        Http::fake([
            'api.resend.com/emails' => Http::response(['id' => 'fake-email-id'], 200),
        ]);

        $user = User::create([
            'name' => 'Oficial Sierra',
            'email' => 'jose@unefa.edu.ve',
            'password' => Hash::make('Secret123!'),
        ]);

        $response = $this->post('/password/forgot', [
            'email' => 'jose@unefa.edu.ve',
        ]);

        $response->assertRedirect(route('password.verify_otp'));
        $this->assertEquals('jose@unefa.edu.ve', session('password.reset.email'));

        // Check DB has the OTP record
        $this->assertDatabaseHas('password_reset_otps', [
            'email' => 'jose@unefa.edu.ve',
        ]);
    }
}
