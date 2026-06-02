<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'guru_id',
        'judul',
        'deskripsi',
        'durasi',
        'jumlah_peserta',
        'kode_ujian',
        'status',
        'acak_soal',
        'tampilkan_hasil'
    ];

    protected $casts = [
        'acak_soal' => 'boolean',
        'tampilkan_hasil' => 'boolean',
    ];

    // Relasi ke guru
    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    // Relasi ke soal
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    // Relasi ke peserta
    public function participants()
    {
        return $this->hasMany(ExamParticipant::class);
    }
}