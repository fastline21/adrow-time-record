<?php
// Init SESSION
if (!isset($_SESSION)) {
    session_start();
}

// Import database
require_once "database.php";

$errors = [];
$fullname = "";
$action = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullname = $_POST["fullname"];
    $action = $_POST["submit"];
    
    if (!$fullname) {
        $errors[] = "Employee Name is required";
    }

    if (!$action) {
        $errors[] = "Time In or AM Break or Lunch or PM Break or Time Out is required";
    }

    if (empty($errors)) {
        date_default_timezone_set("Asia/Manila");
        $statement_select = $pdo->prepare("SELECT id FROM employees WHERE fullname='" . $fullname . "'");
        $statement_select->execute();
        $employee = $statement_select->fetch(PDO::FETCH_ASSOC);

        if ($employee) {
            $statement_insert = $pdo->prepare("INSERT INTO time_records (employee_id, record_date, action) VALUES (:employee_id, :record_date, :action)");
            $statement_insert->bindValue(":employee_id", $employee["id"]);
            $statement_insert->bindValue(":record_date", date('Y-m-d H:i:s'));
            $statement_insert->bindValue(":action", $action);

            $statement_insert->execute();
            function message($action) {
                $message = "";
                if ($action === "time in") {
                    $message = "Time In";
                } elseif ($action === "time out") {
                    $message = "time out";
                } elseif ($action === "am break") {
                    $message = "AM Break";
                } elseif ($action === "pm break") {
                    $message = "PM Break";
                } else {
                    $message = "Lunch";
                }
                return $message;
            };
            $action = message($action);
            $_SESSION["message"] = "<div class='alert alert-primary alert-dismissible fade show' role='alert'>You are now " . $action . " " . $fullname . "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
        } else {
            $errors[] = "Employee not found";
            $_SESSION["errors"] = $errors;
        }
    } else {
        $_SESSION["errors"] = $errors;
    }
}
header("Location: /adrow-time-record");
?>