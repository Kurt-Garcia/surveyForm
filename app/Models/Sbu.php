<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sbu extends Model
{
    protected $fillable = ['name'];

    public function sites(): HasMany
    {
        return $this->hasMany(Site::class);
    }

    public function mainSites()
    {
        return $this->sites()->where('is_main', true);
    }

    public function subSites()
    {
        return $this->sites()->where('is_main', false);
    }
}