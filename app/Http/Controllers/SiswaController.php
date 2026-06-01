<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSiswaRequest;
use App\Http\Requests\UpdateSiswaRequest;
use App\Http\Requests\UpdateNilaiRequest;
use App\Imports\SiswaImport;
use App\Imports\NilaiImport;
use App\Models\Mapel;
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
use PhpOffice\PhpSpreadsheet\Style\Border;

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

    /**
     * Download an Excel template for grade (nilai) import.
     * Column A: No, Column B: Nama Siswa (pre-filled),
     * Column C+: Mapel names, then TKA Matematika & TKA Bahasa Indonesia.
     */
    public function downloadNilaiTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import Nilai');

        $allSiswa = Siswa::with(['nilai', 'tka'])->orderBy('nama_siswa')->get();
        $allMapel = Mapel::orderBy('nama_mapel')->get();

        // --- Build header row ---
        // Regular mapel columns
        $headers = ['No', 'Nama Siswa'];
        foreach ($allMapel as $m) {
            $headers[] = $m->nama_mapel;
        }
        // TKA columns appended at the end
        $tkaHeaders = ['TKA Matematika', 'TKA Bahasa Indonesia'];
        foreach ($tkaHeaders as $tkaHeader) {
            $headers[] = $tkaHeader;
        }

        $lastColIndex  = count($headers) - 1;
        $lastColLetter = $this->columnLetter($lastColIndex);

        foreach ($headers as $col => $header) {
            $cell = $this->columnLetter($col) . '1';
            $sheet->setCellValue($cell, $header);
        }

        // Style regular header columns (deep navy)
        $mapelEndColIndex  = count($allMapel) + 1; // 0-based index of the last mapel column
        $mapelEndColLetter = $this->columnLetter($mapelEndColIndex);
        $regularHeaderRange = 'A1:' . $mapelEndColLetter . '1';
        $sheet->getStyle($regularHeaderRange)->applyFromArray([
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

        // Style TKA header columns (warm amber)
        $tkaStartColIndex  = $mapelEndColIndex + 1;
        $tkaStartColLetter = $this->columnLetter($tkaStartColIndex);
        $tkaHeaderRange    = $tkaStartColLetter . '1:' . $lastColLetter . '1';
        $sheet->getStyle($tkaHeaderRange)->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 11,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'B45309'], // amber-700
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(24);

        // --- Fill student rows ---
        $row = 2;
        foreach ($allSiswa as $index => $s) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $s->nama_siswa);

            // Style number column
            $sheet->getStyle('A' . $row)->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'font'      => ['color' => ['rgb' => '888888']],
            ]);

            // Style name column (subtle read-only bg)
            $sheet->getStyle('B' . $row)->applyFromArray([
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F1F5F9'],
                ],
                'font' => [
                    'bold'  => true,
                    'color' => ['rgb' => '334155'],
                ],
            ]);

            // Pre-fill regular mapel grades
            $nilaiByMapel = $s->nilai->keyBy('mapel_id');
            foreach ($allMapel as $mapelIndex => $m) {
                $colLetter   = $this->columnLetter(2 + $mapelIndex);
                $existingNilai = $nilaiByMapel->get($m->id);
                if ($existingNilai && $existingNilai->nilai !== null) {
                    $sheet->setCellValue($colLetter . $row, $existingNilai->nilai);
                }
                $sheet->getStyle($colLetter . $row)->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
            }

            // Pre-fill TKA grades
            $tkaByMapel = $s->tka->keyBy('mapel');
            $tkaColumns = [
                $tkaStartColIndex     => 'Matematika',
                $tkaStartColIndex + 1 => 'Bahasa Indonesia',
            ];
            foreach ($tkaColumns as $colIndex => $tkaMapelName) {
                $colLetter   = $this->columnLetter($colIndex);
                $existingTka = $tkaByMapel->get($tkaMapelName);
                if ($existingTka) {
                    $sheet->setCellValue($colLetter . $row, $existingTka->nilai);
                }
                $sheet->getStyle($colLetter . $row)->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFFBEB'], // amber-50 tint
                    ],
                ]);
            }

            $row++;
        }

        // --- Column widths ---
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(35);
        foreach ($allMapel as $mapelIndex => $m) {
            $colLetter = $this->columnLetter(2 + $mapelIndex);
            $width     = max(strlen($m->nama_mapel) + 4, 14);
            $sheet->getColumnDimension($colLetter)->setWidth($width);
        }
        // TKA column widths
        $sheet->getColumnDimension($this->columnLetter($tkaStartColIndex))->setWidth(18);
        $sheet->getColumnDimension($this->columnLetter($tkaStartColIndex + 1))->setWidth(22);

        // --- Grid borders on entire data area ---
        $lastDataRow = max($row - 1, 2);
        $sheet->getStyle('A1:' . $lastColLetter . $lastDataRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'CBD5E1'],
                ],
            ],
        ]);

        // --- Note row ---
        $noteRow = $lastDataRow + 2;
        $sheet->setCellValue('A' . $noteRow, 'Catatan: Kolom Nama Siswa sudah terisi otomatis. Isi nilai (0-100) pada kolom mapel (biru) dan kolom TKA (coklat).');
        $sheet->getStyle('A' . $noteRow)->getFont()->setItalic(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF888888'));
        $sheet->mergeCells('A' . $noteRow . ':' . $lastColLetter . $noteRow);

        // Stream response
        $writer   = new Xlsx($spreadsheet);
        $filename = 'template_import_nilai.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    /**
     * Import grades from an uploaded Excel file.
     */
    public function importNilai(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ], [
            'file.required' => 'File Excel wajib diunggah.',
            'file.mimes'    => 'Format file harus .xlsx, .xls, atau .csv.',
            'file.max'      => 'Ukuran file maksimal 5 MB.',
        ]);

        $import = new NilaiImport();
        Excel::import($import, $request->file('file'));

        $message = "Berhasil memperbarui {$import->updated} nilai mapel dan {$import->tkaUpdated} nilai TKA.";
        if ($import->skipped > 0) {
            $message .= " {$import->skipped} baris dilewati.";
        }
        if (count($import->errors) > 0) {
            $message .= " Peringatan: " . implode('; ', array_slice($import->errors, 0, 5));
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Convert a 0-based column index to Excel column letter (A, B, ... Z, AA, AB...).
     */
    private function columnLetter(int $index): string
    {
        $letter = '';
        while ($index >= 0) {
            $letter = chr(65 + ($index % 26)) . $letter;
            $index = intdiv($index, 26) - 1;
        }
        return $letter;
    }
}
