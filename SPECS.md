# Attendify — Feature Specifications

> Each phase is designed to be implemented incrementally. All existing tests must continue to pass after every phase.

---

## Phase 1: Foundation & Authorization Infrastructure

### 1.1 New Enums

**Description**: Create the enum types required by new models.

| Enum | Values | File |
|------|--------|------|
| `ClassStatus` | Active, Archived | `app/Enums/ClassStatus.php` |
| `SessionModality` | Onsite, Online | `app/Enums/SessionModality.php` |
| `SessionStatus` | Scheduled, Active, Completed, Cancelled | `app/Enums/SessionStatus.php` |
| `AttendanceStatus` | Present, Late, Absent, Excused | `app/Enums/AttendanceStatus.php` |
| `AttendanceMarkedBy` | System, Teacher | `app/Enums/AttendanceMarkedBy.php` |

All enums are backed by `string` values using TitleCase (matching project convention in `UserRole`).

### 1.2 EnsureRole Middleware

**Description**: Gate route groups by user role.

**User Stories**:
- As a developer, I can protect route groups so only users with the correct role can access them.

**Acceptance Criteria**:
- Middleware reads one or more allowed roles from the route definition: `role:admin`, `role:admin,teacher`.
- Returns `403 Forbidden` if the authenticated user's role doesn't match.
- Registered as alias `role` in `bootstrap/app.php`.

**Edge Cases**:
- Unauthenticated users are redirected to login by the `auth` middleware (applied before `role`).

### 1.3 Profile Columns Migration

**Description**: Add `avatar_path`, `banner_path`, and `about_me` columns to the `users` table.

**Acceptance Criteria**:
- All three columns are nullable.
- Existing user rows are unaffected (no default values needed).
- User model updated: add columns to `$fillable`, add `avatar_url` and `banner_url` accessors.

### 1.4 Role-Specific Dashboard Routing

**Description**: The existing `/dashboard` route redirects users to their role-specific dashboard view.

**User Stories**:
- As an admin, I see the admin dashboard when I visit `/dashboard`.
- As a teacher, I see the teacher dashboard.
- As a student, I see the student dashboard.

**Acceptance Criteria**:
- `DashboardController@index` returns the appropriate Blade view based on `auth()->user()->role`.
- Three skeleton views are created: `dashboard/admin.blade.php`, `dashboard/teacher.blade.php`, `dashboard/student.blade.php`.
- Each view extends `components.layouts.app` and displays a heading with the role name.
- Navigation sidebar shows role-appropriate menu items.

### 1.5 Tests

- Middleware: authenticated admin can access admin routes; teacher/student get 403.
- Middleware: authenticated teacher can access teacher routes; admin/student get 403.
- Middleware: multi-role middleware (`role:admin,teacher`) allows both roles.
- Dashboard: each role sees the correct view.

---

## Phase 2: Account Invitation System

### 2.1 Invitation Model & Migration

**Description**: Admins invite teachers and students by email. The invitee receives an email with a secure link to create their account.

**Data Model**: See `invitations` table in ARCHITECTURE.md.

**Factory States**:
- `default`: Valid, unexpired, unaccepted invitation.
- `expired()`: `expires_at` in the past.
- `accepted()`: `accepted_at` set.
- `forTeacher()` / `forStudent()`: Role presets.

### 2.2 Invitation Notification

**Description**: Email sent when an admin creates an invitation.

**Acceptance Criteria**:
- Queued mail notification matching the existing password-reset email design (branded with institution name/logo).
- Contains: institution name, role being invited for, acceptance link, expiration note.
- Acceptance URL: `/invitation/accept/{token}`.

**Email Template**: `resources/views/emails/invitation.blade.php`

### 2.3 Admin User Management

**Controller**: `Admin\UserManagementController`

**User Stories**:
- As an admin, I can view a list of all users and pending invitations.
- As an admin, I can invite a new teacher or student by entering their email and selecting a role.
- As an admin, I see validation errors if the email is already registered or already invited.

**Acceptance Criteria**:
- `GET /admin/users` — Lists all users (name, email, role, status) and pending invitations.
- `GET /admin/users/invite` — Form with email field and role select (Teacher, Student).
- `POST /admin/users/invite` — Validates email (required, valid email, unique in users and uninvited in invitations), creates invitation, dispatches `InvitationNotification`.

**Validation Rules** (`InviteUserRequest`):
- `email`: required, email, not in `users.email`, not in active `invitations.email`.
- `role`: required, in [Teacher, Student].

**Views**:
- `admin/users/index.blade.php` — Table of users + pending invitations with status badges.
- `admin/users/invite.blade.php` — Invitation form.

### 2.4 Invitation Acceptance Flow

**Description**: Public route (no auth required) where the invitee creates their account.

**User Stories**:
- As an invitee, I click the link in my email, see a form to set my name and password, and create my account.
- As an invitee, I see an error if my invitation has expired.

**Acceptance Criteria**:
- `GET /invitation/accept/{token}` — Shows account creation form (name, password, password confirmation). Email is pre-filled and read-only.
- `POST /invitation/accept/{token}` — Validates input, creates user with the invitation's role, sets `email_verified_at`, marks invitation as accepted, logs the user in, redirects to `/dashboard`.
- Expired or already-accepted tokens show an error page.

**Validation Rules**:
- `name`: required, string, max 255.
- `password`: required, string, min 8, confirmed.

**Edge Cases**:
- Token not found → 404.
- Token expired → error view with message.
- Token already accepted → error view with message.
- Email already registered (race condition) → reject with validation error.

### 2.5 Expire Stale Invitations Job

**Job**: `ExpireStaleInvitations`

- Scheduled daily.
- Deletes invitation records where `expires_at < now()` and `accepted_at IS NULL`.

### 2.6 Navigation

- Add "Manage Users" link to admin sidebar navigation.

### 2.7 Tests

- Admin can view users list.
- Admin can send invitation (happy path).
- Invitation email is dispatched.
- Validation: duplicate email, invalid role.
- Acceptance: valid token creates user, logs in, redirects.
- Acceptance: expired token shows error.
- Acceptance: already-accepted token shows error.
- Acceptance: invalid token returns 404.
- Non-admin cannot access user management routes.
- Stale invitations job deletes expired records.

---

## Phase 3: Profile System

### 3.1 Profile Controller

**Controller**: `ProfileController`

**User Stories**:
- As any user, I can view another user's profile (name, avatar, banner, about me, role).
- As any user, I can edit my own profile: update avatar, banner, and about me.

**Acceptance Criteria**:
- `GET /profile/{user}` — Public profile view (within authenticated routes). Shows name, role badge, avatar, banner, about me.
- `GET /profile/edit` — Edit form for the authenticated user's own profile.
- `PATCH /profile` — Updates profile fields and handles file uploads.

**Validation Rules** (`UpdateProfileRequest`):
- `avatar`: nullable, image (jpg, png, webp), max 2048 KB.
- `banner`: nullable, image (jpg, png, webp), max 4096 KB.
- `about_me`: nullable, string, max 1000.

### 3.2 File Upload Handling

- Avatar stored at `public/avatars/{user_id}.{ext}` (overwrites previous).
- Banner stored at `public/banners/{user_id}.{ext}` (overwrites previous).
- Old file deleted when replaced.
- `avatar_path` and `banner_path` columns store the relative path.

### 3.3 Default Avatar

- When `avatar_path` is null, the UI renders a DaisyUI avatar placeholder showing the user's initials.
- Update the dashboard layout header to show the user's avatar (or initials placeholder).

### 3.4 Views

- `profile/show.blade.php` — Banner at top, avatar overlapping, name, role badge, about me section.
- `profile/edit.blade.php` — File inputs for avatar and banner, textarea for about me, save button.

### 3.5 Tests

- User can view their own profile.
- User can view another user's profile.
- User can update their avatar (valid image).
- User can update their banner (valid image).
- User can update about me text.
- Validation: file too large is rejected.
- Validation: non-image file is rejected.
- Old avatar is deleted when new one is uploaded.
- Profile shows initials when no avatar is set.

---

## Phase 4: Class Management

### 4.1 SchoolClass Model & Migration

**Description**: Teachers can create and manage classes. Students can enroll via invite codes.

**Data Model**: See `classes` and `class_student` tables in ARCHITECTURE.md.

**Model**: `SchoolClass` (PHP's `Class` is reserved).

**Relationships**:
- `SchoolClass belongsTo User` (teacher).
- `SchoolClass belongsToMany User` (students) via `class_student` pivot with `enrolled_at`.
- `User hasMany SchoolClass` (as teacher).
- `User belongsToMany SchoolClass` (as student).

**Factory States**:
- `default`: Active class with random invite code.
- `archived()`: Status set to Archived.

### 4.2 Teacher Class CRUD

**Controller**: `Teacher\ClassController`

**User Stories**:
- As a teacher, I can view a list of my classes.
- As a teacher, I can create a new class with a name, optional description, and optional section.
- As a teacher, I can view a single class with its enrolled students.
- As a teacher, I can archive a class (soft status change, not deletion).
- As a teacher, I can regenerate the invite code for a class.

**Acceptance Criteria**:
- `GET /teacher/classes` — Lists teacher's classes with student count, status badge, invite code.
- `GET /teacher/classes/create` — Form with name, description, section fields.
- `POST /teacher/classes` — Creates class, generates 8-char alphanumeric invite code, redirects to class show page.
- `GET /teacher/classes/{class}` — Shows class details, student roster, invite code (copyable), link to create session.
- `PATCH /teacher/classes/{class}` — Updates name, description, section.
- `POST /teacher/classes/{class}/archive` — Sets status to Archived.
- `POST /teacher/classes/{class}/regenerate-code` — Generates new invite code.

**Validation Rules** (`StoreClassRequest`):
- `name`: required, string, max 255.
- `description`: nullable, string, max 1000.
- `section`: nullable, string, max 100.

**Invite Code Generation**:
- 8 characters, uppercase alphanumeric (excluding ambiguous chars: 0, O, I, L).
- Generated via `Str::upper(Str::random(8))` with a uniqueness check and retry loop.

### 4.3 Student Class Enrollment

**Controller**: `Student\ClassEnrollmentController`

**User Stories**:
- As a student, I can view my enrolled classes.
- As a student, I can join a class by entering its invite code.

**Acceptance Criteria**:
- `GET /student/classes` — Lists student's enrolled classes.
- `GET /student/classes/join` — Form with a single input for the invite code.
- `POST /student/classes/join` — Validates code, finds the class, checks student isn't already enrolled, creates pivot entry with `enrolled_at`, redirects to student classes list.

**Validation Rules** (`JoinClassRequest`):
- `invite_code`: required, string, size 8.

**Edge Cases**:
- Invalid code → "Class not found" error.
- Already enrolled → "You are already enrolled in this class" error.
- Archived class → "This class is no longer accepting students" error.

### 4.4 Authorization

**Policy**: `SchoolClassPolicy`
- `viewAny`: Teachers see their own classes; students see enrolled classes.
- `view`: Teacher who owns it, or enrolled student.
- `create`: Teachers only.
- `update`: Owning teacher only.
- `archive`: Owning teacher only.

### 4.5 Views

- `teacher/classes/index.blade.php` — Card grid or table of classes.
- `teacher/classes/create.blade.php` — Create form.
- `teacher/classes/show.blade.php` — Class detail with student roster and session list.
- `student/classes/index.blade.php` — Student's enrolled classes list.
- `student/classes/join.blade.php` — Join form with invite code input.

### 4.6 Tests

- Teacher can create a class.
- Teacher can view their classes.
- Teacher cannot view another teacher's class.
- Teacher can archive a class.
- Teacher can regenerate invite code (new code is different).
- Student can join a class with valid invite code.
- Student cannot join an archived class.
- Student cannot join the same class twice.
- Student cannot join with invalid invite code.
- Students cannot access teacher class routes.
- Invite code uniqueness is enforced.

---

## Phase 5: Class Sessions & QR Attendance

### 5.1 ClassSession Model & Migration

**Data Model**: See `class_sessions` table in ARCHITECTURE.md.

**Factory States**:
- `default`: Scheduled session in the future.
- `active()`: Status Active, start_time in the past, end_time in the future.
- `completed()`: Status Completed, both times in the past.
- `cancelled()`: Status Cancelled.
- `onsite()` / `online()`: Modality presets.

### 5.2 AttendanceRecord Model & Migration

**Data Model**: See `attendance_records` table in ARCHITECTURE.md.

**Factory States**:
- `default`: Present, marked by System.
- `late()`: Late status.
- `absent()`: Absent status.
- `excused()`: Excused status, marked by Teacher.

### 5.3 Session Lifecycle

**Controller**: `Teacher\ClassSessionController`

**User Stories**:
- As a teacher, I can schedule a new session for a class (date, time range, modality, location, grace period).
- As a teacher, I can start a scheduled session (changes status to Active, QR becomes scannable).
- As a teacher, I can view the session page with the QR code displayed.
- As a teacher, I can end a session (status → Completed, triggers absent-marking job).
- As a teacher, I can cancel a session.

**Acceptance Criteria**:
- `POST /teacher/classes/{class}/sessions` — Creates session with status Scheduled. Generates `qr_token` and computes `qr_expires_at`.
- `GET /teacher/sessions/{session}` — Shows session details. If Active: displays QR code (SVG). If Scheduled: shows "Start Session" button.
- `POST /teacher/sessions/{session}/start` — Sets status to Active.
- `POST /teacher/sessions/{session}/complete` — Sets status to Completed. Dispatches `MarkAbsenteesAfterSession` job.
- `POST /teacher/sessions/{session}/cancel` — Sets status to Cancelled.

**Validation Rules** (`StartSessionRequest`):
- `modality`: required, valid SessionModality value.
- `location`: nullable, string, max 255.
- `start_time`: required, datetime, after now.
- `end_time`: required, datetime, after start_time.
- `grace_period_minutes`: nullable, integer, min 1, max 60, default 15.

**QR Code Display**:
- `<x-qr-display>` component renders QR as inline SVG.
- QR payload: `{"session_id": "<ulid>", "token": "<qr_token>"}`.
- Display shows session info (class name, time remaining, student scan count).

### 5.4 QR Scanning

**Controller**: `Student\AttendanceScanController`

**User Stories**:
- As a student, I can open the QR scanner page and scan the teacher's QR code using my device camera.
- As a student, I see immediate feedback on whether my attendance was recorded (Present or Late).

**Acceptance Criteria**:
- `GET /student/scan` — Page with camera scanner UI powered by `html5-qrcode`.
- `POST /student/scan` — AJAX endpoint. Accepts `session_id` and `token`.
- Server validates: token matches, session is Active, student is enrolled, no existing record, time within bounds.
- Creates `AttendanceRecord` with appropriate status and `scanned_at: now()`.
- Returns JSON: `{ "status": "Present" | "Late", "class_name": "...", "session_time": "..." }`.

**Validation Rules** (`ScanAttendanceRequest`):
- `session_id`: required, exists in class_sessions.
- `token`: required, string.

**Attendance Status Determination**:
- `scanned_at <= start_time + grace_period_minutes` → **Present**
- `scanned_at > start_time + grace_period_minutes AND scanned_at <= end_time` → **Late**
- No scan by session end → **Absent** (set by `MarkAbsenteesAfterSession` job)

**Error Responses** (JSON, appropriate HTTP status):
- Invalid token → 422 "Invalid QR code."
- Session not active → 422 "This session is not currently active."
- Not enrolled → 403 "You are not enrolled in this class."
- Already scanned → 409 "Attendance already recorded."
- Session expired → 422 "This session has ended."

### 5.5 Mark Absentees Job

**Job**: `MarkAbsenteesAfterSession`

- Dispatched when session is completed.
- Queries enrolled students who have no attendance record for the session.
- Creates `AttendanceRecord` with status `Absent`, `marked_by: System`, `scanned_at: null`.

### 5.6 JavaScript Module — `qr-scanner.js`

- Initializes `html5-qrcode` with rear camera preference.
- On successful decode: parses JSON payload, sends Axios POST to `/student/scan`.
- Displays result (success/error) in a DaisyUI alert component.
- Handles camera permission denied gracefully.

### 5.7 Views

- `teacher/sessions/show.blade.php` — Session info + QR display (when active) + scan progress.
- `student/scan.blade.php` — Camera viewport + result feedback area.
- `student/attendance.blade.php` — Attendance history (all sessions across all classes).

### 5.8 Tests

- Teacher can create a session for their class.
- Teacher can start a session (status changes to Active).
- Teacher can complete a session.
- Teacher can cancel a session.
- Teacher cannot manage another teacher's session.
- Student can scan valid QR and get Present status.
- Student scanning after grace period gets Late status.
- Student cannot scan for a non-active session.
- Student cannot scan if not enrolled.
- Student cannot scan twice for same session.
- Invalid QR token is rejected.
- MarkAbsenteesAfterSession job creates Absent records for non-scanners.
- Student can view their attendance history.

---

## Phase 6: Attendance Management

### 6.1 Teacher Attendance Editing

**Controller**: `Teacher\AttendanceController`

**User Stories**:
- As a teacher, I can view the attendance roster for a session.
- As a teacher, I can change a student's status (e.g., mark as Excused).
- As a teacher, I can add notes to an attendance record.
- As a teacher, I can export a class session's attendance as CSV.

**Acceptance Criteria**:
- `GET /teacher/sessions/{session}/attendance` — Table showing all enrolled students, their status, scan time, and notes. Statuses shown as colored badges.
- `PATCH /teacher/attendance/{record}` — Updates status and/or notes. Sets `marked_by: Teacher`.
- `GET /teacher/sessions/{session}/attendance/export` — Downloads CSV with columns: Student Name, Email, Status, Scanned At, Marked By, Notes.

**Validation Rules** (`UpdateAttendanceRequest`):
- `status`: required, valid AttendanceStatus value.
- `notes`: nullable, string, max 500.

### 6.2 Authorization

**Policy**: `AttendanceRecordPolicy`
- `update`: Only the teacher of the session's class.
- `viewAny`: Teacher for their sessions; students see only their own records.

### 6.3 Views

- `teacher/sessions/attendance.blade.php` — Roster table with inline status dropdowns and notes fields. Save button per row or bulk save. Export CSV button.

### 6.4 Tests

- Teacher can view attendance roster for their session.
- Teacher can update a student's status to Excused.
- Teacher can add notes to an attendance record.
- Updated record shows `marked_by: Teacher`.
- Teacher cannot edit attendance for another teacher's session.
- CSV export contains correct data.
- CSV export has proper headers and formatting.

---

## Phase 7: Dashboard Analytics

### 7.1 Chart.js Integration

**npm package**: `chart.js ^4.4`

**JavaScript Module**: `resources/js/charts.js`
- Exports helper functions: `createBarChart(canvasId, data)`, `createPieChart(canvasId, data)`, `createLineChart(canvasId, data)`.
- Uses DaisyUI theme colors for chart palette consistency.

**Blade Component**: `<x-dashboard.chart-card title="..." canvasId="...">`
- Card wrapper with a `<canvas>` element for Chart.js rendering.
- Accepts chart data as a JSON-encoded Blade prop passed to a `<script>` block.

### 7.2 Admin Dashboard

**Controller**: `Admin\AdminDashboardController`

**Metrics**:
| Stat Card | Source |
|-----------|--------|
| Total Users | `users` count by role |
| Active Classes | `classes` where status = Active |
| Total Sessions (This Month) | `class_sessions` count this month |
| Average Attendance Rate | `attendance_records` Present + Late / total |

**Charts**:
| Chart | Type | Data |
|-------|------|------|
| Attendance Distribution | Pie | Present / Late / Absent / Excused counts |
| Attendance Trend (30 days) | Line | Daily attendance rate over the last 30 days |

### 7.3 Teacher Dashboard

**Controller**: `Teacher\TeacherDashboardController`

**Metrics**:
| Stat Card | Source |
|-----------|--------|
| My Classes | Teacher's active class count |
| Total Students | Sum of enrolled students across classes |
| Sessions This Month | Teacher's session count this month |
| Average Attendance Rate | Across teacher's classes |

**Charts**:
| Chart | Type | Data |
|-------|------|------|
| Per-Class Attendance Rate | Bar | Attendance rate per class |
| Attendance Distribution | Pie | Present / Late / Absent / Excused across all classes |

### 7.4 Student Dashboard

**Controller**: `Student\StudentDashboardController`

**Metrics**:
| Stat Card | Source |
|-----------|--------|
| My Classes | Student's enrolled class count |
| Sessions Attended | Count where status = Present |
| Late Count | Count where status = Late |
| Absent Count | Count where status = Absent |
| Excused Count | Count where status = Excused |
| Attendance Rate | (Present + Late + Excused) / Total × 100 |

**Charts**:
| Chart | Type | Data |
|-------|------|------|
| My Attendance Over Time | Line | Weekly attendance rate for last 12 weeks |
| Status Breakdown | Pie | Present / Late / Absent / Excused |

### 7.5 Stat Card Component

**Component**: `<x-dashboard.stat-card icon="..." label="..." :value="$value" color="...">`
- DaisyUI card with icon, label text, and large value display.
- Optional color prop for accent (`primary`, `success`, `warning`, `error`).

### 7.6 Performance Notes

- Dashboard queries use `DB::query()` / query builder with aggregation — not Eloquent collection operations on large datasets.
- Teacher/Student dashboards scope queries to the authenticated user's classes only.
- Consider caching admin dashboard stats with a 5-minute TTL if data volume grows.

### 7.7 Tests

- Admin dashboard returns correct metrics for seeded data.
- Teacher dashboard scopes data to the authenticated teacher's classes only.
- Student dashboard scopes data to the authenticated student's enrollment only.
- Chart data arrays have the expected structure.
- Empty state: dashboards render correctly with zero data.

---

## Phase 8: Notifications & Scheduled Tasks

### 8.1 Class Session Started Notification

**Notification**: `ClassSessionStartedNotification`

**Trigger**: Dispatched when a teacher starts a session (`ClassSessionController@start`).

**Recipients**: All students enrolled in the session's class.

**Email Content**:
- Subject: "[Institution Name] — Class session started: {Class Name}"
- Body: Class name, session modality, location, start time, link to QR scanner page.
- Design: Matches the existing password-reset / invitation email template.

**Template**: `resources/views/emails/class-session-started.blade.php`

### 8.2 Weekly Attendance Summary Notification

**Notification**: `WeeklyAttendanceSummaryNotification`

**Trigger**: `SendWeeklyReports` scheduled command (Sundays at 6 PM).

**Recipients**:
- **Students**: Receive their personal weekly attendance summary.
- **Teachers**: Receive a summary of attendance across their classes for the week.

**Student Email Content**:
- Subject: "[Institution Name] — Your Weekly Attendance Summary"
- Body: Total sessions this week, Present count, Late count, Absent count, weekly attendance rate, per-class breakdown.

**Teacher Email Content**:
- Subject: "[Institution Name] — Weekly Class Attendance Report"
- Body: Per-class summary — total sessions held, average attendance rate, notable absences count.

**Template**: `resources/views/emails/weekly-attendance-summary.blade.php`

### 8.3 Scheduled Command

**Command**: `SendWeeklyReports`

- Registered in the scheduler: `weeklyOn(0, '18:00')` (Sunday 6 PM).
- Queries all active teachers and students.
- Dispatches `WeeklyAttendanceSummaryNotification` for each recipient.
- Uses chunking to avoid memory issues with large user sets.

### 8.4 Event Integration

When a session is started (`ClassSessionController@start`):
1. Session status updated to Active.
2. `ClassSessionStartedNotification` dispatched to enrolled students (queued).

This can be implemented via:
- Direct dispatch in the controller, or
- A model observer / event listener on `ClassSession` status change.

Preferred: Direct dispatch in the controller for simplicity (no hidden side effects).

### 8.5 Tests

- ClassSessionStartedNotification is dispatched to enrolled students when session starts.
- ClassSessionStartedNotification is NOT sent to non-enrolled students.
- Notification email contains correct class name, time, and scanner link.
- WeeklyAttendanceSummaryNotification contains correct stats for the student.
- WeeklyAttendanceSummaryNotification contains correct stats for the teacher.
- SendWeeklyReports command dispatches notifications for all active users.
- SendWeeklyReports command handles zero sessions gracefully (no error, skip or send empty summary).
- Scheduled command is registered at the correct time.

---

## Cross-Phase Verification Checklist

After completing each phase:

1. `php artisan test --compact` — All tests pass (existing + new).
2. `vendor/bin/pint --dirty --format agent` — Code style is clean.
3. `php artisan route:list` — Routes are correctly defined with proper middleware.
4. Manual smoke test of the new views in the browser.

### Phase-Specific Verification

| Phase | Additional Check |
|-------|-----------------|
| 2 | Invitation email renders in Mailpit (`localhost:8025`) |
| 4 | `php artisan tinker` — verify invite code uniqueness |
| 5 | Test QR scanner on mobile device via Vite HMR |
| 7 | Chart.js renders in all three dashboard views |
| 8 | `php artisan schedule:test` — weekly report is scheduled |
| All | `php artisan route:list --except-vendor` — middleware correct |
