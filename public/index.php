<?php
	// Open database connection
	require_once("../includes/db_connection.php");
		
	// Include functions	
	require_once("../includes/functions.php");
	
	// Include header layout
	include("../includes/layouts/header.php");
	
	// Set current subject/page if there is one
	find_selected(true);
?>

<div id="main">
	<div id="navigation">
		<br>
		<?php echo public_navigation($current_subject, $current_page); ?>
		<br>
	</div>
	
	<div id="page">
		<?php if ($current_page){ ?>
		<!-- MANAGE PAGE -->
		<h2><?php echo htmlentities($current_page["menu_name"]) . "<br>"; ?></h2>
		<div class="view-content">
			<?php echo nl2br(htmlentities($current_page["content"])); ?>
		</div>
		<?php } else { ?>
		<h2>Welcome!</h2>
		
		<?php echo "Please select a subject or page";
	}
?>
	</div>
	
</div>

<?php
	// Include footer layout
	include("../includes/layouts/footer.php");
?>