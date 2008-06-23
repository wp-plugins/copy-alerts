<?php
/*
Plugin Name: Copy Alerts
Plugin URI: http://blog.bitscan.com/copyalerts-wordpress-plugin/
Description: Automatically adds a Copy Alert for a page or post. You'll be notified via email when your content is found on other web pages.
Version: 1.0.2
Author: Mark Nelson
Author URI: http://blog.bitscan.com/copyalerts-wordpress-plugin/
*/

/*  Copyright 2008 BitScan Inc. (http://bitscan.com/)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/

function wpca_show_form()
{
	echo '<p><a href="http://www.copyalerts.com/">';
	echo 'Content monitored by Copy Alerts&trade;</a></p>' . "\n";
}

function wpca_install()
{
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	// Initialise options with default values
	$blogname = get_option('blogname');
	add_option('wpca_widget_title', 'Copy Alerts');
	add_option('wpca_email_from', get_option('admin_email') );		
}

function wpca_options()
{
	// Get current options from database
	$email_from = stripslashes(get_option('wpca_email_from'));

	// Update options if user posted new information
	if( $_POST['wpca_hidden'] == 'ca0319' ) {
		// Read from form
		$email_from = stripslashes($_POST['wpca_email_from']);

		// Save to database
		update_option('wpca_email_from', $email_from );

		// Notify admin of change
		echo '<div id="message" class="updated fade"><p><strong>';
		_e('Options saved.', 'wpca_domain');
		echo '</strong></p></div>';
	}
?>

<div class="wrap">
<h2>Copy Alerts Settings</h2>
<form name="wpca_form" method="post" action="">
<input type="hidden" name="wpca_hidden" value="ca0319"><br />
<fieldset class="options">
<legend>Notification Email</legend>
<table width="100%" cellspacing="2" cellpadding="5" class="optiontable editform">
<tr valign="top">
<td><input type="text" name="wpca_email_from" id="wpca_email_from" value="<?php echo $email_from; ?>" size="40"></td>
</tr>
<tr><td>
<p class="submit">
<input type="submit" name="Submit" value="Update Email &raquo;" />
</p>
</td></tr>	
</table>
</fieldset>
</table>
</form>
</div>

<?php
}

function wpca_widget_init() {

	if (!function_exists('register_sidebar_widget')) {
		return;
	}

	function wpca_widget($args) {
		wpca_show_form();
	}

	function wpca_widget_control() {
		
		$title = get_option('wpca_widget_title');
		
		if ($_POST['wpca_submit']) {
			$title = stripslashes($_POST['wpca_widget_title']);
			update_option('wpca_widget_title', $title );
		}
		
		echo '<p>Title:<input  style="width: 200px;" type="text" value="';
		echo $title . '" name="wpca_widget_title" id="wpca_widget_title" /></p>';
		echo '<input type="hidden" id="wpca_submit" name="wpca_submit" value="1" />';
	}

	$width = 300;
	$height = 100;
	if (!function_exists( 'wp_register_sidebar_widget' )) {
		register_sidebar_widget('Copy Alerts', 'wpca_widget');
		register_widget_control('Copy Alerts', 'wpca_widget_control', $width, $height);
	} else {
		$size = array('width' => $width, 'height' => $height);
		$class = array( 'classname' => 'wpca_opt_in' ); // css classname
		wp_register_sidebar_widget('wpca', 'Copy Alerts', 'wpca_widget', $class);
		wp_register_widget_control('wpca', 'Copy Alerts', 'wpca_widget_control', $size);
	}
}

function wpca_add_to_menu() {
	add_options_page('Copy Alerts Settings', 'Copy Alerts', 7, __FILE__, 'wpca_options' );
}

function add_copyalert() {
?>

<div id="postcopyalerts" class="postbox closed">
<h3><?php _e('Copy Alerts', 'Copy Alerts') ?></h3>
<div class="inside">
<div>
<a style="font-size:12px;margin-top:10px;margin-bottom:10px;text-decoration:none;" href="http://www.copyalerts.com/api/wordpress/?email=<? print stripslashes(get_option('wpca_email_from')) ?>&uri=<? print get_permalink( $page_ID ) ?>" target="_new"><b>CREATE NEW COPY ALERT</a><br />
<b>Monitor this content and get notified via email of other copies found.</b>
</div>
</div>
</div>

<?php
}

register_activation_hook(__FILE__, 'wpca_install');
add_action('admin_menu', 'wpca_add_to_menu');
add_action('plugins_loaded', 'wpca_widget_init');
add_action('edit_form_advanced', 'add_copyalert');
add_action('edit_page_form', 'add_copyalert');

?>
