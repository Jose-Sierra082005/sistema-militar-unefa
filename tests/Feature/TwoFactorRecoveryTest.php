<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\Google2FAService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use ReflectionClass;
use Tests\TestCase;

class TwoFactorRecoveryTest extends TestCase
{
    use RefreshDatabase;

    public function test_two_factor_recover_view_can_be_rendered(): void
    {
        $response = $this->get(route('two-factor.recover'));

        $response->assertStatus(200);
        $response->assertSee('Restablecer Authenticator');
    }

    public function test_cannot_recover_2fa_when_not_enabled(): void
    {
        User::create([
            'name' => 'Cadete Test',
            'email' => 'cadete@unefa.edu.ve',
            'password' => Hash::make('Secret123!'),
            'two_factor_enabled' => false,
        ]);

        $response = $this->post(route('two-factor.recover.send'), [
            'email' => 'cadete@unefa.edu.ve',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_can_recover_2fa_with_email_otp_and_new_authenticator_code(): void
    {
        Http::fake([
            'api.resend.com/emails' => Http::response(['id' => 'fake-email-id'], 200),
        ]);

        $oldSecret = Google2FAService::generateSecretKey();

        User::create([
            'name' => 'Cadete Test',
            'email' => 'cadete@unefa.edu.ve',
            'password' => Hash::make('Secret123!'),
            'two_factor_enabled' => true,
            'two_factor_secret' => $oldSecret,
        ]);

        $this->post(route('two-factor.recover.send'), [
            'email' => 'cadete@unefa.edu.ve',
        ])->assertRedirect(route('two-factor.recover.verify'));

        $otp = session('2fa.recover.otp');
        $this->assertNotEmpty($otp);

        $this->post(route('two-factor.recover.verify.submit'), [
            'code' => $otp,
        ])->assertRedirect(route('two-factor.recover.setup'));

        $setupResponse = $this->get(route('two-factor.recover.setup'));
        $setupResponse->assertStatus(200);
        $setupResponse->assertSee('Restablecer 2FA');

        $newSecret = Google2FAService::generateSecretKey();
        $newCode = $this->totpCodeForSecret($newSecret);

        $this->post(route('two-factor.recover.activate'), [
            'secret' => $newSecret,
            'code' => $newCode,
        ])->assertRedirect(route('login'));

        $user = User::where('email', 'cadete@unefa.edu.ve')->first();
        $this->assertTrue($user->two_factor_enabled);
        $this->assertEquals($newSecret, $user->two_factor_secret);
        $this->assertNotEquals($oldSecret, $user->two_factor_secret);
    }

    private function totpCodeForSecret(string $secret): string
    {
        $reflection = new ReflectionClass(Google2FAService::class);
        $method = $reflection->getMethod('getCode');
        $method->setAccessible(true);

        return $method->invoke(null, $secret, floor(time() / 30));
    }
}
