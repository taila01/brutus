<?php
session_start();
$_SESSION = [];
session_destroy();
header('Location: /brutus/login/login.php');
exit;
