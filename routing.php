<?php
// www/routing.php
if (preg_match('/\.(?:php|html)$/', $_SERVER["REQUEST_URI"])) {
    return false;
} else {
    include __DIR__ . '/start.php';
}