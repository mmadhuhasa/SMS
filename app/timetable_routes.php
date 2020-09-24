<?php
	use Psr\Http\Message\ServerRequestInterface as Request;
	use Psr\Http\Message\ResponseInterface as Response;
	
	/******** GET TIMETABLE FOR CLASS *********/
	$app->post('/apis/timetable', function ($request, $respo, $args) use ($app) {
    require_once("dbmodels/user.crud.php");
	$userCRUD = new UserCRUD(getConnection());
	require_once("dbmodels/school.crud.php");
	$schoolCRUD = new SchoolCRUD(getConnection()); 
	$response = array();
    $response["error"] = true;
	$school_id = $request->getParam('school_id'); 
	$class_id = $request->getParam('class_id');
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
        
        
	  
	 $timetable = getTimetable(1, 4);
	if (true) {
        $response["error"] = false;
		$response["school_name"] = "School: ".$schoolCRUD->getNameByID($schoolID);
		$response["timetable"] = $timetable;
        $response["message"] = "Timetable served.";
		$response["id"] = $id;
	    echoRespnse(200, $response);
		}else{
				  $response["error"] = true;
                  $response["message"] = "Failed to fetch Timetable. Please try again.";
				  echoRespnse(200, $response);
		}})->add($authenticate);
/***********************
**************************/


/****************** CREATE PERIOD ****************/
$app->post('/apis/periods/create', function ($request, $response, $args) use ($app) {
			 $output = array();
		    require_once("dbmodels/period.crud.php");
			$periodCRUD = new PeriodCRUD(getConnection());
            $school_id = $request->getParam('school_id');
            $start_time = $request->getParam('start_time');
			$end_time = $request->getParam('end_time');
			$name = "";
			if(null !== $request->getParam('name')){
				$name = $request->getParam('name');
			}
			$output['error'] = true;
            $output['message'] = '';
			
			//$date_created = date('Y-m-d H:i:s');
			/********** INPUT VALIDATION ***********/
		if(empty($school_id)){
		$output['error'] = true;
        $output['message'] = 'Please select a school.';
        echoRespnse(200, $output);
		return;}

	 
	     if(empty($start_time)){
		  $output['error'] = true;
         $output['message'] = 'Please enter period start time.';
         echoRespnse(200, $output);
		 return;}
		 
		  if(empty($end_time)){
		  $output['error'] = true;
         $output['message'] = 'Please enter period end time.';
         echoRespnse(200, $output);
		 return;}

	    //Overlap Time Validation
		/********** INPUT VALIDATION ***********/
		$userData = $periodCRUD->create($school_id, $name, $start_time, $end_time);
		if ($userData["code"] == INSERT_SUCCESS) {
			$output['error'] = false;
                $output['message'] = 'New period has been created successfully.';
		}else {
                $output['error'] = true;
                $output['message'] = 'Failed to create period for timetable. Please try again.';
				echoRespnse(200, $output);
		        return;
            }

            echoRespnse(200, $output);
        })->add($authenticate);
	

/****************** UPDATE PERIOD ****************/
$app->post('/apis/periods/update', function ($request, $response, $args) use ($app) {
			 require_once("dbmodels/period.crud.php");
			$periodCRUD = new PeriodCRUD(getConnection());
            $school_id = $request->getParam('school_id');
            $start_time = $request->getParam('start_time');
			$end_time = $request->getParam('end_time');
			$name = "";
			if(null !== $request->getParam('name')){
				$name = $request->getParam('name');
			}
			$id = "";
			if(null !== $request->getParam('item_id')){
				$id = $request->getParam('item_id');
			}else{
			$output['error'] = true;
        $output['message'] = 'Please select a period.';
        echoRespnse(200, $output);
		return;
			}
			
           	$output = array();
			$output['error'] = true;
            $output['message'] = '';
			
			//$date_created = date('Y-m-d H:i:s');
			/********** INPUT VALIDATION ***********/
		if(empty($id)){
		$output['error'] = true;
        $output['message'] = 'Please select a period.';
        echoRespnse(200, $output);
		return;}

	 
	     if(empty($start_time)){
		  $output['error'] = true;
         $output['message'] = 'Please enter period start time.';
         echoRespnse(200, $output);
		 return;}
		 
		  if(empty($end_time)){
		  $output['error'] = true;
         $output['message'] = 'Please enter period end time.';
         echoRespnse(200, $output);
		 return;}
        
		$userData = $periodCRUD->update($id, $name, $start_time, $end_time);
		if ($userData) {
			$output['error'] = false;
            $output['message'] = 'Period has been updated successfully.';
		}else {
                $output['error'] = true;
                $output['message'] = 'Failed to update period. Please try again.';
				echoRespnse(200, $output);
		        return;
            }

            echoRespnse(200, $output);
        })->add($authenticate);		
		
		
/******** DELETE PERIOD *********/
	$app->post('/apis/periods/delete', function ($request, $respo, $args) use ($app) {
	require_once("dbmodels/school.crud.php");
    $schoolCRUD = new SchoolCRUD(getConnection());
	require_once("dbmodels/user.crud.php");
    $userCRUD = new UserCRUD(getConnection());
    require_once("dbmodels/period.crud.php");
    $periodCRUD = new PeriodCRUD(getConnection());
    
	$response = array();
    $response["error"] = true;
	$qcode = $request->getParam('item_id');
	if (empty($qcode)) {
        $response["error"] = true;
        $response["message"] = "Invalid request.";
	    echoRespnse(200, $response);
		return;
	}
	
    if(!$periodCRUD->isIDExists($qcode)){
	 $response['error'] = true;
     $response['message'] = 'This period is not available to modify at this moment. Check back later.';
     echoRespnse(200, $response);
	 return;
	}
	
	$id = $qcode;
	/********* Validate Authorization **********/
	/********* Only Super Admin and school owners can delete school ********/
	$authUser = getCallerSnapshot($request, $respo);
	$thisSchoolID = $periodCRUD->getSchoolIDByID($id);
	if(!$authUser["error"]){
		 if($authUser["caller_role"] == 1 || $authUser["caller_role"] == 2 || $authUser["caller_role"] == 4){
		  //If school admin verify   
		 if($authUser["caller_role"] == 2 && !$userCRUD->isSchoolAdmin($authUser["id"], $thisSchoolID)) {
		 $response['error'] = true;
         $response['message'] = 'You are not authorized to perform this action.';
         echoRespnse(200, $response);
		 return;
		 }
		 //If teacher check if he has the priviledge badge
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
	
	$res = $periodCRUD->delete($id);
	if ($res) {
        $response["error"] = false;
        $response["message"] = "Period has been deleted successfully. ";
		$response["id"] = $id;
	    echoRespnse(200, $response);
		}else{
				  $response["error"] = true;
                  $response["message"] = "Failed to delete period. Please try again.";
				  echoRespnse(200, $response);
		}
	})->add($authenticate);
	
	
/********************************************************/	

/****************** CREATE TIMETABLE ENTRY ****************/
$app->post('/apis/timetable/create', function ($request, $response, $args) use ($app) {
			 $output = array();
			require_once("dbmodels/timetable.crud.php");
			$timetableCRUD = new TimetableCRUD(getConnection());
			require_once("dbmodels/master.crud.php");
            $masterCRUD = new MasterCRUD(getConnection());
	        $classes = $masterCRUD->getAllClasses();
	        $countries = $masterCRUD->getAllCountries();
			
            $school_id = $request->getParam('school_id');
            $day_id = $request->getParam('day_id');
			$period_id = $request->getParam('period_id');
            $class_id = $request->getParam('class_id');
			$faculty = $request->getParam('faculty');
			$subject = $request->getParam('subject');
			$title = "";
			if(null !== $request->getParam('title')){
				$title = $request->getParam('title');
			}
			
           
			$output['error'] = true;
            $output['message'] = '';
			
			//$date_created = date('Y-m-d H:i:s');
			/********** INPUT VALIDATION ***********/
			if(empty($school_id)){
		  $output['error'] = true;
         $output['message'] = 'Please select a school.';
         echoRespnse(200, $output);
		 return;}

	     if(empty($class_id)){
		  $output['error'] = true;
         $output['message'] = 'Please select a class to assign timetable entry.';
         echoRespnse(200, $output);
		 return;}
		 
		 	if(empty($day_id)){
		  $output['error'] = true;
         $output['message'] = 'Please select a day of week to continue.';
         echoRespnse(200, $output);
		 return;}
	 
	     if(empty($period_id)){
		  $output['error'] = true;
         $output['message'] = 'Please select a period for timetable entry.';
         echoRespnse(200, $output);
		 return;}
		 
		  if(empty($subject)){
		  $output['error'] = true;
         $output['message'] = 'Please add a subject for this period.';
         echoRespnse(200, $output);
		 return;}
		 
		  if(empty($faculty)){
		  $output['error'] = true;
         $output['message'] = 'Please add a faculty for this period.';
         echoRespnse(200, $output);
		 return;}

	    //Overlap Time Validation
		/********** INPUT VALIDATION ***********/
        if($timetableCRUD->isPeriodForClassAssigned($school_id, $class_id, $period_id)){
                // $output['error'] = true;
                //$output['message'] = 'This period has already been assigned. Try updating the time table';
				//echoRespnse(200, $output);
		       // return;
        }

		$userData = $timetableCRUD->create($school_id, $class_id, $day_id, $period_id, $faculty, $subject, $title);
		if ($userData["code"] == INSERT_SUCCESS) {
			$output['error'] = false;
                $output['message'] = 'New timetable entry has been created successfully.';
		}else {
                $output['error'] = true;
                $output['message'] = 'Failed to create timetable entry. Please try again.';
				echoRespnse(200, $output);
		        return;
            }

            echoRespnse(200, $output);
        })->add($authenticate);
        
        
/****************** UPDATE TIMETABLE ENTRY ****************/
$app->post('/apis/timetable/update', function ($request, $response, $args) use ($app) {
			 $output = array();
			require_once("dbmodels/timetable.crud.php");
			
			/*
			   $output['error'] = true;
         $output['message'] = 'Pleasedqdqdq.';
         echoRespnse(200, $output);
				*/
				
			$timetableCRUD = new TimetableCRUD(getConnection());
			$id = $request->getParam('item_id');
            $class_id = $request->getParam('class_id');
			$day_id = $request->getParam('day_id');
			$period_id = $request->getParam('period_id');
			$faculty = $request->getParam('faculty');
			$subject = $request->getParam('subject');
			$title = "";
			if(null !== $request->getParam('title')){
				$title = $request->getParam('title');
			}
			
           
			$output['error'] = true;
            $output['message'] = '';
			
			//$date_created = date('Y-m-d H:i:s');
			/********** INPUT VALIDATION ***********/
		
		 if(empty($class_id)){
		  $output['error'] = true;
         $output['message'] = 'Please select a class to assign timetable entry.';
         echoRespnse(200, $output);
		 return;}
		 
		 	if(empty($day_id)){
		  $output['error'] = true;
         $output['message'] = 'Please select a day of week to continue.';
         echoRespnse(200, $output);
		 return;}
		 
	     if(empty($period_id)){
		  $output['error'] = true;
         $output['message'] = 'Please select a period for timetable entry.';
         echoRespnse(200, $output);
		 return;}
		 
		  if(empty($subject)){
		  $output['error'] = true;
         $output['message'] = 'Please add a subject for this period.';
         echoRespnse(200, $output);
		 return;}
		 
		  if(empty($faculty)){
		  $output['error'] = true;
         $output['message'] = 'Please add a faculty for this period.';
         echoRespnse(200, $output);
		 return;}

	    //Overlap Time Validation
		/********** INPUT VALIDATION **********
        if($timetableCRUD->isPeriodForClassAssigned($school_id, $class_id, $period_id)){
             $output['error'] = true;
                $output['message'] = 'This period has already been assigned. Try updating the time table';
				echoRespnse(200, $output);
		        return;
        }*/
        
        
		$userData = $timetableCRUD->update($id, $class_id, $day_id, $period_id, $faculty, $subject, $title);
		if ($userData) {
			$output['error'] = false;
            $output['message'] = 'Timetable entry has been updated successfully.';
		}else {
                $output['error'] = true;
                $output['message'] = 'Failed to update timetable entry. Please try again.';
				echoRespnse(200, $output);
		        return;
            }

            echoRespnse(200, $output);
        })->add($authenticate);		
		?>