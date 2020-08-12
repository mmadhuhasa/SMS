<?php
	use Psr\Http\Message\ServerRequestInterface as Request;
	use Psr\Http\Message\ResponseInterface as Response;

/************************ START OF TOPIC VIEWS *********************/
	$app->get('/manage-topics', function (Request $request, Response $response, $args){
		require_once("dbmodels/topic.crud.php");
        $topicCRUD = new TopicCRUD(getConnection());
		require_once("dbmodels/user.crud.php");
    $userCRUD = new UserCRUD(getConnection());
	/********** SERVER SESSION CHECK  ***********/
	if(isset($_SESSION["userID"]) && isset($_SESSION["email"]) && isset($_SESSION["api_key"])){
    $thisUser = $userCRUD->getUserByAPIKey($_SESSION["api_key"]);
	if ($thisUser != null && ($thisUser["role_id"] == 1 || $thisUser["role_id"] == 2) ) {
	 }else{
		$uri = $request->getUri()->withPath($this->router->pathFor('unauthorized'));
        return $response->withRedirect((string)$uri);
	 }
	}else{
	   	$uri = $request->getUri()->withPath($this->router->pathFor('login'));
        return $response->withRedirect((string)$uri);
	}
	 /********** SERVER SESSION CHECK  ***********/
	 $adminMode = false;
	    $topics = $topicCRUD->getAllTopicsForSchool($thisUser["school_id"]);
	    if($thisUser["role_id"] == 1){
			$topics = $topicCRUD->getAllTopics();
			$adminMode = true;
		}
		$data = array();
	    if (count($topics) > 0) {
		foreach ($topics as $res) {
        $schoolProfile = getTopicDetails($res["id"]);
		array_push($data, $schoolProfile);
		}
	    }
		$vars = [
			'page' => [
			'name' => '',
			'title' => 'Manage Topics | Talank SMS',
			'description' => '',
			'data' => $data,
			'adminMode' => $adminMode
			]
		];
		return $this->view->render($response, 'list-topics.twig', $vars);
	})->setName('manage-topics');
/************************ END OF TOPIC VIEWS *********************/

/*************** CREATE TOPIC ROUTE *******************/
	$app->get('/create-new-topic', function (Request $request, Response $response, $args){
		require_once("dbmodels/class.crud.php");
        $classCRUD = new ClassCRUD(getConnection());
		require_once("dbmodels/subject.crud.php");
        $subjectCRUD = new SubjectCRUD(getConnection());
		
		$classes = $classCRUD->getAllClasses();
		$subjects = $subjectCRUD->getAllSubjects();
		$vars = [
			'page' => [
			'name' => '',
			'title' => 'Create New Topic',
			'description' => 'Talank SAS is a next generation school management and automation system. ',
			'classes' => $classes,
			'subjects' => $subjects,
			]
		];
		return $this->view->render($response, 'create-new-topic.twig', $vars);
	})->setName('create-new-topic');
	
	
	/*********** CREATE NEW TOPIC *************/
    $app->post('/apis/topics/create', function ($request, $respo, $args) use ($app) {
	require_once("dbmodels/topic.crud.php");
	$topicCRUD = new TopicCRUD(getConnection());
	require_once("dbmodels/class.crud.php");
	$classCRUD = new ClassCRUD(getConnection());
	require_once("dbmodels/subject.crud.php");
	$subjectCRUD = new SubjectCRUD(getConnection());
	
	$response = array();
    $response["error"] = true;
    $response["message"] = "";
	
	//Step 1: Take User Input
	$title = $request->getParam('title');
	$subject_id = $request->getParam('subject_id');
	$class_id = $request->getParam('class_id');
	$description = $request->getParam('description');
	$image = "";
	//Step 2: Sanitize User Input
	if(empty($title)){
		 $response['error'] = true;
         $response['message'] = 'You must enter topic name.';
         echoRespnse(200, $response);
		 return;
	}
	if(empty($subject_id)){
		$response['error'] = true;
		$response['message'] = 'You must select a subject.';
		echoRespnse(200, $response);
		return;
   }
   if(empty($class_id)){
	$response['error'] = true;
	$response['message'] = 'You must select a class.';
	echoRespnse(200, $response);
	return;
}
	
	/********* Validate Authorization **********/
	/********* Only Super Admin can create new school ********/
	$authUser = getCallerSnapshot($request, $respo);
	if(!$authUser["error"]){
		 if($authUser["caller_role"] == 1 || $authUser["caller_role"] == 2){ } else {
		 $response['error'] = true;
         $response['message'] = 'You are not authorized to create a topic';
         echoRespnse(200, $response);
		 return;
		 }
	}else{
		 $response['error'] = true;
         $response['message'] = 'Invalid request. Please use your authentication signature.';
         echoRespnse(200, $response);
		 return;
	}
	/********* Validated Authorization ********/

	$res = $topicCRUD->create($title, $subject_id, $class_id, $description,$image);
	if ($res["code"] == INSERT_SUCCESS) {
                $response["error"] = false;
                $response["message"] = $title." has been created successfully. ";
			   $response["id"] =  $res["id"];
			 }else{
				  $response["error"] = true;
				  $response["info"] = $res["message"] ;
                  $response["message"] = "Failed to add Topic. Please try again.";
			 }
	echoRespnse(200, $response);
	})->add($authenticate);
	
/**************** EDIT TOPIC ******************/
$app->get('/edit-topic/{qcode}', function ($request, $response, $args){
	require_once("dbmodels/user.crud.php");
    $userCRUD = new UserCRUD(getConnection());
	require_once("dbmodels/topic.crud.php");
	$topicCRUD = new TopicCRUD(getConnection());
	require_once("dbmodels/class.crud.php");
        $classCRUD = new ClassCRUD(getConnection());
		require_once("dbmodels/subject.crud.php");
        $subjectCRUD = new SubjectCRUD(getConnection());
		
		$classes = $classCRUD->getAllClasses();
		$subjects = $subjectCRUD->getAllSubjects();
   
    //ADMIN ONLY
	$qcode = $request->getAttribute('qcode');
    if(empty($qcode)){
	$uri = $request->getUri()->withPath($this->router->pathFor('404'));
    return $response->withRedirect((string)$uri);
    }
	if(!$topicCRUD->doesIDExist($qcode)){
          $uri = $request->getUri()->withPath($this->router->pathFor('404')); 
          return $response->withRedirect((string)$uri);
    }
	$thisTopic = $topicCRUD->getID($qcode);
	$thisTopic =  getTopicDetails($qcode);
	if($thisTopic !== NULL){
		$vars = [
			'page' => [
			'name' => 'profile',
			'title' => 'Edit Topic',
			'description' => 'Edit  Topic',
			'classes' => $classes,
			'subjects' => $subjects,
			'data' => $thisTopic
			]
	];			
	return $this->view->render($response, 'edit-topic.twig', $vars);
	}else{
		$uri = $request->getUri()->withPath($this->router->pathFor('404')); 
        return $response->withRedirect((string)$uri);
	}
	})->setName('edit-topic');
	
	/*********** UPDATE EXISTING TOPIC *************/
    $app->post('/apis/topics/update', function ($request, $respo, $args) use ($app) {
		require_once("dbmodels/user.crud.php");
	   $userCRUD = new UserCRUD(getConnection());
		require_once("dbmodels/topic.crud.php");
	   $topicCRUD = new TopicCRUD(getConnection());
	   
	   $response = array();
	   $response["error"] = true;
	   
	   $id = $request->getParam('item_id');
	   if(empty($id)){
		$response['error'] = true;
		$response['message'] = 'Please pass a valid Topic ID.';
		echoRespnse(200, $response);
		return;
	   }
	   if(!$topicCRUD->doesIDExist($id)){
		$response['error'] = true;
		$response['message'] = 'This topic is not available to modify. Check back later. ';
		echoRespnse(200, $response);
		return;
	   }
	   
	   //Step 1: Take User Input
	   $title = $request->getParam('title');
	   $subject_id = $request->getParam('subject_id');
	   $class_id = $request->getParam('class_id');
	   $image = "";
	   $description = $request->getParam('description');
	   //Step 2: Sanitize User Input
	   if(empty($title)){
			$response['error'] = true;
			$response['message'] = 'You must enter topic name.';
			echoRespnse(200, $response);
			return;
	   }
	   if(empty($subject_id)){
		$response['error'] = true;
		$response['message'] = 'You must select a subject.';
		echoRespnse(200, $response);
		return;
   }
   if(empty($class_id)){
	$response['error'] = true;
	$response['message'] = 'You must select a class.';
	echoRespnse(200, $response);
	return;
}
	   /********* Validate Authorization **********/
	   /********* Only Super Admin can create new school ********/
	   $authUser = getCallerSnapshot($request, $respo);
	   if(!$authUser["error"]){
			if($authUser["caller_role"] == 1 || $authUser["caller_role"] == 2 || $authUser["caller_role"] == 4){
		   } else {
			$response['error'] = true;
			$response['message'] = 'You are not authorized to perform this action.';
			echoRespnse(200, $response);
			return;
			}
	   }else{
			$response['error'] = true;
			$response['message'] = 'Invalid request. Please use your authentication signature.';
			echoRespnse(200, $response);
			return;
	   }
	   /********* Validated Authorization ********/
	   
	   $res = $topicCRUD->update($id, $title, $subject_id, $class_id,$description,$image);
	   if (!$res["error"]) {
		   $response["error"] = false;
		   $response["message"] = $title." has been updated successfully. ";
				}else{
					 $response["error"] = true;
					 $response["info"] = $res["message"] ;
					 $response["message"] = "Failed to update role. Please try again.";
				}
	   echoRespnse(200, $response);
	   })->add($authenticate);
   
   
	   /******** DELETE TOPIC *********/
	   $app->post('/apis/topics/delete', function ($request, $respo, $args) use ($app) {
		require_once("dbmodels/topic.crud.php");
	   $topicCRUD = new TopicCRUD(getConnection());
	   require_once("dbmodels/user.crud.php");
	   $userCRUD = new UserCRUD(getConnection());
	   $response = array();
	   $response["error"] = true;
	   $qcode = $request->getParam('item_id');
	   
	   if (empty($qcode)) {
		   $response["error"] = true;
		   $response["message"] = "Invalid request.";
		   echoRespnse(200, $response);
		   return;
		   }
	   
	   if(!$topicCRUD->doesIDExist($qcode)){
		$response['error'] = true;
		$response['message'] = 'This role is not available to modify at this moment. Check back later.';
		echoRespnse(200, $response);
		return;
	   }
	   
	   $authUser = getCallerSnapshot($request, $respo);
	   if(!$authUser["error"]){
			if($authUser["caller_role"] == 1){
		   } else {
			$response['error'] = true;
			$response['message'] = 'You are not authorized to perform this action.';
			echoRespnse(200, $response);
			return;
			}
	   }else{
			$response['error'] = true;
			$response['message'] = 'Invalid request. Please use your authentication signature.';
			echoRespnse(200, $response);
			return;
	   }
	   /********* Validated Authorization ********/
	   
	   $res = $topicCRUD->delete($qcode);
	   if ($res) {
		   $response["error"] = false;
		   $response["message"] = "Topic has been deleted successfully. ";
		   $response["id"] = $qcode;
		   echoRespnse(200, $response);
		   }else{
					 $response["error"] = true;
					 $response["message"] = "Failed to delete topic. Please try again.";
					 echoRespnse(200, $response);
		   }
	   })->add($authenticate);
	   
	
	
	
	
	
	







	