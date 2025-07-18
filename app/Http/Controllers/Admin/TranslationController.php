<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Translation;
use App\Models\TranslationHeader;
use App\Services\TranslationService;

class TranslationController extends Controller
{
    protected $translationService;

    public function __construct(TranslationService $translationService)
    {
        $this->translationService = $translationService;
    }

    /**
     * Display a listing of translations
     */
    public function index(Request $request)
    {
        $locale = $request->get('locale', '');
        $search = $request->get('search', '');
        
        $query = Translation::query();
        
        // Filter by locale
        if ($locale) {
            $query->whereHas('translationHeader', function($q) use ($locale) {
                $q->where('locale', $locale);
            });
        }
        
        // Search in key or value
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('key', 'like', "%{$search}%")
                  ->orWhere('value', 'like', "%{$search}%");
            });
        }
        
        $translations = $query->with('translationHeader')->orderBy('key')->paginate(20);
        
        $locales = TranslationHeader::getLocaleOptions();
        
        return view('admin.translations.index', compact('translations', 'locales', 'locale', 'search'));
    }

    /**
     * Show the form for creating a new translation
     */
    public function create()
    {
        $locales = TranslationHeader::getLocaleOptions();
        
        return view('admin.translations.create', compact('locales'));
    }

    /**
     * Store a newly created translation
     */
    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string|max:255',
            'locale' => 'required|string|max:5',
            'value' => 'required|string'
        ]);
        
        // Get translation header
        $translationHeader = TranslationHeader::where('locale', $request->locale)->first();
        if (!$translationHeader) {
            return redirect()->back()->withErrors(['locale' => 'Invalid locale selected.']);
        }
        
        // Check if translation already exists
        if (Translation::where('key', $request->key)
                      ->where('translation_header_id', $translationHeader->id)
                      ->exists()) {
            return redirect()->back()->withErrors(['key' => 'Translation already exists for this key and locale.']);
        }
        
        Translation::create([
            'key' => $request->key,
            'translation_header_id' => $translationHeader->id,
            'value' => $request->value
        ]);
        
        // Clear cache
        $this->translationService->clearCache();
        
        return redirect()->route('developer.translations.index')->with('success', 'Translation created successfully.');
    }

    /**
     * Show the form for editing the specified translation
     */
    public function edit(Translation $translation)
    {
        $translation->load('translationHeader');
        $locales = TranslationHeader::getLocaleOptions();
        
        return view('admin.translations.edit', compact('translation', 'locales'));
    }

    /**
     * Update the specified translation
     */
    public function update(Request $request, Translation $translation)
    {
        $request->validate([
            'key' => 'required|string|max:255',
            'locale' => 'required|string|max:5',
            'value' => 'required|string'
        ]);
        
        // Get translation header
        $translationHeader = TranslationHeader::where('locale', $request->locale)->first();
        if (!$translationHeader) {
            return redirect()->back()->withErrors(['locale' => 'Invalid locale selected.']);
        }
        
        // Check if translation already exists (excluding current one)
        if (Translation::where('key', $request->key)
                      ->where('translation_header_id', $translationHeader->id)
                      ->where('id', '!=', $translation->id)
                      ->exists()) {
            return redirect()->back()->withErrors(['key' => 'Translation already exists for this key and locale.']);
        }
        
        $translation->update([
            'key' => $request->key,
            'translation_header_id' => $translationHeader->id,
            'value' => $request->value
        ]);
        
        // Clear cache
        $this->translationService->clearCache();
        
        return redirect()->route('developer.translations.index')->with('success', 'Translation updated successfully.');
    }

    /**
     * Remove the specified translation
     */
    public function destroy(Translation $translation)
    {
        $translation->delete();
        
        // Clear cache
        $this->translationService->clearCache();
        
        return redirect()->route('developer.translations.index')->with('success', 'Translation deleted successfully.');
    }

    /**
     * Clear translation cache
     */
    public function clearCache()
    {
        $this->translationService->clearCache();
        
        return redirect()->route('developer.translations.index')->with('success', 'Translation cache cleared successfully.');
    }

    /**
     * Export translations to language files
     */
    public function export()
    {
        $locales = TranslationHeader::getLocaleOptions();
        
        foreach ($locales as $locale => $name) {
            $translations = Translation::whereHas('translationHeader', function($q) use ($locale) {
                $q->where('locale', $locale);
            })->get();
            
            $filePath = resource_path("lang/{$locale}/messages.php");
            
            // Ensure directory exists
            $directory = dirname($filePath);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
            
            // Build array
            $array = [];
            foreach ($translations as $translation) {
                $array[$translation->key] = $translation->value;
            }
            
            // Write file
            $content = "<?php\n\nreturn " . var_export($array, true) . ";\n";
            file_put_contents($filePath, $content);
        }
        
        return redirect()->route('developer.translations.index')->with('success', 'Translations exported to language files successfully.');
    }

    /**
     * Add a new language
     */
    public function addLanguage(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'locale' => 'required|string|max:5|unique:translation_headers,locale'
        ]);
        
        TranslationHeader::create([
            'name' => $request->name,
            'locale' => strtolower($request->locale),
            'is_active' => false
        ]);
        
        return redirect()->route('developer.translations.index')->with('success', 'Language added successfully.');
    }
    
    /**
     * Deploy selected languages (activate exactly 3 languages including English)
     */
    public function deployLanguages(Request $request)
    {
        $request->validate([
            'languages' => 'required|array|max:3',
            'languages.*' => 'exists:translation_headers,id'
        ]);
        
        // Get English language header
        $englishHeader = TranslationHeader::where('locale', 'en')->first();
        if (!$englishHeader) {
            return redirect()->back()->withErrors(['languages' => 'English language not found. Please ensure English is configured.']);
        }
        
        // Ensure English is always included in the selected languages
        $selectedLanguages = collect($request->languages);
        if (!$selectedLanguages->contains($englishHeader->id)) {
            $selectedLanguages->push($englishHeader->id);
        }
        
        // Validate that we have exactly 3 languages
        if ($selectedLanguages->count() !== 3) {
            return redirect()->back()->withErrors(['languages' => 'Please select exactly 2 additional languages (English is always included).']);
        }
        
        // Deactivate all languages first
        TranslationHeader::query()->update(['is_active' => false]);
        
        // Activate selected languages (including English)
        TranslationHeader::whereIn('id', $selectedLanguages->toArray())->update(['is_active' => true]);
        
        // Clear translation cache
        $this->translationService->clearCache();
        
        return redirect()->route('admin.translations.index')->with('success', 'Languages deployed successfully. Selected languages are now active.');
    }
}
