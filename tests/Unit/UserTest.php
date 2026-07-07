<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    /**
     * Verifica que normalizeCedula remueva puntos y guiones.
     */
    public function test_normalize_cedula_removes_dots_and_dashes(): void
    {
        $this->assertSame('31149881', User::normalizeCedula('31.149.881'));
        $this->assertSame('23456789', User::normalizeCedula('23-456-789'));
    }

    /**
     * Verifica que normalizeCedula maneje espacios y prefijos V/E.
     */
    public function test_normalize_cedula_removes_letters_and_spaces(): void
    {
        $this->assertSame('12345678', User::normalizeCedula(' v 12 345 678 '));
        $this->assertSame('98765432', User::normalizeCedula('E-98.765.432'));
        $this->assertSame('', User::normalizeCedula(''));
        $this->assertSame('', User::normalizeCedula(null));
    }

    /**
     * Verifica los rangos militares basados en XP.
     */
    public function test_rank_cadete_limit(): void
    {
        $user = new User;
        $user->points = 0;
        $this->assertSame('Cadete', $user->rank);

        $user->points = 49;
        $this->assertSame('Cadete', $user->rank);
    }

    public function test_rank_distinguido_limit(): void
    {
        $user = new User;
        $user->points = 50;
        $this->assertSame('Distinguido', $user->rank);

        $user->points = 149;
        $this->assertSame('Distinguido', $user->rank);
    }

    public function test_rank_sargento_academico_limit(): void
    {
        $user = new User;
        $user->points = 150;
        $this->assertSame('Sargento Académico', $user->rank);

        $user->points = 299;
        $this->assertSame('Sargento Académico', $user->rank);
    }

    public function test_rank_teniente_academico_limit(): void
    {
        $user = new User;
        $user->points = 300;
        $this->assertSame('Teniente Académico', $user->rank);

        $user->points = 499;
        $this->assertSame('Teniente Académico', $user->rank);
    }

    public function test_rank_general_academico_limit(): void
    {
        $user = new User;
        $user->points = 500;
        $this->assertSame('General Académico', $user->rank);

        $user->points = 1200;
        $this->assertSame('General Académico', $user->rank);
    }
}
