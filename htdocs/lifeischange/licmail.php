<?php


if( isset( $_POST['weJHlkUY'] ) ){
	
	// kick non empty honeypot
	if( isset( $_POST['email'] ) && !empty( $_POST['email'] ) ){
		echo json_encode(
			array(
				'result' => 'error',
				'details' => array( 'unknown' )
			)
		);
		die;
	}
	
	$message = '';
	$errors = '';
	
	if( isset( $_POST['name'] ) && !empty( $_POST['name'] ) ){
		$message .= 'name: ' . $_POST['name'] . "\r\n";
	} else {
		// error
		$errors[] = 'Name is empty';
	}
	
	if( isset( $_POST['weJHlkUY'] ) && !empty( $_POST['weJHlkUY'] ) ){
		$message .= 'email: ' . $_POST['weJHlkUY'] . "\r\n";
	} else {
		// error
		$errors[] = 'Email is empty';
	}
	
	if( isset( $_POST['phone'] ) && !empty( $_POST['phone'] ) ){
		$message .= 'phone: ' . $_POST['phone'] . "\r\n";
	}
	
	if( filter_var( $_POST['weJHlkUY'], FILTER_VALIDATE_EMAIL ) === false ) {
		$errors[] = 'Email address failed to validate';
	}
	
	if( isset( $_POST['inquirytype'] ) && !empty( $_POST['inquirytype'] ) ){
		$message .= 'type: ' . $_POST['inquirytype'] . "\r\n";
	}
	
	if( isset( $_POST['message'] ) && !empty( $_POST['message'] ) ){
		$message .= 'message: ' . $_POST['message'] . "\r\n";
	} else {
		// error
		$errors[] = 'Message is empty';
	}
	
	if( !empty( $errors )){
		echo json_encode(
			array(
				'result' => 'error',
				'details' => $errors
			)
		);
		die;
	}
	
	//helpnavigate@lifeischange.xyz
	
	$to      = 'helpnavigate@lifeischange.xyz';
	$subject = 'A message from the RND Contact Form';
	//$message = 'name: ' . $_POST['name'] . "\n" . 'email: ' . $_POST['weJHlkUY'] . "\n" . 'phone: ' . $_POST['phone']. "\n" . 'message: ' . $_POST['message'];
	
	
	
	$headers = 'From: webmaster@lifeischange.xyz' . "\r\n" .
	    'Reply-To: ' . $_POST['weJHlkUY'] . "\r\n" .
	    'X-Mailer: PHP/' . phpversion();

	if( mail( $to, $subject, $message, $headers ) ){
		echo json_encode(
			array(
				'result' => 'success'
			)
		);
		die;
	}
	
	echo json_encode(
		array(
			'result' => 'error',
			'details' => array( 'mail failed to send' )
		)
	);
	die;
	
}

echo json_encode(
	array(
		'result' => 'error',
		'details' => array( 'no data' )
	)
);