<?php
/**
 * Generate password hash for admin setup
 * Usage: php generate-hash.php "your-password"
 */

if (php_sapi_name() !== 'cli') {
    die('Este script deve ser executado via CLI.');
}

$password = isset($argv[1]) ? $argv[1] : 'Ag570411@2026';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Senha: $password\n";
echo "Hash: $hash\n";
echo "\nCopie o hash acima e use no SQL:\n";
echo "INSERT INTO admin_users (username, password, name) VALUES ('admin', '$hash', 'Administrador');\n";
