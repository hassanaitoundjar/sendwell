<?php

// Include config file
include ('../admin/db.php');
require ('../vendor/autoload.php');
include ('../mail/mailer.php');
session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($link, $_POST['email']); 
    $new_password = mysqli_real_escape_string($link, $_POST['new_password']); 
    $confirm_password = mysqli_real_escape_string($link, $_POST['password_confirm']); 
    $token = mysqli_real_escape_string($link, $_POST['password_token']); 

    if(!empty($token)) {
        if(!empty($email) && !empty($new_password) && !empty($confirm_password   )) {
            $check_token = "SELECT verify_token FROM users WHERE verify_token = '$token' LIMIT 1";
            $check_token_run = mysqli_query($link, $check_token);

            if(mysqli_num_rows($check_token_run) > 0) {
                if($new_password == $confirm_password) {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $update_password = "UPDATE users SET password='$hashed_password' WHERE verify_token='$token' LIMIT 1";
                    $update_password_run = mysqli_query($link, $update_password);

                    if($update_password_run) {
                        $new_token= md5(rand());
                        $update_new_token = "UPDATE users SET verify_token='$new_token' WHERE verify_token='$token' LIMIT 1";
                        $update_token_new_run = mysqli_query($link, $update_new_token);
                        $_SESSION['status'] = 'New Password Successfully Updated.';
                        header("location: auth-login.php");
                        exit(0);
                    } else {
                        $_SESSION['status'] = 'Did not update password. Something went wrong.';
                        header("location: new-password.php?token=$token&email=$email");
                        exit(0);
                    }
                } else {
                    $_SESSION['status'] = 'Password and Confirm Password do not match.';
                    header("location: new-password.php?token=$token&email=$email");
                    exit(0);
                }
            } else {
                $_SESSION['status'] = 'Invalid url.';
                header("location: new-password.php?token=$token&email=$email");
                exit(0);
            }
        } else {
            $_SESSION['status'] = 'Please fill all the fields.';
            header("location: new-password.php?token=$token&email=$email");
            exit(0);
        }
    } else {
        $_SESSION['status'] = 'Token not available.'; 
        header("location: new-password.php");
        exit(0);
    }
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
    <?php include('/admin/include/header.php')?>
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
                            <h5 class="MuiTypography-root MuiTypography-h5 css-vsfzib">Forgot your password? 🔒</h5>
                            <p class="MuiTypography-root MuiTypography-body2 css-4yvesp">Enter your account
                                email address and we’ll send you a link to reset your password.</p>
                        </div>

                        <form id="formAuthentication" class="mb-3"
                            action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" accept-charset="UTF-8"
                            method="post">
                            <div class="form mb-3">
                                <input type="hidden" name="password_token"
                                    value="<?php if(isset($_GET['token'])){echo $_GET['token'];}  ?>">
                                <label for="email" class="form-label">Email</label>
                                <input type="text" class="form-control" id="email" name="email"
                                    placeholder="Enter your email" autofocus
                                    value="<?php if(isset($_GET['email'])){echo $_GET['email'];}  ?>" />

                            </div>
                            <div class="form mb-3">
                                <label for="exampleInputPassword1" class="form-label">New Password</label>
                                <input type="password" id="password" class="form-control" name="new_password">
                            </div>
                            <div class="form mb-3">
                                <label for="exampleInputPassword1" class="form-label">Confirm your password</label>
                                <input type="password" id="password" class="form-control" name="password_confirm">
                            </div>


                            <button type="submit" name="reset" value="Continue" class="btn btn-primary">Submit</button>
                        </form>
                        <div class="mm-b">

                            <a href="auth-login.php">Return to sign in</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

</body>

</html>