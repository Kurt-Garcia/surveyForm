<?php

if (!function_exists('formatSitesList')) {
    /**
     * Format a collection of sites into a readable string.
     *
     * @param \Illuminate\Database\Eloquent\Collection $sites
     * @return string
     */
    function formatSitesList($sites) {
        if ($sites->isEmpty()) {
            return 'No sites';
        }
        
        if ($sites->count() == 1) {
            return 'Deployed to: ' . $sites->first()->name;
        }
        
        // Group by main sites first
        $mainSites = $sites->where('is_main', true)->pluck('name')->toArray();
        $otherSites = $sites->where('is_main', false)->pluck('name')->toArray();
        
        $sitesList = [];
        
        if (!empty($mainSites)) {
            $sitesList[] = count($mainSites) == 1 ? 
                'Main site: ' . implode(', ', $mainSites) : 
                'Main sites: ' . implode(', ', $mainSites);
        }
        
        if (!empty($otherSites)) {
            $sitesList[] = count($otherSites) == 1 ? 
                'Other site: ' . implode(', ', $otherSites) : 
                'Other sites: ' . implode(', ', $otherSites);
        }
        
        return implode(' | ', $sitesList);
    }
}
