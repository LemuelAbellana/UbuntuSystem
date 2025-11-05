<div x-data="animeForm()">
    <h1><?php echo $this->animeId ? 'Edit' : 'Add New'; ?> Anime</h1>

    <form @submit.prevent="submitForm" method="POST">
        <div class="form-group">
            <label for="title">Title *</label>
            <input type="text"
                   id="title"
                   name="title"
                   value="<?php echo htmlspecialchars($this->title); ?>"
                   x-model="title"
                   required>
        </div>

        <div class="form-group">
            <label for="genre">Genre</label>
            <input type="text"
                   id="genre"
                   name="genre"
                   value="<?php echo htmlspecialchars($this->genre); ?>"
                   x-model="genre"
                   placeholder="e.g., Action, Adventure">
        </div>

        <div class="form-group">
            <label for="episodes">Episodes</label>
            <input type="number"
                   id="episodes"
                   name="episodes"
                   value="<?php echo $this->episodes; ?>"
                   x-model="episodes"
                   min="0">
        </div>

        <div class="form-group">
            <label for="status">Status</label>
            <select id="status" name="status" x-model="status">
                <option value="Ongoing" <?php echo $this->status === 'Ongoing' ? 'selected' : ''; ?>>Ongoing</option>
                <option value="Completed" <?php echo $this->status === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                <option value="Upcoming" <?php echo $this->status === 'Upcoming' ? 'selected' : ''; ?>>Upcoming</option>
            </select>
        </div>

        <div class="form-group">
            <label for="rating">Rating (0-10)</label>
            <input type="number"
                   id="rating"
                   name="rating"
                   value="<?php echo $this->rating; ?>"
                   x-model="rating"
                   step="0.1"
                   min="0"
                   max="10">
            <small>Current: <span x-text="rating"></span></small>
        </div>

        <div class="actions">
            <button type="submit" class="btn <?php echo $this->animeId ? 'btn-warning' : 'btn-success'; ?>">
                <?php echo $this->animeId ? 'Update' : 'Create'; ?> Anime
            </button>
            <a href="/anime" class="btn btn-primary">Back to List</a>
        </div>
    </form>
</div>

<script>
function animeForm() {
    return {
        title: '<?php echo addslashes($this->title); ?>',
        genre: '<?php echo addslashes($this->genre); ?>',
        episodes: <?php echo $this->episodes ?: 0; ?>,
        status: '<?php echo $this->status; ?>',
        rating: <?php echo $this->rating ?: 0; ?>,

        submitForm(event) {
            event.target.submit();
        }
    }
}
</script>
