<?php
require __DIR__ . '/vendor/autoload.php';
require 'includes/basic.php';

spl_autoload_register(function ($className) {
    require_once 'includes/' . $className . '.php';
});

$property = new Property();

if (isset($_POST['submit'])) {
    if ($_POST['action'] == 'add') {
        $property->store($_POST);
    }
    if ($_POST['action'] == 'edit') {
        $property->update($_POST);
    }
}
if (!empty($_GET['delete'])) {
    $property->destroy($_GET['delete']);
}
if (!empty($_GET['edit'])) {
    $row = $property->edit($_GET['edit']);
}

require 'layouts/header.php';

require 'layouts/content.php';

require 'layouts/footer.php';
?>


