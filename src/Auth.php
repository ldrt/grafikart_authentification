<?php
namespace App;
use PDO;

class Auth {
    private $pdo;
    private $loginPath;

    public function __construct(PDO $pdo, string $loginPath)
    {
        $this->pdo = $pdo;
        $this->loginPath = $loginPath;
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
            if(session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['auth'] = $user->id;
            return $user;
        }
        return null;
    }

    public function user() : ?User
    {
        if(session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $id = $_SESSION['auth'] ?? null;
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
        if($user === null || !in_array($user->role, $roles)) {
            header("Location: {$this->loginPath}?forbid=1");
            exit();
        }
    }
}
?>