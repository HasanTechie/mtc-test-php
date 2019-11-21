<?php
$dotenv = Dotenv\Dotenv::create(dirname(__DIR__, 1));
$dotenv->load();

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!is_dir('uploads')) {
    mkdir('uploads/', 0777, true);
    mkdir('uploads/thumbs', 0777, true);
}

require 'functions.php';

spl_autoload_register(function ($className) {
    require_once 'includes/classes/' . $className . '.php';
});

?>