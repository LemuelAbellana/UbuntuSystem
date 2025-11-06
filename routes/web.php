<?php

require_once __DIR__ . '/../app/Controllers/AnimeController.php';
require_once __DIR__ . '/../app/Middleware/Auth.php';
require_once __DIR__ . '/../app/Security/CSRF.php';

use App\Controllers\AnimeController;
use App\Middleware\Auth;
use App\Security\CSRF;

// Parse the URL
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Public routes (no authentication required)
switch (true) {
    // Login page
    case $uri === '/login' && $method === 'GET':
        if (Auth::isLoggedIn()) {
            header('Location: /anime');
            exit;
        }
        require __DIR__ . '/../resources/views/auth/login.php';
        exit;

    // Login action
    case $uri === '/login' && $method === 'POST':
        session_start();
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            die('CSRF token validation failed');
        }

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if (Auth::login($username, $password)) {
            header('Location: /anime');
            exit;
        } else {
            $error = 'Invalid username or password';
            require __DIR__ . '/../resources/views/auth/login.php';
            exit;
        }

    // Logout
    case $uri === '/logout':
        Auth::logout();
        exit;
}

// Protected routes (authentication required)
Auth::check();

$controller = new AnimeController();

// Routes
switch (true) {
    // List all anime
    case $uri === '/anime' && $method === 'GET':
        $controller->index();
        break;

    // Show create form
    case $uri === '/anime/create' && $method === 'GET':
        $controller->create();
        break;

    // Store new anime
    case $uri === '/anime' && $method === 'POST':
        $controller->store();
        break;

    // Show edit form
    case preg_match('/^\/anime\/(\d+)\/edit$/', $uri, $matches) && $method === 'GET':
        $controller->edit($matches[1]);
        break;

    // Update anime
    case preg_match('/^\/anime\/(\d+)$/', $uri, $matches) && $method === 'POST':
        $controller->update($matches[1]);
        break;

    // Delete anime (changed to POST)
    case preg_match('/^\/anime\/(\d+)\/delete$/', $uri, $matches) && $method === 'POST':
        $controller->destroy($matches[1]);
        break;

    // Default: redirect to anime list
    default:
        header('Location: /anime');
        exit;
}
