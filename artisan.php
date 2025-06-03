<?php
require __DIR__ . '/vendor/autoload.php';

use Lms\Core\Database;
use Dotenv\Dotenv;



$dotenv = Dotenv::createImmutable(__DIR__.'/');
$dotenv->safeLoad();

$command = $argv[1] ?? null;

if (!$command) {
    echo "Available commands:\n";
    echo "  migrate    Run all SQL migration files in database/\n";
    echo "  db:wipe   Drop all tables in the database\n";
    exit;
}

$pdo = Database::getInstance();

switch ($command) {
    case 'migrate':
        migrate($pdo);
        break;

    case 'db:wipe':
        dbWipe($pdo);
        break;

    default:
        echo "Unknown command: $command\n";
        exit(1);
}

function migrate(PDO $pdo)
{
    $dir = __DIR__ . '/database';
    if (!is_dir($dir)) {
        echo "Directory database/ not found.\n";
        exit(1);
    }

    $files = glob($dir . '/*.sql');
    sort($files);

    foreach ($files as $file) {
        echo "Running migration: $file\n";
        $sql = file_get_contents($file);
        try {
            $pdo->exec($sql);
            echo "Success\n";
        } catch (PDOException $e) {
            echo "Error in $file: " . $e->getMessage() . "\n";
            exit(1);
        }
    }
    echo "Migrations completed.\n";
}

function dbWipe(PDO $pdo)
{
    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);

    if ($driver === 'mysql') {
        // Disable foreign key checks to allow drop tables
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
        $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
        foreach ($tables as $table) {
            echo "Dropping table $table\n";
            $pdo->exec("DROP TABLE IF EXISTS `$table`");
        }
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    } elseif ($driver === 'pgsql') {
        // PostgreSQL: drop all tables in current schema
        $tables = $pdo->query("
            SELECT tablename FROM pg_tables WHERE schemaname = 'public';
        ")->fetchAll(PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            echo "Dropping table $table\n";
            $pdo->exec("DROP TABLE IF EXISTS \"$table\" CASCADE");
        }
    } elseif ($driver === 'sqlite') {
        $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_COLUMN);
        foreach ($tables as $table) {
            echo "Dropping table $table\n";
            $pdo->exec("DROP TABLE IF EXISTS \"$table\"");
        }
    } else {
        echo "Unsupported database driver for db:wipe\n";
        exit(1);
    }

    echo "Database wiped successfully.\n";
}
