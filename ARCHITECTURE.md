# Attendify — System Architecture

## 1. System Overview

Attendify is a QR-based attendance monitoring system for educational institutions. It supports three user roles (Admin, Teacher, Student) with role-specific dashboards, class management, QR-code-based attendance tracking, a profile system, and analytics via Chart.js.

### Technology Stack

| Layer | Technology | Version |
|-------|-----------|---------|
| Backend | Laravel / PHP | 13 / 8.4 |
| Database | PostgreSQL | 17 |
| Cache / Queue | Redis | 7 |
| Frontend Bundler | Vite | 8 |
| CSS | Tailwind CSS + DaisyUI | 4 / 5 |
| HTTP Client (JS) | Axios | 1.x |
| Testing | Pest / PHPUnit | 4 / 12 |
| Containerization | Docker Compose | — |

### Infrastructure Services (Docker Compose)

| Service | Purpose |
|---------|---------|
| `app` | Laravel HTTP server (`php artisan serve`) |
| `queue` | Queue worker (`php artisan queue:work`) |
| `scheduler` | Cron loop running `schedule:run` every 60s |
| `vite` | Node 22 dev server for HMR |
| `postgres` | PostgreSQL 17 database |
| `redis` | Redis 7 for cache, sessions, and queues |

---

## 2. Database Schema

### Existing Tables

#### `users`
| Column | Type | Notes |
|--------|------|-------|
| id | bigIncrements (PK) | |
| name | string | |
| email | string, unique | |
| role | enum(Admin, Teacher, Student) | Default: Student, indexed |
| email_verified_at | timestamp, nullable | |
| password | string, hashed | |
| remember_token | string, nullable | |
| created_at / updated_at | timestamps | |

#### `password_reset_tokens`
| Column | Type | Notes |
|--------|------|-------|
| email | string (PK) | |
| token | string | |
| created_at | timestamp, nullable | |

#### `sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`
Standard Laravel tables — already migrated.

### New Tables

#### `users` — additional profile columns
| Column | Type | Notes |
|--------|------|-------|
| avatar_path | string, nullable | Relative path in `public/avatars/` |
| banner_path | string, nullable | Relative path in `public/banners/` |
| about_me | text, nullable | Free-text bio |

> Added via migration on the existing `users` table to keep queries simple (no joins for profile data).

#### `invitations`
| Column | Type | Notes |
|--------|------|-------|
| id | ULID (PK) | |
| email | string | Invitee email address |
| role | enum(Teacher, Student) | Target role |
| invited_by | foreignId → users | Admin who sent it |
| token | string(64), unique | Secure random token for acceptance URL |
| accepted_at | datetime, nullable | Null until accepted |
| expires_at | datetime | Default: 7 days from creation |
| created_at / updated_at | timestamps | |

#### `classes`
| Column | Type | Notes |
|--------|------|-------|
| id | ULID (PK) | Sortable, URL-friendly |
| teacher_id | foreignId → users | Owning teacher |
| name | string | e.g. "ICT 101" |
| description | text, nullable | |
| section | string, nullable | e.g. "Section A" |
| invite_code | string(8), unique | Alphanumeric, regenerable |
| status | enum(Active, Archived) | Default: Active |
| created_at / updated_at | timestamps | |

#### `class_student` (pivot)
| Column | Type | Notes |
|--------|------|-------|
| id | bigIncrements (PK) | |
| class_id | foreignId → classes | |
| student_id | foreignId → users | |
| enrolled_at | timestamp | |
| **unique** | (class_id, student_id) | Prevents duplicate enrollment |

#### `class_sessions`
| Column | Type | Notes |
|--------|------|-------|
| id | ULID (PK) | |
| class_id | foreignId → classes | |
| modality | enum(Onsite, Online) | |
| location | string, nullable | Physical room or platform name |
| start_time | datetime | |
| end_time | datetime | |
| grace_period_minutes | unsignedInteger | Default: 15 |
| qr_token | string(64), unique | Cryptographic random for QR payload |
| qr_expires_at | datetime, nullable | `end_time + grace_period` |
| status | enum(Scheduled, Active, Completed, Cancelled) | |
| created_at / updated_at | timestamps | |

#### `attendance_records`
| Column | Type | Notes |
|--------|------|-------|
| id | bigIncrements (PK) | |
| class_session_id | foreignId → class_sessions | |
| student_id | foreignId → users | |
| status | enum(Present, Late, Absent, Excused) | |
| scanned_at | datetime, nullable | Timestamp of QR scan |
| marked_by | enum(System, Teacher) | Who set the record |
| notes | text, nullable | Teacher remarks |
| **unique** | (class_session_id, student_id) | One record per student per session |
| created_at / updated_at | timestamps | |

### Entity Relationship Diagram

```
users 1──┬──N invitations        (invited_by)
         │
         ├──N classes             (teacher_id)
         │     │
         │     ├──N class_student (class_id ↔ student_id → users)
         │     │
         │     └──N class_sessions
         │           │
         │           └──N attendance_records (student_id → users)
         │
         └──N attendance_records  (student_id)
```

---

## 3. Directory Structure (New Files)

```
app/
  Enums/
    ClassStatus.php              # Active, Archived
    SessionModality.php          # Onsite, Online
    SessionStatus.php            # Scheduled, Active, Completed, Cancelled
    AttendanceStatus.php         # Present, Late, Absent, Excused
    AttendanceMarkedBy.php       # System, Teacher
  Http/
    Controllers/
      Admin/
        UserManagementController.php
        AdminDashboardController.php
      Teacher/
        ClassController.php
        ClassSessionController.php
        AttendanceController.php
        TeacherDashboardController.php
      Student/
        StudentDashboardController.php
        AttendanceScanController.php
        ClassEnrollmentController.php
      ProfileController.php
    Middleware/
      EnsureRole.php
    Requests/
      Admin/
        InviteUserRequest.php
      Teacher/
        StoreClassRequest.php
        StartSessionRequest.php
        UpdateAttendanceRequest.php
      Student/
        ScanAttendanceRequest.php
        JoinClassRequest.php
      UpdateProfileRequest.php
  Models/
    SchoolClass.php              # "Class" is a PHP reserved word
    ClassStudent.php             # Pivot model (enrolled_at)
    ClassSession.php
    AttendanceRecord.php
    Invitation.php
  Notifications/
    Auth/
      InvitationNotification.php
    ClassSessionStartedNotification.php
    WeeklyAttendanceSummaryNotification.php
  Policies/
    SchoolClassPolicy.php
    ClassSessionPolicy.php
    AttendanceRecordPolicy.php
    InvitationPolicy.php
  Jobs/
    SendWeeklyAttendanceSummary.php
    ExpireStaleInvitations.php
    MarkAbsenteesAfterSession.php
  Console/
    Commands/
      SendWeeklyReports.php

database/
  factories/
    SchoolClassFactory.php
    ClassSessionFactory.php
    AttendanceRecordFactory.php
    InvitationFactory.php
  migrations/
    xxxx_xx_xx_000001_add_profile_columns_to_users_table.php
    xxxx_xx_xx_000002_create_invitations_table.php
    xxxx_xx_xx_000003_create_classes_table.php
    xxxx_xx_xx_000004_create_class_student_table.php
    xxxx_xx_xx_000005_create_class_sessions_table.php
    xxxx_xx_xx_000006_create_attendance_records_table.php
  seeders/
    ClassSeeder.php
    AttendanceSeeder.php

resources/
  js/
    qr-scanner.js                # html5-qrcode camera integration
    charts.js                    # Chart.js initialization helpers
  views/
    dashboard/
      admin.blade.php
      teacher.blade.php
      student.blade.php
    admin/
      users/
        index.blade.php
        invite.blade.php
    teacher/
      classes/
        index.blade.php
        show.blade.php
        create.blade.php
      sessions/
        show.blade.php
        attendance.blade.php
    student/
      classes/
        index.blade.php
        join.blade.php
      scan.blade.php
      attendance.blade.php
    profile/
      show.blade.php
      edit.blade.php
    invitation/
      accept.blade.php
    components/
      dashboard/
        stat-card.blade.php
        chart-card.blade.php
      qr-display.blade.php
    emails/
      invitation.blade.php
      class-session-started.blade.php
      weekly-attendance-summary.blade.php

tests/
  Feature/
    Admin/
      UserManagementTest.php
    Teacher/
      ClassManagementTest.php
      SessionManagementTest.php
      AttendanceManagementTest.php
    Student/
      EnrollmentTest.php
      AttendanceScanTest.php
    ProfileTest.php
    InvitationAcceptanceTest.php
    DashboardAnalyticsTest.php
    NotificationTest.php
```

---

## 4. Authentication & Authorization Architecture

### Existing Auth

- Login, logout, password reset — already implemented via `AuthenticatedSessionController`, `PasswordResetLinkController`, `NewPasswordController`.
- Custom `ResetPasswordNotification` with branded email template.

### Role Middleware — `EnsureRole`

Registered as `role` alias in `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'role' => \App\Http\Middleware\EnsureRole::class,
    ]);
})
```

Usage in routes:

```php
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(...);
Route::middleware(['auth', 'role:teacher'])->prefix('teacher')->group(...);
Route::middleware(['auth', 'role:student'])->prefix('student')->group(...);
```

### Policies

| Policy | Model | Key Rules |
|--------|-------|-----------|
| `SchoolClassPolicy` | `SchoolClass` | Only the owning teacher can update/archive; students can view enrolled classes |
| `ClassSessionPolicy` | `ClassSession` | Only the class teacher can start/end sessions |
| `AttendanceRecordPolicy` | `AttendanceRecord` | Teachers can edit records for their class sessions; students can view own records |
| `InvitationPolicy` | `Invitation` | Only admins can create/revoke invitations |

### Gates

Simple role gates registered in `AppServiceProvider` for convenience:

```php
Gate::define('admin', fn (User $user) => $user->role === UserRole::Admin);
Gate::define('teacher', fn (User $user) => $user->role === UserRole::Teacher);
Gate::define('student', fn (User $user) => $user->role === UserRole::Student);
```

---

## 5. Route Architecture

### Public Routes
| Method | URI | Controller | Name |
|--------|-----|-----------|------|
| GET | `/` | LandingController@index | `landing` |
| GET | `/new` | SetupController@newIndex | `new.index` |
| GET/POST | `/new/setup/*` | SetupController | `new.setup.*` |
| GET/POST | `/login` | AuthenticatedSessionController | `login` |
| GET/POST | `/forgot-password` | PasswordResetLinkController | `password.*` |
| GET/POST | `/reset-password/*` | NewPasswordController | `password.*` |
| GET | `/invitation/accept/{token}` | InvitationController@show | `invitation.accept` |
| POST | `/invitation/accept/{token}` | InvitationController@store | `invitation.accept.store` |

### Authenticated Routes (common)
| Method | URI | Controller | Name |
|--------|-----|-----------|------|
| GET | `/dashboard` | DashboardController@index | `dashboard` (redirects by role) |
| POST | `/logout` | AuthenticatedSessionController@destroy | `logout` |
| GET | `/profile/{user}` | ProfileController@show | `profile.show` |
| GET | `/profile/edit` | ProfileController@edit | `profile.edit` |
| PATCH | `/profile` | ProfileController@update | `profile.update` |

### Admin Routes — `middleware: auth, role:admin` — prefix: `/admin`
| Method | URI | Controller | Name |
|--------|-----|-----------|------|
| GET | `/admin/dashboard` | AdminDashboardController@index | `admin.dashboard` |
| GET | `/admin/users` | UserManagementController@index | `admin.users.index` |
| GET | `/admin/users/invite` | UserManagementController@create | `admin.users.invite` |
| POST | `/admin/users/invite` | UserManagementController@store | `admin.users.invite.store` |

### Teacher Routes — `middleware: auth, role:teacher` — prefix: `/teacher`
| Method | URI | Controller | Name |
|--------|-----|-----------|------|
| GET | `/teacher/dashboard` | TeacherDashboardController@index | `teacher.dashboard` |
| GET | `/teacher/classes` | ClassController@index | `teacher.classes.index` |
| GET | `/teacher/classes/create` | ClassController@create | `teacher.classes.create` |
| POST | `/teacher/classes` | ClassController@store | `teacher.classes.store` |
| GET | `/teacher/classes/{class}` | ClassController@show | `teacher.classes.show` |
| PATCH | `/teacher/classes/{class}` | ClassController@update | `teacher.classes.update` |
| POST | `/teacher/classes/{class}/archive` | ClassController@archive | `teacher.classes.archive` |
| POST | `/teacher/classes/{class}/regenerate-code` | ClassController@regenerateCode | `teacher.classes.regenerate-code` |
| POST | `/teacher/classes/{class}/sessions` | ClassSessionController@store | `teacher.sessions.store` |
| GET | `/teacher/sessions/{session}` | ClassSessionController@show | `teacher.sessions.show` |
| POST | `/teacher/sessions/{session}/start` | ClassSessionController@start | `teacher.sessions.start` |
| POST | `/teacher/sessions/{session}/complete` | ClassSessionController@complete | `teacher.sessions.complete` |
| POST | `/teacher/sessions/{session}/cancel` | ClassSessionController@cancel | `teacher.sessions.cancel` |
| GET | `/teacher/sessions/{session}/attendance` | AttendanceController@index | `teacher.attendance.index` |
| PATCH | `/teacher/attendance/{record}` | AttendanceController@update | `teacher.attendance.update` |
| GET | `/teacher/sessions/{session}/attendance/export` | AttendanceController@export | `teacher.attendance.export` |

### Student Routes — `middleware: auth, role:student` — prefix: `/student`
| Method | URI | Controller | Name |
|--------|-----|-----------|------|
| GET | `/student/dashboard` | StudentDashboardController@index | `student.dashboard` |
| GET | `/student/classes` | ClassEnrollmentController@index | `student.classes.index` |
| GET | `/student/classes/join` | ClassEnrollmentController@create | `student.classes.join` |
| POST | `/student/classes/join` | ClassEnrollmentController@store | `student.classes.join.store` |
| GET | `/student/scan` | AttendanceScanController@index | `student.scan` |
| POST | `/student/scan` | AttendanceScanController@store | `student.scan.store` |
| GET | `/student/attendance` | AttendanceScanController@history | `student.attendance` |

---

## 6. Frontend Architecture

### Blade Component Hierarchy

```
<x-layouts.app>                        ← Main authenticated layout (exists)
  ├── Role-aware sidebar/navigation    ← Updated with role-specific nav items
  ├── <x-dashboard.stat-card>          ← Reusable stat display (icon, label, value)
  ├── <x-dashboard.chart-card>         ← Card wrapper for Chart.js canvas
  └── <x-qr-display>                  ← QR image with auto-refresh countdown
```

### JavaScript Modules

| Module | Package | Purpose |
|--------|---------|---------|
| `resources/js/qr-scanner.js` | `html5-qrcode` (npm) | Camera-based QR code scanning on student scan page |
| `resources/js/charts.js` | `chart.js` (npm) | Helper functions to initialize Chart.js instances on dashboard views |

Both modules are imported in `resources/js/app.js` and registered via Vite.

### New npm Dependencies

```json
{
  "html5-qrcode": "^2.3",
  "chart.js": "^4.4"
}
```

### New Composer Dependencies

```json
{
  "simplesoftwareio/simple-qrcode": "^4.2"
}
```

---

## 7. QR Code Architecture

### Generation Flow

1. Teacher creates a class session → a cryptographic `qr_token` (64-char hex via `Str::random(64)`) is generated and stored in `class_sessions.qr_token`.
2. Teacher starts the session → the session page displays a QR code image.
3. QR is generated server-side using `simplesoftwareio/simple-qrcode` and rendered as an inline SVG.
4. **QR Payload**: JSON string — `{"session_id": "<ulid>", "token": "<qr_token>"}`.

### Scanning Flow

1. Student navigates to `/student/scan` → the `html5-qrcode` camera scanner activates.
2. Student scans the QR displayed on the teacher's screen.
3. JavaScript decodes the QR payload and sends an AJAX `POST /student/scan` with `session_id` and `token`.
4. Server validates:
   - Token matches `class_sessions.qr_token`
   - Session status is `Active`
   - Current time is before `qr_expires_at`
   - Student is enrolled in the class
   - No existing attendance record for this student + session
5. Server determines attendance status:
   - **Present**: scanned within `start_time` to `start_time + grace_period_minutes`
   - **Late**: scanned after grace period but before `end_time`
6. Server creates `AttendanceRecord` with `marked_by: System` and `scanned_at: now()`.
7. JSON response returned → student sees confirmation.

### Security

- `qr_token` is cryptographically random (64 chars).
- Unique constraint on `(class_session_id, student_id)` prevents double-scanning.
- Token is validated server-side; the QR image alone grants no access without an authenticated session.
- CSRF protection on the AJAX endpoint via Axios (automatically includes XSRF token).

---

## 8. File Storage Architecture

| Asset | Disk | Path | Notes |
|-------|------|------|-------|
| Profile avatars | `public` | `avatars/{user_id}.{ext}` | Max 2 MB, jpg/png/webp |
| Profile banners | `public` | `banners/{user_id}.{ext}` | Max 4 MB, jpg/png/webp |
| QR codes | — | Generated on-the-fly as SVG | Not stored on disk |
| Institution logo | `local` | `app/private/site-assets/` | Existing SiteSettings system |
| Landing banner | `local` | `app/private/site-assets/` | Existing SiteSettings system |

Default avatar: Initials-based fallback rendered via DaisyUI's avatar placeholder component (no file needed).

`storage:link` symlink is already present (`public/storage → storage/app/public`).

---

## 9. Queue & Notification Architecture

### Queued Jobs

| Job | Trigger | Purpose |
|-----|---------|---------|
| `MarkAbsenteesAfterSession` | Dispatched when a session status changes to `Completed` | Creates `Absent` attendance records for enrolled students who didn't scan |
| `ExpireStaleInvitations` | Scheduled daily | Soft-cleans invitations past `expires_at` |
| `SendWeeklyAttendanceSummary` | Scheduled (Sundays 6 PM) | Sends summary emails to students and teachers |

### Notifications

| Notification | Channel | Trigger |
|-------------|---------|---------|
| `InvitationNotification` | Mail (queued) | Admin invites a user |
| `ClassSessionStartedNotification` | Mail (queued) | Teacher starts a class session |
| `WeeklyAttendanceSummaryNotification` | Mail (queued) | Weekly scheduled job |

All notifications are queued via the Redis `default` queue. The `queue` Docker service processes them.

### Scheduled Commands

Registered in `routes/console.php` or `AppServiceProvider`:

| Schedule | Command / Job | Description |
|----------|--------------|-------------|
| `daily()` | `ExpireStaleInvitations` | Clean up expired invitations |
| `weeklyOn(0, '18:00')` | `SendWeeklyReports` | Dispatch weekly attendance summaries |

---

## 10. Testing Strategy

### Test Structure

| Directory | Scope | Examples |
|-----------|-------|---------|
| `tests/Feature/Admin/` | Admin workflows | User invitation CRUD, admin dashboard data |
| `tests/Feature/Teacher/` | Teacher workflows | Class CRUD, session lifecycle, attendance editing |
| `tests/Feature/Student/` | Student workflows | Class enrollment, QR scanning, attendance history |
| `tests/Feature/` | Cross-cutting | Profile management, invitation acceptance, notifications |
| `tests/Unit/` | Isolated logic | Attendance status determination, invite code generation |

### Testing Conventions

- **Framework**: Pest 4 with `pest-plugin-laravel`.
- **Database**: `RefreshDatabase` trait for feature tests; SQLite in-memory or PostgreSQL (matches `phpunit.xml`).
- **Factories**: Every new model has a corresponding factory with useful states (e.g., `ClassSession::factory()->active()`, `Invitation::factory()->expired()`).
- **Authentication**: Use `actingAs()` with factory-created users of the appropriate role.
- **Notifications**: Use `Notification::fake()` to assert notification dispatch without sending real emails.
- **Queue**: Use `Queue::fake()` or `Bus::fake()` to assert job dispatches.
- **File uploads**: Use `UploadedFile::fake()` and `Storage::fake('public')`.
- **Run command**: `php artisan test --compact` (or `--filter=ClassName` for targeted runs).
- **Code style**: `vendor/bin/pint --dirty --format agent` after every change.
