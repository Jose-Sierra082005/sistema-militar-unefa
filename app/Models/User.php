<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'google_id', 'google_token', 'google_refresh_token', 'cedula', 'two_factor_secret', 'two_factor_enabled', 'role', 'points'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_enabled' => 'boolean',
        ];
    }

    public function lessonCompletions()
    {
        return $this->hasMany(LessonCompletion::class);
    }

    /**
     * Normaliza la cédula de identidad limpiando puntos, guiones, espacios y letras.
     */
    public static function normalizeCedula(?string $cedula): string
    {
        if (empty($cedula)) {
            return '';
        }
        $clean = str_replace(['.', '-', ' '], '', $cedula);

        return preg_replace('/^[VEve]/', '', $clean);
    }

    /**
     * Obtiene el rango militar del usuario basado en sus puntos de experiencia (XP).
     */
    public function getRankAttribute(): string
    {
        $pts = $this->points ?? 0;
        if ($pts >= 500) {
            return 'General Académico';
        }
        if ($pts >= 300) {
            return 'Teniente Académico';
        }
        if ($pts >= 150) {
            return 'Sargento Académico';
        }
        if ($pts >= 50) {
            return 'Distinguido';
        }

        return 'Cadete';
    }
}
