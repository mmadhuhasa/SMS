<?php
/*********************************************/
function getUserFullProfile($user_id, $thumbnail = false) {
	require_once("dbmodels/user.crud.php");
    require_once("dbmodels/utils.crud.php");
	require_once("dbmodels/student.crud.php");
	require_once("dbmodels/teacher.crud.php");
	require_once("dbmodels/parent.crud.php");
	$userCRUD = new UserCRUD(getConnection());
	$studentCRUD = new StudentCRUD(getConnection());
    $parentCRUD = new ParentCRUD(getConnection());
	$teacherCRUD = new TeacherCRUD(getConnection());
    $utilCRUD = new UtilCRUD(getConnection());
	require_once("dbmodels/school.crud.php");
	$schoolCRUD = new SchoolCRUD(getConnection());
	$row = $userCRUD->getID($user_id);
	$tmp = array();
	if($row != null){
				//DETAILS
                $tmp["id"] = $row["id"];
				$tmp["first_name"] = $row["first_name"];
				$tmp["last_name"] = $row["last_name"];
			    $tmp["user_image"] = $row["user_image"];
				$tmp["user_name"] = $row["user_name"];
				$tmp["email"] = $row["email"];
				$tmp["mobile"] = $row["mobile"];				
			    $tmp["country"] = $row["country"];
				$tmp["city"] = $row["city"];
				$tmp["status"] = $row["status"];
				$tmp["dob"] = $row["dob"];
				//$tmp["gender"] = $row["gender"];
				$tmp["school_id"] = $row["school_id"];
				$tmp["school_name"] = $schoolCRUD->getNameByID($row["school_id"]);
		        $tmp["role_id"] = $row["role_id"];
		        $tmp["role_name"] = $userCRUD->getRoleName($row["role_id"]);
		        $tmp["ip_address"] = $row["ip_address"];
				
				$tmp["usage"] = $userCRUD->getUsage($row["api_key"]);
    				$tmp["last_seen"] = "";
    				if(!empty($row["last_seen"])){
    					try{
    					$tmp["last_seen"] = $utilCRUD->getFormattedDate($row["last_seen"]);
    				}catch(Exception $e){
    					$tmp["last_seen"] = $row["last_seen"];
    				}
    				}
					
				$tmp["date_created"] = "";
				$tmp["date_updated"] = "";
				try{
					$tmp["date_created"] = $utilCRUD->getFormattedDate($row["date_created"]);
				}catch(Exception $e){
					$tmp["date_created"] = $row["date_created"];
				}
				if(!empty($row["date_updated"])){
				try{
					$tmp["date_updated"] = $utilCRUD->getFormattedDate($row["date_updated"]);
				}catch(Exception $e){
					$tmp["date_updated"] = $row["date_updated"];
				}
				}
				
				 switch($row["role_id"]){
				   case 1:
				   //$tmp["details"] = $studentCRUD->getByUserID($user_id);
				   break;
				   
				   case 2:
                   //$tmp["details"] = $studentCRUD->getByUserID($user_id);
				   break;
				   
				   case 3:
				   //$tmp["student"] = $studentCRUD->getByUserID($user_id);
				   $tmp["details"] = getStudentDetails($user_id);
				   break;
				   
				   case 4:
				   $tmp["details"] = getTeacherDetails($user_id);
				   break;
				   
				   case 4:
				   $tmp["details"] = getParentDetails($user_id);
				   break;
				 }
			}
			return $tmp;
	}
	
	
/*************** GET STUDENT DETAILS ******************/
function getStudentDetails($user_id, $thumbnail = false) {
	require_once("dbmodels/user.crud.php");
    require_once("dbmodels/utils.crud.php");
	require_once("dbmodels/student.crud.php");
	$userCRUD = new UserCRUD(getConnection());
	$studentCRUD = new StudentCRUD(getConnection());
    $utilCRUD = new UtilCRUD(getConnection());
	require_once("dbmodels/class.crud.php");
	$classCRUD = new ClassCRUD(getConnection());
	require_once("dbmodels/section.crud.php");
	$sectionCRUD = new SectionCRUD(getConnection());
	require_once("dbmodels/user_relationships.crud.php");
	$relationshipsCRUD = new UserRelationshipCRUD(getConnection());
	$row = $userCRUD->getID($user_id);
	$tmp = array();
	if($row != null){
	        $tmp["first_name"] = $row["first_name"];
			$tmp["last_name"] = $row["last_name"];
			$tmp["user_name"] = $row["user_name"];

			//DETAILS
			$tmp["guardian"] = "Available";
			$studentDetails = $studentCRUD->getByUserID($user_id);
			if($studentDetails != null){
				 $tmp["roll_no"] = $studentDetails["roll_no"];
				 //$tmp["class"] = $studentDetails["class"];
				 $tmp["class_name"] = $classCRUD->getNameByID($studentDetails["class"]);
				 $tmp["section"] = $studentDetails["section"];
				 $tmp["section_name"] = $sectionCRUD->getNameByID($studentDetails["section"]);
				 if(!$thumbnail){
					 $tmp["admission_no"] = $studentDetails["admission_no"];	 
					 $tmp["religion"] = $studentDetails["religion"];
					 $tmp["caste"] = $studentDetails["caste"];
					 $tmp["blood_group"] = $studentDetails["blood_group"];
					 $tmp["admission_date"] = $studentDetails["admission_date"];
					 if(!empty($tmp["admission_date"])){
						try{
							$tmp["admission_date"] = $utilCRUD->getFormattedDate($tmp["admission_date"]);
						}catch(Exception $e){
							$tmp["admission_date"] = $studentDetails["admission_date"];
						}

						$tmp["parents"] = $relationshipsCRUD->getParentsFor($user_id);
						$tmp["numAllParents"] = count($tmp["parents"]);
						$tmp["numGuardians"] = $relationshipsCRUD->numMainGuardian($user_id);
						$tmp["numOtherParents"] = $tmp["numAllParents"] - $tmp["numGuardians"];
						if($tmp["numGuardians"] <= 0){
							$tmp["guardian"] = "No Guardian Added";
						}
				 	}
			 	}
			}
			return $tmp;
	}
}
	
/*************** GET TEACHER DETAILS ******************/
function getTeacherDetails($user_id, $thumbnail = false) {
	require_once("dbmodels/user.crud.php");
    require_once("dbmodels/utils.crud.php");
	require_once("dbmodels/student.crud.php");
	$userCRUD = new UserCRUD(getConnection());
	$studentCRUD = new StudentCRUD(getConnection());
    $utilCRUD = new UtilCRUD(getConnection());
	require_once("dbmodels/school.crud.php");
	$schoolCRUD = new SchoolCRUD(getConnection());
	require_once("dbmodels/teacher.crud.php");
	$teacherCRUD = new TeacherCRUD(getConnection());
	$tmp = array();
	$row = $userCRUD->getID($user_id);
	if($row != null){
				$tmp["name"] = $row["first_name"];
				$tmp["school_id"] = $row["school_id"];
				$tmp["mobile"] = $row["mobile"];
				$tmp["date_created"] = $row["date_created"];

				//DETAILS
				$teacherDetails = $teacherCRUD->getByUserID($user_id);
				if($teacherDetails != null){
					 $tmp["designation"] = "Sir ".$teacherDetails["designation"];
					 $tmp["joining_date"] = 0;
					 $tmp["is_class_teacher"] = 0;
					 $tmp["class_incharge"] = 0;
					 if(!$thumbnail){







					 }
				}
			}
			return $tmp;
	}
	
/*************** GET PARENTS DETAILS ******************/
function getParentDetails($user_id, $thumbnail = false) {
	require_once("dbmodels/user.crud.php");
    require_once("dbmodels/utils.crud.php");
	require_once("dbmodels/student.crud.php");
	$userCRUD = new UserCRUD(getConnection());
	$studentCRUD = new StudentCRUD(getConnection());
    $utilCRUD = new UtilCRUD(getConnection());
	require_once("dbmodels/school.crud.php");
	$schoolCRUD = new SchoolCRUD(getConnection());
	require_once("dbmodels/user_relationships.crud.php");
	$relationshipsCRUD = new UserRelationshipCRUD(getConnection());
	require_once("dbmodels/parent.crud.php");
	$parentCRUD = new ParentCRUD(getConnection());
	
	$row = $userCRUD->getID($user_id);
	if($row != null){
			    $tmp = array();
				//DETAILS
				$parentDetails = $parentCRUD->getByUserID($user_id);
				 if($parentDetails !== null){
					 $tmp["occupation"] = $parentDetails["occupation"];
					 $tmp["numWards"] = 0;
					 if(!$thumbnail){
					 }
					 $tmp["wards"] = $relationshipsCRUD->getMyWards($user_id);
				 }
                 return $tmp;
				 }
	}

/***************GET USER BASIC DETAILS **********************/
	
	function getUserBasicDetails($user_id) {
	require_once("dbmodels/user.crud.php");
    require_once("dbmodels/utils.crud.php");
	$userCRUD = new UserCRUD(getConnection());
    $utilCRUD = new UtilCRUD(getConnection());
	$row = $userCRUD->getID($user_id);
	if($row != null){
		$tmp = array();
        $tmp["id"] = $row["id"];
		$tmp["first_name"] = $row["first_name"];
		$tmp["last_name"] = $row["last_name"];
		$tmp["user_name"] = $row["user_name"];
		$tmp["user_image"] = $row["user_image"];
		$tmp["status"] = $row["status"];
		$tmp["school_id"] = $row["school_id"];
		$tmp["role_id"] = $row["role_id"];
		$tmp["role_name"] = $userCRUD->getRoleName($row["role_id"]);
		$tmp["api_key"] = $row["api_key"];
		$tmp["date_created"] = $utilCRUD->getFormattedDate($row["date_created"]);
	    return $tmp;
	  }
	  return NULL;
	}

/*************** GET CLASS DETAILS ******************/
	
   function getClassDetails($item_id) {
	    require_once("dbmodels/class.crud.php");
	    $classCRUD = new ClassCRUD(getConnection());
		require_once("dbmodels/user.crud.php");
        $userCRUD = new UserCRUD(getConnection());
		require_once("dbmodels/school.crud.php");
        $schoolCRUD = new SchoolCRUD(getConnection());
	    $row = $classCRUD->getID($item_id);
	    if($row != null){
		$tmp = array();
        $tmp["id"] = $row["id"];
		$tmp["symbol"] = $row["symbol"];
		$tmp["name"] = $row["name"];
		$tmp["strength"] ="0";
		$tmp["sections"] ="N/A";
		$tmp["school_name"] ="";
		$tmp["schoolCode"] ="";
		$thisSchool = $schoolCRUD->getID($row["id"]);
		if($thisSchool !== null){
		$tmp["school_name"] =$thisSchool["name"];
		$tmp["schoolCode"] = $thisSchool["qcode"];
		}
		//$tmp["yop"] = $utilCRUD->getFormattedDate($row["yop"]);
		return $tmp;
		}
	}

/*************** GET SECTION DETAILS ******************/	
	
	
	 function getSectionDetails($item_id) {
	    require_once("dbmodels/class.crud.php");
	    $classCRUD = new ClassCRUD(getConnection());
		require_once("dbmodels/user.crud.php");
        $userCRUD = new UserCRUD(getConnection());
		require_once("dbmodels/school.crud.php");
        $schoolCRUD = new SchoolCRUD(getConnection());
		require_once("dbmodels/section.crud.php");
        $sectionCRUD = new SectionCRUD(getConnection());
	    $row = $sectionCRUD->getID($item_id);
	    if($row != null){
		$tmp = array();
        $tmp["id"] = $row["id"];
		$tmp["name"] = $row["name"];
		$tmp["class_id"] = $row["class_id"];
		$tmp["class_name"] = $classCRUD->getNameByID($row["class_id"]);
		$tmp["school_id"] = $row["school_id"];
        $tmp["strength"] =$row["strength"];
		$thisSchool = $schoolCRUD->getID($row["school_id"]);
		if($thisSchool !== null){
		$tmp["school_name"] =$thisSchool["name"];
		$tmp["schoolCode"] = $thisSchool["qcode"];
		}
		//$tmp["yop"] = $utilCRUD->getFormattedDate($row["yop"]);
		return $tmp;
		}
	}
/*************** GET SUBJECT DETAILS ******************/
	
	function getSubjectDetails($item_id) {
	    require_once("dbmodels/class.crud.php");
	    $classCRUD = new ClassCRUD(getConnection());
		require_once("dbmodels/user.crud.php");
        $userCRUD = new UserCRUD(getConnection());
		require_once("dbmodels/school.crud.php");
        $schoolCRUD = new SchoolCRUD(getConnection());
		require_once("dbmodels/subject.crud.php");
        $subjectCRUD = new SubjectCRUD(getConnection());
	    $row = $subjectCRUD->getID($item_id);
	    if($row != null){
		$tmp = array();
        $tmp["id"] = $row["id"];
		$tmp["title"] = $row["title"];
		$tmp["image"] = "";
		$tmp["school_id"] = $row["school_id"];
		$tmp["description"] = $row["description"];
		$thisSchool = $schoolCRUD->getID($row["id"]);
		if($thisSchool !== null){
		}
		//$tmp["yop"] = $utilCRUD->getFormattedDate($row["yop"]);
		return $tmp;
		}
	}
/*************** GET TOPIC DETAILS ******************/	
	
		function getTopicDetails($item_id) {
	    require_once("dbmodels/class.crud.php");
		$classCRUD = new ClassCRUD(getConnection());
		require_once("dbmodels/subject.crud.php");
	    $subjectCRUD = new SubjectCRUD(getConnection());
		require_once("dbmodels/user.crud.php");
        $userCRUD = new UserCRUD(getConnection());
		require_once("dbmodels/topic.crud.php");
        $topicCRUD = new TopicCRUD(getConnection());
	    $row = $topicCRUD->getID($item_id);
	    if($row != null){
		$tmp = array();
        $tmp["id"] = $row["id"];
		$tmp["title"] = $row["title"];
		$tmp["subject_id"] = $row["subject_id"];
		$tmp["subject_name"] = $subjectCRUD->getNameByID($row["subject_id"]);
		$tmp["class_id"] = $row["class_id"];
		$tmp["class_name"] = $classCRUD->getNameByID($row["class_id"]);
		$tmp["description"] = $row["description"];
		$tmp["image"] = $row["image"];
		return $tmp;
		}
	}
/*************** GET USER ROLE DETAILS ******************/
	
	function getUserRoleDetails($item_id) {
		require_once("dbmodels/user.crud.php");
        $userCRUD = new UserCRUD(getConnection());
		require_once("dbmodels/user_role.crud.php");
        $roleCRUD = new UserRoleCRUD(getConnection());
	    $row = $roleCRUD->getID($item_id);
	    if($row != null){
		$tmp = array();
        $tmp["id"] = $row["id"];
		$tmp["name"] = $row["name"];
		$tmp["description"] = $row["description"];
		//$tmp["yop"] = $utilCRUD->getFormattedDate($row["yop"]);
		return $tmp;
		}
	}
/*************** GET ASSIGNMENT DETAILS ******************/	
        
		function getAssignmentDetails($item_id) {
	    require_once("dbmodels/class.crud.php");
	    $classCRUD = new ClassCRUD(getConnection());
		require_once("dbmodels/user.crud.php");
		$userCRUD = new UserCRUD(getConnection());
		require_once("dbmodels/subject.crud.php");
        $subjectCRUD = new SubjectCRUD(getConnection());
		require_once("dbmodels/school.crud.php");
        $schoolCRUD = new SchoolCRUD(getConnection());
		require_once("dbmodels/assignment.crud.php");
        $assignmentCRUD = new AssignmentCRUD(getConnection());
		require_once("dbmodels/utils.crud.php");
	    $utilCRUD = new UtilCRUD(getConnection());
	    $row = $assignmentCRUD->getID($item_id);
	    if($row != null){
		$tmp = array();
        $tmp["id"] = $row["id"];
		$tmp["title"] = $row["title"];
		$tmp["class_id"] = $row["class_id"];
		$tmp["class_name"] = $classCRUD->getNameByID($row["class_id"]);
		$tmp["subject_id"] = $row["subject_id"];
		$tmp["subject_name"] = $subjectCRUD->getNameByID($row["subject_id"]);
		$tmp["description"] = $row["description"];
		$tmp["image"] = $row["image"];
		$tmp["qcode"] = $row["qcode"];
		$tmp["date_submission"] = $row["date_submission"];
		$tmp["is_published"] = $row["is_published"];
		$tmp["user_id"] = $row["user_id"];
		$tmp["faculty_name"] = $userCRUD->getNameByID($row["user_id"]);
		$tmp["faculty_image"] = $userCRUD->getImageByID($row["user_id"]);
	
		$date_created = $row["date_created"];
		if(!empty($date_created)){
			try{			
				$tmp["date_created"] = $utilCRUD->getFormattedDate($date_created);
			}catch(Exception $e){
				$tmp["date_created"] = $date_created;
			}
		}

		// $thisSchool = $schoolCRUD->getID($row["id"]);
		// if($thisSchool !== null){
		// }

		 $tmp["numSubmissions"] = 0;
		 $tmp["numEvaluated"] = 0;
		// $tmp["is_published"] = "";
		//$tmp["yop"] = $utilCRUD->getFormattedDate($row["yop"]);
		return $tmp;
		}
	}
	
	/*************** GET ASSIGNMENT SUBMISSION DETAILS ******************/	
        
		function getAssignmentSubmissionDetails($item_id) {
	    require_once("dbmodels/class.crud.php");
	    $classCRUD = new ClassCRUD(getConnection());
		require_once("dbmodels/user.crud.php");
		$userCRUD = new UserCRUD(getConnection());
		require_once("dbmodels/submission.crud.php");
        $submissionCRUD = new SubmissionCRUD(getConnection());
		require_once("dbmodels/utils.crud.php");
	    $utilCRUD = new UtilCRUD(getConnection());
	    $row = $submissionCRUD->getID($item_id);
	    if($row != null){
		$tmp = array();
        $tmp["id"] = $row["id"];
		$tmp["assignment_id"] = $row["assignment_id"];
		$tmp["user_id"] = $row["user_id"];
		$tmp["title"] = $row["title"];
		$tmp["content"] = $row["content"];
		$tmp["status"] = $row["status"];
		$tmp["qcode"] = $row["qcode"];
		// $tmp["is_published"] = "";
		//$tmp["yop"] = $utilCRUD->getFormattedDate($row["yop"]);
		return $tmp;
		}
	}
	
/*************** GET SCHOOL DETAILS ******************/	

	function getSchoolDetails($res, $thumbnail = true, $fullDetails = false) {
    require_once("dbmodels/school.crud.php");
	$schoolCRUD = new SchoolCRUD(getConnection());
	require_once("dbmodels/user.crud.php");
	$userCRUD = new UserCRUD(getConnection());
	require_once("dbmodels/utils.crud.php");
	$utilCRUD = new UtilCRUD(getConnection());
	if($res != null){
		$companyProfile = array();
		$companyProfile["id"] = $res["id"];
		$companyProfile["name"] = $res["name"];
		$companyProfile["tagline"] = $res["tagline"];
		$companyProfile["address"] = $res["address"];
		$companyProfile["city"] = $res["city"];
		$companyProfile["country"] = $res["country"];
	    $companyProfile["phone"] = $res["phone"];
	    $companyProfile["email"] = $res["email"];
	    $companyProfile["qcode"] = $res["qcode"];
		$companyProfile["description"] = "";
	    $companyProfile["logo"] = $res["logo"];
		$companyProfile["website"] = $res["website"];
		$companyProfile["status"] = $res["status"];
		$companyProfile["date_created"] = $res["date_created"];
		try{
			$companyProfile["date_created"] = $utilCRUD->getFormattedDate($res["date_created"]);
		}catch(Exception $e){
			$companyProfile["date_created"] = $res["date_created"];
		}
		
		$companyProfile["connections"] = $userCRUD->getUsageBySchool($res["id"]);
		$companyProfile["numAdmins"] = $userCRUD->getNumSchoolAdmins($res["id"]);
		$schoolAdmin = $userCRUD->getFirstSchoolAdmin($res["id"]);
		$companyProfile["admin"] = $schoolAdmin;
		
		$companyProfile["admin"]["id"] = "";
		$companyProfile["admin"]["first_name"] = "";
		$companyProfile["admin"]["last_name"] = "";
		/*
		$tmpSchoolAdmin = array();
		if(null !== $schoolAdmin && null !== $schoolAdmin["id"]){
		$tmpSchoolAdmin["owner_id"] = $res["id"];
		$tmpSchoolAdmin["owner_name"] = $userCRUD->getNameByID($schoolAdmin["id"]);
		$tmpSchoolAdmin["owner_username"] = $userCRUD->getUserName($schoolAdmin["id"]);
		} */
		
		if($fullDetails){
			$companyProfile["users"] = $userCRUD->getNumAllUsersIn($res["id"]);
		}
	    return $companyProfile;
	  }
	  return NULL;
	}
/*************** GET PERIOD DETAILS ******************/	
		
	function getPeriodDetails($school_id, $class_id, $period_id){
	    require_once("dbmodels/period.crud.php");
        $periodCRUD = new PeriodCRUD(getConnection());
        require_once("dbmodels/master.crud.php");
        $masterCRUD = new MasterCRUD(getConnection());
        require_once("dbmodels/user.crud.php");
        $userCRUD = new UserCRUD(getConnection());
        require_once("dbmodels/timetable.crud.php");
        $timetableCRUD = new TimetableCRUD(getConnection());
        $days = $masterCRUD->getAllDays();
        $data = array();
        
       	if (count($days) > 0) {
			    foreach ($days as $item_row) {
			 $tmp = array();       
			  $timeTable = $timetableCRUD->getPeriodRow($school_id, $class_id, $period_id, $item_row["id"]);
         if($timeTable !== null){
					 $tmp["period_id"] = $timeTable["period_id"];
					 $thisPeriod = $periodCRUD->getID($period_id);
					 $tmp["start_time"] = $thisPeriod["start_time"];
					 
					 $tmp["faculty"] = $timeTable["faculty"];
					 $tmp["end_time"] = $thisPeriod["end_time"];
					 $tmp["faculty_name"] = $userCRUD->getNameByID($timeTable["faculty"]);
					 $tmp["section"] = $timeTable["section"];
					 $tmp["subject"] = $timeTable["subject"];
					 $tmp["subject_name"] = "Good";
				//$tmp["numOtherParents"] = $tmp["numAllParents"] - $tmp["numGuardians"];
				
			   //$fullItem = getUserEduDetails($item_row["id"]);
			   array_push($data, $tmp);
		}       
	           }
	           }
	           return $data;
        /*
       */
	}
	
	
		function getTimetable($school_id, $class_id){
	    require_once("dbmodels/period.crud.php");
        $periodCRUD = new PeriodCRUD(getConnection());
        require_once("dbmodels/master.crud.php");
        $masterCRUD = new MasterCRUD(getConnection());
        require_once("dbmodels/user.crud.php");
        $userCRUD = new UserCRUD(getConnection());
        require_once("dbmodels/timetable.crud.php");
        $timetableCRUD = new TimetableCRUD(getConnection());
         require_once("dbmodels/subject.crud.php");
         require_once("dbmodels/class.crud.php");
        $subjectCRUD = new SubjectCRUD(getConnection());
        $classCRUD= new ClassCRUD(getConnection());
        $days = $masterCRUD->getAllDays();
        $periods= $periodCRUD->getPeriods($school_id);
        $data = array();
        
       	if (count($periods) > 0) {
			 foreach ($periods as $period_row) {
			 $tmp = array(); 
			  $tmp["period_id"] = $period_row["id"];
			$tmp["start_time"] = $period_row["start_time"];
			$tmp["end_time"] = $period_row["end_time"];
			$tmp["days"] = array();
			    foreach ($days as $item_row) {
			        
                    $itemTmp = array();
                    $itemTmp["id"] = 0;
					$itemTmp["day_id"] = $item_row["id"];
			        $itemTmp["day"] = $item_row["name"];
					 $itemTmp["faculty"] = "";
					 $itemTmp["faculty_name"] = "";
					 $itemTmp["section"] = "";
					 $itemTmp["subject"] = "";
					 $itemTmp["subject_name"] = "";
					 $itemTmp["class_id"] = "";
					 
					   $timeTable = $timetableCRUD->getPeriodRow($school_id, $class_id, $period_row["id"], $item_row["id"]);
         if($timeTable !== null){
                     $itemTmp["id"] = $timeTable["id"];
					 $itemTmp["faculty"] = $timeTable["faculty"];
					 $itemTmp["faculty_name"] = $userCRUD->getNameByID($timeTable["faculty"]);
					 $itemTmp["section"] = $timeTable["section"];
					 $itemTmp["subject"] = $timeTable["subject"];
					 $itemTmp["class_id"] = $timeTable["class_id"];
					 $itemTmp["class_name"] = $classCRUD->getNameByID($timeTable["class_id"]);
					 $itemTmp["subject_name"] = $subjectCRUD->getNameByID($timeTable["subject"]);
         }
         
				//$tmp["numOtherParents"] = $tmp["numAllParents"] - $tmp["numGuardians"];
				
			   //$fullItem = getUserEduDetails($item_row["id"]);
		 array_push($tmp["days"], $itemTmp);
	           }
	           array_push($data, $tmp);
       	}
       			
	           }
	           return $data;
	}
	
    /**************** REVIEW CODE BELOW ******************/

	
 	
    /*************** GET POST DETAILS ******************/
	function getPostDetails($id, $userID, $thumbnail = true, $displayStats = true, $listDetails = false) {
	require_once("dbmodels/user.crud.php");
	$userCRUD = new UserCRUD(getConnection());
	require_once("dbmodels/utils.crud.php");
	$utilCRUD = new UtilCRUD(getConnection());
	require_once("dbmodels/class.crud.php");
	$classCRUD = new ClassCRUD(getConnection());
	require_once("dbmodels/subject.crud.php");
	$subjectCRUD = new SubjectCRUD(getConnection());
	require_once("dbmodels/topic.crud.php");
	$topicCRUD = new TopicCRUD(getConnection());
	require_once("dbmodels/post.crud.php");
    $postCRUD = new PostCRUD(getConnection());
    //require_once("dbmodels/post_type.crud.php");
    //$postTypeCRUD = new PostTypeCRUD(getConnection());
	require_once("dbmodels/post_tag.crud.php");
    $postTagCRUD = new PostTagCRUD(getConnection());
    require_once("dbmodels/post_preferences.crud.php");
    $postPreferenceCRUD = new PostPreferenceCRUD(getConnection());
    require_once("dbmodels/post_image.crud.php");
    $postImageCRUD = new PostImageCRUD(getConnection());
	require_once("dbmodels/post_like.crud.php");
	$postLikeCRUD = new PostLikeCRUD(getConnection());
	require_once("dbmodels/post_comment.crud.php");
	$postCommentCRUD = new PostCommentCRUD(getConnection());
	require_once("dbmodels/post_view.crud.php");
	$postViewCRUD = new PostViewCRUD(getConnection());
    $res = $postCRUD->getID($id);
	if($res != null){
		$postFullDetails = array();
		$postFullDetails["id"] = $res["id"];
		$postFullDetails["title"] = $res["title"];
		$postFullDetails["post_type"] = $res["post_type"];
		$postFullDetails["author_id"] = $res["author_id"];
		$postFullDetails["class_id"] = $res["class_id"];
		$postFullDetails["subject_id"] = $res["subject_id"];
		$postFullDetails["topic_id"] = $res["topic_id"];
	    $postFullDetails["status"] = $res["status"];
	    $postFullDetails["qcode"] = $res["qcode"];
		$postFullDetails["description"] = $res["description"];
		$postFullDetails["body"] = $res["body"];
	    $postFullDetails["date_created"] = $res["date_created"];
		$postFullDetails["link"] = $res["link"];
		//$postFullDetails["timestamp"] = $res["timestamp"];
		
		if(!empty($postFullDetails["date_created"])){
    		try{
    			$postFullDetails["date_created"] = $utilCRUD->getFormattedDate($res["date_created"]);
    		}catch(Exception $e){
    			$postFullDetails["date_created"] = $res["date_created"];
    		}
    	}
		
		//Post Details 
		$postFullDetails["class_name"] = $classCRUD->getNameByID($res["class_id"]);
		$postFullDetails["subject_name"] = $subjectCRUD->getNameByID($res["subject_id"]);
		$postFullDetails["topic_name"] = $topicCRUD->getNameByID($res["topic_id"]);
		$postFullDetails["author_name"] = $userCRUD->getNameByID($res["author_id"]);
		$postFullDetails["author_role"] = $userCRUD->getRoleNameFromUsers($res["author_id"]);
		$postFullDetails["author_image"] = $userCRUD->getImageByID($res["author_id"]);
		$postFullDetails["author_school"] = $userCRUD->getSchoolName($res["author_id"]);
		
		$postFullDetails["tags"] = array();
		if($postTagCRUD->getNumTags($id) > 0){
			$postFullDetails["tags"] = $postTagCRUD->getTagsForPost($id);
		}
		$postFullDetails["prefs"] = array();
		if($postPreferenceCRUD->isPrefsAvailable($id) > 0){
			$postFullDetails["prefs"] = $postPreferenceCRUD->getPrefsFor($id);
		}
		$postFullDetails["images"] = array();
		if($postImageCRUD->isImageAvailable($id) > 0){
			$postFullDetails["images"] = $postImageCRUD->getImages($id);
		}

		//Personal Data
		$postFullDetails["isLiked"] = false;
		if(!empty($userID)){
			$postFullDetails["isLiked"] = $postLikeCRUD->isLikedBy($userID, $id);
		}
		
		//Post Statistics
		if($displayStats){
			$postFullDetails["numViews"] = $postViewCRUD->getPostViewCount($id);
			$postFullDetails["numUniqueViews"] = $postViewCRUD->getPostViewCountUnique($id);
			$postFullDetails["numLikes"] = $postLikeCRUD->getNumLikes($id);
			$postFullDetails["numComments"] = $postCommentCRUD->getNumCommentsFor($id);
		}

		//Post Attributes List
		if($listDetails){
			$postFullDetails["comments"] = array();
			$postComments = $postCommentCRUD->getCommentsFor($id);
			// get comments
			if (count($postComments) > 0) {
			   foreach ($postComments as $thisComment) {
				   $tmp = getPostCommentItem($thisComment["id"]);
				   array_push($postFullDetails["comments"], $tmp);
			   }
	        }

			// $user_info = array();	
			// foreach ($postFullDetails["comments"] as $key => $value) {
			// 	$user_info = $userCRUD->getUserImage($value['user_id']);
			// 	array_push( $postFullDetails["comments"][$key], array_reduce($user_info, 'array_merge', array()) );
			// }
			// print_r($postFullDetails["comments"]);die();

			// get recent posts
			$postFullDetails["recent_posts"] = array();
			$postFullDetails["recent_posts"] = $postCRUD->getRecentPosts();
	

			// get latest posts
			$postFullDetails["latest_images"] = array();
			$postFullDetails["latest_images"] = $postCRUD->getLatestImages();	// $postImageCRUD->getLatestImages();

		}

	    return $postFullDetails;
	  }
	  return NULL;
	}



	/********** Get Post Comment Detail *********/
	function getPostCommentItem($item_id) {
	    require_once("dbmodels/utils.crud.php");
		require_once("dbmodels/user.crud.php");
		require_once("dbmodels/post_comment.crud.php");
		$postCommentCRUD = new PostCommentCRUD(getConnection());
	    $utilCRUD = new UtilCRUD(getConnection());
		$userCRUD = new UserCRUD(getConnection());
		$row = $postCommentCRUD->getID($item_id);
		if($row != null){
			$tmp = array();
	        $tmp["id"] = $row["id"];
			$tmp["user_id"] = $row["user_id"];
			$tmp["post_id"] = $row["post_id"];
			$tmp["comment"] = $row["comment"];
			$tmp["name"] = $userCRUD->getNameByID($row["user_id"]);
			$tmp["user_image"] = $userCRUD->getImageByID($row["user_id"]);		
	        $tmp["timestamp"] = $row["timestamp"];
			try{
				if(!empty($row["timestamp"])){	 
					$tmp["timestamp"] = $utilCRUD->getFormattedDate($row["timestamp"]);
				}
			}catch(Exception $e){
					//
			}
		    return $tmp;
		}
		return NULL;
	}


?>