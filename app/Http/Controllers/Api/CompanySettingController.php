<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanySettingController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'company_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'logo' => 'nullable|image|max:10240',
        ]);

        $data = $request->only(['company_name', 'phone', 'address']);

        if ($request->hasFile('logo')) {
            if ($user->companySetting && $user->companySetting->logo_path) {
                Storage::disk('public')->delete($user->companySetting->logo_path);
            }

            $data['logo_path'] = $request->file('logo')->store('logos', 'public');
        }

        $setting = $user->companySetting->updateOrCreate([], $data);

        return response()->json([
            ...$setting->toArray(),
            'logo_url' => $setting->logo_path ? asset('storage/' . $setting->logo_path) : null,
        ]);
    }

    public function show()
    {
        $user = auth()->user();
        $setting = $user->companySetting;

        if (! $setting) {
            return response()->json(null, 404);
        }

        return response()->json([
            ...$setting->toArray(),
            'logo_url' => $setting->logo_path ? asset('storage/' . $setting->logo_path) : null,
        ]);
    }
}
