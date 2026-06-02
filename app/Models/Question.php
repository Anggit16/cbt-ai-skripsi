<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'type',
        'question_text',
        'image',
        'options',
        'option_images',
        'correct_answer',
        'score'
    ];

    protected $casts = [
        'options' => 'array',
        'option_images' => 'array'
    ];

    // Helper untuk mendapatkan URL gambar soal
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return null;
    }

    // Helper untuk mendapatkan URL gambar opsi tertentu
    public function getOptionImageUrl($index)
    {
        if ($this->option_images && isset($this->option_images[$index])) {
            return asset('storage/' . $this->option_images[$index]);
        }
        return null;
    }

    // Relasi ke exam
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    // Relasi ke answers
    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}