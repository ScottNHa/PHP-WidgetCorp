<?php 
	// Setup session
	require_once("../includes/session.php");

	// Open database connection
	require_once("../includes/db_connection.php");
		
	// Include functions	
	require_once("../includes/functions.php");
	confirm_logged_in();
	
	// Include header layout
	$layout_context = "admin";
	include("../includes/layouts/header.php");
	
	// Set current subject/page if there is one
	find_selected();
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
		<h2>Create Subject</h2>
		
		<form action="create_subject.php" method="post">
			<p>Menu name:
				<input type="text" name="menu_name" value="" />
			</p>
			<p>Position:
				<select name="position">
					<?php
					$subject_set = find_all_subjects();
					$subject_count = mysqli_num_rows($subject_set);
					
					for($count=1; $count <= $subject_count + 1; $count++){
						if($count > $subject_count){
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
			<input type="submit" name="submit" value="Create Subject" />
		</form>
		<br>
		<a href="manage_content.php">Cancel</a>
	</div>
	
</div>

<?php
	// Include footer layout
	include("../includes/layouts/footer.php");
?>