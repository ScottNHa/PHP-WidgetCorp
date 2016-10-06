<?php 
	// Setup session
	require_once("../includes/session.php");

	// Open database connection
	require_once("../includes/db_connection.php");
		
	// Include functions	
	require_once("../includes/functions.php");
	confirm_logged_in();
	
	// Set current subject/page if there is one
	$current_admin = find_admin_by_id($_GET["id"]);
	
	if(!$current_admin){
		redirect_to("manage_admins.php");
	} else {
		require_once("../includes/validation_functions.php");
	
		// If a valid submission came through
		if (isset($_POST['submit'])){
			$id = $current_admin['id'];
			$username = mysqli_real_escape_string($connection, $_POST['username']);
			$password = password_encrypt($_POST['password']);
		
			// Validations
			$required_fields = array("username", "password");
			validate_presences($required_fields);
			$fields_with_max_lengths = array("username" => 15, "password" => 20);
			validate_max_lengths($fields_with_max_lengths);
		
			if(!empty($errors)){
				$_SESSION["errors"] = $errors;
			} else {
				$query = "UPDATE admins SET username = '{$username}', hashed_password = '{$password}' WHERE id = {$id} LIMIT 1";
				$result = mysqli_query($connection, $query);
		
				if($result && mysqli_affected_rows($connection) === 1){
					$_SESSION['message'] = "Admin updated.";
					$url = "manage_admins.php?id=" . urlencode($current_admin["id"]);
					redirect_to($url);
				} elseif($result && mysqli_affected_rows($connection) === 0){
					$_SESSION['message'] = "Nothing was changed.";
				} else {
					$_SESSION['message'] = "Admin update failed.";
				}
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
		<h2>Edit Admin: <?php echo htmlentities($current_admin["username"]); ?></h2>
		
		<form action="edit_admin.php?id=<?php echo urlencode($current_admin["id"]); ?>" method="post">
			<p>Username:
				<input type="text" name="username" value="<?php echo htmlentities($current_admin["username"]); ?>" />
			</p>
			<p>Password:
				<input type="password" name="password" value="" />
			</p>
			<input type="submit" name="submit" value="Edit Admin" />
		</form>
		<br>
		<a href="manage_admins.php">Cancel</a>
		&nbsp;
		<a href="delete_admin.php?id=<?php echo urlencode($current_admin["id"]); ?>" onclick="return confirm('Are you sure?');">Delete Current Admin</a>
	</div>
	
</div>

<?php
	// Include footer layout
	include("../includes/layouts/footer.php");
?>