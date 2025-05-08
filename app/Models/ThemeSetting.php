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