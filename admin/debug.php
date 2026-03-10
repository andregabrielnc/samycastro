<?php
/**
 * Database Diagnosis Script
 * Debug tool to check database connection issues
 * 
 * Access: https://samycastro.vet/admin/debug.php
 */

require_once __DIR__ . '/../config.php';

// Only show to localhost or with correct token
$isLocalhost = in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1', 'localhost']);
$tokenParam = $_GET['token'] ?? '';
$correctToken = hash('sha256', 'samycastro_debug_' . date('Y-m-d'));

if (!$isLocalhost && $tokenParam !== $correctToken) {
    http_response_code(403);
    die('Access Denied');
}

$results = [];

// 1. Check PHP Configuration
$results['php'] = [
    'version' => phpversion(),
    'sapi' => php_sapi_name(),
    'extensions' => [
        'pdo' => extension_loaded('pdo') ? '✓' : '✗',
        'pdo_mysql' => extension_loaded('pdo_mysql') ? '✓' : '✗',
        'mysqli' => extension_loaded('mysqli') ? '✓' : '✗',
    ],
];

// 2. Check Configuration Constants
$results['config'] = [
    'DB_HOST' => DB_HOST,
    'DB_NAME' => DB_NAME,
    'DB_USER' => DB_USER,
    'DB_CHARSET' => DB_CHARSET,
];

// 3. Test Basic Connection
try {
    $dsn = 'mysql:host=' . DB_HOST . ';charset=' . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 5,
    ]);
    $results['connection_test'] = [
        'status' => 'success',
        'message' => 'Connected to MySQL server',
    ];
} catch (PDOException $e) {
    $results['connection_test'] = [
        'status' => 'error',
        'message' => $e->getMessage(),
        'code' => $e->getCode(),
    ];
}

// 4. Test Database Selection
try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    $results['database_test'] = [
        'status' => 'success',
        'message' => 'Database found and accessible',
    ];
    
    // 5. Check Tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $results['tables'] = [
        'status' => 'success',
        'count' => count($tables),
        'tables' => $tables,
    ];
    
    // 6. Check Admin Users Table
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM admin_users");
    $adminCount = $stmt->fetch()['count'];
    $results['admin_users'] = [
        'status' => 'success',
        'count' => $adminCount,
        'message' => $adminCount > 0 ? 'Admin users found' : 'WARNING: No admin users found',
    ];
    
} catch (PDOException $e) {
    $results['database_test'] = [
        'status' => 'error',
        'message' => $e->getMessage(),
    ];
}

// 6. Check Environment Variables
$results['environment'] = [
    'DB_HOST_ENV' => getenv('DB_HOST') ?: 'not set',
    'DB_NAME_ENV' => getenv('DB_NAME') ?: 'not set',
    'DB_USER_ENV' => getenv('DB_USER') ?: 'not set',
];

// 7. Server Info
$results['server'] = [
    'hostname' => gethostname(),
    'server_ip' => $_SERVER['SERVER_ADDR'] ?? 'unknown',
    'client_ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
    'is_localhost' => $isLocalhost ? 'Yes' : 'No',
];

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Diagnostics - SamyCastro</title>
    <style>
        body { font-family: 'Courier New', monospace; background: #1e1e1e; color: #e0e0e0; padding: 20px; line-height: 1.6; }
        .container { max-width: 1200px; margin: 0 auto; }
        h1 { color: #4ec9b0; margin-bottom: 30px; }
        .section { background: #252526; padding: 20px; margin-bottom: 20px; border-radius: 8px; border-left: 4px solid #4ec9b0; }
        .section-title { color: #4ec9b0; font-size: 1.1em; font-weight: bold; margin-bottom: 15px; }
        .item { padding: 10px 0; border-bottom: 1px solid #3e3e42; }
        .item:last-child { border-bottom: none; }
        .key { color: #9cdcfe; }
        .value { color: #ce9178; }
        .success { color: #6a9955; font-weight: bold; }
        .error { color: #f48771; font-weight: bold; }
        .warning { color: #dcdcaa; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border: 1px solid #3e3e42; }
        th { background: #1e1e1e; color: #4ec9b0; }
        .status-badge { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 0.9em; }
        .status-ok { background: #6a9955; color: white; }
        .status-error { background: #f48771; color: white; }
        .status-warning { background: #dcdcaa; color: #1e1e1e; }
        code { background: #1e1e1e; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 SamyCastro - Database Diagnostics</h1>
        
        <!-- PHP Configuration -->
        <div class="section">
            <div class="section-title">📦 PHP Configuration</div>
            <div class="item"><span class="key">Version:</span> <span class="value"><?= e($results['php']['version']) ?></span></div>
            <div class="item"><span class="key">SAPI:</span> <span class="value"><?= e($results['php']['sapi']) ?></span></div>
            <div class="item"><span class="key">Extensions:</span>
                <table>
                    <tr>
                        <th>Extension</th>
                        <th>Loaded</th>
                    </tr>
                    <?php foreach ($results['php']['extensions'] as $ext => $status): ?>
                    <tr>
                        <td><?= e($ext) ?></td>
                        <td><span class="<?= $status === '✓' ? 'success' : 'error' ?>"><?= $status ?><?= $status === '✓' ? ' Installed' : ' Missing' ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>

        <!-- Database Configuration -->
        <div class="section">
            <div class="section-title">⚙️ Database Configuration</div>
            <div class="item"><span class="key">Host:</span> <span class="value"><?= e($results['config']['DB_HOST']) ?></span></div>
            <div class="item"><span class="key">Database:</span> <span class="value"><?= e($results['config']['DB_NAME']) ?></span></div>
            <div class="item"><span class="key">User:</span> <span class="value"><?= e($results['config']['DB_USER']) ?></span></div>
            <div class="item"><span class="key">Charset:</span> <span class="value"><?= e($results['config']['DB_CHARSET']) ?></span></div>
        </div>

        <!-- Connection Test -->
        <div class="section">
            <div class="section-title">🔗 Server Connection</div>
            <?php if ($results['connection_test']['status'] === 'success'): ?>
            <div class="item"><span class="success status-badge status-ok">✓ OK</span> <?= e($results['connection_test']['message']) ?></div>
            <?php else: ?>
            <div class="item"><span class="error status-badge status-error">✗ ERROR</span> <?= e($results['connection_test']['message']) ?></div>
            <?php endif; ?>
        </div>

        <!-- Database Test -->
        <div class="section">
            <div class="section-title">📊 Database Test</div>
            <?php if (isset($results['database_test'])): ?>
                <?php if ($results['database_test']['status'] === 'success'): ?>
                <div class="item"><span class="success status-badge status-ok">✓ OK</span> <?= e($results['database_test']['message']) ?></div>
                <?php else: ?>
                <div class="item"><span class="error status-badge status-error">✗ ERROR</span> <?= e($results['database_test']['message']) ?></div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Tables -->
        <?php if (isset($results['tables'])): ?>
        <div class="section">
            <div class="section-title">📋 Database Tables (<?= $results['tables']['count'] ?>)</div>
            <?php if ($results['tables']['count'] > 0): ?>
            <div class="item">
                <table>
                    <tr><th>Table Name</th></tr>
                    <?php foreach ($results['tables']['tables'] as $table): ?>
                    <tr><td><?= e($table) ?></td></tr>
                    <?php endforeach; ?>
                </table>
            </div>
            <?php else: ?>
            <div class="item"><span class="warning status-badge status-warning">⚠ WARNING</span> No tables found in database</div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Admin Users -->
        <?php if (isset($results['admin_users'])): ?>
        <div class="section">
            <div class="section-title">👤 Admin Users</div>
            <div class="item">
                <?php if ($results['admin_users']['count'] > 0): ?>
                <span class="success status-badge status-ok">✓</span> <?= $results['admin_users']['count'] ?> admin user(s) found
                <?php else: ?>
                <span class="warning status-badge status-warning">⚠ WARNING</span> No admin users found - You may need to run setup
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Environment Variables -->
        <div class="section">
            <div class="section-title">🌍 Environment Variables</div>
            <div class="item"><span class="key">DB_HOST_ENV:</span> <span class="value"><?= e($results['environment']['DB_HOST_ENV']) ?></span></div>
            <div class="item"><span class="key">DB_NAME_ENV:</span> <span class="value"><?= e($results['environment']['DB_NAME_ENV']) ?></span></div>
            <div class="item"><span class="key">DB_USER_ENV:</span> <span class="value"><?= e($results['environment']['DB_USER_ENV']) ?></span></div>
        </div>

        <!-- Server Info -->
        <div class="section">
            <div class="section-title">🖥️ Server Information</div>
            <div class="item"><span class="key">Hostname:</span> <span class="value"><?= e($results['server']['hostname']) ?></span></div>
            <div class="item"><span class="key">Server IP:</span> <span class="value"><?= e($results['server']['server_ip']) ?></span></div>
            <div class="item"><span class="key">Client IP:</span> <span class="value"><?= e($results['server']['client_ip']) ?></span></div>
            <div class="item"><span class="key">Is Localhost:</span> <span class="value"><?= e($results['server']['is_localhost']) ?></span></div>
        </div>

        <!-- Troubleshooting -->
        <div class="section" style="border-left-color: #dcdcaa;">
            <div class="section-title">💡 Troubleshooting</div>
            <div class="item">
                <strong>Problem: Cannot connect to database</strong><br>
                • Check if MySQL container is running<br>
                • Check if DB_HOST is correct (should be 'db' in Docker or 'localhost' locally)<br>
                • Verify DB_USER and DB_PASS in environment variables<br>
                • Ensure database exists and is accessible
            </div>
            <div class="item">
                <strong>Problem: PDO MySQL extension not loaded</strong><br>
                • Restart your Docker container<br>
                • Check Dockerfile includes: docker-php-ext-install pdo_mysql<br>
                • Rebuild the Docker image
            </div>
            <div class="item">
                <strong>Problem: No admin users found</strong><br>
                • Run the setup script: <code>auto-setup.php</code> (if still available)<br>
                • Or manually insert via PHPMyAdmin<br>
                • Or restore from git: <code>git checkout HEAD~1 auto-setup.php</code>
            </div>
        </div>
    </div>
</body>
</html>
