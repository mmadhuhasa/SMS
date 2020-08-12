<?php
	use Psr\Http\Message\ServerRequestInterface as Request;
	use Psr\Http\Message\ResponseInterface as Response;
	
	
		/******** CREATE USER ROLE *********/
	$app->post('/apis/user_roles/create', function ($request, $respo, $args) use ($app) {
    require_once("dbmodels/user.crud.php");
	$userCRUD = new UserCRUD(getConnection());
	 require_once("dbmodels/user_role.crud.php");
	$roleCRUD = new UserRoleCRUD(getConnection());
	$response = array();
    $response["error"] = true;
	$response["message"] = "";
	$name = $request->getParam('name');
	$description = $request->getParam('description');
	
	if(empty($name)){
		 $response["error"] = true;
        $response["message"] = "Role name can not be empty.";
		echoRespnse(200, $response);
		exit;
	}
	/********* Validate Authorization **********/
	/********* Only Super Admin and School Owner or Permitted User ********/
	$authUser = $roleCRUD->create($name, $description);
	if(!$authUser["error"]){
        $response["error"] = false;
		$response["id"] = $authUser["id"];
        $response["message"] = "New user role has been created! ";
	}else{
		 $response['error'] = true;
         $response['message'] = "Invalid request. Please use your authentication signature.";
         echoRespnse(200, $response);
		 return;
	}
	 echoRespnse(200, $response);
	/********* Validated Authorization ********/
	})->add($authenticate);
	

	
/*********** UPDATE EXISTING ROLE *************/
    $app->post('/apis/user_roles/update', function ($request, $respo, $args) use ($app) {
	 require_once("dbmodels/user.crud.php");
	$userCRUD = new UserCRUD(getConnection());
	 require_once("dbmodels/user_role.crud.php");
	$roleCRUD = new UserRoleCRUD(getConnection());
    
	$response = array();
    $response["error"] = false;
	
	$id = $request->getParam('item_id');
	if(empty($id)){
	 $response['error'] = true;
     $response['message'] = 'Please pass a valid role ID.';
     echoRespnse(200, $response);
	 return;
	}
    if(!$roleCRUD->doesIDExist($id)){
	 $response['error'] = true;
     $response['message'] = 'This role is not available to modify. Check back later. ';
     echoRespnse(200, $response);
	 return;
	}
	
	//Step 1: Take User Input
	$name = $request->getParam('name');
	$description = $request->getParam('description');
	//Step 2: Sanitize User Input
	if(empty($name)){
		 $response['error'] = true;
         $response['message'] = 'You must enter role name.';
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
	
	$res = $roleCRUD->update($id, $name, $description);
	if (!$res["error"]) {
        $response["error"] = false;
        $response["message"] = $name." has been updated successfully. ";
			 }else{
				  $response["error"] = true;
				  $response["info"] = $res["message"] ;
                  $response["message"] = "Failed to update role. Please try again.";
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
	
	$res = $roleCRUD->delete($qcode);
	if ($res) {
        $response["error"] = false;
        $response["message"] = "Role has been deleted successfully. ";
		$response["id"] = $qcode;
	    echoRespnse(200, $response);
		}else{
				  $response["error"] = true;
                  $response["message"] = "Failed to delete role. Please try again.";
				  echoRespnse(200, $response);
		}
	})->add($authenticate);
	
	
	
/************************ START OF USER ROLE VIEWS *********************/
	$app->get('/manage-user-roles', function (Request $request, Response $response, $args){
		require_once("dbmodels/user_role.crud.php");
        $roleCRUD = new UserRoleCRUD(getConnection());
		require_once("dbmodels/user.crud.php");
         $userCRUD = new UserCRUD(getConnection());
	/********** SERVER SESSION CHECK  ***********/
	if(isset($_SESSION["userID"]) && isset($_SESSION["email"]) && isset($_SESSION["api_key"])){
    $thisUser = $userCRUD->getUserByAPIKey($_SESSION["api_key"]);
	if ($thisUser != null && ($thisUser["role_id"] == 1) ) {
	 }else{
		$uri = $request->getUri()->withPath($this->router->pathFor('unauthorized'));
        //return $response->withRedirect((string)$uri);
	 }
	}else{
	   	$uri = $request->getUri()->withPath($this->router->pathFor('login'));
        return $response->withRedirect((string)$uri);
	}
	 /********** SERVER SESSION CHECK  ***********/
	 $adminMode = false;
	    $user_roles = $roleCRUD->getAllUserRoles();
		$data = array();
	    if (count($user_roles) > 0) {
		foreach ($user_roles as $res) {
        $schoolProfile = getUserRoleDetails($res["id"]);
		array_push($data, $schoolProfile);
		}
	    }
		$vars = [
			'page' => [
			'name' => '',
			'title' => 'Manage User Roles | Talank SMS',
			'description' => '',
			'data' => $data,
			'adminMode' => $adminMode
			]
		];
		return $this->view->render($response, 'list-user-role.twig', $vars);
	})->setName('manage-user-roles');
	
/************************ END OF USER ROLE VIEWS *********************/
/*************** CREATE USER ROLE ROUTE *******************/
	$app->get('/create-new-user-role', function (Request $request, Response $response, $args){
		$vars = [
			'page' => [
			'name' => '',
			'title' => 'Create New User Role',
			'description' => 'Talank SAS is a next generation school management and automation system. '
			]
		];
		return $this->view->render($response, 'create-new-user-role.twig', $vars);
	})->setName('create-new-user-role');
	
	/**************** EDIT ROLE ******************/
	$app->get('/edit-role/{qcode}', function ($request, $response, $args){
	require_once("dbmodels/user.crud.php");
    $userCRUD = new UserCRUD(getConnection());
	require_once("dbmodels/user_role.crud.php");
    $roleCRUD = new UserRoleCRUD(getConnection());
   
    //ADMIN ONLY
	$qcode = $request->getAttribute('qcode');
    if(empty($qcode)){
	$uri = $request->getUri()->withPath($this->router->pathFor('404'));
    return $response->withRedirect((string)$uri);
    }
	if(!$roleCRUD->doesIDExist($qcode)){
          $uri = $request->getUri()->withPath($this->router->pathFor('404')); 
          return $response->withRedirect((string)$uri);
    }
	$thisRole = $roleCRUD->getID($qcode);
	if($thisRole !== NULL){
		$vars = [
			'page' => [
			'name' => 'profile',
			'title' => 'Edit User Role',
			'description' => 'Edit User Role',
			'data' => $thisRole
			]
	];			
	return $this->view->render($response, 'edit-role.twig', $vars);
	}else{
		$uri = $request->getUri()->withPath($this->router->pathFor('404')); 
        return $response->withRedirect((string)$uri);
	}
	})->setName('edit-role');
	
	
	