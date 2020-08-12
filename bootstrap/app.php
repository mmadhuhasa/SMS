<?php
session_start();
$_SESSION["app_name"] = "Smart School Automation";
require __DIR__ . '/../slim_vendor/autoload.php';
use Respect\Validation\Validator as v;
// Application settings
$settings = require __DIR__ . '/../app/settings.php';
require __DIR__ . '/../app/dbmodels/Constants.php';
// New Slim app instance
$app = new Slim\App($settings);
$container = $app->getContainer();
// Register component on container
$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig('templates');
    $view->addExtension(new \Slim\Views\TwigExtension(
        $container['router'],
        $container['request']->getUri()
    ));
	$container['view']->addGlobal('session', $_SESSION);
    return $view;
};

$container['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        return $c['view']->render($response->withStatus(404), 'utilities/404.twig', [
            "myMagic" => "Let's roll"
        ]);
    };
};

$container['errorHandler'] = function ($c) {
    return function ($request, $response, $exception) use ($c) {
        return $response->withStatus(500)
            ->withHeader('Content-Type', 'text/html')
            ->write('Something went wrong!');
    };
};

$container['flash'] = function () {
    return new \Slim\Flash\Messages();
};

// Add our dependencies to the container
require __DIR__ . '/../app/dependencies.php';
require __DIR__ . '/../app/helper.php';
//App Specific Routes
require __DIR__ . '/../app/data_utils.php';
require __DIR__ . '/../app/utils_router.php';
require __DIR__ . '/../app/routes.php';
require __DIR__ . '/../app/auth_routes.php';
require __DIR__ . '/../app/test_routes.php';
require __DIR__ . '/../app/school_routes.php';
require __DIR__ . '/../app/notifications_router.php';
require __DIR__ . '/../app/user_routes.php';
require __DIR__ . '/../app/timetable_routes.php';
require __DIR__ . '/../app/timetable_view_routes.php';
require __DIR__ . '/../app/users_view_routes.php';
require __DIR__ . '/../app/classes_routes.php';
require __DIR__ . '/../app/subject_routes.php';
require __DIR__ . '/../app/assignment_routes.php';
require __DIR__ . '/../app/assignment_submissions_routes.php';
require __DIR__ . '/../app/section_routes.php';
require __DIR__ . '/../app/user_role_routes.php';
require __DIR__ . '/../app/topic_routes.php';
require __DIR__ . '/../app/posts_router.php';


function email_validation($str) {
    return (!preg_match( 
"^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^", $str)) 
        ? FALSE : TRUE; 
}

function checkSession()
{
    if(isset($_SESSION["userID"]) && isset($_SESSION["role_id"]) && isset($_SESSION["email"]) && isset($_SESSION["api_key"])){
		return true;
	}
	return false;
}

function checkAdminSession()
{
    require_once("../dbmodels/user.crud.php");
    $userCRUD = new UserCRUD(getConnection());
    if(isset($_SESSION["userID"]) && isset($_SESSION["email"]) && isset($_SESSION["api_key"])){
    /********** SERVER SESSION CHECK  ***********/
    $thisUser = $userCRUD->getUserByAPIKey($_SESSION["api_key"]);
	if ($thisUser != null && $thisUser["id"] == 1 && $thisUser["role_id"] == 1 ) {
	    return true;
	 }else{
		 return false;
	 }
	 /********** SERVER SESSION CHECK  ***********/
	}
	return false;
}