# Campus Management & Academic Event Portal
> An enterprise-grade, accessible web application engineered with Semantic HTML5, Responsive CSS Grid/Flexbox, Vanilla JavaScript, PHP 8, MySQL Relational Database, RESTful JSON APIs, and Role-Based Access Control (RBAC).

---

## 📋 Comprehensive Syllabus Mapping (Items 2–15)

Every milestone specified in the academic syllabus has been implemented, tested, and mapped below:

| Item # | Syllabus Requirement | Core Implementation Files | Description & Architectural Highlights |
|---|---|---|---|
| **2** | **Semantic HTML5 & Accessibility** | `includes/header.php`, `includes/footer.php`, `about.php` | WCAG 2.1 AA compliant. Semantic tags (`<header>`, `<nav>`, `<main>`, `<section>`, `<article>`, `<aside>`, `<footer>`), `.skip-link` keyboard bypass block, ARIA landmarks, `alt` attributes, accessible labels. |
| **3** | **Responsive UI: CSS Grid & Flexbox** | `css/main.css`, `css/components.css`, `css/responsive.css` | CSS Custom Properties (Variables), mobile-first `@media` queries, CSS Grid for dashboard metrics, course & event card galleries; CSS Flexbox for toolbars, headers, and modal actions. |
| **4** | **JS DOM Manipulation & Interactivity** | `js/main.js` | Event listeners, dynamic class toggles, accessible Modal Dialog with focus-trap and Escape-key handling, Toast notification system, and Dark/Light theme switcher persisted in `localStorage`. |
| **5** | **Form Validation & Friendly Errors** | `js/validation.js`, `register.php` | Real-time input validation on `input` and `blur` events, regex pattern matching, interactive password strength meter (visual bar & text rating), shake animation on error, and `aria-invalid` updates. |
| **6** | **External JSON Data with Fetch API** | `explore.php`, `js/explore.js`, `data/campus_courses.json` | Asynchronous ES6+ `fetch()` loading external course catalog JSON, live debounced search by keywords, multi-criteria department dropdown filter, and dynamic DOM rendering. |
| **7** | **PHP Form Processing & CSV/JSON Storage** | `contact.php`, `storage/inquiries.csv`, `storage/inquiries.json` | Strict server-side input validation and sanitization (`filter_var`, `htmlspecialchars`), concurrent file locking (`LOCK_EX`), writing simultaneously to formatted CSV and structured JSON records. |
| **8** | **MySQL Schema, ER Model & Connectivity** | `config/database.php`, `database/schema.sql`, `docs/ER_MODEL.md` | Normalized relational database (1NF, 2NF, 3NF), Foreign Keys (`ON DELETE CASCADE / SET NULL`), B-tree indexes, Singleton PDO connection with `utf8mb4` encoding and prepared statement emulation disabled. |
| **9** | **Secure Registration & Password Hashing** | `register.php`, `database/seed.php` | Parameterized duplicate email and roll number checks, transactional insert across `users` and `students` tables, and password hashing using standard `password_hash($pass, PASSWORD_BCRYPT, ['cost' => 12])`. |
| **10** | **Secure Login, Sessions, RBAC & Remember-Me** | `login.php`, `logout.php`, `includes/auth.php` | Glassmorphism login card with campus architectural background image, session fixation prevention via `session_regenerate_id()`, 15-minute inactivity timeout guard, and 30-day cryptographically secure Remember-Me token cookies. |
| **11** | **Student CRUD with Prepared Statements** | `students/index.php`, `students/create.php`, `students/edit.php`, `students/delete.php` | Full Create, Read, Update, Delete for student records with server-side pagination, search by name/roll/email, department & status filters, and pure PDO prepared statements. |
| **12** | **Event CRUD with Poster Upload & File Validation** | `events/index.php`, `events/create.php`, `events/edit.php`, `events/delete.php` | Event publishing with secure file upload. Validates `UPLOAD_ERR_OK`, 2MB size ceiling, MIME verification via `finfo(FILEINFO_MIME_TYPE)`, extension whitelist (`jpg`, `png`, `webp`), randomized safe naming, and automatic media deletion on edit/delete. |
| **13** | **PHP JSON API & AJAX/Fetch-Based CRUD** | `api/students.php`, `api/events.php`, `ajax_crud.php`, `js/ajax_crud.js` | RESTful JSON API supporting `GET`, `POST`, `PUT`, `DELETE` with HTTP status codes. Interactive AJAX CRUD studio performing live database operations with toast alerts and zero page reloads. |
| **14** | **Admin Dashboard & Dynamic Role Menu** | `dashboard.php`, `includes/header.php` | Role-Based Access Control (RBAC). Admin sees full administrative CRUD, system metrics, inquiries inspection, and audit logs; Students see courses, upcoming events, and inquiry portal. |
| **15** | **Integration, Testing, Docs & Deployment** | `tests/run_tests.php`, `database/seed.php`, `README.md`, `docs/ER_MODEL.md` | Automated test suite verifying 8 integration checkpoints with 100% pass rate, comprehensive deployment guide for XAMPP / PHP built-in server, and Git repository setup. |

---

## 🗂️ Project Directory Structure

```
campus_portal/
├── .gitignore                      # Git ignored files (logs, IDE, vendor)
├── README.md                       # Comprehensive documentation & setup guide
├── index.php                       # Homepage & Academic Overview (Semantic HTML5)
├── about.php                       # Architecture, Accessibility & Standards documentation
├── explore.php                     # External JSON course catalog with Fetch API (Item 6)
├── contact.php                     # Inquiry form with CSV & JSON multi-storage (Item 7)
├── login.php                       # Login with background image, remember-me & timeout (Item 10)
├── register.php                    # Student registration with real-time validation (Item 5 & 9)
├── logout.php                      # Secure logout & cookie invalidation
├── dashboard.php                   # Role-Based Dashboard with dynamic menus & metrics (Item 14)
├── ajax_crud.php                   # Live AJAX CRUD studio without page reloads (Item 13)
│
├── config/
│   └── database.php                # PDO connection singleton & database configuration (Item 8)
│
├── includes/
│   ├── auth.php                    # RBAC, session timeout (15m), remember-me cookie logic (Item 10)
│   ├── csrf.php                    # Anti-CSRF token generator & timing-safe validator (Item 7)
│   ├── helpers.php                 # Sanitization (XSS safe), flash messages, audit logger
│   ├── header.php                  # Semantic HTML5 header, navigation, theme toggle (Item 2 & 14)
│   └── footer.php                  # Semantic HTML5 footer, scripts & metadata
│
├── api/
│   ├── students.php                # RESTful JSON API for students (GET, POST, PUT, DELETE) (Item 13)
│   └── events.php                  # RESTful JSON API for events (Item 13)
│
├── students/
│   ├── index.php                   # Students list with pagination, search & filters (Item 11)
│   ├── create.php                  # Create student form with validation & duplicate check
│   ├── edit.php                    # Edit student form with pre-populated records
│   └── delete.php                  # CSRF-protected student deletion action
│
├── events/
│   ├── index.php                   # Event showcase gallery with poster thumbnails (Item 12)
│   ├── create.php                  # Create event with poster upload & MIME validation
│   ├── edit.php                    # Edit event & replace poster safely
│   └── delete.php                  # Delete event & unlink poster media file
│
├── storage/
│   ├── inquiries.csv               # CSV file storage with safe header & fputcsv (Item 7)
│   └── inquiries.json              # JSON structured file storage with LOCK_EX (Item 7)
│
├── uploads/
│   └── posters/                    # Safe destination for uploaded event poster images (Item 12)
│
├── data/
│   └── campus_courses.json         # External JSON dataset for Fetch API search & filter (Item 6)
│
├── database/
│   ├── schema.sql                  # Complete MySQL relational schema & constraints (Item 8)
│   └── seed.php                    # Database provisioning & sample seed data script
│
├── css/
│   ├── main.css                    # CSS3 variables, typography, reset, dark mode (Item 3)
│   ├── components.css              # Cards, buttons, forms, modals, toasts, glassmorphism (Item 3)
│   └── responsive.css              # CSS Grid & Flexbox layouts, media queries (Item 3)
│
├── js/
│   ├── main.js                     # LocalStorage theme switcher, modals, toasts, nav (Item 4)
│   ├── validation.js               # Registration validation & password strength meter (Item 5)
│   ├── explore.js                  # Fetch API async loader, live search & filter (Item 6)
│   └── ajax_crud.js                # Asynchronous CRUD controller without page reload (Item 13)
│
├── docs/
│   └── ER_MODEL.md                 # Detailed ER diagram, normalization & data dictionary (Item 8)
│
└── tests/
    └── run_tests.php               # Automated 8-point regression & integration test suite (Item 15)
```

---

## 🚀 Local Deployment Guide

### Option A: Running with XAMPP (Recommended)
1. **Start Apache & MySQL:** Open the XAMPP Control Panel and start the **Apache** and **MySQL** services.
2. **Copy / Symlink Project Folder:**
   Ensure the `campus_portal` folder is present inside your XAMPP `htdocs` directory:
   ```
   C:\xampp\htdocs\campus_portal
   ```
3. **Initialize Database:**
   Open a terminal and run the seed script using XAMPP's PHP:
   ```bash
   C:\xampp\php\php.exe database/seed.php
   ```
   *This automatically provisions the `campus_portal_db` database, all relational tables, foreign key constraints, indexes, administrator accounts, sample students, and upcoming events.*
4. **Open in Web Browser:**
   Navigate to:
   ```
   http://localhost/campus_portal/
   ```

---

### Option B: Running with PHP Built-In Web Server
If you want to run directly from the workspace folder without moving files:
1. Ensure MySQL is running on port 3306.
2. Open PowerShell or Command Prompt inside `campus_portal`:
   ```bash
   cd C:\Users\dhair\.gemini\antigravity\scratch\campus_portal
   C:\xampp\php\php.exe database/seed.php
   C:\xampp\php\php.exe -S localhost:8000
   ```
3. Open your browser and navigate to:
   ```
   http://localhost:8000/
   ```

---

## 🔑 Default Accounts & Credentials

| Role | Email Address | Password | Privileges |
|---|---|---|---|
| **System Administrator** | `admin@campus.edu` | `Password@123` | Full access: Traditional CRUD, AJAX Studio, Event publishing, Inquiries inspection, System logs |
| **Enrolled Student** | `student@campus.edu` | `Password@123` | Student view: Course catalog, Upcoming events, Inquiries submission, Student dashboard |

*(Note: The login page includes convenient **1-click demo credential buttons** for testing).*

---

## 🧪 Running the Automated Verification Test Suite

The project includes an automated test runner (`tests/run_tests.php`) that executes unit and integration checks across 8 critical modules:
```bash
C:\xampp\php\php.exe tests/run_tests.php
```
**Verification Output:**
```
=====================================================================
       CAMPUS PORTAL AUTOMATED TEST & INTEGRATION SUITE              
=====================================================================

[✓] Database Connection & UTF8MB4 Handshake            [PASS] Active charset: utf8mb4
[✓] BCRYPT Password Verification (Admin)               [PASS] Hash length: 60, Algorithm: BCRYPT
[✓] Students MySQL Prepared Statements                 [PASS] Found 6 active student records
[✓] Anti-CSRF Generation & Timing-Safe Validation      [PASS] Token length: 64
[✓] CSV & JSON Storage Infrastructure                  [PASS] Storage dir readable and structured
[✓] File Upload Extension Whitelist Security           [PASS] PHP and executable extensions blocked
[✓] Course Catalog JSON Feed for Fetch API             [PASS] Contains 8 valid course objects
[✓] RESTful JSON API Implementation                    [PASS] api/students.php available

---------------------------------------------------------------------
TOTAL TESTS: 8 | PASSED: 8 | FAILED: 0
VERIFICATION STATUS: ALL CHECKS PASSED PERFECTLY (100%)
=====================================================================
```
You can also view this test suite visually in the browser at:
`http://localhost/campus_portal/tests/run_tests.php` or `http://localhost:8000/tests/run_tests.php`.

---

## 🛡️ Security Architecture Highlights
1. **SQL Injection Defense:** 100% of queries use PDO prepared statements with parameterized inputs. Direct string interpolation is strictly prohibited.
2. **Cross-Site Scripting (XSS) Defense:** All dynamic output is escaped using `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')` via the helper function `e()`.
3. **Cross-Site Request Forgery (CSRF) Defense:** Cryptographic tokens generated per session and validated using timing-safe `hash_equals()` on all POST/PUT/DELETE actions.
4. **Password Cryptography:** Passwords hashed with `PASSWORD_BCRYPT` with a cost factor of 12.
5. **Session Inactivity Timeout:** Automated 15-minute inactivity timeout (`SESSION_TIMEOUT_SECONDS = 900`).
6. **Remember-Me Cookie:** 30-day SHA-256 hashed token with `HttpOnly` and `SameSite=Lax` cookie flags.
7. **File Upload Hardening:** Strict MIME type checking via PHP's `finfo` engine, file extension whitelisting (`jpg`, `png`, `webp`), file size checks (max 2MB), and random file renaming to prevent directory traversal or remote code execution.

---
*Created for academic evaluation and submission.*
