<?php
require_once __DIR__ . '/config/functions.php';

// Only destroy customer session, keep admin session intact
unset($_SESSION['customer_id']);
unset($_SESSION['cart']);
redirect('index.php');
