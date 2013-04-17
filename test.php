<?php
$to      = 'helena@astbb.com';

$headers = 'From: webmaster@flu-trackr.com' . "\r\n" . 'Reply-To: webmaster@flu-trackr.com';
    $message = "Hello moo,\n\nThank you for signing up for Flu-Trackr's flu information alerts and being a Flu Hero! We'll \
keep you informed on vaccinations availability in your area as well as any outbreak information.\n\nThe Flu-Trackr Team\n\n
If you would like to unsubscribe to flu alerts, click <a href='http://flu-trackr.com/ajax.php?action=unsubscribe&id=sssy'>here</a>.";

    mail($to, 'Flu Alerts from Flu-Trackr Sign Up',$message, $headers);	


?>
