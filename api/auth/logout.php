<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

session_destroy();
respond(['message' => 'Logged out']);
