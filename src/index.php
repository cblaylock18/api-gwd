<?php
header('Content-Type: application/json');

$path = '/var/www/scraper-output/output.json';

if (!file_exists($path)) {
    http_response_code(404);
    echo json_encode(['error' => 'No game data available']);
    exit;
}

$data = file_get_contents($path);
echo $data;