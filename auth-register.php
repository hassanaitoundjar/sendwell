<?php
// Include config file
include ('./admin/db.php');
require ('./vendor/autoload.php');
include ('./mail/mailer.php');


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$username = $password = $confirm_password = $email = "";
$username_err = $password_err = $confirm_password_err = $email_err ="";



 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    
    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))){
        $username_err = "Username can only contain letters, numbers, and underscores.";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = trim($_POST["username"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "This username is already taken.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    // Validate email
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter an email address.";
    } elseif(!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)){
        $email_err = "Please enter a valid email address.";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE email = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            
            // Set parameters
            $param_email = trim($_POST["email"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $email_err = "This email address is already taken.";
                    $_SESSION['status'] = "This email address is already taken.";
                } else{
                    $email = trim($_POST["email"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
   
    
    
    
    // Validate password
if(empty(trim($_POST["password"]))){
  $password_err = "Please enter a password.";     
} elseif(strlen(trim($_POST["password"])) < 6){
  $password_err = "Password must have at least 6 characters.";
} else{
  $password = trim($_POST["password"]);
}

// Validate confirm password
if (!isset($_POST["confirm_password"]) || empty(trim($_POST["confirm_password"]))){
  $confirm_password_err = "Please confirm password.";     
} else{
  $confirm_password = trim($_POST["confirm_password"]);
  if(empty($password_err) && ($password != $confirm_password)){
      $confirm_password_err = "Password did not match.";
  }
}


    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)&& empty($email_err)){
        

        

        // Prepare an insert statement
        $sql = "INSERT INTO users (username, password, email , verify_token,admin) VALUES (?, ?, ?, ?, ? )";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssssi", $param_username, $param_password, $param_email, $param_verify_token, $param_admin  );

            
            
            // Set parameter
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT);
            $param_email = $email;
            $param_verify_token = bin2hex(random_bytes(32));
            $param_admin = 0;
        
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
               // Send verification email
               $config = include ('./mail/mailer.php');
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
               $mail->addAddress($email, $username);
               $mail->Subject = 'Verify your email';
               $mail->Body = "Hello $username,\n\nPlease click on the following link to verify your email address:\n\nhttps://iptvsmartersproo.com/verify-email.php?token=$param_verify_token";

               if(!$mail->send()) {
                   echo 'Message could not be sent.';
                   echo 'Mailer Error: ' . $mail->ErrorInfo;
               } else {
                   // Redirect to login page
                   header("location: auth-login.php");
               }
           } else{
               echo "Oops! Something went wrong. Please try again later.";
           }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }


    

    
    
    // Close connection
    mysqli_close($link);
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
                            <!-- Add the error messages alert here -->
                            <?php if (!empty($username_err) || !empty($email_err) || !empty($password_err) || !empty($confirm_password_err)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <ul>
                                    <?php if (!empty($username_err)): ?>
                                    <li><?php echo $username_err; ?></li>
                                    <?php endif; ?>
                                    <?php if (!empty($email_err)): ?>
                                    <li><?php echo $email_err; ?></li>
                                    <?php endif; ?>
                                    <?php if (!empty($password_err)): ?>
                                    <li><?php echo $password_err; ?></li>
                                    <?php endif; ?>
                                    <?php if (!empty($confirm_password_err)): ?>
                                    <li><?php echo $confirm_password_err; ?></li>
                                    <?php endif; ?>
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>

                            </div>
                            <?php endif; ?>
                            <div class="form mb-3">
                                <label for="exampleInputEmail1" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username"
                                    value="<?php echo $username; ?>" placeholder="Enter your username" autofocus
                                    aria-describedby="emailHelp">

                            </div>
                            <div class="form mb-3">
                                <label for="exampleInputEmail1" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="<?php echo $email; ?>" placeholder="Enter your Email" autofocus
                                    aria-describedby="emailHelp">

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
                                <label for="exampleInputPassword1" class="form-label">Confirm Password</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="confirm_password" class="form-control"
                                        name="confirm_password" value="<?php echo $confirm_password; ?>"
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                        aria-describedby="password" />
                                </div>
                            </div>


                            <button type="submit" name="submit" value="Sign up" class="btn btn-primary">Sign in</button>
                        </form>
                        <div class="mm-b">
                            <h5>Already have an account?</h5>
                            <a href="./auth-login">Sign in</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
    <!-- To clear the input fields when the page is loaded -->
    <script>
    window.onload = function() {
        document.getElementById("username").value = "";
        document.getElementById("email").value = "";
        document.getElementById("password").value = "";
        document.getElementById("confirm_password").value = "";
    };
    </script>
</body>

</html>