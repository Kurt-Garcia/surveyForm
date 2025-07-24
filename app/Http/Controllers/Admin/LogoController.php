<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Logo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class LogoController extends Controller
{
    public function index()
    {
        $logos = Logo::orderByDesc('is_active')->orderBy('id')->get();
        return view('admin.logos.index', compact('logos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'name' => 'nullable|string|max:255',
        ]);

        $path = $request->file('logo')->store('logos', 'public');

        $logo = Logo::create([
            'file_path' => $path,
            'name' => $request->name,
            'is_active' => false,
        ]);

        // Log activity for logo upload
        activity()
            ->causedBy(Auth::guard('admin')->user())
            ->withProperties([
                'logo_id' => $logo->id,
                'logo_name' => $logo->name,
                'file_path' => $logo->file_path
            ])
            ->event('uploaded')
            ->log('New logo named ' . ($logo->name ?: 'ID: ' . $logo->id) . ' has been uploaded');

        return redirect()->route('admin.logos.index')->with('success', 'Logo uploaded successfully.');
    }

    public function activate($id)
    {
        Logo::query()->update(['is_active' => false]);
        $logo = Logo::findOrFail($id);
        $logo->is_active = true;
        $logo->save();

        // Log activity for logo activation
        activity()
            ->causedBy(Auth::guard('admin')->user())
            ->withProperties([
                'logo_id' => $logo->id,
                'logo_name' => $logo->name
            ])
            ->event('activated')
            ->log('Logo ' . ($logo->name ?: 'ID: ' . $logo->id) . ' has been activated');

        return redirect()->route('admin.logos.index')->with('success', 'Logo activated.');
    }

    public function destroy($id)
    {
        $logo = Logo::findOrFail($id);
        if ($logo->is_active) {
            return redirect()->route('admin.logos.index')->with('error', 'Cannot delete the active logo.');
        }
        
        // Store logo details before deletion for logging
        $logoName = $logo->name;
        $logoId = $logo->id;
        
        Storage::disk('public')->delete($logo->file_path);
        $logo->delete();
        
        // Log activity for logo deletion
        activity()
            ->causedBy(Auth::guard('admin')->user())
            ->withProperties([
                'logo_id' => $logoId,
                'logo_name' => $logoName
            ])
            ->event('deleted')
            ->log('Logo ' . ($logoName ?: 'ID: ' . $logoId) . ' has been deleted');
        
        return redirect()->route('admin.logos.index')->with('success', 'Logo deleted.');
    }
}