# Attendify — Technical Documentation

> Architecture reference, database schema, API routes, and deployment guide.

---

## Table of Contents

1. [Technology Stack](#technology-stack)
2. [Architecture Overview](#architecture-overview)
3. [Database Schema](#database-schema)
4. [Route Reference](#route-reference)
5. [Key Services & Jobs](#key-services--jobs)
6. [Background Queue & Scheduler](#background-queue--scheduler)
7. [Authentication & Authorisation](#authentication--authorisation)
8. [File Storage](#file-storage)
9. [Local Development](#local-development)
10. [Production Deployment](#production-deployment)
11. [Testing](#testing)

---

## Technology Stack

| Layer | Technology | Version |
|-------|-----------|---------|
| Backend | Laravel / PHP | 13 / 8.4 |
| Database | PostgreSQL | 17 |
| Cache / Queue / Sessions | Redis | 7 |
| Frontend Bundler | Vite | 8 |
| CSS Framework | Tailwind CSS | 4 |
| UI Components | DaisyUI | 5 |
| Testing | Pest / PHPUnit | 4 / 12 |
| Containerisation | Docker Compose | — |

---

## Architecture Overview

```
┌─────────────────────────────────────────────────────┐
│  Browser / Mobile                                   │
│  Tailwind CSS v4 + DaisyUI v5                       │
│  Vite (bundler) · Chart.js (analytics)              │
└───────────────────────────┬─────────────────────────┘
                            │ HTTPS
┌───────────────────────────▼─────────────────────────┐
│  Laravel 13 (PHP 8.4)                               │
│  ┌──────────┐  ┌──────────┐  ┌────────────────────┐ │
│  │ Web      │  │ API /    │  │ Console Commands   │ │
│  │ Routes   │  │ JSON     │  │ & Scheduler        │ │
│  └──────────┘  └──────────┘  └────────────────────┘ │
│  Eloquent ORM · Policies · Form Requests            │
│  Notifications (mail + database) · Jobs (queued)    │
└────────────────────────┬────────────────────────────┘
              ┌──────────┴──────────┐
              │                     │
┌─────────────▼──────┐  ┌──────────▼──────────┐
│  PostgreSQL 17      │  │  Redis 7            │
│  (Primary DB)       │  │  Cache / Sessions   │
└────────────────────┘  │  / Queue            │
                         └─────────────────────┘
```

### Docker Compose Services

| Service | Purpose |
|---------|---------|
| `app` | Laravel HTTP server (`php artisan serve`) |
| `queue` | Queue worker (`php artisan queue:work`) |
| `scheduler` | Cron loop (`schedule:run` every 60 s) |
| `vite` | Node 22 dev server (HMR) — dev only |
| `postgres` | PostgreSQL 17 |
| `redis` | Redis 7 |

---

## Database Schema

### `users`

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigIncrements | PK |
| `name` | string | |
| `email` | string, unique | |
| `role` | enum(`Admin`, `Teacher`, `Student`) | default: `Student` |
| `status` | enum(`Active`, `Blocked`, `Archived`) | default: `Active` |
| `status_reason` | text, nullable | Block/archive reason |
| `avatar_path` | string, nullable | Relative path under `public/avatars/` |
| `banner_path` | string, nullable | Relative path under `public/banners/` |
| `about_me` | text, nullable | Free-text bio |
| `guardian_name` | string, nullable | Student's guardian name |
| `guardian_email` | string, nullable | Guardian email for absence alerts |
| `email_verified_at` | timestamp, nullable | |
| `password` | string | Bcrypt hash |
| `remember_token` | string, nullable | |
| `created_at` / `updated_at` | timestamps | |

### `invitations`

| Column | Type | Notes |
|--------|------|-------|
| `id` | ULID (PK) | Sortable, URL-friendly |
| `email` | string | Invitee email |
| `name` | string, nullable | Optional display name |
| `role` | enum(`Admin`, `Teacher`, `Student`) | |
| `invited_by` | foreignId → users | Admin who created it |
| `token` | string(64), unique | Random secure token |
| `accepted_at` | datetime, nullable | Null until accepted |
| `expires_at` | datetime | 7 days from creation |
| `created_at` / `updated_at` | timestamps | |

**Scopes**: `pending()` — not accepted, not expired.

### `classes` (`school_classes` table)

| Column | Type | Notes |
|--------|------|-------|
| `id` | ULID (PK) | |
| `teacher_id` | foreignId → users | |
| `name` | string | e.g. "ICT 101" |
| `description` | text, nullable | |
| `section` | string, nullable | e.g. "Section A" |
| `invite_code` | string(8), unique | Alphanumeric |
| `status` | enum(`Active`, `Archived`) | default: `Active` |
| `created_at` / `updated_at` | timestamps | |

### `class_student` (pivot)

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigIncrements | PK |
| `class_id` | foreignId → classes | |
| `student_id` | foreignId → users | |
| `enrolled_at` | timestamp | |
| **unique** | (`class_id`, `student_id`) | Prevents duplicates |

### `class_sessions`

| Column | Type | Notes |
|--------|------|-------|
| `id` | ULID (PK) | |
| `class_id` | foreignId → classes | |
| `modality` | enum(`Onsite`, `Online`) | |
| `location` | string, nullable | Room or platform link |
| `start_time` | datetime | |
| `end_time` | datetime | |
| `grace_period_minutes` | unsignedInteger | default: 15 |
| `qr_token` | string(64), unique | Cryptographic random |
| `qr_expires_at` | datetime, nullable | `end_time + grace_period` |
| `status` | enum(`Scheduled`, `Active`, `Completed`, `Cancelled`) | |
| `created_at` / `updated_at` | timestamps | |

### `attendance_records`

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigIncrements | PK |
| `class_session_id` | foreignId → class_sessions | |
| `student_id` | foreignId → users | |
| `status` | enum(`Present`, `Late`, `Absent`, `Excused`) | |
| `scanned_at` | datetime, nullable | QR scan timestamp |
| `marked_by` | enum(`System`, `Teacher`) | |
| `notes` | text, nullable | Teacher remarks |
| **unique** | (`class_session_id`, `student_id`) | One record per student per session |
| `created_at` / `updated_at` | timestamps | |

### `excuse_requests`

| Column | Type | Notes |
|--------|------|-------|
| `id` | ULID (PK) | |
| `student_id` | foreignId → users | |
| `class_session_id` | foreignId → class_sessions | |
| `reason` | text | Student-provided reason |
| `document_path` | string, nullable | Uploaded supporting file |
| `status` | enum(`Pending`, `Approved`, `Denied`) | default: `Pending` |
| `reviewed_by` | foreignId → users, nullable | Teacher who reviewed |
| `review_notes` | text, nullable | |
| `created_at` / `updated_at` | timestamps | |

### `activity_logs`

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigIncrements | PK |
| `user_id` | foreignId → users, nullable | Actor (null for system) |
| `action` | string | e.g. `sent_invitations`, `blocked_user` |
| `description` | text | Human-readable summary |
| `subject_type` | string, nullable | Morph type of related model |
| `subject_id` | string, nullable | Morph ID |
| `created_at` | timestamp | |

### `notifications` (Laravel Database Notifications)

| Column | Type | Notes |
|--------|------|-------|
| `id` | uuid | PK |
| `type` | string | Notification class name |
| `notifiable_type` | string | Morph type (e.g. `App\Models\User`) |
| `notifiable_id` | unsignedBigInteger | User ID |
| `data` | json | Notification payload (`title`, `body`, `icon`, `url`) |
| `read_at` | timestamp, nullable | Null until read |
| `created_at` / `updated_at` | timestamps | |

---

## Route Reference

### Public / Auth

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/` | `landing` | Landing page |
| GET | `/login` | `login` | Login form |
| POST | `/login` | `login.store` | Authenticate user |
| POST | `/logout` | `logout` | Sign out |
| GET | `/forgot-password` | `password.request` | Password reset form |
| POST | `/forgot-password` | `password.email` | Send reset link |
| GET | `/reset-password/{token}` | `password.reset` | Reset password form |
| POST | `/reset-password` | `password.update` | Save new password |
| GET | `/invitation/accept/{token}` | `invitation.accept` | Accept invitation form |
| POST | `/invitation/accept/{token}` | `invitation.accept.store` | Create account via invite |
| GET | `/new` | `new.index` | First-time setup start |
| GET | `/new/setup` | `new.setup` | Setup wizard |
| POST | `/new/setup/admin` | `new.setup.admin` | Create admin account |
| POST | `/new/setup/settings` | `new.setup.settings` | Save institution settings |
| GET | `/attend/{session}/{token}` | `attend.show` | Public attendance form |
| POST | `/attend/{session}/{token}` | `attend.store` | Record attendance via public link |

### Authenticated (All Roles)

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/dashboard` | `dashboard` | Role-based dashboard redirect |
| GET | `/profile/edit` | `profile.edit` | Edit own profile |
| PATCH | `/profile` | `profile.update` | Save profile changes |
| GET | `/profile/{user}` | `profile.show` | View any user's public profile |
| GET | `/notifications` | `notifications.index` | Notification center page |
| GET | `/notifications/unread` | `notifications.unread` | JSON: unread count + latest |
| POST | `/notifications/{id}/read` | `notifications.read` | Mark notification as read |
| POST | `/notifications/read-all` | `notifications.read-all` | Mark all as read |

### Admin

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/admin/users` | `admin.users.index` | User list + pending invitations |
| GET | `/admin/users/invite` | `admin.users.invite` | Invite form |
| POST | `/admin/users/invite` | `admin.users.invite.send` | Send invitation(s) |
| GET | `/admin/users/{user}` | `admin.users.show` | User profile detail |
| PATCH | `/admin/users/{user}` | `admin.users.update` | Update user |
| POST | `/admin/users/{user}/block` | `admin.users.block` | Block user |
| POST | `/admin/users/{user}/unblock` | `admin.users.unblock` | Unblock user |
| POST | `/admin/users/{user}/archive` | `admin.users.archive` | Archive user |
| DELETE | `/admin/invitations/{invitation}` | `admin.invitations.invalidate` | Expire invitation |
| **GET** | **`/admin/invitations/pending`** | **`admin.invitations.pending`** | **JSON: pending invitations** |
| GET | `/admin/reports` | `admin.reports.index` | Reports overview |
| GET | `/admin/reports/export/csv` | `admin.reports.export.csv` | CSV export |
| GET | `/admin/reports/export/pdf` | `admin.reports.export.pdf` | PDF export |
| GET | `/admin/leaderboard` | `admin.leaderboard.index` | Class overview |
| GET | `/admin/activity-log` | `admin.activity-log.index` | Activity log |
| GET | `/admin/settings` | `admin.settings.edit` | Site settings form |
| PATCH | `/admin/settings` | `admin.settings.update` | Save site settings |

#### `GET /admin/invitations/pending` — JSON Endpoint

Returns live pending invitation data. Used by the 20-second polling script on the Users page.

**Response:**
```json
{
    "count": 2,
    "items": [
        {
            "id": "01jw...",
            "display_name": "Jane Doe",
            "email": "jane@example.com",
            "role": "Teacher",
            "inviter_name": "Admin User",
            "has_name": true,
            "expires_at": "Jan 15, 2025",
            "invalidate_url": "https://example.com/admin/invitations/01jw..."
        }
    ]
}
```

### Teacher

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/teacher/classes` | `teacher.classes.index` | Class list |
| GET | `/teacher/classes/create` | `teacher.classes.create` | Create class form |
| POST | `/teacher/classes` | `teacher.classes.store` | Save new class |
| GET | `/teacher/classes/{class}` | `teacher.classes.show` | Class detail |
| PATCH | `/teacher/classes/{class}` | `teacher.classes.update` | Update class |
| POST | `/teacher/classes/{class}/archive` | `teacher.classes.archive` | Archive class |
| POST | `/teacher/classes/{class}/enroll` | `teacher.classes.enroll` | Enrol student |
| DELETE | `/teacher/classes/{class}/students/{student}` | `teacher.classes.unenroll` | Remove student |
| GET | `/teacher/classes/{class}/students/search` | `teacher.classes.students.search` | Search students |
| GET | `/teacher/classes/{class}/students/{student}` | `teacher.students.show` | Student detail in class |
| GET | `/teacher/classes/{class}/analytics/pdf` | `teacher.classes.analytics.pdf` | PDF analytics |
| POST | `/teacher/classes/{class}/sessions` | `teacher.sessions.store` | Create session |
| POST | `/teacher/classes/{class}/sessions/bulk` | `teacher.sessions.bulk-store` | Bulk create sessions |
| GET | `/teacher/sessions/{session}` | `teacher.sessions.show` | Session detail + QR |
| PATCH | `/teacher/attendance/{record}` | `teacher.attendance.update` | Override attendance status |
| GET | `/teacher/excuses` | `teacher.excuses.index` | Excuse request list |
| PATCH | `/teacher/excuses/{excuseRequest}` | `teacher.excuses.review` | Approve / deny excuse |
| GET | `/teacher/excuses/{excuseRequest}/download` | `teacher.excuses.download` | Download document |

### Student

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/student/classes` | `student.classes.index` | Enrolled classes |
| GET | `/student/classes/{class}` | `student.classes.show` | Class detail |
| GET | `/student/attendance` | `student.attendance.index` | Attendance history |
| GET | `/student/calendar` | `student.calendar.index` | Session calendar |
| GET | `/student/scan` | `student.scan.index` | QR scanner |
| POST | `/student/scan` | `student.scan.store` | Submit QR token |
| GET | `/student/excuses/create` | `student.excuses.create` | New excuse form |
| POST | `/student/excuses` | `student.excuses.store` | Submit excuse |
| GET | `/student/excuses` | `student.excuses.index` | Excuse history |
| GET | `/student/excuses/{excuseRequest}/download` | `student.excuses.download` | Download own document |
| GET | `/student/notifications` | `student.notifications.edit` | Email notification preferences |
| PATCH | `/student/notifications` | `student.notifications.update` | Save preferences |

---

## Key Services & Jobs

### Jobs

| Job | Trigger | Description |
|-----|---------|-------------|
| `ExpireStaleInvitations` | Scheduled daily | Marks invitations past `expires_at` as expired by ensuring the scope excludes them |
| `MarkAbsenteesAfterSession` | Scheduled — after each session ends | Creates `Absent` attendance records for enrolled students with no scan record |

### Notifications

| Notification | Channels | Trigger |
|-------------|----------|---------|
| `InvitationNotification` | Mail | Admin sends an invitation |
| `ClassSessionStartedNotification` | Mail, Database | Teacher starts a session |
| `SessionCompletedNotification` | Database | Teacher completes a session |
| `AttendanceRecordedNotification` | Database | Student scans QR or uses public link |
| `AttendanceUpdatedNotification` | Database | Teacher manually updates attendance status |
| `ExcuseSubmittedNotification` | Database | Student submits an excuse request |
| `ExcuseReviewedNotification` | Database | Teacher reviews an excuse request |
| `NewUserRegisteredNotification` | Database | User accepts an invitation and registers |
| `WeeklyAttendanceSummaryNotification` | Mail | Scheduled weekly |
| `ParentAbsenceNotification` | Mail | Student marked Absent (guardian email configured) |
| `ResetPasswordNotification` | Mail | User requests password reset |

Database notifications store a JSON payload with `title`, `body`, `icon` (Lucide icon name), and `url` (link to relevant page). The notification bell in the sidebar polls `/notifications/unread` every 15 seconds to update badge counts and display toast popups for new notifications.

### Policies

| Policy | Model | Notes |
|--------|-------|-------|
| `AttendanceRecordPolicy` | `AttendanceRecord` | Teachers can update records for their own sessions only |
| `ClassSessionPolicy` | `ClassSession` | Teachers own sessions via their class |
| `ExcuseRequestPolicy` | `ExcuseRequest` | Teachers review; students create/view own |
| `SchoolClassPolicy` | `SchoolClass` | Teachers own their classes |

---

## Background Queue & Scheduler

The application uses **Redis** as the queue driver.

Queue worker (runs in the `queue` Docker service):
```bash
php artisan queue:work --sleep=3 --tries=3
```

Scheduler (runs in the `scheduler` Docker service, every 60 s):
```bash
php artisan schedule:run
```

Scheduled tasks are defined in `routes/console.php`.

---

## Authentication & Authorisation

- **Authentication**: Laravel's built-in session-based auth (`auth` middleware).
- **Role Middleware**: Custom `EnsureRole` middleware (`app/Http/Middleware/EnsureRole.php`), registered as alias `role`. Usage: `->middleware('role:admin')` or `->middleware('role:admin,teacher')`.
- **Policies**: Gate-based policies for all sensitive models, enforced via `authorize()` calls in controllers.
- **Invitations**: Token-based one-time-use links. Token is 64 random bytes. After acceptance, `accepted_at` is set and the token cannot be reused.

---

## File Storage

Files (avatars, banners, excuse documents) are stored using Laravel's `Storage` facade.

| Asset | Disk | Path prefix |
|-------|------|-------------|
| User avatars | `public` | `avatars/` |
| User banners | `public` | `banners/` |
| Excuse documents | `local` (private) | `excuse-documents/` |

Ensure the `storage:link` symlink is created in production:
```bash
php artisan storage:link
```

---

## Local Development

### Prerequisites

- Docker + Docker Compose
- Node.js 22+ (for local Vite — optional if using `vite` Docker service)

### Start

```bash
# Copy environment file
cp .env.example .env

# Start all services
docker compose up -d

# Install PHP dependencies
docker compose exec app composer install

# Generate app key
docker compose exec app php artisan key:generate

# Run migrations + seeders
docker compose exec app php artisan migrate --seed

# Install JS dependencies & build assets (or run dev server)
npm install
npm run dev
```

Access the app at **http://localhost:8000** (or the port specified in `APP_URL`).

### Running Artisan Commands

All Artisan commands should be run inside the `app` container:

```bash
docker compose exec app php artisan <command>
```

### Running Tests

```bash
docker compose exec app php artisan test --compact
```

Filter by file or test name:
```bash
docker compose exec app php artisan test --compact --filter=InvitationTest
```

---

## Production Deployment

Attendify ships with `docker-compose.prod.yml` for production use.

```bash
# Build production image
docker compose -f docker-compose.prod.yml build

# Start services
docker compose -f docker-compose.prod.yml up -d

# Run post-deploy steps
docker compose -f docker-compose.prod.yml exec app php artisan migrate --force
docker compose -f docker-compose.prod.yml exec app php artisan storage:link
docker compose -f docker-compose.prod.yml exec app php artisan config:cache
docker compose -f docker-compose.prod.yml exec app php artisan route:cache
docker compose -f docker-compose.prod.yml exec app php artisan view:cache
```

### Required Environment Variables (Production)

| Variable | Description |
|----------|-------------|
| `APP_KEY` | Laravel application key (`php artisan key:generate`) |
| `APP_URL` | Public HTTPS URL of the app |
| `DB_HOST` | PostgreSQL host |
| `DB_DATABASE` | Database name |
| `DB_USERNAME` | Database user |
| `DB_PASSWORD` | Database password |
| `REDIS_HOST` | Redis host |
| `MAIL_HOST` | SMTP host |
| `MAIL_PORT` | SMTP port |
| `MAIL_USERNAME` | SMTP username |
| `MAIL_PASSWORD` | SMTP password |
| `MAIL_FROM_ADDRESS` | Sender email address |
| `QUEUE_CONNECTION` | `redis` |
| `CACHE_DRIVER` | `redis` |
| `SESSION_DRIVER` | `redis` |

---

## Testing

The test suite uses **Pest 4** with PHPUnit 12 as the underlying engine.

```
tests/
  Pest.php           # Global pest configuration
  TestCase.php       # Base test case (RefreshDatabase)
  Feature/           # HTTP / integration tests
  Unit/              # Isolated unit tests
```

Key conventions:

- All feature tests use `RefreshDatabase` (automatically applied via `TestCase`).
- Use model factories (`User::factory()`, `Invitation::factory()`, etc.) for test data.
- The `docker compose exec app php artisan test --compact` command runs the full suite.

---

*For the end-user guide, see [USER_MANUAL.md](USER_MANUAL.md).*
