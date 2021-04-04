<?php
require_once "./database.php";
date_default_timezone_set("Asia/Manila");
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link href="assets/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous" />
        <link href="assets/css/main.css" rel="stylesheet" />
        <title><?php echo $page_title; ?> - Adrow Time Record</title>
    </head>
    <body>
        <header class="d-flex justify-content-between">
            <p class="ms-3">Philippine Time: <span class="time"><?php echo date('m/d/Y - h:i:s A'); ?></span></p>
            <p class="me-3">Version: 5.0.1</p>
        </header>