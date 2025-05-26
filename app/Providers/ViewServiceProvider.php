<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Add a global helper function to format sites list
        if (!function_exists('formatSitesList')) {
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
        
        // Add a blade directive to format the sites list
        Blade::directive('formatSitesList', function ($expression) {
            return "<?php echo formatSitesList($expression); ?>";
        });
        
        // Define the method to format sites list for stringable
        Blade::stringable(function ($value) {
            return $value instanceof \Illuminate\Database\Eloquent\Collection && 
                   isset($value->first()->name) ? 
                   formatSitesList($value) : null;
        });
    }
    
    /**
     * Format a collection of sites into a readable string.
     * 
     * @deprecated Use the global formatSitesList function instead
     * @param \Illuminate\Database\Eloquent\Collection $sites
     * @return string
     */
    protected function formatSitesList($sites)
    {
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
