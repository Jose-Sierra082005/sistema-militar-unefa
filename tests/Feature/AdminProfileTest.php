<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProfileTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        return User::create([
            'name' => 'Comandante Sierra',
            'email' => 'admin@unefa.edu.ve',
            'password' => 'Secret123!',
            'role' => 'admin',
        ]);
    }

    private function createStudent(): User
    {
        return User::create([
            'name' => 'Cadete Rodríguez',
            'email' => 'student@unefa.edu.ve',
            'password' => 'Student123!',
            'role' => 'student',
        ]);
    }

    public function test_guest_cannot_access_admin_profile(): void
    {
        $response = $this->get(route('admin.profile.show'));
        $response->assertRedirect(route('login'));
    }

    public function test_student_cannot_access_admin_profile(): void
    {
        $student = $this->createStudent();
        $response = $this->actingAs($student)->get(route('admin.profile.show'));
        $response->assertStatus(403);
    }

    public function test_admin_can_access_admin_profile(): void
    {
        $admin = $this->createAdmin();
        $response = $this->actingAs($admin)->get(route('admin.profile.show'));
        $response->assertStatus(200);
        $response->assertSee('Ajustes del Administrador');
        $response->assertSee('admin@unefa.edu.ve');
    }

    public function test_admin_can_update_profile_info(): void
    {
        $admin = $this->createAdmin();
        $response = $this->actingAs($admin)->post(route('admin.profile.update'), [
            'name' => 'Comandante Sierra Editado',
            'email' => 'sierra_new@unefa.edu.ve',
            'cedula' => '31149881',
        ]);

        $response->assertRedirect(route('admin.profile.show'));
        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'name' => 'Comandante Sierra Editado',
            'email' => 'sierra_new@unefa.edu.ve',
            'cedula' => '31149881',
        ]);
    }

    public function test_admin_cannot_update_profile_with_existing_email(): void
    {
        $admin = $this->createAdmin();
        $otherStudent = $this->createStudent();

        $response = $this->actingAs($admin)->post(route('admin.profile.update'), [
            'name' => 'Comandante Sierra',
            'email' => $otherStudent->email,
        ]);

        $response->assertSessionHasErrors('email');
    }
}
