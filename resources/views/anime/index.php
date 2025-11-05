<?php
$title = 'Anime List';
ob_start();
?>

<h1>Anime List</h1>

<?php if (isset($message)): ?>
    <div class="alert"><?php echo htmlspecialchars($message); ?></div>
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
        <?php if (empty($anime)): ?>
            <tr>
                <td colspan="7" style="text-align: center; padding: 20px;">No anime found</td>
            </tr>
        <?php else: ?>
            <?php foreach ($anime as $item): ?>
            <tr>
                <td><?php echo $item['id']; ?></td>
                <td><?php echo htmlspecialchars($item['title']); ?></td>
                <td><?php echo htmlspecialchars($item['genre']); ?></td>
                <td><?php echo $item['episodes']; ?></td>
                <td><?php echo $item['status']; ?></td>
                <td><?php echo $item['rating']; ?></td>
                <td>
                    <a href="/anime/<?php echo $item['id']; ?>/edit" class="btn btn-warning">Edit</a>
                    <a href="/anime/<?php echo $item['id']; ?>/delete"
                       class="btn btn-danger"
                       onclick="return confirm('Are you sure you want to delete this anime?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
