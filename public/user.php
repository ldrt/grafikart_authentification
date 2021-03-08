<?php
require '../vendor/autoload.php';
use App\App;

App::getAuth()->requireRole('user', 'admin');
?>

Réservé à l'utilisateur