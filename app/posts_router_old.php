<?php
	use Psr\Http\Message\ServerRequestInterface as Request;
	use Psr\Http\Message\ResponseInterface as Response;
    
	$app->post('/apis/posts/create', function ($request, $respo, $args) use ($app) {
	    $post_id = 0;
		require_once("dbmodels/user.crud.php");
	  	$userCRUD = new UserCRUD(getConnection());
		require_once("dbmodels/post.crud.php");
	    $postCRUD = new PostCRUD(getConnection());
	    require_once("dbmodels/post_image.crud.php");
	    $postImageCRUD = new PostImageCRUD(getConnection());
		require_once("dbmodels/post_tag.crud.php");
	    $postTagCRUD = new PostTagCRUD(getConnection());
	    //require_once("dbmodels/post_prefer.crud.php");
	    //$PostPreferCRUD = new PostPreferCRUD(getConnection());
		$response = array();
	    $response["error"] = false;
        $callerID = 0; 
		
		
	  /********* Validate Authorization **********/
	  $authUser = getCallerSnapshot($request, $respo);
	  if(!$authUser["error"]){
		$callerID = $authUser["caller_id"];
		 if($authUser["caller_role"] == 1 || $authUser["caller_role"] == 2 || $authUser["caller_role"] == 4){
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
		
		/********* Capture Input **********/
	    $post_page = $request->getParam('post_page');
	    $title = $request->getParam('post_create_title');
	    $class_id = $request->getParam('post_create_class');
	    $subject_id = $request->getParam('post_create_subject');
	    $topic_id = $request->getParam('post_create_topic');
	    $post_type = $request->getParam('post_create_post_type');
	    $body = $request->getParam('post_body');
	    $description = $request->getParam('description');
	    $post_create_video = $request->getParam('post_create_video');

		$status = "Pending";
	    if (null != $request->getParam('is_published') && $request->getParam('is_published') == 'on') {
		    $status = "Active";
		}
		$is_private = 0;	
		if (null != $request->getParam('is_private') && $request->getParam('is_private') == 'on') {
		    $is_private = 1;
		}
		$is_restricted = 0;
		if (null != $request->getParam('is_restricted') && $request->getParam('is_restricted') == 'on') {
		    $is_restricted = 1;
		}
        $display_author = 0;
		if (null != $request->getParam('display_author') && $request->getParam('display_author') == 'on') {
		    $display_author = 1;
		}
        $display_organization = 0;
		if (null != $request->getParam('display_organization') && $request->getParam('display_organization') == 'on') {
		    $display_organization = 1;
		}	
        $allow_likes = 0;	
	    if (null != $request->getParam('allow_likes') && $request->getParam('allow_likes') == 'on') {
		    $allow_likes = 1;
		}
        $allow_comments = 0;	
	    if (null != $request->getParam('allow_comments') && $request->getParam('allow_comments') == 'on') {
		    $allow_comments = 1;
		}	
        $qcode = $postCRUD->generateCode();
		
        /************** Validation Starts *******
		if(empty($title)){
			 $response['error'] = true;
	         $response['message'] = 'Please enter a title for this post.';
	         echoRespnse(200, $response);
			 return;
		}
		if(empty($class_id)){
			 $response['error'] = true;
	         $response['message'] = 'You must select a class.';
	         echoRespnse(200, $response);
			 return;
		}
		if(empty($subject_id)){
			 $response['error'] = true;
	         $response['message'] = 'You must select a subject.';
	         echoRespnse(200, $response);
			 return;
		}
		if(empty($post_type)){
			 $response['error'] = true;
	         $response['message'] = 'You must select a post type.';
	         echoRespnse(200, $response);
			 return;
		}
		if(empty($body)){
			 $response['error'] = true;
	         $response['message'] = 'Post content can not be empty.';
	         echoRespnse(200, $response);
			 return;
		}
		if(strlen($body) < 10){
			 $response['error'] = true;
	         $response['message'] = 'Post content is too short.';
	         echoRespnse(200, $response);
			 return;
		}
        ************* Validation Ends ***********/
		
		$date_created = date('Y-m-d H:i:s');
        $is_restricted = 0; 

        	if ($post_page === 'create-post'){
				$processResult = $postCRUD->create($title, $post_type, $callerID, $class_id, $subject_id, $description, $body, $is_private, $status, $qcode, $is_restricted, $date_created);
                 if(!$processResult["error"]){
				 $response["error"] = false;
				 $response["id"] = $processResult["id"];
				 $postID = $response["id"];
		         $response["message"] = "New post has been created successfully.";
				 
				 /************* INSERT TAGS **************/
				 if(null !== $request->getParam('post_tags')){
				 $post_tags = $request->getParam('post_tags');
				 if(!empty($post_tags)){
					 insertPostTag($post_tags, $postID);
				 }
			     }
				 /************** TAGS INSERTED *************/
			
			     /************* INSERT PREFS **************/
				 insertPostPreferences($postID, $is_private, $is_restricted, $display_author, $display_organization, $allow_likes, $allow_comments);
				 /************** PREFS INSERTED ***********/
	
				
				$images_upload = '';
				/*
			    if ($_FILES['post_create_image']['name'][0] != null){
				foreach ($_FILES['post_create_image']['name'] as $key => $value) {
					$images_upload .= $value.' ';
					$response["debug"] = $images_upload;
				}
			    }else{
	    		$images_upload = $request->getParam('post_create_image_editpage');
			    }
				*/
			
/*			
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
			    **************************/			
				 }else{
				 $response["error"] = true;
		         $response["message"] = $processResult["message"]; 
				 }
		    }
		    elseif ($post_page === 'edit-post') {
		        $processResult = $postCRUD->update($post_id, $post_create_title ,$post_create_content_type ,$callerID ,$post_create_class ,$post_create_subject ,$is_private ,$status, $qcode, $is_restricted ,$date_created );

				//$PostTypeCRUD = $PostTypeCRUD->update( $post_create_content_type_name, $filename, $post_create_content_body, $images_upload, $post_create_video, $post_id, $enabled );

				//$PostPreferCRUD = $PostPreferCRUD->update( $post_id, $is_published, $is_private, $is_restricted, $display_author, $display_organization, $allow_likes, $allow_comments );
					

		        $response["error"] = false;
				$response["id"] = $post_id;
				// $response["post_type_id"] = $PostTypeCRUD["id"];
		        $response["message"] = "Post has been updated ";
		        $response["message"] .= "and also post type & post prefers is updated! ";
		        // $response["post_type_code"] = $PostTypeCRUD["code"];
		    }
			
		// print_r($response);die();
		/*********############  START LOGO UPLOAD #############**********/
		if ($post_page === 'create-post') {
			//$response["uploads"] = uploadImage($request, $qcode, $PostCRUD["id"]);
		}
		// print_r($response['uploads']);die();
		
		$response["item"] = getPostDetails($postID, false, true, true);
		echoRespnse(200, $response);
	})->add($authenticate);
	

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
	
	
	
	//Inserts or updates post tags - To be enhanced
	function insertPostTag($postTags, $postID) { 
		 require_once("dbmodels/post_tag.crud.php");
	     $postTagCRUD = new PostTagCRUD(getConnection());
		 $response["tags"] = array();
            if(!empty($postTags)){
		    //$postTags = json_decode($postTags, true);
		    $postTags = explode(",", $postTags);  
		    foreach ($postTags as $value) {
			 echo $value;	
		    $result = $postTagCRUD->create($postID, $value);
            if ($result["code"] == INSERT_SUCCESS) {
				$itemID = $result["id"];
				array_push($response["tags"], $itemID);
			}
		  }
		  return $response["tags"];
        }
	}
	
	//Inserts or updates post preferences
	function insertPostPreferences($postID, $is_private, $is_restricted, $display_author, $display_organization, $allow_likes, $allow_comments) { 
		 require_once("dbmodels/post_preferences.crud.php");
	     $preferenceCRUD = new PostPreferenceCRUD(getConnection());
		
		 if($preferenceCRUD->isPrefsAvailable($postID)){
			 $result = $preferenceCRUD->update($postID, $is_private, $is_restricted, $display_author, $display_organization, $allow_likes, $allow_comments);
             if ($result) {
				return "Post preferences updated successfully.";
			 }
		 }else{
			$result = $preferenceCRUD->create($postID, $is_private, $is_restricted, $display_author, $display_organization, $allow_likes, $allow_comments);
            if ($result["code"] == INSERT_SUCCESS) {
				$itemID = $result["id"];
				return "Post preferences created successfully.";
			}
		 }
	}

?>