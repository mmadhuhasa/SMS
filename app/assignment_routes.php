<?php 
	use Psr\Http\Message\ServerRequestInterface as Request;
	use Psr\Http\Message\ResponseInterface as Response;
	
	
/*************** CREATE ASSIGNMENT ROUTE *******************/
$app->get('/create-assignment', function (Request $request, Response $response, $args){
    require_once("dbmodels/class.crud.php");
    $classCRUD = new ClassCRUD(getConnection());
	require_once("dbmodels/subject.crud.php");
    $subjectCRUD = new SubjectCRUD(getConnection());
	$classes = $classCRUD->getAllClasses();
	$subjects = $subjectCRUD->getAllSubjects();
    $vars = [
        'page' => [
        'name' => 'assignments',
        'title' => 'Create Assignment | Talank SMS',
		'description' => 'Talank SAS is a next generation school management and automation system. ',
		'classes' => $classes,
		'subjects' => $subjects,
        ]
    ];
    return $this->view->render($response, 'create-assignment.twig', $vars);
})->setName('create-assignment');


/*************** VIEW ASSIGNMENT ROUTE *******************/

    $app->get('/view-assignment/{qcode}', function (Request $request, Response $response, $args){
		require_once("dbmodels/user.crud.php");
		$userCRUD = new UserCRUD(getConnection());
		require_once("dbmodels/utils.crud.php");
		$utilCRUD = new UtilCRUD(getConnection());
		require_once("dbmodels/assignment.crud.php");
		$assignmentCRUD = new AssignmentCRUD(getConnection());
		//VERIFY QCODE
		$qcode = $request->getAttribute('qcode');
		if(empty($qcode)){
		$uri = $request->getUri()->withPath($this->router->pathFor('404'));
		return $response->withRedirect((string)$uri);
		}
		if(!$assignmentCRUD->isCodeValid($qcode)){
			  $uri = $request->getUri()->withPath($this->router->pathFor('unauthorized')); 
			  return $response->withRedirect((string)$uri);
		}
		$thisAssignment = $assignmentCRUD->getByQCode($qcode);
		if($thisAssignment !== NULL){
			$thisAssignment = getAssignmentDetails($thisAssignment["id"]);			
    $vars = [
        'page' => [
        'name' => 'assignments',
        'title' => 'View Assignment | Talank SMS',
		'description' => 'Talank SAS is a next generation school management and automation system. ',
		'data' => $thisAssignment,
		'showAttachments' => true
        ]
    ];
	return $this->view->render($response, 'assignment-view.twig', $vars);
}else{
	$uri = $request->getUri()->withPath($this->router->pathFor('404')); 
	return $response->withRedirect((string)$uri);
}
})->setName('view-assignment');



/*************** REVIEW SUBMISSION ROUTE *******************/

    $app->get('/review-submission', function (Request $request, Response $response, $args){
    $vars = [
        'page' => [
        'name' => 'school-calendar',
        'title' => 'Create Assignment | Talank SMS',
        'description' => 'Talank SAS is a next generation school management and automation system. '
        ]
    ];
    return $this->view->render($response, 'review-submission.twig', $vars);
})->setName('review-submission');

/*************** VIEW ASSIGNMENT-SUBMISSION ROUTE *******************/

$app->get('/view-assignment-submission', function (Request $request, Response $response, $args){
    $vars = [
        'page' => [
        'name' => 'school-calendar',
        'title' => 'View Submission | Talank SMS',
        'description' => 'Talank SAS is a next generation school management and automation system. '
        ]
    ];
    return $this->view->render($response, 'view-assignment-submission.twig', $vars);
})->setName('view-assignment-submission');

	/******** CREATE ASSIGNMENT *********/
	$app->post('/apis/assignments/create', function ($request, $respo, $args) use ($app) {
    require_once("dbmodels/user.crud.php");
	$userCRUD = new UserCRUD(getConnection());
    require_once("dbmodels/assignment.crud.php");
    $assignmentCRUD = new AssignmentCRUD(getConnection());
	$response = array();
    $response["error"] = true;
	$response["message"] = "";
	$title = $request->getParam('title');
	$class_id = $request->getParam('class_id');
	$subject_id = $request->getParam('subject_id');
	$description = $request->getParam('description');
	$user_id  = $request->getParam('user_id');
	//$image = $request->getParam('image');
	$image = "";
	$date_submission = $request->getParam('date_submission');
	$is_published = 1;
	if(null !== $request->getParam('is_published')){
		$is_published = $request->getParam('is_published');
	}

	if(empty($title)){
		$response["error"] = true;
        $response["message"] = "Assignment name can not be empty.";
		echoRespnse(200, $response);
		exit;
	}
	if(empty($date_submission)){
		$response["error"] = true;
        $response["message"] = "Please select a last date for assignment submission.";
		echoRespnse(200, $response);
		exit;
	}
	/********* Validate Authorization **********/

	$qcode = $assignmentCRUD->generateCode();
	/********* Only Super Admin and School Owner or Permitted User ********/
	$authUser = $assignmentCRUD->create($user_id, $title, $class_id, $subject_id, $description ,$image, $date_submission, $is_published, $qcode);
	if(!$authUser["error"]){
        $response["error"] = false;
		$response["id"] = $authUser["id"];
        $response["message"] = "New assignment has been created! ";
	}else{
		 $response['error'] = true;
         $response['message'] = "Failed to create assignment.".$authUser["message"];
         echoRespnse(200, $response);
		 return;
	}
	 echoRespnse(200, $response);
	/********* Validated Authorization ********/
	})->add($authenticate);
	
/*********** UPDATE EXISTING ASSIGNMENT *************/
    $app->post('/apis/assignments/update', function ($request, $respo, $args) use ($app) {
	 require_once("dbmodels/user.crud.php");
	 $userCRUD = new UserCRUD(getConnection());
	 require_once("dbmodels/assignment.crud.php");
	 $assignmentCRUD = new AssignmentCRUD(getConnection());
    
	$response = array();
    $response["error"] = false;
	
	$id = $request->getParam('item_id');
	if(empty($id)){
	 $response['error'] = true;
     $response['message'] = 'Please pass a valid section ID.';
     echoRespnse(200, $response);
	 return;
	}
    if(!$assignmentCRUD->doesIDExist($id)){
	 $response['error'] = true;
     $response['message'] = 'This assignment is not available to modify. Check back later. ';
     echoRespnse(200, $response);
	 return;
	}
	
	//Step 1: Take User Input
	$title = $request->getParam('title');
	$class_id = $request->getParam('class_id');
	$subject_id = $request->getParam('subject_id');
	$description = $request->getParam('description');
	$image = "";
	$date_submission = $request->getParam('date_submission');
	$is_published = 1;
	if(null !== $request->getParam('is_published')){
		$is_published = $request->getParam('is_published');
	}
	//Step 2: Sanitize User Input
	if(empty($title)){
		 $response['error'] = true;
         $response['message'] = 'You must enter assignment title.';
         echoRespnse(200, $response);
		 return;
	}
	/********* Validate Authorization **********/
	/********* Only Super Admin can create new school ********/
	$authUser = getCallerSnapshot($request, $respo);
	if(!$authUser["error"]){
		 if($authUser["caller_role"] == 1 || $authUser["caller_role"] == 2 || $authUser["caller_role"] == 4){
			 //If teacher then can modify his own records
			 //If school admin then cabn modify his own schools record
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
	
	$res = $assignmentCRUD->update($id ,$title, $class_id, $subject_id, $description ,$image, $date_submission, $is_published);
	if (!$res["error"]) {
        $response["error"] = false;
        $response["message"] = "Assignment has been updated successfully. ";
			 }else{
				  $response["error"] = true;
				  $response["info"] = $res["message"] ;
                  $response["message"] = "Failed to update assignment. Please try again.";
				  echoRespnse(200, $response);
			 }
	echoRespnse(200, $response);
	})->add($authenticate )   ;

/******** DELETE ASSIGNMENT*********/
$app->post('/apis/assignments/delete', function ($request, $respo, $args) use ($app) {
	require_once("dbmodels/assignment.crud.php");
   $assignmentCRUD = new AssignmentCRUD(getConnection());
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
   
   if(!$assignmentCRUD->doesIDExist($qcode)){
	$response['error'] = true;
	$response['message'] = 'This assignment is not available to modify at this moment. Check back later.';
	echoRespnse(200, $response);
	return;
   }
   
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
   
   $res = $assignmentCRUD->delete($qcode);
   if ($res) {
	   $response["error"] = false;
	   $response["message"] = "Assignment has been deleted successfully. ";
	   $response["id"] = $qcode;
	   echoRespnse(200, $response);
	   }else{
				 $response["error"] = true;
				 $response["message"] = "Failed to delete assignment. Please try again.";
				 echoRespnse(200, $response);
	   }
   })->add($authenticate);	


  /**************** EDIT ASSIGNMENT ******************/
	$app->get('/edit-assignment/{qcode}', function ($request, $response, $args){
		require_once("dbmodels/user.crud.php");
		$userCRUD = new UserCRUD(getConnection());
		require_once("dbmodels/assignment.crud.php");
		$assignmentCRUD = new AssignmentCRUD(getConnection());
		require_once("dbmodels/class.crud.php");
		$classCRUD = new ClassCRUD(getConnection());
		require_once("dbmodels/subject.crud.php");
		$subjectCRUD = new SubjectCRUD(getConnection());
		$classes = $classCRUD->getAllClasses();
		$subjects = $subjectCRUD->getAllSubjects();
		$qcode = $request->getAttribute('qcode');
		if(empty($qcode)){
		$uri = $request->getUri()->withPath($this->router->pathFor('404'));
		return $response->withRedirect((string)$uri);
		}
		if(!$assignmentCRUD->isQCodeExists($qcode)){
			  $uri = $request->getUri()->withPath($this->router->pathFor('404')); 
			  return $response->withRedirect((string)$uri);
		}
		$thisAssignment = $assignmentCRUD->getByQCode($qcode);

		$thisAssignment = getAssignmentDetails($thisAssignment["id"]);


		if($thisAssignment !== NULL){
			$vars = [
				'page' => [
				'name' => 'subject',
				'title' => 'Edit Assignment',
				'data' => $thisAssignment,
				'classes' => $classes,
	        	'subjects' => $subjects,
				]
		];			
		return $this->view->render($response, 'edit-assignment.twig', $vars);
		}else{
			$uri = $request->getUri()->withPath($this->router->pathFor('404')); 
			return $response->withRedirect((string)$uri);
		}
		})->setName('edit-assignment');
		
		
			
	/**************** GET LIST OF ASSIGNMENT ***********/
	$app->get('/apis/assignments/list', function($request, $response, $args) {
		require_once("dbmodels/assignment.crud.php");
		$assignmentCRUD = new AssignmentCRUD(getConnection());
		$var_response = array();
		try{
		$dataArr = $assignmentCRUD->getAllAssignments();
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

	/************************ START OF SUBJECT VIEWS *********************/
	$app->get('/manage-assignments', function (Request $request, Response $response, $args){
		require_once("dbmodels/assignment.crud.php");
		$assignmentCRUD = new AssignmentCRUD(getConnection());
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
		//$assignments = $assignmentCRUD->getAllAssignmentsForSchool($thisUser["school_id"]);
		$assignments = $assignmentCRUD->getAllAssignmentsForTeacher($thisUser["id"]);

	    // if($thisUser["role_id"] == 1){
		// 	$assignments = $assignmentCRUD->getAllAssignments();
		// 	$adminMode = true;
		// }
		$data = array();
	    if (count($assignments) > 0) {
		foreach ($assignments as $res) {
        $schoolProfile = getAssignmentDetails($res["id"]);
		array_push($data, $schoolProfile);
		}
	    }
		$vars = [
			'page' => [
			'name' => '',
			'title' => 'Manage Assignments | Talank SMS',
			'description' => '',
			'data' => $data,
			'adminMode' => $adminMode
			]
		];
		return $this->view->render($response, 'manage-assignments.twig', $vars);
	})->setName('manage-assignments');

/************************ END OF ASIGNMENTS VIEWS *********************/		
	

	
