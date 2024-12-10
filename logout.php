<?php
session_start();
session_destroy();
file_put_contents('logs/app.log', date("Y-m-d H:i:s") . " - LOGOUT - User logged out\n", FILE_APPEND);
header("Location: index.php");
exit;
?>
