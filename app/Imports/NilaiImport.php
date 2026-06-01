<?php

namespace App\Imports;

use App\Models\Mapel;
use App\Models\Nilai;
use App\Models\Siswa;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class NilaiImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    public int $updated = 0;
    public int $skipped = 0;
    public array $errors = [];

    /**
     * Map of normalized mapel names => mapel IDs (built once).
     */
    private array $mapelMap = [];

    public function __construct()
    {
        // Build a lookup: lowercase(nama_mapel) => id
        $this->mapelMap = Mapel::all()
            ->keyBy(fn($m) => $this->normalizeHeading($m->nama_mapel))
            ->map(fn($m) => $m->id)
            ->toArray();
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $rowArray = $row->toArray();

            // Get student name from the 'nama_siswa' column
            $namaSiswa = trim((string) ($rowArray['nama_siswa'] ?? ''));

            if ($namaSiswa === '') {
                $this->skipped++;
                continue;
            }

            // Find student by exact name
            $siswa = Siswa::where('nama_siswa', $namaSiswa)->first();

            if (!$siswa) {
                $this->skipped++;
                $this->errors[] = "Baris " . ($index + 2) . ": Siswa \"{$namaSiswa}\" tidak ditemukan.";
                continue;
            }

            // Iterate all other columns (skipping 'no' and 'nama_siswa') to find mapel values
            foreach ($rowArray as $heading => $value) {
                $normalizedHeading = $this->normalizeHeading((string) $heading);

                // Skip the 'no' and 'nama_siswa' columns
                if (in_array($normalizedHeading, ['no', 'nama_siswa'])) {
                    continue;
                }

                // Check if this heading matches a known mapel
                if (!isset($this->mapelMap[$normalizedHeading])) {
                    continue; // Unknown column, skip silently
                }

                $mapelId = $this->mapelMap[$normalizedHeading];
                $nilaiValue = $value;

                // Skip empty/null values
                if ($nilaiValue === null || $nilaiValue === '') {
                    continue;
                }

                // Validate numeric value
                if (!is_numeric($nilaiValue)) {
                    $this->errors[] = "Baris " . ($index + 2) . ": Nilai \"{$nilaiValue}\" untuk mapel \"{$heading}\" bukan angka.";
                    continue;
                }

                $nilaiInt = (int) $nilaiValue;

                if ($nilaiInt < 0 || $nilaiInt > 100) {
                    $this->errors[] = "Baris " . ($index + 2) . ": Nilai \"{$nilaiInt}\" di luar rentang 0-100.";
                    continue;
                }

                Nilai::updateOrCreate(
                    [
                        'siswa_id' => $siswa->id,
                        'mapel_id' => $mapelId,
                    ],
                    [
                        'nilai' => $nilaiInt,
                    ]
                );

                $this->updated++;
            }
        }
    }

    /**
     * Normalize a heading string for comparison:
     * lowercase, trim, replace spaces with underscores.
     */
    private function normalizeHeading(string $value): string
    {
        return str_replace(' ', '_', strtolower(trim($value)));
    }
}
