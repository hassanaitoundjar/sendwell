<?php



session_start();
include ('./admin/db.php');
if(isset($_GET['token']))
{
    $token = $_GET['token'];

    $verify_query = "SELECT verify_token,verify_status FROM users WHERE verify_token = ? LIMIT 1";
    $stmt = mysqli_prepare($link, $verify_query);
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);
    $verify_query_run = mysqli_stmt_get_result($stmt);

    if(mysqli_num_rows($verify_query_run) > 0)
    {
        $row = mysqli_fetch_array($verify_query_run);
        if($row['verify_status'] == "0")
        {
            $clicked_token = $row['verify_token'];
            $update_query = "UPDATE users SET verify_status='1' WHERE verify_token=? LIMIT 1";
            $stmt = mysqli_prepare($link, $update_query);
            mysqli_stmt_bind_param($stmt, "s", $clicked_token);
            $update_query_run = mysqli_stmt_execute($stmt);

            if($update_query_run)
            {
                $_SESSION['status'] = "your account has been verified";
                header("Location: auth-login.php");
                exit(0);
            }
            else
            {
                $_SESSION['status'] = "verification failed";
                header("Location: auth-login.php");
                exit(0);
            }
        }
        else
        {
            $_SESSION['status'] = "Email already verified. Please login.";
            header("Location: auth-login.php");
            exit(0);
        }
    }
    else
    {
        $_SESSION['status'] = "This token does not exist.";
        header("Location: auth-login.php");
        exit(0);
    }
}
else
{
    header("Location: auth-login.php");
    exit(0);
}




?>