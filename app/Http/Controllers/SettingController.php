<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpsertSettingRequest;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function publicBranding()
    {
        $keys = ['center_name', 'logo_url'];
        $settings = Setting::whereIn('key', $keys)->pluck('value', 'key');
        $centerName = $settings->get('center_name') ?: 'Lancet - Medical Services';

        return response()->json([
            'center_name' => $centerName,
            'logo_url' => $settings->get('logo_url'),
        ]);
    }

    public function index(Request $request)
    {
        $group = $request->string('group')->toString() ?: 'general';
        $rows = Setting::where('group', $group)->get(['key', 'value']);

        $settings = [];
        foreach ($rows as $row) {
            $settings[$row->key] = $row->value;
        }

        return response()->json([
            'group' => $group,
            'settings' => $settings,
        ]);
    }

    public function store(UpsertSettingRequest $request)
    {
        $group = $request->input('group');
        $settings = $request->input('settings', []);

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['group' => $group, 'value' => $value]
            );
        }

        return response()->json(['message' => 'Settings saved']);
    }

    public function uploadLogo(Request $request)
    {
        $data = $request->validate([
            'logo' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
        ]);

        $path = $data['logo']->store('settings', 'public');
        $url = '/storage/'.$path;

        Setting::updateOrCreate(
            ['key' => 'logo_path'],
            ['group' => 'general', 'value' => $path]
        );

        Setting::updateOrCreate(
            ['key' => 'logo_url'],
            ['group' => 'general', 'value' => $url]
        );

        return response()->json([
            'message' => 'Logo uploaded',
            'logo_url' => $url,
        ]);
    }
}
