<?php
require '../vendor/autoload.php';

use App\App;

session_start();

$error = false;

$auth = App::getAuth();

// User already connected
/*if ($auth->user() !== null) {
    header('Location: index.php');
    exit();
}*/

// Authentification
if(!empty($_POST)){
    $user = $auth->login($_POST['username'], $_POST['password']);
    if ($user) {
        header('Location: index.php?login=1');
        exit();
    }
    $error = true;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body class="p-4">
    <h1>Se connecter</h1>

    <?php if($error) : ?>
    <div class="alert alert-danger">Identifiant ou mot de passe incorrect</div>
    <?php endif ?>

    <?php if(isset($_GET['forbid'])) : ?>
    <div class="alert alert-danger">Accès à la page interdit</div>
    <?php endif ?>

    <form action="" method="post">
        <div class="form-froup">
            <input type="text" name ="username" placeholder="pseudo" class="form-control">
        </div>
        <div class="form-froup">
            <input type="password" name ="password" placeholder="mot de passe" class="form-control">
        </div>
        <button class="btn btn-primary">Se connecter</button>
    </form>

    <?php dump($_SESSION) ?>
</body>
</html>