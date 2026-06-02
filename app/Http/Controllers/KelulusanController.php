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

        $settingsPath = storage_path('app/settings/range.json');
        $settings = [
            'start' => now()->addDay()->format('Y-m-d\TH:i'),
            'end' => now()->addDays(5)->format('Y-m-d\TH:i'),
        ];
        if (file_exists($settingsPath)) {
            $settings = json_decode(file_get_contents($settingsPath), true);
        }

        $now = now();
        $start = \Illuminate\Support\Carbon::parse($settings['start']);
        $end = \Illuminate\Support\Carbon::parse($settings['end']);

        $isBefore = $now->lt($start);
        $isAfter = $now->gt($end);

        // Jika query nisn kosong, tampilkan landing page tanpa data siswa
        if (blank($nisn)) {
            return Inertia::render('kelulusan', [
                'search' => null,
                'siswa' => null,
                'error' => null,
                'settings' => $settings,
                'serverTime' => $now->toIso8601String(),
            ]);
        }

        // Sebelum countdown start, jangan tampilkan data/error jika dipaksa search
        if ($isBefore) {
            return Inertia::render('kelulusan', [
                'search' => $nisn,
                'siswa' => null,
                'error' => 'Akses ditolak: Pengumuman kelulusan belum dibuka.',
                'settings' => $settings,
                'serverTime' => $now->toIso8601String(),
            ]);
        }

        // Setelah countdown end, jangan tampilkan data/error jika dipaksa search
        if ($isAfter) {
            return Inertia::render('kelulusan', [
                'search' => $nisn,
                'siswa' => null,
                'error' => 'Akses ditolak: Periode pengumuman kelulusan telah berakhir.',
                'settings' => $settings,
                'serverTime' => $now->toIso8601String(),
            ]);
        }

        // Cari siswa berdasarkan nisn dengan eager loading nilai, mapel, dan tka
        $siswa = Siswa::with([
            'nilai' => fn($q) => $q->orderBy('mapel_id'),
            'nilai.mapel',
            'tka'
        ])
            ->where('nisn', $nisn)
            ->first();

        // Jika siswa tidak ditemukan
        if (!$siswa) {
            return Inertia::render('kelulusan', [
                'search' => $nisn,
                'siswa' => null,
                'error' => 'Data siswa tidak ditemukan',
                'settings' => $settings,
                'serverTime' => $now->toIso8601String(),
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

        // Hitung rata-rata TKA
        $tkaCollection = $siswa->tka;
        $rerataTka = $tkaCollection->isNotEmpty()
            ? round($tkaCollection->avg('nilai', 2)) : 0;

        // Transformasi format nilai TKA untuk dikirim ke props
        $tkaFormatted = $siswa->tka->map(function ($item) use ($rerataTka) {
            return [
                'mata_pelajaran' => $item->mapel,
                'nilai' => $item->nilai,
            ];
        })->values()->all();

        // Kirim data ke Inertia
        return Inertia::render('kelulusan', [
            'search' => $nisn,
            'siswa' => [
                'nama_siswa' => $siswa->nama_siswa,
                'nisn' => $siswa->nisn,
                'status_kelulusan' => $siswa->lulus,
                'rata_rata' => (float) $rataRata,
                'nilai' => $nilaiFormatted,
                'tka' => $tkaFormatted,
                'rata_rata_tka' => $rerataTka
            ],
            'error' => null,
            'settings' => $settings,
            'serverTime' => $now->toIso8601String(),
        ]);
    }
}
