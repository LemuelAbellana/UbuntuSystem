<?php

// Production settings - disable error display
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/var/log/php_errors.log');

// Entry point for the application
require_once __DIR__ . '/../routes/web.php';
