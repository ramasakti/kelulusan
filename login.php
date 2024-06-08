<?php
session_start();
include 'config.php';
$username = htmlspecialchars($_POST['username'] ?? '');
$password = htmlspecialchars($_POST['password'] ?? '');

$sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
$result = mysqli_query($conn, $sql);
$x = mysqli_fetch_array($result);

if (mysqli_num_rows($result) > 0) {
    $_SESSION['auth'] = 1;
    $_SESSION['username'] = $password;

    header('location: dashboard.php');
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
            <h2 class="card-title">Login</h2>
            <form action="" method="post">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" class="form-control" name="username" placeholder="Username" />
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="text" class="form-control" name="password" placeholder="Password" />
                </div>
                <div class="mb-3">
                    <button type="submit" class="btn btn-outline-info w-100">Login</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
