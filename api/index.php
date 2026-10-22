<?php

// Pastikan folder cache sementara tersedia di /tmp (karena filesystem Vercel read-only)
$tmpDirs = [
    '/tmp/views',
    '/tmp/cache',
    '/tmp/sessions',
    '/tmp/logs',
];

foreach ($tmpDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Forward request serverless Vercel ke index.php Laravel
require __DIR__ . '/../public/index.php';
