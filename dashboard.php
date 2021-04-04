<?php
// Init SESSION
if (!isset($_SESSION)) {
    session_start();
}

// Page Title
$page_title = "Dashboard";

// Import header
require_once "./views/partials/header.php";

// Initial data
$time_record = [];
$_SESSION["start_selected_date"] = date("Y-m-d");
$_SESSION["end_selected_date"] = date("Y-m-d");
$_SESSION["selected_employee"] = "All";

// Query for time record table
function time_record($start_date, $end_date, $selected_employee) {
    global $pdo;

    if ($selected_employee === "All") {
        // If select is All
        $statement = $pdo->prepare("SELECT employees.fullname, time_records.record_date, time_records.action FROM time_records INNER JOIN employees ON time_records.employee_id=employees.id WHERE DATE(time_records.record_date) BETWEEN :start_record_date AND :end_record_date ORDER BY time_records.record_date ASC");
    } else {
        // If select an employee
        $statement = $pdo->prepare("SELECT employees.fullname, time_records.record_date, time_records.action FROM time_records INNER JOIN employees ON time_records.employee_id=employees.id WHERE employees.fullname = :employee AND DATE(time_records.record_date) BETWEEN :start_record_date AND :end_record_date ORDER BY time_records.record_date ASC");
        // Binding value for employee fullname
        $statement->bindValue(":employee", $selected_employee);
    }
    // Binding value for statement variable
    $statement->bindValue(":start_record_date", $start_date);
    $statement->bindValue(":end_record_date", $end_date);
    // Execute statement
    $statement->execute();
    // Return fetch table
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

// Query for listing employee name
$statement_employee = $pdo->prepare("SELECT fullname FROM employees ORDER BY fullname ASC");
// Execute query
$statement_employee->execute();
// Fetch table
$employees = $statement_employee->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["start_record_date"]) && isset($_POST["end_record_date"]) && isset($_POST["selected_employee"])) {
        // Get time record table using POST
        $time_record = time_record($_POST["start_record_date"], $_POST["end_record_date"], $_POST["selected_employee"]);

        // Add session variable
        $_SESSION["start_selected_date"] = $_POST["start_record_date"];
        $_SESSION["end_selected_date"] = $_POST["end_record_date"];
        $_SESSION["selected_employee"] = $_POST["selected_employee"];
    } else {
        // Set timezone to Asia/Manila
        date_default_timezone_set("Asia/Manila");

        // Fullname variable
        $fullname = $_POST["fullname"];
        
        // Statement for new employee
        $statement_new_employee = $pdo->prepare("INSERT INTO employees (fullname, create_date) VALUES (:fullname, :create_date)");
        $statement_new_employee->bindValue(":fullname", $fullname);
        $statement_new_employee->bindValue(":create_date", date('Y-m-d H:i:s'));

        // Execute statement
        $statement_new_employee->execute();

        // Change location to dashboard
        header("Location: /adrow-time-record/dashboard.php");
        exit;
    }
} else if ($_SERVER["REQUEST_METHOD"] === "GET") {
    // If not admin
    if (!isset($_SESSION["admin"])) {
        // Error message
        $_SESSION["errors"] = ["You are not authorized."];

        // Change location to index
        header("Location: /adrow-time-record");
        exit;
    }
    
    // Get time record table using SESSION
    $time_record = time_record($_SESSION["start_selected_date"], $_SESSION["end_selected_date"], $_SESSION["selected_employee"]);
}

// Empty time record table
$time_record_table = [];

function check_table($fullname, $record_date, $action) {
    // Access Global variable time record table
    global $time_record_table;

    // Convert record date to format date
    $date_only = date_format(date_create($record_date),"F j, Y");

    // Convert record date to format time
    $time_only = date_format(date_create($record_date), "h:i:s A");
    
    // Count Time record table
    if (count($time_record_table) > 0) {
        // Get index of date in time record table
        $time_record_table_key = array_search($date_only, array_column($time_record_table, "date"));

        // If found
        if ($time_record_table_key > -1) {
            // Loop time record table into item key and item value
            foreach ($time_record_table as $item_key => $item_value) {
                // If same item value and date only
                if ($item_value["date"] === $date_only) {
                    // Loop item value into key and value
                    foreach ($item_value as $key => $value) {
                        // If key is int type
                        if (gettype($key) === "integer") {
                            $check_exist = array_search($fullname, array_column($item_value, "fullname"));
                            if (gettype($check_exist) === "integer") {
                                // Action
                                if ($action === "time in") {
                                    $time_record_table[$item_key][$check_exist]["time_in"] = $time_only;
                                } elseif ($action === "am break") {
                                    // If value am break is set
                                    if (isset($time_record_table[$item_key][$check_exist]["am_break"])) {
                                        // Get " - " index
                                        $replace_time = strpos($time_record_table[$item_key][$check_exist]["am_break"], " - ");

                                        // If found index
                                        if ($replace_time) {
                                            // Change am break to latest am break
                                            $time_record_table[$item_key][$check_exist]["am_break"] = substr_replace($time_record_table[$item_key][$check_exist]["am_break"], " - " . $time_only, $replace_time);
                                        } else {
                                            // Concat am break to another am break
                                            $time_record_table[$item_key][$check_exist]["am_break"] .= " - " . $time_only;
                                        }
                                    } else {
                                        // Add am break time only
                                        $time_record_table[$item_key][$check_exist]["am_break"] = $time_only;
                                    }
                                } elseif ($action === "lunch") {
                                    // If value lunch is set
                                    if (isset($time_record_table[$item_key][$check_exist]["lunch"])) {
                                        // Get " - " index
                                        $replace_time = strpos($time_record_table[$item_key][$check_exist]["lunch"], " - ");
                                        
                                        // If found index
                                        if ($replace_time) {
                                            // Change lunch to latest lunch
                                            $time_record_table[$item_key][$check_exist]["lunch"] = substr_replace($time_record_table[$item_key][$check_exist]["lunch"], " - " . $time_only, $replace_time);
                                        } else {
                                            // Concat lunch to another lunch
                                            $time_record_table[$item_key][$check_exist]["lunch"] .= " - " . $time_only;
                                        }
                                    } else {
                                        // Add lunch time only
                                        $time_record_table[$item_key][$check_exist]["lunch"] = $time_only;
                                    }
                                } elseif ($action === "pm break") {
                                    // If value pm break is set
                                    if (isset($time_record_table[$item_key][$check_exist]["pm_break"])) {
                                        // Get " - " index
                                        $replace_time = strpos($time_record_table[$item_key][$check_exist]["pm_break"], " - ");

                                        // If found index
                                        if ($replace_time) {
                                            // Change pm break to pm break lunch
                                            $time_record_table[$item_key][$check_exist]["pm_break"] = substr_replace($time_record_table[$item_key][$check_exist]["pm_break"], " - " . $time_only, $replace_time);
                                        } else {
                                            // Concat pm break to another pm break
                                            $time_record_table[$item_key][$check_exist]["pm_break"] .= " - " . $time_only;
                                        }
                                    } else {
                                        // Add pm break time only
                                        $time_record_table[$item_key][$check_exist]["pm_break"] = $time_only;
                                    }     
                                } else {
                                    // Add time out time only
                                    $time_record_table[$item_key][$check_exist]["time_out"] = $time_only;
                                }
                            } else {
                                // Action
                                if ($action === "time in") {
                                    // Add new entry of exist date time in
                                    $time_record_table[$item_key][] = array("fullname" => $fullname, "time_in" => $time_only);
                                } elseif ($action === "am break") {
                                    // Add new entry of exist date am break
                                    $time_record_table[$item_key][] = array("fullname" => $fullname, "am_break" => $time_only);
                                } elseif ($action === "lunch") {
                                    // Add new entry of exist date lunch
                                    $time_record_table[$item_key][] = array("fullname" => $fullname, "lunch" => $time_only);
                                } elseif ($action === "pm break") {
                                    // Add new entry of exist date pm break
                                    $time_record_table[$item_key][] = array("fullname" => $fullname, "pm_break" => $time_only);     
                                } else {
                                    // Add new entry of exist date time out
                                    $time_record_table[$item_key][] = array("fullname" => $fullname, "time_out" => $time_only);
                                }
                            }

                            // Exit loop
                            break;
                        }
                    }
                }
            }
        } else {
            // Push new array in time record table
            $time_record_table[] = array("date" => $date_only);
            
            // Action
            if ($action === "time in") {
                // Push to lastest array time in
                $time_record_table[count($time_record_table) - 1][] = array("fullname" => $fullname, "time_in" => $time_only);
            } elseif ($action === "am break") {
                // Push to lastest array am break
                $time_record_table[count($time_record_table) - 1][] = array("fullname" => $fullname, "am_break" => $time_only);
            } elseif ($action === "lunch") {
                // Push to lastest array lunch
                $time_record_table[count($time_record_table) - 1][] = array("fullname" => $fullname, "lunch" => $time_only);
            } elseif ($action === "pm break") {
                // Push to lastest array pm break
                $time_record_table[count($time_record_table) - 1][] = array("fullname" => $fullname, "pm_break" => $time_only);       
            } else {
                // Push to lastest array time out
                $time_record_table[count($time_record_table) - 1][] = array("fullname" => $fullname, "time_out" => $time_only);
            }
        }
    } else {
        // Push new array in time record table
        $time_record_table[] = array("date" => $date_only);

        // Action
        if ($action === "time in") {
            // Push to first index time in
            $time_record_table[0][] = array("fullname" => $fullname, "time_in" => $time_only);
        } elseif ($action === "am break") {
            // Push to first index am break
            $time_record_table[0][] = array("fullname" => $fullname, "am_break" => $time_only);
        } elseif ($action === "lunch") {
            // Push to first index lunch
            $time_record_table[0][] = array("fullname" => $fullname, "lunch" => $time_only);
        } elseif ($action === "pm break") {
            // Push to first index pm break
            $time_record_table[0][] = array("fullname" => $fullname, "pm_break" => $time_only);      
        } else {
            // Push to first index time out
            $time_record_table[0][] = array("fullname" => $fullname, "time_out" => $time_only);
        }
    }
}

// Loop time record into key and value
foreach ($time_record as $key => $value) {
    // Extract fullname, record date, and action
    $fullname = $value['fullname'];
    $record_date = $value["record_date"];
    $action = $value["action"];
    // Call check table
    check_table($fullname, $record_date, $action);
}

// Message for action
function message($action) {
    $message = "";
    if ($action === "time in") {
        $message = "Time In";
    } elseif ($action === "time out") {
        $message = "Time out";
    } elseif ($action === "am break") {
        $message = "AM Break";
    } elseif ($action === "pm break") {
        $message = "PM Break";
    } else {
        $message = "Lunch";
    }
    return $message;
};
?>
<div class="container">
    <div class="inline-block my-3">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
            Add Employee
        </button>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#viewEmployeeModal">
            View Employees
        </button>
        <button type="button" class="btn btn-primary" id="printTimeRecord">
            Print Time Record
        </button>
        <a href="logout.php" class="btn btn-primary">Logout</a>
    </div>
    <div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-labelledby="addEmployeeModalLabel" aria-hidden="true">
        <form method="post" action="">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add Employee</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <input type="text" name="fullname" class="form-control" placeholder="Fullname" />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="modal fade" id="viewEmployeeModal" tabindex="-1" aria-labelledby="viewEmployeeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">View Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <?php
                        if (count($employees) > 0) {
                            ?>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Fullname</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($employees as $key => $value) {
                                        ?>
                                        <tr>
                                            <th scope="row"><?php echo $key + 1; ?></th>
                                            <td><?php echo $value["fullname"]; ?></td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <?php
                        } else {
                            ?>
                            <p>No employee found</p>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <form method="post" class="row g-3">
        <div class="col-auto">
            <label for="">Start Date:</label>
            <input type="date" class="form-control" name="start_record_date" value="<?php echo $_SESSION["start_selected_date"]; ?>" />
        </div>
        <div class="col-auto">
            <label for="">End Date:</label>
            <input type="date" class="form-control" name="end_record_date" value="<?php echo $_SESSION["end_selected_date"]; ?>" />
        </div>
        <div class="col-auto">
            <label for="">Employee:</label>
            <select name="selected_employee" class="form-control">
                <option value="All" <?php echo $_SESSION["selected_employee"] === "All" ? "selected" : "" ?>>All</option>
                <?php
                foreach ($employees as $key => $value) {
                    ?>
                    <option value="<?php echo $value["fullname"]; ?>" <?php echo ($_SESSION["selected_employee"] === $value["fullname"]) ? "selected" : ""; ?>><?php echo $value["fullname"]; ?></option>
                    <?php
                }
                ?>
            </select>
        </div>
        <div class="col-auto">
            <br />
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>
    <div class="table-responsive" id="timeRecordTable">
        <?php
        if (count($time_record_table) > 0) {
            foreach ($time_record_table as $item_key => $item_value) {
                ?>
                <h3 class="mt-5"><?php echo $item_value["date"]; ?></h3>
                <?php
                if (gettype($item_value) === "array") {
                    ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Fullname</th>
                                <th scope="col">Time In</th>
                                <th scope="col">AM Break</th>
                                <th scope="col">Lunch</th>
                                <th scope="col">PM Break</th>
                                <th scope="col">Time Out</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($item_value as $key => $value) {
                                if (gettype($key) === "integer") {
                                    ?>
                                    <tr>
                                        <th scope="row"><?php echo (int)$key + 1 ; ?></th>
                                        <td><?php echo $value["fullname"]; ?></td>
                                        <td><?php if (isset($value["time_in"])) echo $value["time_in"]; ?></td>
                                        <td><?php if (isset($value["am_break"])) echo $value["am_break"]; ?></td>
                                        <td><?php if (isset($value["lunch"])) echo $value["lunch"]; ?></td>
                                        <td><?php if (isset($value["pm_break"])) echo $value["pm_break"]; ?></td>
                                        <td><?php if (isset($value["time_out"])) echo $value["time_out"]; ?></td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                    <?php
                }
            }
        } else {
            ?>
            <p class="mt-3">No record found</p>
            <?php
        }
        ?>
    </div>
    <iframe id="printFrame" name="print_frame" width="0" height="0" frameborder="0" src="about:blank" contenteditable="true"></iframe>
</div>

<?php
// Import footer
require_once "./views/partials/footer.php";
?>
