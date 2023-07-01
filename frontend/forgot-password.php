<?php
session_start();
// Include config file
include ('../admin/db.php');
require ('../vendor/autoload.php');
include ('../mail/mailer.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $email = mysqli_real_escape_string($link, $_POST['email']); 
    $token = md5(rand());

    $check_email= "SELECT username, email FROM users WHERE email ='$email' LIMIT 1";
    $check_email_run= mysqli_query($link , $check_email);

    if(mysqli_num_rows($check_email_run) > 0){
        $row= mysqli_fetch_array($check_email_run);
        $get_username = $row['username'];
        $get_email = $row['email'];

        $update_token = "UPDATE users SET verify_token='$token' WHERE email = '$get_email' LIMIT 1";
        $update_token_run= mysqli_query ($link , $update_token);
        if($update_token_run) {
          $config = include ('../mail/mailer.php');
          $email_config = $config['email'];
          $mail = new PHPMailer;

          // Configure mailer settings
            $mail->isSMTP();
            $mail->Host = $email_config['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $email_config['username'];
            $mail->Password = $email_config['password'];
            $mail->SMTPSecure = 'tls';
            $mail->Port = $email_config['port'];

            $mail->setFrom($email_config['admin_email'], $email_config['admin_name']);
            $mail->addAddress($get_email);
            $mail->Subject = 'Reset your password';
            $mail->Body = "Hi $get_username,\n\nWe've had a request for a password reset on your account. If you requested this then you can change your password now by visiting the URL: \n\n https://iptvsmartersproo.com/frontend/new-password.php?token=$token&email=$get_email \n\n If you did not request this then you need to take no further action and your password will not change. If this email alarms you please contact support for further help. \n\n Thanks, ";

            if(!$mail->send()) {
                $_SESSION['status'] = 'Message could not be sent.';
                $_SESSION['error'] = $mail->ErrorInfo;
                header("location: forgot-password.php");
                exit(0);
            } else {
                // Redirect to login page
                header("location: auth-login.php");
                exit(0);
            }
        }
        else {
            $_SESSION['status'] = 'Something went wrong. #1';
            header("location: forgot-password.php");
            exit(0);
        }
    }
    else {
        $_SESSION['status'] = 'No email found.';
        header("location: forgot-password.php");
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
    <?php


include('../admin/include/header.php')

     
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
                            <h5 class="MuiTypography-root MuiTypography-h5 css-vsfzib">Forgot your password? 🔒</h5>
                            <p class="MuiTypography-root MuiTypography-body2 css-4yvesp">Enter your account
                                email address and we’ll send you a link to reset your password.</p>
                        </div>

                        <form id="formAuthentication" class="mb-3"
                            action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" accept-charset="UTF-8"
                            method="post">
                            <div class="form mb-3">
                                <label for="exampleInputEmail1" class="form-label">Email address</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="Enter your email" aria-describedby="emailHelp">

                            </div>


                            <button type="submit" name="reset" class="btn btn-primary">Continue</button>
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