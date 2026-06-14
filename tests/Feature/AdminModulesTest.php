<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\MilitaryPersonnel;
use App\Models\Weapon;
use App\Models\GuardShift;
use App\Models\Evaluation;

class AdminModulesTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        return User::create([
            'name' => 'Comandante Sierra',
            'email' => 'sierra@unefa.edu.ve',
            'password' => bcrypt('Secret123!'),
        ]);
    }

    private function createMilitaryPersonnel(): MilitaryPersonnel
    {
        return MilitaryPersonnel::create([
            'name' => 'Teniente José Sierra',
            'cedula' => '31149881',
            'rank' => 'Teniente',
            'role' => 'Oficial de Comando',
            'status' => 'Activo',
            'phone' => '0412-1234567',
            'email' => 'jose@unefa.edu.ve',
        ]);
    }

    public function test_unauthenticated_user_cannot_access_admin_modules(): void
    {
        $response = $this->get(route('admin.armory.index'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('admin.guards.index'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('admin.evaluations.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_manage_armory(): void
    {
        $user = $this->createAdmin();
        $personnel = $this->createMilitaryPersonnel();

        // 1. Get index
        $response = $this->actingAs($user)->get(route('admin.armory.index'));
        $response->assertStatus(200);

        // 2. Get create
        $response = $this->actingAs($user)->get(route('admin.armory.create'));
        $response->assertStatus(200);

        // 3. Post store
        $response = $this->actingAs($user)->post(route('admin.armory.store'), [
            'serial' => 'UNEFA-AK103-001',
            'type' => 'Fusil de Asalto',
            'model' => 'AK-103',
            'condition' => 'Excelente',
            'status' => 'Asignado',
            'assigned_to' => $personnel->id,
        ]);

        $response->assertRedirect(route('admin.armory.index'));
        $this->assertDatabaseHas('weapons', [
            'serial' => 'UNEFA-AK103-001',
            'assigned_to' => $personnel->id,
        ]);
    }

    public function test_admin_can_manage_guard_shifts(): void
    {
        $user = $this->createAdmin();
        $personnel = $this->createMilitaryPersonnel();

        // 1. Get index
        $response = $this->actingAs($user)->get(route('admin.guards.index'));
        $response->assertStatus(200);

        // 2. Post store
        $response = $this->actingAs($user)->post(route('admin.guards.store'), [
            'personnel_id' => $personnel->id,
            'post' => 'Garita Principal (Acceso)',
            'shift_time' => 'Turno Alpha: 00:00 - 06:00',
            'date' => '2026-06-14',
            'status' => 'Programado',
        ]);

        $response->assertRedirect(route('admin.guards.index'));
        $this->assertDatabaseHas('guard_shifts', [
            'personnel_id' => $personnel->id,
            'post' => 'Garita Principal (Acceso)',
        ]);
    }

    public function test_admin_can_manage_evaluations(): void
    {
        $user = $this->createAdmin();
        $personnel = $this->createMilitaryPersonnel();

        // 1. Get index
        $response = $this->actingAs($user)->get(route('admin.evaluations.index'));
        $response->assertStatus(200);

        // 2. Post store
        $response = $this->actingAs($user)->post(route('admin.evaluations.store'), [
            'personnel_id' => $personnel->id,
            'course_name' => 'Tiro de Precisión (AK-103)',
            'score' => 19,
            'evaluator' => 'Cnel. José Sierra',
            'date' => '2026-06-14',
            'comments' => 'Agrupación excelente.',
        ]);

        $response->assertRedirect(route('admin.evaluations.index'));
        $this->assertDatabaseHas('evaluations', [
            'personnel_id' => $personnel->id,
            'course_name' => 'Tiro de Precisión (AK-103)',
            'score' => 19,
        ]);
    }
}
