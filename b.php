<?php 
function curl($url, $data = null, $headers = null) {
	$ch = curl_init();
	$options = array(
		CURLOPT_URL => $url,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_FOLLOWLOCATION => true,
	);
	if ($data != "") {
		$options[CURLOPT_POST] = true;
		$options[CURLOPT_POSTFIELDS] = $data;
	}
	if ($headers != "") {
		$options[CURLOPT_HTTPHEADER] = $headers;
	}

	curl_setopt_array($ch, $options);
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}
function number($length) {
    $characters = '0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
// Headers
$headers = array();
$headers[] = 'X-LIBRARY: okhttp+network-api';
$headers[] = 'Authorization: Basic dGhlc2FpbnRzYnY6ZGdDVnlhcXZCeGdN';
$headers[] = 'User-Agent: Booking.App/22.9 Android/7.1.2; Type: tablet; AppStore: google; Brand: OnePlus; Model: A5010;';
$headers[] = 'X-Booking-API-Version: 1';
$headers[] = 'Content-Encoding: gzip';
$headers[] = 'Content-Type: application/x-gzip; contains="application/json"; charset=utf-8';
$headers[] = 'Host: iphone-xml.booking.com';
$headers[] = 'Accept-Encoding: gzip, deflate';

$hotel = array('3326463', '4984319');
// URL
$str        = '1234'; 
$shufflestr = str_shuffle($str);
$regis_url = "https://iphone-xml.booking.com/json/mobile.createUserAccount?&user_os=7.1.2&user_version=22.9-android&device_id=aa7bf591-e6dd-419a-8a4f-29e762d89$shufflestr&network_type=wifi&languagecode=en-us&display=large_mdpi&affiliate_id=337862";
$wishlist_url = 'https://iphone-xml.booking.com/json/mobile.Wishlist?&user_os=7.1.2&user_version=22.9-android&device_id=aa7bf591-e6dd-419a-8a4f-29e762d8948a&network_type=wifi&languagecode=en-us&display=large_mdpi&affiliate_id=337862';


$g          = file_get_contents($argv[1]);
$f          = explode("\r\n", $g);
$f          = array_unique($f);
$count      = 0;
$hitung     = count($f);
foreach ($f as $data) {
    $pecah       = explode("|", $data);
    $email          = str_replace(' ', '', $pecah[0]);
    $password          = str_replace(' ', '', $pecah[1]);
// Register
$regis_data = json_encode(array(
	"email"				=> $email, 
	"password"			=> $password, 
	"return_auth_token" => 1));
$register = json_decode(curl($regis_url, $regis_data, $headers), true);
// Wishlist
$create_wishlist = json_encode(array(
	"wishlist_action" => "create_new_wishlist", 
	"name" => "Jakarta", "hotel_id" => "28250", 
	"auth_token" => $register['auth_token'])
);
$create = json_decode(curl($wishlist_url, $create_wishlist, $headers), true);
if($create['success'] == '1'){
	echo " \e[0;33m[$count/$hitung]\e[0m";
	echo "SUCCES CREATE => $email|$password".PHP_EOL;

	foreach ($hotel as $hotel_id) {
		$add_wishlist = json_encode(array(
			"wishlist_action" => "save_hotel_to_wishlists", 
			"new_states" => 1, 
			"list_ids" => $create['id'], 
			"hotel_id" => $hotel_id, 
			"list_dest_id" => "city%3A%3A-2679652", 
			"update_list_search_config" => 1, 
			"checkin" => "2020-07-27", 
			"checkout" => "2020-07-28", 
			"num_rooms" => 1, "num_adults" => 2, 
			"num_children" => 0, 
			"auth_token" => $register['auth_token']));
		$add = json_decode(curl($wishlist_url, $add_wishlist, $headers), true);
	}
	if($add['gta_add_three_items_campaign_status']['status'] != 'not_yet_reached_wishlist_threshold'){
		echo $add['gta_add_three_items_campaign_status']['modal_body_text'].PHP_EOL;
		echo "\n";
	}
}else{
	echo "Gagal membuat wishlist\n".PHP_EOL;
}
}
?>
