<?php
session_start();
session_unset();
session_destroy();
header("Location: /EXPLORE_SCHORLASHIP-IN-CAMBODIA/html/index.html");
exit();
?>
