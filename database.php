<?php
require_once "env.php";
$db_host = $_ENV["DB_HOST"];
$db_port = $_ENV["DB_PORT"];
$db_name = $_ENV["DB_NAME"];
$db_username = $_ENV["DB_USERNAME"];
$db_password = $_ENV["DB_PASSWORD"];
$pdo = new PDO("mysql:host=" . $db_host . ";port=" . $db_port . ";dbname=" . $db_name . "", $db_username, $db_password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>