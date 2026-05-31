<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSiswaRequest;
use App\Http\Requests\UpdateSiswaRequest;
use App\Http\Requests\UpdateNilaiRequest;
use App\Models\Siswa;
use App\Models\Nilai;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class SiswaController extends Controller
{
    /**
     * Store a newly created student in storage.
     */
    public function store(StoreSiswaRequest $request): RedirectResponse
    {
        Siswa::create($request->validated());

        return redirect()->back()->with('success', 'Siswa berhasil ditambahkan.');
    }

    /**
     * Update the specified student in storage.
     */
    public function update(UpdateSiswaRequest $request, Siswa $siswa): RedirectResponse
    {
        $siswa->update($request->validated());

        return redirect()->back()->with('success', 'Siswa berhasil diperbarui.');
    }

    /**
     * Remove the specified student from storage.
     */
    public function destroy(Siswa $siswa): RedirectResponse
    {
        $siswa->delete();

        return redirect()->back()->with('success', 'Siswa berhasil dihapus.');
    }

    public function updateNilai(UpdateNilaiRequest $request, Siswa $siswa): RedirectResponse
    {
        $validated = $request->validated();
        $submittedNilai = $validated['nilai'] ?? [];
        $submittedTka = $validated['tka'] ?? [];

        DB::transaction(function () use ($siswa, $submittedNilai, $submittedTka) {
            // 1. Proses Nilai Reguler
            $submittedMapelIds = collect($submittedNilai)->pluck('mapel_id')->toArray();

            // Hapus nilai reguler siswa yang tidak ada di data kiriman
            Nilai::where('siswa_id', $siswa->id)
                ->whereNotIn('mapel_id', $submittedMapelIds)
                ->delete();

            // Update atau Insert nilai reguler baru
            foreach ($submittedNilai as $item) {
                Nilai::updateOrCreate(
                    [
                        'siswa_id' => $siswa->id,
                        'mapel_id' => $item['mapel_id'],
                    ],
                    [
                        'nilai' => $item['nilai'],
                    ]
                );
            }

            // 2. Proses Nilai TKA
            $submittedTkaMapels = collect($submittedTka)->pluck('mapel')->toArray();

            // Hapus nilai TKA siswa yang tidak ada di data kiriman
            \App\Models\TKA::where('siswa_id', $siswa->id)
                ->whereNotIn('mapel', $submittedTkaMapels)
                ->delete();

            // Update atau Insert nilai TKA baru
            foreach ($submittedTka as $item) {
                \App\Models\TKA::updateOrCreate(
                    [
                        'siswa_id' => $siswa->id,
                        'mapel' => $item['mapel'],
                    ],
                    [
                        'nilai' => $item['nilai'],
                    ]
                );
            }
        });

        return redirect()->back()->with('success', 'Nilai siswa berhasil diperbarui.');
    }
}
