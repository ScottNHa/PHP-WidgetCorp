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
?>

<div id="main">
	
	<div id="navigation">
		<?php
		echo "<br><a href=\"admin.php\">&laquo; Main Menu</a><br>";
		echo "<ul class=\"subjects\">";
		?>
	</div>
	
	<div id="page">
		<?php echo session_message(); ?>
		<h2>Manage Admins</h2>
		<table>
			<tr>
				<th style="text-align: left; width: 200px;">Username</th>
				<th colspan="2" style="text-align: left;">Actions</th>
			</tr>
			
		<?php $admin_set = find_all_admins();				
		
		while($admin = mysqli_fetch_assoc($admin_set)){
			$output  = "<tr><td>";
			$output .= htmlentities($admin["username"]);
			$output .= "</td>";
			$output .= "<td><a href=\"edit_admin.php?id=";
			$output .= urlencode($admin["id"]);
			$output .= "\">";
			$output .= "Edit";
			$output .= "</a></td></tr>";
			echo $output;
		}
		?>
	</table>
		<br>
		<a href="new_admin.php">Add New Admin</a>	
	</div>
	
</div>

<?php
	// Include footer layout
	include("../includes/layouts/footer.php");
?>