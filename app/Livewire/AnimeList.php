<?php

namespace App\Livewire;

use App\Models\Anime;

require_once __DIR__ . '/../Models/Anime.php';

class AnimeList {
    public $anime = [];
    public $message = '';

    public function __construct() {
        $this->loadAnime();
    }

    public function loadAnime() {
        $model = new Anime();
        $this->anime = $model->all();
    }

    public function delete($id) {
        $model = new Anime();
        if ($model->delete($id)) {
            $this->message = 'Anime deleted successfully';
            $this->loadAnime();
        } else {
            $this->message = 'Failed to delete anime';
        }
    }

    public function render() {
        return __DIR__ . '/../../resources/views/livewire/anime-list.php';
    }
}
