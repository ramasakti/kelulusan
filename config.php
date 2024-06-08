<?php

$dbhost = "localhost"; // Nama host
$dbuser = "root"; // Nama pengguna
$dbpass = "root"; // Kata sandi
$dbname = "kelulusan"; // Nama database

// Buat koneksi
$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

// Periksa koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}