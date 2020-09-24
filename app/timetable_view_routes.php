<?php
	// Psr-7 Request and Response interfaces
	use Psr\Http\Message\ServerRequestInterface as Request;
	use Psr\Http\Message\ResponseInterface as Response;
		/*************** START OF TIMETABLE VIEWS API ****************/
	$app->get('/time-table/{schoolCode}', function (Request $request, Response $response, $args){
		require_once("dbmodels/user.crud.php");
        $userCRUD = new UserCRUD(getConnection());
        require_once("dbmodels/master.crud.php");
        $masterCRUD = new MasterCRUD(getConnection());
		$schools = $userCRUD->getAllUsers(2, 1);
		require_once("dbmodels/period.crud.php");
		$periodCRUD = new PeriodCRUD(getConnection());
		require_once("dbmodels/timetable.crud.php");
        $timetableCRUD = new TimetableCRUD(getConnection());
		 
		require_once("dbmodels/school.crud.php");
        $schoolCRUD = new SchoolCRUD(getConnection()); 
		
		$schoolID = 1;
		$class = 4;
		$days = $masterCRUD->getAllDays();
		
		$qcode = $request->getAttribute('schoolCode');
    if(empty($qcode)){
	$uri = $request->getUri()->withPath($this->router->pathFor('404'));
    //return $response->withRedirect((string)$uri);
    }
	if(!$schoolCRUD->isCodeValid($qcode)){
          $uri = $request->getUri()->withPath($this->router->pathFor('404'));
         // return $response->withRedirect((string)$uri);
    }
	$thisSchool = $schoolCRUD->getByQCode($qcode);
		$schoolID = $thisSchool["id"];
	
		$periods = $periodCRUD->getPeriods($schoolID);	
		$timetable = array();
		//$timetable["routine"] = array();
		/************* LIST EDUCATION ************
		if (count($periods) > 0) {
			    foreach ($periods as $period_row) {
			     $timetable["start_time"] = $period_row["start_time"];  
			     $timetable["end_time"] = $period_row["end_time"];  
			    //$fullItem = array();
			   $fullItem = getPeriodDetails($schoolID, $class, $period_row["id"]);
			   //getPeriodDetails($school_id, $class_id, $day_id, $period_id)
			   array_push($timetable["routine"], $fullItem);
	           }
	    }
			************/
			    
		//echo json_encode($timetable, true);	
		
		$timetable = getTimetable($schoolID, 4);
		$title = "Time Table";
		$html = "This page is under construction.";
		/*
		$html .= "<div class='table-responsive m-t-20'><table class='table table-filter table-hover m-b-0'>";
$html .= "<tr>";
$html .= "<th>Time</th>";
$html .= "<th>Monday</th>";
$html .= "<th>Tuesday</th>";
$html .= "<th>Wednesday</th>";
$html .= "<th>Thursday</th>";
$html .= "<th>Friday</th>";
$html .= "<th>Saturday</th>";
$html .= "<th>Sunday</th>";
$html .= "</tr>";
for($i=0; $i<count($periods);$i++){

    $html .= "<tr>";
    $html .= "<td>".$periods[$i]["start_time"].' - ';
    $html .= $periods[$i]["end_time"];
    $html .= "</td>";
    
    if($i==13){
        $html .= "<td colspan='7' align='center'> REST </td>";
    }
    else{
        $mondayData = $timetableCRUD->getPeriodRow($school_id, $class_id, $periods[$i]["id"], 1);
        $tuesData = $timetableCRUD->getPeriodRow($school_id, $class_id, $periods[$i]["id"], 2);
        $wedData = $timetableCRUD->getPeriodRow($school_id, $class_id, $periods[$i]["id"], 3);
        $thuData = $timetableCRUD->getPeriodRow($school_id, $class_id, $periods[$i]["id"], 4);
        $html .= "<td> ".$mondayData["faculty"]." ".$mondayData["subject"]."</td>";
        $html .=  "<td> ".$tuesData["faculty"]." ".$tuesData["subject"]."</td>";
        $html .=  "<td> ".$timetableCRUD->getPeriodRow($school_id, $class_id, $periods[$i]["id"], 3)."</td>";
        $html .= "<td> ".$wedData["faculty"]." ".$wedData["subject"]."</td>";
        $html .=  "<td> ".$thuData["faculty"]." ".$thuData["subject"]."</td>";
        $html .=  "<td> ".$timetableCRUD->getPeriodRow($school_id, $class_id, $periods[$i]["id"], 6)."</td>";
        $html .=  "<td> ".$timetableCRUD->getPeriodRow($school_id, $class_id, $periods[$i]["id"], 7)."</td>";     
        
        //  $html .= "<td> ".$mondayData["faculty"]." for ".$periods[$i]["id"]."</td>";
        // $html .=  "<td> ".$periods[$i]["period_id"]." for ".$periods[$i]["id"]."</td>";
        // $html .=  "<td> ".$periods[$i]["period_id"]." for ".$periods[$i]["id"]."</td>";
        // $html .= "<td> ".$periods[$i]["period_id"]." for ".$periods[$i]["id"]."</td>";
        // $html .=  "<td> ".$periods[$i]["period_id"]." for ".$periods[$i]["id"]."</td>";
        // $html .=  "<td> ".$periods[$i]["period_id"]." for ".$periods[$i]["id"]."</td>";
        // $html .=  "<td> ".$periods[$i]["period_id"]." for ".$periods[$i]["id"]."</td>";
    }
    $html .= "</tr>";
}
$html .= '</div></html>';
*/

		$vars = [
			'page' => [
			'name' => 'timetable',
			'title' => $title.' | Talank SMS',
			'description' => '',
			'days' => $days,
			'periods' => $periods,
			'timetable' => $timetable,
			'rawTable' => $html,
			'$title' => $title
			]
		];
		return $this->view->render($response, 'timetable.twig', $vars);
	})->setName('time-table');
	
	
	$app->get('/add-timetable-entry[/{schoolCode}]', function (Request $request, Response $respo, $args){
		require_once("dbmodels/user.crud.php");
		require_once("dbmodels/master.crud.php");
		require_once("dbmodels/school.crud.php");
		require_once("dbmodels/period.crud.php");
        $userCRUD = new UserCRUD(getConnection());
        $masterCRUD = new MasterCRUD(getConnection());
        $schoolCRUD = new SchoolCRUD(getConnection());
        $periodCRUD = new PeriodCRUD(getConnection());           
        $response = array();
        $school_id = 1;
    /********* Validate Authorization **********/
	/********* Only Super Admin and school owners and permitted ones ********
	$authUser = getCallerSnapshot($request, $respo);
	$thisSchool = $schoolCRUD->getID($id);
	if(!$authUser["error"]){
		 if($authUser["caller_role"] == 1 || $authUser["caller_role"] == 2){
		 if(!$authUser["caller_role"] == 1 && !$userCRUD->isSchoolAdmin($authUser["id"], $id)) {
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
	********* Validated Authorization ********/
	
	
	    $periodID = 0;
	   $allGetVars = $request->getQueryParams();
        if(isset($allGetVars['period'])){
            $periodID = $allGetVars['period'];
        }
        $class_id = 0;
        if(isset($allGetVars['class_id'])){
            $class_id = $allGetVars['class_id'];
        }
        $day_id = 0;
        if(isset($allGetVars['day'])){
            $day_id = $allGetVars['day'];
        }
        
        
	   $classes = $masterCRUD->getAllClasses();
	   $subjects = $masterCRUD->getAllSubjects();
	   $days = $masterCRUD->getAllDays();
	   $teachers = $userCRUD->getAllUsers(4, $school_id);
	   $periods = $periodCRUD->getPeriods($school_id);
	   $schoolCode = "";
	   $schoolID = "";
	   if(isset($_SESSION["school_id"])){
	       $schoolID = $_SESSION["school_id"];
	   }
	  if(!$schoolCRUD->isIDExists($schoolID)){
    	 $uri = $request->getUri()->withPath($this->router->pathFor('404'));
        return $respo->withRedirect((string)$uri);
	   }
	   
	   $thisSchool = $schoolCRUD->getID($schoolID);
	   if($thisSchool !== null){
		  $schoolCode  = $thisSchool["qcode"];
	   }
	   //Now check authority 
	   if(!$_SESSION["role_id"] == 1 && !$userCRUD->isSchoolAdmin($_SESSION["userID"], $schoolID)) {
		 $uri = $request->getUri()->withPath($this->router->pathFor('unauthorized'));
        return $respo->withRedirect((string)$uri);
		 }

	   $vars = [
			'page' => [
			'name' => 'timetable',
			'title' => 'Manage Teachers | Talank SMS',
			'description' => '',
			'days' => $days,
			'classes' => $classes,
			'subjects' => $subjects,
			'teachers' => $teachers,
			'periods' => $periods,
			'param' => $schoolCode,
			'params' => [
			'period_id' => $periodID,
			'class_id' => $class_id,
			'day_id' => $day_id
			]
			]
		];
		return $this->view->render($respo, 'add-timetable-entry.twig', $vars);
	})->setName('add-timetable-entry');



	$app->get('/update-timetable-entry', function (Request $request, Response $respo, $args){
		require_once("dbmodels/user.crud.php");
		require_once("dbmodels/master.crud.php");
		require_once("dbmodels/school.crud.php");
		require_once("dbmodels/period.crud.php");
		require_once("dbmodels/timetable.crud.php");
        $userCRUD = new UserCRUD(getConnection());
        $masterCRUD = new MasterCRUD(getConnection());
        $schoolCRUD = new SchoolCRUD(getConnection());
        $periodCRUD = new PeriodCRUD(getConnection());
        $timeTableCRUD = new TimetableCRUD(getConnection());
        $response = array();
        $school_id = 0;
        
        
        //Single GET parameter
        $allGetVars = $request->getQueryParams();
        $recordID = 0;
        $routine = array();
        if(null !== $allGetVars['record']){
            $recordID = $allGetVars['record'];
            $routine = $timeTableCRUD->getID($recordID);
             if(null !== $routine){
            $school_id = $routine['school_id'];
        }
        }
        $periodID = 0;
        if(null !== $allGetVars['period']){
            $periodID = $allGetVars['period'];
        }
        $class_id = 0;
        if(null !== $allGetVars['class_id']){
            $class_id = $allGetVars['class_id'];
        }
         $day_id = 0;
        if(null !== $allGetVars['day_id']){
            $day_id = $allGetVars['day_id'];
        }
        
        if($recordID == 0 && $periodID == 0){
        $uri = $request->getUri()->withPath($this->router->pathFor('manage-periods'));
        return $respo->withRedirect((string)$uri);
        }

    /********* Validate Authorization **********/
	/********* Only Super Admin and school owners and permitted ones ********
	$authUser = getCallerSnapshot($request, $respo);
	$thisSchool = $schoolCRUD->getID($id);
	if(!$authUser["error"]){
		 if($authUser["caller_role"] == 1 || $authUser["caller_role"] == 2){
		 if(!$authUser["caller_role"] == 1 && !$userCRUD->isSchoolAdmin($authUser["id"], $id)) {
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
	********* Validated Authorization ********/
	
	   $classes = $masterCRUD->getAllClasses();
	   $subjects = $masterCRUD->getAllSubjects();
	   $days = $masterCRUD->getAllDays();
	   $teachers = $userCRUD->getAllUsers(4, $school_id);
	   $periods = $periodCRUD->getPeriods($school_id);
	   //$schoolCode = $request->getAttribute('schoolCode');
	   $schoolID = "";
	   if(isset($_SESSION["school_id"])){
	       $schoolID = $_SESSION["school_id"];
	   }
	   if(null !== $request->getAttribute('schoolCode')){
	   $schoolCode = $request->getAttribute('schoolCode');
	   if(!$schoolCRUD->isQCodeExists($schoolCode)){
    	 $uri = $request->getUri()->withPath($this->router->pathFor('404'));
        return $respo->withRedirect((string)$uri);
	   }
	   $schoolID = $schoolCRUD->getIDByQCode($schoolCode);
	  
	   }
	   
	   //Now check authority 
	   if(!$_SESSION["role_id"] == 1 && !$userCRUD->isSchoolAdmin($_SESSION["userID"], $schoolID)) {
		 $uri = $request->getUri()->withPath($this->router->pathFor('unauthorized'));
        return $respo->withRedirect((string)$uri);
		 }

	   $vars = [
			'page' => [
			'name' => 'timetable',
			'title' => 'Update Timetable Entry | Talank SMS',
			'description' => '',
			'days' => $days,
			'classes' => $classes,
			'subjects' => $subjects,
			'teachers' => $teachers,
			'periods' => $periods,
			'routine' => $routine,
			'params' => [
			'period_id' => $periodID,
			'class_id' => $class_id,
			'day_id' => $day_id
			]
			]
		];
		return $this->view->render($respo, 'update-timetable-entry.twig', $vars);
	})->setName('update-timetable-entry');
	
	
	

	$app->get('/manage-periods', function (Request $request, Response $response, $args){
		require_once("dbmodels/user.crud.php");
        $userCRUD = new UserCRUD(getConnection());
		require_once("dbmodels/period.crud.php");
		$periodCRUD = new PeriodCRUD(getConnection());
		require_once("dbmodels/school.crud.php");
        $schoolCRUD = new SchoolCRUD(getConnection());
		$school = 1;
		
		/********** SERVER SESSION CHECK  ***********/
	if(isset($_SESSION["userID"]) && isset($_SESSION["email"]) && isset($_SESSION["api_key"]) && isset($_SESSION["school_id"])){
    $thisUser = $userCRUD->getUserByAPIKey($_SESSION["api_key"]);
	if ($thisUser != null && ($thisUser["role_id"] == 1 || $thisUser["role_id"] == 2)) {
		$school = $thisUser["school_id"];
	 }else{
		$uri = $request->getUri()->withPath($this->router->pathFor('unauthorized'));
        return $response->withRedirect((string)$uri);
	 }
	}else{
	   	$uri = $request->getUri()->withPath($this->router->pathFor('login'));
        return $response->withRedirect((string)$uri);
	}
	 /********** SERVER SESSION CHECK  ***********/
    if(empty($school)){
	$uri = $request->getUri()->withPath($this->router->pathFor('404'));
    return $response->withRedirect((string)$uri);
    }
    $thisSchool = $schoolCRUD->getID($school);
    $periods = $periodCRUD->getPeriods($school);
	$schools = $schoolCRUD->getSchoolsUnder($_SESSION["userID"]);
	$vars = [
			'page' => [
			'name' => 'timetable',
			'title' => 'Manage Periods | Talank SMS',
			'description' => '',
			'periods' => $periods,
			'school' => $thisSchool,
			'schools' => $schools
			]
		];
		return $this->view->render($response, 'manage-periods.twig', $vars);
	})->setName('manage-periods');
	
	
	
	// UPDATE PERIOD ROUTE
	$app->get('/edit-period/{qcode}', function (Request $request, Response $response, $args){
    require_once("dbmodels/period.crud.php");
    $periodCRUD = new PeriodCRUD(getConnection());
    
	$qcode = $request->getAttribute('qcode');
    if(empty($qcode)){
	$uri = $request->getUri()->withPath($this->router->pathFor('manage-periods'));
    return $response->withRedirect((string)$uri);
    }
	if(!$periodCRUD->isIDExists($qcode)){
          $uri = $request->getUri()->withPath($this->router->pathFor('404')); 
          return $response->withRedirect((string)$uri);
    }
	$thisPeriod = $periodCRUD->getID($qcode);
		$vars = [
			'page' => [
			'name' => 'timetable',
			'title' => 'Update Period | Talank SMS',
			'period' => $thisPeriod,
			'description' => 'Talank SAS is a next generation school management and automation system. '
			]
		];
		return $this->view->render($response, 'edit-period.twig', $vars);
	})->setName('edit-period');
