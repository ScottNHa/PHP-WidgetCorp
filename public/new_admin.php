<?php 
	// Setup session
	require_once("../includes/session.php");

	// Open database connection
	require_once("../includes/db_connection.php");
		
	// Include functions	
	require_once("../includes/functions.php");
	require_once("../includes/validation_functions.php");
	confirm_logged_in();

	// If a valid submission came through
	if (isset($_POST['submit'])){
		$username = mysqli_real_escape_string($connection, $_POST['username']);
		$hashed_password = password_encrypt($_POST['hashed_password']);
	
		// Validations
		$required_fields = array("username", "hashed_password");
		validate_presences($required_fields);
		$fields_with_max_lengths = array("username" => 15, "hashed_password" => 20);
		validate_max_lengths($fields_with_max_lengths);
	
		// If there are errors
		if(!empty($errors)){
			$_SESSION["errors"] = $errors;
		} else {
			$query = "INSERT INTO admins (username, hashed_password) VALUES ('{$username}', '{$hashed_password}')";
			$result = mysqli_query($connection, $query);
	
			if($result && mysqli_affected_rows($connection) === 1){
				$_SESSION['message'] = "Admin created.";
				$url = "manage_admins.php?";
				redirect_to($url);
			} else {
				$_SESSION['message'] = "Admin creation failed.";
			}
		}
	
	}
	
	// Include header layout
	$layout_context = "admin";
	include("../includes/layouts/header.php");

?>

<div id="main">
	
	<div id="navigation">
		<?php
		$output  = "<br><a href=\"manage_admins.php\">&laquo; Manage Admins</a><br>";
		$output .= "<ul class=\"subjects\">";
		echo $output;
		?>
	</div>
	
	<div id="page">
		<?php
			echo session_message();
			$errors = session_errors();
			echo form_errors($errors);
		?>
		<h2>Add Admin:</h2>
		
		<form action="new_admin.php" method="post">
			<p>Username:
				<input type="text" name="username" value="" />
			</p>
			<p>Password:
				<input type="password" name="hashed_password" value="" />
			</p>
			<input type="submit" name="submit" value="Create Admin" />
		</form>
		<br>
		<a href="manage_admins.php?">Cancel</a>
	</div>
	
</div>

<?php
	// Include footer layout
	include("../includes/layouts/footer.php");
?>