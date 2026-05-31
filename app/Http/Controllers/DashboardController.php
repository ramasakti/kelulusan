<?php

namespace App\Http\Controllers;

use App\Models\Mapel;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(): InertiaResponse
    {
        $mapel = Mapel::orderBy('nama_mapel')->get();
        $siswa = Siswa::with(['nilai.mapel', 'tka'])
            ->orderBy('nama_siswa')
            ->get();

        $settingsPath = storage_path('app/settings/range.json');
        $settings = [
            'start' => now()->addDay()->format('Y-m-d\TH:i'),
            'end' => now()->addDays(5)->format('Y-m-d\TH:i'),
        ];

        if (file_exists($settingsPath)) {
            $settings = json_decode(file_get_contents($settingsPath), true);
        } else {
            if (!file_exists(dirname($settingsPath))) {
                mkdir(dirname($settingsPath), 0755, true);
            }
            file_put_contents($settingsPath, json_encode($settings, JSON_PRETTY_PRINT));
        }

        return Inertia::render('dashboard', [
            'mapel' => $mapel,
            'siswa' => $siswa,
            'settings' => $settings,
        ]);
    }

    /**
     * Update countdown time range settings.
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'start' => 'required|date',
            'end' => 'required|date|after:start',
        ]);

        $settingsPath = storage_path('app/settings/range.json');
        
        if (!file_exists(dirname($settingsPath))) {
            mkdir(dirname($settingsPath), 0755, true);
        }

        file_put_contents($settingsPath, json_encode([
            'start' => $request->start,
            'end' => $request->end,
        ], JSON_PRETTY_PRINT));

        return redirect()->back();
    }
}
