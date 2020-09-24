<?php
/**************** GET LIST OF SCHOOLS ***********/
    $app->get('/apis/schools/list', function($request, $response, $args) {
	require_once("dbmodels/user.crud.php");
    $userCRUD = new UserCRUD(getConnection());
	require_once("dbmodels/school.crud.php");
	$schoolCRUD = new SchoolCRUD(getConnection());
	$var_response = array();
	//$snap = getCallerSnapshot($request, $respo);
	try{
	$dataArr = $schoolCRUD->getAllSchools();
    $data = array();
	if (count($dataArr) > 0) {
		foreach ($dataArr as $res) {
        $companyProfile = getSchoolDetails($res);
		array_push($data, $companyProfile);
		}
	    }
		$var_response["items"] = $data;
		}catch(Exception $e){
			$var_response["error"] = true;
            $var_response["message"] = "Failed to fetch data => ".$e.getMessage();
		}
        echoRespnse(200, $var_response);
        })->add($authenticate);
		
		
/*********** CREATE NEW SCHOOL *************/
    $app->post('/apis/schools/create', function ($request, $respo, $args) use ($app) {
	require_once("dbmodels/school.crud.php");
    $schoolCRUD = new SchoolCRUD(getConnection());
	$response = array();
    $response["error"] = false;
	
	/********* Validate Authorization **********/
	/********* Only Super Admin can create new school ********/
	$authUser = getCallerSnapshot($request, $respo);
	if(!$authUser["error"]){
		 if($authUser["caller_role"] == 1){ } else {
		 $response['error'] = true;
         $response['message'] = 'You are not authorized to create a school profile.';
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
	
	$name = $request->getParam('name');
	$city = $request->getParam('city');
	$country = $request->getParam('country');
	$address = $request->getParam('address');
	$phone = "";
	$email = "";
	$status = "Pending";
	$tagline = "";
	if (null != $request->getParam('email')) {
	     $email = $request->getParam('email');
	 }
	 if (null != $request->getParam('phone')) {
	     $phone = $request->getParam('phone');
	 }
	 if (null != $request->getParam('status')) {
	     $status = $request->getParam('status');
	 }
	 if (null != $request->getParam('tagline')) {
	     $tagline = $request->getParam('tagline');
	}
	$website = "";
	if (null != $request->getParam('website')) {
	     $website = $request->getParam('website');
	}
	$registration_no = "";
	if (null != $request->getParam('registration_no')) {
	     $registration_no = $request->getParam('registration_no');
	}
	
	$date_created = date('Y-m-d H:i:s');
	
	if(empty($name)){
		 $response['error'] = true;
         $response['message'] = 'Please enter the school name.';
         echoRespnse(200, $response);
		 return;
	}
	if(empty($registration_no)){
		 $response['error'] = true;
         $response['message'] = 'You must enter School Registration ID.';
         echoRespnse(200, $response);
		 return;
	}
	if(empty($city)){
		 $response['error'] = true;
         $response['message'] = 'Please enter the city name.';
         echoRespnse(200, $response);
		 return;
	}
	if(empty($country)){
		 $response['error'] = true;
         $response['message'] = 'Please select a country.';
         echoRespnse(200, $response);
		 return;
	}


	$response["debug"] = "";
	$qcode = $schoolCRUD->generateCode();
	$res = $schoolCRUD->create($name, $tagline, $address, $city, $country, $phone, $email, $website, $registration_no, $date_created, $qcode, $status);
	if ($res["code"] == INSERT_SUCCESS) {
                $response["error"] = false;
                $response["message"] = "School - ".$name." has been created successfully. ";
				$id = $res["id"];
				$response["id"] = $id;
				$response["qcode"] = $qcode;
	/*********############  START LOGO UPLOAD #############**********/
	$files = $request->getUploadedFiles();
    if (!empty($files['logo'])) {
    try{
    $newCoverfile = $files['logo'];
    $cover_file_type = "Unknown";
	$response["message"] .= " Got logo.";
	if ($newCoverfile->getError() === UPLOAD_ERR_OK) {
    $uploadCoverName = $newCoverfile->getClientFilename();
	$uploadCoverName = explode(".", $uploadCoverName);
    $ext = array_pop($uploadCoverName);
    $ext = strtolower($ext);
    $uploadCoverName = $qcode. "." . $ext;
	
	$file_size = $newCoverfile->getSize();
	$cover_file_type = $newCoverfile->getClientMediaType();
	if(!$cover_file_type == "image/jpg" || !$cover_file_type == "image/jpeg" || !$cover_file_type == "image/jpeg"){
		 $response['error'] = true;
         $response['message'] = 'Please upload a png, jpg or jpeg image file as the Cover Image.';
         echoRespnse(200, $response);
		 return;
	}
	
	if($cover_file_type > 1000000){
		 $response['error'] = true;
         $response['message'] = 'Upload a cover image of size not more than 1 MB.';
         echoRespnse(200, $response);
		 return;
	}
	
	$fileToTest = "uploads/images/schools/$uploadCoverName";
	if(file_exists($fileToTest)) {
    unlink($fileToTest);
	}
    $newCoverfile->moveTo($fileToTest);
    //$docCRUD->updateCover($id, $uploadCoverName);
    if($schoolCRUD->updateLogo($id, $uploadCoverName)){
         $response['message'] .= ' Logo has been updated.';
    }
	}
    }catch(Exception $e){
        $response["message"] .= " Failed to upload logo.";
		echoRespnse(200, $response);
		exit;
         }
	}
/********* 2. END OF COVER UPLOAD **********/
			 }else{
				  $response["error"] = true;
				  $response["info"] = $res["message"] ;
                  $response["message"] = "Failed to add school profile. Please try again.";
				  echoRespnse(200, $response);
			 }
	echoRespnse(200, $response);
	})->add($authenticate);
	
	
	function uploadLogo($request, $qcode){
	require_once("dbmodels/school.crud.php");
    $schoolCRUD = new SchoolCRUD(getConnection());
	$response = array();
    $response["error"] = false;	
	$files = $request->getUploadedFiles();
    if (!empty($files['logo'])) {
    try{
    $newCoverfile = $files['logo'];
    $cover_file_type = "Unknown";
	$response["message"] = " Got logo.";
	if ($newCoverfile->getError() === UPLOAD_ERR_OK) {
    $uploadCoverName = $newCoverfile->getClientFilename();
	$uploadCoverName = explode(".", $uploadCoverName);
    $ext = array_pop($uploadCoverName);
    $ext = strtolower($ext);
    $uploadCoverName = $qcode. "." . $ext;
	
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
	
	$fileToTest = "uploads/images/schools/$uploadCoverName";
	if(file_exists($fileToTest)) {
    unlink($fileToTest);
	}
    $newCoverfile->moveTo($fileToTest);
    //$docCRUD->updateCover($id, $uploadCoverName);
    if($schoolCRUD->updateLogo($id, $uploadCoverName)){
         $response['message'] .= ' Logo has been updated.';
    }
	}
    }catch(Exception $e){
        $response["message"] .= " Failed to upload logo.";
		//echoRespnse(200, $response);
		//exit;
         }
	}
	return $response;
	}
	
	
/*********** UPDATE EXISTING SCHOOL *************/
    $app->post('/apis/schools/update', function ($request, $respo, $args) use ($app) {
	require_once("dbmodels/school.crud.php");
    $schoolCRUD = new SchoolCRUD(getConnection());
	$response = array();
    $response["error"] = false;
	
	$id = $request->getParam('item_id');
	if(empty($id)){
	 $response['error'] = true;
     $response['message'] = 'Please pass school ID.';
     echoRespnse(200, $response);
	 return;
	}
    if(!$schoolCRUD->isIDExists($id)){
	 $response['error'] = true;
     $response['message'] = 'This school is not available to modify at this moment. Check back later. '.$id;
     echoRespnse(200, $response);
	 return;
	}
	/********* Validate Authorization **********/
	/********* Only Super Admin can create new school ********/
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
	/********* Validated Authorization ********/
	
	$name = $request->getParam('name');
	$city = $request->getParam('city');
	$country = $request->getParam('country');
	$address = $request->getParam('address');
	$phone = "";
	$email = "";
	$status = "Pending";
	$tagline = "";
	if (null != $request->getParam('email')) {
	     $email = $request->getParam('email');
	 }
	 if (null != $request->getParam('phone')) {
	     $phone = $request->getParam('phone');
	 }
	 if (null != $request->getParam('status')) {
	     $status = $request->getParam('status');
	 }
	 if (null != $request->getParam('tagline')) {
	     $tagline = $request->getParam('tagline');
	}
	$registration_no = "";
	if (null != $request->getParam('registration_no')) {
	     $registration_no = $request->getParam('registration_no');
	}
	
	$date_updated = date('Y-m-d H:i:s');
	if(empty($name)){
		 $response['error'] = true;
         $response['message'] = 'Please enter the school name.';
         echoRespnse(200, $response);
		 return;
	}
	if(empty($registration_no)){
		 $response['error'] = true;
         $response['message'] = 'You must enter School Registration ID.';
         echoRespnse(200, $response);
		 return;
	}
	if(empty($city)){
		 $response['error'] = true;
         $response['message'] = 'Please enter the city name.';
         echoRespnse(200, $response);
		 return;
	}
	if(empty($country)){
		 $response['error'] = true;
         $response['message'] = 'Please select a country.';
         echoRespnse(200, $response);
		 return;
	}
    $website = "";
	if (null != $request->getParam('website')) {
	     $website = $request->getParam('website');
	}
	$pincode = "";
	if (null != $request->getParam('pincode')) {
	     $pincode = $request->getParam('pincode');
	}

	$response["debug"] = "";
	$res = $schoolCRUD->update($id, $name, $tagline, $address, $city, $country, $pincode, $phone, $email, $website, $registration_no, $status, $date_updated);
	if ($res) {
                $response["error"] = false;
                $response["message"] = "School - ".$name." profile has been updated successfully. ";
				$qcode = $schoolCRUD->getQCodeByID($id);
				 $response["uploads"] = uploadLogo($request, $qcode);
				/****** COVER UPLOAD ****
			     try{
	$files = $request->getUploadedFiles();
    if (!empty($files['image'])) {
         $uploadResult = uploadProjectImage($id, $files);
         $response["info"] = $uploadResult["message"];
        if (!$uploadResult["error"]) {
             $response["debug"] .= "Success: ".$uploadResult["message"];
         }else{
             $response["debug"] .= "Failre: ".$uploadResult["message"];
         }
    }
			     }catch(Exception $e){
			         $response["debug"] .= "Exception Cover Upload: ".$e->getMessage();
			     }
				*************/
			 }else{
				  $response["error"] = true;
				  $response["info"] = $res["message"] ;
                  $response["message"] = "Failed to update school profile. Please try again.";
				  echoRespnse(200, $response);
			 }
	echoRespnse(200, $response);
	})->add($authenticate);

	
	/******** DELETE SCHOOL *********/
	$app->post('/apis/schools/delete', function ($request, $respo, $args) use ($app) {
	require_once("dbmodels/school.crud.php");
    $schoolCRUD = new SchoolCRUD(getConnection());
	require_once("dbmodels/user.crud.php");
    $userCRUD = new UserCRUD(getConnection());
	$response = array();
    $response["error"] = true;
	$qcode = $request->getParam('item');
	if (empty($qcode)) {
        $response["error"] = true;
        $response["message"] = "Invalid request.";
	    echoRespnse(200, $response);
		return;
		}
	
    if(!$schoolCRUD->isQCodeExists($qcode)){
	 $response['error'] = true;
     $response['message'] = 'This school is not available to modify at this moment. Check back later.';
     echoRespnse(200, $response);
	 return;
	}
	
	$id = $schoolCRUD->getIDByQCode($qcode);
	/********* Validate Authorization **********/
	/********* Only Super Admin can create new school ********/
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
	/********* Validated Authorization ********/
	
	$res = $schoolCRUD->delete($id);
	if ($res) {
        $response["error"] = false;
        $response["message"] = "School profile has been deleted successfully. ";
		$response["id"] = $id;
	    echoRespnse(200, $response);
		}else{
				  $response["error"] = true;
                  $response["message"] = "Failed to delete school profile. Please try again.";
				  echoRespnse(200, $response);
		}
	})->add($authenticate);
?>