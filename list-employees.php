<?php
// Import database
require_once "./database.php";

// Query employees
$statement = $pdo->prepare("SELECT * FROM employees ORDER BY fullname ASC");

// Execute query
$statement->execute();

// Fetch employees
$employees = $statement->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    header("Content-type: application/json");
    echo json_encode($employees);
}
?>