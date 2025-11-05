<?php

require_once __DIR__ . '/../app/Controllers/AnimeController.php';

use App\Controllers\AnimeController;

// Parse the URL
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

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

    // Delete anime
    case preg_match('/^\/anime\/(\d+)\/delete$/', $uri, $matches) && $method === 'GET':
        $controller->destroy($matches[1]);
        break;

    // Default: redirect to anime list
    default:
        header('Location: /anime');
        exit;
}
