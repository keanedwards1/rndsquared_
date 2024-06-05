<?php

error_reporting(E_ERROR | E_PARSE);



define('SITEROOT', '../../../');



require_once(SITEROOT . 'wp-config.php');

require_once(SITEROOT . 'wp-load.php');

require_once(SITEROOT . 'wp-includes/wp-db.php');



// Initiate

do_service(); 





function xmlWrap($nodes) { 

	header("Content-type: text/xml");

	$xml = "<?xml version='1.0' encoding='utf-8'?>\r";

	$xml .= "<data>\r";

	$xml .= $nodes;

	$xml .= "</data>";

	return $xml;

}



function nodeWrap($node) { 
	return "\t" . $node . "\r"; 
}

function parse($str) { 
	$str = rawurlencode($str);
	$str = str_replace('%5C', '', $str);
	return $str;
}

function do_service() {

	include('services.php'); 



	/**

	 * API KEY CHECK	

	 */



	global $wpdb;

	$sql = $wpdb->prepare("SELECT option_value from ".$wpdb->options." WHERE option_name = 'flash_api_key'");

	$apiKey = $wpdb->get_var($sql);

	$service = $_REQUEST['service'];



	if ($_REQUEST['apiKey'] != $apiKey) { 

		echo xmlWrap('<node error="true" param="apiKey" msg="INVALID API KEY" />'); 

		return; 

	}

	

	

	/**

	 * FUNCTION EXECUTION 

	 */

	else { 

		$func = $_REQUEST['service'];

		if (function_exists($func)) { echo $func(); }

		else { echo xmlWrap('<node error="true" param="service" msg="INVALID SERVICE" />'); }

	}

}







?>