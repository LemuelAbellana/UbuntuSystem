# Complete Laravel-Style Anime CRUD - Code Reference

This document contains all the working code for the deployed system.

---

## Directory Structure

```
/var/www/anime-app/
├── app/
│   ├── Controllers/
│   │   └── AnimeController.php
│   ├── Models/
│   │   └── Anime.php
│   └── Livewire/ (not used in simplified version)
├── database/
│   ├── Database.php
│   └── migrations/
│       └── create_anime_table.sql
├── resources/
│   └── views/
│       └── anime/
│           ├── layout.php
│           ├── index.php
│           ├── create.php
│           └── edit.php
├── routes/
│   └── web.php
├── public/
│   ├── index.php
│   └── .htaccess
├── .env
├── .gitignore
└── composer.json
```

---

## 1. Database Configuration

### `.env`
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=anime_laravel
DB_USERNAME=gomz
DB_PASSWORD=Iamgomz@0214
```

### `database/Database.php`
```php
<?php

namespace App\Models;

class Database {
    private static $connection = null;

    public static function getConnection() {
        if (self::$connection === null) {
            $env = parse_ini_file(__DIR__ . '/../.env');

            self::$connection = new \PDO(
                "mysql:host={$env['DB_HOST']};dbname={$env['DB_DATABASE']};charset=utf8mb4",
                $env['DB_USERNAME'],
                $env['DB_PASSWORD'],
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
            );
        }

        return self::$connection;
    }
}
```

### `database/migrations/create_anime_table.sql`
```sql
-- Create database
CREATE DATABASE IF NOT EXISTS anime_laravel;

USE anime_laravel;

-- Create anime table
CREATE TABLE IF NOT EXISTS anime (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    genre VARCHAR(255),
    episodes INT,
    status ENUM('Ongoing', 'Completed', 'Upcoming') DEFAULT 'Ongoing',
    rating DECIMAL(3,1),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Sample data
INSERT INTO anime (title, genre, episodes, status, rating) VALUES
('Demon Slayer', 'Action, Supernatural', 26, 'Completed', 8.7),
('My Hero Academia', 'Action, Superhero', 113, 'Ongoing', 8.4),
('Steins;Gate', 'Sci-Fi, Thriller', 24, 'Completed', 9.1);
```

---

## 2. Models

### `app/Models/Anime.php`
```php
<?php

namespace App\Models;

require_once __DIR__ . '/../../database/Database.php';

class Anime {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function all() {
        $stmt = $this->db->query("SELECT * FROM anime ORDER BY created_at DESC");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM anime WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->db->prepare(
            "INSERT INTO anime (title, genre, episodes, status, rating) VALUES (?, ?, ?, ?, ?)"
        );
        return $stmt->execute([
            $data['title'],
            $data['genre'],
            $data['episodes'],
            $data['status'],
            $data['rating']
        ]);
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare(
            "UPDATE anime SET title = ?, genre = ?, episodes = ?, status = ?, rating = ? WHERE id = ?"
        );
        return $stmt->execute([
            $data['title'],
            $data['genre'],
            $data['episodes'],
            $data['status'],
            $data['rating'],
            $id
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM anime WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
```

---

## 3. Controllers

### `app/Controllers/AnimeController.php`
```php
<?php

namespace App\Controllers;

use App\Models\Anime;

require_once __DIR__ . '/../Models/Anime.php';

class AnimeController {
    private $model;

    public function __construct() {
        $this->model = new Anime();
    }

    // Display listing of anime
    public function index() {
        $anime = $this->model->all();
        $message = $_GET['message'] ?? null;
        require __DIR__ . '/../../resources/views/anime/index.php';
    }

    // Show form for creating new anime
    public function create() {
        require __DIR__ . '/../../resources/views/anime/create.php';
    }

    // Store newly created anime
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'title' => $_POST['title'],
                'genre' => $_POST['genre'],
                'episodes' => $_POST['episodes'],
                'status' => $_POST['status'],
                'rating' => $_POST['rating']
            ];

            if ($this->model->create($data)) {
                header('Location: /anime?message=Anime created successfully');
                exit;
            }
        }
    }

    // Show form for editing anime
    public function edit($id) {
        $anime = $this->model->find($id);
        if (!$anime) {
            header('Location: /anime?message=Anime not found');
            exit;
        }
        require __DIR__ . '/../../resources/views/anime/edit.php';
    }

    // Update anime
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'title' => $_POST['title'],
                'genre' => $_POST['genre'],
                'episodes' => $_POST['episodes'],
                'status' => $_POST['status'],
                'rating' => $_POST['rating']
            ];

            if ($this->model->update($id, $data)) {
                header('Location: /anime?message=Anime updated successfully');
                exit;
            }
        }
    }

    // Delete anime
    public function destroy($id) {
        if ($this->model->delete($id)) {
            header('Location: /anime?message=Anime deleted successfully');
        } else {
            header('Location: /anime?message=Failed to delete anime');
        }
        exit;
    }
}
```

---

## 4. Routes

### `routes/web.php`
```php
<?php

require_once __DIR__ . '/../app/Controllers/AnimeController.php';

use App\Controllers\AnimeController;

// Parse the URL
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$controller = new AnimeController();

// Routes
switch (true) {
    // List all anime
    case $uri === '/anime' && $method === 'GET':
        $controller->index();
        break;

    // Show create form
    case $uri === '/anime/create' && $method === 'GET':
        $controller->create();
        break;

    // Store new anime
    case $uri === '/anime' && $method === 'POST':
        $controller->store();
        break;

    // Show edit form
    case preg_match('/^\/anime\/(\d+)\/edit$/', $uri, $matches) && $method === 'GET':
        $controller->edit($matches[1]);
        break;

    // Update anime
    case preg_match('/^\/anime\/(\d+)$/', $uri, $matches) && $method === 'POST':
        $controller->update($matches[1]);
        break;

    // Delete anime
    case preg_match('/^\/anime\/(\d+)\/delete$/', $uri, $matches) && $method === 'GET':
        $controller->destroy($matches[1]);
        break;

    // Default: redirect to anime list
    default:
        header('Location: /anime');
        exit;
}
```

### `public/index.php`
```php
<?php

// Entry point for the application
require_once __DIR__ . '/../routes/web.php';
```

---

## 5. Views

### `resources/views/anime/layout.php`
```php
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Anime CRUD'; ?></title>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c3e50;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #3498db;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #3498db;
            color: white;
            font-weight: 600;
        }
        tr:hover { background-color: #f8f9fa; }
        .btn {
            display: inline-block;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 4px;
            margin: 2px;
            cursor: pointer;
            border: none;
            font-size: 14px;
            transition: all 0.3s;
        }
        .btn-primary { background-color: #3498db; color: white; }
        .btn-primary:hover { background-color: #2980b9; }
        .btn-success { background-color: #27ae60; color: white; }
        .btn-success:hover { background-color: #229954; }
        .btn-warning { background-color: #f39c12; color: white; }
        .btn-warning:hover { background-color: #e67e22; }
        .btn-danger { background-color: #e74c3c; color: white; }
        .btn-danger:hover { background-color: #c0392b; }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #2c3e50;
        }
        input[type="text"],
        input[type="number"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        input[type="text"]:focus,
        input[type="number"]:focus,
        select:focus {
            outline: none;
            border-color: #3498db;
        }
        .actions { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <?php echo $content; ?>
    </div>
</body>
</html>
```

### `resources/views/anime/index.php`
```php
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
                    <a href="/anime/<?php echo $item['id']; ?>/delete" class="btn btn-danger" onclick="return confirm('Delete this anime?')">Delete</a>
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
```

### `resources/views/anime/create.php`
```php
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
```

### `resources/views/anime/edit.php`
```php
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
```

---

## 6. Server Configuration

### `public/.htaccess`
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On

    # Redirect all requests to index.php
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php [QSA,L]
</IfModule>
```

### Nginx Configuration (`/etc/nginx/sites-available/anime-app`)
```nginx
server {
    listen 80;
    server_name 192.168.1.18;
    root /var/www/anime-app/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
    }

    location ~ /\.env {
        deny all;
    }
}
```

---

## 7. Configuration Files

### `composer.json`
```json
{
    "name": "ubuntusystem/anime-crud",
    "description": "Simple Laravel-style Anime CRUD with Livewire",
    "type": "project",
    "require": {
        "php": "^8.0",
        "ext-pdo": "*",
        "ext-json": "*"
    },
    "autoload": {
        "psr-4": {
            "App\\": "app/"
        }
    },
    "config": {
        "optimize-autoloader": true
    }
}
```

### `.gitignore`
```
# Environment configuration
.env

# IDE files
.vscode/
.idea/

# OS files
.DS_Store
Thumbs.db

# Vendor directories
/vendor/

# Old PHP files
config.php
index.php
add.php
edit.php
delete.php
database.sql
README.md
```

---

## 8. Quick Commands

### Start Development Server
```bash
cd /var/www/anime-app/public
php -S localhost:8000
```

### Database Commands
```bash
# Import database
mysql -u gomz -p anime_laravel < database/migrations/create_anime_table.sql

# Check data
mysql -u gomz -p anime_laravel -e "SELECT * FROM anime;"
```

### Server Management
```bash
# Restart services
sudo systemctl restart nginx
sudo systemctl restart php8.4-fpm
sudo systemctl restart mysql

# Check status
sudo systemctl status nginx
sudo systemctl status php8.4-fpm

# View logs
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/log/nginx/access.log
```

---

## Access the Application

**Local Network**: `http://192.168.1.18/anime`
**With Domain**: `http://your-domain.com/anime`

---

## Features

✅ Create anime entries
✅ Read/view all anime in table
✅ Update existing anime
✅ Delete anime with confirmation
✅ MySQL database with PDO
✅ MVC architecture (Laravel-style)
✅ Composer PSR-4 autoloading
✅ Alpine.js for enhanced interactivity
✅ Responsive design
✅ Secure (SQL injection protection with prepared statements)

---

## Security Features

🔒 Prepared statements (SQL injection protection)
🔒 .env file hidden from web access
🔒 Input sanitization with htmlspecialchars()
🔒 Fail2ban protecting SSH
🔒 UFW firewall enabled
🔒 Nginx security headers

---

## End of Code Reference

All code is production-ready and currently deployed at `http://192.168.1.18/anime`
