<?php
require_once "env.php";
// Init SESSION
if (!isset($_SESSION)) {
    session_start();
}

// Page title
$page_title = "Home";

// Import header
require_once "./views/partials/header.php";
?>

<div class="container time-in-out">
    <?php
    require_once "./alert.php";
    ?>
    <div class="row">
        <div class="col-md-6 text-center">
            <img src="assets/images/adrow-logo.png" class="img-fluid" />
        </div>
        <div class="col-md-6">
            <form autocomplete="off" method="post" action="time-in-out.php" class="form">
                <h1 class="title text-center">Adrow Time Record</h1>
                <h2 class="time text-center d-block"><?php echo date('m/d/Y - h:i:s A'); ?></h2>
                <div class="mb-3">
                    <input type="text" class="form-control rounded-0" name="fullname" id="inputFullname" />
                    <ul id="employees" class="pt-3 nav flex-column"></ul>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary submit me-2" name="submit" value="time in">Time In</button>
                    <button type="submit" class="btn btn-primary submit me-2" name="submit" value="am break">AM Break</button>
                    <button type="submit" class="btn btn-primary submit me-2" name="submit" value="lunch">Lunch</button>
                    <button type="submit" class="btn btn-primary submit me-2" name="submit" value="pm break">PM Break</button>
                    <button type="submit" class="btn btn-primary submit ms-2" name="submit" value="time out">Time Out</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
// Import footer
require_once "./views/partials/footer.php";
?>