<?php
	// Psr-7 Request and Response interfaces
	use Psr\Http\Message\ServerRequestInterface as Request;
	use Psr\Http\Message\ResponseInterface as Response;
	
	/*************** AUTO HTTP TO HTTPS *******************/
	$app->add(function (Request $request,  Response $response, $next) {
    if ($request->getUri()->getScheme() !== 'https') {
        $uri = $request->getUri()->withScheme("https")->withPort(null);
        return $response->withRedirect( (string)$uri );
    } else {
        return $next($request, $response);
    }
   });


	/*************** HOME ROUTE *******************/
	$app->get('/', function (Request $request, Response $response, $args){
		$vars = [
			'page' => [
			'name' => '',
			'title' => 'Home | Smart School Automation',
			//'title' => 'Home | Talank SMS'.BRAND_TITLE,
			'description' => 'Smart School Automation is a next generation school management and automation system. '
			]
		];
		return $this->view->render($response, 'home.twig', $vars);
	})->setName('home');
	
	
	/*************** DASHBOARD ROUTE *******************/
	$app->get('/school-dashboard', function (Request $request, Response $response, $args){
		//require_once("dbmodels/service.crud.php");
        //$serviceCRUD = new ServiceCRUD(getConnection());
		//$services = $serviceCRUD->getAllServices();
		
		$vars = [
			'page' => [
			'name' => 'schools',
			'title' => 'Dashboard | Talank SMS',
			'description' => 'Talank SAS is a next generation school management and automation system. '
			]
		];
		return $this->view->render($response, 'dashboard.twig', $vars);
	})->setName('school-dashboard');
	
	
	/*************** DASHBOARD ROUTE *******************/
	$app->get('/demo-login', function (Request $request, Response $response, $args){
		$helper = new Helper();
	    if($helper->validateMemberSession()){
		 // $routeLink = redirectToApp();
		 $routeLink = "authenticating";
          $uri = $request->getUri()->withPath($this->router->pathFor($routeLink));
         return $response->withRedirect((string)$uri);
	    }
	    session_destroy();
		session_unset();
		$vars = [
			'page' => [
			'name' => 'demos',
			'title' => 'Demo Login | Talank SMS',
			'description' => 'Talank SAS is a next generation school management and automation system. '
			]
		];
		return $this->view->render($response, 'demo-login.twig', $vars);
	})->setName('demo-login');
	
	
	$app->get('/dashboard', function (Request $request, Response $response, $args){
		require_once("dbmodels/user.crud.php");
        $userCRUD = new UserCRUD(getConnection());
		require_once("dbmodels/school.crud.php");
		$schoolCRUD = new SchoolCRUD(getConnection());
		
		/********** SERVER SESSION CHECK  ***********/
	   if(isset($_SESSION["userID"]) && isset($_SESSION["email"]) && isset($_SESSION["api_key"])){
		$thisUser = $userCRUD->getUserByAPIKey($_SESSION["api_key"]);
		if ($thisUser != null && $thisUser["id"] > 0) {
			$accessorID = $thisUser["id"];
			$accessorName = $thisUser["first_name"];
			$accessorRole = $thisUser["role_id"];
			$schoolID = $thisUser["school_id"];
			$schoolName = $schoolCRUD->getNameByID($schoolID);

			$pageTitle = "Dashboard";
			$statsData = array();
			
			switch($accessorRole){
case 1:
	$pageTitle = "Admin Dashboard";
	$numStudents = $userCRUD->getNumUsersIn($schoolID, 3);
	$numTeachers = $userCRUD->getNumUsersIn($schoolID, 4);
	$numParents = $userCRUD->getNumUsersIn($schoolID, 5);
	$statsData["numStudents"] = $numStudents;
	$statsData["numTeachers"] = $numTeachers;
	$statsData["numParents"] = $numParents;
	$statsData["numTotalAssignments"] = 0;
	$statsData["numTotalEContents"] = 0;
break;

	case 2:
		$pageTitle = "Admin Dashboard";
		$numStudents = $userCRUD->getNumUsersIn($schoolID, 3);
		$numTeachers = $userCRUD->getNumUsersIn($schoolID, 4);
		$numParents = $userCRUD->getNumUsersIn($schoolID, 5);
		$statsData["numStudents"] = $numStudents;
		$statsData["numTeachers"] = $numTeachers;
		$statsData["numParents"] = $numParents;
		$statsData["numTotalAssignments"] = 0;
		$statsData["numTotalEContents"] = 0;
	break;

	case 3:
		$pageTitle = "Student Dashboard";
		break;

	case 4:
		$pageTitle = "Teacher Dashboard";
		$statsData["avgAttendance"] = 77.5;
		$statsData["numTotalAssignments"] = 0;
		$statsData["numTotalEContents"] = 0;
	break;

	case 5:
		$pageTitle = "Parent Dashboard";
		break;

	default:
			}

			$vars = [
			'page' => [
			'name' => 'dashboard',
			'title' => $pageTitle.' | Talank SMS',
			'accessorRole' => $accessorRole,
			'statsData' => $statsData,
			'description' => 'Talank SAS is a next generation school management and automation system. '
			]
		];
		return $this->view->render($response, 'dashboard.twig', $vars);
		 }else{
			$uri = $request->getUri()->withPath($this->router->pathFor('login'));
			return $response->withRedirect((string)$uri);
		 }
		}else{
			$uri = $request->getUri()->withPath($this->router->pathFor('login'));
			return $response->withRedirect((string)$uri);
		}
		 /********** SERVER SESSION CHECK  ***********/
	})->setName('dashboard');
	
	
	$app->get('/manage-schools', function (Request $request, Response $response, $args){
		require_once("dbmodels/school.crud.php");
        $schoolCRUD = new SchoolCRUD(getConnection());
		$schools = $schoolCRUD->getAllSchools();
		$data = array();
	    if (count($schools) > 0) {
		foreach ($schools as $res) {
        $schoolProfile = getSchoolDetails($res);
		array_push($data, $schoolProfile);
		}
	    }
		$vars = [
			'page' => [
			'name' => '',
			'title' => 'Manage Schools | Talank SMS',
			'description' => '',
			'schools' => $data
			]
		];
		return $this->view->render($response, 'list-schools.twig', $vars);
	})->setName('manage-schools');

	
	// REGISTER ROUTE
	$app->get('/register-school', function (Request $request, Response $response, $args){
	require_once("dbmodels/utils.crud.php");
    $utilCRUD = new UtilCRUD(getConnection());
    $countries = $utilCRUD->getAllCountries();
		$vars = [
			'page' => [
			'name' => 'schools',
			'title' => 'Register School | Talank SMS',
			'countries' => $countries,
			'description' => 'Talank SAS is a next generation school management and automation system. '
			]
		];
		return $this->view->render($response, 'register-school.twig', $vars);
	})->setName('register-school');
	
	
	// UPDATE SCHOOL ROUTE
	$app->get('/edit-school/{qcode}', function (Request $request, Response $response, $args){
	require_once("dbmodels/utils.crud.php");
    $utilCRUD = new UtilCRUD(getConnection());
    $countries = $utilCRUD->getAllCountries();
	require_once("dbmodels/school.crud.php");
    $schoolCRUD = new SchoolCRUD(getConnection());
	$qcode = $request->getAttribute('qcode');
    if(empty($qcode)){
	$uri = $request->getUri()->withPath($this->router->pathFor('select-document-type'));
    return $response->withRedirect((string)$uri);
    }
	 if(!$schoolCRUD->isQCodeExists($qcode)){
          $uri = $request->getUri()->withPath($this->router->pathFor('unauthorized')); 
          return $response->withRedirect((string)$uri);
    }
	$thisSchool = $schoolCRUD->getByQCode($qcode);
		$vars = [
			'page' => [
			'name' => 'schools',
			'title' => 'Update School | Talank SMS',
			'countries' => $countries,
			'school' => $thisSchool,
			'description' => 'Talank SAS is a next generation school management and automation system. '
			]
		];
		return $this->view->render($response, 'edit-school.twig', $vars);
	})->setName('edit-school');
	
		$app->get('/register-parent', function (Request $request, Response $response, $args){
		require_once("dbmodels/user.crud.php");
        $userCRUD = new UserCRUD(getConnection());
		$schools = $userCRUD->getAllUsers(2, 1);
		$vars = [
			'page' => [
			'name' => '',
			'title' => 'Manage Teachers | Talank SMS',
			'description' => '',
			'schools' => $schools
			]
		];
		return $this->view->render($response, 'register_parent.twig', $vars);
	})->setName('register-parent');
	
	
		$app->get('/manage-teachers', function (Request $request, Response $response, $args){
		require_once("dbmodels/user.crud.php");
        $userCRUD = new UserCRUD(getConnection());
		// $schools = $userCRUD->getAllUsers(2, 1);
		$teachers = array();
		$role_id = 4;
		$school_id = 1;
		$dataArr = $userCRUD->getAllUsers($role_id, $school_id);
	    if (count($dataArr) > 0) {
			   foreach ($dataArr as $row) {
			   $tmp = array();
			   $tmp = getTeacherDetails($row["id"]);
			   //$tmp = getUserFullProfile($row["id"]);
			   array_push($teachers, $tmp);
			   }
	    }

		$vars = [
			'page' => [
			'name' => '',
			'title' => 'Manage Teachers | Talank SMS',
			'description' => '',
			'teachers' => $teachers
			]
		];
		return $this->view->render($response, 'list-teachers.twig', $vars);
	})->setName('manage-teachers');
	
	
		// REGISTER ROUTE
	$app->get('/register-teacher', function (Request $request, Response $response, $args){
		$vars = [
			'page' => [
			'name' => 'users',
			'title' => 'Register Teacher | Talank SMS',
			'description' => 'Talank SAS is a next generation school management and automation system. '
			]
		];
		return $this->view->render($response, 'register-teacher.twig', $vars);
	})->setName('register-teacher');
	
	
	
		$app->get('/manage-permissions[/{user_name}]', function (Request $request, Response $response, $args){
		require_once("dbmodels/user.crud.php");
        $userCRUD = new UserCRUD(getConnection());
        require_once("dbmodels/master.crud.php");
        $masterCRUD = new MasterCRUD(getConnection());
		require_once("dbmodels/app_module.crud.php");
        $moduleCRUD = new AppModuleCRUD(getConnection());
        
        //Get the user ID
        $user_id = 1;
        
        $user_permissions = array();
		$modules = $moduleCRUD->getAllAppModules();	
		if (count($modules) > 0) {
		foreach ($modules as $res) {
		$module_id = $res["id"];
		$user_permissions["module"] = $module_id;
		$allPermissions = $moduleCRUD->getAppModulesFor($module_id);
		$user_permissions["module"]["permissions"] = $allPermissions;
		if (count($allPermissions) > 0) {
		foreach ($allPermissions as $permissions) {
		$permission_id = $permissions["id"];
		if($moduleCRUD->getUserPermissionRecordID($user_id, $permission_id) > 0){
		     $tmp["module"]["permissions"]["value"] = $moduleCRUD->isPermitted($user_id, $permission_id) ? 1: 0;
		}else{
		   $tmp["module"]["permissions"]["value"] = 0; 
		}
		//array_push($data, $companyProfile);
		}
	    }
		}
	    }
	    
		$title = "Manage User Permissions";	
		$vars = [
			'page' => [
			'name' => 'timetable',
			'title' => $title.' | Talank SMS',
			'description' => '',
			'modules' => $modules,
			'user_permissions' => $user_permissions,
			'$title' => $title
			]
		];
		return $this->view->render($response, 'manage-permissions.twig', $vars);
	})->setName('manage-permissions');
	
	
	
		/*	*/
	$app->get('/school-calendar', function (Request $request, Response $response, $args){
		$vars = [
			'page' => [
			'name' => 'school-calendar',
		    'title' => 'School Calendar | Talank SMS',
			'description' => 'Talank SAS is a next generation school management and automation system. '
			]
		];
		return $this->view->render($response, 'school-calendar.twig', $vars);
	})->setName('school-calendar');
	
	
	
	$app->get('/attendance', function (Request $request, Response $response, $args){
		$vars = [
			'page' => [
			'name' => 'attendance',
			'title' => 'Attendance | Talank SMS',
			'description' => 'Talank SAS is a next generation school management and automation system. '
			]
		];
		return $this->view->render($response, 'attendance.twig', $vars);
	})->setName('attendance');
	


	// ABOUT ROUTE
	$app->get('/login', function (Request $request, Response $response, $args){
	    $helper = new Helper();
	    if($helper->validateMemberSession()){
		  //$routeLink = redirectToApp();
		  $routeLink = "authenticating";
          $uri = $request->getUri()->withPath($this->router->pathFor($routeLink));
         return $response->withRedirect((string)$uri);
	    }
	    session_destroy();
		session_unset();
		$vars = [
			'page' => [
			'name' => 'login',
		    'title' => 'Login | Talank SMS',
			'description' => 'Talank SAS is a next generation school management and automation system. '
			],
		];
		return $this->view->render($response, 'login.twig', $vars);

	})->setName('login');
		
		
	// NOT FOUND ROUTE
	$app->get('/maintenance', function (Request $request, Response $response, $args){
	    $_SESSION["last_saved"] = "";
		$vars = [
			'page' => [
			'title' => 'Under Maintenance | Talank SMS',
			'description' => 'Talank SAS is a next generation school management and automation system. '
			],
		];	
		return $this->view->render($response, 'utilities/maintenance.twig', $vars);
	})->setName('maintenance');
	
	
	// NOT FOUND ROUTE
	$app->get('/404', function (Request $request, Response $response, $args)   {
	    $_SESSION["last_saved"] = "";
		$vars = [
			'page' => [
			'title' => 'Page Not Found | Talank SMS',
			'description' => 'Talank SAS is a next generation school management and automation system. '
			],
		];	
		return $this->view->render($response, 'utilities/404.twig', $vars);
	})->setName('404');
	
	
	// unauthorized ROUTE
	$app->get('/unauthorized', function (Request $request, Response $response, $args)   {
	    	$_SESSION["last_saved"] = "";
		$vars = [
			'page' => [
			'title' => 'Register Student | Talank SMS',
			'description' => 'Talank SAS is a next generation school management and automation system. '
			],
		];	
		return $this->view->render($response, 'unauthorized.twig', $vars);
	})->setName('unauthorized');


	// LOGOUT ROUTE
	$app->get('/logout', function (Request $request, Response $response, $args){
		session_destroy();
		session_unset();
		$vars = [
			'page' => [
			'title' => 'Login | Talank SMS',
			'description' => 'Talank SAS is a next generation school management and automation system. '
			],
		];
   $uri = $request->getUri()->withPath($this->router->pathFor('login'));
   return $response->withRedirect((string)$uri);
	});
	
function getConnection() {
$host = 'localhost';
// $db   = 'schoolautomationdb';
$port = '3306';
$db   = 'schoolasdb';
$user = 'talankdb';
$pass = 'talankdb';
$charset = 'utf8';
$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
$pdo = new PDO($dsn, $user, $pass, $opt);
	return $pdo;
}

/**
 * Adding Middle Layer to authenticate every request
 * Checking if the request has valid api key in the 'Authorization' header
 */

    $authenticate = function($request, $response, $next) {
	require_once("dbmodels/user.crud.php");
	$userCRUD = new UserCRUD(getConnection());
    $headers = $request->getHeaders();
    $output = array();
    $authArr = $request->getHeader("Authorization");
	$api_key = "";
	$signature = "";
	if(empty($authArr)){
		$output["error"] = true;
            $output["message"] = "Authorization token not found with request.";
            echoRespnse(401, $output);
			$request = $request->withAttribute('error', true);
            return $response;
	}else{
		$api_key = $authArr[0];
		$signature = $request->getUri();
	}
	$output["api_key"] = $api_key;
    if (isset($api_key) && !empty($api_key)) {
        if (!$userCRUD->isValidApiKey($api_key)) {
            $output["error"] = true;
            $output["message"] = "Access Denied. Invalid Api key.";
            echoRespnse(401, $output);
			$request = $request->withAttribute('error', true);
            return $response;
        } else {
			addUsageCount($api_key, $signature);
			$output["error"] = false;
            $output["message"] = "Access Granted.";
			$response = $next($request, $response);
    return $response->withHeader('Content-type', 'application/json');
        }
    } else {
        // api key is missing in header
        $output["error"] = true;
        $output["message"] = "Api key is misssing";
        echoRespnse(400, $output);
		$request = $request->withAttribute('error', true);
        return $response;
    }
};

function addUsageCount($api_key, $signature) {
	  require_once("dbmodels/user.crud.php");
	  $userCRUD = new UserCRUD(getConnection());
	  $userCRUD->addToUsage($api_key, $signature);
}

function redirectToApp() {
	     switch($_SESSION["role_id"]){
	     case 1:
	     return "admin-dashboard";
         break;
         
         case 2:
	     return "school-dashboard";
         break;
         
         case 3:
	     return "admin-dashboard";
         break;
         
         case 4:
	     return "teacher-dashboard";
         break;
         
         case 5:
	     return "parent-dashboard";
         break;
         
         default:
	     return "login";
	  }
}


function getCallerSnapshot($request, $respo) {
	  require_once("dbmodels/user.crud.php");
	  $userCRUD = new UserCRUD(getConnection());
	  //$userCRUD->addToUsage($api_key, $signature);
	  
	  $response = array();
    $response["error"] = true;
	$headers = $request->getHeaders();
    $authArr = $request->getHeader("Authorization");
	$api_key = "";
	if(empty($authArr)){
		$response["error"] = true;
            $response["message"] = "Authorization token not found with request.";
            //echoRespnse(401, $response);
			//$request = $request->withAttribute('error', true);
            return $response;
	}else{
		$api_key = $authArr[0];
		$response["error"] = false;
		$response["api_key"] = $api_key;
		$response["caller_role"] = $userCRUD->getRoleByAPIKey($api_key);
		$response["caller_role_name"] = $userCRUD->getRoleName($response["caller_role"]);
		$userRow = $userCRUD->getUserByAPIKey($api_key);
		if($userRow !== null){
		$response["caller_id"] = $userRow["id"];
		$response["caller_school_id"] = $userRow["school_id"];
		}
	}
	return $response;
}


/******** API TESTER *********/
	$app->post('/apitester', function ($request, $respo, $args) use ($app) {
    require_once("dbmodels/user.crud.php");
	$userCRUD = new UserCRUD(getConnection());
	require_once("dbmodels/school.crud.php");
	$schoolCRUD = new SchoolCRUD(getConnection());
	require_once("dbmodels/post.crud.php");
	$postCRUD = new PostCRUD(getConnection());
	$response = array();
    $response["error"] = true;
	$id = $request->getParam('id');
	
	$headers = $request->getHeaders();
    $authArr = $request->getHeader("Authorization");
	$api_key = "";
	if(empty($authArr)){
		$response["error"] = true;
            $response["message"] = "APITester => Authorization token not found with request.";
            echoRespnse(401, $response);
			$request = $request->withAttribute('error', true);
            return $response;
	}else{
		$api_key = $authArr[0];
		$response["api_key"] = $api_key;
		$response["caller_role"] = $userCRUD->getRoleByAPIKey($api_key);
		$response["caller_role_name"] = $userCRUD->getRoleName($response["caller_role"]);
	}
	
	$userRow = $userCRUD->getUserByAPIKey($api_key);
	$schoolID = "School ID: ".$userRow["school_id"];
	
	
	
	   require_once("dbmodels/master.crud.php");
        $masterCRUD = new MasterCRUD(getConnection());
		require_once("dbmodels/app_module.crud.php");
        $moduleCRUD = new AppModuleCRUD(getConnection());
        
        //Get the user ID
        $user_id = 1;
        /*
        $user_permissions = array();
		$modules = $moduleCRUD->getAllAppModules();	
		if (count($modules) > 0) {
		$tmp = array();    
		foreach ($modules as $res) {
		$module_id = $res["id"];
		$tmp["module_id"] = $module_id;
		$tmp["module_title"] = $res["title"];
		$allPermissions = $moduleCRUD->getAppModulesFor($module_id);
		$tmp["permissions"] = array();
		if (count($allPermissions) > 0) {
		foreach ($allPermissions as $permissions) {
		 $permitRow = array();   
		$permission_id = $permissions["id"];
		 $permitRow["id"] = $permissions["id"];
		 $permitRow["title"] = $permissions["title"];
		 $permitRow["description"] = $permissions["description"];
		  $permitRow["value"] = 0;
		if($moduleCRUD->getUserPermissionRecordID($user_id, $permission_id) > 0){
		     $permitRow["value"] = $moduleCRUD->isPermitted($user_id, $permission_id) ? 1: 0;
		}
		array_push($tmp["permissions"], $permitRow);
		}
	    }
	    array_push($user_permissions, $tmp);
		}
	    }
	  */
	  
	 //$timetable = getTimetable(1, 4);
	  
	$res = $userCRUD->getUsage($api_key);	
    $snap = getCallerSnapshot($request, $respo);
	if (true) {
        $response["error"] = false;
		$response["result"] = "Usage/Api Hits: ".$res;
		//$response["post"] = getPostDetails(313, 1, false, true, true);
		//$response["posts"] = $postCRUD->getAllPosts();
		//$response["user_permissions"] = $user_permissions;
		//$response["timetable"] = $timetable;
		$response["student"] = getStudentDetails(4, false);
        $response["message"] = "This is cool debug place. ";
		$response["id"] = $id;
	    echoRespnse(200, $response);
		}else{
				  $response["error"] = true;
                  $response["message"] = "Failed to do whatever operation. Please try again.";
				  echoRespnse(200, $response);
		}})->add($authenticate);
/***********************
END OF TESTER
**************************/



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
			   $tmp["date_created"] = $utilCRUD->getFormalDate($row["date_created"]);
			   $tmp["sender_id"] = $row["sender_id"];
			   $tmp["sender_name"] = $userCRUD->getNameByID($row["sender_id"]);
			   //$tmp["sender_qcode"] = $userCRUD->getUserName($row["sender_id"]);
			   $tmp["link"] = "";
			   switch($tmp["data_title"]){
				   case "User":
				   $tmp["link"] = "view-profile/";
				   if(!empty($tmp["data_id"])){ $tmp["link"] .= $tmp["data_id"];}
				   break;
				   
				   case "Service":
				   $tmp["link"] = "view-service-details/";
				   if(!empty($tmp["data_id"])){ $tmp["link"] .= $tmp["data_id"];}
				   break;
				   
				   case "Property":
				   $tmp["link"] = "property-details/";
				   if(!empty($tmp["data_id"])){ $tmp["link"] .= $tmp["data_id"];}
				   break;
				   
				   case "Portfolio":
				   $tmp["link"] = "view-portfolio/";
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
}


function echoRespnse($status_code, $response) {
    //$app = \Slim\Slim::getInstance();
    echo json_encode($response);
}