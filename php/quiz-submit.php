<?php
session_start();
$_SESSION['completedQuiz'] = true;
header("Location: ../index.html");
exit;
?>
