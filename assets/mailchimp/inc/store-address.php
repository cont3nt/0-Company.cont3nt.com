<?php
// // ----------------------------------------------------------------------------------------------------
// // - Display Errors
// // ----------------------------------------------------------------------------------------------------
// ini_set('display_errors', 'On');
// ini_set('html_errors', 0);

// // ----------------------------------------------------------------------------------------------------
// // - Error Reporting
// // ----------------------------------------------------------------------------------------------------
// error_reporting(-1);

/*///////////////////////////////////////////////////////////////////////
Part of the code from the book
Building Findable Websites: Web Standards, SEO, and Beyond
by Aarron Walter (aarron@buildingfindablewebsites.com)
http://buildingfindablewebsites.com

Distrbuted under Creative Commons license
http://creativecommons.org/licenses/by-sa/3.0/us/
///////////////////////////////////////////////////////////////////////*/


function storeAddress() {

	// Validation
	if (empty($_GET['email'])) {
		return "No email address provided";
	}

	if(!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*$/i", $_GET['email'])) {
		return "Email address is invalid";
	}

	// grab an API Key from http://admin.mailchimp.com/account/api/
	$api_key = 'dcbf1e06f360d2f02c3efcbe77a2f245-us4';

	// grab your List's Unique Id by going to http://admin.mailchimp.com/lists/
	// Click the "settings" link for the list - the Unique Id is at the bottom of that page.
	$list_id = "c302a489d0";

	require_once('MailChimp.php');

	$api = new MailChimp($api_key);

	$merge_fields = array(
		'FNAME' => !empty($_GET['fname']) ? $_GET['fname'] : '',
		'LNAME' => !empty($_GET['lname']) ? $_GET['lname'] : '',
		'PHONE' => !empty($_GET['phone']) ? $_GET['phone'] : '',
	);

	$result = $api->post("lists/{$list_id}/members", array(
		'email_address'     => $_GET['email'],
		'status'            => 'subscribed',
		'merge_fields'      => $merge_fields
	), 100);

	if (!empty($result['status']) && $result['status'] === 'subscribed') {
		return 'Success! Check your email to confirm sign up.';
	} else {
		$message = array();

		if( !empty($result['title']) ) {
			$message[] = $result['title'];
		}

		if( !empty($result['detail']) ) {
			$message[] = $result['detail'];
		}

		return 'Error: '.implode(' - ', $message);
	}
}

// If being called via ajax, autorun the function
if ( !empty($_GET['ajax']) ) {
	echo storeAddress();
}
?>
