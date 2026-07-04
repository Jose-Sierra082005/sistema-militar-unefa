<?php

namespace Tests\Unit;

use App\Services\Google2FAService;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class Google2FAServiceTest extends TestCase
{
    public function test_verify_code_accepts_current_totp(): void
    {
        $secret = Google2FAService::generateSecretKey();

        $reflection = new ReflectionClass(Google2FAService::class);
        $method = $reflection->getMethod('getCode');
        $method->setAccessible(true);
        $code = $method->invoke(null, $secret, (int) floor(time() / 30));

        $this->assertTrue(Google2FAService::verifyCode($secret, $code));
    }

    public function test_qr_image_url_contains_decodable_otpauth_secret(): void
    {
        $url = Google2FAService::getQRCodeImageUrl('Cadete', 'test@unefa.edu.ve', 'RDNUMWPQPBVUXAA4');
        parse_str(parse_url($url, PHP_URL_QUERY), $query);

        $this->assertArrayHasKey('data', $query);
        $this->assertStringContainsString('secret=RDNUMWPQPBVUXAA4', urldecode($query['data']));
        $this->assertStringContainsString('otpauth://totp/', urldecode($query['data']));
    }

    public function test_normalize_code_keeps_six_digits(): void
    {
        $this->assertSame('050471', Google2FAService::normalizeCode(' 050 471 '));
        $this->assertSame('123456', Google2FAService::normalizeCode('123456'));
    }
}
