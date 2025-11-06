<?php
session_start();
require_once __DIR__ . '/../../../app/Security/CSRF.php';
use App\Security\CSRF;

$title = 'Login';
ob_start();
?>

<div class="container" style="max-width: 400px; margin: 100px auto;">
    <h1 style="text-align: center;">Login</h1>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger" style="background-color: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 20px; border-radius: 4px;">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form action="/login" method="POST" style="background: #f9f9f9; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <input type="hidden" name="csrf_token" value="<?php echo CSRF::generateToken(); ?>">

        <div class="form-group" style="margin-bottom: 20px;">
            <label for="username" style="display: block; margin-bottom: 5px; font-weight: bold;">Username</label>
            <input type="text" id="username" name="username" required autofocus style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label for="password" style="display: block; margin-bottom: 5px; font-weight: bold;">Password</label>
            <input type="password" id="password" name="password" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px;">Login</button>
        </div>
    </form>

    <p style="text-align: center; margin-top: 20px; color: #666; font-size: 14px;">
        Default credentials: <strong>admin / admin123</strong>
    </p>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
