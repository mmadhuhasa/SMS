<?php

	use Psr\Http\Message\ServerRequestInterface as Request;
	use Psr\Http\Message\ResponseInterface as Response;
	/************************ START OF CLASS VIEWS *********************/
	$app->get('/manage-classes', function (Request $request, Response $response, $args){
		require_once("dbmodels/class.crud.php");
        $classCRUD = new ClassCRUD(getConnection());
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
	    $classes = $classCRUD->getAllOurClasses($thisUser["school_id"]);
	    if($thisUser["role_id"] == 1){
			$classes = $classCRUD->getAllClasses();
			$adminMode = true;
		}
		$data = array();
	    if (count($classes) > 0) {
		foreach ($classes as $res) {
        $schoolProfile = getClassDetails($res["id"]);
		array_push($data, $schoolProfile);
		}
	    }
		$vars = [
			'page' => [
			'name' => '',
			'title' => 'Manage Classes | Talank SMS',
			'description' => '',
			'data' => $data,
			'adminMode' => $adminMode
			]
		];
		return $this->view->render($response, 'list-classes.twig', $vars);
	})->setName('manage-classes');
	/************************ END OF CLASS VIEWS *********************/
	
/*************** HOME ROUTE *******************/
	$app->get('/create-new-class', function (Request $request, Response $response, $args){
		//require_once("dbmodels/service.crud.php");
        //$serviceCRUD = new ServiceCRUD(getConnection());
		//$services = $serviceCRUD->getAllServices();
		$vars = [
			'page' => [
			'name' => '',
			'title' => 'Create New Class',
			'description' => 'Talank SAS is a next generation school management and automation system. '
			]
		];
		return $this->view->render($response, 'create-new-class.twig', $vars);
	})->setName('create-new-class');
		
	
	/**************** VIEW PROFILE ******************/
	$app->get('/edit-class/{qcode}', function ($request, $response, $args){
	require_once("dbmodels/user.crud.php");
    $userCRUD = new UserCRUD(getConnection());
	require_once("dbmodels/class.crud.php");
    $classCRUD = new ClassCRUD(getConnection());
    require_once("dbmodels/utils.crud.php");
    $utilCRUD = new UtilCRUD(getConnection());
    $countries = $utilCRUD->getAllCountries();
    //ADMIN ONLY
	$qcode = $request->getAttribute('qcode');
    if(empty($qcode)){
	$uri = $request->getUri()->withPath($this->router->pathFor('404'));
    return $response->withRedirect((string)$uri);
    }
	if(!$classCRUD->doesIDExist($qcode)){
          $uri = $request->getUri()->withPath($this->router->pathFor('404')); 
          return $response->withRedirect((string)$uri);
    }
	$thisClass = $classCRUD->getID($qcode);
	if($thisClass !== NULL){
		//$thisClass = getUserFullProfile($thisUser["id"]);
		$vars = [
			'page' => [
			'name' => 'profile',
			'title' => 'Edit Class',
			'description' => 'Edit Class',
			'data' => $thisClass
			]
	];			
	return $this->view->render($response, 'edit-class.twig', $vars);
	}else{
		$uri = $request->getUri()->withPath($this->router->pathFor('404')); 
        return $response->withRedirect((string)$uri);
	}
	})->setName('edit-class');
	
/**************** GET LIST OF CLASSES ***********/
    $app->get('/apis/classes/list', function($request, $response, $args) {
	require_once("dbmodels/class.crud.php");
    $classCRUD = new ClassCRUD(getConnection());
	$var_response = array();
	try{
	$dataArr = $classCRUD->getAllClasses();
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



/*********** CREATE NEW CLASS *************/
  
	$app->post('/apis/classes/create', function ($request, $respo, $args) use ($app) {
    require_once("dbmodels/user.crud.php");
	$userCRUD = new UserCRUD(getConnection());
    require_once("dbmodels/class.crud.php");
    $classCRUD = new ClassCRUD(getConnection());
	$response = array();
    $response["error"] = true;
	$response["message"] = "";
	$name = $request->getParam('name');
	$school_id = $request->getParam('school_id');
	$symbol = $request->getParam('symbol');

	if(empty($name)){
		$response["error"] = true;
        $response["message"] = "Class name can not be empty.";
		echoRespnse(200, $response);
		exit;
	}
	
	
	/********* Validate Authorization **********/

	//$id = $schoolCRUD->generateCode();
	/********* Only Super Admin and School Owner or Permitted User ********/
	$authUser = $classCRUD->create($name, $symbol, $school_id);
	if(!$authUser["error"]){
        $response["error"] = false;
		$response["id"] = $authUser["id"];
        $response["message"] = "New Class has been created! ";
	}else{
		 $response['error'] = true;
         $response['message'] = "Failed to create class. ".$authUser["message"];
         echoRespnse(200, $response);
		 return;
	}
	 echoRespnse(200, $response);
	/********* Validated Authorization ********/
	})->add($authenticate);
	
/*********** UPDATE EXISTING CLASS *************/
    $app->post('/apis/classes/update', function ($request, $respo, $args) use ($app) {
	 require_once("dbmodels/user.crud.php");
	 $userCRUD = new UserCRUD(getConnection());
	 require_once("dbmodels/class.crud.php");
    $classCRUD = new ClassCRUD(getConnection());
	require_once("dbmodels/school.crud.php");
    $schoolCRUD = new SchoolCRUD(getConnection());
    
	$response = array();
    $response["error"] = false;
	
	$id = $request->getParam('item_id');
	if(empty($id)){
	 $response['error'] = true;
     $response['message'] = 'Please pass a valid class ID.';
     echoRespnse(200, $response);
	 return;
	}
    if(!$classCRUD->doesIDExist($id)){
	 $response['error'] = true;
     $response['message'] = 'This class is not available to modify. Check back later. ';
     echoRespnse(200, $response);
	 return;
	}
	
	//Step 1: Take User Input
	$name = $request->getParam('name');
	$school_id = $request->getParam('school_id');
	$symbol = $request->getParam('symbol');
	//Step 2: Sanitize User Input
	if(empty($name)){
		 $response['error'] = true;
         $response['message'] = 'You must enter class name.';
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
	
	$res = $classCRUD->update($id, $name, $symbol, $school_id);
	if (!$res["error"]) {
        $response["error"] = false;
        $response["message"] = "Class has been updated successfully. ";
			 }else{
				  $response["error"] = true;
				  $response["info"] = $res["message"] ;
                  $response["message"] = "Failed to update class. Please try again.";
				  echoRespnse(200, $response);
			 }
	echoRespnse(200, $response);
	})->add($authenticate )   ;
	
	/******** DELETE CLASS *********/
	$app->post('/apis/classes/delete', function ($request, $respo, $args) use ($app) {
	require_once("dbmodels/class.crud.php");
    $classCRUD = new ClassCRUD(getConnection());
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
	
    if(!$classCRUD->isIDExists($qcode)){
	 $response['error'] = true;
     $response['message'] = 'This class is not available to modify at this moment. Check back later.';
     echoRespnse(200, $response);
	 return;
	}
	
	$schoolID = $classCRUD->getSchoolByID($qcode);
	/********* Validate Authorization **********/
	/********* Only Super Admin and school owners can delete school ********/
	$authUser = getCallerSnapshot($request, $respo);
	if(!$authUser["error"]){
		 if($authUser["caller_role"] == 1 || $authUser["caller_role"] == 2){
		 if(!$authUser["caller_role"] == 1 && !$userCRUD->isSchoolAdmin($authUser["id"], $schoolID)) {
		 $response['error'] = true;
         $response['message'] = 'You are not authorized to perform this action.';
         echoRespnse(200, $response);
		 return;
		 }
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
	
	$res = $classCRUD->delete($qcode);
	if ($res) {
        $response["error"] = false;
        $response["message"] = "Class has been deleted successfully. ";
		$response["id"] = $qcode;
	    echoRespnse(200, $response);
		}else{
				  $response["error"] = true;
                  $response["message"] = "Failed to delete class. Please try again.";
				  echoRespnse(200, $response);
		}
	})->add($authenticate);
	
?>