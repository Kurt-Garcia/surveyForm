<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer extends Model
{
    protected $table = 'TBLCUSTOMER';

    protected $fillable = [
        'site_id',
        'MDCODE',
        'CUSTCODE',
        'CUSTNAME',
        'CONTACTCELLNUMBER',
        'CONTACTPERSON',
        'CONTACTLANDLINE',
        'ADDRESS',
        'FREQUENCYCATEGORY',
        'MCPDAY',
        'MCPSCHEDULE',
        'GEOLOCATION',
        'LASTUPDATED',
        'LASTPURCHASE',
        'LATITUDE',
        'LONGITUDE',
        'STOREIMAGE',
        'SYNCSTAT',
        'DATES_TAMP',
        'TIME_STAMP',
        'ISLOCKON',
        'PRICECODE',
        'STOREIMAGE2',
        'CUSTTYPE',
        'ISVISIT',
        'DEFAULTORDTYPE',
        'CITYMUNCODE',
        'REGION',
        'PROVINCE',
        'MUNICIPALITY',
        'BARANGAY',
        'AREA',
        'WAREHOUSE',
        'KASOSYO',
        'EMAIL'
    ];

    protected $casts = [
        'ISLOCKON' => 'boolean',
        'LATITUDE' => 'decimal:6',
        'LONGITUDE' => 'decimal:6',
        'LASTUPDATED' => 'integer',
        'LASTPURCHASE' => 'integer',
        'FREQUENCYCATEGORY' => 'integer',
        'MCPDAY' => 'integer',
        'SYNCSTAT' => 'integer',
        'PRICECODE' => 'integer'
    ];

    /**
     * Get the site that owns the customer.
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Get the customer's full address formatted
     */
    public function getFormattedAddressAttribute(): string
    {
        $parts = array_filter([
            $this->ADDRESS,
            $this->BARANGAY,
            $this->MUNICIPALITY,
            $this->PROVINCE
        ]);
        
        return implode(', ', $parts);
    }

    /**
     * Scope for filtering by site
     */
    public function scopeBySite($query, $siteId)
    {
        return $query->where('site_id', $siteId);
    }

    /**
     * Scope for filtering by region
     */
    public function scopeByRegion($query, $region)
    {
        return $query->where('REGION', $region);
    }

    /**
     * Scope for filtering by province
     */
    public function scopeByProvince($query, $province)
    {
        return $query->where('PROVINCE', $province);
    }
}
