<?php
	
require 'PHPMailerAutoload.php';
function mail_with_attachment($server, $username, $password, $port, $to, $files = array()){
	$debug_text += $server . '<br>';
	$debug_text += $username . '<br>';
	$debug_text += $password. '<br>';
	$debug_text += $port. '<br>';
	$debug_text += $to. '<br>';
	

	$mail = new PHPMailer;
	//$mail->SMTPDebug  = 3;
	$mail->isSMTP();
	$mail->Host = $server;
	$mail->SMTPAuth = true;
	$mail->Username = $username;
	$mail->Password = $password;
	$mail->SMTPSecure = 'tls';
	$mail->Port = $port;
	$mail->SMTPOptions = array(
		'ssl' => array(
			'verify_peer' => false,
			'verify_peer_name' => false,
			'allow_self_signed' => true
		)
	);
	//$mail -> SMTPOptions = [ 'ssl' => [ 'verify_peer' => false ] ];
	
	$mail->setFrom($username, 'web mailer');
	$mail->addAddress($to);
	
	foreach($files as $file){
		$mail->addAttachment($_SERVER['DOCUMENT_ROOT'] . $file);
	}
	
	$mail->isHTML(true);
	
	$mail->Subject = 'Server backup ' . $_SERVER['SERVER_NAME'];
	$mail->Body    = "Backup server " . $_SERVER['SERVER_NAME'] . ' with succes, here are your files.';
	
	if(!$mail->send()) {
	    return 'Mailer Error: ' . $mail->ErrorInfo ;
	} else {
	    return 'Message has been sent';
	}

}
	
?>