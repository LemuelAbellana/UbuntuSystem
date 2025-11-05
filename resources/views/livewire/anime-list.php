<div x-data="animeList()">
    <h1>Anime List</h1>

    <?php if ($this->message): ?>
        <div class="alert" x-show="true" x-init="setTimeout(() => $el.remove(), 3000)">
            <?php echo htmlspecialchars($this->message); ?>
        </div>
    <?php endif; ?>

    <div class="actions">
        <a href="/anime/create" class="btn btn-success">Add New Anime</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Genre</th>
                <th>Episodes</th>
                <th>Status</th>
                <th>Rating</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($this->anime)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px;">No anime found</td>
                </tr>
            <?php else: ?>
                <?php foreach ($this->anime as $item): ?>
                <tr x-transition>
                    <td><?php echo $item['id']; ?></td>
                    <td><?php echo htmlspecialchars($item['title']); ?></td>
                    <td><?php echo htmlspecialchars($item['genre']); ?></td>
                    <td><?php echo $item['episodes']; ?></td>
                    <td><span class="badge badge-<?php echo strtolower($item['status']); ?>"><?php echo $item['status']; ?></span></td>
                    <td><?php echo $item['rating']; ?></td>
                    <td>
                        <a href="/anime/<?php echo $item['id']; ?>/edit" class="btn btn-warning">Edit</a>
                        <a href="/anime/<?php echo $item['id']; ?>/delete"
                           class="btn btn-danger"
                           @click.prevent="deleteAnime(<?php echo $item['id']; ?>)">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
function animeList() {
    return {
        deleteAnime(id) {
            if (confirm('Are you sure you want to delete this anime?')) {
                window.location.href = '/anime/' + id + '/delete';
            }
        }
    }
}
</script>

<style>
.badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
}
.badge-ongoing { background-color: #3498db; color: white; }
.badge-completed { background-color: #27ae60; color: white; }
.badge-upcoming { background-color: #f39c12; color: white; }
</style>
