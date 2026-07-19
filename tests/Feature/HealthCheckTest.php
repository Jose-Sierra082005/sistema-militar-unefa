<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    /**
     * Verificar que el endpoint de salud (/health) responda exitosamente.
     */
    public function test_health_check_returns_ok_status(): void
    {
        $response = $this->get('/health');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'timestamp',
            'services' => [
                'database' => [
                    'status',
                    'driver',
                ],
                'storage' => [
                    'status',
                    'writable',
                ],
                'server' => [
                    'php_version',
                    'environment',
                    'timezone',
                    'timestamp',
                ],
            ],
        ]);

        $response->assertJsonFragment([
            'status' => 'OK',
        ]);
    }
}
