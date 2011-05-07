<?php

/**
 * 	SmugMug module for Photographer Connections
 *
 *	Adds short code [smugmug_albums], embedding form in page from ShootQ
 * 
 */
	//define ('BLOGSITE_CONNECT_SMUGMUG_METHOD', 'oauth');
	//define ('BLOGSITE_CONNECT_SMUGMUG_API_VER', '1.3.0');
	
	require_once("smugmug/phpSmug.php"); 
	
	blogsite_connect_include_folder('modules/smugmug/functions');
	
	add_action ('blogsite_connect_extra', 				'blogsite_connect_smugmug_connection_menu');
	add_filter ('blogsite_connect_extra_settings',		'blogsite_connect_smugmug_settings',10,1);
	
	add_action( 'admin_init', 							'blogsite_connect_register_smugmug_settings' );
	add_action( 'init', 								'blogsite_connect_smugmug_init' );

	add_action( 'wp_head', 								'blogsite_connect_add_smugmug_css' );
	add_action( 'wp_footer', 							'blogsite_connect_add_smugmug_js' );
		
	add_shortcode	('smugmug_albums', 					'blogsite_connect_smugmug_albums');


	if (!defined('BLOGSITE_CONNECT_SMUGMUG_API')) { define ('BLOGSITE_CONNECT_SMUGMUG_API', 'U280pL1oNrobAluOQo8IPf00usFPS8qU'); };
	if (!defined('BLOGSITE_CONNECT_SMUGMUG_APP')) { define ('BLOGSITE_CONNECT_SMUGMUG_APP', 'Photographer Connections for WordPress'); };
	if (!defined('BLOGSITE_CONNECT_SMUGMUG_OAUTH')) { define ('BLOGSITE_CONNECT_SMUGMUG_OAUTH', '6f049e2b5d09bffc9be74535d1054678'); };

	function blogsite_connect_smugmug_connection() {
	

		if ( !defined( 'BLOGSITE_CONNECT_SMUGMUG_METHOD' ) || BLOGSITE_CONNECT_SMUGMUG_METHOD != 'oauth' ) {
			if ( defined( 'BLOGSITE_CONNECT_SMUGMUG_API_VER' ) && BLOGSITE_CONNECT_SMUGMUG_API_VER == '1.3.0' ) {
				$return = blogsite_connect_smugmug_connect_130();
			} else {
				$return = blogsite_connect_smugmug_connect();
			}
		} else {
			$return = blogsite_connect_smugmug_oAuth();
		}
		
		return $return;
	}

	/**
	 * establish connection with SmugMug
	 *
	 */	
	function blogsite_connect_smugmug_connect() {
		$blogsite_smugmug_settings = get_option('blogsite_smugmug_settings');
		$username = $blogsite_smugmug_settings['username'];
		$password = $blogsite_smugmug_settings['password'];
		
		if ( defined('BLOGSITE_CONNECT_SMUGMUG_API' ) && defined ( 'BLOGSITE_CONNECT_SMUGMUG_APP' ) && $username != '' && $password !='' ) {
			
			$connectArgs = array(
				'APIKey' => BLOGSITE_CONNECT_SMUGMUG_API,
				'AppName' => BLOGSITE_CONNECT_SMUGMUG_APP,
			);

			$loginArgs = array(
				'EmailAddress' => $username,
				'Password' => $password,
			);
			
			try {
				$smugObject = new phpSmug( $connectArgs );
				
				$smugObject->login( $loginArgs );
				
				if ( !defined('BLOGSITE_CONNECT_SMUGMUG_OBJECT') ) 		{ define('BLOGSITE_CONNECT_SMUGMUG_OBJECT', maybe_serialize($smugObject)); };		
				return $smugObject;
			}
			catch( Exception $e ) {
				if ( $username != '' && $password !='' && $e->getCode() != '1' ) {
					echo "{$e->getMessage()} (Error Code: {$e->getCode()})";
				} elseif ( $e->getCode() == '1' ) {
					echo '<div class="error">Invalid Username / Password combination.</div>';
				}
	 		}
		} elseif ( $username != '' && $password !='' ) {
			$smugObject = false;
			return $smugObject;
		}
	}

	function blogsite_connect_smugmug_connect_130() {
		$blogsite_smugmug_settings = get_option('blogsite_smugmug_settings');
		$username = $blogsite_smugmug_settings['username'];
		$password = $blogsite_smugmug_settings['password'];
		
		if ( defined('BLOGSITE_CONNECT_SMUGMUG_API' ) && defined ( 'BLOGSITE_CONNECT_SMUGMUG_APP' ) && $username != '' && $password !='' ) {
			
			$connectArgs = array(
				'APIKey' => BLOGSITE_CONNECT_SMUGMUG_API,
				'AppName' => BLOGSITE_CONNECT_SMUGMUG_APP,
				'APIVer' => '1.3.0',				
			);

			$loginArgs = array(
				'NickName' => $username,
				'Password' => $password,
			);
			
			try {
				$smugObject = new phpSmug( $connectArgs );

				$smugObject->accounts_browse( $loginArgs );

				if ( !defined('BLOGSITE_CONNECT_SMUGMUG_OBJECT') ) 		{ define('BLOGSITE_CONNECT_SMUGMUG_OBJECT', maybe_serialize($smugObject)); };		
				return $smugObject;
			}
			catch( Exception $e ) {
				if ( $username != '' && $password !='' && $e->getCode() != '1' ) {
					echo "{$e->getMessage()} (Error Code: {$e->getCode()})";
				} elseif ( $e->getCode() == '1' ) {
					echo '<div class="error">Invalid Username / Password combination.</div>';
				}
	 		}
		} elseif ( $username != '' && $password !='' ) {
			$smugObject = false;
			return $smugObject;
		}
	}

	function blogsite_connect_smugmug_oAuth() {
		$blogsite_smugmug_settings = get_option('blogsite_smugmug_settings');
		
		$token['id'] = $blogsite_smugmug_settings['token'];
		$token['secret'] = $blogsite_smugmug_settings['token_secret'];
		
		if ( defined('BLOGSITE_CONNECT_SMUGMUG_API' ) && defined ( 'BLOGSITE_CONNECT_SMUGMUG_APP' ) && defined ( 'BLOGSITE_CONNECT_SMUGMUG_OAUTH' ) ) {
			
			$connectArgs = array(
				'APIKey' => BLOGSITE_CONNECT_SMUGMUG_API,
				'AppName' => BLOGSITE_CONNECT_SMUGMUG_APP,
				'APIVer' => '1.3.0',
				'OAuthSecret' => BLOGSITE_CONNECT_SMUGMUG_OAUTH,
			);
			
			// try to create a new object
			try {
				$smugObject = new phpSmug( $connectArgs );
			}
			catch( Exception $e ) {
				echo "{$e->getMessage()} (Error Code: {$e->getCode()})";
	 		}
			
			// if we don't have a token yet...
			// if ( $token['id'] != '' && $token['secret'] != '' && !isset( $_SESSION['SmugGalReqToken'] ) ) {
			if ( !isset( $_SESSION['SmugGalReqToken'] ) ) {

				try {				
				
					$reqToken = $smugObject->auth_getRequestToken();
					$_SESSION['SmugGalReqToken'] = serialize( $reqToken );
									
					echo "<p>Click <a href='".$smugObject->authorize("Access=Read", "Permissions=None")."' target='_blank'><strong>HERE</strong></a> to Authorize This Demo.</p>";
      			 	echo "<p>A new window/tab will open asking you to login to SmugMug (if not already logged in).  Once you've logged it, SmugMug will redirect you to a page asking you to approve the access (it's read only) to your public photos.  Approve the request and come back to this page and click REFRESH below.</p>";
        			echo "<p><a href='".$_SERVER['PHP_SELF']."'><strong>REFRESH</strong></a></p>";
			
					// once they are uathorized...
					$token = $smugObject->auth_getAccessToken();
					
				}
				catch( Exception $e ) {
					echo "{$e->getMessage()} (Error Code: {$e->getCode()})";
	 			}



			} else {
			
				$reqToken = unserialize( $_SESSION['SmugGalReqToken'] );
				unset( $_SESSION['SmugGalReqToken'] );
				session_unregister( 'SmugGalReqToken' );
			
				try {				
				
					// Step 3: Use the Request token obtained in step 1 to get an access token
					$smugObject->setToken("id={$reqToken['Token']['id']}", "Secret={$reqToken['Token']['Secret']}");
					$token = $smugObject->auth_getAccessToken();	// The results of this call is what your application needs to store.
		
					// Set the Access token for use by phpSmug.   
					$smugObject->setToken( "id={$token['Token']['id']}", "Secret={$token['Token']['Secret']}" );
				}
				catch( Exception $e ) {
					echo "{$e->getMessage()} (Error Code: {$e->getCode()})";
	 			}			
			}
			
			// save token info for next time
			$blogsite_smugmug_settings['token'] = $token['Token']['id'];
			$blogsite_smugmug_settings['token_secret'] = $token['Token']['Secret'];
			update_option ( 'blogsite_smugmug_settings' , $blogsite_smugmug_settings );
		} else {
			// something wrong with APP info in constants
		}
		return $smugObject;
	}

	/**
	 * Add settings to Photographer Connections menus
	 *
	 */		
	function blogsite_connect_smugmug_settings($connect_settings){
		$connect_settings['smugmug']['name'] = 'SmugMug';
		$connect_settings['smugmug']['setting'] = 'smugmug';
		$connect_settings['smugmug']['page'] = 'blogsite_smugmug_settings';
		return $connect_settings;
	}

	/**
	 * register settings
	 *
	 */		
	function blogsite_connect_register_smugmug_settings() {
		register_setting( 'blogsite_smugmug_settings_group', 'blogsite_smugmug_settings');
	}

	/**
	 * Add admin menu
	 *
	 */		
	function blogsite_connect_smugmug_connection_menu() {
		$blogsite_connect_settings = get_option('blogsite_connect_settings');	
		if 	($blogsite_connect_settings['smugmug'] == 'yes' ){
			add_submenu_page( 'blogsite_photo_connect', 'SmugMug', 'SmugMug', 'administrator', 'blogsite_smugmug_settings' , 'blogsite_smugmug_settings_page');
		};	
	}

	/**
	 * admin settings page
	 *
	 */		
	function blogsite_smugmug_settings_page() {
	?>
	<div class="wrap">
		<h2>SmugMug Settings</h2>
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
							<input type="hidden" name="blogsite_smugmug_settings[username_prev]" value="<?php echo $blogsite_smugmug_settings['username']; ?>" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">SmugMug Password</th>
						<td>
							<input type="password" style="width:400px" name="blogsite_smugmug_settings[password]" value="<?php echo $blogsite_smugmug_settings['password']; ?>" />
							<input type="hidden" name="blogsite_smugmug_settings[password_prev]" value="<?php echo $blogsite_smugmug_settings['password']; ?>" />
						</td>
					</tr>			
	
				</table>
				<h3>Style Settings</h3>
				<table class="form-table">	
					<tr valign="top">
						<th scope="row">Include Default Style?</th>
						<td>
							<input type="checkbox" style="width:400px" name="blogsite_smugmug_settings[include_css]" <?php if ( $blogsite_smugmug_settings['include_css'] != '' ) echo 'checked="checked"'; ?> value="yes" /><br />
							This plugin includes some default style to help the galleries look nicer. You can also style them using some CSS.
						</td>
					</tr>	
				</table>
				<!-- NOT SURE CACHE IS DOING ANYTHING ------
				<h3>Cache Settings</h3>
				<table class="form-table">	
					<tr valign="top">
						<th scope="row">Reset Cache & Get New SmugMug Albums</th>
						<td>
							<input type="checkbox" style="width:400px" name="blogsite_smugmug_settings[clear_cache]" value="</?php echo time(); ?>" /><br />
							What is this? Each time we get the SmugMug albums, we try to save them to the site to save time and help pages load faster.<br />
							If you have new albums that don't seem to be showing up, check this box and we will check with SmugMug again for new albums.<br />
							
						</td>
					</tr>	
				</table> 
				-->
			</div>
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>		
		</form>
		
	<?php 
		blogsite_connect_smugmug_preview();	
	}
	
	/**
	 * enqueue jQuery
	 *
	 */		
	function blogsite_connect_smugmug_init() {
		wp_enqueue_script ( 'jquery' );
	}
	
	/**
	 * Add css to wp_head
	 *
	 */		
	function blogsite_connect_add_smugmug_css() {
		$blogsite_smugmug_settings = get_option('blogsite_smugmug_settings');
		if ( $blogsite_smugmug_settings['include_css'] == 'yes' ) {
			$css = "\n \n \t" . "<!-- Start Photographer Connections SmugMug CSS -->";
			$css .= "<style type='text/css'>";
			$css .= "\n \t" . ".smugmug-gallery-outer-wrapper { display: block; clear: both; width: 100%; overflow: hidden; }";
			$css .= "\n \t" . ".smugmug-gallery-outer-wrapper .smugmug-gallery-wrapper { display: none; }";
			$css .= "\n \t" . ".smugmug-gallery-outer-wrapper .thumbnails { width: 150px; float: left; padding: 2px; margin: 10px; border: 1px solid #bababa; }";
			$css .= "\n \t" . ".smugmug-gallery-outer-wrapper .book { width:208px; float:left; margin:10px; border:1px #dedede solid; padding:5px; }";
			$css .= "\n \t" . ".smugmug-gallery-outer-wrapper .title { margin-bottom:6px; }";
			$css .= "\n \t" . ".smugmug-gallery-outer-wrapper .description	{ font-size:11px; font-family:Geneva, Arial, Helvetica, sans-serif; }";
			$css .= "\n \t" . ".smugmug-gallery-outer-wrapper .date { font-size:10px; color:#999; margin-top:4px; }";
			$css .= "\n \t" . ".smugmug-gallery-outer-wrapper .smugmug-gallery-loader { background: url( ".BLOGSITE_CONNECT_PLUGIN_URL."/modules/smugmug/loader.gif) no-repeat center center; margin: 10px auto; border-top: 1px solid #D5D5D5; border-bottom: 1px solid #D5D5D5; padding:40px 0; display:none; height: 100px; width: 100%; }";
			$css .= "\n \t" . ".smugmug-gallery-outer-wrapper a { border: none; }";
			$css .= "\n \t" . ".smugmug-gallery-outer-wrapper h2 { clear: both; }";
			$css .= "</style>";
			$css .= "\n \t" . "<!-- END Photographer Connections SmugMug CSS -->";
			$css .= "\n \n ";
			echo $css;
		}
		
	}

	/**
	 * Add javascript to front-end footer
	 *
	 */		
	function blogsite_connect_add_smugmug_js() {
	?>
		<script type="text/javascript">
		/* <![CDATA[ */
		jQuery(document).ready(function($){
			$('.smugmug-gallery-outer-wrapper .smugmug-gallery-loader').fadeIn(100);
	    
			$(window).load(function(){
	    		$('.smugmug-gallery-outer-wrapper .smugmug-gallery-loader').fadeOut(800);
				$('.smugmug-gallery-outer-wrapper .smugmug-gallery-wrapper').fadeIn(1500);
			});
		});
		/* ]]> */
		</script>
	<?php 
	}
	
	/**
	 * Gets all albums
	 *
	 * @return array	
	 */		
	function blogsite_connect_get_smugmug_albums() { 
		$smug = blogsite_connect_smugmug_connection();
		if ( is_object( $smug ) ) {
			try {
				$albums = $smug->albums_get();
			}	
			catch( Exception $e ) {
				echo "{$e->getMessage()} (Error Code: {$e->getCode()}) - blogsite_connect_get_smugmug_albums";
		}
		} else {
			$albums = array();
		}
		return $albums;
	};

	/**
	 * Gets all images from an album
	 *
	 * @return array	
	 */		
	function blogsite_connect_get_smugmug_images($album) {
		$smug = blogsite_connect_smugmug_connection();
		if ( is_object( $smug ) ) {
			try {
				$images = $smug->images_get( 'AlbumID=' . $album['id'], 'AlbumKey=' .$album['Key'], "Heavy=1" );
				$images = ($smug->APIVer == "1.2.2") ? $images['Images'] : $images;
			}
			catch( Exception $e ) {
				echo "{$e->getMessage()} (Error Code: {$album['id']}) - blogsite_connect_get_smugmug_images";
	 	 	}	
		} else { 
			$images = array();
		};
		return $images; 
	}
	
	/**
	 * Gets thumbnail from an album
	 *
	 * @return string		
	 */		
	function blogsite_connect_get_smugmug_thumb($album) {
		$smug = blogsite_connect_smugmug_connection();
		if ( is_object( $smug ) ) {
			try {	
				$images = $smug->images_get( 'AlbumID=' . $album['id'], 'AlbumKey=' .$album['Key'], "Heavy=1" );
				$image = ($smug->APIVer == "1.2.2") ? $images['Images'][0]['ThumbURL'] : $images[0]['ThumbURL'];
				return $image; 
			}
			catch( Exception $e ) {
				echo "{$e->getMessage()} (Error Code: {$e->getCode()}) - blogsite_connect_get_smugmug_thumb";
	 		}	
		}
	}
	
	/**
	 * Preview of galleries in admin settings page
	 *
	 */	
	function blogsite_connect_smugmug_preview() {
	
		$blogsite_smugmug_settings = get_option('blogsite_smugmug_settings');
		
		$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 0;
		if ( empty( $pagenum ) )
			$pagenum = 1;

		//$cats = blogsite_connect_get_smugmug_cats();


	/* testing cache
	
		if ( $blogsite_smugmug_settings['username'] != '' && $blogsite_smugmug_settings['password'] != '' ) {
			$cache_check = get_option('blogsite_connect_smugmug_albums_cache_timestamp');
			$albums = get_option('blogsite_connect_smugmug_albums_cache');
		} else {
			$cache_check = '';
			$albums = array();	
		}
		
		if ( $blogsite_smugmug_settings['username'] != $blogsite_smugmug_settings['username_prev'] || $blogsite_smugmug_settings['password'] != $blogsite_smugmug_settings['password_prev'] ) {
			$cache_check = time();
		}
	
		if ( $cache_check == '' )
			$test['cache_check'] = $cache_check;
		
		if ( !is_array( $albums ) )
			$test['albums'] = 'no';

		if ( $blogsite_smugmug_settings['clear_cache'] >= $cache_check )
			$test['clear_cache'] = $blogsite_smugmug_settings['clear_cache'] . ' check: '. $cache_check;
	*/
			
	//	if ( $cache_check == '' || empty( $albums ) || $blogsite_smugmug_settings['clear_cache'] >= $cache_check ) {
	
			$albums = blogsite_connect_get_smugmug_albums();
	
	//		update_option('blogsite_connect_smugmug_albums_cache',$albums);
	//		update_option('blogsite_connect_smugmug_albums_cache_timestamp',time());
	
	//	} else {
	//		$albums = get_option('blogsite_connect_smugmug_albums_cache');
	// 		$test['using_cache'] = 'true';
	//	};

	//	print_r($test);

		if ( is_array( $albums ) ) {
	
			foreach ( $albums as $key => $row ) {
	    		$title[$key]  = $row['Title'];
			}
		
			if ( is_array( $albums ) && is_array( $title ) )
				array_multisort( $title, SORT_ASC, $albums );
	
			$per_page = 10;
			$pages =  intval( count( $albums ) / $per_page );
			$count_albums = count( $albums );
	
			if ( $count_albums > 0 ) {
		
			$page_links = paginate_links( array(
				'base' => add_query_arg( 'pagenum', '%#%' ),
				'format' => '',	
				'prev_text' => __('&laquo;'),
				'next_text' => __('&raquo;'),
				'total' => $pages,
				'current' => $pagenum,	
				'end_size' => 2,
				'mid_size' => 2,	
			));
	
		};
		
		echo '<div class="pane">';
		echo '<h3>Instructions</h3>';
	
		echo '<p>To automatically show all albums in any page or post, copy and paste this shortcode:<br />[smugmug_albums]</p>';
		echo '<p>To automatically show a specific album in any page or post, include the album name in the shortcode:<br />[smugmug_albums album="Album Name"]</p>';
		
		$page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
							number_format_i18n( ( $pagenum - 1 ) * $per_page + 1 ),
							number_format_i18n( min( $pagenum * $per_page, $count_albums ) ),
							number_format_i18n( $count_albums ),
							$page_links
							);
	
			echo '<div class="pane">';
	
			if ( $page_links ) {
				echo '<h3>Currently Available Albums - last cached: '.date('M d, Y',$blogsite_smugmug_settings['clear_cache']).'</h3>';
				echo '<p>Added Albums that you are not seeing here? Try resetting the cache with the settings above</p>';
				echo '<div class="tablenav">';
				echo "<div class='tablenav-pages'>$page_links_text</div>";	
				echo '</div>';
			};
			
			for ( $count = ( $pagenum - 1 ) * $per_page ; $count <= ( ($pagenum - 1) * $per_page ) + ( $per_page -1 ); $count ++ ) {
				if ( !empty( $albums[$count] ) ) {
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
			}
	
		echo '</div>';
		};
	}

	/**
	 * Shortcode
	 *
	 */	
	function blogsite_connect_smugmug_albums($atts='') {
		ob_start();
		global $post;
		extract(shortcode_atts(array(
			'album' => '',
			'image_size' => '',
			'title' => 'h2',
			'show_title' => 'true',
	    ), $atts));
	
		$album_name = $album;	 
		$html_final = '<ul class="smugmug-gallery-outer-wrapper">';
		$html_final .= '<li class="smugmug-gallery-loader"></li>';
		
		$albums = blogsite_connect_get_smugmug_albums();
		
		if ( $album_name == '' && is_array( $albums ) && !empty ( $albums ) ) {
			foreach ($albums as $album) {
				$html_final .= '<li class="smugmug-gallery-wrapper">';
	
				if ( $show_title != 'false' ) 
					$html_final .= '<'.$title.'>' . $album['Title'] . '</'.$title.'>';
				
				$images = blogsite_connect_get_smugmug_images($album);
				foreach ($images as $image) {
					$html_final .= '<a rel="shadowbox" href="'.$image['X2LargeURL'].'"><img class="thumbnails" src="'.$image['ThumbURL'].'" title="'.$image['Caption'].'" alt="'.$image['id'].'" /></a>';
				}
				$html_final .= '</li>';
	
			}
		} else {
			if ( is_array( $albums ) && !empty ( $albums ) ) {
				foreach ($albums as $album) {
					if ($album['Title'] == html_entity_decode($album_name)) {
						$html_final .= '<li class="smugmug-gallery-wrapper">';
				
						if ( $show_title != 'false' ) 
							$html_final .= '<'.$title.'>' . $album['Title'] . '</'.$title.'>';	
				
						$images = blogsite_connect_get_smugmug_images($album);
						foreach ($images as $image) {
							$html_final .= '<a rel="shadowbox[album-'.$post->ID.';player=img;]" href="'.$image['X2LargeURL'].'"><img class="thumbnails" src="'.$image['ThumbURL'].'" title="'.$image['Caption'].'" alt="'.$image['id'].'" /></a>';
						}
						$html_final .= '</li>';
					};
				};
			};	
		};
		$html_final .= '</ul>';
	
		echo $html_final; 
			
		$output_string = ob_get_contents();;
		ob_end_clean();
		
		return $output_string;
	}

?>