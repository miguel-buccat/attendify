<p align="center">
  <picture>
    <source media="(prefers-color-scheme: dark)" srcset="attendify-dark.png">
    <source media="(prefers-color-scheme: light)" srcset="attendify-light.png">
    <img src="attendify-dark.png" alt="Attendify Logo" width="500">
  </picture>
</p>
<p align="center">
  <em><b>Attendify</b> – An attendance monitoring system built for the 2026 DLSU-D SHS ICT Work Immersion Program at Erovoutika Robotics and Automation Solutions</em>
</p>

---

## Tech Stack

- **Backend**: PHP 8.4, Laravel 13
- **Frontend**: Vite 8, Tailwind CSS 4, Axios
- **Database**: PostgreSQL 17
- **Cache / Queues**: Redis 7, Laravel Queue Worker
- **Mail Testing**: Mailpit
- **Testing**: Pest 4, PHPUnit 12
- **Local Dev Orchestration**: Docker Compose


---

## Deployment

### Production

#### Prerequisites

Before deploying to production, make sure the target server has:

- PHP 8.4+ with required extensions for Laravel and PostgreSQL
- Composer 2+
- Node.js 22+ and npm
- PostgreSQL 17+
- Redis 7+
- Web server (Nginx or Apache)
- A process manager for workers (Supervisor or systemd)
- SSL certificate and domain configured

#### Deployment Steps

1. Clone the repository on your server.
2. Install PHP dependencies:

```bash
composer install --no-dev --optimize-autoloader
```

3. Install frontend dependencies and build assets:

```bash
npm ci
npm run build
```

4. Configure environment:

```bash
cp .env.example .env
php artisan key:generate
```

5. Update `.env` with production values (database, cache, mail, app URL, etc.).
6. Run database migrations:

```bash
php artisan migrate --force
```

7. Optimize Laravel caches:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

8. Start and monitor queue workers using your process manager.
9. Configure scheduled tasks to run every minute:

```cron
* * * * * cd /path/to/attendify && php artisan schedule:run >> /dev/null 2>&1
```

### Development

#### Prerequisites

For local containerized development, install:

- Docker Engine / Docker Desktop
- Docker Compose v2+

If you are using WSL2, ensure Docker Desktop WSL integration is enabled for your distro.

#### Local Development (Docker)

1. Create a local env file from the development template:

```bash
cp .env.development .env
```

2. Generate an app key:

```bash
docker compose run --rm app php artisan key:generate
```

3. Build and start all services:

```bash
docker compose up --build
```

4. Run migrations:

```bash
docker compose exec app php artisan migrate
```

5. Open services:
- App: http://localhost:8000
- Vite HMR: http://localhost:5173
- Mailpit: http://localhost:8025

#### Useful Development Commands

```bash
# Stop services
docker compose down

# Stop and remove volumes
docker compose down -v

# Run tests
docker compose exec app php artisan test --compact

# Follow app logs
docker compose logs -f app

# Run artisan command
docker compose exec app php artisan <command>

# Reset Attendify system
docker compose exec app php artisan app:dev-reset-site
```


## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
