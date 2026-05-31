<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMapelRequest;
use App\Http\Requests\UpdateMapelRequest;
use App\Models\Mapel;
use Illuminate\Http\RedirectResponse;

class MapelController extends Controller
{
    /**
     * Store a newly created subject in storage.
     */
    public function store(StoreMapelRequest $request): RedirectResponse
    {
        Mapel::create($request->validated());

        return redirect()->back()->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    /**
     * Update the specified subject in storage.
     */
    public function update(UpdateMapelRequest $request, Mapel $mapel): RedirectResponse
    {
        $mapel->update($request->validated());

        return redirect()->back()->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    /**
     * Remove the specified subject from storage.
     */
    public function destroy(Mapel $mapel): RedirectResponse
    {
        $mapel->delete();

        return redirect()->back()->with('success', 'Mata pelajaran berhasil dihapus.');
    }
}
