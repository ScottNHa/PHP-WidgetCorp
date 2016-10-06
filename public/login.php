<?php 
	// Setup session
	require_once("../includes/session.php");

	// Open database connection
	require_once("../includes/db_connection.php");
		
	// Include functions	
	require_once("../includes/functions.php");
	require_once("../includes/validation_functions.php");

	$username = "";

	// If a valid submission came through
	if (isset($_POST['submit'])){
		$username = mysqli_real_escape_string($connection, $_POST['username']);
		$password = $_POST['password'];
	
		// Validations
		$required_fields = array("username", "password");
		validate_presences($required_fields);
	
		// If there are errors
		if(!empty($errors)){
			$_SESSION["errors"] = $errors;
		} else {
			$found_admin = attempt_login($username, $password);
	
			if($found_admin){
				$_SESSION['admin_id'] = $found_admin['id'];
				$_SESSION['username'] = $found_admin['username'];
				redirect_to("admin.php");
			} else {
				$_SESSION['message'] = "Username/password not found.";
			}
		}
	
	}
	
	// Include header layout
	$layout_context = "admin";
	include("../includes/layouts/header.php");

?>

<div id="main">
	
	<div id="navigation">
	</div>
	
	<div id="page">
		<?php
			echo session_message();
			$errors = session_errors();
			echo form_errors($errors);
		?>
		<h2>Login:</h2>
		
		<form action="login.php" method="post">
			<p>Username:
				<input type="text" name="username" value="<?php echo htmlentities($username)?>" />
			</p>
			<p>Password:
				<input type="password" name="password" value="" />
			</p>
			<input type="submit" name="submit" value="Sign In" />
		</form>
	</div>
	
</div>

<?php
	// Include footer layout
	include("../includes/layouts/footer.php");
?>