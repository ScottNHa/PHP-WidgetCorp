<?php
	session_start();
	
	// Display the message inside the session if there is one
	function session_message(){
		if(isset($_SESSION['message'])){
			$output  = "<div class=\"message\">";
			$output .= htmlentities($_SESSION["message"]);
			$output .= "</div>"; 
			
			$_SESSION["message"] = null;
			return $output;
		}
	}
	
	// Return the errors inside the session if there is one
	function session_errors(){
		if(isset($_SESSION["errors"])){
			$errors = $_SESSION["errors"];
			$_SESSION["errors"] = null;
			return $errors;
		}
	}
?>