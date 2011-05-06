<?php
/*

	This is meant as a sample for starting a new module.
	Start by replacing SAMPLE with the name of the new service, EX: flickr
	Then place the file in the 'modules' folder and it will be activated
	
	Use the settings page to acquire whatever API or usernames you need,
	then create whatever functions or shortcodes you need to make use of things.
	
	Need Ideas? Here are some great photography API's:
	http://blog.programmableweb.com/2008/03/11/36-photo-apis/
	
*/

	add_action ('blogsite_connect_extra', 				'blogsite_connect_SAMPLE_create_menu');
	add_filter ('blogsite_connect_extra_settings',		'blogsite_connect_SAMPLE_settings');
	
	add_action( 'admin_init', 							'blogsite_connect_register_SAMPLE_settings' );
	add_action( 'wp_head', 								'blogsite_connect_add_SAMPLE_functions' );


function blogsite_connect_add_SAMPLE_functions() {
	$blogsite_connect_settings = get_option('blogsite_connect_settings');	
	if 	($blogsite_connect_settings['SAMPLE'] == 'yes' ){
		// DO SAMPLE STUFF...
	};
}

function blogsite_connect_SAMPLE_settings($connect_settings){
	$connect_settings['SAMPLE']['name'] = 'SAMPLE';
	$connect_settings['SAMPLE']['setting'] = 'SAMPLE';
	$connect_settings['SAMPLE']['page'] = 'blogsite_SAMPLE_settings';
	
	return $connect_settings;
}

function blogsite_connect_register_SAMPLE_settings() {
	register_setting( 'blogsite_SAMPLE_settings_group', 'blogsite_SAMPLE_settings');
}
function blogsite_connect_SAMPLE_create_menu() {
	$blogsite_connect_settings = get_option('blogsite_connect_settings');	
	if 	($blogsite_connect_settings['SAMPLE'] == 'yes' ){
		add_submenu_page( 'blogsite_photo_connect', 'SAMPLE', 'SAMPLE', 'administrator', 'blogsite_SAMPLE_settings' , 'blogsite_SAMPLE_settings_page');
	};
}

function blogsite_SAMPLE_settings_page() {
?>
<div class="wrap">
	<h2>SAMPLE Settings</h2>
	<form method="post" action="options.php">
    <?php settings_fields( 'blogsite_SAMPLE_settings_group' ); ?>
	<?php $blogsite_SAMPLE_settings = get_option('blogsite_SAMPLE_settings'); ?>
		<div class="pane">
			<h3>Coming Soon...</h3>
		</div>
	</form>
</div>
<?php
}

?>