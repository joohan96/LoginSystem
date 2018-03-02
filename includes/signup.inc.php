<?php

# Check if the user has actually called this file using submit
# The public will be able to access this file if you don't put this check
if (isset($_POST['submit'])) {
    
    include_once 'dbh.inc.php';

    # "mysqli_real_escape_string" prevents user to manuall change code 
    $first = mysqli_real_escape_string($conn, $_POST['first']);
    $last = mysqli_real_escape_string($conn, $_POST['last']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $uid = mysqli_real_escape_string($conn, $_POST['uid']);
    $pwd = mysqli_real_escape_string($conn, $_POST['pwd']);

    # Error Handlers
    # Check for empty fields 
    if (empty($first) || empty($last) || empty($email) || empty($uid) || empty($pwd)) {
        header("Location: ../signup.php?signup=empty");
        exit();
    } else {
        # Check if input characters are valid
        if (!preg_match("/^[a-zA-Z]*$/", $first) || !preg_match("/^[a-zA-Z]*$/", $last)){
            header("Location: ../signup.php?signup=invalid");
            exit();
        } else {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL))  {
                header("Location: ../signup.php?signup=email");
                exit();
            } else {
                $sql = "SELECT * FROM users WHERE user_uid='$uid'";
                $result = mysqli_query($conn, $sql);
                $resultCheck = mysqli_num_rows($result);

                if ($resultCheck > 0) {
                    header("Location: ../signup.php?signup=usertaken");
                    exit();
                } else {
                    # Hash the password so that even people with access to the database can't recognize them
                    $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);
                    
                    # Insert user into the db
                    $sql = "INSERT INTO users (user_first, user_last, user_email, user_uid, user_pwd) VALUES ('$first', '$last', '$email', '$uid', '$hashedPwd');";
                    mysqli_query($conn, $sql);
                    header("Location: ../signup.php?signup=success");
                    exit();
                }
            }
        }
    }

} else {
    # Send the user to signup.php
    header("Location : ../signup.php");
    exit();
}