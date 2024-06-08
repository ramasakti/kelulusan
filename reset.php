<?php
include 'config.php';

$sql = "DELETE FROM kelulusan";
mysqli_query($conn, $sql);

header('location: dashboard.php');