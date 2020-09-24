<?php
	use Psr\Http\Message\ServerRequestInterface as Request;
	use Psr\Http\Message\ResponseInterface as Response;
	
$app->get('/manage-users', function ($request, $response, $args){
	require_once("dbmodels/user.crud.php");
	require_once("dbmodels/utils.crud.php");
	$userCRUD = new UserCRUD(getConnection());
    $utilCRUD = new UtilCRUD(getConnection());	
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
    $data = $userCRUD->getAllUsers();
	$custom_data = array();
	if (count($data) > 0) {
			   foreach ($data as $row) {
			   $tmp = array();
               $tmp["id"] = $row["id"];
               $tmp["first_name"] = $row["first_name"];
               $tmp["last_name"] = $row["last_name"];
			   $tmp["status"] = $row["status"];
			   $tmp["date_created"] = $utilCRUD->getTimeDifference($row["date_created"]);
		       $tmp["user_name"] = $row["user_name"];
		       $tmp["email"] = $row["email"];
		      
			   array_push($custom_data, $tmp);
			   }
	}
		$vars = [
			'page' => [
			'name' => 'manage-users',
			'title' => 'Manage Users',
			'description' => 'List of all users',
			'data' => $custom_data,
			]
		];	
		
		return $this->view->render($response, 'admin_users_listing.twig', $vars);
	})->setName('manage-users');
	
	
	/**************** VIEW PROFILE ******************/
	$app->get('/profile/{qcode}', function ($request, $response, $args){
	require_once("dbmodels/user.crud.php");
    $userCRUD = new UserCRUD(getConnection());
    require_once("dbmodels/utils.crud.php");
    $utilCRUD = new UtilCRUD(getConnection());
    $countries = $utilCRUD->getAllCountries();
    //VERIFY QCODE
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
		$thisUser = getUserFullProfile($thisUser["id"]);
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
	})->setName('profile');
	
	
			
	// REGISTER ROUTE
	$app->get('/register-student', function (Request $request, Response $response, $args){
		require_once("dbmodels/master.crud.php");
        $masterCRUD = new MasterCRUD(getConnection());
		$classes = $masterCRUD->getAllClasses();
		$countries = $masterCRUD->getAllCountries();
		$vars = [
			'page' => [
			'name' => '',
			'title' => 'Register Student | Talank SMS',
			'description' => 'Talank SAS is a next generation school management and automation system. ',
			'classes' => $classes,
			'countries' => $countries,
			]
		];
		return $this->view->render($response, 'register-student.twig', $vars);
	})->setName('register-student');
	
	$app->get('/edit-profile/{qcode}', function($request, $response, $args) {
	//ADMIN AND SELF ONLY
	require_once("dbmodels/utils.crud.php");
	require_once("dbmodels/master.crud.php");
    $utilCRUD = new UtilCRUD(getConnection());
    $countries = $utilCRUD->getAllCountries();
	require_once("dbmodels/user.crud.php");
    $userCRUD = new UserCRUD(getConnection());
	$qcode = $request->getAttribute('qcode');
	$adminMode = false;
	
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
	$masterCRUD = new MasterCRUD(getConnection());
	$classes = $masterCRUD->getAllClasses();
	$countries = $masterCRUD->getAllCountries();
	$thisUser = getUserFullProfile($thisUser["id"]);
	
	$thisUserSchoolID = $thisUser["school_id"];
	/********** SERVER SESSION CHECK  ***********/
	if(isset($_SESSION["userID"]) && isset($_SESSION["email"]) && isset($_SESSION["api_key"])){
    $authUser = $userCRUD->getUserByAPIKey($_SESSION["api_key"]);
	if ($authUser != null) {
		if ($authUser["role_id"] == 1 ) {
			$adminMode = true;
		}else{
		if($authUser["role_id"] == 2 && $userCRUD->isSchoolAdmin($_SESSION["userID"], $thisUserSchoolID)) {
			$adminMode = true;
		}
		}
	 }else{
		$uri = $request->getUri()->withPath($this->router->pathFor('unauthorized'));
        return $response->withRedirect((string)$uri);
	 }
	}else{
	   	$uri = $request->getUri()->withPath($this->router->pathFor('login'));
        return $response->withRedirect((string)$uri);
	}
	 /********** SERVER SESSION CHECK  ***********/

	$vars = [
			'page' => [
			'name' => 'View profile',
			'title' => 'Edit Profile',
			'description' => 'Manage Profile',
			'user' => $thisUser,
			'classes' => $classes,
			'countries' => $countries,
			'adminMode' => $adminMode
			]
	];			
	return $this->view->render($response, 'edit-profile.twig', $vars);
	}else{
		$uri = $request->getUri()->withPath($this->router->pathFor('404')); 
        return $response->withRedirect((string)$uri);
	}
})->setName('edit-profile');



	$app->get('/manage-students', function (Request $request, Response $response, $args){
		require_once("dbmodels/user.crud.php");
        $userCRUD = new UserCRUD(getConnection());
        $students = array();
		$role_id = 3;
		$school_id = 1;
		$dataArr = $userCRUD->getAllUsers($role_id, $school_id);
	    if (count($dataArr) > 0) {
			   foreach ($dataArr as $row) {
			   $tmp = array();
			   $tmp = getStudentDetails($row["id"]);
			   //$tmp = getUserFullProfile($row["id"]);
			   array_push($students, $tmp);
			   }
	    }
		$vars = [
			'page' => [
			'name' => '',
			'title' => 'Manage Students | Talank SMS',
			'description' => '',
			'students' => $students
			]
		];
		return $this->view->render($response, 'list-students.twig', $vars);
	})->setName('manage-students');


 
	$app->get('/manage-parents', function (Request $request, Response $response, $args){
		require_once("dbmodels/user.crud.php");
        $userCRUD = new UserCRUD(getConnection());
        $students = array();
		$role_id = 5;
		$school_id = 1;
		$dataArr = $userCRUD->getAllUsers($role_id, $school_id);
	    if (count($dataArr) > 0) {
			   foreach ($dataArr as $row) {
			   $tmp = array();
			   $tmp = getStudentDetails($row["id"]);
			   //$tmp = getUserFullProfile($row["id"]);
			   array_push($students, $tmp);
			   }
	    }
		$vars = [
			'page' => [
			'name' => '',
			'title' => 'Manage Students | Talank SMS',
			'description' => '',
			'students' => $students
			]
		];
		return $this->view->render($response, 'list-parents.twig', $vars);
	})->setName('manage-students');


		
?>