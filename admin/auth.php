<?php
require_once __DIR__ . '/../config.php';
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}
requireLogin();
