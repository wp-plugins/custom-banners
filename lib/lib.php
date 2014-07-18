<?php
include("cbkg.php");

function isValidCBKey(){
	$email = get_option('custom_banners_registered_name');
	$webaddress = get_option('custom_banners_registered_url');
	$key = get_option('custom_banners_registered_key');
	
	$keygen = new CBKG();
	$computedKey = $keygen->computeKey($webaddress, $email);
	$computedKeyEJ = $keygen->computeKeyEJ($email);

	if ($key == $computedKey || $key == $computedKeyEJ) {
		return true;
	} else {
		$plugin = "custom-banners-pro/custom-banners-pro.php";
		
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		
		if(is_plugin_active($plugin)){
			return true;
		}
		else {
			return false;
		}
	}
}

function isValidMSCBKey(){
	$plugin = "custom-banners-pro/custom-banners-pro.php";
		
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	
	if(is_plugin_active($plugin)){
		return true;
	}
	else {
		return false;
	}
}
?>