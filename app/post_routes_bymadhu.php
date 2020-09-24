<?php
	// Psr-7 Request and Response interfaces
	use Psr\Http\Message\ServerRequestInterface as Request;
	use Psr\Http\Message\ResponseInterface as Response;
    
	/********** CREATE POST *****************/
	$app->get('/create-post', function (Request $request, Response $response, $args){
        require_once("dbmodels/user.crud.php");
        $userCRUD = new UserCRUD(getConnection());
		require_once("dbmodels/class.crud.php");
		$classCRUD = new ClassCRUD(getConnection());
        $classes = $classCRUD->getAllClasses();
        require_once("dbmodels/subject.crud.php");
     	$subjectCRUD = new SubjectCRUD(getConnection());
		$subjects = $subjectCRUD->getAllSubjects();
        require_once("dbmodels/topic.crud.php");
		$topicCRUD = new TopicCRUD(getConnection());
		$topics = $topicCRUD->getAllTopics();
	
		/********** SERVER SESSION CHECK  ***********/
		if(isset($_SESSION["userID"]) && isset($_SESSION["email"]) && isset($_SESSION["api_key"])){
			$thisUser = $userCRUD->getUserByAPIKey($_SESSION["api_key"]);
			if ($thisUser != null && $thisUser["id"] == 1 && $thisUser["role_id"] == 1 ) {
			 }
			 else{
				$uri = $request->getUri()->withPath($this->router->pathFor('unauthorized'));
				return $response->withRedirect((string)$uri);
			 }
		}
		else{
			  $uri = $request->getUri()->withPath($this->router->pathFor('login'));
			  return $response->withRedirect((string)$uri);
		}
		 /********** SERVER SESSION CHECK  ***********/


		$vars = [
			'page' => [
            'name' => 'posts',
            'classes' => $classes,
            'subjects' => $subjects,
            'topics' => $topics,
		    'title' => 'Create E-Content | Talank SMS',
			'description' => 'Talank SAS is a next generation school management and automation system. '
			]
		];
		return $this->view->render($response, 'post-create.twig', $vars);
	})->setName('create-post');

	
	$app->get('/posts', function (Request $request, Response $response, $args){
		require_once("dbmodels/post.crud.php");
        $postCRUD = new PostCRUD(getConnection());
		$posts = $postCRUD->getAllPosts();
		//print_r($posts);die();
		$data = array();
	    if (count($posts) > 0) {
			foreach ($posts as $res) {
		        $postDetails = getPostDetails($res["id"]);
				array_push($data, $postDetails);
			}
	    }
		$vars = [
			'page' => [
			'name' => 'posts',
		    'title' => 'Posts | Talank SMS',
			'description' => 'Talank SAS is a next generation school management and automation system. ',
			'posts' => $data
			]
		];
		return $this->view->render($response, 'post.twig', $vars);
	})->setName('posts');
	

	$app->get('/post-detail/{post_id}', function (Request $request, Response $response, $args){
		$id = $request->getAttribute('post_id');
		// print_r($id);die();

		// require_once("dbmodels/post.crud.php");
  //       $postCRUD = new PostCRUD(getConnection());

	    if (null != $id) {
		    $postDetails = getSinglePost($id);
	    }
		// print_r($postDetails);die();


		$vars = [
			'page' => [
			'name' => 'posts',
		    'title' => 'Post Detail | Talank SMS',
			'description' => 'Talank SAS is a next generation school management and automation system. ',
			'post' => $postDetails
			]
		];
		return $this->view->render($response, 'post-detail.twig.php', $vars);
	})->setName('post-detail');


	$app->get('/manage-posts', function ($request, $response, $args){
		require_once("dbmodels/user.crud.php");
		require_once("dbmodels/utils.crud.php");
		$userCRUD = new UserCRUD(getConnection());
		$utilCRUD = new UtilCRUD(getConnection());
		require_once("dbmodels/post.crud.php");
        $postCRUD = new PostCRUD(getConnection());
		$posts = $postCRUD->getAllPosts();
		$data = array();
	    if (count($posts) > 0) {
			foreach ($posts as $res) {
		        $postProfile = getPostDetails($res["id"]);
				array_push($data, $postProfile);
			}
	    }

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
		
			$vars = [
				'page' => [
				'name' => 'manage-posts',
				'title' => 'Manage E-Contents',
				'description' => 'List of all posts',
				'posts' => $data
				]
			];	
			
			return $this->view->render($response, 'posts-list.twig', $vars);
		})->setName('manage-posts');

	/********** END OF E-CONTENT ROUTE *****************/
   

	$app->post('/apis/posts/create', function ($request, $respo, $args) use ($app) {
	    $post_id = 0;
		require_once("dbmodels/post.crud.php");
	    $PostCRUD = new PostCRUD(getConnection());
	    require_once("dbmodels/post_type.crud.php");
	    $PostTypeCRUD = new PostTypeCRUD(getConnection());
	    require_once("dbmodels/post_prefer.crud.php");
	    $PostPreferCRUD = new PostPreferCRUD(getConnection());
		$response = array();
	    $response["error"] = false;
    
	    $post_page = $request->getParam('post_page');
	    $post_create_title = $request->getParam('post_create_title');
	    $post_create_class = $request->getParam('post_create_class');
	    $post_create_subject = $request->getParam('post_create_subject');
	    $post_create_topic = $request->getParam('post_create_topic');
	    $post_create_content_type = $request->getParam('post_create_content_type');
	    $post_create_content_file = $request->getParam('post_create_content_file');
	    $post_create_content_body = $request->getParam('post_create_content_body');
	    // print_r(count($_FILES['files']['name']));die();
	    $post_create_image = $request->getParam('post_create_image');
	    $post_create_video = $request->getParam('post_create_video');


	    if (null != $request->getParam('is_published') && $request->getParam('is_published') == 'on') {
		    $is_published = 1;	// on or checked
		}
		else{
		 	$is_published = 0;	// off or unchecked
		}

		if (null != $request->getParam('is_private') && $request->getParam('is_private') == 'on') {
		    $is_private = 1;
		}
		else{
		 	$is_private = 0;	
		}

		if (null != $request->getParam('is_restricted') && $request->getParam('is_restricted') == 'on') {
		    $is_restricted = 1;
		}
		else{
		 	$is_restricted = 0;	
		}

		if (null != $request->getParam('display_author') && $request->getParam('display_author') == 'on') {
		    $display_author = 1;
		}
		else{
		 	$display_author = 0;	
		}

		if (null != $request->getParam('display_organization') && $request->getParam('display_organization') == 'on') {
		    $display_organization = 1;
		}
		else{
		 	$display_organization = 0;	
		}	

	    if (null != $request->getParam('allow_likes') && $request->getParam('allow_likes') == 'on') {
		    $allow_likes = 1;
		}
		else{
		 	$allow_likes = 0;	
		}

	    if (null != $request->getParam('allow_comments') && $request->getParam('allow_comments') == 'on') {
		    $allow_comments = 1;
		}
		else{
		 	$allow_comments = 0;	
		}		
 

        /************** Validation Starts ***********/
		if(empty($post_create_title)){
			 $response['error'] = true;
	         $response['message'] = 'Please enter the title name.';
	         echoRespnse(200, $response);
			 return;
		}
		if(empty($post_create_class)){
			 $response['error'] = true;
	         $response['message'] = 'You must enter Class.';
	         echoRespnse(200, $response);
			 return;
		}
		if(empty($post_create_subject)){
			 $response['error'] = true;
	         $response['message'] = 'You must enter Subject.';
	         echoRespnse(200, $response);
			 return;
		}

		$date_created = date('Y-m-d H:i:s');
		$session_logged_userid = $_SESSION['userID'];
		$status = "Pending";

		// additional for post type	 
		if ($post_page === 'edit-post'){

			$files = $request->getUploadedFiles();
			$newCoverfile = $files['post_create_content_file'];

			if ($_FILES['post_create_content_file']['name'] == null){
	    		$filename = $request->getParam('post_create_content_file_editpage');
			}else{
				$filename = $newCoverfile->getClientFilename();
			}
			// $newCoverfile2 = $files['post_create_image'];
			$images_upload = '';
			if ($_FILES['post_create_image']['name'][0] != null){
				foreach ($_FILES['post_create_image']['name'] as $key => $value) {
					$images_upload .= $value.' ';
				}
			}else{
	    		$images_upload = $request->getParam('post_create_image_editpage');
			}
		}
		else // from create post
		{

			$files = $request->getUploadedFiles();
			$newCoverfile = $files['post_create_content_file'];
			$images_upload = '';
			if ($_FILES['post_create_image'] != null){
				foreach ($_FILES['post_create_image']['name'] as $key => $value) {
					$images_upload .= $value.' ';
				}
			}else{
				$images_upload = '';
			}
		}		
		
		// print_r($filename);die();
		$enabled = 1;




		require_once("dbmodels/school.crud.php");
	    $schoolCRUD = new SchoolCRUD(getConnection());
		$qcode = $schoolCRUD->generateCode();

		/********* Validate Authorization **********/
		/********* Only Super Admin can create new school ********/

		// $authUser = getCallerSnapshot($request, $respo);
		require_once("dbmodels/user.crud.php");
	  	$userCRUD = new UserCRUD(getConnection());
	  	//$userCRUD->addToUsage($api_key, $signature);
	  
	 	$response = array();
	    $response["error"] = true;
	    $authArr = $_SESSION['api_key'];
		$api_key = "";
		if(empty($authArr)){
			$response["error"] = true;
	        $response["message"] = "APITester => Authorization token not found with request.";
	        echoRespnse(401, $response);
			$request = $request->withAttribute('error', true);
	        return $response;
		}else{
			$api_key = $authArr;
			$response["error"] = false;
			$response["api_key"] = $api_key;
			$response["caller_role"] = $userCRUD->getRoleByAPIKey($api_key);
			$response["caller_role_name"] = $userCRUD->getRoleName($response["caller_role"]);
			$userRow = $userCRUD->getUserByAPIKey($api_key);
			if($userRow !== null){
				$response["id"] = $userRow["id"];
				$response["school_id"] = $userRow["school_id"];
			}
		}
	



		if(!$response["error"]){
			 if($response["caller_role"] == 1 || $response["caller_role"] == 2){
			 		// print_r('success'); die();
			 } 
			 else {
			 	$response['error'] = true;
	         	$response['message'] = 'You are not authorized to create a section.';
	         	echoRespnse(200, $response);
			 	return;
			 }
		}else{
			 $response['error'] = true;
	         $response['message'] = 'Invalid request. Please use your authentication signature.';
	         echoRespnse(200, $response);
			 return;
		}
		/********* Validate Authorization **********/



		// print_r($result);die();
		if(!$response["error"]){ 		// checking for false

			switch (null != $post_create_content_type) {
				case '1':
					$post_create_content_type_name = 'image';
					break;
				case '2':
					$post_create_content_type_name = 'video';
					break;
				case '3':
					$post_create_content_type_name = 'pdf';
					break;
				case '4':
					$post_create_content_type_name = 'link';
					break;

				default:
					$post_create_content_type_name = '';
					break;
			}

			if ($post_page === 'create-post'){

				$PostCRUD = $PostCRUD->create( $post_create_title ,$post_create_content_type ,$session_logged_userid ,$post_create_class ,$post_create_subject ,$is_private ,$status, $qcode, $is_restricted ,$date_created );

				$PostTypeCRUD = $PostTypeCRUD->create( $post_create_content_type_name, $filename, $post_create_content_body, $images_upload, $post_create_video, $PostCRUD["id"], $enabled );

				$PostPreferCRUD = $PostPreferCRUD->create( $PostCRUD["id"], $is_published, $is_private, $is_restricted, $display_author, $display_organization, $allow_likes, $allow_comments );
					

		        $response["error"] = false;
				$response["id"] = $PostCRUD["id"];
				$response["post_type_id"] = $PostTypeCRUD["id"];
		        $response["message"] = "New post has been created ";
		        $response["message"] .= "and also post type & post prefers is created! ";
		        $response["post_type_code"] = $PostTypeCRUD["code"];
		    }
		    elseif ($post_page === 'edit-post') {
		        $PostCRUD = $PostCRUD->update($post_id, $post_create_title ,$post_create_content_type ,$session_logged_userid ,$post_create_class ,$post_create_subject ,$is_private ,$status, $qcode, $is_restricted ,$date_created );

				$PostTypeCRUD = $PostTypeCRUD->update( $post_create_content_type_name, $filename, $post_create_content_body, $images_upload, $post_create_video, $post_id, $enabled );

				$PostPreferCRUD = $PostPreferCRUD->update( $post_id, $is_published, $is_private, $is_restricted, $display_author, $display_organization, $allow_likes, $allow_comments );
					

		        $response["error"] = false;
				$response["id"] = $post_id;
				// $response["post_type_id"] = $PostTypeCRUD["id"];
		        $response["message"] = "Post has been updated ";
		        $response["message"] .= "and also post type & post prefers is updated! ";
		        // $response["post_type_code"] = $PostTypeCRUD["code"];
		    }
		    else{
		    	 $response['error'] = true;
		         $response['message'] = 'Invalid request. page request not found.';
		         echoRespnse(200, $response);
				 return;
		    }

		}else{
			 $response['error'] = true;
	         $response['message'] = "Invalid request. Please use your authentication signature.";
	         echoRespnse(200, $response);
			 return;
		}
		/********* Validated Authorization ********/
 
		// print_r($response);die();
		/*********############  START LOGO UPLOAD #############**********/
		if ($post_page === 'create-post') {
			$response["uploads"] = uploadImage($request, $qcode, $PostCRUD["id"]);
		}
		// print_r($response['uploads']);die();


// /**######## UPLOAD IMAGES #######**/
// 			if(isset($_FILES['uploads'])){  
// 				foreach($_FILES['uploads']['tmp_name'] as $key => $tmp_name)
// 				{
// 					$numCounts++;
// 					if(!empty($_FILES['upload']['error'][$key]))
// 				    {
// 				            return false; 
// 				    }
// 					if( !empty( $tmp_name ) && is_uploaded_file( $tmp_name ) )
// 					{
// 						$file_name = $key.$_FILES['uploads']['name'][$key];
// 					    $file_size =$_FILES['uploads']['size'][$key];
// 					    $file_tmp =$_FILES['uploads']['tmp_name'][$key];
// 					    $file_type=$_FILES['uploads']['type'][$key];  
// 						$path = "images/portfolio/";
// 						$filePath = $path. "".$file_name;
// 					    move_uploaded_file($file_tmp, $filePath);
// 						$portfolioCRUD->addPortfolioImage($id, $filePath);			
// 						$numUploads++;
// 					}
// 				}
// 			//$response["message"] .= " Image Uploaded : ".$numUploads." of ".$numCounts;
// 			}		
// 			}else{
// 				  $response["error"] = true;
//                   $response["message"] = "Failed to save project. Please try again.";
// 				  echoRespnse(200, $response);
// 			}







		// return json_encode($response);
		echoRespnse(200, $response);
		exit();
		// header('Location: https://localhost/SMARTSCHOOLAUTOMATION/manage-posts');

	});
	


	
	function uploadImage($request, $qcode, $id){

		require_once("dbmodels/school.crud.php");
	    $schoolCRUD = new SchoolCRUD(getConnection());	
		$response = array();
	    $response["error"] = false;	

		$files = $request->getUploadedFiles();
	    
	    if (!empty($files['post_create_content_file'])) {

		    try{
			    $newCoverfile = $files['post_create_content_file'];
			    $cover_file_type = "Unknown";
			    $response["message"] = " Got File.";
				if ($newCoverfile->getError() === UPLOAD_ERR_OK) {
				    $uploadCoverName = $newCoverfile->getClientFilename();
					$uploadCoverName = explode(".", $uploadCoverName);
				    $ext = array_pop($uploadCoverName);
				    $ext = strtolower($ext);
				    $uploadCoverName = $qcode."-banner". "." . $ext;
					
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
					
					$fileToTest = "uploads/images/posts/$uploadCoverName";
					if(file_exists($fileToTest)) {
				    	unlink($fileToTest);
					}
				    $newCoverfile->moveTo($fileToTest);
				    //$docCRUD->updateCover($id, $uploadCoverName);


				    // update content banner file name in post_type table
				    require_once("dbmodels/post_type.crud.php");
	    			$PostTypeCRUD = new PostTypeCRUD(getConnection()); 
				    
				    if($PostTypeCRUD->update_postbanner($id, $uploadCoverName)){
				         $response['message'] .= ' banner has been updated.';
				    }
				}
	    	}catch(Exception $e){
		        $response["message"] .= " Failed to upload banner.";
				//echoRespnse(200, $response);
				//exit;
		    }
		}

		if (!empty($files['post_create_image'])) {
		    try{
			    $newCoverfile = $files['post_create_image'];
			    // print_r($newCoverfile);die();
			    $cover_file_type = "Unknown";
			    $response["message"] = " Got File.";
			    $filenames = '';
			    $i = 1;
				if ($newCoverfile != null) {

					foreach ($newCoverfile as $upload) {
						$uploadCoverName = $upload->getClientFilename();
						// print_r($uploadCoverName);die();
						$uploadCoverName = explode(".", $uploadCoverName);
					    $ext = array_pop($uploadCoverName);
					    $ext = strtolower($ext);
					    $uploadCoverName = $qcode."-image-".$i. "." . $ext;
						
						$file_size = $upload->getSize();
						$cover_file_type = $upload->getClientMediaType();
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
						
						$fileToTest = "uploads/images/posts/$uploadCoverName";
						if(file_exists($fileToTest)) {
					    	unlink($fileToTest);
						}
					    $upload->moveTo($fileToTest);
					    //$docCRUD->updateCover($id, $uploadCoverName);

					    $i++;
					    $filenames .= $uploadCoverName.' ';
					}

				}    
				// print_r($filenames);die();
			    // update content banner file name in post_type table
			    require_once("dbmodels/post_type.crud.php");
    			$PostTypeCRUD = new PostTypeCRUD(getConnection());
			    
			    if($PostTypeCRUD->update_postimage($id, $filenames)){
			         $response['message'] .= ' banner has been updated.';
			    }
				
	    	}catch(Exception $e){
		        $response["message"] .= " Failed to upload banner.";
				//echoRespnse(200, $response);
				//exit;
		    }
		}

		return $response;
	}




	$app->get('/edit-post/{post_id}', function (Request $request, Response $response, $args){
		$id = $request->getAttribute('post_id');


	    if (null != $id) {
		    $postDetails = getOnlySinglePostDetails($id);
	    }
	   
	   	require_once("dbmodels/class.crud.php");
	    $classCRUD = new ClassCRUD(getConnection());
        $classes = $classCRUD->getAllClasses();
         // print_r($postDetails);die();

		$vars = [
			'page' => [
			'name' => 'edit-posts',
		    'title' => 'Create E-Content | Talank SMS',
			'description' => 'Talank SAS is a next generation school management and automation system. ',
			'post' => $postDetails,
			'classes' => $classes,
			]
		];
		return $this->view->render($response, 'edit-post.twig', $vars);
	})->setName('posts');

?>