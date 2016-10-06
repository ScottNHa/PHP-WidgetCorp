<?php

	// Redirect the browser to a new location
	function redirect_to($new_location){
		header("Location: " . $new_location);
		exit;
	}

	// Basic function to test a query result
	function confirm_query($result_set){
		if(!$result_set){
			die("Database query failed.");
		}
	}
	
	// Query for finding all subjects in the database
	function find_all_subjects($public=true){
		global $connection;
		
		$query  = "SELECT * FROM subjects ";
		if($public){
			$query .= "WHERE visible = 1 ";
		}
		$query .= "ORDER BY position ASC";
		$subject_set = mysqli_query($connection, $query);
		confirm_query($subject_set);
		
		return $subject_set;
	}
	
	// Query for finding all pages of a given subject in the database
	function find_pages_for_subject($subject_id, $public=true){
		global $connection;
		$safe_subject_id = mysqli_real_escape_string($connection, $subject_id);
		
		$query = "SELECT * FROM pages WHERE subject_id = {$safe_subject_id}  ";
		if($public){
			$query .= "AND visible = 1 ";
		}
		$query .= "ORDER BY position ASC";
		$page_set = mysqli_query($connection, $query);
		confirm_query($page_set);
		
		return $page_set;
	}
	
	// Query for finding information about a subject from a subject ID
	function find_subject_by_id($subject_id, $public=true){
		global $connection;
		$safe_subject_id = mysqli_real_escape_string($connection, $subject_id);
		
		$query  = "SELECT * FROM subjects WHERE id = {$safe_subject_id} ";
		if($public){
			$query .= "AND visible = 1 ";
		}
		$query .= "LIMIT 1";
		$subject_set = mysqli_query($connection, $query);
		confirm_query($subject_set);
		if ($subject = mysqli_fetch_assoc($subject_set)){
			return $subject;
		} else {
			return null;
		}
	}
	
	// Query for finding information about a page from a page ID
	function find_page_by_id($page_id, $public=true){
		global $connection;
		$safe_page_id = mysqli_real_escape_string($connection, $page_id);
		
		$query = "SELECT * FROM pages WHERE id = {$safe_page_id} ";
		if($public){
			$query .= "AND visible = 1 ";
		}
		$query .= "LIMIT 1";
		$page_set = mysqli_query($connection, $query);
		confirm_query($page_set);
		
		if($page = mysqli_fetch_assoc($page_set)){
			return $page;
		} else {
			return null;
		}
	}
	
	// Defaults a page when a subject is selected (for public)
	function find_default_page_for_subject($subject_id){
		$page_set = find_pages_for_subject($subject_id);
		if($first_page = mysqli_fetch_assoc($page_set)){
			return $first_page;
		} else {
			return null;
		}
	}
	
	// Set current subject/page if there is one
	function find_selected($public=false){
		global $current_subject;
		global $current_page;
		
		if(isset($_GET["subject"])){
			$current_subject = find_subject_by_id($_GET["subject"], $public);
			if($public){
				$current_page = find_default_page_for_subject($current_subject["id"]);
				
			} else {
				$current_page = null;
			}
		} elseif(isset($_GET["page"])){
			$current_subject = null;
			$current_page = find_page_by_id($_GET["page"], $public);
		} else {
			$current_subject = null;
			$current_page = null;
		}
	}
	
	// Query for finding all admins
	function find_all_admins(){
		global $connection;
		
		$query = "SELECT * FROM admins ORDER BY username ASC";
		$admin_set = mysqli_query($connection, $query);
		confirm_query($admin_set);
		
		return $admin_set;
	}
	
	// Qery for finding an admin by id
	function find_admin_by_id($admin_id){
		global $connection;
		$safe_admin_id = mysqli_real_escape_string($connection, $admin_id);
		
		$query = "SELECT * FROM admins WHERE id = {$safe_admin_id} LIMIT 1";
		$admin_set = mysqli_query($connection, $query);
		confirm_query($admin_set);
		
		if($admin = mysqli_fetch_assoc($admin_set)){
			return $admin;
		} else {
			return null;
		}
	}
	
	function find_admin_by_username($username){
		global $connection;
		$safe_username = mysqli_real_escape_string($connection, $username);
		
		$query = "SELECT * FROM admins WHERE username = '{$safe_username}' LIMIT 1";
		$username_set = mysqli_query($connection, $query);
		confirm_query($username_set);
		
		if($admin = mysqli_fetch_assoc($username_set)){
			return $admin;
		} else {
			return null;
		}
	}
	
	// Navigation function
	function navigation($subject_array, $page_array){
		$output  = "<br><a href=\"admin.php\">&laquo; Main Menu</a><br>";
		$output .= "<ul class=\"subjects\">";
		
		// 2. Perform database query for subjects
		$subject_set = find_all_subjects(false);
		
		// Loop through the query of subjects
		while($subject = mysqli_fetch_assoc($subject_set)){ 
			$output .= "<li";
			// If the subject is the currently selected subject
			if($subject_array && $subject["id"] === $subject_array["id"]){
				$output .= " class=\"selected\"";
			}
			$output .= ">";
			
			// Set up an HTML link to that subject ID
			$output .= "<a href=\"manage_content.php?subject=";
			$output .= urlencode($subject["id"]);
			$output .= "\">";
			$output .= htmlentities($subject["menu_name"]); 
			$output .= "</a>";
			
			$output .= "<ul class=\"pages\">";
			
			// 2. Perform database query for pages
			$page_set = find_pages_for_subject($subject["id"], false);
		
			// Loop through the query of pages
			while($page = mysqli_fetch_assoc($page_set)){
				$output .= "<li";
				// If the page is the currently selected page
				if($page_array && $page["id"] === $page_array["id"]){
					$output .= " class=\"selected\"";
				}
				$output .= ">";

				// Set up an HTML link to that page ID
				$output .= "<a href=\"manage_content.php?page=";
				$output .= urlencode($page["id"]);
				$output .= "\">";
				$output .= htmlentities($page["menu_name"]);
				$output .= "</a></li>";
			} 
		
			// Release the page query
			mysqli_free_result($page_set);
			$output .= "</ul></li>";

		}
		
		// Release the subject query
		mysqli_free_result($subject_set); 
		$output .= "</ul>";
		
		return $output;
	}
	
	function public_navigation($subject_array, $page_array){
		$output = "<ul class=\"subjects\">";
		
		// 2. Perform database query for subjects
		$subject_set = find_all_subjects();
		
		// Loop through the query of subjects
		while($subject = mysqli_fetch_assoc($subject_set)){ 
			$output .= "<li";
			// If the subject is the currently selected subject
			if($subject_array && $subject["id"] === $subject_array["id"]){
				$output .= " class=\"selected\"";
			}
			$output .= ">";
			
			// Set up an HTML link to that subject ID
			$output .= "<a href=\"index.php?subject=";
			$output .= urlencode($subject["id"]);
			$output .= "\">";
			$output .= htmlentities($subject["menu_name"]); 
			$output .= "</a>";
			
			if($subject_array && $subject["id"] === $subject_array["id"] || $page_array["subject_id"] === $subject["id"]){
				$output .= "<ul class=\"pages\">";
				// 2. Perform database query for pages
				$page_set = find_pages_for_subject($subject["id"]);
		
				// Loop through the query of pages
				while($page = mysqli_fetch_assoc($page_set)){
					$output .= "<li";
					// If the page is the currently selected page
					if($page_array && $page["id"] === $page_array["id"]){
						$output .= " class=\"selected\"";
					}
					$output .= ">";

					// Set up an HTML link to that page ID
					$output .= "<a href=\"index.php?page=";
					$output .= urlencode($page["id"]);
					$output .= "\">";
					$output .= htmlentities($page["menu_name"]);
					$output .= "</a></li>";
				}
				
				// Release the page query
				mysqli_free_result($page_set);
				$output .= "</ul>";
			}
			$output .= "</li>";
		}
		
		// Release the subject query
		mysqli_free_result($subject_set); 
		$output .= "</ul>";
		
		return $output;
	}
	
	// Output form errors with $errors array
	function form_errors($errors=array()){
		$output = "";
		if(!empty($errors)){
			$output .= "<div class=\"error\">";
			$output .= "Please fix the following error(s):";
			$output .= "<ul>";
			
			foreach ($errors as $key => $error){
				$output .= "<li>";
				$output .= htmlentities($error);
				$output .= "</li>";
			}
			
			$output .= "</ul>";
			$output .= "</div>";
		}
		
		return $output;
	}
	
	// Encrpyt our password
	function password_encrypt($password){
		return password_hash($password, PASSWORD_BCRYPT);
	}
	
	// Attempt a login with the given credentials
	function attempt_login($username, $password){
		$admin = find_admin_by_username($username);
		
		if($admin){
			if(password_verify($password, $admin["hashed_password"])){
				return $admin;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	function logged_in(){
		return isset($_SESSION['admin_id']);
	}
	
	function confirm_logged_in(){
		if (!logged_in()){
			redirect_to("login.php");
		}
	}
	
?>