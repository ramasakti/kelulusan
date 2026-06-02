<?php

namespace App\Imports;

use App\Models\Mapel;
use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\TKA;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class NilaiImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    public int $updated    = 0;
    public int $tkaUpdated = 0;
    public int $skipped    = 0;
    public array $errors   = [];

    /**
     * Map of normalized mapel names => mapel IDs (built once).
     */
    private array $mapelMap = [];

    /**
     * Fixed TKA column headings => canonical enum value in the tka table.
     */
    private const TKA_COLUMNS = [
        'tka_matematika'      => 'Matematika',
        'tka_bahasa_indonesia' => 'Bahasa Indonesia',
    ];

    public function __construct()
    {
        // Build a lookup: lowercase(nama_mapel) => id
        $this->mapelMap = Mapel::all()
            ->keyBy(fn($m) => $this->normalizeHeading((string) $m->nama_mapel))
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

            // Iterate all other columns (skipping 'no' and 'nama_siswa')
            foreach ($rowArray as $heading => $value) {
                $normalizedHeading = $this->normalizeHeading((string) $heading);

                // Skip non-data columns
                if (in_array($normalizedHeading, ['no', 'nama_siswa'])) {
                    continue;
                }

                // Skip empty / null values for any column
                if ($value === null || $value === '') {
                    continue;
                }

                // ── TKA branch ──────────────────────────────────────────────
                if (isset(self::TKA_COLUMNS[$normalizedHeading])) {
                    if (!is_numeric($value)) {
                        $this->errors[] = "Baris " . ($index + 2) . ": Nilai TKA \"{$value}\" untuk kolom \"{$heading}\" bukan angka.";
                        continue;
                    }

                    $nilaiInt = $value;

                    if ($nilaiInt < 0 || $nilaiInt > 100) {
                        $this->errors[] = "Baris " . ($index + 2) . ": Nilai TKA \"{$nilaiInt}\" di luar rentang 0-100.";
                        continue;
                    }

                    TKA::updateOrCreate(
                        [
                            'siswa_id' => $siswa->id,
                            'mapel'    => self::TKA_COLUMNS[$normalizedHeading],
                        ],
                        [
                            'nilai' => $nilaiInt,
                        ]
                    );

                    $this->tkaUpdated++;
                    continue;
                }

                dd($this->mapelMap, $rowArray);
                // ── Regular Nilai branch ────────────────────────────────────
                if (!isset($this->mapelMap[$normalizedHeading])) {
                    continue; // Unknown column, skip silently
                }

                if (!is_numeric($value)) {
                    $this->errors[] = "Baris " . ($index + 2) . ": Nilai \"{$value}\" untuk mapel \"{$heading}\" bukan angka.";
                    continue;
                }

                $nilaiInt = (int) $value;

                if ($nilaiInt < 0 || $nilaiInt > 100) {
                    $this->errors[] = "Baris " . ($index + 2) . ": Nilai \"{$nilaiInt}\" di luar rentang 0-100.";
                    continue;
                }

                Nilai::updateOrCreate(
                    [
                        'siswa_id' => $siswa->id,
                        'mapel_id' => $this->mapelMap[$normalizedHeading],
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
        $value = strtolower(trim($value));

        // Hilangkan titik
        $value = str_replace('.', '', $value);

        // Ganti koma dan tanda hubung menjadi spasi (sebelum replace spasi)
        $value = str_replace([',', '-'], ' ', $value);

        // Ubah spasi (termasuk hasil konversi di atas) menjadi underscore
        $value = preg_replace('/\s+/', '_', $value);

        // Hanya pertahankan huruf, angka, underscore
        $value = preg_replace('/[^a-z0-9_]/', '', $value);

        return $value;
    }
}
