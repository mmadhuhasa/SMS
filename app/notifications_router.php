<?php 
// Psr-7 Request and Response interfaces
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

	
	/******** VIEW NOTIFICATIONS *********/
$app->get('/notifications', function ($request,  $response, $args)   {
    if (!checkSession()) {
		$uri = $request->getUri()->withPath($this->router->pathFor('login')); 
        return $response->withRedirect((string)$uri);
	}
    require_once("dbmodels/notification.crud.php");
    require_once("dbmodels/user.crud.php");
    $userCRUD = new UserCRUD(getConnection());
    $notificationCRUD = new NotificationCRUD(getConnection());
    /********** SERVER SESSION CHECK  ***********/
    if(isset($_SESSION["userID"]) && isset($_SESSION["email"]) && isset($_SESSION["api_key"])){
        $thisUser = $userCRUD->getUserByAPIKey($_SESSION["api_key"]);
    }else{
        $uri = $request->getUri()->withPath($this->router->pathFor('login')); 
        return $response->withRedirect((string)$uri);
    }
	 /********** SERVER SESSION CHECK  ***********/
    $data = $notificationCRUD->getNotificationsFor($_SESSION["userID"]);
    $numAllNoti = 0;
    $numUnreadNoti = 0;
    
    $numAllNoti = $notificationCRUD->getNumAllNotisFor($_SESSION["userID"]);
    $numUnreadNoti = $notificationCRUD->getNumUnreadNotifications($_SESSION["userID"]);
    //Do proper session management in helper
    $custom_data = array();
	if (count($data) > 0) {
	foreach ($data as $row) {
			   $tmp = getNotificationResponse($row["id"]);
            // $tmp["id"] = $row["id"];
            // $tmp["title"] = $row["title"];
            // $tmp["message"] = $row["message"];
            // $tmp["status"] = $row["status"];
            // $tmp["sender_image"] = $userCRUD->getUserImageByID($row["sender_id"]);
            // $tmp["date_created"] = $utilCRUD->getFormalDate($row["date_created"]);
            // $tmp["action_link"] = $notificationCRUD->getActionLink($row["data_id"], $row["data_title"]);
			array_push($custom_data, $tmp);
			   }
	}
	
		$vars = [
			'page' => [
			'name' =>'notifications',
			'page_title' => 'My Notifications',
			'description' => '',
			'data' => $custom_data,
			'numAllNoti' => $numAllNoti,
			'numUnreadNoti' => $numUnreadNoti
			],
		];
	
		return $this->view->render($response, 'admin_notifications.twig', $vars);
	})->setName('notifications');


	/******** VIEW NOTIFICATIONS *********/
    $app->get('/contact-form-submissions', function ($request,  $response, $args)   {
    if (!checkSession()) {
		$uri = $request->getUri()->withPath($this->router->pathFor('login')); 
        return $response->withRedirect((string)$uri);
	}
    require_once("dbmodels/contacts.crud.php");
    require_once("dbmodels/user.crud.php");
    require_once("dbmodels/utils.crud.php");
    $utilCRUD = new UtilCRUD(getConnection());
    $userCRUD = new UserCRUD(getConnection());
    $contactsCRUD = new ContactsCRUD(getConnection());
    /********** SERVER SESSION CHECK  ***********/
    if(isset($_SESSION["userID"]) && isset($_SESSION["email"]) && isset($_SESSION["api_key"])){
        $thisUser = $userCRUD->getUserByAPIKey($_SESSION["api_key"]);
        if ($thisUser != null && $thisUser["id"] == 1 && $thisUser["role_id"] == 1 ) {
	 }else{
		$uri = $request->getUri()->withPath($this->router->pathFor('unauthorized'));
        return $response->withRedirect((string)$uri);
	 }
    }else{
        $uri = $request->getUri()->withPath($this->router->pathFor('login')); 
        return $response->withRedirect((string)$uri);
    }
	 /********** SERVER SESSION CHECK  ***********/
    $data = $contactsCRUD->getAllMessages();
    $custom_data = array();
	if (count($data) > 0) {
	foreach ($data as $row) {
			   $tmp = getNotificationResponse($row["id"]);
             $tmp["id"] = $row["id"];
             $tmp["name"] = $row["name"];
             $tmp["message"] = $row["message"];
             $tmp["subject"] = $row["subject"];
             $tmp["email"] = $row["email"];
             $tmp["date_created"] = $utilCRUD->getFormalDate($row["date_created"]);
			array_push($custom_data, $tmp);
			   }
	}
	
		$vars = [
			'page' => [
			'name' =>'contact',
			'page_title' => 'Contact Form Submissions',
			'description' => '',
			'data' => $custom_data
			],
		];
	
		return $this->view->render($response, 'admin_contact_submissions.twig', $vars);
	})->setName('contact-form-submissions');
	
?>