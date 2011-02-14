<?php
	require_once("smugmug/phpsmug/phpSmug.php"); 
	
	add_action ('blogsite_connect_extra', 				'blogsite_connect_smugmug_create_menu');
	add_filter ('blogsite_connect_extra_settings',		'blogsite_connect_smugmug_settings',10,1);
	
	add_action( 'admin_init', 							'blogsite_connect_register_smugmug_settings' );
	add_action( 'wp_footer', 							'blogsite_connect_add_smugmug_js' );

	add_action( 'init', 								'blogsite_connect_smugmug_create' );
	
	add_shortcode	('smugmug_albums', 					'blogsite_connect_smugmug_albums');


	if (!defined('BLOGSITE_CONNECT_SMUGMUG_API')) { define ('BLOGSITE_CONNECT_SMUGMUG_API', 'U280pL1oNrobAluOQo8IPf00usFPS8qU'); };
	if (!defined('BLOGSITE_CONNECT_SMUGMUG_APP')) { define ('BLOGSITE_CONNECT_SMUGMUG_APP', 'Photographer Connections for WordPress'); };

function blogsite_connect_smugmug_create() {
	$blogsite_smugmug_settings = get_option('blogsite_smugmug_settings');
	$username = $blogsite_smugmug_settings['username'];
	$password = $blogsite_smugmug_settings['password'];
	
	if (defined('BLOGSITE_CONNECT_SMUGMUG_API') && defined ('BLOGSITE_CONNECT_SMUGMUG_APP') && $username != '' && $password !='') {
		$smugObject = new phpSmug('APIKey=' . BLOGSITE_CONNECT_SMUGMUG_API, 'AppName=' . BLOGSITE_CONNECT_SMUGMUG_APP);
		
		$cache = $smugObject->enableCache('type=fs', 'cache_dir='.WP_CONTENT_DIR.'/smugmug-cache');
		$smugConnect = $smugObject->login('EmailAddress=' . $username, 'Password=' . $password);

		if ( !defined('BLOGSITE_CONNECT_SMUGMUG_OBJECT') ) 		{ define('BLOGSITE_CONNECT_SMUGMUG_OBJECT', maybe_serialize($smugObject)); };		
		if ( !defined('BLOGSITE_CONNECT_SMUGMUG_CONNECT') ) 	{ define('BLOGSITE_CONNECT_SMUGMUG_CONNECT', maybe_serialize($smugConnect)); };		
		if ( !defined('BLOGSITE_CONNECT_SMUGMUG_USER_ID') )		{ define('BLOGSITE_CONNECT_SMUGMUG_USER_ID', $smugConnect['User']['id']); };		
		if ( !defined('BLOGSITE_CONNECT_SMUGMUG_USER_HASH') ) 	{ define('BLOGSITE_CONNECT_SMUGMUG_USER_HASH', $smugConnect['PasswordHash']); };		
	}
}

function blogsite_connect_smugmug_settings($connect_settings){
	$connect_settings['smugmug']['name'] = 'SmugMug';
	$connect_settings['smugmug']['setting'] = 'smugmug';
	$connect_settings['smugmug']['page'] = 'blogsite_smugmug_settings';
	return $connect_settings;
}

function blogsite_connect_register_smugmug_settings() {
	register_setting( 'blogsite_smugmug_settings_group', 'blogsite_smugmug_settings');
}

function blogsite_connect_smugmug_create_menu() {
	$blogsite_connect_settings = get_option('blogsite_connect_settings');	
	if 	($blogsite_connect_settings['smugmug'] == 'yes' ){
		add_submenu_page( 'blogsite_photo_connect', 'SmugMug', 'SmugMug', 'administrator', 'blogsite_smugmug_settings' , 'blogsite_smugmug_settings_page');
	};	
}

function blogsite_smugmug_settings_page() {
?>
<div class="wrap">
	<h2>SmugMug Settings (beta)</h2>
	<form method="post" action="options.php">
    <?php settings_fields( 'blogsite_smugmug_settings_group' ); ?>
	<?php $blogsite_smugmug_settings = get_option('blogsite_smugmug_settings'); ?>

		<div class="pane">
			<h3>Account Settings</h3>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">SmugMug UserName</th>
					<td>
						<input type="text" style="width:400px" name="blogsite_smugmug_settings[username]" value="<?php echo $blogsite_smugmug_settings['username']; ?>" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">SmugMug Password</th>
					<td>
						<input type="password" style="width:400px" name="blogsite_smugmug_settings[password]" value="<?php echo $blogsite_smugmug_settings['password']; ?>" />
					</td>
				</tr>			
			</table>
		</div>
		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
		</p>		
	</form>
	<?php blogsite_connect_smugmug_preview(); ?>
<?php
}
function blogsite_connect_add_smugmug_js() {
?>
<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){
	$('#loader').fadeIn(400);
    
	$(window).load(function(){
    	$('#loader').fadeOut(400);

		$('#main').fadeIn(1500);
	});
});
/* ]]> */
</script>
<?php 
}


function blogsite_connect_get_smugmug_albums() { 
	$smug = unserialize(BLOGSITE_CONNECT_SMUGMUG_OBJECT);
	$albums = $smug->albums_get();	
	return $albums; 
};

function blogsite_connect_get_smugmug_images($album) {
	
	$smug = unserialize(BLOGSITE_CONNECT_SMUGMUG_OBJECT);
	
	$images = $smug->images_get( 'AlbumID=' . $album['id'], 'AlbumKey=' .$album['Key'], "Heavy=1" );
	$images = ($smug->APIVer == "1.2.2") ? $images['Images'] : $images;
	
	return $images; 
}

function blogsite_connect_smugmug_albums($atts='') {
    global $post;
	extract(shortcode_atts(array(
		'album_name' => '',
		'image_size' => '',
		'title' => 'h2',
    ), $atts));

	$albums = blogsite_connect_get_smugmug_albums(); 
	if ($album_name == '' ) {
		foreach ($albums as $album) {
			echo '<'.$title.'>' . $album['Title'] . '</'.$title.'>';
			//echo '<p>Category: ' . $album['Category']['Name'] . '</p>';
			$images = blogsite_connect_get_smugmug_images($album);
			foreach ($images as $image) {
				echo '<a rel="shadowbox" href="'.$image['X2LargeURL'].'"><img class="thumbnails" src="'.$image['ThumbURL'].'" title="'.$image['Caption'].'" alt="'.$image['id'].'" /></a>';
			}
		}
	} else {
		foreach ($albums as $album) {
			if ($album['Title'] == $album_name) {
				echo '<'.$title.'>Name: ' . $album['Title'] . '</'.$title.'>';		
				$images = blogsite_connect_get_smugmug_images($album);
				foreach ($images as $image) {
					echo '<a rel="shadowbox[album-'.$post->ID.';player=img;]" href="'.$image['X2LargeURL'].'"><img class="thumbnails" src="'.$image['ThumbURL'].'" title="'.$image['Caption'].'" alt="'.$image['id'].'" /></a>';
				}
			};
		};	
	};
	
	echo $html_final; 
}

function blogsite_connect_smugmug_preview() {
	
	$albums = blogsite_connect_get_smugmug_albums(); 
	echo '<div class="pane">';
	echo '<p>To automatically show all albums in any page or post, copy and paste this shortcode:<br />[smugmug_albums]</p>';
	echo '<p>To automatically show a specific album in any page or post, include the album name in the shortcode:<br />[smugmug_albums album="Album Name"]</p>';

	echo '<h3>These are your currently available albums</h3>';
		foreach ($albums as $album) {
			$images = blogsite_connect_get_smugmug_images($album);
		?>
			<div class="pane">
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php echo $album['Title']; ?></th>
					<td width="100px"><img class="thumbnails" src="<?php echo $images[0]['ThumbURL']; ?>" />
					</td>
					<td>To add this album to a post, copy and paste this shortcode:<br />
					<span style="background: #EAF2FA; padding: 2px 8px 4px;">[smugmug_albums album="<?php echo $album['Title'];?>"]</span>
					</td>
				</tr>		
			</table>
			</div>
<?php
		}
	
}

?>