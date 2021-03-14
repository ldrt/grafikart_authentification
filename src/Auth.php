<?php
namespace App;

use App\Exception\ForbiddenException;
use PDO;

class Auth {
    private $pdo;
    private $loginPath;
    private $session;

    public function __construct(PDO $pdo, string $loginPath, array &$session)
    {
        $this->pdo = $pdo;
        $this->loginPath = $loginPath;
        $this->session = &$session; //&$array = reference to $array
    }

    public function login(string $username, string $password) : ?User
    {
        $query = $this->pdo->prepare('SELECT * FROM users WHERE username = :username');
        $query->execute(['username' => $username]);
        $user = $query->fetchObject(User::class);
        if ($user === false) {
            return null;
        }
        if (password_verify($password, $user->password)) {
            $this->session['auth'] = $user->id;
            return $user;
        }
        return null;
    }

    public function user() : ?User
    {
        $id = $this->session['auth'] ?? null;
        if ($id === null) {
            return null;
        }
        $query = $this->pdo->prepare('SELECT * FROM users WHERE id = :id');
        $query->execute(['id' => $id]);
        $user = $query->fetchObject(User::class);
        return $user ?: null;
    }

    // ...$var : several params in form of an array
    public function requireRole(string ...$roles) : void
    {
        $user = $this->user();
        if($user === null) {
            throw new ForbiddenException("Vous devez être connecté pour votre cette page");
        }
        if (!in_array($user->role, $roles)) {
            $roles = implode(',', $roles);
            $role = $user->role;
            throw new ForbiddenException("Vous avez pas le rôle nécessaire ($role != $roles) pour accéder à la page");
        }
    }
}
?>