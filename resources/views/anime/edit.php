<?php
session_start();
require_once __DIR__ . '/../../../app/Security/CSRF.php';
use App\Security\CSRF;

$title = 'Edit Anime';
ob_start();
?>

<div class="container">
    <h1>Edit Anime</h1>

    <form action="/anime/<?php echo $anime['id']; ?>" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo CSRF::generateToken(); ?>">
        <input type="hidden" name="_method" value="PUT">

        <div class="form-group">
            <label for="title">Title *</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($anime['title']); ?>" required maxlength="255">
        </div>

        <div class="form-group">
            <label for="genre">Genre</label>
            <input type="text" id="genre" name="genre" value="<?php echo htmlspecialchars($anime['genre']); ?>" maxlength="255">
        </div>

        <div class="form-group">
            <label for="episodes">Episodes</label>
            <input type="number" id="episodes" name="episodes" value="<?php echo htmlspecialchars($anime['episodes']); ?>" min="0">
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
            <input type="number" id="rating" name="rating" value="<?php echo htmlspecialchars($anime['rating']); ?>" step="0.1" min="0" max="10">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update Anime</button>
            <a href="/anime" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
