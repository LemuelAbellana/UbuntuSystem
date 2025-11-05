<?php

namespace App\Livewire;

use App\Models\Anime;

require_once __DIR__ . '/../Models/Anime.php';

class AnimeForm {
    public $animeId = null;
    public $title = '';
    public $genre = '';
    public $episodes = 0;
    public $status = 'Ongoing';
    public $rating = 0;
    public $errors = [];

    public function __construct($id = null) {
        if ($id) {
            $this->animeId = $id;
            $this->load($id);
        }
    }

    public function load($id) {
        $model = new Anime();
        $anime = $model->find($id);
        if ($anime) {
            $this->title = $anime['title'];
            $this->genre = $anime['genre'];
            $this->episodes = $anime['episodes'];
            $this->status = $anime['status'];
            $this->rating = $anime['rating'];
        }
    }

    public function save() {
        $data = [
            'title' => $_POST['title'] ?? $this->title,
            'genre' => $_POST['genre'] ?? $this->genre,
            'episodes' => $_POST['episodes'] ?? $this->episodes,
            'status' => $_POST['status'] ?? $this->status,
            'rating' => $_POST['rating'] ?? $this->rating
        ];

        $model = new Anime();

        if ($this->animeId) {
            $success = $model->update($this->animeId, $data);
            $message = 'Anime updated successfully';
        } else {
            $success = $model->create($data);
            $message = 'Anime created successfully';
        }

        if ($success) {
            header("Location: /anime?message=$message");
            exit;
        }
    }

    public function render() {
        return __DIR__ . '/../../resources/views/livewire/anime-form.php';
    }
}
