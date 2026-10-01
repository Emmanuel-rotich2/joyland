<?php
declare(strict_types=1);

$dbHost = getenv('DB_HOST') ?: '127.0.0.1';
$dbPort = getenv('DB_PORT') ?: '3306';
$dbName = getenv('DB_NAME') ?: 'fgck_joyland';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPassword = getenv('DB_PASSWORD') ?: '';

$dsn = sprintf(
	'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
	$dbHost,
	$dbPort,
	$dbName
);

try {
	$pdo = new PDO($dsn, $dbUser, $dbPassword, [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::ATTR_EMULATE_PREPARES => false,
	]);
} catch (PDOException $e) {
	exit('Database connection failed. Check the DB_* environment variables.');
}