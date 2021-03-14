<?php

use App\Auth;
use App\Exception\ForbiddenException;
use PHPUnit\Framework\TestCase;

class AuthTest extends TestCase{
    /**
     * @var Auth
     */
    private $auth;
    private $session = [];

    /**
     * @before
     */
    public function setAuth()
    {
        // Create DB (in memory) for testing
        $pdo = new PDO("sqlite::memory:", null, null, [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        $pdo->query('CREATE TABLE users (id INTEGER, username TEXT, password TEXT, role TEXT)');
        // Create some users
        for ($i = 1; $i <=10; $i++) {
            $password = password_hash("user$i", PASSWORD_BCRYPT);
            $pdo->query("INSERT INTO users (id, username, password, role) VALUES ($i, 'user$i', '$password', 'user$i')");
        }
        $this->auth = new Auth($pdo, "/login", $this->session);
    }
    
    public function testLoginWithBadUsername()
    {
        $this->assertNull($this->auth->login('aze', 'aze'));
    }

    public function testLoginWithBadPassword()
    {
        $this->assertNull($this->auth->login('user1', 'aze'));
    }

    public function testLoginSuccess()
    {
        $this->assertObjectHasAttribute("username", $this->auth->login('user1', 'user1'));
        $this->assertEquals(1, $this->session['auth']);
    }

    public function testUserWhenNotConnected()
    {
        $this->assertNull($this->auth->user());
    }

    public function testUserWhenConnectedWithNonexistingUser()
    {
        $this->session['auth'] = 11;
        $this->assertNull($this->auth->user());
    }

    public function testUserWhenConnected()
    {
        $this->session['auth'] = 4;
        $user = $this->auth->user();
        $this->assertIsObject($user);
        $this->assertEquals("user4", $user->username);
    }

    public function testRequireRole()
    {
        $this->session['auth'] = 2;
        $this->auth->requireRole('user2');
        $this->expectNotToPerformAssertions();
    }

    public function testRequireRoleWithoutLoginThrowException()
    {
        $this->expectException(ForbiddenException::class);
        $user = $this->auth->requireRole('user3');
    }

    public function testRequireRoleThrowException()
    {
        $this->expectException(ForbiddenException::class);
        $this->session['auth'] = 2;
        $this->auth->requireRole('user3');
    }
}
?>