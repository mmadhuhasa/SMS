<?php 
function sendNotify($sender_id, $receiver_id, $title, $message, $data_id, $data_title){
require_once("dbmodels/notification.crud.php");
$notiCRUD = new NotificationCRUD(getConnection());
$response = array();
$response["test"] = "";
$status="Pending";
$date_created = date('Y-m-d H:i:s');
try{
$noti_res = $notiCRUD->create($sender_id, $receiver_id, $title, $message, $data_id, $data_title, $status, $date_created);
if ($noti_res["code"] == INSERT_SUCCESS) {
$response["error"] = false;
$response["message"] = "Notification Sent.";
}else{
	 $response["error"] = true;
	 $response["message"] = "Failed to send notification.";
}
}catch (Exception $e) {
       $response["error"] = true;
       $response["test"] = "NotiException: ".$e->getMessage();
	   $response["message"] = "Oops! Error send notification. ". $response["test"];
       return $response;
       }
	   return $response;
}
/*
function getNotificationResponse($id) {
	  require_once("dbmodels/notification.crud.php");
	  require_once("dbmodels/user.crud.php");
	  require_once("dbmodels/utils.crud.php");
	  $utilCRUD = new UtilCRUD(getConnection());
	  $userCRUD = new UserCRUD(getConnection());
	  $notiCRUD = new NotificationCRUD(getConnection());
	  $row = $notiCRUD->getID($id);
	  $tmp = array();
	  if($row != null && count($row) > 0){
		       $tmp["id"] = $row["id"];
               $tmp["title"] = $row["title"];
               $tmp["message"] = $row["message"];
			   $tmp["status"] = $row["status"];
			   $tmp["data_id"] = $row["data_id"];
			   $tmp["data_title"] = $row["data_title"];
			   $tmp["date_created"] = $utilCRUD->getFormattedDate($row["date_created"]);
			   
			   $tmp["sender_id"] = $row["sender_id"];
			   $tmp["sender_name"] = $userCRUD->getNameByID($row["sender_id"]);
			   $tmp["sender_qcode"] = $userCRUD->getUserName($row["sender_id"]);
			   $tmp["link"] = "";
			   switch($tmp["data_title"]){
				   case "User":
				   $tmp["link"] = "people/details/";
				   if(!empty($tmp["data_id"])){ $tmp["link"] .= $tmp["data_id"];}
				   break;
				   
				   case "Company":
				   $tmp["link"] = "companies/details/";
				   if(!empty($tmp["data_id"])){ $tmp["link"] .= $tmp["data_id"];}
				   break;
				   
				   case "Property":
				   $tmp["link"] = "property-details/";
				   if(!empty($tmp["data_id"])){ $tmp["link"] .= $tmp["data_id"];}
				   break;
				   
				   case "Endorsement":
				   $tmp["link"] = "skill-set/";
				   if(!empty($tmp["data_id"])){ $tmp["link"] .= $tmp["data_id"];}
				   break;
				   
				   case "ServiceComment":
				   $tmp["link"] = "service-inquiries/";
				   break;
				   
				   case "PropertyComment":
				   $tmp["link"] = "property-inquiries/";
				   break;
				   
				   case "Message":
				   $tmp["link"] = "messages/";
				   break;
				   
				   default:
				   $tmp["link"] = "";
			   }
			   return $tmp;
	  }
    return null;
}*/
?>