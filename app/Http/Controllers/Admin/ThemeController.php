<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ThemeSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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
        $themes = ThemeSetting::all();
        return view('admin.themes.index', compact('themes'));
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
        $request->validate([
            'name' => 'required|string|max:255|unique:theme_settings,name',
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
            'primary_color' => $request->primary_color,
            'secondary_color' => $request->secondary_color,
            'accent_color' => $request->accent_color,
            'background_color' => $request->background_color,
            'text_color' => $request->text_color,
            'heading_font' => $request->heading_font,
            'body_font' => $request->body_font,
            'custom_css' => $request->custom_css,
        ]);

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
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('theme_settings', 'name')->ignore($theme->id),
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
        $theme->setAsActive();

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
        if ($theme->is_active) {
            return redirect()->route('admin.themes.index')
                ->with('error', 'You cannot delete the active theme.');
        }

        $theme->delete();

        return redirect()->route('admin.themes.index')
            ->with('success', 'Theme deleted successfully!');
    }
}