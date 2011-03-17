<?php


function getPictagePage($url=""){
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

function shootq_curl($url='',$data='') {
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
		error_log("ShootQ Error: ".curl_errno($ch).": $httpcode $response_json");
	}
	curl_close($ch);
}

?>