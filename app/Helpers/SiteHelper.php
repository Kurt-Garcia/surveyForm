<?php

if (!function_exists('formatSitesList')) {
    /**
     * Format a collection of sites into a readable string with tooltip for overflow.
     *
     * @param \Illuminate\Database\Eloquent\Collection $sites
     * @param int $maxVisible Maximum number of sites to show before truncating
     * @return string
     */
    function formatSitesList($sites, $maxVisible = 1) {
        if ($sites->isEmpty()) {
            return 'No sites';
        }
        
        if ($sites->count() == 1) {
            return 'Deployed to: ' . $sites->first()->name;
        }
        
        // Group by main sites first
        $mainSites = $sites->where('is_main', true)->pluck('name')->toArray();
        $otherSites = $sites->where('is_main', false)->pluck('name')->toArray();
        
        // Combine all sites for counting, prioritizing main sites first
        $allSites = array_merge($mainSites, $otherSites);
        $totalSites = count($allSites);
        
        // For multiple sites, always show only the first site with +N More
        $visibleSites = array_slice($allSites, 0, $maxVisible);
        $remainingCount = $totalSites - $maxVisible;
        
        // Create tooltip content with all remaining sites
        $remainingSites = array_slice($allSites, $maxVisible);
        $tooltipContent = 'Additional sites: ' . implode(', ', $remainingSites);
        
        return 'Deployed to: ' . implode(', ', $visibleSites) . 
               ' <span class="sites-more-indicator" data-bs-toggle="tooltip" data-bs-placement="top" ' .
               'data-bs-title="' . htmlspecialchars($tooltipContent, ENT_QUOTES, 'UTF-8') . '" ' .
               'style="color: #007bff; cursor: pointer; text-decoration: underline; font-weight: 500;">+' . 
               $remainingCount . ' More</span>';
    }
}
