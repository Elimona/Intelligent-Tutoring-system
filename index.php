<?php
// public/index.php

// Autoload dependencies
require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables if using Dotenv
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Basic routing (simple example)
$requestUri = $_SERVER['REQUEST_URI'];
switch ($requestUri) {
    case '/':
        require __DIR__ . '/../src/views/home.php';
        break;
    case '/login':
        require __DIR__ . '/login.php';
        break;
    default:
        http_response_code(404);
        echo "Page not found";
        break;
}
