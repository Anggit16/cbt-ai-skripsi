<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'mata_pelajaran',
        'kelas',
        'jurusan',
        'nomor_absen',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // TAMBAHKAN 3 METHOD INI
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isGuru(): bool
    {
        return $this->role === 'guru';
    }

    public function isSiswa(): bool
    {
        return $this->role === 'siswa';
    }

    // Relasi
    public function exams()
    {
        return $this->hasMany(Exam::class, 'guru_id');
    }

    public function examParticipants()
    {
        return $this->hasMany(ExamParticipant::class, 'siswa_id');
    }
}