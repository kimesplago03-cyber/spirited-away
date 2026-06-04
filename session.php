<?php
session_start();
//identify if the user has loggedin or not
if( !isset($_SESSION['username']) ) {
	header("Location: login.php");
	exit();
} else {
    
}?>
