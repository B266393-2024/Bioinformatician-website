<?php
// This script handles user logout by first starting the session to access any existing session data,
// then clearing all session variables using session_unset(), and completely destroying the session with session_destroy().
// After successfully logging the user out, it redirects them to the homepage (index.php) using the header() function,
// and finally calls exit() to terminate the script and ensure no further code is executed.
session_start();
session_unset();
session_destroy();
header("Location: index.php");
exit();
?>
