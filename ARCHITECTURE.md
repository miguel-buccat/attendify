# Attendify — System Architecture

## 1. System Overview

Attendify is a QR-based attendance monitoring system for educational institutions. It supports three user roles (Admin, Teacher, Student) with role-specific dashboards, class management, QR-code-based attendance tracking, excuse requests, a notification system, a profile system, and analytics via Chart.js.

### Technology Stack

| Layer | Technology | Version |
|-------|-----------|---------|
| Backend | Laravel / PHP | 13 / 8.4 |
| Database | PostgreSQL | 17 |
| Cache / Queue / Sessions | Redis | 7 |
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

### `school_classes`

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

> Model is `SchoolClass` because `Class` is a PHP reserved word.

### `class_student` (pivot)

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigIncrements | PK |
| `class_id` | foreignId → school_classes | |
| `student_id` | foreignId → users | |
| `enrolled_at` | timestamp | |
| **unique** | (`class_id`, `student_id`) | Prevents duplicates |

> No dedicated pivot model — managed via Laravel's many-to-many relationship on `SchoolClass`.

### `class_sessions`

| Column | Type | Notes |
|--------|------|-------|
| `id` | ULID (PK) | |
| `class_id` | foreignId → school_classes | |
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

### `password_reset_tokens`, `sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`

Standard Laravel tables.

### Entity Relationship Diagram

```
users 1──┬──N invitations          (invited_by)
         │
         ├──N school_classes       (teacher_id)
         │     │
         │     ├──N class_student  (class_id ↔ student_id → users)
         │     │
         │     └──N class_sessions
         │           │
         │           ├──N attendance_records (student_id → users)
         │           │
         │           └──N excuse_requests    (student_id → users, reviewed_by → users)
         │
         ├──N activity_logs        (user_id)
         │
         └──N notifications        (notifiable_id)
```

---

## 3. Directory Structure

```
app/
  Enums/
    AttendanceMarkedBy.php       # System, Teacher
    AttendanceStatus.php         # Present, Late, Absent, Excused
    ClassStatus.php              # Active, Archived
    ExcuseRequestStatus.php      # Pending, Approved, Denied
    SessionModality.php          # Onsite, Online
    SessionStatus.php            # Scheduled, Active, Completed, Cancelled
    UserRole.php                 # Admin, Teacher, Student
    UserStatus.php               # Active, Blocked, Archived
  Http/
    Controllers/
      Controller.php
      DashboardController.php
      InvitationController.php
      LandingController.php
      NotificationController.php
      ProfileController.php
      PublicAttendanceController.php
      SetupController.php
      SiteAssetController.php
      Admin/
        ActivityLogController.php
        AdminDashboardController.php
        ReportController.php
        SiteSettingsController.php
        UserManagementController.php
      Auth/
        AuthenticatedSessionController.php
        NewPasswordController.php
        PasswordResetLinkController.php
      Student/
        AttendanceCalendarController.php
        AttendanceScanController.php
        ClassEnrollmentController.php
        ExcuseRequestController.php
        NotificationPreferenceController.php
        StudentDashboardController.php
      Teacher/
        AttendanceController.php
        ClassAnalyticsController.php
        ClassController.php
        ClassSessionController.php
        ExcuseRequestController.php
        StudentPerformanceController.php
        TeacherDashboardController.php
    Middleware/
      EnsureRole.php
    Requests/
      UpdateProfileRequest.php
      Admin/
        InviteUserRequest.php
        UpdateUserRequest.php
      Student/
        JoinClassRequest.php
        ScanAttendanceRequest.php
        StoreExcuseRequest.php
      Teacher/
        BulkScheduleSessionRequest.php
        EnrollStudentsRequest.php
        ReviewExcuseRequest.php
        StartSessionRequest.php
        StoreClassRequest.php
        UpdateAttendanceRequest.php
        UpdateClassRequest.php
  Models/
    ActivityLog.php
    AttendanceRecord.php
    ClassSession.php
    ExcuseRequest.php
    Invitation.php
    SchoolClass.php              # "Class" is a PHP reserved word
    User.php
  Notifications/
    Auth/
      InvitationNotification.php
      ResetPasswordNotification.php
    AttendanceRecordedNotification.php
    AttendanceUpdatedNotification.php
    ClassSessionStartedNotification.php
    ExcuseReviewedNotification.php
    ExcuseSubmittedNotification.php
    NewUserRegisteredNotification.php
    ParentAbsenceNotification.php
    SessionCompletedNotification.php
    WeeklyAttendanceSummaryNotification.php
  Policies/
    AttendanceRecordPolicy.php
    ClassSessionPolicy.php
    ExcuseRequestPolicy.php
    SchoolClassPolicy.php
  Jobs/
    ExpireStaleInvitations.php
    MarkAbsenteesAfterSession.php
  Console/
    Commands/
      DevResetSite.php
      SendWeeklyReports.php

database/
  factories/
    AttendanceRecordFactory.php
    ClassSessionFactory.php
    ExcuseRequestFactory.php
    InvitationFactory.php
    SchoolClassFactory.php
    UserFactory.php
  seeders/
    DatabaseSeeder.php

resources/
  js/
    app.js                       # Entry point, imports below
    bootstrap.js                 # Axios setup
    qr-scanner.js                # html5-qrcode camera integration
    charts.js                    # Chart.js initialization helpers
  views/
    landing.blade.php
    attendance/
      public.blade.php
    admin/
      settings.blade.php
      activity-log/index.blade.php
      leaderboard/index.blade.php
      reports/index.blade.php
      users/
        index.blade.php
        invite.blade.php
        show.blade.php
    auth/
      forgot-password.blade.php
      login.blade.php
      reset-password.blade.php
    components/
      alert.blade.php
      qr-display.blade.php
      dashboard/
        stat-card.blade.php
        chart-card.blade.php
      form/field.blade.php
      layouts/
        app.blade.php
        auth.blade.php
      nav/sidebar.blade.php
    dashboard/
      admin.blade.php
      teacher.blade.php
      student.blade.php
    emails/
      invitation.blade.php
      class-session-started.blade.php
      parent-absence.blade.php
      weekly-attendance-summary.blade.php
      auth/reset-password.blade.php
    invitation/
      accept.blade.php
      invalid.blade.php
    new/
      index.blade.php
      setup.blade.php
    notifications/index.blade.php
    profile/
      show.blade.php
      edit.blade.php
    reports/
      admin-attendance.blade.php
      class-analytics.blade.php
      session-attendance.blade.php
    student/
      attendance.blade.php
      scan.blade.php
      calendar/index.blade.php
      classes/
        index.blade.php
        show.blade.php
      excuses/
        create.blade.php
        index.blade.php
      notifications/edit.blade.php
    teacher/
      classes/
        create.blade.php
        index.blade.php
        show.blade.php
      excuses/index.blade.php
      sessions/
        show.blade.php
        attendance.blade.php
      students/show.blade.php

tests/
  Pest.php
  TestCase.php
  Feature/
    AuthTest.php
    DashboardAnalyticsTest.php
    DashboardTest.php
    EnsureRoleMiddlewareTest.php
    NewFeaturesTest.php
    NotificationSystemTest.php
    Phase8NotificationsTest.php
    ProfileTest.php
    PublicAttendanceTest.php
    SetupFlowTest.php
    Admin/
      UserManagementTest.php
    Student/
      CalendarTest.php
      ClassDetailTest.php
      ClassEnrollmentTest.php
      ExcuseRequestTest.php
    Teacher/
      AttendanceManagementTest.php
      ClassManagementTest.php
      SessionManagementTest.php
  Unit/
    SiteSettingsTest.php
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
| `ExcuseRequestPolicy` | `ExcuseRequest` | Teachers review; students create/view own |

### Gates

Simple role gates registered in `AppServiceProvider` for convenience:

```php
Gate::define('admin', fn (User $user) => $user->role === UserRole::Admin);
Gate::define('teacher', fn (User $user) => $user->role === UserRole::Teacher);
Gate::define('student', fn (User $user) => $user->role === UserRole::Student);
```

---

## 5. Route Architecture

> This is a **server-rendered Blade application** — there is no separate API layer. A few routes return JSON for in-page polling (e.g. notification counts, pending invitations, attendance data).

### Public Routes (no auth)

| Method | URI | Controller | Name |
|--------|-----|-----------|------|
| GET | `/` | LandingController@index | `landing` |
| GET | `/new` | SetupController@newIndex | `new.index` |
| GET | `/new/setup` | SetupController@index | `new.setup` |
| POST | `/new/setup/admin` | SetupController@storeAdmin | `new.setup.admin` |
| POST | `/new/setup/settings` | SetupController@storeSettings | `new.setup.settings` |
| GET | `/site-assets/{key}` | SiteAssetController@show | `site-assets.show` |
| GET | `/attend/{session}/{token}` | PublicAttendanceController@show | `attend.show` |
| POST | `/attend/{session}/{token}` | PublicAttendanceController@store | `attend.store` |

### Guest Routes (unauthenticated only)

| Method | URI | Controller | Name |
|--------|-----|-----------|------|
| GET | `/login` | AuthenticatedSessionController@create | `login` |
| POST | `/login` | AuthenticatedSessionController@store | `login.store` |
| GET | `/forgot-password` | PasswordResetLinkController@create | `password.request` |
| POST | `/forgot-password` | PasswordResetLinkController@store | `password.email` |
| GET | `/reset-password/{token}` | NewPasswordController@create | `password.reset` |
| POST | `/reset-password` | NewPasswordController@store | `password.update` |
| GET | `/invitation/accept/{token}` | InvitationController@show | `invitation.accept` |
| POST | `/invitation/accept/{token}` | InvitationController@store | `invitation.accept.store` |

### Authenticated Routes (all roles)

| Method | URI | Controller | Name |
|--------|-----|-----------|------|
| GET | `/dashboard` | DashboardController@index | `dashboard` |
| POST | `/logout` | AuthenticatedSessionController@destroy | `logout` |
| GET | `/profile/edit` | ProfileController@edit | `profile.edit` |
| PATCH | `/profile` | ProfileController@update | `profile.update` |
| GET | `/profile/{user}` | ProfileController@show | `profile.show` |
| GET | `/notifications` | NotificationController@index | `notifications.index` |
| GET | `/notifications/unread` | NotificationController@unread | `notifications.unread` |
| POST | `/notifications/{id}/read` | NotificationController@markAsRead | `notifications.read` |
| POST | `/notifications/read-all` | NotificationController@markAllAsRead | `notifications.read-all` |

> `/notifications/unread` returns JSON (used by 15-second polling for badge counts and toast popups).

### Admin Routes — `middleware: auth, role:admin` — prefix: `/admin`

| Method | URI | Controller | Name |
|--------|-----|-----------|------|
| GET | `/admin/users` | UserManagementController@index | `admin.users.index` |
| GET | `/admin/users/invite` | UserManagementController@invite | `admin.users.invite` |
| POST | `/admin/users/invite` | UserManagementController@sendInvitation | `admin.users.invite.send` |
| GET | `/admin/users/{user}` | UserManagementController@show | `admin.users.show` |
| PATCH | `/admin/users/{user}` | UserManagementController@update | `admin.users.update` |
| POST | `/admin/users/{user}/block` | UserManagementController@block | `admin.users.block` |
| POST | `/admin/users/{user}/unblock` | UserManagementController@unblock | `admin.users.unblock` |
| POST | `/admin/users/{user}/archive` | UserManagementController@archive | `admin.users.archive` |
| DELETE | `/admin/invitations/{invitation}` | UserManagementController@invalidateInvitation | `admin.invitations.invalidate` |
| GET | `/admin/invitations/pending` | UserManagementController@pendingInvitations | `admin.invitations.pending` |
| GET | `/admin/settings` | SiteSettingsController@edit | `admin.settings.edit` |
| PATCH | `/admin/settings` | SiteSettingsController@update | `admin.settings.update` |
| GET | `/admin/activity-log` | ActivityLogController@index | `admin.activity-log.index` |
| GET | `/admin/reports` | ReportController@index | `admin.reports.index` |
| GET | `/admin/reports/export/csv` | ReportController@exportCsv | `admin.reports.export.csv` |
| GET | `/admin/reports/export/pdf` | ReportController@exportPdf | `admin.reports.export.pdf` |
| GET | `/admin/leaderboard` | ReportController@leaderboard | `admin.leaderboard.index` |

> `/admin/invitations/pending` returns JSON (used by 20-second polling on the Users page).

### Teacher Routes — `middleware: auth, role:teacher` — prefix: `/teacher`

| Method | URI | Controller | Name |
|--------|-----|-----------|------|
| GET | `/teacher/classes` | ClassController@index | `teacher.classes.index` |
| GET | `/teacher/classes/create` | ClassController@create | `teacher.classes.create` |
| POST | `/teacher/classes` | ClassController@store | `teacher.classes.store` |
| GET | `/teacher/classes/{class}` | ClassController@show | `teacher.classes.show` |
| PATCH | `/teacher/classes/{class}` | ClassController@update | `teacher.classes.update` |
| POST | `/teacher/classes/{class}/archive` | ClassController@archive | `teacher.classes.archive` |
| GET | `/teacher/classes/{class}/students/search` | ClassController@searchStudents | `teacher.classes.students.search` |
| POST | `/teacher/classes/{class}/enroll` | ClassController@enroll | `teacher.classes.enroll` |
| DELETE | `/teacher/classes/{class}/students/{student}` | ClassController@unenroll | `teacher.classes.unenroll` |
| POST | `/teacher/classes/{class}/sessions` | ClassSessionController@store | `teacher.sessions.store` |
| POST | `/teacher/classes/{class}/sessions/bulk` | ClassSessionController@bulkStore | `teacher.sessions.bulk-store` |
| GET | `/teacher/sessions/{session}` | ClassSessionController@show | `teacher.sessions.show` |
| POST | `/teacher/sessions/{session}/start` | ClassSessionController@start | `teacher.sessions.start` |
| POST | `/teacher/sessions/{session}/complete` | ClassSessionController@complete | `teacher.sessions.complete` |
| POST | `/teacher/sessions/{session}/cancel` | ClassSessionController@cancel | `teacher.sessions.cancel` |
| POST | `/teacher/sessions/{session}/cancel-upcoming` | ClassSessionController@cancelUpcoming | `teacher.sessions.cancel-upcoming` |
| GET | `/teacher/sessions/{session}/attendance` | ClassSessionController@attendanceData | `teacher.sessions.attendance` |
| GET | `/teacher/sessions/{session}/attendance/manage` | AttendanceController@index | `teacher.attendance.index` |
| PATCH | `/teacher/attendance/{record}` | AttendanceController@update | `teacher.attendance.update` |
| GET | `/teacher/sessions/{session}/attendance/export` | AttendanceController@export | `teacher.attendance.export` |
| GET | `/teacher/sessions/{session}/attendance/export-pdf` | AttendanceController@exportPdf | `teacher.attendance.export-pdf` |
| GET | `/teacher/classes/{class}/students/{student}` | StudentPerformanceController@show | `teacher.students.show` |
| GET | `/teacher/classes/{class}/analytics/pdf` | ClassAnalyticsController@exportPdf | `teacher.classes.analytics.pdf` |
| GET | `/teacher/excuses` | ExcuseRequestController@index | `teacher.excuses.index` |
| PATCH | `/teacher/excuses/{excuseRequest}` | ExcuseRequestController@review | `teacher.excuses.review` |
| GET | `/teacher/excuses/{excuseRequest}/download` | ExcuseRequestController@download | `teacher.excuses.download` |

> `/teacher/sessions/{session}/attendance` returns JSON (used for real-time attendance list on session page).

### Student Routes — `middleware: auth, role:student` — prefix: `/student`

| Method | URI | Controller | Name |
|--------|-----|-----------|------|
| GET | `/student/classes` | ClassEnrollmentController@index | `student.classes.index` |
| GET | `/student/classes/{class}` | ClassEnrollmentController@show | `student.classes.show` |
| GET | `/student/scan` | AttendanceScanController@index | `student.scan.index` |
| POST | `/student/scan` | AttendanceScanController@store | `student.scan.store` |
| GET | `/student/attendance` | AttendanceScanController@history | `student.attendance.index` |
| GET | `/student/calendar` | AttendanceCalendarController@index | `student.calendar.index` |
| GET | `/student/notifications` | NotificationPreferenceController@edit | `student.notifications.edit` |
| PATCH | `/student/notifications` | NotificationPreferenceController@update | `student.notifications.update` |
| GET | `/student/excuses` | ExcuseRequestController@index | `student.excuses.index` |
| GET | `/student/excuses/create` | ExcuseRequestController@create | `student.excuses.create` |
| POST | `/student/excuses` | ExcuseRequestController@store | `student.excuses.store` |
| GET | `/student/excuses/{excuseRequest}/download` | ExcuseRequestController@download | `student.excuses.download` |

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
| Excuse documents | `local` | `excuse-documents/` | Private; downloaded via controller |
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

All notifications are queued via the Redis `default` queue. The `queue` Docker service processes them.

### Scheduled Commands

Registered in `routes/console.php`:

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
