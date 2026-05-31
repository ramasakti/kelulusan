<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class KelulusanController extends Controller
{
    /**
     * Display the graduation status search page.
     */
    public function index(Request $request): Response
    {
        $nisn = $request->query('nisn');

        // Jika query nisn kosong, tampilkan landing page tanpa data siswa
        if (blank($nisn)) {
            return Inertia::render('kelulusan', [
                'search' => null,
                'siswa' => null,
                'error' => null,
            ]);
        }

        // Cari siswa berdasarkan nisn dengan eager loading nilai dan mapel
        $siswa = Siswa::with('nilai.mapel')
            ->where('nisn', $nisn)
            ->first();

        // Jika siswa tidak ditemukan
        if (!$siswa) {
            return Inertia::render('kelulusan', [
                'search' => $nisn,
                'siswa' => null,
                'error' => 'Data siswa tidak ditemukan',
            ]);
        }

        // Hitung rata-rata nilai
        $nilaiCollection = $siswa->nilai;
        $rataRata = $nilaiCollection->isNotEmpty() 
            ? round($nilaiCollection->avg('nilai'), 2) 
            : 0;

        // Transformasi format nilai untuk dikirim ke props
        $nilaiFormatted = $nilaiCollection->map(function ($item) {
            return [
                'mata_pelajaran' => $item->mapel ? $item->mapel->nama_mapel : 'Tidak Diketahui',
                'nilai' => $item->nilai,
            ];
        })->values()->all();

        // Kirim data ke Inertia
        return Inertia::render('kelulusan', [
            'search' => $nisn,
            'siswa' => [
                'nama_siswa' => $siswa->nama_siswa,
                'nisn' => $siswa->nisn,
                'status_kelulusan' => $siswa->lulus ? 'LULUS' : 'TIDAK LULUS',
                'rata_rata' => (float) $rataRata,
                'nilai' => $nilaiFormatted,
            ],
            'error' => null,
        ]);
    }
}
