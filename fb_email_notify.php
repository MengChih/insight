<?php
// to whom
phpinfo();
$to  = 'godhuen@gmail.com';
 
// subject
$subject = 'A new Facebook comment on Your Site';
 
// message
$message = '
<html>
<body>
  <h4>There is a new comment on the following article.</h4>
  </br>
  </hr>'.
  $_GET['path'].'
 
</body>
</html>
';
 
// To send HTML mail, the Content-type header must be set
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
 
// Additional headers
$headers .= 'From: Notifications Module <noreply@example.com>' . "\r\n";

echo 'should to send'; 
// Mail it

wp_mail( $to, $subject, $message);
?>
