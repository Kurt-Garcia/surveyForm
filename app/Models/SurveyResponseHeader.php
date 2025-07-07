<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class SurveyResponseHeader extends Model
{
    protected $table = 'survey_response_headers';
    
    protected $fillable = [
        'survey_id',
        'admin_id',
        'user_site_id',
        'account_name',
        'account_type',
        'date',
        'start_time',
        'end_time',
        'recommendation',
        'allow_resubmit'
    ];

    protected $casts = [
        'date' => 'datetime:Y-m-d H:i:s',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'recommendation' => 'integer',
        'allow_resubmit' => 'boolean'
    ];

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function userSite(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'user_site_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(SurveyResponseDetail::class, 'header_id');
    }
    
    public function improvementAreas(): HasMany
    {
        return $this->hasMany(SurveyImprovementArea::class, 'header_id');
    }

    public static function hasResponded($surveyId, $accountName): bool
    {
        return static::where('survey_id', $surveyId)
                    ->where('account_name', $accountName)
                    ->where('allow_resubmit', false)
                    ->exists();
    }
}