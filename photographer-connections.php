<?php
/*
Plugin Name: Photographer Connections
Plugin URI: http://photographyblogsites.com/wordpress-plugins/photographer-connections
Description: Connect to photography sites: SmugMug, Pictage, ShootQ.
Author: Marty Thornley
Version: 1.0
Author URI: http://martythornley.com/
*/
/*  Copyright 2011  Partner Interactive, LLC.

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

	if (!defined ('BLOGSITE_CONNECT_PLUGIN_BASEDIR')) { define ('BLOGSITE_CONNECT_PLUGIN_BASEDIR', dirname(__FILE__)); };

	blogsite_connect_include_folder('library/functions');
	blogsite_connect_include_folder('modules');

	add_action ('admin_menu', 				'blogsite_connect_create_menu');
	add_action( 'admin_init', 				'blogsite_connect_register_settings' );
	add_action( 'admin_init', 				'blogsite_connect_update_settings' );
	
function blogsite_connect_create_menu() {
	
	// multisite Supre Admin Menu
	add_submenu_page('ms-admin.php', 'Photographer Connections', 'Photographer Connections', 10, 'blogsite_photo_connect_settings', 'blogsite_photo_connect_super_admin');
	
	// test version for single user...
	//add_menu_page('Photographer Connections', 'Photographer Connections', 'administrator', 'blogsite_photo_connect', 'blogsite_photo_connect_super_admin',plugins_url('/icon.jpg', __FILE__));
	
	add_menu_page('Photographer Connections', 'Photographer Connections', 'administrator', 'blogsite_photo_connect', 'blogsite_photo_connect_settings_page',plugins_url('library/images/icon.jpg', __FILE__));
	do_action('blogsite_connect_extra');
}

function blogsite_connect_register_settings() {
	register_setting( 'blogsite_connect_settings_group', 		'blogsite_connect_settings');
	register_setting( 'blogsite_connect_site_settings_group', 	'blogsite_connect_site_settings');
}


function blogsite_connect_include_folder($dir='functions') {

	$path = BLOGSITE_CONNECT_PLUGIN_BASEDIR . '/'.$dir;
	$includes = array();
	if (file_exists ($path)){ 
	if ($handle = opendir($path)) {
		$displayString = "";
		$count = 0; 	
		while (false !== ($file = readdir($handle))) {			if ($file != "." && $file != "..") {
				$include = $file;
				$file = substr($file, strrpos($file, '.') + 1);
				if ($file == "php" ) { 
					$includes[$count]=$include;
					$count++;
				}
			}	
		}
		if ($count = 0) {
			echo 'No pages created yet';
		}					
	}
	}
	foreach ($includes as $include) {
		require_once (BLOGSITE_CONNECT_PLUGIN_BASEDIR.'/'.$dir.'/'.$include); 
	}
};

function blogsite_photo_connect_settings_page(){
?>
<div class="wrap">
	<h2>Photographer Connections</h2>
	<form method="post" action="options.php">
    <?php settings_fields( 'blogsite_connect_settings_group' ); ?>
	<?php $blogsite_connect_settings = get_option('blogsite_connect_settings'); ?>
	<?php $blogsite_connect_site_settings = get_site_option('blogsite_connect_site_settings'); ?>

		<div class="pane">
			<h3>Photography Vendors</h3>
			<p>What services do you use?</p>
			<p>Enable to activate settings pages for each.</p>
			<table class="form-table">
				<?php $connect_settings = array(); ?>
				<?php $connect_settings = apply_filters('blogsite_connect_extra_settings',$connect_settings); ?>
					
				<?php foreach ($connect_settings as $connect_setting) { ?>
				
				<?php if ($blogsite_connect_site_settings[$connect_setting['setting']] == 'yes' || !is_multisite()) { ?>
				
				<tr valign="top">
					<th scope="row"><?php echo $connect_setting['name']; if ($blogsite_connect_settings[$connect_setting['setting']] == 'yes' ) { echo ': <a href="'.get_admin_url().'admin.php?page='.$connect_setting['page'].'">Settings</a>'; }; ?></th>
					<td>
						Enable: <input type="radio" name="blogsite_connect_settings[<?php echo $connect_setting['setting']; ?>]" value="yes" <?php if ($blogsite_connect_settings[$connect_setting['setting']] == 'yes' ) { echo 'checked="checked"'; }; ?>/>
						<br />
						Disable <input type="radio" name="blogsite_connect_settings[<?php echo $connect_setting['setting']; ?>]" value="no" <?php if ($blogsite_connect_settings[$connect_setting['setting']] != 'yes' ) { echo 'checked="checked"'; }; ?>/>
					</td>
				</tr>
									
				<?php } ?>
				<?php } ?>
												
			</table>
		</div>
		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
		</p>
	</form>
</div>
<?php
}

function blogsite_connect_update_settings() {
	if ( wp_verify_nonce( $_POST[ 'blogsite_connect_nonce' ], 'blogsite_connect_nonce' )) {
		if (isset ($_POST['blogsite_connect_site_settings'])) {
			foreach ($_POST['blogsite_connect_site_settings'] as $key => $value) {
				$blogsite_connect_site_settings[$key] = $value;  
			}
			update_site_option('blogsite_connect_site_settings', $blogsite_connect_site_settings);
		}
	}	
}


function blogsite_photo_connect_super_admin(){
?>
<div class="wrap">
	<h2>Photographer Connections Site-wide settings</h2>
	<form name="theform" method="post" enctype="multipart/form-data" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']);?>">
	<?php wp_nonce_field( 'blogsite_connect_nonce', 'blogsite_connect_nonce', false, true ); ?>
	
	<?php $blogsite_connect_site_settings = get_site_option('blogsite_connect_site_settings'); ?>
		<div class="pane">
			<h3>Photography Vendors</h3>
			<p>What services do you want to enable for each site?</p>
			<p>This will give each site the ability to enable these services. If disabled, individual sites will not have access to them.</p>
			<table class="form-table">
				<?php $connect_settings = array(); ?>
				<?php $connect_settings = apply_filters('blogsite_connect_extra_settings',$connect_settings); ?>
					
				<?php foreach ($connect_settings as $connect_setting) { ?>
				
				<tr valign="top">
					<th scope="row"><?php echo $connect_setting['name']; ?></th>
					<td>
						Enable: <input type="radio" name="blogsite_connect_site_settings[<?php echo $connect_setting['setting']; ?>]" value="yes" <?php if ($blogsite_connect_site_settings[$connect_setting['setting']] == 'yes' ) { echo 'checked="checked"'; }; ?>/>
						<br />
						Disable <input type="radio" name="blogsite_connect_site_settings[<?php echo $connect_setting['setting']; ?>]" value="no" <?php if ($blogsite_connect_site_settings[$connect_setting['setting']] != 'yes' ) { echo 'checked="checked"'; }; ?>/>
					</td>
				</tr>					
					
				<?php } ?>
												
			</table>
		</div>
		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
		</p>
	</form>
</div>
<?php
}
?>