<?php

namespace App\Imports;

use App\Models\Siswa;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class SiswaImport implements ToCollection, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    public array $errors = [];
    public int $imported = 0;
    public int $skipped = 0;

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $nisn  = trim((string) ($row['nisn'] ?? ''));
            $nama  = trim((string) ($row['nama'] ?? ''));

            if ($nisn === '' || $nama === '') {
                $this->skipped++;
                continue;
            }

            // Skip duplicate NISN – update if already exists
            Siswa::updateOrCreate(
                ['nisn' => $nisn],
                ['nama_siswa' => $nama, 'lulus' => 'LULUS']
            );

            $this->imported++;
        }
    }

    public function rules(): array
    {
        return [
            '*.nisn' => ['required', 'string'],
            '*.nama' => ['required', 'string'],
        ];
    }
}
