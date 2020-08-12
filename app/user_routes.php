<?php
	use Psr\Http\Message\ServerRequestInterface as Request;
	use Psr\Http\Message\ResponseInterface as Response;
	
	/**************** GET USERS LIST ***********/
		$app->post('/apis/users/list', function($request, $response, $args) {
	    require_once("dbmodels/user.crud.php");
        $userCRUD = new UserCRUD(getConnection());
	    $var_response = array();
		/************* TAKE PARAMETERS ************/
		$search_item = "";
		$role_id = 0;
		$school_id = 0;
		$var_response["filters"] = "";
		if(null !== $request->getParam('role_id')){
				$role_id = $request->getParam('role_id');
		}
		if(null !== $request->getParam('school_id')){
				$school_id = $request->getParam('school_id');
		}
		/************* TAKE PARAMETERS ************/
		try{
		$dataArr = $userCRUD->getAllUsers($role_id, $school_id);
		$data = array();
	    if (count($dataArr) > 0) {
			   foreach ($dataArr as $row) {
			   $tmp = array();
			   $tmp = getUserFullProfile($row["id"], false);
			   //$tmp = getUserBasicDetails($row["id"]);
			   array_push($data, $tmp);
			   }
	    }
		$var_response["error"] = false;
        $var_response["message"] = "We have found ".count($dataArr)." people.";
		$var_response["result"] = $data;
		}catch(Exception $e){
			$var_response["error"] = true;
            $var_response["message"] = "Failed to fetch data => ".$e->getMessage();
		}
        echoRespnse(200, $var_response);
        })->add($authenticate);
	
	function registerUserAccount($first_name, $last_name, $address, $city, $country, $dob, $mobile, $email, $password, $role_id, $school_id, $status){
	require_once("dbmodels/user.crud.php");
	$userCRUD = new UserCRUD(getConnection());
	require_once 'dbmodels/PassHash.php';
	require_once 'dbmodels/utils.crud.php';
	$utilCRUD = new UtilCRUD(getConnection());
	$output = array();
	
		$password_hash = PassHash::hash($password);
		    $api_key = $utilCRUD->generateApiKey();
			$user_name = $utilCRUD->createNewUsername(8);
			$status = "Active";
			$date_created = date('Y-m-d H:i:s');
			$ip_address = "";
			try{
			$ip_address = $_SERVER['REMOTE_ADDR'];
			}catch(Exception $e){

			}
			
           $res = $userCRUD->register($first_name, $last_name, $user_name, $address, $city, $country, $dob, $mobile, $email, $password, $role_id, $school_id, $date_created, $status, $api_key, $ip_address);
            if ($res["code"] == INSERT_SUCCESS) {
                $output["error"] = false;
               $output["message"] = $first_name." ".$last_name." is now registered.";
				$user_id = $res["id"];
				$output["id"] = $user_id;
				$output["user_name"] = $user_name;
			}else{
				$output["error"] = true;
			    $output["message"] = "Error while creating student profile.";
			}
	}
	
	
	function registerStudent($user_id, $admission_no, $roll_no, $class, $section, $religion, $caste, $blood_group, $admission_date){
	require_once("dbmodels/student.crud.php");
	$studentCRUD = new StudentCRUD(getConnection());
	$output = array();
            $res = $studentCRUD->create($user_id, $admission_no, $roll_no, $class, $section, $religion, $caste, $blood_group, $admission_date);
            if ($res["code"] == INSERT_SUCCESS) {
                $output["error"] = false;
			    $output["message"] = "Student profile has been created successfully.";
				$user_id = $res["id"];
				$output["id"] = $user_id;
			}else{
				$output["error"] = true;
			    $output["message"] = $res["message"];
			}
			
			return $output;
	}
	
	function registerGuardian($user_id, $occupation){
	require_once("dbmodels/parent.crud.php");
	$parentCRUD = new ParentCRUD(getConnection());
	$output = array();
            $res = $parentCRUD->create($user_id, $occupation);
            if ($res["code"] == INSERT_SUCCESS) {
                $output["error"] = false;
			    $output["message"] = "Guardian profile has been created successfully.";
				$user_id = $res["id"];
				$output["id"] = $user_id;
			}else{
				$output["error"] = true;
			    $output["message"] = "Error while creating guardian profile.";
			}
	}
	
	
	function registerTeacher($user_id, $designation, $religion, $role, $joining_date){
	require_once("dbmodels/teacher.crud.php");
	$teacherCRUD = new TeacherCRUD(getConnection());
	$output = array();
            $res = $teacherCRUD->create($user_id, $designation, $religion, $role, $joining_date);
            if ($res["code"] == INSERT_SUCCESS) {
                $output["error"] = false;
			    $output["message"] = "Teacher profile has been created successfully.";
				$user_id = $res["id"];
				$output["id"] = $user_id;
			}else{
				$output["error"] = true;
			    $output["message"] = "Error while creating teacher profile.";
			}
	}
	
		/********************  REGISTER A NEW USER **********************/
        $app->post('/auth/registration', function ($request, $response, $args) use ($app) {
            require_once("dbmodels/user.crud.php");
			require_once 'dbmodels/PassHash.php';
			require_once 'dbmodels/utils.crud.php';
		    $utilCRUD = new UtilCRUD(getConnection());
		    require_once 'dbmodels/notification.crud.php';
		    $notiCRUD = new NotificationCRUD(getConnection());
		    $userCRUD = new UserCRUD(getConnection());
		    require_once 'dbmodels/school.crud.php';
		    $schoolCRUD = new SchoolCRUD(getConnection());
			
            $output = array();
            $output["info"] = "";
            // reading post parameters
            $first_name = $request->getParam('first_name');
			$last_name = $request->getParam('last_name');
			$email = $request->getParam('email');
					
			$mobile = "";
			if(null !== $request->getParam('mobile')){
				$mobile = $request->getParam('mobile');
			}
			$address = "";
			if(null !== $request->getParam('address')){
				$address = $request->getParam('address');
			}
			$city = "";
			if(null !== $request->getParam('city')){
				$city = $request->getParam('city');
			}
			$country = "";
			if(null !== $request->getParam('country')){
				$country = $request->getParam('country');
			}
			$role_id = 0;
			if(null !== $request->getParam('role_id')){
				$role_id = $request->getParam('role_id');
			}else{
				$output["error"] = true;
				$output["message"] = "You must select a user role.";
				echoRespnse(200, $output);
				return;
			}
			$school_id = 1;
			if(null !== $request->getParam('school_id') && !empty($request->getParam('school_id')) && $request->getParam('school_id') > 0){
				$school_id = $request->getParam('school_id');
			}else{
				$output["error"] = true;
				$output["message"] = "You must associate a school with this account.";
				echoRespnse(200, $output);
				return;
			}
			$output["school_account"] = $school_id;
			$dob = "";
			if(null !== $request->getParam('dob')){
				$dob = $request->getParam('dob');
			}
			
				
			if(empty($first_name)){
					$output["error"] = true;
				    $output["message"] = "First name can not be empty.";
					echoRespnse(200, $output);
					return;
			}
			
			if(empty($email)){
					$output["error"] = true;
				    $output["message"] = "Please enter your email address.";
					echoRespnse(200, $output);
					return;
			}
			
		   if(empty($country)){
					$output["error"] = true;
				    $output["message"] = "Please select the country of your residence.";
					echoRespnse(200, $output);
					return;
			}
			
			$password = "";
			if(null !== $request->getParam('autogenerate_pass')){
				$autogenerate_pass = $request->getParam('autogenerate_pass');
				if($autogenerate_pass){
					$password = $utilCRUD->createNewUsername(6);
				}
			}else{
			$password_repeat = "";	
			if(null !== $request->getParam('password')){
				$password = $request->getParam('password');
			}else{
					$output["error"] = true;
				    $output["message"] = "Please enter a password for this account.";
					echoRespnse(200, $output);
					return;
			}
			
			if(strlen($password) < 6){
					$output["error"] = true;
				    $output["message"] = "Your password must contain at least six characters. We recommend you using a string password.";
					echoRespnse(200, $output);
					return;
			}
			if(null !== $request->getParam('password_repeat')){
				$password_repeat = $request->getParam('password_repeat');
			}else{
					$output["error"] = true;
				    $output["message"] = "Please repeat the password for this account.";
					echoRespnse(200, $output);
					return;
			}
			if(empty($password_repeat) || $password !== $password_repeat){
					$output["error"] = true;
				    $output["message"] = "Your password did not match. Please check again.";
					echoRespnse(200, $output);
					return;
			}
			}
			
			
			$password_hash = PassHash::hash($password);
		    $api_key = $utilCRUD->generateApiKey();
			$user_name = $utilCRUD->createNewUsername(8);
			$status = "Active";
			$date_created = date('Y-m-d H:i:s');
			$ip_address = "";
			try{
			$ip_address = $_SERVER['REMOTE_ADDR'];
			}catch(Exception $e){

			}
			
			
			 //$res = registerUserAccount($first_name, $last_name, $address, $city, $country, $dob, $mobile, $email, $password, $role_id, $school_id, $status);
            $res = $userCRUD->register($first_name, $last_name, $user_name, $address, $city, $country, $dob, $mobile, $email, $password, $role_id, $school_id, $date_created, $status, $api_key, $ip_address);
            if (!$res["error"]) {
                $output["error"] = false;
                if(null !== $request->getParam('auth_account_generate')){
			    $output["message"] = $first_name." ".$last_name." is now registered.";
                }else{
                $output["message"] = "Welcome ".$first_name." ".$last_name." ! You are now registsred.";
                }
				$user_id = $res["id"];
				$output["id"] = $user_id;
				$output["user_name"] = $user_name;
				
			
			switch($role_id){
			case 3:
		    $roll_no = $request->getParam('roll_no');
			$class = $request->getParam('class');
			$section = $request->getParam('section');
			$religion = $request->getParam('religion');
			$caste = $request->getParam('caste');
			$blood_group = $request->getParam('blood_group');
			$admission_date = $request->getParam('admission_date');
			$admission_no = "";
			if(null !== $request->getParam('admission_no')){
			    $admission_no = $request->getParam('admission_no');
			}
		
			$profileResult = registerStudent($user_id, $admission_no, $roll_no, $class, $section, $religion, $caste, $blood_group, $admission_date);
			 if ($profileResult["code"] == INSERT_SUCCESS) {
			    if(null !== $request->getParam('auth_account_generate')){
			    $output["message"] .= " A new student account has been created successfully.";
                }else{
                $output["message"] .= " Your new student account has been created successfully.";
                }
			 }else{
			     $output["message"] .= " Error while creating a student account.".$profileResult["message"];
			      $output["createDebug"] = $profileResult["message"];
			 }
			 $output["message"] .= $profileResult["message"];
			break;
			
			
			case 4:
		    $designation = $request->getParam('designation');
			$religion = $request->getParam('religion');
			$role = $request->getParam('role');
			$joining_date = $request->getParam('joining_date');
		
			$profileResult = registerTeacher($user_id, $designation, $religion, $role, $joining_date);
			 if ($profileResult["code"] == INSERT_SUCCESS) {
			    if(null !== $request->getParam('auth_account_generate')){
			    $output["message"] .= " A new teacher account has been created successfully.";
                }else{
                $output["message"] .= " Your new student account has been created successfully.";
                }
			 }else{
			      $output["message"] .= " Error while creating a teacher account.";
			      $output["createDebug"] = $profileResult["message"];
			 }
			 $output["message"] .= $profileResult["message"];
			break;
			
			
			case 5:
			$occupation = $request->getParam('occupation');
			$profileResult = registerGuardian($user_id, $occupation);
			 if ($profileResult["code"] == INSERT_SUCCESS) {
			    if(null !== $request->getParam('auth_account_generate')){
			    $output["message"] .= " A new parent account has been created successfully.";
                }else{
                $output["message"] .= " New parent account has been created successfully.";
                }
			 }else{
			      $output["message"] .= " Error while creating a parent account.".$profileResult["message"];
			      $output["createDebug"] = $profileResult["message"];
			 }
			 $output["message"] .= $profileResult["message"];
			break;
			
			}
			
			/*****************/	
			if(null !== $request->getParam('auth_account_generate') && $request->getParam('auth_account_generate')){	
			}else{
			$userData = $userCRUD->getUserByEmail($email);
	        if ($userData != NULL) {
			$_SESSION['app'] = BRAND_NAME;
			$_SESSION['status'] = $userData["status"];
			$_SESSION['userID'] = $userData["id"];
	        $_SESSION['first_name'] = $userData["first_name"];
	        $_SESSION['last_name'] = $userData["last_name"];
	        $_SESSION['role_id'] = $userData["role_id"];
	        $_SESSION['email'] = $userData["email"];
	        $_SESSION['api_key'] = $userData["api_key"];
			$_SESSION['user_name'] = $userData["user_name"];
			
			$_SESSION['school_id'] = $userData["school_id"];
			$_SESSION['school_name'] = $schoolCRUD->getNameByID($userData["school_id"]);
	        }
            }
            /*****************/
            $output["school_id"] = $_SESSION['school_id'];
			$output["school"] = $_SESSION['school_name'];	
            } else if ($res["code"] == INSERT_FAILURE) {
                $output["error"] = true;
                $output["message"] = "Oops! An error occurred while registering user";
            } else if ($res["code"] == ALREADY_EXIST) {
                $output["error"] = true;
                $output["message"] = "The email is already registered.";
            } 
            
            // echo json response
            echoRespnse(200, $output);
        })->add($authenticate);
		


    /******** UPDATE USER *********/
	$app->post('/apis/users/update', function ($request, $respo, $args) use ($app) {
	require_once("dbmodels/user.crud.php");
	/************* Either Admin, SuperAdmin of same school and parents/guardian can edit profile ****************/
	$userCRUD = new UserCRUD(getConnection());
	$response = array();
    $response["error"] = true;
    $response["admin_mode"] = 0;
     if (null != $request->getParam('admin_mode')) {
			 $response["admin_mode"] = $request->getParam('admin_mode');
	}
			 	
	$id = $request->getParam('user_id');
	$email = $request->getParam('email');
	if(!$userCRUD->doesIDExist($id)){
					$response["error"] = true;
				    $response["message"] = "We could not find this user account. Please try again. =>".$id." => ".$email;
					echoRespnse(200, $response);
					return;
	}
	
	/********* Validate Authorization **********/
	/********* Only Super Admin can create new school ********/
	$authUser = getCallerSnapshot($request, $respo);
	if(!$authUser["error"]){
		 if($authUser["caller_role"] == 1 || $authUser["caller_role"] == 2 || $authUser["caller_role"] == 5){
		 if(!$authUser["caller_role"] == 5 && !$userCRUD->isAlreadyRelated($id, $authUser["id"])) {
		 $response['error'] = true;
         $response['message'] = 'You are not authorized to perform this action.';
         echoRespnse(200, $response);
		 return;
		 }
		 if($authUser["caller_role"] == 2) {
			 if($userCRUD->getSchoolIDByID($authUser["id"]) == ($authUser["school_id"])){
		 $response['error'] = true;
         $response['message'] = 'You are not authorized to perform this action.';
         echoRespnse(200, $response);
		 return;
		 }
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
	
	$first_name = $request->getParam('first_name');
	$last_name = $request->getParam('last_name');
	$email = $request->getParam('email');
		if($userCRUD->isEmailExistExcept($email, $id)){
					$response["error"] = true;
				    $response["message"] = "This email address is already in use.";
					echoRespnse(200, $response);
					return;
			}
	$user_name = $request->getParam('user_name');
	if($userCRUD->isUsernameExistExcept($user_name, $id)){
					$response["error"] = true;
				    $response["message"] = "This username has been taken.";
					echoRespnse(200, $response);
					return;
			}
	$status = $request->getParam('status');
	$address = $request->getParam('address');
	$city = $request->getParam('city');
	$country = $request->getParam('country');
	$pincode = "";
	$dob = $request->getParam('dob');
	$mobile = $request->getParam('mobile');
	$image = "";
	$date_updated = date('Y-m-d H:i:s');
	 
	if(empty($first_name)){
					$response["error"] = true;
				    $response["message"] = "First name can not be empty.";
					echoRespnse(200, $response);
					return;
			}
			
			if(empty($email)){
					$response["error"] = true;
				    $response["message"] = "Please enter your email address.";
					echoRespnse(200, $response);
					return;
			}
			
		   if(empty($country)){
					$response["error"] = true;
				    $response["message"] = "Please select the country of your residence.";
					echoRespnse(200, $response);
					return;
			}
			
		 if(empty($id) || $id <= 0){
					$response["error"] = true;
				    $response["message"] = "Invalid Request.";
					echoRespnse(200, $response);
					return;
			}
			
	$res = $userCRUD->update($id, $first_name, $last_name, $user_name, $address, $city, $country, $pincode, $dob, $mobile, $email, $status, $date_updated);
	if (!$res["error"]) {
        $response["error"] = false;
        $response["message"] = "User profile has been updated successfully. ";
		$response["id"] = $id;
		
		try{
		 if (null != $request->getParam('admin_mode')) {
	     $userData = $userCRUD->getUserByEmail($email);
	     if ($userData != NULL && $userData["id"] == $_SESSION['userID']) {
	     	 /*********************************/
			$_SESSION['app'] = "offsetmyemissions"; 
			$_SESSION['status'] = $userData["status"];
			$_SESSION['userID'] = $userData["id"];
	        $_SESSION['first_name'] = $userData["first_name"];
	        $_SESSION['last_name'] = $userData["last_name"];
	        $_SESSION['role_id'] = $userData["role_id"];
	        $_SESSION['email'] = $userData["email"];
	        $_SESSION['api_key'] = $userData["api_key"];
			$_SESSION['user_name'] = $userData["user_name"];
			 /*********************************/
	 }
	}
		}catch(Exception $e){
		    $response["info"] = "Exception : ".$e->getMessage();
		}
	    echoRespnse(200, $response);
		}else{
				  $response["error"] = true;
                  $response["message"] = "Failed to update user profile. Please try again.".$res["message"];
				  echoRespnse(200, $response);
		}
	})->add($authenticate);
		
	
	/******** DELETE USER *********/
	$app->post('/apis/users/delete', function ($request, $respo, $args) use ($app) {
    require_once("dbmodels/user.crud.php");
	$userCRUD = new UserCRUD(getConnection());
	$response = array();
    $response["error"] = true;
	$id = $request->getParam('user_id');
	
	/********* Validate Authorization **********/
	/********* Only Super Admin and School Owner or Permitted User *******
	$authUser = getCallerSnapshot($request, $respo);
	if(!$authUser["error"]){
		 if($authUser["caller_role"] == 1 || $authUser["caller_role"] == 2){
		 if(!$authUser["caller_role"] == 1 && !$userCRUD->isSchoolAdmin($authUser["id"], $id)) {
		 $response['error'] = true;
         $response['message'] = 'You are not authorized to perform this action. You can not delete users outside schools you manage.';
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
	******** Validated Authorization ********/
	
	$res = $userCRUD->delete($id);		   
	if ($res) {
        $response["error"] = false;
        $response["message"] = "Account has been deleted successfully. ";
		$response["id"] = $id;
	    echoRespnse(200, $response);
		}else{
				  $response["error"] = true;
                  $response["message"] = "Failed to delete user account. Please try again.";
				  echoRespnse(200, $response);
		}
	})->add($authenticate);
	
	
	
	
	/******** UPLOAD USER IMAGE *********/
	$app->post('/apis/users/uploadPhoto', function ($request, $respo, $args) use ($app) {
    require_once("dbmodels/user.crud.php");
	$userCRUD = new UserCRUD(getConnection());
	$response = array();
    $response["error"] = true;
	$response["message"] = "";
	$id = $request->getParam('user_id');
	if(!empty($id)){
		if(!$userCRUD->doesIDExist($id)){
          $response["error"] = true;
        $response["message"] = "Could not find any user account. Please try again.";
		echoRespnse(200, $response);
		exit;
    }
	}else{
		$response["error"] = true;
        $response["message"] = "Invalid request.";
		echoRespnse(200, $response);
		exit;
	}
	/********* Validate Authorization **********/
	/********* Only Super Admin and School Owner or Permitted User ********/
	$authUser = getCallerSnapshot($request, $respo);
	if(!$authUser["error"]){
	$username = $userCRUD->getUserName($id);		
	//$res = uploadUserImage($request, $username);
    $res = uploadUserImage($request, $id);	
	if (!$res["error"]) {
        $response["error"] = false;
		$uploadCoverName = $res["url"];
		$response["username"] = $username;
        $response["message"] = "Great! ";
		if($userCRUD->updateImage($id, $uploadCoverName)){
         $response['message'] .= ' Profile photo has been updated.';
        }
		$response["id"] = $id;
	    echoRespnse(200, $response);
		}else{
				  $response["error"] = true;
                  $response["message"] = "Failed to upload photo. ".$res["message"].".";
				  echoRespnse(200, $response);
				  exit;
		}
		
	}else{
		 $response['error'] = true;
         $response['message'] = 'Invalid request. Please use your authentication signature.';
         echoRespnse(200, $response);
		 return;
	}
	/********* Validated Authorization ********/
	})->add($authenticate);
	
	
	function uploadUserImage($request, $qcode){
	require_once("dbmodels/user.crud.php");
    $userCRUD = new UserCRUD(getConnection());
	$response = array();
    $response["error"] = false;	
	$files = $request->getUploadedFiles();
    if (!empty($files['user_image'])) {
    try{
    $newCoverfile = $files['user_image'];
    $cover_file_type = "Unknown";
	if ($newCoverfile->getError() === UPLOAD_ERR_OK) {
    $uploadCoverName = $newCoverfile->getClientFilename();
	$uploadCoverName = explode(".", $uploadCoverName);
    $ext = array_pop($uploadCoverName);
    $ext = strtolower($ext);
    $uploadCoverName = $qcode. "." . $ext;
	$response['url'] = $uploadCoverName;
	$file_size = $newCoverfile->getSize();
	$cover_file_type = $newCoverfile->getClientMediaType();
	if(!$cover_file_type == "image/jpg" || !$cover_file_type == "image/jpeg" || !$cover_file_type == "image/jpeg"){
		 $response['error'] = true;
         $response['message'] = 'Please upload a png, jpg or jpeg image file as the Cover Image.';
         return $response;
	}
	
	if($cover_file_type > 1000000){
		 $response['error'] = true;
         $response['message'] = 'Upload a cover image of size not more than 1 MB.';
         //echoRespnse(200, $response);
		 return $response;
	}
	
	$fileToTest = "uploads/images/users/photos/$uploadCoverName";
	if(file_exists($fileToTest)) {
    unlink($fileToTest);
	}
    $newCoverfile->moveTo($fileToTest);
	}
    }catch(Exception $e){
        $response["message"] .= " Failed to upload photo.";
		//echoRespnse(200, $response);
		//exit;
         }
	}
	return $response;
	}
	
?>