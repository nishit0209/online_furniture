<?php

if ($authenticated) {
    session_start();
    $_SESSION['user_logged_in'] = true;
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    header("Location: profile.php");
    exit();
} 