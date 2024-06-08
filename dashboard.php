<?php
session_start();
include 'config.php';

if (!isset($_SESSION['auth'])) {
    header('location: login.php');
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
</head>
<style>
    body {
        font-family: "Inter", sans-serif;
        font-optical-sizing: auto;
        font-weight: 400;
        font-style: normal;
    }
</style>
<body>
    <div class="page">
    <!-- Sidebar -->
    <aside class="navbar navbar-vertical navbar-expand-sm navbar-transparent">
        <div class="container-fluid">
        <button class="navbar-toggler" type="button">
            <span class="navbar-toggler-icon"></span>
        </button>
        <h1 class="navbar-brand navbar-brand-autodark">
            <a href="/">
            <img src="..." width="110" height="32" alt="SMP Islam Parlaungan" class="navbar-brand-image">
            </a>
        </h1>
        <div class="collapse navbar-collapse" id="sidebar-menu">
            <ul class="navbar-nav pt-lg-3">
                <li class="nav-item">
                    <a class="nav-link" href="./">
                    <span class="nav-link-title">
                        Home
                    </span>
                    </a>
                </li>
            </ul>
        </div>
        </div>
    </aside>
    <div class="page-wrapper">
        <div class="page-header d-print-none">
            <div class="container-xl">
                <div class="row g-2 align-items-center">
                    <div class="col">
                        <h2 class="page-title">
                        Dashboard
                        </h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="page-body">
            <div class="container-xl">
                <div class="btn-list">
                    <button type="button" class="btn btn-teal mb-3" data-bs-toggle="modal" data-bs-target="#exampleModal">
                        Import Data CSV
                    </button>
                    <form action="reset.php" method="post">
                        <button type="submit" class="btn btn-danger mb-3" >
                            Reset Data
                        </button>
                    </form>
                </div>

                <div class="modal" id="exampleModal" tabindex="-1">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Import Data Siswa</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <a href="import.csv">Download Template</a>
                                <form action="import.php" method="post" enctype="multipart/form-data">
                                    <input type="file" name="csv_file" accept=".csv" required>
                                    <input class="btn btn-primary btn-sm" type="submit" name="submit" value="Upload">
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn me-auto" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <table class="table table-responsive">
                    <thead>
                        <tr>
                            <th>
                                NISN
                            </th>
                            <th>
                                Nama
                            </th>
                            <th>
                                Lulus
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM kelulusan";
                        $result = mysqli_query($conn, $sql);
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                        ?>
                        <tr>
                            <td>
                                <?= $row['nisn']; ?>
                            </td>
                            <td>
                                <?= $row['nama_siswa']; ?>
                            </td>
                            <td>
                                <?= $row['lulus']; ?>
                            </td>
                        </tr>
                        <?php
                        }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>
</body>

</html>
