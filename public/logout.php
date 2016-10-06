<?php
	// Setup Session
	require_once("../includes/session.php");
	
	// Include redirect function
	require_once("../includes/functions.php");
	
	/*
	// #1: Simple Session Wipe
	$_SESSION["admin_id"] = null;
	$_SESSION["username"] = null;
	redirect_to("login.php");
	*/
	
	// #2: Full Session Destroy
	$_SESSION = array();
	if(isset($_COOKIE[session_name()])){
		setcookie(session_name(), '', time()-42000, '/');
	}
	session_destroy();
	redirect_to("login.php");
	
?>