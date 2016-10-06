<?php

	// Setup session
	require_once("../includes/session.php");

	// Open database connection
	require_once("../includes/db_connection.php");
	
	// Include functions	
	require_once("../includes/functions.php");

	$current_subject = find_subject_by_id($_GET["subject"], false);
	if(!$current_subject){
		redirect_to("manage_content.php");
	}
	$id = $current_subject["id"];
	
	// Check if the current subject has no children
	$pages_set = find_pages_for_subject($id);
	if(mysqli_num_rows($pages_set) > 0){
		$_SESSION["message"] = "Cannot delete a subject with pages.";
		redirect_to("manage_content.php?subject={$id}");
	} else {
		$query = "DELETE FROM subjects WHERE id = {$id} LIMIT 1";
		$result = mysqli_query($connection, $query);
	
		if($result && mysqli_affected_rows($connection) === 1){
			$_SESSION["message"] = "Subject deleted.";
			redirect_to("manage_content.php");
		} else {
			$_SESSION["message"] = "Subject deletion failed.";
			redirect_to("manage_content.php?subject={$id}");
		}	
	}

?>