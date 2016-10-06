<?php 
	// Setup session
	require_once("../includes/session.php");

	// Open database connection
	require_once("../includes/db_connection.php");
		
	// Include functions	
	require_once("../includes/functions.php");
	confirm_logged_in();
	
	// Set current subject/page if there is one
	find_selected();
	
	if(!$current_subject){
		redirect_to("manage_content.php");
	} else {
		require_once("../includes/validation_functions.php");
	
		// If a valid submission came through
		if (isset($_POST['submit'])){
			$id = $current_subject["id"];
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
			} else {
				$query = "UPDATE subjects SET menu_name = '{$menu_name}', position = {$position}, visible = {$visible} WHERE id = {$id} LIMIT 1";
				$result = mysqli_query($connection, $query);
		
				if($result && mysqli_affected_rows($connection) === 1){
					$_SESSION['message'] = "Subject updated.";
					redirect_to("manage_content.php");
				} elseif($result && mysqli_affected_rows($connection) === 0){
					$_SESSION['message'] = "Nothing was changed.";
				} else {
					$_SESSION['message'] = "Subject update failed.";
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
		echo navigation($current_subject, $current_page);
		?>
	</div>
	
	<div id="page">
		<?php
			echo session_message();
			$errors = session_errors();
			echo form_errors($errors);
		?>
		<h2>Edit Subject: <?php echo htmlentities($current_subject["menu_name"]); ?></h2>
		
		<form action="edit_subject.php?subject=<?php echo urlencode($current_subject["id"]); ?>" method="post">
			<p>Menu name:
				<input type="text" name="menu_name" value="<?php echo htmlentities($current_subject["menu_name"]); ?>" />
			</p>
			<p>Position:
				<select name="position">
					<?php
					$subject_set = find_all_subjects(false);
					$subject_count = mysqli_num_rows($subject_set);
					
					for($count=1; $count <= $subject_count; $count++){
						echo "<option value=\"{$count}\"";
						if((int) $current_subject["position"] === $count){
							echo " selected";
						}
						echo ">{$count}</option>";
					}
					?>
				</select>
			</p>
			<p>Visible:
				<input type="radio" name="visible" value="0" <?php if($current_subject["visible"] == 0) echo " checked"; ?>/> No
				&nbsp;
				<input type="radio" name="visible" value="1" <?php if($current_subject["visible"] == 1) echo " checked"; ?>/> Yes
			</p>
			<input type="submit" name="submit" value="Edit Subject" />
		</form>
		<br>
		<a href="manage_content.php">Cancel</a>
		&nbsp;
		<a href="delete_subject.php?subject=<?php echo urlencode($current_subject["id"]); ?>" onclick="return confirm('Are you sure?');">Delete Current Subject</a>
	</div>
	
</div>

<?php
	// Include footer layout
	include("../includes/layouts/footer.php");
?>