<?php
setcookie("firebase_token", "", time() - 3600, "/");
header("Location: index.php?success=Logout Success");
exit;
?>