<?php
session_start();
require_once __DIR__ . '/../../../app/Security/CSRF.php';
use App\Security\CSRF;

$title = 'Add New Anime';
ob_start();
?>

<div class="container">
    <h1>Add New Anime</h1>

    <form action="/anime" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo CSRF::generateToken(); ?>">

        <div class="form-group">
            <label for="title">Title *</label>
            <input type="text" id="title" name="title" required maxlength="255">
        </div>

        <div class="form-group">
            <label for="genre">Genre</label>
            <input type="text" id="genre" name="genre" maxlength="255">
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

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Add Anime</button>
            <a href="/anime" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
