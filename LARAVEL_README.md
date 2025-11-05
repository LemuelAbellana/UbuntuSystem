# Laravel-Style Anime CRUD System

A simple CRUD system for managing anime, built following Laravel conventions with MySQL database.

## Features

- **Create**: Add new anime with title, genre, episodes, status, and rating
- **Read**: View all anime in a formatted table
- **Update**: Edit existing anime details
- **Delete**: Remove anime with confirmation

## Project Structure

```
├── app/
│   ├── Controllers/
│   │   └── AnimeController.php    # Handles all CRUD operations
│   └── Models/
│       └── Anime.php               # Database model
├── database/
│   ├── Database.php                # Database connection class
│   └── migrations/
│       └── create_anime_table.sql  # Database schema
├── resources/
│   └── views/
│       └── anime/
│           ├── layout.php          # Base layout template
│           ├── index.php           # List all anime
│           ├── create.php          # Create form
│           └── edit.php            # Edit form
├── routes/
│   └── web.php                     # Application routes
├── public/
│   ├── index.php                   # Entry point
│   └── .htaccess                   # URL rewriting
└── .env                            # Environment configuration
```

## Setup Instructions

### 1. Configure Database

Edit [.env](.env) file with your MySQL credentials:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=anime_laravel
DB_USERNAME=root
DB_PASSWORD=
```

### 2. Create Database

Import the migration file:
```bash
mysql -u root -p < database/migrations/create_anime_table.sql
```

Or manually run the SQL in phpMyAdmin/MySQL Workbench.

### 3. Configure Web Server

**Option A: Using PHP Built-in Server**
```bash
cd public
php -S localhost:8000
```
Then access: `http://localhost:8000/anime`

**Option B: Using Apache/XAMPP**
- Place project in `htdocs` folder
- Set document root to `public` directory
- Access: `http://localhost/anime`

## Routes

| Method | URI                    | Action                |
|--------|------------------------|-----------------------|
| GET    | `/anime`               | List all anime        |
| GET    | `/anime/create`        | Show create form      |
| POST   | `/anime`               | Store new anime       |
| GET    | `/anime/{id}/edit`     | Show edit form        |
| POST   | `/anime/{id}`          | Update anime          |
| GET    | `/anime/{id}/delete`   | Delete anime          |

## Database Schema

**anime** table:
- `id` - Primary key (auto-increment)
- `title` - Anime title (required)
- `genre` - Genre(s)
- `episodes` - Number of episodes
- `status` - Ongoing/Completed/Upcoming
- `rating` - Rating (0-10)
- `created_at` - Creation timestamp
- `updated_at` - Last update timestamp

## MVC Architecture

Following Laravel conventions:

- **Model** ([app/Models/Anime.php](app/Models/Anime.php)) - Handles database operations
- **View** ([resources/views/anime/](resources/views/anime/)) - UI templates
- **Controller** ([app/Controllers/AnimeController.php](app/Controllers/AnimeController.php)) - Request handling logic

## Technologies

- PHP 8.x
- MySQL
- PDO for database interaction
- MVC Architecture (Laravel-style)
