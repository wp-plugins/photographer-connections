<?php

/**
 * 	SmugMug module for Photographer Connections
 *
 *	Adds short code [smugmug_albums], embedding form in page from ShootQ
 * 
 */

	require_once( "smugmug/phpsmug/phpSmug.php" ); 
	
	add_action ( 'blogsite_connect_extra', 				'blogsite_connect_smugmug_create_menu' );
	add_filter ( 'blogsite_connect_extra_settings',		'blogsite_connect_smugmug_settings' , 10 , 1 );
	
	add_action( 'admin_init', 							'blogsite_connect_register_smugmug_settings' );
	add_action( 'wp_footer', 							'blogsite_connect_add_smugmug_js' );

	add_action( 'init', 								'blogsite_connect_smugmug_create' );
	
	add_shortcode	( 'smugmug_albums', 				'blogsite_connect_smugmug_albums');


	if ( !defined( 'BLOGSITE_CONNECT_SMUGMUG_API' ) ) { define ( 'BLOGSITE_CONNECT_SMUGMUG_API' , 'U280pL1oNrobAluOQo8IPf00usFPS8qU' ); };
	if ( !defined( 'BLOGSITE_CONNECT_SMUGMUG_APP' ) ) { define ( 'BLOGSITE_CONNECT_SMUGMUG_APP' , 'Photographer Connections for WordPress' ); };

function blogsite_connect_smugmug_create() {
	$blogsite_smugmug_settings = get_option( 'blogsite_smugmug_settings' );
	$username = $blogsite_smugmug_settings['username'];
	$password = $blogsite_smugmug_settings['password'];
	
	if ( defined( 'BLOGSITE_CONNECT_SMUGMUG_API') && defined( 'BLOGSITE_CONNECT_SMUGMUG_APP' ) && $username != '' && $password !='' ) {
		$smugObject = new phpSmug( 'APIKey=' . BLOGSITE_CONNECT_SMUGMUG_API, 'AppName=' . BLOGSITE_CONNECT_SMUGMUG_APP );
		
		$cache = $smugObject->enableCache( 'type=fs', 'cache_dir='.WP_CONTENT_DIR.'/smugmug-cache' );
		$smugConnect = $smugObject->login( 'EmailAddress=' . $username, 'Password=' . $password );

		if ( !defined( 'BLOGSITE_CONNECT_SMUGMUG_OBJECT' ) ) 		{ define( 'BLOGSITE_CONNECT_SMUGMUG_OBJECT', maybe_serialize( $smugObject ) ); };		
		if ( !defined( 'BLOGSITE_CONNECT_SMUGMUG_CONNECT' ) ) 		{ define( 'BLOGSITE_CONNECT_SMUGMUG_CONNECT', maybe_serialize( $smugConnect ) ); };		
		if ( !defined( 'BLOGSITE_CONNECT_SMUGMUG_USER_ID' ) )		{ define( 'BLOGSITE_CONNECT_SMUGMUG_USER_ID', $smugConnect['User']['id'] ); };		
		if ( !defined( 'BLOGSITE_CONNECT_SMUGMUG_USER_HASH' ) ) 	{ define( 'BLOGSITE_CONNECT_SMUGMUG_USER_HASH', $smugConnect['PasswordHash'] ); };		
	}
}

function blogsite_connect_smugmug_settings( $connect_settings ){
	$connect_settings['smugmug']['name'] = 'SmugMug';
	$connect_settings['smugmug']['setting'] = 'smugmug';
	$connect_settings['smugmug']['page'] = 'blogsite_smugmug_settings';
	return $connect_settings;
}

function blogsite_connect_register_smugmug_settings() {
	register_setting( 'blogsite_smugmug_settings_group', 'blogsite_smugmug_settings' );
}

function blogsite_connect_smugmug_create_menu() {
	$blogsite_connect_settings = get_option( 'blogsite_connect_settings' );	
	if 	( $blogsite_connect_settings['smugmug'] == 'yes' ){
		add_submenu_page( 'blogsite_photo_connect', 'SmugMug', 'SmugMug', 'administrator', 'blogsite_smugmug_settings' , 'blogsite_smugmug_settings_page' );
	};	
}

function blogsite_smugmug_settings_page() {
?>
<div class="wrap">
	<h2>SmugMug Settings (beta)</h2>
	<form method="post" action="options.php">
    <?php settings_fields( 'blogsite_smugmug_settings_group' ); ?>
	<?php $blogsite_smugmug_settings = get_option( 'blogsite_smugmug_settings' ); ?>

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
				<tr valign="top">
					<th scope="row">Reset Cache & Get New SmugMug Albums</th>
					<td>
						<input type="checkbox" style="width:400px" name="blogsite_smugmug_settings[clear_cache]" value="<?php echo time(); ?>" /><br />
						What is this? Each time we get the SmugMug albums, we try to save them to the site to save time and help pages load faster.<br />
						If you have new albums that don't seem to be showing up, check this box and we 
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
	if ( defined( 'BLOGSITE_CONNECT_SMUGMUG_OBJECT' ) ) {
		$smug = unserialize( BLOGSITE_CONNECT_SMUGMUG_OBJECT );
		$albums = $smug->albums_get();	
		return $albums; 
	};
};

function blogsite_connect_get_smugmug_images($album) {
	
	if ( defined( 'BLOGSITE_CONNECT_SMUGMUG_OBJECT' ) ) {
		$smug = unserialize( BLOGSITE_CONNECT_SMUGMUG_OBJECT );
		
		$images = $smug->images_get( 'AlbumID=' . $album['id'], 'AlbumKey=' .$album['Key'], "Heavy=1" );
		$images = ( $smug->APIVer == "1.2.2" ) ? $images['Images'] : $images;
		
		return $images; 
	};
}

function blogsite_connect_get_smugmug_thumb($album) {
	
	if ( defined( 'BLOGSITE_CONNECT_SMUGMUG_OBJECT' ) ) {
		$smug = unserialize( BLOGSITE_CONNECT_SMUGMUG_OBJECT );
		
		$images = $smug->images_get( 'AlbumID=' . $album['id'], 'AlbumKey=' .$album['Key'], "Heavy=1" );
		$image = ($smug->APIVer == "1.2.2") ? $images['Images'][0]['ThumbURL'] : $images[0]['ThumbURL'];
		
		return $image; 
	}
}

function blogsite_connect_smugmug_albums($atts='') {
    global $post;
	extract(shortcode_atts(array(
		'album' => '',
		'image_size' => '',
		'title' => 'h2',
		'show_title' => 'true',
    ), $atts));

	$album_name = $album;

	$albums = blogsite_connect_get_smugmug_albums(); 
	if ( $album_name == '' ) {
		foreach ( $albums as $album ) {
			echo '<div class="smugmug-gallery-outer-wrapper">';
			echo '<div class="smugmug-gallery-wrapper">';

			if ( $show_title != 'false' ) 
				echo '<'.$title.'>' . $album['Title'] . '</'.$title.'>';
			
			$images = blogsite_connect_get_smugmug_images($album);
			foreach ( $images as $image ) {
				echo '<a rel="shadowbox" href="'.$image['X2LargeURL'].'"><img class="thumbnails" src="'.$image['ThumbURL'].'" title="'.$image['Caption'].'" alt="'.$image['id'].'" /></a>';
			}
			echo '</div>';
			echo '</div>';

		}
	} else {
		foreach ( $albums as $album ) {
			if ( $album['Title'] == html_entity_decode( $album_name ) ) {
			echo '<div class="smugmug-gallery-wrapper">';
			
				if ( $show_title != 'false' ) 
					echo '<'.$title.'>' . $album['Title'] . '</'.$title.'>';	
			
				$images = blogsite_connect_get_smugmug_images($album);
				foreach ( $images as $image ) {
					echo '<a rel="shadowbox[album-'.$post->ID.';player=img;]" href="'.$image['X2LargeURL'].'"><img class="thumbnails" src="'.$image['ThumbURL'].'" title="'.$image['Caption'].'" alt="'.$image['id'].'" /></a>';
				}
			echo '</div>';
			};
		};	
	};
	
	echo $html_final; 
}

function blogsite_connect_smugmug_preview() {

	$blogsite_smugmug_settings = get_option( 'blogsite_smugmug_settings' );
	
	$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 0;
	if ( empty( $pagenum ) )
		$pagenum = 1;

	$cache_check = get_option( 'blogsite_connect_smugmug_albums_cache_timestamp' );
	$albums = get_option( 'blogsite_connect_smugmug_albums_cache' );

	if ( is_array( $albums ) ) {
		foreach ( $albums as $key => $row ) {
    		$title[$key]  = $row['Title'];
		}
		array_multisort( $title, SORT_ASC, $albums );
	};

	if ( $cache_check == '' || !is_array( $albums ) || $blogsite_smugmug_settings['clear_cache'] >= $cache_check ) {
		$albums = blogsite_connect_get_smugmug_albums();
		update_option( 'blogsite_connect_smugmug_albums_cache' , $albums );
		update_option( 'blogsite_connect_smugmug_albums_cache_timestamp' , time() );
	};

	$per_page = 10;
	$pages =  intval( count( $albums ) / $per_page );
	
	
	$page_links = paginate_links( array(
		'base' 		=> add_query_arg( 'pagenum', '%#%' ),
		'format' 	=> '',	
		'prev_text' => __('&laquo;'),
		'next_text' => __('&raquo;'),
		'total' 	=> $pages,
		'current' 	=> $pagenum,	
		'end_size' 	=> 2,
		'mid_size' 	=> 2,	
	));
	

	echo '<div class="pane">';
	echo '<h3>Instructions</h3>';

	echo '<p>To automatically show all albums in any page or post, copy and paste this shortcode:<br />[smugmug_albums]</p>';
	echo '<p>To automatically show a specific album in any page or post, include the album name in the shortcode:<br />[smugmug_albums album="Album Name"]</p>';

	
	$count_albums = count($albums);
	
	$page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
						number_format_i18n( ( $pagenum - 1 ) * $per_page + 1 ),
						number_format_i18n( min( $pagenum * $per_page, $count_albums ) ),
						number_format_i18n( $count_albums ),
						$page_links
						);

		
	echo '<div class="pane">';

	if ( $page_links ) {
		echo '<h3>Currently Available Albums - last cached: '.date( 'M d, Y' , $blogsite_smugmug_settings['clear_cache'] ).'</h3>';
		echo '<p>Added Albums that you are not seeing here? Try resetting the cache with the settings above</p>';
		echo '<div class="tablenav">';
		echo "<div class='tablenav-pages'>$page_links_text</div>";	
		echo '</div>';
	};
		
		for ( $count = ( $pagenum - 1 ) * $per_page ; $count <= ( ($pagenum - 1) * $per_page ) + ($per_page -1); $count ++ ) {
			$image = blogsite_connect_get_smugmug_thumb( $albums[$count] );
		?>
			<div class="pane">
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php echo $albums[$count]['Title']; ?></th>
					<td width="100px"><img class="thumbnails" src="<?php echo $image ; ?>" />
					</td>
					<td>To add this album to a post, copy and paste this shortcode:<br />
					<span style="background: #EAF2FA; padding: 2px 8px 4px;">[smugmug_albums album="<?php echo $albums[$count]['Title'];?>"]</span>
					</td>
				</tr>		
			</table>
			</div>
<?php
		}
	echo '</div>';
}

?>