<?php
// Init SESSION
if (!isset($_SESSION)) {
    session_start();
}

// Page title
$page_title = "Login Admin";

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
            <form autocomplete="off" method="post" action="auth.php" class="form">
                <h1 class="title text-center">Admin Login</h1>
                <div class="mb-3">
                    <input type="email" class="form-control rounded-0" name="email" id="inputEmail" placeholder="Email" />
                </div>
                <div class="mb-3">
                    <input type="password" class="form-control rounded-0" name="password" id="inputPassword" placeholder="Password" />
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary submit">Login</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
// Import footer
require_once "./views/partials/footer.php";
?>