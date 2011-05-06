<?php

/**
 * 	ShootQ module for Photographer Connections
 *
 *	Adds short code [shootq_form], embedding form in page from ShootQ
 *	Adds functionality to Contact Form 7, allowing form data to be sent to ShootQ
 * 
 */

	add_action ('blogsite_connect_extra',				'blogsite_connect_shootq_create_menu');
	add_filter ('blogsite_connect_extra_settings',		'blogsite_connect_shootq_settings',1,1);

	add_action( 'admin_init', 							'blogsite_connect_register_shootq_settings' );
	add_action( 'wp_head', 								'blogsite_connect_add_shootq_functions' );
	add_action ('wpcf7_before_send_mail', 				'blogsite_shootq_send');	

	add_shortcode('shootq_form', 							'blogsite_add_shootq');

function blogsite_connect_add_shootq_functions() {
	$blogsite_connect_settings = get_option('blogsite_connect_settings');	
	if 	($blogsite_connect_settings['shootq'] == 'yes' ){
		// CONTACT FORM 7
		// PRO PHOTO BLOG
		add_action( 'p3_contact_pre_email', 	'blogsite_shootq_send' );
		// SENDS FROM PRO PHOTO FORM?
		add_filter('the_content', 				'blogsite_shootq_send_prophoto', 30);
	};
}
	
function blogsite_connect_register_shootq_settings() {
	register_setting( 'blogsite_shootq_settings_group', 'blogsite_shootq_settings');
	
	// KEEP OLD SETTINGS...
	$old_apiKey = get_option('shootq-api-key');
	$old_brand = get_option('shootq-brand-abbreviation');
	
	$blogsite_shootq_settings = get_option('blogsite_shootq_settings'); 
	$apiKey = $blogsite_shootq_settings['api_key'];
	$brand = $blogsite_shootq_settings['brand'];	
		
	if 	($blogsite_shootq_settings['api_key'] == '' && $old_apiKey != '') {
		$blogsite_shootq_settings['api_key'] = $old_apiKey;
	}
	if 	($blogsite_shootq_settings['brand'] == '' && $old_brand != '') {
		$blogsite_shootq_settings['brand'] = $old_brand;
	}
	update_option('blogsite_shootq_settings',$blogsite_shootq_settings);
}

function blogsite_connect_shootq_settings($connect_settings){
	$connect_settings['shootq']['name'] = 'ShootQ';
	$connect_settings['shootq']['setting'] = 'shootq';
	$connect_settings['shootq']['page'] = 'blogsite_shootq_settings';
	return $connect_settings;
}

function blogsite_connect_shootq_create_menu() {

	$blogsite_connect_settings = get_option('blogsite_connect_settings');	
	if 	($blogsite_connect_settings['shootq'] == 'yes' ){
		add_submenu_page( 'blogsite_photo_connect', 'ShootQ Settings', 'ShootQ', 'administrator', 'blogsite_shootq_settings' , 'blogsite_shootq_settings_page');
	};
}


function blogsite_shootq_send($contactForm) {
	$post = $contactForm->posted_data;
	$blogsite_shootq_settings = get_option('blogsite_shootq_settings');	
	$apiKey = $blogsite_shootq_settings['api_key'];
	$brand = $blogsite_shootq_settings['brand'];
	
	if(!$apiKey || !$brand) return;
	
	$url = "https://app.shootq.com/api/{$brand}/leads";
	$data = array();
	$data['api_key'] = $apiKey;
	
	// CONTACT INFORMATION ******************************************************
	
	$contact = array();
	
	// NAME
	
	if($name = $post['name']) {
		$name = explode(" ", $name);
		$contact['first_name'] = $name[0];
		$contact['last_name'] = $name[1];
	} else {
		if ( $post['first_name'] != '' ) {
			$contact['first_name'] = $post['first_name'];
		};
		if ( $post['last_name'] != '' ) {
			$contact['last_name'] = $post['last_name'];
		};
	};
	
	
	$contact["phones"] = array(array( "number" => $post['phonenumber']));
	
	if ( $post['email'] != '' && eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $post['email'] ) ) {
		$contact["emails"] = array(array( "email" => $post['email']));
	};
	
	$data["contact"] = $contact;
	
	// EVENT INFORMATION *********************************************************

	// TYPE
	
	if ( $post['type'] != '' ) {	
		$type = $post['type'];
	} else {
		$type = 'Job';
	};

	// DATE 
	
	$date = $post['date'];
	$timestamp = strtotime($date);
	$date = date('m/d/Y',$timestamp);
	
	// REMARKS
	$remarks = '';
	if ($post['subject'] != '') {
		$remarks .= $post['subject']."\n\r\n\r";
	};
	if ($post['message'] != '') {
		$remarks .= $post['message']."\n\r\n\r";
	};
	if ($post['remarks'] != '') {
		$remarks .= $post['remarks'];
	};

	$data['event'] = array(
		'type' => $type ? $type : "Job",
		'date' => $date,
		'remarks' => $remarks,
		'referred_by' => $post['referred_by'],
		'referrer_id' => $post['referrer_id'],
	);
	
	// WEDDING INFO 
	if ($type == 'Wedding' ) {
		$wedding = array (
			'ceremony_location' 	=> $post['ceremony_location'],
			'ceremony_start_time' 	=> $post['ceremony_start_time'], // format '18:30'
			'ceremony_end_time'		=> $post['ceremony_end_time'],
			'reception_location' 	=> $post['reception_location'],
			'reception_start_time' 	=> $post['reception_start_time'], // format '18:30'
			'reception_end_time' 	=> $post['reception_end_time'],
			'groomsmen_count' 		=> $post['groomsmen_count'],
			'bridesmaids_count' 	=> $post['bridesmaids_count'],
			'guests_count'			=> $post['guests_count'],		
		);
		$data['event']['wedding'] = $wedding;
	};
	
	// PORTRAIT INFO
	if ($type == 'Portrait') {
		$portrait = array (
			'classifier'			=> $post['classifier'],
			'group_size' 			=> $post['group_size'],
		);
		$data['event']['portrait'] = $portrait;
	};

	// EXTRA ********************************************************************
	
	// can be any number of extras in an array, 'name' => 'value';
	// not sure what this does?
	// it doesn't show up anywhere?
	
	foreach ($post as $key => $value) {
		$shootq_test = explode("_", $key);
		if ($shootq_test[0] == 'shootq') {
			$extra[$shootq_test[1]] = $value;
		};
		if (is_array($data['extra'])) {
			$data['extra'] = $extra;
		}
	};
	
	// Error test
	// update_option ('last_form_check',$data);
		
	$lead_json = json_encode($data);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: application/json"));
	curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $lead_json);
	$response_json = curl_exec($ch);
	$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	$response = json_decode($response_json);
	if (curl_errno($ch) == 0 && $httpcode == 200) {
	} else {
		error_log( get_bloginfo('url')." had a ShootQ Error: ".curl_errno($ch).": $httpcode $response_json");
	}
	curl_close($ch);
}	


// Pro Photo
function blogsite_shootq_send_prophoto($content){
	if( isset($_POST['email']) ){  
		$blogsite_shootq_settings = get_option('blogsite_shootq_settings'); 
		$apiKey = $blogsite_shootq_settings['api_key'];
		$brand = $blogsite_shootq_settings['brand'];
		if(!$apiKey || !$brand) return;
			
		$url = "https://app.shootq.com/api/".$brand."/leads";
		$sq_type = ucwords(strtolower($_POST['custom-field1']));
		if(!$sq_type){$sq_type = "Job";}
		$sq_first = $_POST['firstname']; 
		if(!$sq_first){
			if(isset($_POST['lastname'])){
				$names = explode(" ", $_POST['lastname']);
				$sq_first = $names[0];
				$sq_last = $names[1];
				if($names[2]){
	    			$sq_last .= $names[2];
				}
			}else{
			$sq_first = "John";
			$sq_last = "Doe";
			}
		}else{
			$sq_last = $_POST['lastname']; 
		}

		$sq_phone = $_POST['phone'];
		$sq_email = $_POST['email'];
		$sq_date = $_POST['custom-field2'];
		if(strpos($sq_date, '/') === false){
			$sq_date = date('m/d/Y');
		}

		$sq_referred = $_POST['custom-field3'];
		$sq_comments = $_POST['message'];
		

		if($fbs['shootq_debug']){ // DEBUG Show Post Array
  			echo '<p>--- SHOOTQ DEBUG -----------------------------<br />(Turn this off in the wordpress admin under <strong>Settings > ShootQ</strong>)<br /><br /><strong>POST Array:</strong><br />';
  			print_r($_POST);
			echo $sq_date;
			echo $sq_type;
			echo $sq_first;
			echo $sq_last;
  			echo "<br /><br /><strong>Type of submission</strong>: ".$sq_type."<br /><br /></p>";
		}
        if($api_key != "" && $sq_first != "" && $sq_email != ""){
		$ctext = $text;
		$text .= "<p>";
		$lead = array();
		$lead['api_key'] = $api_key;
		$lead['contact'] = array();
		$lead['contact']['first_name'] = $sq_first;
		$lead['contact']['last_name'] = $sq_last;
		$lead['contact']['phones'] = array();
		$lead['contact']['phones'][0] = array();
		$lead['contact']['phones'][0]['type'] = 'Home';
		$lead['contact']['phones'][0]['number'] = $sq_phone;
		$lead['contact']['emails'] = array();
		$lead['contact']['emails'][0] = array();
		$lead['contact']['emails'][0]['type'] = 'Home';
		$lead['contact']['emails'][0]['email'] = $sq_email;
		if(isset($_POST['shootq_field_custom_1'])){
			$lead['contact']['role'] = $_POST['shootq_field_custom_1'];
		}
		$lead['event'] = array();
		$lead['event']['type'] = $sq_type;
		$lead['event']['date'] = $sq_date;
		$lead['event']['referred_by'] = $sq_referred;
		$lead['event']['remarks'] = $sq_comments;
		$lead['event']['extra'] = array();
		foreach ($_POST as $key => $val) {
			if($fbs['shootq_debug']){ // DEBUG Show Post Array
				echo "Key $key, Value $val<br />";
			}
			$cff = substr($key, 0, 3);
			$cff2 = substr($key, 0, 10);
			if($cff != "cf_" && $cff2 != "sendbutton" && $key != "shootq" && $key != "first" && $key != "last" && $key != "firstname" && $key != "lastname" && $key != "name" && $key != "date" && $key != "comments" && $key != "phone"  && $key != "type"  && $key != "email" && $key != "submit"  && $key != "_wpnonce_p3" && $key != "anti-spam" && $key != "spam_question"/* && $key != "referpage"*/){
				$lead['event']['extra'][$key] = $val;
			}
		} 

		$lead_json = json_encode($lead);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: application/json"));
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $lead_json);
		$response_json = curl_exec($ch);
		$response = json_decode($response_json);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($httpcode == 200) {
   			if($fbs['shootq_debug']){ // DEBUG Show Output
   				$text .= "<strong>HTTPCode</strong>: ".$httpcode."<br />";
   				$text .= "<strong>JSON Response:</strong>".$response_json;
   				$text .= "<br /><br /> --- END DEBUG -----------------------------<br /><br /><br /><br /><br />";
   			}
			$text .= $fbs['shootq_success'];
		} else {
  			if($fsq['shootq_debug']){ // DEBUG Show Output
   				$text .= "There was a problem: ".$httpcode."\n\n";
   				$text .= $response_json;
   				$text .= "<br />No Data<br /><br /> --- END DEBUG -----------------------------<br /><br /><br /><br /><br />";
   			}
   			$text .= $fbs['shootq_fail'];
		}
		$text .= "</p>";
		if(!$fbs['shootq_location']){
			$text .= $ctext;
		}
		curl_close($ch);
		} 
	} 
	return $content;
}

// Short Code
function blogsite_add_shootq($atts) {
	
	$blogsite_shootq_settings = get_option('blogsite_shootq_settings');
	//print_r($shootq_vars);
    extract(shortcode_atts(array(
			'width' => $blogsite_shootq_settings['width'],
            'height' => $blogsite_shootq_settings['height'],
			'bg_color' => $blogsite_shootq_settings['bg_color'],
			'logo' => 'false',			
	), $atts));
	
	$shootqurl = 'https://app.shootq.com/public/'.$blogsite_shootq_settings['brand'].'/contact';
	if($url){ 
		if(strpos($url, 'http') == false){
			$shootqurl = 'https://app.shootq.com/public/'.$url.'/contact';
		}else{
			$shootqurl = $url;
		}
	}
	if(!$width){ $width = "100%";}
	if(!$height){ $height = '120%';}
    if (strpos($width, 'px') == false && strpos($width, '%') == false) { 
		$width .= 'px'; 
	};
	if ( strpos($height, 'px') == false && strpos($height, '%') == false){ 
		$height .= 'px'; 
	};
	
	$border = $blogsite_shootq_settings['border'];
	
	if(!$border){ $border = "0";};
	if(!$scrolling){ $scrolling = "auto"; };
	if(!$bg_color){ $bg_color = "transparent"; };
	if ( $bg_color != 'transparent' && strpos($bg_color, '#') == false){ 
		$bg_color = '#'.$bg_color;
	}

	   
    return	'<iframe id="shootqform" src="' . $shootqurl . '" style="width:'.$width . '; height:'.$height.';background-color:'.$bg_color.'" frameborder="'.$border.'"></iframe>';
}

function blogsite_shootq_settings_page() {
?>
<div class="wrap">
	<h2>Shoot Q Integration Settings</h2>
	<form method="post" action="options.php">
    <?php settings_fields( 'blogsite_shootq_settings_group' ); ?>
	<?php $blogsite_shootq_settings = get_option('blogsite_shootq_settings'); ?>
		<div class="pane">
			<h3>Account Settings</h3>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">ShootQ API Key</th>
					<td>
						<input type="text" style="width:400px" name="blogsite_shootq_settings[api_key]" value="<?php echo $blogsite_shootq_settings['api_key']; ?>" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">ShootQ Brand Abbreviation</th>
					<td>
						<input type="text" style="width:400px" name="blogsite_shootq_settings[brand]" value="<?php echo $blogsite_shootq_settings['brand']; ?>" />
					</td>
				</tr>
			</table>
		</div>
		<div class="pane">
			<h3>Embedded Contact Form Settings</h3>
			<p>To use the embedded form, place the shortcode [shootq] in any post or page.</p>
			<table class="form-table">
				<caption></caption>
				<tbody>
					<tr>
						<th scope="row">Width</th>
						<td><input type="text" name="blogsite_shootq_settings[width]" size="10" value="<?php echo $blogsite_shootq_settings['width']; ?>" /></td>
					</tr>
					<tr>
						<th scope="row">Height</th>
						<td><input type="text" name="blogsite_shootq_settings[height]" size="10" value="<?php echo $blogsite_shootq_settings['height']; ?>" /></td>
					</tr>
					<tr>
						<th scope="row">Background Color</th>
						<td><input type="text" name="blogsite_shootq_settings[bg_color]" size="10" value="<?php echo $blogsite_shootq_settings['bg_color']; ?>" /> Background Color must be a HEX code, ex: black is #000000</td>
					</tr>
					<tr>
						<th scope="row">Border</th>
						<td>
							<input type="radio" name="blogsite_shootq_settings[border]" value="0" <?php if($blogsite_shootq_settings['border'] == "" || $shootq_vars['border'] == "0"){ echo 'checked'; }?> > Off
							<input type="radio" name="blogsite_shootq_settings[border]" value="1" <?php if($blogsite_shootq_settings['border'] == "1"){ echo 'checked'; }?> > On
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
		</p>
	</form>
</div>
<?php } 

	/* CHECK FOR EXISTING SHOOTQ API INFO
	
	// FROM CF7 PLUGIN
	$apiKey = get_option('api_key');
	$brand = get_option('brand');
	
	// FROM FLAUNT BOOKS PLUGIN...
	$fbs = get_option('fb_shootq');  // Get Settings
	$api_key = $fbs['shootq_api'];
	$brand_abbreviation = $fbs['shootq_brand'];
	
	*/
?>