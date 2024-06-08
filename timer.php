<?php
date_default_timezone_set('Asia/Jakarta');
include 'config.php';

$now = new DateTime();
$specificDate = new DateTime('2024-06-09 20:00:00'); // Replace with your desired date and time

if ($now->format('YmdHis') > $specificDate->format('YmdHis')) {
    header('location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengumuman Kelulusan SMP Islam Parlaungan</title>
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/js/tabler.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
        }

        body {
            background-image: url('1.png');
            background-repeat: no-repeat;
            background-size: cover;
            background-attachment: fixed;
            font-family: "Inter", sans-serif;
            font-optical-sizing: auto;
            font-weight: 400;
            font-style: normal;
        }

        .card {
            width: 80%;
            height: 500px;
            background-color: rgba(255, 255, 255, 0.185);
            /* Set opacity to 50% */
            border: none;
            padding: 20px;
            margin: 20px auto;
        }

        .card-content {
            color: #333;
        }

        .card-title {
            font-size: 18px;
            font-weight: bold;
        }

        .form-control {
            background-color: rgba(255, 255, 255, 0.5); /* Set background to semi-transparent */
            border: 1px solid #ccc;
            color: #333;
        }

        .form-control::placeholder {
            color: #aaa;
        }

        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.5); /* Maintain semi-transparent background on focus */
            outline: none;
            border-color: #80bdff;
            box-shadow: 0 0 5px rgba(128, 189, 255, 0.5);
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="card-content">
            <h1 class="text-center">Maaf Pengumuman Kelulusan Belum Dapat Diakses!</h1>
        </div>
    </div>
</body>

</html>
