<?php
require_once __DIR__ . '/config/functions.php';

session_destroy();
redirect('index.php');
