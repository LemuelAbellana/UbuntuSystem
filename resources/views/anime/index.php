<?php
$title = 'Anime List';
ob_start();

// Create Livewire component
require_once __DIR__ . '/../../../app/Livewire/AnimeList.php';
$component = new \App\Livewire\AnimeList();
if (isset($message)) {
    $component->message = $message;
}

// Render component
require $component->render();

$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
