<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSiswaRequest;
use App\Http\Requests\UpdateSiswaRequest;
use App\Http\Requests\UpdateNilaiRequest;
use App\Imports\SiswaImport;
use App\Models\Siswa;
use App\Models\Nilai;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

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

    /**
     * Import students from an uploaded Excel file.
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ], [
            'file.required' => 'File Excel wajib diunggah.',
            'file.mimes'    => 'Format file harus .xlsx, .xls, atau .csv.',
            'file.max'      => 'Ukuran file maksimal 5 MB.',
        ]);

        $import = new SiswaImport();
        Excel::import($import, $request->file('file'));

        $message = "Berhasil mengimpor {$import->imported} siswa.";
        if ($import->skipped > 0) {
            $message .= " {$import->skipped} baris dilewati (data tidak lengkap).";
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Download a blank Excel template for student import.
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import Siswa');

        // Set header row
        $headers = ['No', 'NISN', 'Nama'];
        foreach ($headers as $col => $header) {
            $cell = chr(65 + $col) . '1';
            $sheet->setCellValue($cell, $header);
        }

        // Style header
        $sheet->getStyle('A1:C1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 11,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E3A5F'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Add 3 sample rows (blank)
        for ($row = 2; $row <= 4; $row++) {
            $sheet->setCellValue('A' . $row, $row - 1);
        }

        // Auto-width columns
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(40);
        $sheet->getRowDimension(1)->setRowHeight(22);

        // Add note in row 6
        $sheet->setCellValue('A6', 'Catatan: Kolom NISN & Nama wajib diisi. Kolom No hanya untuk referensi urutan.');
        $sheet->getStyle('A6')->getFont()->setItalic(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF888888'));
        $sheet->mergeCells('A6:C6');

        // Stream response
        $writer = new Xlsx($spreadsheet);
        $filename = 'template_import_siswa.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }
}
