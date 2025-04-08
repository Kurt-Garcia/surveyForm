<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyForm extends Model
{
    protected $fillable = [
        'accountName',
        'accountType',
        'date',
        'Q1',
        'Q2',
        'Q3',
        'Q4',
        'Q5',
        'Q6',
        'surveyRating',
        'comments'
    ];
}
