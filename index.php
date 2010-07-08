<?php
/*
Plugin Name: Simple History
Plugin URI: http://eskapism.se/code-playground/simple-history/
Description: Nice little change log
Version: 0.2
Author: Pär Thernström
Author URI: http://eskapism.se/
License: GPL2
*/

/*  Copyright 2010  Pär Thernström (email: par.thernstrom@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define( "SIMPLE_HISTORY_VERSION", "0.2");
define( "SIMPLE_HISTORY_NAME", "Simple History"); 
define( "SIMPLE_HISTORY_URL", WP_PLUGIN_URL . '/simple-history/');

add_action( 'admin_head', "simple_history_admin_head" );
add_action( 'admin_init', 'simple_history_admin_init' ); // start listening to changes
add_action( 'init', 'simple_history_init' ); // start listening to changes
add_action( 'admin_menu', 'simple_history_admin_menu' );
add_action( 'wp_dashboard_setup', 'simple_history_wp_dashboard_setup' );
add_action( 'wp_ajax_simple_history_ajax', 'simple_history_ajax' );

function simple_history_ajax() {

	$type = $_POST["type"];
	if ($type == "All types") { $type = "";	}

	$user = $_POST["user"];
	if ($user == "By all users") { $user = "";	}

	$page = (int) $_POST["page"];

	$args = array(
		"is_ajax" => true,
		"filter_type" => $type,
		"filter_user" => $user,
		"page" => $page
	);
	simple_history_print_history($args);
	exit;

}

function simple_history_admin_menu() {

	#define( "SIMPLE_HISTORY_PAGE_FILE", menu_page_url("simple_history_page", false)); // no need yet

	// show as page?
	if (simple_history_setting_show_as_page()) {
		add_management_page(SIMPLE_HISTORY_NAME, SIMPLE_HISTORY_NAME, "read", "simple_history_page", "simple_history_management_page");
	}

}


function simple_history_wp_dashboard_setup() {
	if (simple_history_setting_show_on_dashboard()) {
		wp_add_dashboard_widget("simple_history_dashboard_widget", SIMPLE_HISTORY_NAME, "simple_history_dashboard");
	}
}

function simple_history_dashboard() {
	simple_history_print_nav();
	simple_history_print_history();
}

function simple_history_admin_head() {
	?>
	<script type="text/javascript">
		/**
		 *  load history items via ajax
		 */
		var simple_history_current_page = 0;
		jQuery(".simple-history-filter a").live("click", function() {
			$t = jQuery(this);
			$t.closest("ul").find("li").removeClass("selected");
			$t.closest("li").addClass("selected");
			$ol = jQuery("ol.simple-history");
			
			jQuery(".simple-history-added-by-ajax").remove();
			
			var $wrapper = jQuery("#simple-history-ol-wrapper");
			$wrapper.height($wrapper.height()); // so dashboard widget does not collapse when loading new items
		
			$ol.fadeOut("fast");

			simple_history_current_page = 0;
			var data = {
				"action": "simple_history_ajax",
				"type": jQuery(".simple-history-filter-type li.selected a").text(),
				"user": jQuery(".simple-history-filter-user li.selected a").text()
			};
			jQuery.post(ajaxurl, data, function(data, textStatus, XMLHttpRequest){
				$ol.html(data);
				$ol.fadeIn("fast");
				$wrapper.height("auto");
			});
			
			return false;
		});
		
		jQuery("#simple-history-load-more a").live("click", function() {
			simple_history_current_page++;

			jQuery("#simple-history-load-more,#simple-history-load-more-loading").toggle();
			
			$ol = jQuery("ol.simple-history:last");
			var data = {
				"action": "simple_history_ajax",
				"type": jQuery(".simple-history-filter-type li.selected a").text(),
				"user": jQuery(".simple-history-filter-user li.selected a").text(),
				"page": simple_history_current_page
			};
			jQuery.post(ajaxurl, data, function(data, textStatus, XMLHttpRequest){
				var $new_elm = jQuery("<ol class='simple-history simple-history-added-by-ajax'>" + data + "</ol>");
				$new_elm.hide();
				$ol.after($new_elm);
				$new_elm.show("slow");
				jQuery("#simple-history-load-more,#simple-history-load-more-loading").toggle();
			});
			return false;
		});
	</script>
	<?php
}


/*
http://codex.wordpress.org/Plugin_API/Action_Reference
   
create_category 
    Runs when a new category is created. Action function arguments: category ID. 

delete_category 
    Runs just after a category is deleted from the database and its corresponding links/posts are updated to remove the category. Action function arguments: category ID. 

edit_category 
    Runs when a category is updated/edited, including when a post or blogroll link is added/deleted or its categories are updated (which causes the count for the category to update). Action function arguments: category ID. 
    
edit_comment 
    Runs after a comment is updated/edited in the database. Action function arguments: comment ID
    
delete_comment 
    Runs just before a comment is deleted. Action function arguments: comment ID. 

comment_closed 
    Runs when the post is marked as not allowing comments while trying to display comment entry form. Action function argument: post ID. 

comment_id_not_found 
    Runs when the post ID is not found while trying to display comments or comment entry form. Action function argument: post ID. 

comment_flood_trigger 
    Runs when a comment flood is detected, just before wp_die is called to stop the comment from being accepted. Action function arguments: time of previous comment, time of current comment. 

comment_on_draft 
    Runs when the post is a draft while trying to display a comment entry form or comments. Action function argument: post ID. 

comment_post 
    Runs just after a comment is saved in the database. Action function arguments: comment ID, approval status ("spam", or 0/1 for disapproved/approved). 

edit_comment 
    Runs after a comment is updated/edited in the database. Action function arguments: comment ID. 

delete_comment 
    Runs just before a comment is deleted. Action function arguments: comment ID. 

wp_set_comment_status 
    Runs when the status of a comment changes. Action function arguments: comment ID, status string indicating the new status ("delete", "approve", "spam", "hold"). 

add_link 
    Runs when a new blogroll link is first added to the database. Action function arguments: link ID. 

delete_link 
    Runs when a blogroll link is deleted. Action function arguments: link ID. 

edit_link 
    Runs when a blogroll link is edited. Action function arguments: link ID. 
    
switch_theme 
    Runs when the blog's theme is changed. Action function argument: name of the new theme. 
    

*/

function simple_history_init() {
	// users
	add_action("wp_login", "simple_history_wp_login");
	add_action("wp_logout", "simple_history_wp_logout");
	add_action("delete_user", "simple_history_delete_user");
	add_action("user_register", "simple_history_user_register");
	add_action("profile_update", "simple_history_profile_update");
}

function simple_history_admin_init() {

	// posts
	add_action("save_post", "simple_history_save_post");
	add_action("transition_post_status", "simple_history_transition_post_status", 10, 3);
	add_action("delete_post", "simple_history_delete_post");
	
	// attachments/media
	add_action("add_attachment", "simple_history_add_attachment");
	add_action("edit_attachment", "simple_history_edit_attachment");
	add_action("delete_attachment", "simple_history_delete_attachment");

	add_settings_section("simple_history_settings_general", SIMPLE_HISTORY_NAME, "simple_history_settings_page", "general");
	add_settings_field("simple_history_settings_field_1", "Show Simple History", "simple_history_settings_field", "general", "simple_history_settings_general");
	register_setting("general", "simple_history_show_on_dashboard");
	register_setting("general", "simple_history_show_as_page");

	wp_enqueue_style( "simple_history_styles", SIMPLE_HISTORY_URL . "styles.css", false, SIMPLE_HISTORY_VERSION );	

}
function simple_history_settings_page() {
	// leave empty. must exist.
}

function simple_history_setting_show_on_dashboard() {
	return (bool) get_option("simple_history_show_on_dashboard", 1);
}
function simple_history_setting_show_as_page() {
	return (bool) get_option("simple_history_show_as_page", 1);
}


function simple_history_settings_field() {
	$show_on_dashboard = simple_history_setting_show_on_dashboard();
	$show_as_page = simple_history_setting_show_as_page();
	?>
	<input <?php echo $show_on_dashboard ? "checked='checked'" : "" ?> type="checkbox" value="1" name="simple_history_show_on_dashboard" id="simple_history_show_on_dashboard" />
	<label for="simple_history_show_on_dashboard">on the dashboard</label>

	<br />
	
	<input <?php echo $show_as_page ? "checked='checked'" : "" ?> type="checkbox" value="1" name="simple_history_show_as_page" id="simple_history_show_as_page" />
	<label for="simple_history_show_as_page">as a page under the tools menu</label>
	<?php
}


function simple_history_add_attachment($attachment_id) {
	simple_history_add("add", "attachment", "", $attachment_id);
}
function simple_history_edit_attachment($attachment_id) {
	// is this only being called if the title of the attachment is changed?!
	simple_history_add("update", "attachment", "", $attachment_id);
}
function simple_history_delete_attachment($attachment_id) {
	simple_history_add("delete", "attachment", "", $attachment_id);
}

// user is updated
function simple_history_profile_update($user_id) {
	simple_history_add("update", "user", "", $user_id);
}

// user is created
function simple_history_user_register($user_id) {
	simple_history_add("create", "user", "", $user_id);
}

// user is deleted
function simple_history_delete_user($user_id) {
	simple_history_add("delete", "user", "", $user_id);
}

// user logs in
function simple_history_wp_login($user) {
	$current_user = wp_get_current_user();
	$user = get_user_by("login", $user);
	// if user id = null then it's because we are logged out and then no one is acutally loggin in.. like a.. ghost-user!
	if ($current_user->ID == 0) {
		$user_id = $user->ID;
	} else {
		$user_id = $current_user->ID;
	}
	simple_history_add("login", "user", "", $user->ID, $user_id);
}
// user logs out
function simple_history_wp_logout() {
	$current_user = wp_get_current_user();
	simple_history_add("logout", "user", "", $current_user->ID);
}

function simple_history_delete_post($post_id) {
	if (wp_is_post_revision($post_id) == false) {
		$post = get_post($post_id);
		if ($post->post_status != "auto-draft" && $post->post_status != "inherit") {
			simple_history_add("delete", "post", $post->post_type, $post_id);
		}
	}
}

function simple_history_save_post($post_id) {

	if (wp_is_post_revision($post_id) == false) {
		// not a revision
		// it should also not be of type auto draft
		$post = get_post($post_id);
		if ($post->post_status != "auto-draft") {
			// bonny_d($post);
			#echo "save";
			// [post_title] => neu
			// [post_type] => page
		}
		
	}
}

// post has changed status
function simple_history_transition_post_status($new_status, $old_status, $post) {

	#echo "<br>From $old_status to $new_status";

	// From new to auto-draft <- ignore
	// From new to inherit <- ignore
	// From auto-draft to draft <- page/post created
	// From draft to draft
	// From draft to pending
	// From pending to publish
	# From pending to trash
	// if not from & to = same, then user has changed something
	//bonny_d($post); // regular post object
	if ($old_status == "auto-draft" && ($new_status != "auto-draft" && $new_status != "inherit")) {
		// page created
		$action = "create";
	} elseif ($new_status == "auto-draft" || ($old_status == "new" && $new_status == "inherit")) {
		// page...eh.. just leave it.
		return;
	} elseif ($new_status == "trash") {
		$action = "delete";
	} else {
		// page updated. i guess.
		$action = "update";
	}
	$object_type = "post";
	$object_subtype = $post->post_type;
	if ($object_subtype == "revision") {
		// don't log revisions
		return;
	}
	
	if (wp_is_post_revision($post->ID) === false) {
		// ok, no revision
		$object_id = $post->ID;
	} else {
		return; 
	}
	
	simple_history_add($action, $object_type, $object_subtype, $object_id);
}

function simple_history_add($action, $object_type, $object_subtype, $object_id, $user_id = null) {
	global $wpdb;
	$tableprefix = $wpdb->prefix;
	if ($user_id) {
		$current_user_id = $user_id;
	} else {
		$current_user = wp_get_current_user();
		$current_user_id = (int) $current_user->ID;
	}
	$sql = "INSERT INTO {$tableprefix}simple_history SET date = now(), action = '$action', object_type = '$object_type', object_subtype = '$object_subtype', user_id = '$current_user_id', object_id = '$object_id'";
	$wpdb->query($sql);
}


// Returns an English representation of a past date within the last month
// Graciously stolen from http://ejohn.org/files/pretty.js
// ..and simple_history stole it even more graciously from simple-php-framework http://github.com/tylerhall/simple-php-framework/
function simple_history_time2str($ts) {
    #if(!ctype_digit($ts))
    #   $ts = strtotime($ts);

    $diff = time() - $ts;
    if($diff == 0)
        return 'now';
    elseif($diff > 0)
    {
        $day_diff = floor($diff / 86400);
        if($day_diff == 0)
        {
            if($diff < 60) return 'just now';
            if($diff < 120) return '1 minute ago';
            if($diff < 3600) return floor($diff / 60) . ' minutes ago';
            if($diff < 7200) return '1 hour ago';
            if($diff < 86400) return floor($diff / 3600) . ' hours ago';
        }
        if($day_diff == 1) return 'Yesterday';
        if($day_diff < 7) return $day_diff . ' days ago';
        if($day_diff < 31) return ceil($day_diff / 7) . ' weeks ago';
        if($day_diff < 60) return 'last month';
        return date('F Y', $ts);
    }
    else
    {
        $diff = abs($diff);
        $day_diff = floor($diff / 86400);
        if($day_diff == 0)
        {
            if($diff < 120) return 'in a minute';
            if($diff < 3600) return 'in ' . floor($diff / 60) . ' minutes';
            if($diff < 7200) return 'in an hour';
            if($diff < 86400) return 'in ' . floor($diff / 3600) . ' hours';
        }
        if($day_diff == 1) return 'Tomorrow';
        if($day_diff < 4) return date('l', $ts);
        if($day_diff < 7 + (7 - date('w'))) return 'next week';
        if(ceil($day_diff / 7) < 4) return 'in ' . ceil($day_diff / 7) . ' weeks';
        if(date('n', $ts) == date('n') + 1) return 'next month';
        #return date('F Y', $ts);
        return $ts; // return back and let us do something else with it
    }
}

function simple_history_purge_db() {
	global $wpdb;
	$tableprefix = $wpdb->prefix;
	$sql = "DELETE FROM {$tableprefix}simple_history WHERE DATE_ADD(date, INTERVAL 60 DAY) < now()";
	$wpdb->query($sql);
}

function simple_history_management_page() {

	simple_history_purge_db();
	?>
	
	<div class="wrap">
	
		<h2><?php echo SIMPLE_HISTORY_NAME ?></h2>
		
		<?php	
		simple_history_print_nav();
		simple_history_print_history();
		?>
	
	</div>
	
	<?php
}

if (!function_exists("bonny_d")) {
	function bonny_d($var) {
		echo "<pre>";
		print_r($var);
		echo "</pre>";
	}
}


// when activating plugin: create tables
register_activation_hook(__FILE__,'simple_history_install');
function simple_history_install () {
   global $wpdb;

   $table_name = $wpdb->prefix . "simple_history";
   if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
      
   $sql = "CREATE TABLE " . $table_name . " (
	  id int(10) NOT NULL AUTO_INCREMENT,
	  date datetime NOT NULL,
	  action varchar(255) NOT NULL,
	  object_type varchar(255) NOT NULL,
	  object_subtype VARCHAR(255) NOT NULL,
	  user_id int(10) NOT NULL,
	  object_id int(10) NOT NULL,
	  PRIMARY KEY (id)
	);";
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
	
	}
}

function simple_history_print_nav() {

	global $wpdb;
	$tableprefix = $wpdb->prefix;
	
	// fetch all types that are in the log
	$simple_history_type_to_show = $_GET["simple_history_type_to_show"];
	$sql = "SELECT DISTINCT object_type, object_subtype FROM {$tableprefix}simple_history ORDER BY object_type, object_subtype";
	$arr_types = $wpdb->get_results($sql);
	#echo "<p>View:</p>";
	$str_types = "";
	$str_types .= "<ul class='simple-history-filter simple-history-filter-type'>";
	$css = "";
	if (empty($simple_history_type_to_show)) {
		$css = "class='selected'";
	}

	// add_query_arg(
	$link = esc_html(add_query_arg("simple_history_type_to_show", ""));
	#echo "<li>Filter by type: </li>";
	$str_types .= "<li $css><a href='$link'>All types</a> | </li>";
	foreach ($arr_types as $one_type) {
		$css = "";
		if ($one_type->object_subtype && $simple_history_type_to_show == ($one_type->object_type."/".$one_type->object_subtype)) {
			$css = "class='selected'";
		} elseif (!$one_type->object_subtype && $simple_history_type_to_show == $one_type->object_type) {
			$css = "class='selected'";
		}
		$str_types .= "<li $css>";
		$arg = "";
		if ($one_type->object_subtype) {
			$arg = $one_type->object_type."/".$one_type->object_subtype;
		} else {
			$arg = $one_type->object_type;
		}
		$link = esc_html(add_query_arg("simple_history_type_to_show", $arg));
		$str_types .= "<a href='$link'>";
		$str_types .= $one_type->object_type;
		if ($one_type->object_subtype) {
			$str_types .= "/".$one_type->object_subtype;
		}
		$str_types .= "</a> | ";
		$str_types .= "</li>";
	}
	$str_types .= "</ul>";
	$str_types = str_replace("| </li></ul>", "</li></ul>", $str_types);
	if (!empty($arr_types)) {
		echo $str_types;
	}

	// fetch all users that are in the log
	$sql = "SELECT DISTINCT user_id FROM {$tableprefix}simple_history WHERE user_id <> 0";
	$arr_users_regular = $wpdb->get_results($sql);
	foreach ($arr_users_regular as $one_user) {
		$arr_users[$one_user->user_id] = array("user_id" => $one_user->user_id);
	}
	if (!empty($arr_users)) {
		foreach ($arr_users as $user_id => $one_user) {
			$user = get_user_by("id", $user_id);
			$arr_users[$user_id]["user_login"] = $user->user_login;
			$arr_users[$user_id]["user_nicename"] = $user->user_nicename;
			$arr_users[$user_id]["first_name"] = $user->first_name;
			$arr_users[$user_id]["last_name"] = $user->last_name;
		}
	}

	if ($arr_users) {
		$simple_history_user_to_show = $_GET["simple_history_user_to_show"];
		$str_users = "";
		$str_users .= "<ul class='simple-history-filter simple-history-filter-user'>";
		$css = "";
		if (empty($simple_history_user_to_show)) {
			$css = " class='selected' ";
		}
		$link = esc_html(add_query_arg("simple_history_user_to_show", ""));
		#echo "<li>Filter by user: </li>";
		$str_users .= "<li $css><a href='$link'>By all users</a> | </li>";
		foreach ($arr_users as $user_id => $user_info) {
			$link = esc_html(add_query_arg("simple_history_user_to_show", $user_id));
			$css = "";
			if ($user_id == $simple_history_user_to_show) {
				$css = " class='selected' ";
			}
			$str_users .= "<li $css>";
			$str_users .= "<a href='$link'>";
			$str_users .= $user_info["user_nicename"];
			$str_users .= "</a> | ";
			$str_users .= "</li>";
		}
		$str_users .= "</ul>";
		$str_users = str_replace("| </li></ul>", "</li></ul>", $str_users);
		echo $str_users;
	}


}



// output the log
// take filtrering into consideration
function simple_history_print_history($args = null) {
	
	global $wpdb;
	
	$defaults = array(
		"page" => 0,
		"items" => 5,
		"filter_type" => "",
		"filter_user" => "",
		"is_ajax" => false
	);
	$args = wp_parse_args( $args, $defaults );
	#bonny_d($args);
	$simple_history_type_to_show = $args["filter_type"];
	$simple_history_user_to_show = $args["filter_user"];
	
	$where = " WHERE 1=1 ";
	if ($simple_history_type_to_show) {
		$filter_type = "";
		$filter_subtype = "";
		if (strpos($simple_history_type_to_show, "/") !== false) {
			// split it up
			$arr_args = explode("/", $simple_history_type_to_show);
			$filter_type = $arr_args[0];
			$filter_subtype = $arr_args[1];
		} else {
			$filter_type = $simple_history_type_to_show;
		}
		$where .= " AND object_type = '$filter_type' ";
		$where .= " AND object_subtype = '$filter_subtype' ";
	}
	if ($simple_history_user_to_show) {
		$userinfo = get_user_by("slug", $simple_history_user_to_show);
		$where .= " AND user_id = '" . $userinfo->ID . "'";
	}

	$tableprefix = $wpdb->prefix;
	$limit_page = $args["page"] * $args["items"];
	$sql_limit = " LIMIT $limit_page, $args[items]";
	$sql = "SELECT SQL_CALC_FOUND_ROWS *, UNIX_TIMESTAMP(date) as date_unix FROM {$tableprefix}simple_history $where ORDER BY date DESC $sql_limit";
	$rows = $wpdb->get_results($sql);
	$query_total_rows = $wpdb->get_var("SELECT FOUND_ROWS()");
	#echo "<p>Total $query_total_rows rows found</p>";
	if ($rows) {
		if (!$args["is_ajax"]) { echo "<div id='simple-history-ol-wrapper'><ol class='simple-history'>"; }
		foreach ($rows as $one_row) {
			/*
				stdClass Object
				(
				    [id] => 94
				    [date] => 2010-07-03 21:08:43
				    [action] => update
				    [object_type] => post
				    [object_subtype] => page
				    [user_id] => 1
				    [object_id] => 732
				    [date_unix] => 1278184123
				)				
			*/
			#bonny_d($one_row);
							
			$object_type = $one_row->object_type;
			$object_subtype = $one_row->object_subtype;
			$object_id = $one_row->object_id;
			$user_id = $one_row->user_id;
			$action = $one_row->action;

			$css = "";
			if ("attachment" == $object_type) {
				if (wp_get_attachment_image_src($object_id, array(50,50), true)) {
					// yep, it's an attachment and it has an icon/thumbnail
					$css = 'simple-history-has-attachment-thumnbail';
				}
			}
			if ("user" == $object_type) {
				$css = 'simple-history-has-attachment-thumnbail';
			}
			
			echo "<li class='$css'>";

			#echo $object_type;

			echo "<div class='first'>";
			
			// who performed the action
			$who = "";
			#if ($action == "login" && $user_id == 0) {
			#	$user = get_user_by("id", $object_id);
			#} else {
				$user = get_user_by("id", $user_id);
			#}
			
			$who .= "<span class='who'>";
			if ($user) {
				// http://localhost/wordpress3/wp-admin/user-edit.php?user_id=6
				$user_link = "user-edit.php?user_id={$user->ID}";
				$who .= "<a href='$user_link'>";
				$who .= $user->user_nicename;
				$who .= "</a>";
				if ($user->first_name || $user->last_name) {
					$who .= " (";
					if ($user->first_name && $user->last_name) {
						$who .= $user->first_name . " " . $user->last_name;
					} else {
						$who .= $user->first_name . $user->last_name; // just one of them, no space necessary
					}
					$who .= ")";
				}
			} else {
				$who .= "&lt;Unknown or deleted user&gt;";
			}
			$who .= "</span>";
			
			// what and object
			if ("post" == $object_type) {
				
				$post_out = "";
				$post_out .= $object_subtype;
				$post = get_post($object_id);
				if (null == $post) {
					// post does not exist, probably deleted
					$post_out .= " &lt;unknown name&gt;";
				} else {
					$title = get_the_title($object_id);
					$edit_link = get_edit_post_link($object_id, 'display');
					$post_out .= " <a href='$edit_link'>";
					$post_out .= "<span class='simple-history-title'>{$title}</span>";
					$post_out .= "</a>";
				}
				if ("create" == $action) {
					$post_out .= " created ";
				} elseif ("update" == $action) {
					$post_out .= " updated ";
				} elseif ("delete" == $action) {
					$post_out .= " deleted";
				}
				
				$post_out = ucfirst($post_out);
				echo $post_out;

				
			} elseif ("attachment" == $object_type) {
			
				$attachment_out = "";
				$attachment_out .= "attachment ";
				$title = get_the_title($object_id);
				if (is_null($title)) {
					$attachment_out .= " &lt;deleted&gt;";
				} else {

					$edit_link = get_edit_post_link($object_id, 'display');
					$attachment_image_src = wp_get_attachment_image_src($object_id, array(50,50), true);
					$attachment_image = "";
					if ($attachment_image_src) {
						$attachment_image = "<a class='simple-history-attachment-thumbnail' href='$edit_link'><img src='{$attachment_image_src[0]}' alt='Attachment icon' width='{$attachment_image_src[1]}' height='{$attachment_image_src[2]}' /></a>";
					}
					$attachment_out .= $attachment_image;
					$attachment_out .= " <a href='$edit_link'>";
					$attachment_out .= "<span class='simple-history-title'>{$title}</span>";
					$attachment_out .= "</a>";
					
					#echo " (".get_post_mime_type($object_id).")";
				}

				if ("add" == $action) {
					$attachment_out .= " added ";
				} elseif ("update" == $action) {
					$attachment_out .= " updated ";
				} elseif ("delete" == $action) {
					$attachment_out .= " deleted ";
				}
				
				$attachment_out = ucfirst($attachment_out);
				echo $attachment_out;
				#echo " <span class='simple-history-discrete'>(".get_post_mime_type($object_id).")</span>";


			} elseif ("user" == $object_type) {
				$user_out = "";
				$user_out .= "user";
				#if ($action == "logout" && $object_id == 0) {
				#	$user = get_user_by("id", $user_id);
				#} else {
					$user = get_user_by("id", $object_id);
				#}
				$user_link = "user-edit.php?user_id={$user->ID}";
				$user_out .= " <a href='$user_link'>";
				$user_out .= $user->user_nicename;
				$user_out .= "</a>";
				if ($user->first_name || $user->last_name) {
					$user_out .= " (";
					if ($user->first_name && $user->last_name) {
						$user_out .= $user->first_name . " " . $user->last_name;
					} else {
						$user_out .= $user->first_name . $user->last_name; // just one of them, no space necessary
					}
					$user_out .= ")";
				}

				$user_avatar = get_avatar($user->user_email, "50"); 
				$user_out .= "<a class='simple-history-attachment-thumbnail' href='$user_link'>$user_avatar</a>";

				if ("create" == $action) {
					$user_out .=  " added ";
				} elseif ("update" == $action) {
					$user_out .= " updated ";
				} elseif ("delete" == $action) {
					$user_out .= " deleted ";
				} elseif ("login" == $action) {
					$user_out .= " logged in";
				} elseif ("logout" == $action) {
					$user_out .= " logged out";
				}
				
				$user_out = ucfirst($user_out);
				echo $user_out;
			}
			echo "</div>";
			
			echo "<div class='second'>";
			// when
			$date_i18n_date = date_i18n(get_option('date_format'), strtotime($one_row->date), $gmt=false);
			$date_i18n_time = date_i18n(get_option('time_format'), strtotime($one_row->date), $gmt=false);		
			echo "By $who, ";
			echo "<span class='when' title='$date_i18n_date at $date_i18n_time'>".simple_history_time2str($one_row->date_unix)."</span>";

			echo "</div>";

			echo "</li>";
		}
		
		if (!$args["is_ajax"]) {
			echo "</ol>
			</div>
			<p id='simple-history-load-more'><a href='#'>Show $args[items] more</a></p>
			<p class='hidden' id='simple-history-load-more-loading'>Loading...</p>
			";
		}
	} else {
		echo "<p>No history items found.</p>";
		if (!$args["is_ajax"]) {
			echo "<p>Please note that Simple History only records things that happen after this plugin have been installed.</p>";
		}
		
	}
}

?>