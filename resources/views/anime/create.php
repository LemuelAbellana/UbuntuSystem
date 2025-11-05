<?php
$title = 'Add New Anime';
ob_start();
?>

<h1>Add New Anime</h1>

<form action="/anime" method="POST">
    <div class="form-group">
        <label for="title">Title *</label>
        <input type="text" id="title" name="title" required>
    </div>

    <div class="form-group">
        <label for="genre">Genre</label>
        <input type="text" id="genre" name="genre" placeholder="e.g., Action, Adventure">
    </div>

    <div class="form-group">
        <label for="episodes">Episodes</label>
        <input type="number" id="episodes" name="episodes" min="0">
    </div>

    <div class="form-group">
        <label for="status">Status</label>
        <select id="status" name="status">
            <option value="Ongoing">Ongoing</option>
            <option value="Completed">Completed</option>
            <option value="Upcoming">Upcoming</option>
        </select>
    </div>

    <div class="form-group">
        <label for="rating">Rating (0-10)</label>
        <input type="number" id="rating" name="rating" step="0.1" min="0" max="10">
    </div>

    <div class="actions">
        <button type="submit" class="btn btn-success">Create Anime</button>
        <a href="/anime" class="btn btn-primary">Back to List</a>
    </div>
</form>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
