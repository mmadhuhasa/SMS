<?php 
	use Psr\Http\Message\ServerRequestInterface as Request;
	use Psr\Http\Message\ResponseInterface as Response;
	
	/********************  CREATE SUBJECT **********************/
	$app->post('/apis/subjects/create', function ($request, $respo, $args) use ($app) {
    require_once("dbmodels/user.crud.php");
	$userCRUD = new UserCRUD(getConnection());
    require_once("dbmodels/subject.crud.php");
    $subjectCRUD = new SubjectCRUD(getConnection());
	$response = array();
    $response["error"] = true;
	$response["message"] = "";
	$name = $request->getParam('name');
	$image = "";
	$school_id = $request->getParam('school_id');
	$description = $request->getParam('description');
	if(empty($name)){
		$response["error"] = true;
        $response["message"] = "Subject name can not be empty.";
		echoRespnse(200, $response);
		exit;
	}
	/********* Validate Authorization **********/
	/********* Only Super Admin can create new school ********/
	$authUser = getCallerSnapshot($request, $respo);
	if(!$authUser["error"]){
		 if($authUser["caller_role"] == 1 || $authUser["caller_role"] == 2){
			 } else {
		 $response['error'] = true;
         $response['message'] = 'You are not authorized to create a section.';
         echoRespnse(200, $response);
		 return;
		 }
	}else{
		 $response['error'] = true;
         $response['message'] = 'Invalid request. Please use your authentication signature.';
         echoRespnse(200, $response);
		 return;
	}
	/********* Validate Authorization **********/

	$result = $subjectCRUD->create($name, $description,$school_id ,$image);
	if(!$authUser["error"]){
        $response["error"] = false;
		$response["id"] = $result["id"];
        $response["message"] = "New subject has been created! ";
	}else{
		 $response['error'] = true;
         $response['message'] = "Invalid request. Please use your authentication signature.";
         echoRespnse(200, $response);
		 return;
	}
	 echoRespnse(200, $response);
	/********* Validated Authorization ********/
	})->add($authenticate);
	
	
	
/**************** EDIT SUBJECT ******************/
	$app->get('/edit-subject/{qcode}', function ($request, $response, $args){
	require_once("dbmodels/user.crud.php");
    $userCRUD = new UserCRUD(getConnection());
	require_once("dbmodels/subject.crud.php");
    $subjectCRUD = new SubjectCRUD(getConnection());
   
    //ADMIN ONLY
	$qcode = $request->getAttribute('qcode');
    if(empty($qcode)){
	$uri = $request->getUri()->withPath($this->router->pathFor('404'));
    return $response->withRedirect((string)$uri);
    }
	if(!$subjectCRUD->doesIDExist($qcode)){
          $uri = $request->getUri()->withPath($this->router->pathFor('404')); 
          return $response->withRedirect((string)$uri);
    }
	$thisSubject = $subjectCRUD->getID($qcode);
	if($thisSubject !== NULL){
		$vars = [
			'page' => [
			'name' => 'subject',
			'title' => 'Edit Subject',
			'data' => $thisSubject
			]
	];			
	return $this->view->render($response, 'edit-subject.twig', $vars);
	}else{
		$uri = $request->getUri()->withPath($this->router->pathFor('404')); 
        return $response->withRedirect((string)$uri);
	}
	})->setName('edit-subject');
	
	
		
/**************** GET LIST OF SUBJECTS ***********/
    $app->get('/apis/subjects/list', function($request, $response, $args) {
	require_once("dbmodels/subject.crud.php");
    $subjectCRUD = new SubjectCRUD(getConnection());
	$var_response = array();
	try{
	$dataArr = $subjectCRUD->getAllSubjects();
    $data = array();
	/*
	if (count($dataArr) > 0) {
		foreach ($dataArr as $res) {
        $companyProfile = getSchoolDetails($res);
		array_push($data, $companyProfile);
		}
	    }
		*/
		$var_response["items"] = $dataArr;
		}catch(Exception $e){
			$var_response["error"] = true;
            $var_response["message"] = "Failed to fetch data => ".$e.getMessage();
		}
        echoRespnse(200, $var_response);
        })->add($authenticate);


	 /*********** UPDATE EXISTING SUBJECT *************/
     $app->post('/apis/subjects/update', function ($request, $respo, $args) use ($app) {
		require_once("dbmodels/user.crud.php");
		$userCRUD = new UserCRUD(getConnection());
		require_once("dbmodels/subject.crud.php");
		$subjectCRUD = new SubjectCRUD(getConnection());
		$response = array();
		$response["error"] = true;
		
		$id = $request->getParam('item_id');
		if(empty($id)){
		 $response["error"] = true;
		 $response["message"] = 'Please pass a valid subject ID.';
		 echoRespnse(200, $response);
		 return;
		}
		if(!$subjectCRUD->doesIDExist($id)){
			$response["error"]  = true;
		 $response["message"] = 'This subject is not available to modify. Check back later. ';
		 echoRespnse(200, $response);
		 return;
		}
		
		//Step 1: Take User Input
		$name = $request->getParam('name');
		//$image = $request->getParam('image');
		$image = "";
		$school_id = $request->getParam('school_id');
		$description = $request->getParam('description');
		//Step 2: Sanitize User Input
		if(empty($name)){
			 $response["error"] = true;
			 $response["message"] = 'You must enter subject name.';
			 echoRespnse(200, $response);
			 return;
		}
		if(empty($school_id)){
			$response['error'] = true;
			$response['message'] = 'Please must select a school.';
			echoRespnse(200, $response);
			return;
	   }
		/********* Validate Authorization **********/
		/********* Only Super Admin can create new school ********/
		$authUser = getCallerSnapshot($request, $respo);
		if(!$authUser["error"]){
			if($authUser["caller_role"] == 1 || $authUser["caller_role"] == 2 || $authUser["caller_role"] == 4){
			} else {
			 $response["error"] = true;
			 $response["message"] = 'You are not authorized to perform this action.';
			 echoRespnse(200, $response);
			 return;
			 }
		}else{
			 $response["error"] = true;
			 $response["message"] = 'Invalid request. Please use your authentication signature.';
			 echoRespnse(200, $response);
			 return;
		}
		/********* Validated Authorization ********/
		
		$res = $subjectCRUD->update($id, $name, $description, $school_id, $image);
		if (!$res["error"]) {
			$response["error"] = false;
			$response["message"] =   $name." has been updated successfully.";
				 }else{
					  $response["error"] = true;
					  $response["info"] = $res["message"];
					  $response["message"] = "Failed to update subject. Please try again.";

				 }
		echoRespnse(200, $response);
	   })->add($authenticate);


/******** DELETE SUBJECT*********/
	$app->post('/apis/subjects/delete', function ($request, $respo, $args) use ($app) {
	 require_once("dbmodels/subject.crud.php");
	$subjectCRUD = new SubjectCRUD(getConnection());
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
	
    if(!$subjectCRUD->doesIDExist($qcode)){
	 $response['error'] = true;
     $response['message'] = 'This subject is not available to modify at this moment. Check back later.';
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
	
	$res = $subjectCRUD->delete($qcode);
	if ($res) {
        $response["error"] = false;
        $response["message"] = "Subject has been deleted successfully. ";
		$response["id"] = $qcode;
	    echoRespnse(200, $response);
		}else{
				  $response["error"] = true;
                  $response["message"] = "Failed to delete subject. Please try again.";
				  echoRespnse(200, $response);
		}
	})->add($authenticate);
	
/************************ START OF SUBJECT VIEWS *********************/
	$app->get('/manage-subjects', function (Request $request, Response $response, $args){
		require_once("dbmodels/subject.crud.php");
        $subjectCRUD = new SubjectCRUD(getConnection());
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
	    $subjects = $subjectCRUD->getAllSubjectsForSchool($thisUser["school_id"]);
	    if($thisUser["role_id"] == 1){
			$subjects = $subjectCRUD->getAllSubjects();
			$adminMode = true;
		}
		$data = array();
	    if (count($subjects) > 0) {
		foreach ($subjects as $res) {
        $schoolProfile = getSubjectDetails($res["id"]);
		array_push($data, $schoolProfile);
		}
	    }
		$vars = [
			'page' => [
			'name' => '',
			'title' => 'Manage Subjects | Talank SMS',
			'description' => '',
			'data' => $data,
			'adminMode' => $adminMode
			]
		];
		return $this->view->render($response, 'list-subjects.twig', $vars);
	})->setName('manage-subjects');
/************************ END OF SUBJECT VIEWS *********************/
	
	
   /*************** CREATE SUBJECT ROUTE *******************/
	$app->get('/create-new-subject', function (Request $request, Response $response, $args){
		$vars = [
			'page' => [
			'name' => '',
			'title' => 'Create New Subject',
			'description' => 'Talank SAS is a next generation school management and automation system. '
			]
		];
		return $this->view->render($response, 'create-new-subject.twig', $vars);
	})->setName('create-new-subject');
	

?>