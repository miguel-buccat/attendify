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

Both staging and production use the same multi-stage `Dockerfile` (PHP-FPM + Nginx + Supervisor) and differ only in compose-level environment values. They are designed for deployment via **Coolify** (or any Docker Compose-based host).

### Architecture

Each environment runs **5 containers** from two compose files:

| Container | Image | Role |
|-----------|-------|------|
| `app` | Custom (Dockerfile) | PHP-FPM + Nginx serving the application on port **8080** |
| `queue` | Custom (Dockerfile) | Laravel queue worker |
| `scheduler` | Custom (Dockerfile) | Laravel task scheduler (`schedule:work`) |
| `postgres` | postgres:17-alpine | PostgreSQL database |
| `redis` | redis:7-alpine | Cache store |

A shared **`app-storage`** Docker volume is mounted at `/var/www/html/storage` across `app`, `queue`, and `scheduler` so uploaded files (avatars, banners, site settings) persist and are accessible by all services.

### Environment Variables

Set these through the **Coolify UI** (or a `.env` file next to the compose file). Variables marked **required** have no default and must be provided.

#### Required

| Variable | Description |
|----------|-------------|
| `APP_KEY` | Laravel encryption key. Generate with `php artisan key:generate --show` |
| `APP_URL` | Full public URL (e.g. `https://attendify.example.com`) |
| `DB_PASSWORD` | PostgreSQL password |
| `MAIL_HOST` | SMTP server hostname |
| `MAIL_USERNAME` | SMTP username |
| `MAIL_PASSWORD` | SMTP password |
| `MAIL_FROM_ADDRESS` | Sender email address (e.g. `noreply@attendify.example.com`) |

#### Optional (have defaults)

| Variable | Default | Description |
|----------|---------|-------------|
| `APP_NAME` | `Attendify` | Application display name |
| `APP_PORT` | `8080` | Host port mapped to the app container |
| `DB_DATABASE` | `attendify` | PostgreSQL database name |
| `DB_USERNAME` | `attendify` | PostgreSQL username |
| `BCRYPT_ROUNDS` | `12` | Password hashing cost |
| `LOG_LEVEL` | `warning` | Log verbosity (`debug`, `info`, `warning`, `error`) |
| `SESSION_DOMAIN` | `null` | Cookie domain for sessions |
| `REDIS_PASSWORD` | `null` | Redis authentication password (omit or set to `null` to disable) |
| `MAIL_MAILER` | `smtp` | Mail driver |
| `MAIL_PORT` | `587` | SMTP port |
| `MAIL_ENCRYPTION` | `tls` | SMTP encryption (`tls`, `ssl`, or `null`) |
| `MAIL_FROM_NAME` | `Attendify` | Sender display name |

### Staging

Uses `docker-compose.staging.yml`. Identical to production except `APP_DEBUG` is `true` for easier troubleshooting.

```bash
docker compose -f docker-compose.staging.yml up -d --build
```

### Production

Uses `docker-compose.prod.yml`. Debug is disabled, sessions are encrypted, and caches are optimized on startup.

```bash
docker compose -f docker-compose.prod.yml up -d --build
```

### What Happens on Startup

The entrypoint script (`docker/production/entrypoint.sh`) runs automatically on every deploy:

1. Creates storage directories for uploads (`avatars/`, `banners/`, `site-settings/`, `site-assets/`)
2. Sets correct permissions (`www-data`, `775`)
3. Creates the `public/storage` symlink
4. Caches config, routes, views, and events
5. Runs `php artisan migrate --force`

### Coolify Setup

1. Create a new **Docker Compose** resource in Coolify
2. Point it to the repository and select the appropriate compose file:
   - Staging: `docker-compose.staging.yml`
   - Production: `docker-compose.prod.yml`
3. Add the required environment variables in the Coolify UI
4. Set the exposed port to **8080** (or match your `APP_PORT` value)
5. Configure Coolify's reverse proxy to route your domain to the app container on port 8080
6. Deploy

### Persistent Volumes

| Volume | Mount Point | Purpose |
|--------|-------------|---------|
| `app-storage` | `/var/www/html/storage` | User uploads, logs, framework cache |
| `pgsql-data` | `/var/lib/postgresql/data` | PostgreSQL data |
| `redis-data` | `/data` | Redis persistence |

> **Backup note:** Back up the `app-storage` and `pgsql-data` volumes regularly. User-uploaded avatars, banners, and site assets live in `app-storage`.

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
