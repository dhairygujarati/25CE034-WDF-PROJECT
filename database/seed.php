<?php
/**
 * Database Seed and Migration Script
 *
 * Automatically provisions tables and realistic academic data
 * Run via CLI: php database/seed.php
 * Or accessible via web during installation.
 */

require_once __DIR__ . '/../config/database.php';

try {
    // 1. Connect to MySQL server (without DB) to create database if not exists
    $rawPdo = Database::getRawConnection();
    $rawPdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "[✓] Database '" . DB_NAME . "' ensured.\n";

    // 2. Connect to the specific database
    $pdo = Database::getConnection();

    // 3. Load and execute schema.sql
    $schemaSql = file_get_contents(__DIR__ . '/schema.sql');
    $pdo->exec($schemaSql);
    echo "[✓] Schema tables and constraints verified.\n";

    // 4. Seed default Users (Admin & Student)
    $usersStmt = $pdo->query("SELECT COUNT(*) FROM users");
    if ($usersStmt->fetchColumn() == 0) {
        $defaultPassword = password_hash('Password@123', PASSWORD_BCRYPT, ['cost' => 12]);
        
        $insertUser = $pdo->prepare("
            INSERT INTO users (full_name, email, password_hash, role) 
            VALUES (:name, :email, :pass, :role)
        ");

        $insertUser->execute([
            ':name'  => 'System Administrator',
            ':email' => 'admin@campus.edu',
            ':pass'  => $defaultPassword,
            ':role'  => 'admin'
        ]);

        $insertUser->execute([
            ':name'  => 'Alex Rivera',
            ':email' => 'student@campus.edu',
            ':pass'  => $defaultPassword,
            ':role'  => 'student'
        ]);

        echo "[✓] Default user accounts created:\n";
        echo "    - Admin:   admin@campus.edu   / Password@123\n";
        echo "    - Student: student@campus.edu / Password@123\n";
    }

    // 5. Seed sample students
    $studentsStmt = $pdo->query("SELECT COUNT(*) FROM students");
    if ($studentsStmt->fetchColumn() == 0) {
        $students = [
            ['CS-2023-001', 'Sophia', 'Chen', 'sophia.chen@campus.edu', '+1-555-0101', 'Computer Science', 6, 3.88, 'Active'],
            ['IT-2023-014', 'Marcus', 'Johnson', 'marcus.j@campus.edu', '+1-555-0102', 'Information Technology', 4, 3.65, 'Active'],
            ['CS-2023-042', 'Aisha', 'Patel', 'aisha.patel@campus.edu', '+1-555-0103', 'Computer Science', 6, 3.92, 'Active'],
            ['EC-2022-009', 'Liam', 'O\'Connor', 'liam.oc@campus.edu', '+1-555-0104', 'Electronics Engineering', 8, 3.50, 'Graduated'],
            ['ME-2023-021', 'Emma', 'Davis', 'emma.davis@campus.edu', '+1-555-0105', 'Mechanical Engineering', 4, 3.45, 'Active'],
            ['DS-2024-003', 'Carlos', 'Rodriguez', 'carlos.r@campus.edu', '+1-555-0106', 'Data Science', 2, 3.78, 'Active'],
            ['CS-2024-019', 'Zoe', 'Kowalski', 'zoe.k@campus.edu', '+1-555-0107', 'Computer Science', 2, 3.81, 'Active'],
            ['IT-2022-033', 'Ethan', 'Wright', 'ethan.w@campus.edu', '+1-555-0108', 'Information Technology', 8, 3.20, 'Graduated']
        ];

        $insStudent = $pdo->prepare("
            INSERT INTO students (roll_number, first_name, last_name, email, phone, department, semester, gpa, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        foreach ($students as $s) {
            $insStudent->execute($s);
        }
        echo "[✓] Seeded " . count($students) . " sample student records.\n";
    }

    // 6. Seed sample events
    $eventsStmt = $pdo->query("SELECT COUNT(*) FROM events");
    if ($eventsStmt->fetchColumn() == 0) {
        $adminId = $pdo->query("SELECT id FROM users WHERE role = 'admin' LIMIT 1")->fetchColumn();

        $events = [
            [
                'Annual Tech Hackathon 2026',
                'Hackathon',
                'A 36-hour competitive software hackathon focusing on AI, Cloud computing, and Sustainable Technology solutions. Open to all departments.',
                '2026-11-15',
                '09:00:00',
                'Innovation Hub - Hall B',
                'Department of Computer Science',
                150,
                'assets/images/sample_poster1.svg',
                $adminId
            ],
            [
                'AI & Data Science Symposium',
                'Seminar',
                'Keynote lectures from industry leaders in machine learning, ethical generative AI, and real-world data science engineering applications.',
                '2026-11-22',
                '14:00:00',
                'Main Auditorium',
                'Data Science Club',
                200,
                'assets/images/sample_poster2.svg',
                $adminId
            ],
            [
                'Full-Stack Web Development Workshop',
                'Workshop',
                'Hands-on masterclass covering modern front-end architectures, RESTful API design, database normalization, and secure authentication.',
                '2026-12-05',
                '10:30:00',
                'Computing Lab 3',
                'IEEE Student Branch',
                60,
                'assets/images/sample_poster3.svg',
                $adminId
            ],
            [
                'Inter-College Cultural Fiesta',
                'Cultural',
                'Annual university cultural festival celebrating music, arts, dramatic performances, and culinary showcases from around the world.',
                '2026-12-18',
                '17:00:00',
                'Open Amphitheatre',
                'Student Activity Council',
                500,
                'assets/images/sample_poster4.svg',
                $adminId
            ]
        ];

        $insEvent = $pdo->prepare("
            INSERT INTO events (title, category, description, event_date, event_time, venue, organizer, max_participants, poster_url, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        foreach ($events as $e) {
            $insEvent->execute($e);
        }
        echo "[✓] Seeded " . count($events) . " sample event records.\n";
    }

    // 7. Seed activity log entry
    $logStmt = $pdo->prepare("
        INSERT INTO activity_logs (user_id, action, details, ip_address) 
        VALUES (1, 'SYSTEM_INIT', 'Database schema and seed records populated successfully', '127.0.0.1')
    ");
    $logStmt->execute();
    echo "[✓] System initialization logged.\n";
    echo "\n=== Database provisioning completed successfully! ===\n";

} catch (PDOException $e) {
    echo "[!] Database Error: " . $e->getMessage() . "\n";
    exit(1);
}
