<?php

namespace App\Http\Controllers;

use App\Models\Mapel;
use App\Models\Siswa;
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
        $siswa = Siswa::with('nilai.mapel')
            ->orderBy('nama_siswa')
            ->get();

        return Inertia::render('dashboard', [
            'mapel' => $mapel,
            'siswa' => $siswa,
        ]);
    }
}
