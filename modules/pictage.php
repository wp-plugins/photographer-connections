<?php

/**
 * 	Pictage module for Photographer Connections
 *
 *	Adds short code [pictage_events]
 * 
 */

	add_action 	('blogsite_connect_extra', 				'blogsite_connect_pictage_create_menu');
	add_filter 	('blogsite_connect_extra_settings',		'blogsite_connect_pictage_settings');
	
	add_action	( 'admin_init', 						'blogsite_connect_register_pictage_settings' );
	add_action	( 'wp_head', 							'blogsite_connect_add_pictage_functions' );
	
	add_shortcode	('pictage_events', 						'pictage_events');


function blogsite_connect_add_pictage_functions() {
	$blogsite_connect_settings = get_option('blogsite_connect_settings');	
	if 	($blogsite_connect_settings['pictage'] == 'yes' ){
		// DO Pictage STUFF...
	};
}

function blogsite_connect_pictage_settings($connect_settings){
	$connect_settings['pictage']['name'] = 'Pictage';
	$connect_settings['pictage']['setting'] = 'pictage';
	$connect_settings['pictage']['page'] = 'blogsite_pictage_settings';
	
	return $connect_settings;
}

function blogsite_connect_register_pictage_settings() {
	register_setting( 'blogsite_pictage_settings_group', 'blogsite_pictage_settings');
}
function blogsite_connect_pictage_create_menu() {
	$blogsite_connect_settings = get_option('blogsite_connect_settings');	
	if 	($blogsite_connect_settings['pictage'] == 'yes' ){
		add_submenu_page( 'blogsite_photo_connect', 'Pictage', 'Pictage', 'administrator', 'blogsite_pictage_settings' , 'blogsite_pictage_settings_page');
	};
}

function pictage_events($atts='') {
    extract(shortcode_atts(array(
		'studio_id' => '',
		'list_css' => '',
		'list_item_css' => '',
    ), $atts));

	if ($studio_id == '') {
		$blogsite_pictage_settings = get_option('blogsite_pictage_settings');
		$studio_id = $blogsite_pictage_settings['studio_id'];
	};
	
	$full = "http://external.pictage.com/external/PHTINTEG?photog=$studio_id";
	$html = getPage($full);

	// REMOVE ALL THE STUPID EXTRA STUFF THEY ADD IN...
	$html = str_replace('target="PICTAGE"', 'class="pictage_links"', $html);
	$html = str_replace("<hr>", " ", $html);
	$html = str_replace("<title>Pictage</title>", "", $html);
	$html = str_replace("<br>", "", $html);
	
	// ADD LINKS TO LIST ITEMS
	$html = preg_replace('/<a href=\"(.*?)\/a>/', '<li><a href="$1/a></li>', $html);
	
	// WRAP IN A DIV AND UL
	$html_final = '<div class="pictage-events">';
	$html_final .= '<ul>';
	$html_final .= $html;
	$html_final .= '</ul>';
	$html_final .= '</div>';
	
	echo $html_final; 
}

function getPage($url=""){
 $ch = curl_init();
 curl_setopt($ch,CURLOPT_URL, $url);
 curl_setopt($ch,CURLOPT_FRESH_CONNECT,TRUE);
 curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,5);
 curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
 curl_setopt($ch,CURLOPT_TIMEOUT,10);
 $html=curl_exec($ch);
 if($html==false){
  $m=curl_error(($ch));
  error_log($m);
 }
 curl_close($ch);
 return $html;
}

function blogsite_pictage_settings_page() {
?>
<div class="wrap">
	<h2>Pictage Settings</h2>
	<form method="post" action="options.php">
    <?php settings_fields( 'blogsite_pictage_settings_group' ); ?>
	<?php $blogsite_pictage_settings = get_option('blogsite_pictage_settings'); ?>
		<div class="pane">
			<h3>List Pictage Events</h3>
			<p>Use the short code [pictage_events] in any page or post to list your public events.</p>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">Pictage Studio ID</th>
					<td>
						<input type="text" style="width:400px" name="blogsite_pictage_settings[studio_id]" value="<?php echo $blogsite_pictage_settings['studio_id']; ?>" /><i>i.e. AB123 (Upper Case Only)</i>
					</td>
				</tr>
			</table>
		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
		</p>			
		</div>
	</form>
</div>
<?php
}

?>