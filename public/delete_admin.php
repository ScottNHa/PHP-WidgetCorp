<?php

	// Setup session
	require_once("../includes/session.php");

	// Open database connection
	require_once("../includes/db_connection.php");
	
	// Include functions	
	require_once("../includes/functions.php");

	$current_admin= find_admin_by_id($_GET["id"]);
	if(!$current_admin){
		redirect_to("manage_admins.php");
	}
	
	$id = $current_admin["id"];
	
	$query = "DELETE FROM admins WHERE id = {$id} LIMIT 1";
	$result = mysqli_query($connection, $query);
	
	if($result && mysqli_affected_rows($connection) === 1){
		$_SESSION["message"] = "Admin deleted.";
		redirect_to("manage_admins.php");
	} else {
		$_SESSION["message"] = "Admin deletion failed.";
		redirect_to("edit_admin.php?id={$id}");	
	}

?>