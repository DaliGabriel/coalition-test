# Task Manager

A task management application built with Laravel 11. Tasks can be created, edited, deleted, and reordered via drag-and-drop. An optional project filter lets you focus on one project at a time.

---

## Requirements

### With Docker (recommended)

| Requirement | Version |
|-------------|---------|
| Docker      | 24+     |
| Docker Compose | v2+  |

### Without Docker

| Requirement | Version |
|-------------|---------|
| PHP         | 8.3+    |
| Composer    | 2.x     |
| Node.js     | 18+     |
| MySQL       | 8.0+    |

---

## Quick Start with Docker

```bash
# 1. Clone and enter the project
git clone https://github.com/DaliGabriel/coalition-test.git && cd coalition-test

# 2. Configure environment
cp .env.example .env

# 3. Build and start containers
docker compose up -d --build

# 4. Install dependencies and run migrations
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

The application will be available at <http://localhost:8080> (set `APP_PORT=8080` in `.env` to override the default port 80).

Update your `.env` to match the Docker database service:

```dotenv
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=coalition_test
DB_USERNAME=laravel
DB_PASSWORD=secret
```

---

## Local Installation (without Docker)

### 1. Install dependencies

```bash
composer install
npm install
```

### 2. Configure the environment

```bash
cp .env.example .env
```

Set your database credentials in `.env`:

```dotenv
DB_DATABASE=coalition_test
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
```

### 3. Generate the application key

```bash
php artisan key:generate
```

### 4. Run migrations and seed sample data

```bash
php artisan migrate
php artisan db:seed
```

This creates three starter projects: **Personal**, **Work**, and **Home**.

---

## Running the Application

```bash
composer run dev
```

Starts the development server, queue worker, and Vite concurrently. The application will be available at <http://localhost:8000>.

---

## Running the Tests

```bash
composer run test
```

Uses an in-memory SQLite database — no additional configuration required.

```
Tests:    21 passed (43 assertions)
Duration: ~0.81s
```

Tests also run automatically on every push and pull request via the CI pipeline.

---

## Usage

- **Add a task** — Enter a name, optionally pick a project, and click **Add Task**. The task is appended with the next available priority number.
- **Edit a task** — Hover over a task and click **Edit** to update its name or project inline.
- **Delete a task** — Hover over a task and click **Delete**. A confirmation prompt prevents accidental deletion.
- **Reorder tasks** — Drag the ↕ handle to a new position. Priorities are saved automatically via a background request.
- **Filter by project** — Choose a project from the dropdown. The task list and the "Add Task" form both respect the active filter.

---

## Architecture

### Backend: Controller → Service → Repository

```
app/
  Http/
    Controllers/
      TaskController.php          — thin controller, delegates to TaskService
    Requests/
      Task/
        IndexTaskRequest.php      — project filter validation + projectId() helper
        StoreTaskRequest.php      — create validation
        UpdateTaskRequest.php     — update validation
        ReorderTasksRequest.php   — reorder payload validation
  Services/
    TaskService.php               — business logic (priority, reorder, CRUD)
  Repositories/
    TaskRepository.php            — all Task Eloquent queries
    ProjectRepository.php         — all Project Eloquent queries
  Models/
    Task.php                      — belongsTo Project
    Project.php                   — hasMany Tasks (ordered by priority)
```

### Frontend: Blade Components

```
resources/
  views/
    components/
      layouts/
        app.blade.php             — base HTML layout, CDN scripts
      page-header.blade.php       — reusable page heading
      flash-messages.blade.php    — success / error banners
      project-filter.blade.php    — project dropdown filter
      project-select.blade.php    — reusable <select> (DRY)
      task-form.blade.php         — create task form
      task-list.blade.php         — sortable task list (Alpine + SortableJS)
      task-item.blade.php         — single task row with inline edit
    tasks/
      index.blade.php             — page orchestrator (~15 lines)
  public/
    js/
      app.js                      — taskSorter() Alpine component
```

### Database Schema

**projects**

| Column     | Type         |
|------------|--------------|
| id         | bigint (PK)  |
| name       | varchar(255) |
| created_at | timestamp    |
| updated_at | timestamp    |

**tasks**

| Column     | Type              | Notes                             |
|------------|-------------------|-----------------------------------|
| id         | bigint (PK)       |                                   |
| name       | varchar(255)      |                                   |
| priority   | unsigned int      | Lower = higher priority           |
| project_id | bigint (FK, null) | References projects(id), cascades |
| created_at | timestamp         |                                   |
| updated_at | timestamp         |                                   |

### Key Design Decisions

- **Priority assignment** — new tasks receive `max(priority) + 1` within their project scope, avoiding gaps or table locks.
- **Drag-and-drop reorder** — SortableJS sends a single AJAX POST with the complete ordered ID list; the backend assigns `priority = position + 1` in one pass.
- **Eager loading** — `Task::with('project')` on the index query prevents N+1 queries when rendering project names.
- **Alpine.js for interactivity** — inline edit toggle uses `x-data="{ editing: false }"` instead of manual DOM manipulation, keeping Blade components declarative.
- **Static JS file** — `taskSorter()` lives in `public/js/app.js`; the reorder URL is passed via `data-reorder-url` on the element, keeping Blade expressions out of JS files.

### Frontend Libraries (CDN, no build step)

| Library      | Purpose                       |
|--------------|-------------------------------|
| Tailwind CSS | Utility-first styling         |
| Alpine.js    | Reactive inline interactivity |
| SortableJS   | Drag-and-drop list reordering |

---

## CI/CD

### Continuous Integration (GitHub Actions)

Every push and pull request to `main` or `develop` automatically:

1. Installs PHP 8.3 and Composer dependencies
2. Copies `.env.example` to `.env` and generates an application key
3. Runs the full test suite against an in-memory SQLite database

See [`.github/workflows/ci.yml`](.github/workflows/ci.yml).

### Continuous Deployment

> CD pipeline coming soon — triggered on merge to `main`, deploys to VPS via SSH.

---

## Docker

```
Dockerfile                  — PHP 8.3 FPM (Alpine)
docker-compose.yml          — app (PHP-FPM) + nginx + db (MySQL 8)
docker/nginx/default.conf   — Nginx virtual host config
.dockerignore               — excludes vendor, node_modules, logs
```

Useful commands:

```bash
docker compose up -d            # start all services in background
docker compose exec app bash    # shell into the PHP container
docker compose logs -f          # follow logs
docker compose down -v          # stop and remove volumes
```

---

## Production Deployment

1. Set `APP_ENV=production` and `APP_DEBUG=false` in `.env`.
2. Run `php artisan config:cache` and `php artisan route:cache`.
3. Point your web server document root to the `public/` directory.
4. Ensure `storage/` and `bootstrap/cache/` are writable by the web server.
