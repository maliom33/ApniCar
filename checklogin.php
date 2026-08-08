<?php
function check_login()
{
if(strlen($_SESSION['email'])==0)
	{
		$host = $_SERVER['HTTP_HOST'];
		$uri  = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		$extra="login.php";
		$_SESSION["email"]="";
		header("Location: http://$host$uri/$extra");
	}
}
?>