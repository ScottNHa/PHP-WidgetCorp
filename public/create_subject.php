<?php
	// Setup a session
	require_once("../includes/session.php");

	// Open database connection
	require_once("../includes/db_connection.php");
	
	// Include functions	
	require_once("../includes/functions.php");
	require_once("../includes/validation_functions.php");
	confirm_logged_in();
	
	// If a valid submission came through
	if (isset($_POST['submit'])){
		$menu_name = mysqli_real_escape_string($connection, $_POST['menu_name']);
		$position = (int) $_POST['position'];
		$visible = (int) $_POST['visible'];
		
		// Validations
		$required_fields = array("menu_name", "position", "visible");
		validate_presences($required_fields);
		$fields_with_max_lengths = array("menu_name" => 30);
		validate_max_lengths($fields_with_max_lengths);
		
		if(!empty($errors)){
			$_SESSION["errors"] = $errors;
			redirect_to("new_subject.php");
		} else {
			$query = "INSERT INTO subjects (menu_name, position, visible) VALUES ('{$menu_name}', {$position}, {$visible})";
			$result = mysqli_query($connection, $query);
		
			if($result){
				$_SESSION['message'] = "Subject created.";
				redirect_to("manage_content.php");
			} else {
				$_SESSION['message'] = "Subject creation failed.";
				redirect_to("new_subject.php");
			}
		}
		
	} else {
		redirect_to("new_subject.php");
	}

	// Close database connection
	if(isset($connection)){
		mysqli_close($connection);
	}
?>