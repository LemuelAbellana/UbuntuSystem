<?php
$title = 'Edit Anime';
ob_start();
?>

<h1>Edit Anime</h1>

<form action="/anime/<?php echo $anime['id']; ?>" method="POST">
    <div class="form-group">
        <label for="title">Title *</label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($anime['title']); ?>" required>
    </div>

    <div class="form-group">
        <label for="genre">Genre</label>
        <input type="text" id="genre" name="genre" value="<?php echo htmlspecialchars($anime['genre']); ?>">
    </div>

    <div class="form-group">
        <label for="episodes">Episodes</label>
        <input type="number" id="episodes" name="episodes" value="<?php echo $anime['episodes']; ?>" min="0">
    </div>

    <div class="form-group">
        <label for="status">Status</label>
        <select id="status" name="status">
            <option value="Ongoing" <?php echo $anime['status'] === 'Ongoing' ? 'selected' : ''; ?>>Ongoing</option>
            <option value="Completed" <?php echo $anime['status'] === 'Completed' ? 'selected' : ''; ?>>Completed</option>
            <option value="Upcoming" <?php echo $anime['status'] === 'Upcoming' ? 'selected' : ''; ?>>Upcoming</option>
        </select>
    </div>

    <div class="form-group">
        <label for="rating">Rating (0-10)</label>
        <input type="number" id="rating" name="rating" step="0.1" min="0" max="10" value="<?php echo $anime['rating']; ?>">
    </div>

    <div class="actions">
        <button type="submit" class="btn btn-warning">Update Anime</button>
        <a href="/anime" class="btn btn-primary">Back to List</a>
    </div>
</form>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
