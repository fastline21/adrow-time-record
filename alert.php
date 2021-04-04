<?php
if (isset($_SESSION["message"])) {
    echo $_SESSION["message"];
    unset($_SESSION["message"]);
}
if (isset($_SESSION["errors"])) {
    $errors = $_SESSION["errors"];
    foreach ($errors as $key => $value) {
        echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>" . $value . "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
    }
    unset($_SESSION["errors"]);
}
?>