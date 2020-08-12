<?php
/******** AUTH LOADER ********/
$app->get('/authenticating', function ($request, $response, $args){
	require_once("dbmodels/user.crud.php");
	require_once("dbmodels/utils.crud.php");
	 $userCRUD = new UserCRUD(getConnection());
    $utilCRUD = new UtilCRUD(getConnection());
	//ADMIN ONLY
	$redirectLink = "login";
	$status = "";
		/********** SERVER SESSION CHECK  ***********/
	if(isset($_SESSION["userID"]) && isset($_SESSION["email"]) && isset($_SESSION["api_key"])){
    $thisUser = $userCRUD->getUserByAPIKey($_SESSION["api_key"]);
	if ($thisUser != null) {
		$userRole = $thisUser["role_id"];
		$status = $thisUser["status"];
		switch($userRole){
			case 1:
			$redirectLink = "dashboard";
			break;
			
			case 2:
			$redirectLink = "dashboard";
			break;
			
			case 3:
			$redirectLink = "dashboard";
			break;
			
			case 4:
			$redirectLink = "dashboard";
			break;
			
			case 5:
			$redirectLink = "dashboard";
			break;
		}
	 }else{
		$uri = $request->getUri()->withPath($this->router->pathFor('login'));
        return $response->withRedirect((string)$uri);
	 }
	}else{
	   	$uri = $request->getUri()->withPath($this->router->pathFor('login'));
        //return $response->withRedirect((string)$uri);
	}
	 /********** SERVER SESSION CHECK  ***********/
		$vars = [
			'page' => [
			'name' => 'auth',
			'title' => 'Authenticating',
			'description' => 'Authenticating Account',
			'data' => $thisUser,
			'redirect' => $redirectLink,
			'status' => $status
			]
		];	
		
		return $this->view->render($response, 'authenticating.twig', $vars);
	})->setName('authenticating');
	
	
/****************** LOGIN USER ****************/
		$app->post('/auth/login', function ($request, $response, $args) use ($app) {
		    require_once("dbmodels/user.crud.php");
		    require_once("dbmodels/school.crud.php");
		    require_once("dbmodels/user_role.crud.php");
            $email = $request->getParam('email');
            $password = $request->getParam('password');
            $output = array();
			$output['error'] = true;
            $output['message'] = 'AuthService is authenticating your access.';
			$userCRUD = new UserCRUD(getConnection());
			$schoolCRUD= new SchoolCRUD(getConnection());
			$userRoleCRUD  = new UserRoleCRUD(getConnection());
			$date_created = date('Y-m-d H:i:s');
			/********** INPUT VALIDATION ***********/
			if(empty($email)){
		  $output['error'] = true;
         $output['message'] = 'Please enter your email address.';
         echoRespnse(200, $output);
		 return;}

	     if(!email_validation($email)) {
         $output['error'] = true;
         $output['message'] = 'Please enter a valid email address.';
         echoRespnse(200, $output);
		 return;}

	     if(empty($password)){
		  $output['error'] = true;
         $output['message'] = 'Please enter your password.';
         echoRespnse(200, $output);
		 return;}

		if (!$userCRUD->doesEmailExist($email)){
		 $output['error'] = true;
         $output['message'] = 'We did not find any account registered using this email.';
         echoRespnse(200, $output);
		 return;
		}
		/********** INPUT VALIDATION ***********/

		$userData = $userCRUD->getUserByEmail($email);
		if ($userData != NULL) {}else {
                $output['error'] = true;
                $output['message'] = 'Unable to find account owner record. Please try again.';
				echoRespnse(200, $output);
		        return;
            }

        if ($userCRUD->checkLogin($email, $password)) {
			$userCRUD->updateLastSeen($userData["id"], $date_created);
			 $current_user = $userData["id"];
			  $output['error'] = false;
			  $output['username'] = $userData["user_name"];
			  $tmp = getUserBasicDetails($userData["id"]);
			  $output['userData'] = $tmp;
			  //$output['userData'] = json_encode($tmp);
			  //$output['myProfile'] = getUserFullProfile($current_user, false);
			 /*********************************/
			 if($userData["status"] != NULL && $userData["status"] == "Blocked"){
				$output['error'] = true;
                $output['message'] = 'Your account has been blocked to access our Services. Please contact us via contact page if you have any concern related to your account.';
				if(isset($_SESSION)) {
                session_destroy();
                }
				echoRespnse(200, $output);
				exit;
			 }
			 if(session_id() == '' || !isset($_SESSION)) {
              session_start();
             }
             
            
			$_SESSION['app'] = BRAND_NAME;
			$_SESSION['userID'] = $userData["id"];
	        $_SESSION['first_name'] = $userData["first_name"];
	        $_SESSION['last_name'] = $userData["last_name"];
			$_SESSION['role_id'] = $userData["role_id"];
			$_SESSION['role_name'] = $userRoleCRUD->getNameByID($userData["role_id"]);
			$_SESSION['email'] = $userData["email"];
	        $_SESSION['status'] = $userData["status"];
	        $_SESSION['api_key'] = $userData["api_key"];
			$_SESSION['user_name'] = $userData["user_name"];

			$user_image = $userData["user_image"];
			if(empty($user_image)){
				$_SESSION['user_image'] = "images/profile.png";
			}else{
				$_SESSION['user_image'] = $userData["user_image"];
			}
			$_SESSION['school_id'] = $userData["school_id"];
			$_SESSION['school_name'] = $schoolCRUD->getNameByID($userData["school_id"]);
			//ADD THE SESSION PAGE HERE
			  $output['redirection'] = "";
			  if(isset($_SESSION['last_saved'])){
	           $output['redirection']  = $_SESSION["last_saved"];
               }
              $output['message'] = 'Welcome '.$userData["first_name"].'! You are authenticated successfully.';
            } else {
				$output['error'] = true;
                $output['message'] = 'Login failed. Invalid email address or password. Please check and try again.';
            }
            echoRespnse(200, $output);
        });		
        
        
    /*********************** UPDATE PASSWORD *********************/
	 $app->post('/users/reset_password', function ($request, $respo, $args) use ($app) {
            $response = array();
			require_once("dbmodels/user.crud.php");
			require_once 'dbmodels/PassHash.php';
		    $userCRUD = new UserCRUD(getConnection());

			$user_id = $request->getParam('member_id');
			$password = $request->getParam('password');
	
			if(empty($password) || strlen($password) < 6){
					$response["error"] = true;
				    $response["message"] = "Password must be at least 6 characters.";
					echoRespnse(200, $response);
					return;
			}
			$email = $userCRUD->getEmail($user_id);
			$password_hash = PassHash::hash($password);
				 $res = $userCRUD->updatePassword($user_id, $password_hash);
				if ($res) {
                $response["error"] = false;
				 $response["message"] = "Password for ".$email." has been updated successfully.";
            } else{
                $response["error"] = true;
                $response["message"] = "Oops! An error occurred updating Password. Try again.";
            }
            // echo json response
            echoRespnse(200, $response);
        });
		
		
	/*********************** UPDATE PASSWORD *********************/
	 $app->post('/apis/auth/creds/update', function ($request, $respo, $args) use ($app) {
            $response = array();
			require_once("dbmodels/user.crud.php");
			require_once 'dbmodels/PassHash.php';
		    $userCRUD = new UserCRUD(getConnection());

			$user_id = $request->getParam('member_id');
			$password = $request->getParam('pass1');
			$password2 = $request->getParam('pass2');
			$old_password = $request->getParam('old_password');
	
			if($password !== $password2){
					$response["error"] = true;
				    $response["message"] = "Your new password did not match.";
					echoRespnse(200, $response);
					return;
			}
			
			if(strlen($password) < 6){
					$response["error"] = true;
				    $response["message"] = "Your new password is too short. Use at least 6 characters.";
					echoRespnse(200, $response);
					return;
			}
			$email = $userCRUD->getEmail($user_id);
			if ($userCRUD->checkLogin($email, $old_password)) {
				$password_hash = PassHash::hash($password);
				 $res = $userCRUD->updatePassword($user_id, $password_hash);
				if ($res) {
                $response["error"] = false;
				 $response["message"] = "Your password has been updated successfully.";
            } else{
                $response["error"] = true;
                $response["message"] = "Oops! An error occurred updating Password. Try again.";
            }
			}else{
				  $response["error"] = true;
				  $response["message"] = "Your current password did not match. Please try again.".$user_id." vs ".$old_password;
			}
            // echo json response
            echoRespnse(200, $response);
        })->add($authenticate);
		
		?>