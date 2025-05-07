<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ThemeSetting extends Model
{
    protected $table = 'theme_settings';
    
    protected $fillable = [
        'name',
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
     * Get the active theme
     *
     * @return ThemeSetting|null
     */
    public static function getActiveTheme()
    {
        return self::where('is_active', true)->first();
    }

    /**
     * Set this theme as active and deactivate all others
     */
    public function setAsActive()
    {
        // Begin transaction
        \Illuminate\Support\Facades\DB::beginTransaction();
        
        try {
            // Deactivate all themes
            self::where('id', '!=', $this->id)->update(['is_active' => false]);
            
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
        return <<<CSS
        :root {
            --primary-color: {$this->primary_color};
            --secondary-color: {$this->secondary_color};
            --accent-color: {$this->accent_color};
            --background-color: {$this->background_color};
            --text-color: {$this->text_color};
            --heading-font: '{$this->heading_font}', sans-serif;
            --body-font: '{$this->body_font}', sans-serif;
            
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
            'Inter' => 'Inter',
            'Roboto' => 'Roboto',
            'Open Sans' => 'Open Sans',
            'Lato' => 'Lato',
            'Montserrat' => 'Montserrat',
            'Raleway' => 'Raleway',
            'Poppins' => 'Poppins',
            'Nunito' => 'Nunito',
            'Playfair Display' => 'Playfair Display',
            'Source Sans Pro' => 'Source Sans Pro',
            'Ubuntu' => 'Ubuntu',
        ];
    }
}