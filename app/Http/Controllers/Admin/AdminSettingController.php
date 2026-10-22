<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminSettingController extends Controller
{
    /**
     * Display settings page
     */
    public function index()
    {
        $settings = [
            'app_name' => config('app.name', 'ParkSafe'),
            'app_version' => 'v1.0.0',
            'parking_location' => 'Gedung FILKOM UB, Jl. Veteran No. 8, Malang',
            'language' => 'Bahasa Indonesia',
            'timezone' => 'WIB (GMT+7)',
            'date_format' => 'DD MMM YYYY',
            'theme_mode' => 'Gelap',
            'sound_notification' => true,
            'auto_logout' => '30 Menit',
        ];

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Save system settings
     */
    public function update(Request $request)
    {
        // Store in session or return success
        return back()->with('success', 'Pengaturan sistem ParkSafe berhasil diperbarui.');
    }
}
