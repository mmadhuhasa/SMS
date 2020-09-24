<?php
	use Psr\Http\Message\ServerRequestInterface as Request;
	use Psr\Http\Message\ResponseInterface as Response;
    
	$app->post('/apis/posts/create', function ($request, $respo, $args) use ($app) {
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
	    if (null == $request->getParam('post_qcode') ){
	    	$post_qcode = 0;
	    }else{
	    	$post_qcode =  $request->getParam('post_qcode');
	    }
	    $post_title = $request->getParam('post_title');
	    $class_id = $request->getParam('post_class');
	    $subject_id = $request->getParam('post_subject');
	    $topic_id = $request->getParam('post_topic');
	    $post_type = $request->getParam('post_type');
	    $post_tags = $request->getParam('post_tags');
	    $post_body = $request->getParam('post_body'); 
	    $post_description = $request->getParam('post_description');
	    $post_link = $request->getParam('link');
	    // print_r($post_tags);die();	
		$post_status = "Active";
	    if (null != $request->getParam('is_published') && $request->getParam('is_published') == 'on') {
		    $post_status = "Pending";
		}
		$post_is_private = 0;	
		if (null != $request->getParam('is_private') && $request->getParam('is_private') == 'on') {
		    $post_is_private = 1;
		}
		$post_is_restricted = 0;
		if (null != $request->getParam('is_restricted') && $request->getParam('is_restricted') == 'on') {
		    $post_is_restricted = 1;
		}
        $post_display_author = 0;
		if (null != $request->getParam('display_author') && $request->getParam('display_author') == 'on') {
		    $post_display_author = 1;
		}
        $post_display_organization = 0;
		if (null != $request->getParam('display_organization') && $request->getParam('display_organization') == 'on') {
		    $post_display_organization = 1;
		}	
        $post_allow_likes = 0;	
	    if (null != $request->getParam('allow_likes') && $request->getParam('allow_likes') == 'on') {
		    $post_allow_likes = 1;
		}
        $post_allow_comments = 0;	
	    if (null != $request->getParam('allow_comments') && $request->getParam('allow_comments') == 'on') {
		    $post_allow_comments = 1;
		}

		if($post_page === 'create-post'){
        	$post_qcode = $postCRUD->generateCode();	
		}
		
        //************* Validation Starts *******//
		if(empty($post_title)){
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
		if(empty($post_body)){
			 $response['error'] = true;
	         $response['message'] = 'Post content can not be empty.';
	         echoRespnse(200, $response);
			 return;
		}
		if(strlen($post_body) < 100){
			 $response['error'] = true;
	         $response['message'] = 'Post content is too short.';
	         echoRespnse(200, $response);
			 return;
		}
        //************* Validation Ends **********//
		
		$date_created = date('Y-m-d H:i:s');
        // $is_restricted = 0; 

    	if ( $post_page === 'create-post' )
    	{

    		/************* Insert in posts table **************/
			$processResult = $postCRUD->create($post_title, $post_type, $callerID, $class_id, $subject_id, $topic_id, $post_description, $post_body, $post_status, $post_qcode, $date_created, $post_link);
			
    		/************* Inserted id from posts table **************/
             if(!$processResult["error"]){
				 $response["error"] = false;
				 $response["id"] = $processResult["id"];
				 $postID = $response["id"];
		         $response["message"] = "New post has been created successfully.";
			

				 /************* INSERT in post TAGS table **************/
				 if(null !== $request->getParam('post_tags')){
				 	 $post_tags = $request->getParam('post_tags');
					 if(!empty($post_tags)){
						 insertPostTag($post_tags, $postID);
					 }
			     }
			

			     /************* INSERT in post PREFS table **************/
				 insertPostPreferences($postID, $post_is_private, $post_is_restricted, $post_display_author, $post_display_organization, $post_allow_likes, $post_allow_comments);

			     /************* INSERT iamges in post image table **************/
				 $image_uploads = uploadImage($request, $post_qcode, $postID);
				//print_r($image_uploads);die();
			 }else{
			 	 $response["error"] = true;
	        	 $response["message"] = $processResult["message"]; 
			 }

	    }
	    elseif ( $post_page === 'edit-post' ) 
	    {
	    	$postID = $postCRUD->getIDByQCode($post_qcode);

    		/************* Update in posts table **************/
	        $processResult = $postCRUD->update($postID, $post_title, $post_type, $callerID, $class_id, $subject_id, $topic_id, $post_description, $post_body, $post_status, $post_qcode, $date_created, $post_link);

			/************* Inserted id from posts table **************/
             if(!$processResult["error"]){
				 $response["error"] = false;
				 // $response["id"] = $processResult["id"];
				 // $postID = $response["id"];
		         $response["message"] = "New post has been updated successfully.";
			

				 /************* INSERT in post TAGS table **************/
				 if(null !== $request->getParam('post_tags')){
				 	 $post_tags = $request->getParam('post_tags');
					 if(!empty($post_tags)){
						 insertPostTag($post_tags, $postID);
					 }
			     }
			

			     /************* INSERT in post PREFS table **************/
				 insertPostPreferences($postID, $post_is_private, $post_is_restricted, $post_display_author, $post_display_organization, $post_allow_likes, $post_allow_comments);


			     /************* INSERT iamges in post image table **************/
				 $image_uploads = uploadImage($request, $post_qcode, $postID);
			
			 }else{
			 	 $response["error"] = true;
	        	 $response["message"] = $processResult["message"]; 
			 }	

	        $response["error"] = false;
			$response["id"] = $postID;
			// $response["post_type_id"] = $PostTypeCRUD["id"];
	        $response["message"] = "Post has been updated ";
	        $response["message"] .= "and also post type & post prefers is updated! ";
	        // $response["post_type_code"] = $PostTypeCRUD["code"];
	    }
		// $response["item"] = getPostDetails($postID, false, true, true);
		echoRespnse(200, $response);
	    
	})->add($authenticate);
	

	function uploadImage($request, $qcode, $id){

		$response = array();
	    $response["error"] = false;	

		$files = $request->getUploadedFiles();
	    

		if (!empty($files['uploads'])) {

		    try{
			    $newCoverfile = $files['uploads'];
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
					    $uploadCoverName = $qcode."-".$i. "." . $ext;
						
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
							 return $response;
						}
						
						$fileToTest = "uploads/images/posts/$uploadCoverName";
						if(file_exists($fileToTest)) {
					    	unlink($fileToTest);
						}
					    $upload->moveTo($fileToTest);
					    //$docCRUD->updateCover($id, $uploadCoverName);

					 
					    /********* Insert Images in post image table *********/
					    require_once("dbmodels/post_image.crud.php");
	    				$PostImageCRUD = new PostImageCRUD(getConnection());	
					    $result = $PostImageCRUD->addPostImage($id, $uploadCoverName);
						
			            if ($result["code"] == INSERT_SUCCESS) {
							$itemID = $result["id"];
							$response["images_id"] = $itemID;
							$response["images_name"] = $uploadCoverName;
						}

					    // $filenames .= $uploadCoverName.' ';
					    $i++;

					}

				}    
			    
			    // if($PostTypeCRUD->update_postimage($id, $filenames)){
			    //      $response['message'] .= ' image has been updated.';
			    // }
				
	    	}catch(Exception $e){
		        $response["message"] .= " Failed to upload iamges.";
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

	    $postTagsDB_raw = $postTagCRUD->getOnlyTagsForPost($postID); 
	    $postTagsDB = array();
	    if (isset($postTagsDB_raw)) {
	    	foreach ($postTagsDB_raw as $key => $value) {
	    		foreach ($value as $key => $item) {
					array_push($postTagsDB, $item);
				}	
	    	}
	    }
	    
		$response["tags"] = array();

        if( !empty($postTags) && empty($postTagsDB) ){			// check from create post
		    //$postTags = json_decode($postTags, true);
		    $postTags = explode(",", $postTags);  
		    foreach ($postTags as $value) {
			 	// echo $value;	
		    	$result = $postTagCRUD->create($postID, $value);
	            if ($result["code"] == INSERT_SUCCESS) {
					$itemID = $result["id"];
					array_push($response["tags"], $itemID);
				}
	  		}
	  		return $response["tags"];
    	}

    	if( !empty($postTags) && !empty($postTagsDB) ){		// check from edit post
		    //$postTags = json_decode($postTags, true);
		    $postTags = explode(",", $postTags);  
		    foreach ($postTags as $value) {
		    	// echo $value;	
    			if (!in_array($value, $postTagsDB)){			// check each postTag
			 		$result = $postTagCRUD->create($postID, $value);

			 		array_push($postTagsDB, $value);				// save this value to this variable for further check

		            if ($result["code"] == INSERT_SUCCESS) {
						$itemID = $result["id"];
						array_push($response["tags"], $itemID);
					}
			 	}
			}
			foreach ($postTagsDB as $value) {
		    	// echo $value;	
    			if (!in_array($value, $postTags)){			// check each postTag
			 		$result = $postTagCRUD->deleteTag($value, $postID);
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





		/**************** GET LIST OF SUBJECTS BASED ON CLASS SELECTION FOR POST ***********/
    $app->get('/apis/posts/subjects/{class_id}', function($request, $response, $args) {
    	$class_id = $request->getAttribute('class_id');

    	require_once("dbmodels/subject.crud.php");
	    $subjectCRUD = new SubjectCRUD(getConnection());
		$var_response = array();
		// print_r('hi'); die();
        try{
			$dataArr = $subjectCRUD->getAllSubjectsForclass($class_id);
			// $count = count($dataArr) - 1;
			// // print_r( $count);die();
			// if($count >= 0){
   //      		$var_response = "<option>Select subject";
			// 	for ($i=$count; $i >= 0 ; $i--) { 
			// 		$id = $dataArr[$i]['id'];
			// 		$title = $dataArr[$i]['title'];
			// 		$var_response .= "<option value=$id>$title";
			// 		// htmlspecialchars((string)$enter_string))
			// 		// echo "<input type=\"text\">";
			// 	}

		 //    }else{
		 //        $var_response = '<option>Subject not available</option>';
		 //    }
			$var_response = $dataArr;

		}catch(Exception $e){
			$var_response["error"] = true;
            $var_response["message"] = "Failed to fetch data => ".$e->getMessage();
		}

        echoRespnse(200, $var_response);
        // })->add($authenticate);
    });


    	/**************** GET LIST OF TOPICS BASED ON SUBJECT SELECTION FOR POST ***********/
    $app->get('/apis/posts/topics/{subject_id}', function($request, $response, $args) {
    	$subject_id = $request->getAttribute('subject_id');

		require_once("dbmodels/topic.crud.php");
	   	$topicCRUD = new TopicCRUD(getConnection());
		$var_response = array();

        try{
			$dataArr = $topicCRUD->getAllTopicsForclass($subject_id);

			$var_response = $dataArr;
		}catch(Exception $e){
			$var_response["error"] = true;
            $var_response["message"] = "Failed to fetch data => ".$e->getMessage();
		}

        echoRespnse(200, $var_response);
        // })->add($authenticate);
 

    });


   	/**************** Delete post from manage post ***********/
	$app->post('/delete-post', function (Request $request, Response $response, $args) use ($app) {
		// $post_qcode = $request->getAttribute('post_qcode');		// for GET METHOD
	    $post_qcode = $request->getParam('item_qcode');
	    
	   	require_once("dbmodels/post.crud.php");
	    $postCRUD = new PostCRUD(getConnection());
	    require_once("dbmodels/post_image.crud.php");
	    $postImageCRUD = new PostImageCRUD(getConnection());
		require_once("dbmodels/post_tag.crud.php");
	    $postTagCRUD = new PostTagCRUD(getConnection());
	    require_once("dbmodels/post_preferences.crud.php"); 
	    $PostPreferCRUD = new PostPreferenceCRUD(getConnection());
	    require_once("dbmodels/post_like.crud.php"); 
		$postLikeCRUD = new PostLikeCRUD(getConnection());
		require_once("dbmodels/post_comment.crud.php");
		$postCommentCRUD = new PostCommentCRUD(getConnection());
		require_once("dbmodels/post_view.crud.php");
		$postViewCRUD = new PostViewCRUD(getConnection());

	    $postID = $postCRUD->getIDByQCode($post_qcode);


	    // deleting post records in all related tables
        $delete_post = $postCRUD->deletePost($post_qcode);

        if( $postImageCRUD->isImageAvailable($postID) ){
			$delete_postImage = $postImageCRUD->deleteAll($postID);
		}
		if( $PostPreferCRUD->isPrefsAvailable($postID) ){
			$delete_postPrefer = $PostPreferCRUD->deleteAll($postID);
		}
        if( $postTagCRUD->isTagAvailable($postID) ){
			$delete_postTag = $postTagCRUD->deleteAll($postID);
		}
		if( $postLikeCRUD->isLikeAvailable($postID) ){
			$delete_postLike = $postLikeCRUD->deleteAll($postID);
		}
		if( $postCommentCRUD->isCommentAvailable($postID) ){
			$delete_postComment = $postCommentCRUD->deleteAll($postID);
		}
		if( $postViewCRUD->isViewAvailable($postID) ){
			$delete_postView = $postViewCRUD->deleteAll($postID);
		}


		// deleting multiple image files in directory
		$filename = $post_qcode;
		$i = 1;
        $response = array();	
        $filepath = "uploads/images/posts/".$filename."-".$i;	

		do {
			if( file_exists($filepath.".PNG") ){
				// @unlink($filepath."png");
				// clearstatcache(TRUE, $filepath."png");
	        	$response["file"] = "file deleted".$filepath.".PNG";
		    	unlink($filepath.".PNG");
			} 
			else if( file_exists($filepath.".JPG") ){
	        	$response["file"] = "file deleted".$filepath.".JPG";
		    	unlink($filepath.".JPG");
			}
			else if ( file_exists($filepath.".JPEG") ){
	        	$response["file"] = "file deleted".$filepath.".JPEG";
	        	unlink($filepath.".JPEG");
			}
			else{
	        	$response["file"] = "file not found or not deleted-".$filepath;
			}

		    $i++;
			$filepath = "uploads/images/posts/".$filename."-".$i;	 
		} while (file_exists($filepath.".PNG") || file_exists($filepath.".JPG") || file_exists($filepath.".JPEG") ); 



		if ($delete_post) {
	        $response["error"] = false;
	        $response["message"] = "Post has been deleted successfully. ";
		    echoRespnse(200, $response);
		}
		else{
		    $response["error"] = true;
            $response["message"] = "Post to delete subject. Please try again.";
		    echoRespnse(200, $response);
		}
	})->setName('delete-posts')->add($authenticate); 



	// update view count for this post
	function updateView($id){
	    $user_id = $_SESSION["userID"];

	    require_once("dbmodels/post_view.crud.php");
        $postViewCRUD = new PostViewCRUD(getConnection());
        // debug_print_backtrace();
        $count_view = $postViewCRUD->getPostViewCount($id);

        if($count_view == null || $count_view == 0){
        	$first_view = 1;
			$postViewCRUD->create($id, $user_id);
        }
        else{
        	// $postViewCRUD->update($id, $user_id);
			$postViewCRUD->create($id, $user_id);	
		}

	}
	

	  /*************** GET SINGLE POST DETAILS ******************/
	function getSinglePostDetails($qcode, $thumbnail = true, $displayStats = true, $listDetails = false) {
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

	    $res = $postCRUD->getByQCode($qcode);

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
			if($postTagCRUD->getNumTags($postFullDetails["id"]) > 0){
				$postFullDetails["tags"] = $postTagCRUD->getTagsForPost($postFullDetails["id"]);
			}
			$postFullDetails["prefs"] = array();
			if($postPreferenceCRUD->isPrefsAvailable($postFullDetails["id"]) > 0){
				$postFullDetails["prefs"] = $postPreferenceCRUD->getPrefsFor($postFullDetails["id"]);
			}
			$postFullDetails["images"] = array();
			if($postImageCRUD->isImageAvailable($postFullDetails["id"]) > 0){
				$postFullDetails["images"] = $postImageCRUD->getImages($postFullDetails["id"]);
			}
			
			//Post Statistics
			if($displayStats){
				$postFullDetails["numViews"] = $postViewCRUD->getPostViewCount($postFullDetails["id"]);
				$postFullDetails["numUniqueViews"] = $postViewCRUD->getPostViewCountUnique($postFullDetails["id"]);
				$postFullDetails["numLikes"] = $postLikeCRUD->getNumLikes($postFullDetails["id"]);
				$postFullDetails["numComments"] = $postCommentCRUD->getNumCommentsFor($postFullDetails["id"]);
			}
			//Post Attributes List
			if($listDetails){
				$postFullDetails["comments"] = $postCommentCRUD->getCommentsFor($id);

				$user_info = array();	
				foreach ($postFullDetails["comments"] as $key => $value) {
					$user_info = $userCRUD->getUserImage($value['user_id']);
					array_push( $postFullDetails["comments"][$key], array_reduce($user_info, 'array_merge', array()) );
				}
				// print_r($postFullDetails["comments"]);die();
			}
		    return $postFullDetails;
		  }
		  return NULL;
	}



	   	/**************** Comment post from detail post ***********/
	$app->post('/apis/posts/comment', function (Request $request, Response $response, $args){

	    // $data = $request->getQueryParams();		// for GET METHOD
	    // $post_page = $data['post_page'];		// for GET METHOD
	    $post_page = $request->getParam('post_page');
	    $user_id = $request->getParam('user_id');
	    $post_id = $request->getParam('post_id');
	    $comment = $request->getParam('comment_box');

	   	require_once("dbmodels/post_comment.crud.php");
	    $postCommentCRUD = new PostCommentCRUD(getConnection());
        $result = $postCommentCRUD->create($comment, $post_id, $user_id);

        $response = array();	
		if ($result['error'] === false) {
	        $response["error"] = false;
	        $response["message"] = "Comment has been created successfully. ";
			$response["status"] = "commented";
			$response["comment_id"] = $result['id'];

			require_once("dbmodels/post_comment.crud.php");
	    	$postCommentCRUD = new PostCommentCRUD(getConnection());

        	$postComments = $postCommentCRUD->getID($response["comment_id"]);
	        	$response["comment"] = $postComments['comment'];
	        	$response["post_id"] = $postComments['post_id'];
	        	$response["user_id"] = $postComments['user_id'];
	        	$response["date_created"] = $postComments['date_created'];

			$response["numComments"] = $postCommentCRUD->getNumCommentsFor($response["post_id"]);

        	require_once("dbmodels/user.crud.php");
			$userCRUD = new UserCRUD(getConnection());

        	$response["author_name"] = $userCRUD->getNameByID($response["user_id"]);
			$response["author_role"] = $userCRUD->getRoleNameFromUsers($response["user_id"]);
			$response["author_image"] = $userCRUD->getImageByID($response["user_id"]);

		    echoRespnse(200, $response);
		}
		else{
		    $response["error"] = true;
            $response["message"] = "Comment not create. Please try again.";
			$response["status"] = "notcommented";
		    echoRespnse(200, $response);
		}

	})->setName('comment-posts')->add($authenticate); 



	   	/**************** Like post from detail post ***********/
	$app->post('/apis/posts/like', function (Request $request, Response $response, $args) {

	    $post_page = $request->getParam('post_page');
	    $button_name = $request->getParam('button_name');
	    $user_id = $request->getParam('user_id');
	    $post_id = $request->getParam('post_id');

	   	require_once("dbmodels/post_like.crud.php");
	    $postLikeCRUD = new PostLikeCRUD(getConnection());

	   	$response = array(); 
	   	if($button_name === 'like'){
			if($postLikeCRUD->isLikedBy($user_id, $post_id) > 0){

				$response["error"] = false;
		        $response["message"] = "You have already Liked this Post. ";
		        $response["status"] = "liked";
			    echoRespnse(200, $response);
			}
			else{
		        $result = $postLikeCRUD->create($post_id, $user_id);

		        $response = array();	
				if ($result['error'] === false) {
			        $response["error"] = false;
			        $response["message"] = "Like has been created successfully. ";
			        $response["status"] = "liked";

					$response["numLikes"] = $postLikeCRUD->getNumLikes($post_id);

				    echoRespnse(200, $response);
				}
				else{
				    $response["error"] = true;
		            $response["message"] = "Like not create. Please try again.";
			        $response["status"] = "notliked";
				    echoRespnse(200, $response);
				}
			}

		}else{

			$result = $postLikeCRUD->deleteFav($user_id, $post_id);

		        $response = array();	
				if ($result) {
			        $response["error"] = false;
			        $response["message"] = "Like has been deleted successfully. ";
			        $response["status"] = "disliked";

					$response["numLikes"] = $postLikeCRUD->getNumLikes($post_id);

				    echoRespnse(200, $response);
				}
				else{
				    $response["error"] = true;
		            $response["message"] = "Like not delete. Please try again.";
			        $response["status"] = "notdisliked";
				    echoRespnse(200, $response);
				}
		}	
			
	})->setName('like-posts')->add($authenticate); 



	/******** DELETE POST IMAGE *********/
	$app->post('/post_images/delete', function ($request, $respo, $args) use ($app) {	
	    require_once("dbmodels/user.crud.php");
		$userCRUD = new UserCRUD(getConnection());
		require_once("dbmodels/post.crud.php");
	    $postCRUD = new PostCRUD(getConnection());
	    require_once("dbmodels/post_image.crud.php");
	    $postImageCRUD = new PostImageCRUD(getConnection());

		$response = array();
	    $response["error"] = true;
		$id = $request->getParam('item_id');

		$postID = $postImageCRUD->getPostByItemID($id);
		$postDetails = $postCRUD->getID($postID);
		$owner_id = $postDetails["author_id"];

		if (!checkSession()) {
			$response["error"] = true;
	        $response["message"] = "Please login to perform this action.";
			echoRespnse(200, $response);
			exit;
		}
		if ($_SESSION["role_id"] !== 1) {
			if ($owner_id != $_SESSION["userID"]) {
				$response["error"] = true;
		        $response["message"] = "You are not authorized to perform this action. ";
				$response["id"] = $id;
			    echoRespnse(200, $response);
				exit;
			}
		}

		$res = $postImageCRUD->delete($id);		   
		
		if ($res) {
	        $response["error"] = false;
	        $response["message"] = "Post Image has been deleted successfully.";
			$response["id"] = $id;

			if( $postImageCRUD->isImageAvailable($postID) ){
				$response["postImages"] = $postImageCRUD->getImages($postID);
			}
		    echoRespnse(200, $response);
		}
		else{
		    $response["error"] = true;
            $response["message"] = "Failed to delete Post image. Please try again.";
		    echoRespnse(200, $response);
		}
	});



	/**************** GET FILTERED DATA FOR POSTS ***********/
    $app->get('/postsFilter', function($request, $response, $args) {
    	$text_search = $request->getParam('search_box');
    	$post_class = $request->getParam('post_class');
    	$post_subject = $request->getParam('post_subject');
    	$post_extra = $request->getParam('post_extra');

    	require_once("dbmodels/post.crud.php");
	    $postCRUD = new PostCRUD(getConnection());
	    require_once("dbmodels/user.crud.php");
        $userCRUD = new UserCRUD(getConnection());

        $response = array();
	    $response["error"] = true;

		/********** SERVER SESSION CHECK  ***********/
		if(isset($_SESSION["userID"]) && isset($_SESSION["email"]) && isset($_SESSION["api_key"])){
			$thisUser = $userCRUD->getUserByAPIKey($_SESSION["api_key"]);
			if ($thisUser != null) {
			 }
			 else{
				$uri = $request->getUri()->withPath($this->router->pathFor('login'));
				return $response->withRedirect((string)$uri);
			 }
		}
		else{
			  $uri = $request->getUri()->withPath($this->router->pathFor('login'));
			  return $response->withRedirect((string)$uri);
		}
		/********** SERVER SESSION CHECK  ***********/

		$posts = $postCRUD->getFilteredPosts($status = "Active", $text_search, $post_class, $post_subject, $post_extra);	// get Active posts and Filterd posts
		
		$response["post"] = array();
	    if (count($posts) > 0) {
			foreach ($posts as $res) {
		        $postDetails = getPostDetails($res["id"], $thisUser["id"]);
				array_push($response["post"], $postDetails);
				$response["error"] = false;
			}
	    }
	     // print_r($data);die();
    	echoRespnse(200, $response);

    });	



?>