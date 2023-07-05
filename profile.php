<?php
include ('../admin/db.php');
include ('../admin/include/userid.php');
// Check if the user is not logged in, redirect to the login page with an error message
include('./admin/include/checker-uesr.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];

    // Update user data
    if (isset($_POST['update_data'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];

        $stmt = mysqli_prepare($link, "UPDATE users SET username = ?, email = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "ssi", $username, $email, $user_id);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_affected_rows($stmt) > 0) {
            $_SESSION['status'] = "User data updated successfully!";
        } else {
            $_SESSION['status'] = "User data updated successfully!";
        }
    }

    // Change password
    if (isset($_POST['change_password'])) {
        $old_password = $_POST['old_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        $stmt = mysqli_prepare($link, "SELECT password FROM users WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        $stored_password = $row['password'];

        if (password_verify($old_password, $stored_password)) {
            if ($new_password === $confirm_password) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                $stmt = mysqli_prepare($link, "UPDATE users SET password = ? WHERE id = ?");
                mysqli_stmt_bind_param($stmt, "si", $hashed_password, $user_id);
                mysqli_stmt_execute($stmt);

                if (mysqli_stmt_affected_rows($stmt) > 0) {
                    $_SESSION['status'] = "Password updated successfully!";
                } else {
                    $_SESSION['status'] = "Error updating password.";
                }
            } else {
                $_SESSION['status'] = "New password and confirm password do not match.";
            }
        } else {
            $_SESSION['status'] = "Incorrect old password.";
        }
    }
    mysqli_stmt_close($stmt);
}
mysqli_close($link);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>DarkPan - Bootstrap 5 Admin Template</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">
    <?php


include('./admin/include/header.php')

     
     ?>
</head>

<body>
    <div class="page-content   ">
        <!-- Spinner Start -->
        <div id="spinner"
            class="show bg-dark position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->


        <!-- Sidebar Start -->
        <?php


include('../admin/include/sidebar.php')

     
     ?>
        <!-- Sidebar End -->


        <!-- Content Start -->
        <div class="content w-100 align-items-center ">
            <!-- Navbar Start -->
            <?php


include('../admin/include/navbar.php')

     
     ?>
            <!-- Navbar End -->


            <!-- Sale & Revenue Start -->
            <div class=" pt-4 px-4">
                <div class="row g-4">
                    <div class="col-sm-12 col-xl-12">
                        <div class="bg-white rounded d-flex align-items-center justify-content-between p-4">
                            <i class="fa fa-chart-line fa-3x text-primary"></i>
                            <div class="ms-3">
                                <p class="mb-2">Today Sale</p>
                                <h6 class="mb-0">$1234</h6>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!-- Sale & Revenue End -->


            <!-- Sales Chart Start -->
            <div class=" pt-4 px-4">

                <div class="row g-4">
                    <div class="profile ">
                        <div class="col-sm-12 col-xl-12">

                            <h2 class="text-muted">My profile</h2>
                        </div>

                    </div>
                </div>
            </div>


            <div class="card-with pt-4 px-4">
                <div class="row g-4">
                    <div class="col-sm-12 col-xl-12">
                        <div class="chartone text-center rounded p-4">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <h6 class="mb-0  ">Your details</h6>

                            </div>
                            <?php if (isset($_SESSION['status'])): ?>
                            <div class="alert <?php echo ($_SESSION['status'] == 'Error updating user data.' || $_SESSION['status'] == 'Error updating password.' || $_SESSION['status'] == 'New password and confirm password do not match.' || $_SESSION['status'] == 'Incorrect old password.') ? 'alert-danger' : 'alert-success'; ?> alert-dismissible fade show"
                                role="alert">
                                <?php echo $_SESSION['status']; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                            <?php unset($_SESSION['status']);
                            endif; ?>

                            <form class="update mb-3" id="formAuthentication"
                                action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" accept-charset="UTF-8"
                                method="post">
                                <div class="mb-3   form-row">
                                    <label for="exampleInputEmail1" class="form-label">Username</label>
                                    <input type="text" id="username" name="username" class="form-control"
                                        aria-describedby="emailHelp" value="<?php echo htmlspecialchars($username); ?>">

                                </div>
                                <hr>
                                <div class="mb-3  form-row">
                                    <label for="exampleInputEmail1" class="form-label">Email address</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        aria-describedby="emailHelp" value="<?php echo htmlspecialchars($email); ?>">

                                </div>

                                <button type="submit" name="update_data"
                                    class=" d-flex justify-content-start align-items-center btn btn-primary">Update
                                </button>

                                <hr>
                                <div class="mb-3 form-row">
                                    <label for="disabledTextInput" class="form-label">Password</label>
                                    <input type="password" class="form-control " id="disabledTextInput"
                                        placeholder="****************" disabled>
                                    <!-- Button trigger modal -->



                                </div>


                                <div class="change-password">
                                    <button type="button"
                                        class="d-flex justify-content-start align-items-center  btn btn-primary"
                                        data-bs-toggle="modal" data-bs-target="#exampleModal">
                                        Change Password
                                    </button>
                                    <!-- Modal -->
                                    <div class="modal fade" id="exampleModal" tabindex="-1"
                                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title text-muted" id="exampleModalLabel">Change
                                                        Password</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <form id="formAccountSettings"
                                                    action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>"
                                                    accept-charset="UTF-8" method="post">

                                                    <div class="modal-body">
                                                        <label for="old_password" class="form-label">Old
                                                            Password</label>
                                                        <input type="password" name="old_password" class="form-control"
                                                            id="old_password">
                                                        <label for="new_password" class="form-label">New
                                                            Password</label>
                                                        <input type="password" name="new_password" class="form-control"
                                                            id="new_password">
                                                        <label for="confirm_password" class="form-label">Confirm
                                                            Password</label>
                                                        <input type="password" name="confirm_password"
                                                            class="form-control" id="confirm_password">
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" name="change_password"
                                                            class="btn btn-primary">Save
                                                            changes</button>
                                                    </div>
                                                </form>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>

                                <!--                                 
                                <div class="account">

                                    <button type="submit" name="update_data" class="btn btn-primary">Update </button>
                                    <button type="button" class="btn btn-primary">Delete Account</button>
                                </div> -->



                            </form>
                        </div>
                    </div>

                </div>
            </div>
            <!-- Sales Chart End -->







            <!-- Footer Start -->
            <div class=" pt-4 px-4">
                <div class=" rounded-top p-4">
                    <div class="row">
                        <div class="col-12 col-sm-6 text-center text-sm-start">
                            &copy; <a href="#">Your Site Name</a>, All Right Reserved.
                        </div>
                        <div class="col-12 col-sm-6 text-center text-sm-end">
                            <!--/*** This template is free as long as you keep the footer author’s credit link/attribution link/backlink. If you'd like to use the template without the footer author’s credit link/attribution link/backlink, you can purchase the Credit Removal License from "https://htmlcodex.com/credit-removal". Thank you for your support. ***/-->
                            Designed By <a href="https://htmlcodex.com">HTML Codex</a>
                            <br>Distributed By: <a href="https://themewagon.com" target="_blank">ThemeWagon</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer End -->
        </div>
        <!-- Content End -->


        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <!-- JavaScript Libraries -->
    <?php


include('../admin/include/javascript.php')

     
     ?>
</body>

</html>