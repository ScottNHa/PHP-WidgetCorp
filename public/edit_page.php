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
	
	if(!$current_page){
		redirect_to("manage_content.php");
	} else {
		require_once("../includes/validation_functions.php");
	
		// If a valid submission came through
		if (isset($_POST['submit'])){
			$id = $current_page["id"];
			$menu_name = mysqli_real_escape_string($connection, $_POST['menu_name']);
			$position = (int) $_POST['position'];
			$visible = (int) $_POST['visible'];
			$content = mysqli_real_escape_string($connection, $_POST['content']);
		
			// Validations
			$required_fields = array("menu_name", "position", "visible", "content");
			validate_presences($required_fields);
			$fields_with_max_lengths = array("menu_name" => 30);
			validate_max_lengths($fields_with_max_lengths);
		
			if(!empty($errors)){
				$_SESSION["errors"] = $errors;
			} else {
				$query = "UPDATE pages SET menu_name = '{$menu_name}', position = {$position}, visible = {$visible}, content = '{$content}' WHERE id = {$id} LIMIT 1";
				$result = mysqli_query($connection, $query);
		
				if($result && mysqli_affected_rows($connection) === 1){
					$_SESSION['message'] = "Page updated.";
					$url = "manage_content.php?page=" . urlencode($current_page["id"]);
					redirect_to($url);
				} elseif($result && mysqli_affected_rows($connection) === 0){
					$_SESSION['message'] = "Nothing was changed.";
				} else {
					$_SESSION['message'] = "Page update failed.";
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
		<h2>Edit Page: <?php echo htmlentities($current_page["menu_name"]); ?></h2>
		
		<form action="edit_page.php?page=<?php echo urlencode($current_page["id"]); ?>" method="post">
			<p>Menu name:
				<input type="text" name="menu_name" value="<?php echo htmlentities($current_page["menu_name"]); ?>" />
			</p>
			<p>Position:
				<select name="position">
					<?php
					$page_set = find_pages_for_subject($current_page["subject_id"]);
					$page_count = mysqli_num_rows($page_set);
					
					for($count=1; $count <= $page_count; $count++){
						echo "<option value=\"{$count}\"";
						if((int) $current_page["position"] === $count){
							echo " selected";
						}
						echo ">{$count}</option>";
					}
					?>
				</select>
			</p>
			<p>Visible:
				<input type="radio" name="visible" value="0" <?php if($current_page["visible"] == 0) echo " checked"; ?>/> No
				&nbsp;
				<input type="radio" name="visible" value="1" <?php if($current_page["visible"] == 1) echo " checked"; ?>/> Yes
			</p>
			<p>Content:
				<br>
				<textarea rows="3" cols="20" name="content"><?php echo htmlentities($current_page["content"]); ?></textarea>
			</p>
			<input type="submit" name="submit" value="Edit Page" />
		</form>
		<br>
		<a href="manage_content.php?page=<?php echo urlencode($current_page["id"]) ?>">Cancel</a>
		&nbsp;
		<a href="delete_page.php?page=<?php echo urlencode($current_page["id"]); ?>" onclick="return confirm('Are you sure?');">Delete Current Page</a>
	</div>
	
</div>

<?php
	// Include footer layout
	include("../includes/layouts/footer.php");
?>