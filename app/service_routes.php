<?php 
/*********** SERVICES *************/
$app->get('/add-project', function($request, $response, $args) {
	require_once("dbmodels/user.crud.php");
    $userCRUD = new UserCRUD(getConnection());
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
	 
	 require_once("dbmodels/category.crud.php");
    $categoryCRUD = new CategoryCRUD(getConnection());
    $categories = $categoryCRUD->getAllCategories();
    require_once("dbmodels/service.crud.php");
    $serviceCRUD = new ServiceCRUD(getConnection());
    $project_units = $serviceCRUD->getAllServiceUnits();
    $vars = [
			'page' => [
			'name' => 'manage-projects',
			'page_title' => 'Add New Project',
			'description' => 'Fill up the form below to create a new Project.',
			'categories'=>$categories,
			'project_units'=>$project_units,
			'editMode'=>false
			]
		];	
	return $this->view->render($response, 'service-add.twig', $vars);
})->setName('add-project');

$app->post('/services/create', function ($request, $respo, $args) use ($app) {
	require_once("dbmodels/service.crud.php");
    $serviceCRUD = new ServiceCRUD(getConnection());
	/*
		$helper = new Helper();
	if(!$helper->validateMemberSession()){
		$response['error'] = true;
         $response['message'] = 'You are not authenticated to perform this action.';
         echoRespnse(200, $response);
		 return;
	}	
	*/
	$response = array();
    $response["error"] = false;
	$title = $request->getParam('title');
	$price = $request->getParam('price');
	$unit_amount = $request->getParam('unit_amount');
	$unit_name = $request->getParam('unit_name');
	$tag = $request->getParam('tag');
	$body = $request->getParam('body');
	$pre_body = $request->getParam('pre_body');
	$description = $request->getParam('description');
	$is_published = $request->getParam('is_published');
	$is_front= $request->getParam('is_front');
	$image = "";
	$date_created = date('Y-m-d H:i:s');
	
	if(empty($title)){
		 $response['error'] = true;
         $response['message'] = 'Please enter a title for this project.';
         echoRespnse(200, $response);
		 return;
	}
	if(empty($description)){
		  $response['error'] = true;
         $response['message'] = 'You must add a short description about this project.';
         echoRespnse(200, $response);
		 return;
	}
	if(empty($price)){
		  $response['error'] = true;
         $response['message'] = 'Please enter an offseting price for the project.';
         echoRespnse(200, $response);
		 return;
	}
	if(!is_numeric($price) || $price <= 0){
		  $response['error'] = true;
         $response['message'] = 'Please enter a valid offseting price for the project.';
         echoRespnse(200, $response);
		 return;
	}
if(!is_numeric($price) || $price <= 0){
		  $response['error'] = true;
         $response['message'] = 'Please enter a valid offseting price for the project.';
         echoRespnse(200, $response);
		 return;
	}
		if(empty($unit_name)){
		  $response['error'] = true;
         $response['message'] = 'Please select a unit for amount of carbon.';
         echoRespnse(200, $response);
		 return;
	}
	if(empty($unit_amount)){
		  $response['error'] = true;
         $response['message'] = 'You must enter amount of carbon in .'.$unit_name.' to create a calculation rule';
         echoRespnse(200, $response);
		 return;
	}
	$response["debug"] = "";
	$res = $serviceCRUD->create($title, $price, $unit_amount, $unit_name, $description, $pre_body, $body, $tag, $is_published, $is_front);
	if ($res["code"] == INSERT_SUCCESS) {
                $response["error"] = false;
                $response["message"] = "Your project - ".$title." has been saved successfully. ";
				$id = $res["id"];
				$response["id"] = $id;
				
				/****** COVER UPLOAD *****/
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
				/**************/
			 }else{
				  $response["error"] = true;
				  $response["info"] = $res["message"] ;
                  $response["message"] = "Failed to add project. Please try again.";
				  echoRespnse(200, $response);
			 }
	echoRespnse(200, $response);
	})->add($authenticate);
	
	
	$app->post('/services/update', function ($request, $respo, $args) use ($app) {
	require_once("dbmodels/service.crud.php");
    $serviceCRUD = new ServiceCRUD(getConnection());
	//ADMIN ONLY
	/*
	$helper = new Helper();
	if(!checkAdminSession()){
		$response['error'] = true;
         $response['message'] = 'You are not authenticated to perform this action.';
         echoRespnse(200, $response);
		 return;
	}	*/
	
	$response = array();
    $response["error"] = false;
    $response["info"] = "";
	$title = $request->getParam('title');
	$price = $request->getParam('price');
	$unit_amount = $request->getParam('unit_amount');
	$unit_name = $request->getParam('unit_name');

	$tag = $request->getParam('tag');
	$body = $request->getParam('body');
	$pre_body = $request->getParam('pre_body');
	$description = $request->getParam('description');
	$is_published = $request->getParam('is_published');
	$is_front= $request->getParam('is_front');
	$id = $request->getParam('item_id');
	
	if(empty($id)){
		 $response['error'] = true;
         $response['message'] = 'Invalid request. Please try again.';
         echoRespnse(200, $response);
		 return;
	}
	
	if(!$serviceCRUD->doProjectExists($id)){
		$response['error'] = true;
         $response['message'] = 'The project you are tring to modify is not available.';
         echoRespnse(200, $response);
		 return;
	}	
	
		if(empty($title)){
		 $response['error'] = true;
         $response['message'] = 'Please enter a title for this project.';
         echoRespnse(200, $response);
		 return;
	}
	if(empty($description)){
		  $response['error'] = true;
         $response['message'] = 'You must add a short description about this project.';
         echoRespnse(200, $response);
		 return;
	}
	if(empty($price)){
		  $response['error'] = true;
         $response['message'] = 'Please enter an offseting price for the project.';
         echoRespnse(200, $response);
		 return;
	}
	if(!is_numeric($price) || $price <= 0){
		  $response['error'] = true;
         $response['message'] = 'Please enter a valid offseting price for the project.';
         echoRespnse(200, $response);
		 return;
	}
if(!is_numeric($price) || $price <= 0){
		  $response['error'] = true;
         $response['message'] = 'Please enter a valid offseting price for the project.';
         echoRespnse(200, $response);
		 return;
	}
		if(empty($unit_name)){
		  $response['error'] = true;
         $response['message'] = 'Please select a unit for amount of carbon.';
         echoRespnse(200, $response);
		 return;
	}
	if(empty($unit_amount)){
		  $response['error'] = true;
         $response['message'] = 'You must enter amount of carbon in .'.$unit_name.' to create a calculation rule';
         echoRespnse(200, $response);
		 return;
	}
	
	if(empty($title)){
		 $response['error'] = true;
         $response['message'] = 'Please enter a title for this project.';
         echoRespnse(200, $response);
		 return;
	}
	
	$res = $serviceCRUD->update($id, $title, $price, $unit_amount, $unit_name, $description, $pre_body, $body, $tag, $is_published, $is_front, $price);
	if ($res) {
        $response["error"] = false;
        $response["message"] = "Project has been updated successfully. ";
		$response["id"] = $id;
		
		
	//$image = $request->getParam('image');
    $files = $request->getUploadedFiles();
    if (!empty($files['image'])) {
         $uploadResult = uploadProjectImage($id, $files);
         $response["info"] = $uploadResult["message"];
        if (!$uploadResult["error"]) {
             $response["message"] .= " ".$uploadResult["message"];
         }
    }
    echoRespnse(200, $response);
	}else{
				  $response["error"] = true;
                  $response["message"] = "Failed to update project. Please try again.";
				  echoRespnse(200, $response);
		}
	})->add($authenticate);
	

	$app->get('/edit-project/{id}', function($request, $response, $args) {
    require_once("dbmodels/service.crud.php");
    $serviceCRUD = new ServiceCRUD(getConnection());
    require_once("dbmodels/category.crud.php");
    $categoryCRUD = new CategoryCRUD(getConnection());
    $categories = $categoryCRUD->getAllCategories();
     $project_units = $serviceCRUD->getAllServiceUnits();
	//ADMIN ONLY
	$helper = new Helper();
	if(!$helper->validateMemberSession()){
		$uri = $request->getUri()->withPath($this->router->pathFor('home')); 
        return $response->withRedirect((string)$uri);
	}	
	$id = $request->getAttribute('id');
	$service = $serviceCRUD->getID($id);
	if($service !== NULL && count($service) > 0){
	$image = $service["image"];
	$title= $service["title"];
	if(empty($title)){
		$uri = $request->getUri()->withPath($this->router->pathFor('404')); 
        return $response->withRedirect((string)$uri);
	}
	$body = $service["body"];
	$is_published = $service["is_published"];
	$is_front = $service["is_front"];
	$price = $service["price"];
	$pre_body= $service["pre_body"];
	$description = $service["description"];
	$category_id = $service["category_id"];
	$category = $categoryCRUD->getNameByID($category_id);
	$tag= $service["tag"];
	}else{
	$uri = $request->getUri()->withPath($this->router->pathFor('404')); 
    return $response->withRedirect((string)$uri);
	}
	
    $vars = [
			'page' => [
			'name' => 'manage-projects',
			'page_title' => 'Update Project',
			'description' => 'Update your Existing Project',
			'categories'=>$categories,
			'project_units'=>$project_units,
			'editMode'=>true,
			],
			'service' => [
			'service_id' => $id,
			'title' => $title,
            'body' => $body,
			'image' => $image,
			'category_id' => $category_id,
			'category' => $category,
			'pre_body' => $pre_body,
			'description' => $description,
			'is_published' => $is_published,
			'is_front' => $is_front,
			'price' => $price,
			'unit_amount' => $service["unit_amount"],
			'unit_name' => $service["unit_name"],
			'tag' => $tag			
			]
		];	
	return $this->view->render($response, 'service-edit.twig', $vars);
})->setName('edit-project');

	/*************** DISPLAY PROJECT PAGE ***************/
	$app->get('/view-project/{id}', function ($request, $response, $args)   {
	require_once("dbmodels/service.crud.php");
    $serviceCRUD = new ServiceCRUD(getConnection());
    	  require_once("dbmodels/category.crud.php");
    $categoryCRUD = new CategoryCRUD(getConnection());
    require_once("dbmodels/user_offset.crud.php");
    $userOffsetCRUD = new UserOffsetCRUD(getConnection());
    require_once("dbmodels/user.crud.php");
    $userCRUD = new UserCRUD(getConnection());
	$id = $request->getAttribute('id');
	if(!$serviceCRUD->doProjectExists($id)){
		$uri = $request->getUri()->withPath($this->router->pathFor('404')); 
        return $response->withRedirect((string)$uri);
	}
	$service = $serviceCRUD->getID($id);
	$other_services = $serviceCRUD->getAllOtherServices($id);
	$thisProject = array();
	if($service !== NULL){
	    	$thisProject["id"] = $service["id"];
	$thisProject["title"] = $service["title"];
	$thisProject["image"] = $service["image"];
	$thisProject["body"] = $service["body"];
	$thisProject["pre_body"] =$service["pre_body"];
	
	$thisProject["price"] = $service["price"];
	$thisProject["unit_amount"] =$service["unit_amount"];
	$thisProject["unit_name"] = $service["unit_name"];

	$thisProject["category"] = $categoryCRUD->getNameByID($sub_category_id);
	$thisProject["description"] = $service["description"];
	$rawTags = $service["tag"];
	$thisProject["tag"] = array();
	if(!empty($rawTags)){
	    $thisProject["tag"]= explode(',', $rawTags);
	}
	
	$thisProject["relatedOffsets"] = $userOffsetCRUD->getCompletedOffsetsForProject($service["id"]);
	
	$thisProject["sumOffsets"] = $userOffsetCRUD->getSumTotalOffsetForProject($service["id"]);
	
	$vars = [
			'page' => [
			'name' => 'manage-projects',
			'page_title' => $service["title"],
			'other_services' => $other_services,
			'description' => 'Check out what services we offer at Integrity Solutions Consulting.',
			'project' => $thisProject
			]
		];	
	}else{
		$uri = $request->getUri()->withPath($this->router->pathFor('404')); 
        return $response->withRedirect((string)$uri);
	}
		return $this->view->render($response, 'view-service.twig', $vars);
	})->setName('view-service');
	
	
	$app->get('/manage-projects', function ($request, $response, $args){
	require_once("dbmodels/service.crud.php");
	require_once("dbmodels/category.crud.php");
	require_once("dbmodels/utils.crud.php");
	$categoryCRUD = new CategoryCRUD(getConnection());
    require_once("dbmodels/user_offset.crud.php");
    $userOffsetCRUD = new UserOffsetCRUD(getConnection());
    $utilCRUD= new UtilCRUD(getConnection());
	require_once("dbmodels/user.crud.php");
    $userCRUD = new UserCRUD(getConnection());
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
    $serviceCRUD = new ServiceCRUD(getConnection());
    $data = $serviceCRUD->getAllServices();
    $offset_projects = array();
    	if (count($data) > 0) {
			   foreach ($data as $row) {
			   $tmp = array();
               $tmp["id"] = $row["id"];
               $tmp["title"] = $row["title"];
               $tmp["price"] = $row["price"];
                $tmp["unit_amount"] = $row["unit_amount"];
                $tmp["unit_name"] = $row["unit_name"];
                $tmp["category"] = $categoryCRUD->getNameByID($row["category_id"]);
                $tmp["date_created"] = $utilCRUD->getFormalDate($row["timestamp"]);
                $tmp["numRelatedOffsets"] = $userOffsetCRUD->getNumCompletedOffsetsForProject($row["id"]);
	
	          $tmp["sumOffsets"] = $userOffsetCRUD->getSumTotalOffsetForProject($row["id"]);
	          if(empty($tmp["sumOffsets"])){
	              $tmp["sumOffsets"] = 0;
	          }
			   array_push($offset_projects, $tmp);
			   }
	}
		$vars = [
			'page' => [
			'page_title' => 'List All Projects',
			'description' => 'List of all carbon offsetting projects',
			'data' => $offset_projects,
			],
			'sessions' => [
			'first_name' => $_SESSION["first_name"],
			'last_name' => $_SESSION["last_name"],
			'user_name' => $_SESSION["user_name"],
			'api_key' => $_SESSION["api_key"],
			'email' => $_SESSION["email"]
			]
		];	
		
		return $this->view->render($response, 'admin_services_listing.twig', $vars);
	})->setName('manage-projects');
	
	
	/************* UPDATE IMAGE ***********/
	$app->post('/services/media/update', function ($request, $respo, $args) use ($app) {
	require_once("dbmodels/service.crud.php");
    $serviceCRUD = new ServiceCRUD(getConnection());
	$response = array();
    $response["error"] = false;
	$id = $request->getParam('service_id');
	$image = $request->getParam('image');
    $files = $request->getUploadedFiles();
    if (empty($files['image'])) {
         $response['error'] = true;
         $response['message'] = 'Upload a valid image for service.';
         echoRespnse(200, $response);
		 return;
    }
    $newfile = $files['image'];
    $file_type = "Unknown";
	if ($newfile->getError() === UPLOAD_ERR_OK) {
    $uploadFileName = $newfile->getClientFilename();
	$uploadFileName = explode(".", $uploadFileName);
    $ext = array_pop($uploadFileName);
    $ext = strtolower($ext);
    $uploadFileName = $id. "." . $ext;
		
	$file_size = $newfile->getSize();
	$file_type = $newfile->getClientMediaType();
	if(!$file_type == "image/jpg" || !$file_type == "image/jpeg" || !$file_type == "image/png"){
		 $response['error'] = true;
          $response['message'] = 'Please upload a png, jpg or jpeg image file.';
         echoRespnse(200, $response);
		 return;
	}
	
	if($file_size > 500000){
		 $response['error'] = true;
         $response['message'] = 'Upload an image of size not more than 500 KB.';
         echoRespnse(200, $response);
		 return;
	}
    $newfile->moveTo("images/services/$uploadFileName");
	$res = $serviceCRUD->updateImage($id, $uploadFileName);
	if (!$res["error"]) {
        $response["error"] = false;
        $response["message"] = "Service image been updated successfully. ";
		$response["id"] = $id;
	    echoRespnse(200, $response);
		}else{
				  $response["error"] = true;
                  $response["message"] = "Failed to update service image. Please try again.".$res["message"];
				  echoRespnse(200, $response);
		}
	}else{
	     $response['error'] = true;
         $response['message'] = 'Upload a valid image for blog.';
         echoRespnse(200, $response);
		 return;
    }
	});
	
	
		/************* UPDATE IMAGE ***********/
	function uploadProjectImage($id, $files) {
	require_once("dbmodels/service.crud.php");
    $serviceCRUD = new ServiceCRUD(getConnection());
	$response = array();
    $response["error"] = false;
    if (empty($files['image'])) {
         $response['error'] = true;
         $response['message'] = 'Upload a valid image for service.';
		 return $response;
    }
    $newfile = $files['image'];
    $file_type = "Unknown";
	if ($newfile->getError() === UPLOAD_ERR_OK) {
    $uploadFileName = $newfile->getClientFilename();
	$uploadFileName = explode(".", $uploadFileName);
    $ext = array_pop($uploadFileName);
    $ext = strtolower($ext);
    $uploadFileName = $id. "." . $ext;
		
	$file_size = $newfile->getSize();
	$file_type = $newfile->getClientMediaType();
	if(!$file_type == "image/jpg" || !$file_type == "image/jpeg" || !$file_type == "image/png"){
		 $response['error'] = true;
          $response['message'] = 'Please upload a png, jpg or jpeg image file.';
		 return $response;
	}
	
	if($file_size > 1000000){
		 $response['error'] = true;
         $response['message'] = 'Upload an image of size not more than 1 MB.';
		 return $response;
	}
    $newfile->moveTo("images/services/$uploadFileName");
	$res = $serviceCRUD->updateImage($id, $uploadFileName);
	if (!$res["error"]) {
        $response["error"] = false;
        $response["message"] = "Project cover image been updated successfully. ";
		$response["id"] = $id;
	    return $response;
		}else{
				  $response["error"] = true;
                  $response["message"] = "Failed to update project cover image. Please try again.".$res["message"];
				  return $response;
		}
	}else{
	     $response['error'] = true;
         $response['message'] = 'Upload a valid project cover image.';
         return $response;
    }
	}
	
	
/******** DELETE SERVICE *********/
	$app->post('/services/delete', function ($request, $respo, $args) use ($app) {
	require_once("dbmodels/service.crud.php");
    $serviceCRUD = new ServiceCRUD(getConnection());
    require_once("dbmodels/user.crud.php");
    $userCRUD = new UserCRUD(getConnection());
	$response = array();
    $response["error"] = true;
	$id = $request->getParam('service_id');
	
		if(!$serviceCRUD->doProjectExists($id)){
	 $response['error'] = true;
     $response['message'] = 'The project you are trying to delete is not available.';
     echoRespnse(200, $response);
	 return;
	}
	

	/********** SERVER SESSION CHECK  ***********/
	if(isset($_SESSION["userID"]) && isset($_SESSION["email"]) && isset($_SESSION["api_key"])){
    $thisUser = $userCRUD->getUserByAPIKey($_SESSION["api_key"]);
	if ($thisUser != null && $thisUser["id"] == 1 && $thisUser["role_id"] == 1 ) {
	 }else{
		 $response["message"] = "You are not authorized to perform this action. ";
		$response["id"] = $id;
	    echoRespnse(200, $response);
	    return;
	 }
	}else{
	    $response["message"] = "You are not authorized to perform this action. ";
		$response["id"] = $id;
	    echoRespnse(200, $response);
	    return;
	}
	 /********** SERVER SESSION CHECK  ***********/
	$res = $serviceCRUD->delete($id);	
	if ($res) {
        $response["error"] = false;
        $response["message"] = "Project has been deleted successfully. ";
		$response["id"] = $id;
	    echoRespnse(200, $response);
		}else{
				  $response["error"] = true;
                  $response["message"] = "Failed to delete project. Please try again.";
				  echoRespnse(200, $response);
		}
	})->add($authenticate);
	
	
	
	/******** FETCH SERVICE *********/
	$app->post('/projects/fetch', function ($request, $respo, $args) use ($app) {
	require_once("dbmodels/service.crud.php");
    $serviceCRUD = new ServiceCRUD(getConnection());
	$response = array();
    $response["error"] = true;
	$id = $request->getParam('projectID');
	$res = $serviceCRUD->getID($id);	
	if ($res) {
        $response["error"] = false;
        $response["message"] = "Project detailed fetched. ";
		$response["project_title"] = $res["title"];
		$response["project_image"] = $res["image"];
		$response["project_price"] = $res["price"];
		$response["unit_amount"] = $res["unit_amount"];
		$response["unit_name"] = $res["unit_name"];
		$unitPrice = number_format((float)($res["price"]/$res["unit_amount"]), 4, '.', '');
		$response["project_unit_price"] = $unitPrice;
		
	    echoRespnse(200, $response);
		}else{
				  $response["error"] = true;
                  $response["message"] = "Failed to fetch project details.";
				  echoRespnse(200, $response);
		}
	});
	
?>