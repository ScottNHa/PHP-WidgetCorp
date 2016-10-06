<?php 
	// Setup session
	require_once("../includes/session.php");

	// Open database connection
	require_once("../includes/db_connection.php");
		
	// Include functions	
	require_once("../includes/functions.php");
	
	// Include header layout
	confirm_logged_in();
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
		<br>
		<a href="new_subject.php">+ Add a subject</a>
	</div>
	
	<div id="page">
		<?php
			echo session_message();
		
			if ($current_subject){ ?>
				<!-- MANAGE SUBJECT -->
				<br>
				<h2>Manage Subject</h2>
				
				<?php
				echo "Menu Name: " . htmlentities($current_subject["menu_name"]) . "<br>";
				echo "Position: " . $current_subject["position"] . "<br>";
				echo "Visible: ";
				echo $current_subject["visible"] == 1 ? "yes" : "no";
				echo "<br>";
				?>
				<br>
				<a href="edit_subject.php?subject=<?php echo urlencode($current_subject["id"]); ?>">Edit Subject</a>
				<br>
				<?php $subject_pages = find_pages_for_subject($current_subject["id"]);
				if(mysqli_num_rows($subject_pages) >= 1){
					echo "<div style=\"margin-top: 2em; border-top: 1px solid #000000;\">";
					echo "<h3>Pages in this subject:</h3>";
					echo "<ul>";
				
				
				while($page = mysqli_fetch_assoc($subject_pages)){
					$output  = "<li><a href=\"manage_content.php?page=";
					$output .= urlencode($page["id"]);
					$output .= "\">";
					$output .= htmlentities($page["menu_name"]);
					$output .= "</a></li><br>";
					echo $output;
				}
			}
				?>
				</ul>
				<br>
				<a href="new_page.php?subject=<?php echo urlencode($current_subject["id"]); ?>">+ Add New Page</a>
				
				
				<?php } elseif ($current_page){ ?>
					<!-- MANAGE PAGE -->
				<h1>
					<a href="manage_content.php?subject=<?php echo htmlentities($current_page["subject_id"]); ?>">&laquo;</a>
				</h1>
				<h2>Manage Page</h2> 
					
				<?php
				echo "Menu Name: " . htmlentities($current_page["menu_name"]) . "<br>";
				echo "Position: " . $current_page["position"] . "<br>";
				echo "Visible: ";
				echo $current_page["visible"] == 1 ? "yes" : "no";
				echo "<br>"; ?>
				Content:<br>
				<div class="view-content">
					<?php echo htmlentities($current_page["content"]); ?>
				</div>
				<br>
				<a href="edit_page.php?page=<?php echo urlencode($current_page["id"]); ?>">Edit Page</a>
				<?php } else { ?>
				<h2>Manage Content</h2>
				
				<?php
				echo "Please select a subject or page";
			}
		?>
	</div>
	
</div>

<?php
	// Include footer layout
	include("../includes/layouts/footer.php");
?>