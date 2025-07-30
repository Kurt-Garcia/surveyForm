<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ThemeSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ThemeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Display a listing of themes
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        
        // Get themes created by the current admin AND global default themes
        $themes = ThemeSetting::where(function($query) use ($admin) {
            $query->where('admin_id', $admin->id)  // Admin-specific themes
                  ->orWhereNull('admin_id');         // Global default themes
        })->orderBy('admin_id', 'asc')              // Show global themes first
          ->orderBy('created_at', 'asc')
          ->get();
        
        // Get the active theme for this admin (could be default or custom)
        $activeTheme = ThemeSetting::getActiveTheme($admin->id);
        
        return view('admin.themes.index', compact('themes', 'activeTheme'));
    }

    /**
     * Show the form for creating a new theme
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $fonts = ThemeSetting::getAvailableFonts();
        return view('admin.themes.create', compact('fonts'));
    }

    /**
     * Store a newly created theme
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                // Theme names must be unique per admin (composite unique constraint)
                Rule::unique('theme_settings', 'name')->where('admin_id', $admin->id),
            ],
            'primary_color' => 'required|string|max:7',
            'secondary_color' => 'required|string|max:7',
            'accent_color' => 'required|string|max:7',
            'background_color' => 'required|string|max:7',
            'text_color' => 'required|string|max:7',
            'heading_font' => 'required|string|max:100',
            'body_font' => 'required|string|max:100',
            'custom_css' => 'nullable|string|max:10000',
        ]);

        $theme = ThemeSetting::create([
            'name' => $request->name,
            'admin_id' => $admin->id, // Associate theme with current admin
            'primary_color' => $request->primary_color,
            'secondary_color' => $request->secondary_color,
            'accent_color' => $request->accent_color,
            'background_color' => $request->background_color,
            'text_color' => $request->text_color,
            'heading_font' => $request->heading_font,
            'body_font' => $request->body_font,
            'custom_css' => $request->custom_css,
        ]);

        // Log activity for theme creation
        activity()
            ->causedBy($admin)
            ->withProperties([
                'theme_id' => $theme->id,
                'theme_name' => $theme->name,
                'primary_color' => $theme->primary_color,
                'secondary_color' => $theme->secondary_color,
                'accent_color' => $theme->accent_color,
                'background_color' => $theme->background_color,
                'text_color' => $theme->text_color,
                'heading_font' => $theme->heading_font,
                'body_font' => $theme->body_font
            ])
            ->event('created')
            ->log('New theme named ' . $theme->name . ' was created');

        if ($request->has('activate') && $request->activate) {
            $theme->setAsActive();
        }

        return redirect()->route('admin.themes.index')
            ->with('success', 'Theme created successfully!');
    }

    /**
     * Show the form for editing the theme
     *
     * @param  ThemeSetting  $theme
     * @return \Illuminate\View\View
     */
    public function edit(ThemeSetting $theme)
    {
        $admin = Auth::guard('admin')->user();
        
        // Prevent editing global/default themes
        if ($theme->admin_id === null) {
            return redirect()->route('admin.themes.index')
                ->with('error', 'Default themes cannot be edited. Please create a new theme instead.');
        }
        
        // Ensure the theme belongs to the current admin
        if ($theme->admin_id !== $admin->id) {
            abort(403, 'Unauthorized action.');
        }
        
        $fonts = ThemeSetting::getAvailableFonts();
        return view('admin.themes.edit', compact('theme', 'fonts'));
    }

    /**
     * Update the theme
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  ThemeSetting  $theme
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, ThemeSetting $theme)
    {
        $admin = Auth::guard('admin')->user();
        
        // Prevent updating global/default themes
        if ($theme->admin_id === null) {
            return redirect()->route('admin.themes.index')
                ->with('error', 'Default themes cannot be updated. Please create a new theme instead.');
        }
        
        // Ensure the theme belongs to the current admin
        if ($theme->admin_id !== $admin->id) {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('theme_settings', 'name')
                    ->ignore($theme->id)
                    ->where('admin_id', $admin->id),
            ],
            'primary_color' => 'required|string|max:7',
            'secondary_color' => 'required|string|max:7',
            'accent_color' => 'required|string|max:7',
            'background_color' => 'required|string|max:7',
            'text_color' => 'required|string|max:7',
            'heading_font' => 'required|string|max:100',
            'body_font' => 'required|string|max:100',
            'custom_css' => 'nullable|string|max:10000',
        ]);

        $theme->update([
            'name' => $request->name,
            'primary_color' => $request->primary_color,
            'secondary_color' => $request->secondary_color,
            'accent_color' => $request->accent_color,
            'background_color' => $request->background_color,
            'text_color' => $request->text_color,
            'heading_font' => $request->heading_font,
            'body_font' => $request->body_font,
            'custom_css' => $request->custom_css,
        ]);

        // Log activity for theme update
        activity()
            ->causedBy($admin)
            ->withProperties([
                'theme_id' => $theme->id,
                'theme_name' => $theme->name,
                'primary_color' => $theme->primary_color,
                'secondary_color' => $theme->secondary_color,
                'accent_color' => $theme->accent_color,
                'background_color' => $theme->background_color,
                'text_color' => $theme->text_color,
                'heading_font' => $theme->heading_font,
                'body_font' => $theme->body_font
            ])
            ->event('updated')
            ->log('Theme ' . $theme->name . ' has been updated');

        if ($request->has('activate') && $request->activate) {
            $theme->setAsActive();
        }

        return redirect()->route('admin.themes.index')
            ->with('success', 'Theme updated successfully!');
    }

    /**
     * Activate the theme
     *
     * @param  ThemeSetting  $theme
     * @return \Illuminate\Http\RedirectResponse
     */
    public function activate(ThemeSetting $theme)
    {
        $admin = Auth::guard('admin')->user();
        
        // Allow activation of global themes OR admin-specific themes
        if ($theme->admin_id !== null && $theme->admin_id !== $admin->id) {
            abort(403, 'Unauthorized action.');
        }
        
        $theme->setAsActive();

        // Log activity for theme activation
        activity()
            ->causedBy($admin)
            ->withProperties([
                'theme_id' => $theme->id,
                'theme_name' => $theme->name
            ])
            ->event('activated')
            ->log('Theme ' . $theme->name . ' has been activated');

        return redirect()->route('admin.themes.index')
            ->with('success', 'Theme has been activated successfully!');
    }

    /**
     * Remove the theme
     *
     * @param  ThemeSetting  $theme
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(ThemeSetting $theme)
    {
        $admin = Auth::guard('admin')->user();
        
        // Prevent deleting global/default themes
        if ($theme->admin_id === null) {
            return redirect()->route('admin.themes.index')
                ->with('error', 'Default themes cannot be deleted.');
        }
        
        // Ensure the theme belongs to the current admin
        if ($theme->admin_id !== $admin->id) {
            abort(403, 'Unauthorized action.');
        }
        
        if ($theme->is_active) {
            return redirect()->route('admin.themes.index')
                ->with('error', 'You cannot delete the active theme.');
        }

        // Log activity for theme deletion
        activity()
            ->causedBy($admin)
            ->withProperties([
                'theme_id' => $theme->id,
                'theme_name' => $theme->name
            ])
            ->event('deleted')
            ->log('Theme ' . $theme->name . ' has been deleted');

        $theme->delete();

        return redirect()->route('admin.themes.index')
            ->with('success', 'Theme deleted successfully!');
    }
}