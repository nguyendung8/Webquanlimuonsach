<?php

    include 'config.php';

    session_start();

    $user_id = $_SESSION['user_id'];
    
    if($user_id) {
        mysqli_query($conn, "UPDATE users SET is_logged_in = 0 WHERE id = $user_id") or die('query failed');
    }
    session_unset();
    session_destroy();

    header('location:login.php');

?>