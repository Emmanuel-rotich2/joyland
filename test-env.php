<?php

header('Content-Type: text/plain');

echo "=== VERCEL ENVIRONMENT TEST ===\n\n";

echo "DB_HOST: " . (getenv('DB_HOST') ?: 'NOT FOUND') . "\n";
echo "DB_PORT: " . (getenv('DB_PORT') ?: 'NOT FOUND') . "\n";
echo "DB_NAME: " . (getenv('DB_NAME') ?: 'NOT FOUND') . "\n";
echo "DB_USER: " . (getenv('DB_USER') ?: 'NOT FOUND') . "\n";
echo "DB_PASSWORD: " . (getenv('DB_PASSWORD') !== false ? 'FOUND' : 'NOT FOUND') . "\n";