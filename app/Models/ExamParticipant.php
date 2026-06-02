<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id', 'siswa_id', 'status', 'is_ready', 'ready_at',
        'total_score', 'rank', 'started_at', 'finished_at'
    ];

    protected $casts = [
        'is_ready' => 'boolean',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'ready_at' => 'datetime',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function siswa()
    {
        return $this->belongsTo(User::class, 'siswa_id');
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}