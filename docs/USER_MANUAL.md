# Attendify — User Manual

> A QR-based attendance monitoring system for educational institutions.

---

## Table of Contents

1. [Getting Started](#getting-started)
2. [Admin Role](#admin-role)
3. [Teacher Role](#teacher-role)
4. [Student Role](#student-role)
5. [Profile Management](#profile-management)
6. [Themes & Accessibility](#themes--accessibility)

---

## Getting Started

### First-Time Setup

When Attendify is first deployed, navigate to `/new` in your browser. The setup wizard will guide you through:

1. **Institution settings** — Enter your institution name and upload a logo.
2. **Admin account creation** — Create the first administrator account (name, email, password).

Once setup is complete, you will be redirected to the login page.

### Logging In

1. Go to `/login`.
2. Enter your email address and password.
3. Click **Sign In**.

> If you have forgotten your password, click **Forgot your password?** on the login page and follow the email reset instructions.

### Logging Out

Click the **Logout** button at the bottom of the sidebar (desktop) or drawer (mobile). A confirmation dialog will appear before you are signed out.

---

## Admin Role

Administrators manage the entire system: users, settings, reports, and activity logs.

### Dashboard

The Admin dashboard shows key statistics at a glance:

| Stat Card | Description |
|-----------|-------------|
| Total Users | Number of registered users across all roles |
| Teachers | Number of active teacher accounts |
| Students | Number of active student accounts |
| Pending Invitations | Open invitations that have not yet been accepted |

### User Management

Navigate to **Manage Users** in the sidebar.

#### Viewing Users

All registered users are listed with their name, email, role badge, and status badge. Click any user to open their profile page.

#### Inviting New Users

1. Click the **Invite User** button (top-right of the Users page).
2. Fill in the invitee's **Full Name** (optional), **Email Address**, and **Role** (Admin, Teacher, or Student).
3. To invite multiple users at once, click **+ Add Another**.
4. Click **Send Invitation** (or **Send N Invitations** for bulk invites).

An email containing a secure acceptance link (valid for **7 days**) is sent to each invitee.

> **Real-time updates**: The Pending Invitations panel refreshes automatically every 20 seconds. If another admin sends an invite or one is accepted while you are on the page, the list will update without a full page reload.

#### Managing Existing Invitations

In the **Pending Invitations** panel on the Users page you can:

- See who was invited, their role, and the expiry date.
- **Invalidate** an invitation by clicking the ✕ button next to it. This immediately expires the invitation link.

#### Blocking & Archiving Users

On a user's profile page, use the action buttons:

| Action | Effect |
|--------|--------|
| **Block** | Prevents login; account remains visible. Optionally provide a reason. |
| **Unblock** | Restores a blocked account to Active status. |
| **Archive** | Soft-deactivates the account. Optionally provide a reason. |

> You cannot block or archive your own account.

### Reports

Navigate to **Reports** in the sidebar.

- **Attendance Overview** — Aggregated attendance statistics across all classes.
- **Export CSV** — Download raw attendance data as a spreadsheet.
- **Export PDF** — Download a formatted PDF report.

### Class Overview (Leaderboard)

The **Class Overview** page shows attendance performance ranked across all classes, helping you identify classes with low attendance rates.

### Activity Log

Every significant action in the system (user creation, invitation sent, class created, attendance marked, etc.) is recorded in the **Activity Log**. You can filter and browse the log to audit changes.

### Site Settings

Navigate to **Site Settings** to update:

- Institution name
- Institution logo
- Other global configuration

---

## Teacher Role

Teachers manage their own classes, schedule sessions, and review excuse requests.

### Dashboard

The Teacher dashboard shows:

| Stat Card | Description |
|-----------|-------------|
| My Classes | Number of active classes you own |
| Sessions Today | Class sessions scheduled or active today |
| Pending Excuses | Excuse requests awaiting your review |
| Total Students | Total enrolled students across all your classes |

### Managing Classes

Navigate to **My Classes** in the sidebar.

#### Creating a Class

1. Click **New Class**.
2. Enter the class **Name**, optional **Description**, and optional **Section**.
3. Click **Create Class**.

#### Class Detail Page

Click on a class to open its detail page. From here you can:

- **View enrolled students** — See the full student roster with attendance summaries.
- **Schedule sessions** — Create individual sessions or bulk-schedule recurring ones.
- **View analytics** — See session-by-session attendance breakdown with charts.
- **Export PDF analytics** — Download a PDF class analytics report.
- **Archive the class** — Prevents new sessions but preserves all records.

#### Enrolling / Removing Students

On the class detail page:

- Use the **Search & Add Student** input to find and enrol an existing student by name or email.
- Click the remove (✕) button next to a student to unenrol them.

### Managing Sessions

#### Scheduling a Session

On the class detail page, click **Add Session**:

1. Choose **Modality**: Onsite or Online.
2. Optionally enter a **Location** (room number or platform link).
3. Set **Start Time** and **End Time**.
4. Set the **Grace Period** (minutes after start time that QR scanning closes — default 15 minutes).
5. Click **Save**.

#### Starting a Session

When it is time for a session to begin, open the session detail page and click **Start Session**. This activates the QR code for student scanning.

#### Viewing a Session

The session detail page shows:

- A real-time **attendance list** (Present, Late, Absent, Excused).
- The active **QR code** while the session is running.
- Ability to **manually override** individual student attendance statuses.

### Excuse Requests

Navigate to **Excuse Requests** to review submissions from your students.

- **Approve** an excuse to change the student's attendance status for that session to _Excused_.
- **Deny** an excuse with an optional note.
- Download uploaded **supporting documents** (e.g. medical certificates).

---

## Student Role

Students enrol in classes, scan QR codes to mark attendance, and submit excuses for absences.

### Dashboard

The Student dashboard shows:

| Stat Card | Description |
|-----------|-------------|
| My Classes | Number of classes you are enrolled in |
| Sessions This Week | Sessions occurring in the current week |
| Attendance Rate | Your overall attendance percentage |
| Pending Excuses | Excuse requests you have submitted that are pending |

### Scanning Attendance

1. Navigate to **Scan QR** in the sidebar (or bottom nav on mobile).
2. Allow camera access when prompted.
3. **Point your camera at the QR code** displayed by your teacher's session screen.
4. A success message confirms your attendance has been recorded.

> QR codes expire at `session end time + grace period`. Scanning after this time will not register attendance.

### Attendance Records

Navigate to **Attendance** to view your complete attendance history across all classes, including:

- Session date and time
- Status (Present, Late, Absent, Excused)
- Class name

### Calendar

The **Calendar** view shows upcoming and past sessions in a monthly calendar layout, helping you plan around scheduled classes.

### Submitting an Excuse

If you were absent or late, you can submit an excuse request:

1. Go to **My Classes** → select the class → find the session.
2. Or navigate directly to **Excuses** → **New Excuse Request**.
3. Enter the reason and optionally upload a supporting document (PDF, image).
4. Submit — your teacher will be notified.

### Notification Preferences

Navigate to **Notifications** (via the bell icon or sidebar) to configure which email notifications you receive:

- Session start notifications
- Weekly attendance summary
- Parent/guardian absence notifications (if a guardian email is configured on your profile)

---

## Profile Management

All roles can manage their profile by clicking their name/avatar at the bottom of the sidebar.

### Editable Fields

| Field | Notes |
|-------|-------|
| Full Name | Displayed throughout the system |
| Email Address | Used for login and notifications |
| Password | Leave blank to keep your current password |
| Avatar | Upload a square profile photo |
| Profile Banner | Upload a wide banner image for your profile page |
| About Me | Short free-text bio |
| Guardian Name | (Students) Name of parent/guardian |
| Guardian Email | (Students) Email address for absence notifications |

### Viewing Another User's Profile

Click a user's name anywhere in the system to visit their public profile page, which shows their name, role, avatar, banner, and bio.

---

## Themes & Accessibility

Attendify ships with **light** and **dark** themes.

- The **theme toggle** button (sun/moon icon) is located at the bottom of the sidebar on desktop, and in the mobile drawer.
- Your theme preference is saved in your browser (`localStorage`) and persists across sessions.

---

*For technical documentation, see [TECHNICAL.md](TECHNICAL.md).*
