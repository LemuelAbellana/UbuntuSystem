<?php
$title = 'Edit Anime';
ob_start();

// Create Livewire component with anime ID
require_once __DIR__ . '/../../../app/Livewire/AnimeForm.php';
$component = new \App\Livewire\AnimeForm($anime['id']);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $component->save();
}

// Render component
require $component->render();

$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
