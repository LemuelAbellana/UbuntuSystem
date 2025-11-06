<?php
namespace App\Middleware;

class Auth {
    // Password hash for 'admin123' - change this password
    private const PASSWORD_HASH = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

    public static function check() {
        session_start();

        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    }

    public static function login($username, $password) {
        // Simple hardcoded check (replace with database)
        if ($username === 'admin' && password_verify($password, self::PASSWORD_HASH)) {
            $_SESSION['user_id'] = 1;
            $_SESSION['username'] = $username;
            return true;
        }
        return false;
    }

    public static function logout() {
        session_start();
        session_destroy();
        header('Location: /login');
        exit;
    }

    public static function isLoggedIn() {
        session_start();
        return !empty($_SESSION['user_id']);
    }
}
