<?php

namespace Database\Seeders;

use App\Models\Mapel;
use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed Test User
        User::updateOrCreate(
            ['email' => 'parlaungan.sch@gmail.com'],
            [
                'name' => 'Admin Sekolah',
                'password' => bcrypt('parlaungan1977'),
            ]
        );

        // Seed Mapel (Subjects)
        $subjects = [
            'Matematika',
            'Bahasa Indonesia',
            'Bahasa Inggris',
            'Fisika',
            'Kimia',
            'Biologi',
            'Sejarah',
        ];

        $mapelModels = [];
        foreach ($subjects as $subjectName) {
            $mapelModels[$subjectName] = Mapel::firstOrCreate(['nama_mapel' => $subjectName]);
        }

        // // Seed Siswa & Nilai
        // // 1. Budi Santoso (Lulus)
        // $budi = Siswa::updateOrCreate(
        //     ['nisn' => '1234567890'],
        //     [
        //         'nama_siswa' => 'Budi Santoso',
        //         'lulus' => true,
        //     ]
        // );

        // $budiGrades = [
        //     'Matematika' => 90,
        //     'Bahasa Indonesia' => 81,
        //     'Bahasa Inggris' => 85,
        //     'Fisika' => 88,
        //     'Kimia' => 83,
        //     'Biologi' => 87,
        //     'Sejarah' => 90,
        // ];

        // foreach ($budiGrades as $subject => $grade) {
        //     Nilai::updateOrCreate(
        //         [
        //             'siswa_id' => $budi->id,
        //             'mapel_id' => $mapelModels[$subject]->id,
        //         ],
        //         ['nilai' => $grade]
        //     );
        // }

        // // 2. Ani Lestari (Lulus)
        // $ani = Siswa::updateOrCreate(
        //     ['nisn' => '0987654321'],
        //     [
        //         'nama_siswa' => 'Ani Lestari',
        //         'lulus' => true,
        //     ]
        // );

        // $aniGrades = [
        //     'Matematika' => 95,
        //     'Bahasa Indonesia' => 92,
        //     'Bahasa Inggris' => 94,
        //     'Fisika' => 90,
        //     'Kimia' => 88,
        //     'Biologi' => 91,
        //     'Sejarah' => 93,
        // ];

        // foreach ($aniGrades as $subject => $grade) {
        //     Nilai::updateOrCreate(
        //         [
        //             'siswa_id' => $ani->id,
        //             'mapel_id' => $mapelModels[$subject]->id,
        //         ],
        //         ['nilai' => $grade]
        //     );
        // }

        // // 3. Eko Prasetyo (Tidak Lulus)
        // $eko = Siswa::updateOrCreate(
        //     ['nisn' => '1122334455'],
        //     [
        //         'nama_siswa' => 'Eko Prasetyo',
        //         'lulus' => false,
        //     ]
        // );

        // $ekoGrades = [
        //     'Matematika' => 55,
        //     'Bahasa Indonesia' => 60,
        //     'Bahasa Inggris' => 58,
        //     'Fisika' => 50,
        //     'Kimia' => 52,
        //     'Biologi' => 55,
        //     'Sejarah' => 62,
        // ];

        // foreach ($ekoGrades as $subject => $grade) {
        //     Nilai::updateOrCreate(
        //         [
        //             'siswa_id' => $eko->id,
        //             'mapel_id' => $mapelModels[$subject]->id,
        //         ],
        //         ['nilai' => $grade]
        //     );
        // }
    }
}
