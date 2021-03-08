<?php
require '../vendor/autoload.php';
use App\App;

App::getAuth()->requireRole('admin');
?>

Réservé à l'admin