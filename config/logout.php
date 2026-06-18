<?php 

session_start();
unset($_SESSION['user']);
unset($_SESSION['welcome_lead_context']);
unset($_SESSION['explore_send_id']);
header("Location: ../login.php");
