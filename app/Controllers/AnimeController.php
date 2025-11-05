<?php

namespace App\Controllers;

use App\Models\Anime;

require_once __DIR__ . '/../Models/Anime.php';

class AnimeController {
    private $model;

    public function __construct() {
        $this->model = new Anime();
    }

    // Display listing of anime
    public function index() {
        $anime = $this->model->all();
        $message = $_GET['message'] ?? null;
        require __DIR__ . '/../../resources/views/anime/index.php';
    }

    // Show form for creating new anime
    public function create() {
        require __DIR__ . '/../../resources/views/anime/create.php';
    }

    // Store newly created anime
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'title' => $_POST['title'],
                'genre' => $_POST['genre'],
                'episodes' => $_POST['episodes'],
                'status' => $_POST['status'],
                'rating' => $_POST['rating']
            ];

            if ($this->model->create($data)) {
                header('Location: /anime?message=Anime created successfully');
                exit;
            }
        }
    }

    // Show form for editing anime
    public function edit($id) {
        $anime = $this->model->find($id);
        if (!$anime) {
            header('Location: /anime?message=Anime not found');
            exit;
        }
        require __DIR__ . '/../../resources/views/anime/edit.php';
    }

    // Update anime
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'title' => $_POST['title'],
                'genre' => $_POST['genre'],
                'episodes' => $_POST['episodes'],
                'status' => $_POST['status'],
                'rating' => $_POST['rating']
            ];

            if ($this->model->update($id, $data)) {
                header('Location: /anime?message=Anime updated successfully');
                exit;
            }
        }
    }

    // Delete anime
    public function destroy($id) {
        if ($this->model->delete($id)) {
            header('Location: /anime?message=Anime deleted successfully');
        } else {
            header('Location: /anime?message=Failed to delete anime');
        }
        exit;
    }
}
