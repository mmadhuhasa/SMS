<?php
class Helper { 
function validateAdminSession(){
    if (isset($_SESSION['app']) && isset($_SESSION['first_name']) && isset($_SESSION['last_name']) && isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1) {
		 return true;
	}else{
		return false;
	}
}


function validateMemberSession(){
    if (isset($_SESSION['app']) && isset($_SESSION['first_name']) && isset($_SESSION['last_name']) && isset($_SESSION['role_id'])) {
		 return true;
	}else{
		return false;
	}
}

function sendEmail($to, $subject, $body){
require("php-mailer/PHPMailerAutoload.php");	
$name = "Smart School Automation";
$from = "support@smartschoolautomation.com"; 
$headers = array("From: $from",
    "Reply-To: $from",
    "X-Mailer: PHP/" . PHP_VERSION
);
$headers = implode("\r\n", $headers);
//mail($to,$subject,$body,$headers); 
$response = array();
try{
$mail = new PHPMailer();
$mail->IsHTML(true);
$mail->SetFrom($from);
$mail->From = $from;
$mail->FromName = $name;
$mail->AddAddress($to);            
$mail->IsHTML(true);
$mail->Subject = $subject;
$mail->Body    = $body;
$mail->AltBody = "This is an automated mail from ".$name.".";
$mail->addReplyTo($from, "Reply");

if($mail->Send())
{
   $response["error"] = false;
   $response["message"] = "Mail sent successfully.";
}else{
	$response["error"] = true;
	$response["message"] = "Mail failed to sent.";
	}
}catch (phpmailerException $e) {
       $response["error"] = true;
       //$response["message"] = "phpmailerException: ".$e->getMessage();
	   $response["message"] = "Oops! Failed to send mail. Please try again.";
       return $response;
       }
	   return $response;
    }

}
	?>