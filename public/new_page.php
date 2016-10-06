<?php 
	// Setup session
	require_once("../includes/session.php");

	// Open database connection
	require_once("../includes/db_connection.php");
		
	// Include functions	
	require_once("../includes/functions.php");
	require_once("../includes/validation_functions.php");
	confirm_logged_in();
	
	// Set current subject/page if there is one
	find_selected();
	if(!$current_subject){
		redirect_to("manage_content.php");
	}

	// If a valid submission came through
	if (isset($_POST['submit'])){
		$subject_id = $current_subject["id"];
		$menu_name = mysqli_real_escape_string($connection, $_POST['menu_name']);
		$position = (int) $_POST['position'];
		$visible = (int) $_POST['visible'];
		$content = mysqli_real_escape_string($connection, $_POST['content']);
	
		// Validations
		$required_fields = array("menu_name", "position", "visible", "content");
		validate_presences($required_fields);
		$fields_with_max_lengths = array("menu_name" => 30);
		validate_max_lengths($fields_with_max_lengths);
	
		// If there are errors
		if(!empty($errors)){
			$_SESSION["errors"] = $errors;
		} else {
			$query = "INSERT INTO pages (subject_id, menu_name, position, visible, content) VALUES ({$subject_id}, '{$menu_name}', {$position}, {$visible}, '{$content}')";
			$result = mysqli_query($connection, $query);
	
			if($result && mysqli_affected_rows($connection) === 1){
				$_SESSION['message'] = "Page created.";
				$url = "manage_content.php?subject=" . urlencode($current_subject["id"]);
				redirect_to($url);
			} else {
				$_SESSION['message'] = "Page creation failed.";
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
		<h2>Add Page: <?php echo htmlentities($current_page["menu_name"]); ?></h2>
		
		<form action="new_page.php?subject=<?php echo urlencode($_GET["subject"]); ?>" method="post">
			<p>Menu name:
				<input type="text" name="menu_name" value="" />
			</p>
			<p>Position:
				<select name="position">
					<?php
					$page_set = find_pages_for_subject($current_subject["id"]);
					$page_count = mysqli_num_rows($page_set);
					
					for($count=1; $count <= $page_count + 1; $count++){
						if($count > $page_count){
							echo "<option value=\"{$count}\" selected>{$count}</option>";

						} else {
							echo "<option value=\"{$count}\">{$count}</option>";
						}
					}
					?>
				</select>
			</p>
			<p>Visible:
				<input type="radio" name="visible" value="0" checked/> No
				&nbsp;
				<input type="radio" name="visible" value="1" /> Yes
			</p>
			<p>Content:
				<br>
				<textarea rows="20" cols="40" name="content"></textarea>
			</p>
			<input type="submit" name="submit" value="Add Page" />
		</form>
		<br>
		<a href="manage_content.php?subject=<?php echo urlencode($current_subject["id"]); ?>">Cancel</a>
		&nbsp;
		<!-- <a href="delete_subject.php?subject=<?php //echo urlencode($current_page["id"]); ?>" onclick="return confirm('Are you sure?');">Delete Current Page</a> -->
	</div>
	
</div>

<?php
	// Include footer layout
	include("../includes/layouts/footer.php");
?>