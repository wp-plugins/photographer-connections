<?php

/**
 * 	Album Exposure module for Photographer Connections
 *
 *	Adds short code [album_exposure]
 *
 * 	Adds page templates to child theme and theme to accomodate Album Exposure page
 * 
 */

	add_action ('blogsite_connect_extra', 				'blogsite_connect_album_exposure_create_menu');
	add_filter ('blogsite_connect_extra_settings',		'blogsite_connect_album_exposure_settings');
	
	add_action( 'admin_init', 							'blogsite_connect_register_album_exposure_settings' );
	add_action( 'wp_head', 								'blogsite_connect_add_album_exposure_functions' );
	add_action( 'init', 								'blogsite_connect_check_templates' );

	add_shortcode	('album_exposure', 					'album_exposure');
	
function blogsite_connect_check_templates() {
	delete_option('template_created');
	$album_template = BLOGSITE_CONNECT_PLUGIN_BASEDIR . '/modules/album-exposure/templates/page-album-exposure.php';
	$childtheme_template = STYLESHEETPATH . '/page-album-exposure.php';
	$theme_template = TEMPLATEPATH . '/page-album-exposure.php';
	$template_version = get_option('blogsite_connect_current_template_version');
	$plugin_version = get_option('blogsite_connect_current_version');

		
	if ( file_exists($album_template) && !file_exists($childtheme_template)) {
		copy ($album_template,$childtheme_template);
		update_option('album_exposure_child_template_created','yes');
	} elseif (file_exists($childtheme_template)){
		update_option('album_exposure_child_template_created','yes');
	} else {
		update_option('album_exposure_child_template_created','no');
	};
	
	if ( file_exists($album_template) && !file_exists($theme_template)) {
		copy ($album_template,$theme_template);
		update_option('album_exposure_template_created','yes');
	} elseif (file_exists($theme_template)){
		update_option('album_exposure_template_created','yes');
	} else {
		update_option('album_exposure_template_created','no');
	};

	if ($plugin_version != $template_version ) {
		copy ($album_template,$theme_template);
		copy ($album_template,$theme_template);
		update_option('album_exposure_template_created','yes');
		update_option('album_exposure_child_template_created','yes');
	}
}

function blogsite_connect_add_album_exposure_functions() {
	$blogsite_connect_settings = get_option('blogsite_connect_settings');	
	
	if 	($blogsite_connect_settings['album_exposure'] == 'yes' ){
		// DO album_exposure STUFF...		
	};
	
	// This is outside settings in case they use it without activating...	
		$css = "\n\n";
		$css .= '<style type="text/css">'."\n";
		$css .= '     html	{height:100%; width:100%}'."\n";
		$css .= '     body	{height:100%; width:100%}'."\n";
		$css .= '</style>'."\n\n";

		if (is_page_template('page-album-exposure.php') ) {
			echo $css;
		}
	
}

function blogsite_connect_album_exposure_settings($connect_settings){
	$connect_settings['album_exposure']['name'] = 'Album Exposure';
	$connect_settings['album_exposure']['setting'] = 'album_exposure';
	$connect_settings['album_exposure']['page'] = 'blogsite_album_exposure_settings';
	
	return $connect_settings;
}

function blogsite_connect_register_album_exposure_settings() {
	register_setting( 'blogsite_album_exposure_settings_group', 'blogsite_album_exposure_settings');
}
function blogsite_connect_album_exposure_create_menu() {
	$blogsite_connect_settings = get_option('blogsite_connect_settings');	
	if 	($blogsite_connect_settings['album_exposure'] == 'yes' ){
		add_submenu_page( 'blogsite_photo_connect', 'Album Exposure', 'Album Exposure', 'administrator', 'blogsite_album_exposure_settings' , 'blogsite_album_exposure_settings_page');
	};
}

function album_exposure($atts='') {
	$blogsite_album_exposure_settings = get_option('blogsite_album_exposure_settings');

    extract(shortcode_atts(array(
		'username' => $blogsite_album_exposure_settings['username'],
    ), $atts));

 	$album_exposure = '<iframe src="http://albumexposure.com/'.$username.'" frameborder="0" scrolling="auto" id="topFrame" title="topFrame" width="100%" height="100%"/>';
	echo $album_exposure;

}


function blogsite_album_exposure_settings_page() {

	// ADD CREATION OF NEW TEMPLATE

?>
<div class="wrap">
	
	<?php 
		$child_template = get_option('album_exposure_child_template_created'); 
		$theme_template = get_option('album_exposure_template_created'); 
	?>
	
	<h2>Album Exposure Settings (beta)</h2>
	<p>Seems to be working well. Any feedback appreciated!
	<p>
	<form method="post" action="options.php">
    <?php settings_fields( 'blogsite_album_exposure_settings_group' ); ?>
	<?php $blogsite_album_exposure_settings = get_option('blogsite_album_exposure_settings'); ?>
		<div class="pane">
			<h3>Account Settings</h3>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">Album Exposure UserName</th>
					<td>
						<input type="text" style="width:400px" name="blogsite_album_exposure_settings[username]" value="<?php echo $blogsite_album_exposure_settings['username']; ?>" />
					</td>
				</tr>
			</table>
			<h3>Display Page Options</h3>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">SEO: Give the page a title meta tag:</th>
					<td>
						<input type="text" style="width:400px" name="blogsite_album_exposure_settings[meta_title]" value="<?php echo $blogsite_album_exposure_settings['meta_title']; ?>" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">SEO: Give the page a description meta tag:</th>
					<td>
						<input type="text" style="width:400px" name="blogsite_album_exposure_settings[meta_desc]" value="<?php echo $blogsite_album_exposure_settings['meta_desc']; ?>" />
					</td>
				</tr>
			</table>
		</div>
		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
		</p>
	</form>

		<h3>Custom Page Templates</h3>
		<div class="pane">
			<p>The album viewer is pretty big and would work best in a page that fills the enitre browser window. Most themes will not have a page template that will accomodate this.</p>
			
			<?php if ( $child_template  == 'yes' && $theme_template == 'yes') { ?>
			<p>We have automatically added one to your theme as a starting point. It is a file called <code>page-album-exposure.php</code></p> 
			<p>You will find it in your theme (or child theme) folder. Feel free to edit as you see fit.</p>
			<p>As you edit any page, add the [album_exposure] shortcode, and select the new "Album Exposure" page template from the list.</p>
			
			<?php } else { ?>
			
			<p>The plugin attempted to automatically create a page template for you but was unsuccesful.</p>
			
			<div class="pane">
				<h3>Download Template File</h3>
				<p>You can download this file, change the extension to ".php" and upload it to your theme's folder.</p>
				<a class="button-secondary" href="<?php echo BLOGSITE_CONNECT_PLUGIN_URL; ?>modules/album-exposure/templates/page-album-exposure.txt">Download Sample Page Template</a>
			</div>
		
			<?php }; ?>
		</div>
</div>
<?php
}

?>