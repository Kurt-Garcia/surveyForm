<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class ThemeSetting extends Model
{
    protected $table = 'theme_settings';
    
    protected $fillable = [
        'name',
        'admin_id',
        'is_active',
        'primary_color',
        'secondary_color',
        'accent_color',
        'background_color',
        'text_color',
        'heading_font',
        'body_font',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the admin that owns this theme
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * Get the active theme for a specific admin or global
     *
     * @param int|null $adminId
     * @return ThemeSetting|null
     */
    public static function getActiveTheme($adminId = null)
    {
        if ($adminId) {
            // First try to get admin-specific active theme
            $adminTheme = self::where('is_active', true)
                             ->where('admin_id', $adminId)
                             ->first();
            
            if ($adminTheme) {
                return $adminTheme;
            }
        }
        
        // Fallback to global theme (admin_id = null)
        return self::where('is_active', true)
                  ->whereNull('admin_id')
                  ->first();
    }

    /**
     * Set this theme as active and deactivate all others for the current admin
     */
    public function setAsActive()
    {
        // Begin transaction
        \Illuminate\Support\Facades\DB::beginTransaction();
        
        try {
            $admin = \Illuminate\Support\Facades\Auth::guard('admin')->user();
            
            if (!$admin) {
                throw new \Exception('No authenticated admin found');
            }
            
            // Deactivate ALL themes that could be active for this admin:
            // 1. Admin-specific themes created by this admin
            // 2. Global/default themes (admin_id is null)
            self::where('id', '!=', $this->id)
                ->where(function($query) use ($admin) {
                    $query->where('admin_id', $admin->id)  // Admin's own themes
                          ->orWhereNull('admin_id');        // Global themes
                })
                ->update(['is_active' => false]);
            
            // Activate this theme
            $this->is_active = true;
            $this->save();
            
            \Illuminate\Support\Facades\DB::commit();
            return true;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return false;
        }
    }

    /**
     * Generate CSS variables from theme settings
     */
    public function generateCssVariables()
    {
        // Helper function to convert hex to RGB
        $hexToRgb = function($hex) {
            $hex = ltrim($hex, '#');
            if (strlen($hex) == 3) {
                $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
            }
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
            return "$r, $g, $b";
        };

        return <<<CSS
        :root {
            --primary-color: {$this->primary_color};
            --secondary-color: {$this->secondary_color};
            --accent-color: {$this->accent_color};
            --background-color: {$this->background_color};
            --text-color: {$this->text_color};
            --heading-font: '{$this->heading_font}', sans-serif;
            --body-font: '{$this->body_font}', sans-serif;
            
            /* RGB versions for rgba() usage */
            --primary-color-rgb: {$hexToRgb($this->primary_color)};
            --secondary-color-rgb: {$hexToRgb($this->secondary_color)};
            --accent-color-rgb: {$hexToRgb($this->accent_color)};
            --background-color-rgb: {$hexToRgb($this->background_color)};
            --text-color-rgb: {$hexToRgb($this->text_color)};
            
            /* Derived variables */
            --primary-gradient: linear-gradient(135deg, {$this->primary_color}, {$this->secondary_color});
            --card-bg-color: #ffffff;
            --border-color: rgba(0, 0, 0, 0.125);
            --input-bg: #ffffff;
            --input-border: #ced4da;
            --input-focus-border: {$this->primary_color};
            --btn-primary-bg: {$this->primary_color};
            --btn-primary-color: #ffffff;
            --shadow-color: rgba(0, 0, 0, 0.08);
        }
        
        /* Dark mode adjustments */
        html.dark-mode {
            --card-bg-color: #1e1e1e;
            --border-color: rgba(255, 255, 255, 0.125);
            --input-bg: #2d2d2d;
            --input-border: #444444;
            --shadow-color: rgba(0, 0, 0, 0.3);
        }
CSS;
    }

    /**
     * Get available Google Fonts for selection
     */
    public static function getAvailableFonts()
    {
        return [
            // Microsoft Word & common system fonts
            'Arial' => 'Arial',
            'Arial Black' => 'Arial Black',
            'Calibri' => 'Calibri',
            'Cambria' => 'Cambria',
            'Candara' => 'Candara',
            'Comic Sans MS' => 'Comic Sans MS',
            'Consolas' => 'Consolas',
            'Constantia' => 'Constantia',
            'Corbel' => 'Corbel',
            'Courier New' => 'Courier New',
            'Garamond' => 'Garamond',
            'Georgia' => 'Georgia',
            'Impact' => 'Impact',
            'Lucida Console' => 'Lucida Console',
            'Lucida Sans Unicode' => 'Lucida Sans Unicode',
            'Palatino Linotype' => 'Palatino Linotype',
            'Segoe UI' => 'Segoe UI',
            'Tahoma' => 'Tahoma',
            'Times New Roman' => 'Times New Roman',
            'Trebuchet MS' => 'Trebuchet MS',
            'Verdana' => 'Verdana',
            // Google Fonts
            'Roboto' => 'Roboto',
            'Open Sans' => 'Open Sans',
            'Lato' => 'Lato',
            'Montserrat' => 'Montserrat',
            'Oswald' => 'Oswald',
            'Raleway' => 'Raleway',
            'Poppins' => 'Poppins',
            'Merriweather' => 'Merriweather',
            'Nunito' => 'Nunito',
            'Quicksand' => 'Quicksand',
            'Source Sans Pro' => 'Source Sans Pro',
            'PT Sans' => 'PT Sans',
            'Ubuntu' => 'Ubuntu',
            'Rubik' => 'Rubik',
            'Work Sans' => 'Work Sans',
            'Fira Sans' => 'Fira Sans',
            'Josefin Sans' => 'Josefin Sans',
            'Dancing Script' => 'Dancing Script',
            'Pacifico' => 'Pacifico',
            'Bebas Neue' => 'Bebas Neue',
            'Playfair Display' => 'Playfair Display',
            'Lobster' => 'Lobster',
            'Indie Flower' => 'Indie Flower',
        ];
    }
}