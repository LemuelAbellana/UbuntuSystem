<?php
$title = 'Add New Anime';
ob_start();

// Create Livewire component
require_once __DIR__ . '/../../../app/Livewire/AnimeForm.php';
$component = new \App\Livewire\AnimeForm();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $component->save();
}

// Render component
require $component->render();

$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
