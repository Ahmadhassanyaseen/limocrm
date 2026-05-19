<?php 

session_start();
unset($_SESSION['user']);
unset($_SESSION['welcome_lead_context']);
header("Location: ../login.php");
