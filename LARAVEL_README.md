# Laravel-Style Anime CRUD System with Livewire

A simple CRUD system for managing anime, built following Laravel conventions with MySQL database, Composer autoloading, and Livewire-style reactive components.

## Features

- **Create**: Add new anime with title, genre, episodes, status, and rating
- **Read**: View all anime in a formatted table
- **Update**: Edit existing anime details
- **Delete**: Remove anime with confirmation
- **Composer**: PSR-4 autoloading for organized code structure
- **Livewire-Style Components**: Reactive UI with Alpine.js
- **Interactive UI**: Real-time form validation and smooth interactions

## Tech Stack

- PHP 8.x
- MySQL with PDO
- Composer (autoloading)
- Alpine.js (for reactivity)
- Livewire-style components
- MVC Architecture

## Project Structure

```
├── app/
│   ├── Controllers/
│   │   └── AnimeController.php    # Handles all CRUD operations
│   ├── Models/
│   │   └── Anime.php               # Database model
│   └── Livewire/
│       ├── AnimeList.php           # List component
│       └── AnimeForm.php           # Form component
├── database/
│   ├── Database.php                # Database connection class
│   └── migrations/
│       └── create_anime_table.sql  # Database schema
├── resources/
│   └── views/
│       ├── anime/
│       │   ├── layout.php          # Base layout with Alpine.js
│       │   ├── index.php           # List page
│       │   ├── create.php          # Create page
│       │   └── edit.php            # Edit page
│       └── livewire/
│           ├── anime-list.php      # List component view
│           └── anime-form.php      # Form component view
├── routes/
│   └── web.php                     # Application routes
├── public/
│   ├── index.php                   # Entry point
│   └── .htaccess                   # URL rewriting
├── composer.json                   # Composer configuration
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
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 2. Install Dependencies

Run Composer to generate autoload files:
```bash
composer install
```

Or if you have composer.phar:
```bash
php composer.phar install
```

### 3. Create Database

Import the migration file:
```bash
mysql -u root -p < database/migrations/create_anime_table.sql
```

Or run [test_connection.php](test_connection.php) to automatically create the database:
```bash
php test_connection.php
```

### 4. Start Server

**Option A: PHP Built-in Server**
```bash
cd public
php -S localhost:8000
```
Then access: `http://localhost:8000/anime`

**Option B: Apache/XAMPP**
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

## Livewire Components

### AnimeList Component
[app/Livewire/AnimeList.php](app/Livewire/AnimeList.php)
- Displays all anime in a table
- Handles delete actions with confirmation
- Shows success/error messages
- Uses Alpine.js for interactivity

### AnimeForm Component
[app/Livewire/AnimeForm.php](app/Livewire/AnimeForm.php)
- Handles both create and edit forms
- Real-time form data binding with Alpine.js
- Loads existing data for editing
- Validates and saves data

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
- **View** ([resources/views/](resources/views/)) - UI templates with Alpine.js
- **Controller** ([app/Controllers/AnimeController.php](app/Controllers/AnimeController.php)) - Request handling logic
- **Livewire Components** ([app/Livewire/](app/Livewire/)) - Reactive component logic

## Alpine.js Integration

The system uses Alpine.js (CDN) for reactive UI components:
- Form data binding with `x-model`
- Click events with `@click`
- Transitions with `x-transition`
- Component initialization with `x-data`

No build process required - Alpine.js loads from CDN!
