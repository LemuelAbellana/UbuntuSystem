<?php
session_start();
require_once __DIR__ . '/../../../app/Security/CSRF.php';
use App\Security\CSRF;

$title = 'Anime List';
ob_start();
?>

<div class="container">
    <h1>Anime List</h1>

    <?php if (isset($message)): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="actions">
        <a href="/anime/create" class="btn btn-primary">Add New Anime</a>
    </div>

    <?php if (empty($anime)): ?>
        <p>No anime found.</p>
    <?php else: ?>
        <table class="table">
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
                <?php foreach ($anime as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['id']); ?></td>
                        <td><?php echo htmlspecialchars($item['title']); ?></td>
                        <td><?php echo htmlspecialchars($item['genre']); ?></td>
                        <td><?php echo htmlspecialchars($item['episodes']); ?></td>
                        <td><?php echo htmlspecialchars($item['status']); ?></td>
                        <td><?php echo htmlspecialchars($item['rating']); ?></td>
                        <td>
                            <a href="/anime/<?php echo $item['id']; ?>/edit" class="btn btn-sm btn-warning">Edit</a>
                            <form action="/anime/<?php echo $item['id']; ?>/delete" method="POST" style="display: inline;">
                                <input type="hidden" name="csrf_token" value="<?php echo CSRF::generateToken(); ?>">
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this anime?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
