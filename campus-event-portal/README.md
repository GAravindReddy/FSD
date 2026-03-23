# 🎓 CampusVerse — Campus Event Portal

A full-stack web application built with **PHP**, **MySQL**, and **vanilla CSS/JS** that allows students to discover and register for campus events, while admins can manage everything from a dedicated dashboard.

---

## 📸 Overview

CampusVerse is a campus event management portal with two roles — **Student** and **Admin**. Students can browse upcoming events, register for them, and manage their registrations. Admins can create, edit, and delete events, and view all student registrations in real time.

---

## 🗂️ Project Structure

```
campus-event-portal/
│
├── index.php                  ← Public landing/home page
├── login.php                  ← Login page (Student & Admin)
├── register.php               ← Student registration page
├── logout.php                 ← Session destroy & redirect
│
├── config/
│   ├── base.php               ← BASE_URL constant (auto-detect)
│   └── db.php                 ← PDO database connection
│
├── user/
│   ├── dashboard.php          ← Student dashboard
│   ├── register_event.php     ← Browse & register for events
│   └── my_events.php          ← View & cancel registrations
│
├── admin/
│   ├── admin_dashboard.php    ← Admin overview & stats
│   ├── manage_events.php      ← List, edit, delete events
│   ├── add_event.php          ← Create new event
│   ├── edit_event.php         ← Edit existing event
│   ├── delete_event.php       ← Delete event handler
│   └── view_registrations.php ← View all student registrations
│
├── includes/
│   ├── auth.php               ← Auth guards, CSRF, rate limiting
│   ├── header.php             ← Shared inner page header
│   └── footer.php             ← Shared footer
│
├── assets/
│   ├── css/style.css          ← Global shared styles
│   └── js/script.js           ← Global JS (scroll reveal, alerts)
│
└── database/
    ├── phpmyadmin_setup.sql   ← Full DB setup with sample data
    ├── schema.sql             ← Raw schema only
    ├── fix_login.sql          ← Reset accounts & credentials
    └── update_name.sql        ← Update student name utility
```

---

## ⚙️ Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.2 |
| Database | MySQL 8 via PDO |
| Frontend | HTML5, CSS3, Vanilla JS |
| Fonts | Plus Jakarta Sans (Google Fonts) |
| Server | Apache (XAMPP) |
| Auth | PHP Sessions + bcrypt |

---

## 🚀 Installation & Setup

### Prerequisites
- XAMPP installed (Apache + MySQL running)
- PHP 8.0 or higher
- MySQL 5.7 or higher

### Step 1 — Copy Project Files

Copy the entire `campus-event-portal` folder into your XAMPP web root:

```
C:\xampp\htdocs\campus-event-portal\
```

Make sure the folder structure matches exactly as shown above.

### Step 2 — Configure Database Connection

Open `config/db.php` and update with your MySQL credentials:

```php
define('DB_HOST',    '127.0.0.1');
define('DB_PORT',    '3307');        // Change if your MySQL uses a different port
define('DB_NAME',    'campus_event_portal');
define('DB_USER',    'root');
define('DB_PASS',    '');            // Your MySQL password
define('DB_CHARSET', 'utf8mb4');
```

> **Note:** Check your phpMyAdmin config at `C:\xampp\phpMyAdmin\config.inc.php` to find the correct host, port, and password.

### Step 3 — Configure Base URL

Open `config/base.php` — it is already hardcoded for the standard XAMPP setup:

```php
define('BASE_URL', 'http://localhost:8080/campus-event-portal');
```

If your Apache runs on a different port or your folder name is different, update this line accordingly.

### Step 4 — Create Database

1. Open phpMyAdmin: `http://localhost:8080/phpmyadmin`
2. Click the **SQL** tab
3. Paste the full contents of `database/phpmyadmin_setup.sql`
4. Click **Go**

This will create the database, all tables, sample events, and default login accounts automatically.

### Step 5 — Launch

Open your browser and go to:

```
http://localhost:8080/campus-event-portal/
```

---

## 🔑 Default Login Credentials

| Role | Email | Password |
|---|---|---|
| **Admin** | `admin@campus.edu` | `password` |
| **Student** | `student@campus.edu` | `password` |

> ⚠️ Change these credentials after your first login in a production environment.

---

## 🗄️ Database Schema

### `users` table
| Column | Type | Description |
|---|---|---|
| id | INT AUTO_INCREMENT | Primary key |
| name | VARCHAR(120) | Full name |
| email | VARCHAR(160) UNIQUE | Login email |
| department | VARCHAR(100) | Department name |
| year | TINYINT | Year of study (1–5) |
| password | VARCHAR(255) | Bcrypt hashed password |
| is_active | TINYINT(1) | Account status |
| created_at | DATETIME | Registration timestamp |

### `admins` table
| Column | Type | Description |
|---|---|---|
| id | INT AUTO_INCREMENT | Primary key |
| name | VARCHAR(120) | Admin name |
| email | VARCHAR(160) UNIQUE | Login email |
| password | VARCHAR(255) | Bcrypt hashed password |
| created_at | DATETIME | Created timestamp |

### `events` table
| Column | Type | Description |
|---|---|---|
| id | INT AUTO_INCREMENT | Primary key |
| title | VARCHAR(200) | Event title |
| description | TEXT | Full description |
| category | VARCHAR(60) | Category (Technical, Sports, etc.) |
| location | VARCHAR(200) | Venue |
| event_date | DATETIME | Date and time of event |
| registration_deadline | DATETIME | Last date to register |
| max_participants | INT | Maximum seats |
| price | DECIMAL(8,2) | Entry fee (0 = Free) |
| organizer | VARCHAR(120) | Organising department/club |
| is_active | TINYINT(1) | Visibility toggle |
| created_by | INT (FK) | Admin who created it |
| created_at | DATETIME | Creation timestamp |

### `registrations` table
| Column | Type | Description |
|---|---|---|
| id | INT AUTO_INCREMENT | Primary key |
| user_id | INT (FK) | References users.id |
| event_id | INT (FK) | References events.id |
| status | ENUM | confirmed / cancelled / attended |
| registered_at | DATETIME | Registration timestamp |

---

## 🔐 Security Features

- **Bcrypt password hashing** — all passwords hashed with `PASSWORD_BCRYPT` (cost 12)
- **CSRF tokens** — every form includes a unique token validated on submission
- **PDO prepared statements** — all database queries use parameterized statements, preventing SQL injection
- **Session regeneration** — session ID regenerated on every login to prevent session fixation attacks
- **Session guards** — every protected page checks session before rendering
- **Rate limiting** — helper function in `includes/auth.php` limits repeated actions
- **Input sanitization** — all user input sanitized with `htmlspecialchars`, `filter_input`, and `strip_tags`
- **Secure logout** — `session_unset()`, `session_destroy()`, and cookie clearing on logout

---

## 👤 Student Features

- Register with name, email, department, and year
- Login with Student role
- View personalised dashboard with greeting and stats
- Browse all upcoming events with live slot availability
- Register for events with one click
- Cancel registrations
- View registered events split into Upcoming and Past
- Password strength meter on registration

---

## 🛠️ Admin Features

- Login with Admin role
- Dashboard with total events, students, registrations, and upcoming event count
- Create new events with full details (title, category, location, date, deadline, max participants, price, organizer)
- Edit any existing event
- Delete events (cascades to registrations automatically)
- View all registrations with search and filter by event, department, or student name
- Real-time registration count per event

---

## 📅 Event Categories

| Category | Description |
|---|---|
| Technical | Hackathons, coding contests, workshops |
| Sports | Inter-college tournaments, fitness events |
| Cultural | Dance, music, art, theatre |
| Academic | Seminars, paper presentations, quizzes |
| Workshop | Hands-on skill sessions |
| Social | Fests, fairs, community events |

---

## 🌐 URL Reference

| Page | URL |
|---|---|
| Home | `http://localhost:8080/campus-event-portal/` |
| Login | `http://localhost:8080/campus-event-portal/login.php` |
| Register | `http://localhost:8080/campus-event-portal/register.php` |
| Student Dashboard | `http://localhost:8080/campus-event-portal/user/dashboard.php` |
| Browse Events | `http://localhost:8080/campus-event-portal/user/register_event.php` |
| My Registrations | `http://localhost:8080/campus-event-portal/user/my_events.php` |
| Admin Dashboard | `http://localhost:8080/campus-event-portal/admin/admin_dashboard.php` |
| Manage Events | `http://localhost:8080/campus-event-portal/admin/manage_events.php` |
| Add Event | `http://localhost:8080/campus-event-portal/admin/add_event.php` |
| View Registrations | `http://localhost:8080/campus-event-portal/admin/view_registrations.php` |
| phpMyAdmin | `http://localhost:8080/phpmyadmin` |

---

## 🐛 Troubleshooting

### "Service temporarily unavailable" on homepage
- MySQL is not running. Open XAMPP Control Panel and click **Start** next to MySQL.

### "Access denied for user root" database error
- Wrong password in `config/db.php`. Check `C:\xampp\phpMyAdmin\config.inc.php` for the correct password.

### "Not Found" after login redirect
- Wrong port or folder name in `config/base.php`. Update `BASE_URL` to match your exact URL.

### Login fails with correct credentials
- The database was not set up. Run `database/phpmyadmin_setup.sql` in phpMyAdmin.
- Or run `database/fix_login.sql` to reset accounts with known credentials.

### PHP warnings showing on event cards
- `error_reporting(0)` is included at the top of each page. If warnings still show, check your `php.ini` and set `display_errors = Off`.

### phpMyAdmin not reachable at localhost/phpmyadmin
- Your Apache runs on port 8080. Use `http://localhost:8080/phpmyadmin` instead.

---

## 📝 Notes

- This project is built for **educational and demonstration purposes**.
- The default admin password is `password` — change it before any real deployment.
- All dates use PHP's `date()` and MySQL's `DATETIME` — ensure your server timezone is correctly set.
- The project uses **no external PHP frameworks** — pure PHP with PDO for simplicity and portability.

---

## 👨‍💻 Built With

- PHP 8.2
- MySQL 8 / MariaDB
- HTML5 + CSS3
- Vanilla JavaScript
- Google Fonts — Plus Jakarta Sans
- XAMPP (Apache + MySQL)

---

*CampusVerse — Empowering campus communities, one event at a time.* 🎓
