<?php
include('./admin/db.php');
// Initialize the session
session_start();

// Check if the user is already logged in, if yes then redirect him to the appropriate index page
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
        header("location: index.php");
        exit;
    } else {
        header("location: auth-login.php");
        exit;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!empty(trim($_POST['email'])) && !empty(trim($_POST['password'])) && !empty($_POST['g-recaptcha-response'])) {

        $email = mysqli_real_escape_string($link, $_POST['email']);

        $stmt = mysqli_prepare($link, "SELECT id, username, email, password, admin, verify_status, is_blocked FROM users WHERE email = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);

            if ($row['is_blocked'] == 1) {
                $_SESSION['status'] = "Your account is blocked. Please contact the administrator.";
                header("Location: auth-login");
                exit(0);
            }

            if (password_verify($_POST['password'], $row['password'])) {
                if ($row['verify_status'] == 1) {
                    // Verify reCAPTCHA response
                    $recaptchaResponse = $_POST['g-recaptcha-response'];
                    $recaptchaSecretKey = '6LfP7fsmAAAAALHJp2PEPkLGKZBzvmgdpPAvPDj8'; // Replace with your reCAPTCHA Secret Key

                    $recaptchaVerifyUrl = 'https://www.google.com/recaptcha/api/siteverify';
                    $recaptchaData = array(
                        'secret' => $recaptchaSecretKey,
                        'response' => $recaptchaResponse
                    );

                    $recaptchaOptions = array(
                        'http' => array(
                            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                            'method' => 'POST',
                            'content' => http_build_query($recaptchaData),
                        ),
                    );

                    $recaptchaContext = stream_context_create($recaptchaOptions);
                    $recaptchaResult = file_get_contents($recaptchaVerifyUrl, false, $recaptchaContext);
                    $recaptchaResponseData = json_decode($recaptchaResult);

                    if (!$recaptchaResponseData->success) {
                        $_SESSION['status'] = "reCAPTCHA verification failed. Please try again.";
                        header("Location: auth-login.php");
                        exit(0);
                    }

                    // Valid reCAPTCHA response, proceed with login

                    $_SESSION['user_id'] = $row['id']; // set user ID in session variable
                    $_SESSION['email'] = $row['email'];
                    $_SESSION['username'] = $row['username'];
                    $_SESSION["loggedin"] = true;
                    $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];

                    if (!empty($_POST['remember'])) {
                        // set cookie with login credentials
                        setcookie('email', $row['email'], time() + (86400 * 30), "/");
                        setcookie('password', $_POST['password'], time() + (86400 * 30), "/");
                        setcookie('remember', 'checked', time() + (86400 * 30), "/"); // add a remember cookie
                    }else {
                        setcookie('email', '', time() - 3600, "/"); // delete the email cookie
                        setcookie('password', '', time() - 3600, "/"); // delete the password cookie
                        setcookie('remember', '', time() - 3600, "/"); // delete the remember cookie
                    }

                    if ($row['admin'] == 1) { // check if user is an admin
                        $_SESSION['admin'] = true;
                        header("Location: index.php"); // redirect to admin index page
                    } else {
                        $_SESSION['admin'] = false;
                        $_SESSION['status'] = "You are not an admin.";
                        header("Location: index.php"); // redirect to user index page
                    }

                    exit(0);
                } else {
                    $_SESSION['status'] = "Please verify your email address to log in.";
                    header("Location: auth-login.php");
                    exit(0);
                }
            } else {
                $_SESSION['status'] = "Invalid email or password.";
                header("Location: auth-login.php");
                exit(0);
            }
        } else {
            $_SESSION['status'] = "Invalid email or password.";
            header("Location: auth-login.php");
            exit(0);
        }
    } else {
        $_SESSION['status'] = "All fields are mandatory.";
        header("Location: auth-login.php");
        exit(0);
    }
}

// Block or Reactivate User Account
if (isset($_GET['action']) && isset($_GET['user_id'])) {
    $action = $_GET['action'];
    $user_id = $_GET['user_id'];

    if ($action == 'block') {
        // Update the user's is_blocked status to 1 (blocked)
        $stmt = mysqli_prepare($link, "UPDATE users SET is_blocked = 1 WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);

        $_SESSION['status'] = "User account blocked successfully.";
    } elseif ($action == 'reactivate') {
        // Update the user's is_blocked status to 0 (reactivated)
        $stmt = mysqli_prepare($link, "UPDATE users SET is_blocked = 0 WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);

        $_SESSION['status'] = "User account reactivated successfully.";
    }

    header("Location: user_control.php");
    exit(0);
}
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
    <div class="login-area">
        <div class="layout-wrapper">

            <div class="app-content">
                <div class="content-center">
                    <div class="css-1olb7mw">

                        <div class="css-1vmv954">
                            <svg width="35" height="29" version="1.1" viewBox="0 0 30 23"
                                xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <g id="Artboard" transform="translate(-95.000000, -51.000000)">
                                        <g id="logo" transform="translate(95.000000, 50.000000)">
                                            <path id="Combined-Shape" fill="#9155FD"
                                                d="M30,21.3918362 C30,21.7535219 29.9019196,22.1084381 29.7162004,22.4188007 C29.1490236,23.366632 27.9208668,23.6752135 26.9730355,23.1080366 L26.9730355,23.1080366 L23.714971,21.1584295 C23.1114106,20.7972624 22.7419355,20.1455972 22.7419355,19.4422291 L22.7419355,19.4422291 L22.741,12.7425689 L15,17.1774194 L7.258,12.7425689 L7.25806452,19.4422291 C7.25806452,20.1455972 6.88858935,20.7972624 6.28502902,21.1584295 L3.0269645,23.1080366 C2.07913318,23.6752135 0.850976404,23.366632 0.283799571,22.4188007 C0.0980803893,22.1084381 2.0190442e-15,21.7535219 0,21.3918362 L0,3.58469444 L0.00548573643,3.43543209 L0.00548573643,3.43543209 L0,3.5715689 C3.0881846e-16,2.4669994 0.8954305,1.5715689 2,1.5715689 C2.36889529,1.5715689 2.73060353,1.67359571 3.04512412,1.86636639 L15,9.19354839 L26.9548759,1.86636639 C27.2693965,1.67359571 27.6311047,1.5715689 28,1.5715689 C29.1045695,1.5715689 30,2.4669994 30,3.5715689 L30,3.5715689 Z">
                                            </path>
                                            <polygon id="Rectangle" opacity="0.077704" fill="#000"
                                                points="0 8.58870968 7.25806452 12.7505183 7.25806452 16.8305646">
                                            </polygon>
                                            <polygon id="Rectangle" opacity="0.077704" fill="#000"
                                                points="0 8.58870968 7.25806452 12.6445567 7.25806452 15.1370162">
                                            </polygon>
                                            <polygon id="Rectangle" opacity="0.077704" fill="#000"
                                                points="22.7419355 8.58870968 30 12.7417372 30 16.9537453"
                                                transform="translate(26.370968, 12.771227) scale(-1, 1) translate(-26.370968, -12.771227) ">
                                            </polygon>
                                            <polygon id="Rectangle" opacity="0.077704" fill="#000"
                                                points="22.7419355 8.58870968 30 12.6409734 30 15.2601969"
                                                transform="translate(26.370968, 11.924453) scale(-1, 1) translate(-26.370968, -11.924453) ">
                                            </polygon>
                                            <path id="Rectangle" fill-opacity="0.15" fill="#FFF"
                                                d="M3.04512412,1.86636639 L15,9.19354839 L15,9.19354839 L15,17.1774194 L0,8.58649679 L0,3.5715689 C3.0881846e-16,2.4669994 0.8954305,1.5715689 2,1.5715689 C2.36889529,1.5715689 2.73060353,1.67359571 3.04512412,1.86636639 Z">
                                            </path>
                                            <path id="Rectangle" fill-opacity="0.35" fill="#FFF"
                                                transform="translate(22.500000, 8.588710) scale(-1, 1) translate(-22.500000, -8.588710) "
                                                d="M18.0451241,1.86636639 L30,9.19354839 L30,9.19354839 L30,17.1774194 L15,8.58649679 L15,3.5715689 C15,2.4669994 15.8954305,1.5715689 17,1.5715689 C17.3688953,1.5715689 17.7306035,1.67359571 18.0451241,1.86636639 Z">
                                            </path>
                                        </g>
                                    </g>
                                </g>
                            </svg>
                            <h1>PayWell</span></h1>

                        </div>
                        <div class="MuiBox-root css-1fobf8d">
                            <h5 class="MuiTypography-root MuiTypography-h5 css-vsfzib">Welcome to Materio! 👋🏻</h5>
                            <p class="MuiTypography-root MuiTypography-body2 css-4yvesp">Please sign-in to your account
                                and start the adventure</p>
                        </div>

                        <form id="formAuthentication" class="mb-3"
                            action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" accept-charset="UTF-8"
                            method="post">
                            <p><?php echo (isset($_GET['error']) && $_GET['error'] === 'access_denied') ? "Access denied. Please log in to continue." : ""; ?>
                            </p>

                            <?php if (isset($_SESSION['status'])): ?>
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <?php echo $_SESSION['status']; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                            <?php unset($_SESSION['status']);
                           endif; ?>
                            <div class="form mb-3">
                                <label for="exampleInputEmail1" class="form-label">Email address</label>
                                <input type="email" class="form-control" aria-describedby="emailHelp" id="email"
                                    name="email">

                            </div>
                            <div class="form mb-3">
                                <label for="exampleInputPassword1" class="form-label">Password</label>
                                <div class="input-group input-group-merge  ">
                                    <input type="password" id="password" class="form-control" name="password"
                                        value="<?php echo $password; ?>"
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                        aria-describedby="password" />
                                </div>
                            </div>
                            <div class="form mb-3">
                                <div class="g-recaptcha" data-sitekey="6LfP7fsmAAAAANUUVEa-uuXCSHREaH9AcmhIAwHu"></div>
                            </div>




                            <div class="mm-check">

                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="remember-me" name="remember">
                                    <label class="form-check-label" for="exampleCheck1">Remember Me</label>

                                </div>
                                <a href="./forgot-password">Forgot Password?</a>
                            </div>

                            <button type="submit" name="login-btn" class="btn btn-primary">Sign in</button>
                        </form>

                        <div class="mm-b">
                            <h5>New on our platform?</h5>
                            <a href="./auth-register"> Create an account</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

</body>

</html>