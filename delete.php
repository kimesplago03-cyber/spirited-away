<?php
session_start();
if( !isset($_SESSION['username']) ) {
	header("Location: login.php");
	exit();
} else {
    
}
$id1=$_GET['scid'];
include("conn.php");
mysqli_query($conn , "DELETE FROM $tablelog WHERE ID='$id1'");
$_SESSION['status'] = " Deleted Successfully!";
header("Location: index.php");
?>