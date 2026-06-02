<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_participant_id', 'question_id', 'answer_text', 'score', 'similarity_score'
    ];

    public function examParticipant()
    {
        return $this->belongsTo(ExamParticipant::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}