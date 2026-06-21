<?php
// Destroy the current session and redirect to home page
session_start();
session_unset();
session_destroy();
header('Location: index.php');
exit();
?>