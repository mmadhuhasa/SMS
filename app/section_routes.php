<?php
	use Psr\Http\Message\ServerRequestInterface as Request;
	use Psr\Http\Message\ResponseInterface as Response;

/************************ START OF SECTION VIEWS *********************/
	$app->get('/manage-sections', function (Request $request, Response $response, $args){
		require_once("dbmodels/section.crud.php");
        $sectionCRUD = new SectionCRUD(getConnection());
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
	    $sections = $sectionCRUD->getAllSectionsForSchool($thisUser["school_id"]);
	    if($thisUser["role_id"] == 1){
			$sections = $sectionCRUD->getAllSections();
			$adminMode = true;
		}
		$data = array();
	    if (count($sections) > 0) {
		foreach ($sections as $res) {
        $schoolProfile = getSectionDetails($res["id"]);
		array_push($data, $schoolProfile);
		}
	    }
		$vars = [
			'page' => [
			'name' => '',
			'title' => 'Manage Sections | Talank SMS',
			'description' => '',
			'data' => $data,
			'adminMode' => $adminMode
			]
		];
		return $this->view->render($response, 'list-sections.twig', $vars);
	})->setName('manage-sections');
/************************ END OF SECTION VIEWS *********************/
	
	
/*************** CREATE SECTION ROUTE *******************/
	$app->get('/create-new-section', function (Request $request, Response $response, $args){
		require_once("dbmodels/class.crud.php");
        $classCRUD = new ClassCRUD(getConnection());
		$classes=$classCRUD->getAllOurClasses($_SESSION["school_id"]);
		$vars = [
			'page' => [
			'name' => '',
			'title' => 'Create New Section',
			'classes' => $classes,
			'description' => 'Talank SAS is a next generation school management and automation system. '
			]
		];
		return $this->view->render($response, 'create-new-section.twig', $vars);
	})->setName('create-new-section');
	

		/******** CREATE SECTION *********/
	$app->post('/apis/sections/create', function ($request, $respo, $args) use ($app) {
    require_once("dbmodels/user.crud.php");
	$userCRUD = new UserCRUD(getConnection());
	require_once("dbmodels/class.crud.php");
    $classCRUD = new ClassCRUD(getConnection());
    require_once("dbmodels/section.crud.php");
    $sectionCRUD = new SectionCRUD(getConnection());
	$response = array();
    $response["error"] = true;
	$response["message"] = "";
	$name = $request->getParam('name');
	$class_id = $request->getParam('class_id');
	$school_id = $request->getParam('school_id');
	$strength = $request->getParam('strength');
	if(empty($name)){
		$response["error"] = true;
        $response["message"] = "Section name can not be empty.";
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

	$result = $sectionCRUD->create($name, $class_id, $school_id, $strength);
	if(!$result["error"]){
        $response["error"] = false;
		$response["id"] = $result["id"];
        $response["message"] = "New section has been created! ";
	}else{
		 $response['error'] = true;
         $response['message'] = "Error creating section. Please try again.";
         echoRespnse(200, $response);
		 return;
	}
	 echoRespnse(200, $response);
     })->add($authenticate);
	
/**************** EDIT SECTION ******************/
	$app->get('/edit-section/{qcode}', function ($request, $response, $args){
	require_once("dbmodels/user.crud.php");
    $userCRUD = new UserCRUD(getConnection());
	require_once("dbmodels/section.crud.php");
    $sectionCRUD = new SectionCRUD(getConnection());
	require_once("dbmodels/class.crud.php");
        $classCRUD = new ClassCRUD(getConnection());
		$classes=$classCRUD->getAllOurClasses($_SESSION["school_id"]);
   
    //ADMIN ONLY
	$qcode = $request->getAttribute('qcode');
    if(empty($qcode)){
	$uri = $request->getUri()->withPath($this->router->pathFor('404'));
    return $response->withRedirect((string)$uri);
    }
	if(!$sectionCRUD->doesIDExist($qcode)){
          $uri = $request->getUri()->withPath($this->router->pathFor('404')); 
          return $response->withRedirect((string)$uri);
    }
	$thisSection = $sectionCRUD->getID($qcode);

	$thisSection = getSectionDetails($thisSection["id"]);

	if($thisSection !== NULL){
		$vars = [
			'page' => [
			'name' => 'profile',
			'title' => 'Edit Section',
			'classes' => $classes,
			'data' => $thisSection
			]
	];			
	return $this->view->render($response, 'edit-section.twig', $vars);
	}else{
		$uri = $request->getUri()->withPath($this->router->pathFor('404')); 
        return $response->withRedirect((string)$uri);
	}
	})->setName('edit-section');
	
	
		
/**************** GET LIST OF SEctions ***********/
    $app->get('/apis/sections/list', function($request, $response, $args) {
	require_once("dbmodels/section.crud.php");
    $sectionCRUD = new SectionCRUD(getConnection());
	$var_response = array();
	try{
	$dataArr = $sectionCRUD->getAllSections();
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


	/*********** UPDATE EXISTING SECTION *************/
    $app->post('/apis/sections/update', function ($request, $respo, $args) use ($app) {
	 require_once("dbmodels/user.crud.php");
	$userCRUD = new UserCRUD(getConnection());
	 require_once("dbmodels/section.crud.php");
	$sectionCRUD = new SectionCRUD(getConnection());
    
	$response = array();
    $response["error"] = false;
	
	$id = $request->getParam('item_id');
	if(empty($id)){
	 $response['error'] = true;
     $response['message'] = 'Please pass a valid section ID.';
     echoRespnse(200, $response);
	 return;
	}
    if(!$sectionCRUD->doesIDExist($id)){
	 $response['error'] = true;
     $response['message'] = 'This section is not available to modify. Check back later. ';
     echoRespnse(200, $response);
	 return;
	}
	
	//Step 1: Take User Input
	$name = $request->getParam('name');
	$class_id = $request->getParam('class_id');
	$school_id = $request->getParam('school_id');
	$strength = $request->getParam('strength');
	//Step 2: Sanitize User Input
	if(empty($name)){
		 $response['error'] = true;
         $response['message'] = 'You must enter section name.';
         echoRespnse(200, $response);
		 return;
	}
	if(empty($school_id)){
		 $response['error'] = true;
         $response['message'] = 'You must pass school ID.';
         echoRespnse(200, $response);
		 return;
	}
	
	/********* Validate Authorization **********/
	/********* Only Super Admin can create new school ********/
	$authUser = getCallerSnapshot($request, $respo);
	if(!$authUser["error"]){
		if($authUser["caller_role"] == 1 || $authUser["caller_role"] == 2){
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
	
	$res = $sectionCRUD->update($id, $name, $class_id, $school_id, $strength);
	if (!$res["error"]) {
        $response["error"] = false;
        $response["message"] = $name." has been updated successfully. ";
			 }else{
				  $response["error"] = true;
				  $response["info"] = $res["message"] ;
                  $response["message"] = "Failed to update section. Please try again.";
				  echoRespnse(200, $response);
			 }
	echoRespnse(200, $response);
	})->add($authenticate);


/******** DELETE SECTION *********/
	$app->post('/apis/sections/delete', function ($request, $respo, $args) use ($app) {
	 require_once("dbmodels/section.crud.php");
	$sectionCRUD = new SectionCRUD(getConnection());
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
	
    if(!$sectionCRUD->doesIDExist($qcode)){
	 $response['error'] = true;
     $response['message'] = 'This section is not available to modify at this moment. Check back later.';
     echoRespnse(200, $response);
	 return;
	}
	
	$authUser = getCallerSnapshot($request, $respo);
	if(!$authUser["error"]){
		 if($authUser["caller_role"] == 1 || $authUser["caller_role"] == 2){
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
	
	$res = $sectionCRUD->delete($qcode);
	if ($res) {
        $response["error"] = false;
        $response["message"] = "Section has been deleted successfully. ";
		$response["id"] = $qcode;
	    echoRespnse(200, $response);
		}else{
				  $response["error"] = true;
                  $response["message"] = "Failed to delete section. Please try again.";
				  echoRespnse(200, $response);
		}
	})->add($authenticate);
	
	
		