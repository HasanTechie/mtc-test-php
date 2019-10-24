<?php
$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();

$connection = mysqli_connect(getenv('DB_HOST'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'), getenv('DB_DATABASE'));

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>