<?php
session_start();

// Destroy session to log out the user
session_unset();
session_destroy();

// Redirect to the homepage or login page
header("Location: ../index.html");
exit();
?>
