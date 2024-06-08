<?php
include 'config.php';

if (isset($_FILES["csv_file"]) && $_FILES["csv_file"]["error"] == 0) {
    $csvFile = $_FILES["csv_file"]["tmp_name"];
    
    // Buka file CSV
    if (($handle = fopen($csvFile, "r")) !== FALSE) {
        // Lewati baris pertama jika berisi header
        fgetcsv($handle);

        // Loop melalui setiap baris di file CSV
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            // Asumsikan kolom pertama adalah NISN, kolom kedua adalah Nama Siswa, dan kolom ketiga adalah Status Kelulusan
            $nisn = $conn->real_escape_string($data[0]);
            $nama_siswa = $conn->real_escape_string($data[1]);
            $lulus = $conn->real_escape_string($data[2]);

            // Insert ke database
            $sql = "INSERT INTO kelulusan (nisn, nama_siswa, lulus) VALUES ('$nisn', '$nama_siswa', $lulus)";

            if (!$conn->query($sql)) {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
        fclose($handle);
        header('location: dashboard.php');
    } else {
        echo "Error opening the file.";
    }
} else {
    echo "Error: " . $_FILES["csv_file"]["error"];
}