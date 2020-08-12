<?php 
	use Psr\Http\Message\ServerRequestInterface as Request;
	use Psr\Http\Message\ResponseInterface as Response;

/*************** TAKE ASSIGNMENT ROUTE *******************/

$app->get('/take-assignment/{qcode}', function (Request $request, Response $response, $args){
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
			'name' => 'take-assignment',
			'title' => 'Take Assignment | Talank SMS',
			'description' => 'Talank SAS is a next generation school management and automation system. ',
			'data' => $thisAssignment,
			'showAttachments' => true
		]
];		
	return $this->view->render($response, 'take-assignment.twig', $vars);
	}else{
		$uri = $request->getUri()->withPath($this->router->pathFor('404')); 
        return $response->withRedirect((string)$uri);
	}
})->setName('take-assignment');



	/******** CREATE ASSIGNMENT_SUBMISSION *********/
	$app->post('/apis/submissions/create', function ($request, $respo, $args) use ($app) {
    require_once("dbmodels/user.crud.php");
	$userCRUD = new UserCRUD(getConnection());
    require_once("dbmodels/assignment_submission.crud.php");
    $submissionCRUD = new AssignmentSubmissionCRUD(getConnection());
	$response = array();
    $response["error"] = true;
	$response["message"] = "";
	$assignment_id = $request->getParam('assignment_id');
    $user_id = $request->getParam('user_id');
	$title = $request->getParam('title');
	$content = $request->getParam('content');
	$date_submitted = date('Y-m-d H:i:s');
	$status  = "Pending";

	if(empty($content)){
		$response["error"] = true; 
        $response["message"] = "Submission content can not be empty.";
		echoRespnse(200, $response);
		exit;
	}
	if(empty($user_id)){
		$response["error"] = true;
        $response["message"] = "User id can not be empty.";
		echoRespnse(200, $response);
		exit;
	}
	/********* Validate Authorization **********/

	$qcode = $submissionCRUD->generateCode();
	/********* Only Super Admin and School Owner or Permitted User ********/
	$authUser = $submissionCRUD->create($assignment_id, $user_id, $title, $content , $status, $qcode);
	if(!$authUser["error"]){
        $response["error"] = false;
		$response["id"] = $authUser["id"];
        $response["message"] = "Your entry has been submitted successfully.";
	}else{
		 $response['error'] = true;
         $response['message'] = "Failed to create submission.".$authUser["message"];
         echoRespnse(200, $response);
		 return;
	}
	 echoRespnse(200, $response);
	/********* Validated Authorization ********/
	})->add($authenticate);
	
/*********** UPDATE EXISTING ASSIGNMENT_SUBMISSION *************/
    $app->post('/apis/submissions/update', function ($request, $respo, $args) use ($app) {
	 require_once("dbmodels/user.crud.php");
	 $userCRUD = new UserCRUD(getConnection());
	require_once("dbmodels/assignment_submission.crud.php");
    $submissionCRUD = new AssignmentSubmissionCRUD(getConnection());
    
	$response = array();
    $response["error"] = false;
	
	$id = $request->getParam('item_id');
	if(empty($id)){
	 $response['error'] = true;
     $response['message'] = 'Please pass a valid  ID.';
     echoRespnse(200, $response);
	 return;
	}
    if(!$submissionCRUD->doesIDExist($id)){
	 $response['error'] = true;
     $response['message'] = 'This submission is not available to modify. Check back later. ';
     echoRespnse(200, $response);
	 return;
	}
	
	//Step 1: Take User Input
	$assignment_id = $request->getParam('assignment_id');
    $user_id = $request->getParam('user_id');
	$title = $request->getParam('title');
	$content = $request->getParam('content');
	$date_submitted = date('Y-m-d H:i:s');
	$status  ="Pending";
	
	//Step 2: Sanitize User Input
	if(empty($content)){
		 $response['error'] = true;
         $response['message'] = 'You must enter submission content.';
         echoRespnse(200, $response);
		 return;
	}
	if(empty($user_id)){
		$response["error"] = true;
        $response["message"] = "User id can not be empty.";
		echoRespnse(200, $response);
		exit;
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
	
	$res = $submissionCRUD->update($id, $assignment_id, $user_id, $title, $content , $status);
	if (!$res["error"]) {
        $response["error"] = false;
        $response["message"] = "Submission has been updated successfully. ";
			 }else{
				  $response["error"] = true;
				  $response["info"] = $res["message"] ;
                  $response["message"] = "Failed to update submission. Please try again.";
				  echoRespnse(200, $response);
			 }
	echoRespnse(200, $response);
	})->add($authenticate )   ;

/******** DELETE ASSIGNMENT_SUBMISSION*********/
$app->post('/apis/submissions/delete', function ($request, $respo, $args) use ($app) {
   require_once("dbmodels/submission.crud.php");
   $submissionCRUD = new SubmissionCRUD(getConnection());
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
   
   if(!$submissionCRUD->doesIDExist($qcode)){
	$response['error'] = true;
	$response['message'] = 'This submission is not available to modify at this moment. Check back later.';
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
   
   $res = $submissionCRUD->delete($qcode);
   if ($res) {
	   $response["error"] = false;
	   $response["message"] = "Submission has been deleted successfully. ";
	   $response["id"] = $qcode;
	   echoRespnse(200, $response);
	   }else{
				 $response["error"] = true;
				 $response["message"] = "Failed to delete Submission. Please try again.";
				 echoRespnse(200, $response);
	   }
   })->add($authenticate);	


  /**************** EDIT ASSIGNMENT_SUBMISSION ******************/
	$app->get('/edit-submission/{qcode}', function ($request, $response, $args){
		require_once("dbmodels/user.crud.php");
		$userCRUD = new UserCRUD(getConnection());
		require_once("dbmodels/submission.crud.php");
        $submissionCRUD = new SubmissionCRUD(getConnection());
		$qcode = $request->getAttribute('qcode');
		if(empty($qcode)){
		$uri = $request->getUri()->withPath($this->router->pathFor('404'));
		return $response->withRedirect((string)$uri);
		}
		if(!$submissionCRUD->isQCodeExists($qcode)){
			  $uri = $request->getUri()->withPath($this->router->pathFor('404')); 
			  return $response->withRedirect((string)$uri);
		}
		$thisSubmission = $SubmissionCRUD->getByQCode($qcode);

		$thisSubmission = getSubmissionDetails($thisAssignmentSubmission["id"]);


		if($thisSubmission !== NULL){
			$vars = [
				'page' => [
				'name' => 'subject',
				'title' => 'Edit Submission',
				'data' => $thisSubmission,
				]
		];			
		return $this->view->render($response, 'edit-submission.twig', $vars);
		}else{
			$uri = $request->getUri()->withPath($this->router->pathFor('404')); 
			return $response->withRedirect((string)$uri);
		}
		})->setName('edit-submission');
		
		
			
	/**************** GET LIST OF ASSIGNMENT_SUBMISSION ***********/
	$app->get('/apis/submissions/list', function($request, $response, $args) {
		require_once("dbmodels/assignment_submission.crud.php");
        $submissionCRUD = new SubmissionCRUD(getConnection());
		$var_response = array();
		try{
		$dataArr = $SubmissionCRUD->getAllSubmissions();
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

	/************************ START OF ASSIGNMENT_SUBMISSION VIEWS *********************/
	$app->get('manage-assignments/{:qcode}', function (Request $request, Response $response, $args){
		require_once("dbmodels/submission.crud.php");
        $submissionCRUD = new SubmissionCRUD(getConnection());
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

	     //ADMIN ONLY
	$qcode = $request->getAttribute('qcode');
    if(empty($qcode)){
	$uri = $request->getUri()->withPath($this->router->pathFor('404'));
    return $response->withRedirect((string)$uri);
    }
	if(!$userCRUD->doesUserNameExist($qcode)){
          $uri = $request->getUri()->withPath($this->router->pathFor('unauthorized')); 
          return $response->withRedirect((string)$uri);
    }
	$thisUser = $userCRUD->getByUsername($qcode);
	if($thisUser !== NULL){
		$thisUser =  ($thisUser["id"]);
		$vars = [
			'page' => [
			'name' => 'profile',
			'title' => 'My Profile',
			'description' => 'Manage Profile',
			'user' => $thisUser
			]
	];			
	return $this->view->render($response, 'profile.twig', $vars);
	}else{
		$uri = $request->getUri()->withPath($this->router->pathFor('404')); 
        return $response->withRedirect((string)$uri);
	}

		//$assignments = $assignmentCRUD->getAllAssignmentsForSchool($thisUser["school_id"]);
		$submissions = $SubmissionCRUD->getAllSubmissionsForTeacher($thisUser["id"]);

	    // if($thisUser["role_id"] == 1){
		// 	$assignments = $assignmentCRUD->getAllAssignments();
		// 	$adminMode = true;
		// }
		$data = array();
	    if (count($submissions) > 0) {
		foreach ($submissions as $res) {
        $schoolProfile = getSubmissionDetails($res["id"]);
		array_push($data, $schoolProfile);
		}
	    }
		$vars = [
			'page' => [
			'name' => '',
			'title' => 'Manage Submission  | Talank SMS',
			'description' => '',
			'data' => $data,
			'adminMode' => $adminMode
			]
		];
		return $this->view->render($response, 'manage-assignments.twig', $vars);
	})->setName('manage-assignments');

/************************ END OF ASSIGNMENT_SUBMISSIONS VIEWS *********************/		
	

	
