<?php
/**
 * Dra. Samla Cristie - Database Configuration
 * Edit these values to match your MySQL server settings.
 */

// Session must start before any output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'samlavet');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_CHARSET', 'utf8mb4');

// Site settings
define('SITE_NAME', 'Dra. Samla Cristie');
define('SITE_URL', ''); // Leave empty for relative URLs
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('UPLOAD_URL', 'uploads/');

/**
 * Get PDO database connection
 */
function getDB($returnError = false) {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            if ($returnError) {
                return ['error' => $e->getMessage()];
            }
            die('Erro na conexão com o banco de dados: ' . $e->getMessage());
        }
    }
    return $pdo;
}

/**
 * Test database connection
 */
function testDatabaseConnection() {
    try {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
        return ['success' => true];
    } catch (PDOException $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Require admin login
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Sanitize output
 */
function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Get a site setting from DB
 */
function getSetting($key, $default = '') {
    try {
        $db = getDB();
        $stmt = $db->prepare('SELECT setting_value FROM settings WHERE setting_key = ?');
        $stmt->execute([$key]);
        $row = $stmt->fetch();
        return $row ? $row['setting_value'] : $default;
    } catch (Exception $e) {
        return $default;
    }
}

/**
 * Get all settings as associative array
 */
function getAllSettings() {
    try {
        $db = getDB();
        $stmt = $db->query('SELECT setting_key, setting_value FROM settings');
        $settings = [];
        while ($row = $stmt->fetch()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    } catch (Exception $e) {
        return [];
    }
}

/**
 * CSRF Protection
 */
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Secure File Upload
 */
function secureUpload($file, $prefix = 'img') {
    $allowedMime = ['image/jpeg','image/png','image/gif','image/webp'];
    $allowedExt = ['jpg','jpeg','png','gif','webp'];
    $maxSize = 5 * 1024 * 1024; // 5MB

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['error' => 'Erro no upload'];
    }

    if ($file['size'] > $maxSize) {
        return ['error' => 'Arquivo muito grande (máximo 5MB)'];
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $allowedMime)) {
        return ['error' => 'Tipo de arquivo não permitido'];
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt)) {
        return ['error' => 'Extensão não permitida'];
    }

    $newName = $prefix . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
    $uploadDir = realpath(__DIR__.'/uploads');

    if (!$uploadDir || !is_dir($uploadDir)) {
        mkdir(__DIR__.'/uploads', 0755, true);
        $uploadDir = realpath(__DIR__.'/uploads');
    }

    $destination = $uploadDir . DIRECTORY_SEPARATOR . $newName;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        chmod($destination, 0644);
        return ['success' => true, 'path' => 'uploads/' . $newName];
    }

    return ['error' => 'Falha ao mover arquivo'];
}

/**
 * Login Rate Limiting
 */
function checkLoginAttempts() {
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
        $_SESSION['login_last_attempt'] = time();
    }

    if (time() - $_SESSION['login_last_attempt'] > 900) {
        $_SESSION['login_attempts'] = 0;
    }

    if ($_SESSION['login_attempts'] >= 5) {
        $waitTime = 900 - (time() - $_SESSION['login_last_attempt']);
        if ($waitTime > 0) {
            return ['blocked' => true, 'wait' => ceil($waitTime / 60)];
        }
        $_SESSION['login_attempts'] = 0;
    }

    return ['blocked' => false];
}

function incrementLoginAttempts() {
    $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
    $_SESSION['login_last_attempt'] = time();
}

function resetLoginAttempts() {
    $_SESSION['login_attempts'] = 0;
}

/**
 * Security Headers
 */
function setSecurityHeaders() {
    header("X-Frame-Options: DENY");
    header("X-Content-Type-Options: nosniff");
    header("X-XSS-Protection: 1; mode=block");
    header("Referrer-Policy: strict-origin-when-cross-origin");
}
