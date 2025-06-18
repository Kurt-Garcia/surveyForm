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
        // Register the SiteHelper
        require_once app_path('Helpers/SiteHelper.php');
        
        // Add a blade directive to format the sites list
        Blade::directive('formatSitesList', function ($expression) {
            return "<?php echo formatSitesList($expression); ?>";
        });
        
        // Add a blade directive to format the sites list with HTML output
        Blade::directive('formatSitesListHtml', function ($expression) {
            return "<?php echo formatSitesList($expression); ?>";
        });
        
        // Define the method to format sites list for stringable
        Blade::stringable(function (\Illuminate\Support\Stringable $value) {
            return $value instanceof \Illuminate\Database\Eloquent\Collection && 
                   isset($value->first()->name) ? 
                   formatSitesList($value) : null;
        });
    }
}
