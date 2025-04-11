<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyForm extends Model
{
    protected $fillable = [
        'survey_id',
        'user_id',
        'accountName',
        'accountType',
        'submission_date',
        'status',
        'recommendation',
        'comments'
    ];

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function responses()
    {
        return $this->hasMany(SurveyResponse::class);
    }
}
