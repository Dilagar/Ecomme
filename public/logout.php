<?php
require_once __DIR__ . '/../lib/auth.php';
user_logout();
redirect('/Ecomme/public/login.php');
?>

