<?php
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
	    require_once("dbmodels/post.crud.php");
		$postCRUD = new PostCRUD(getConnection());
		$postTypes = $postCRUD->getAllPostTypes();
		
		/********** SERVER SESSION CHECK  ***********/
		if(isset($_SESSION["userID"]) && isset($_SESSION["email"]) && isset($_SESSION["api_key"])){
			$thisUser = $userCRUD->getUserByAPIKey($_SESSION["api_key"]);
			if ($thisUser != null && ($thisUser["role_id"] == 1 || $thisUser["role_id"] == 2 || $thisUser["role_id"] == 4)) {
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
			'postTypes' => $postTypes,
		    'title' => 'Create Post | Talank SMS',
			'description' => 'Talank SAS is a next generation school management and automation system. '
			]
		];
		return $this->view->render($response, 'post-create.twig.php', $vars);
	})->setName('create-post');

	
	$app->get('/posts', function (Request $request, Response $response, $args){
		require_once("dbmodels/user.crud.php");
        $userCRUD = new UserCRUD(getConnection());
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

		require_once("dbmodels/post.crud.php");
        $postCRUD = new PostCRUD(getConnection());
		$posts = $postCRUD->getAllPosts("Active");	// get only Active posts
		//print_r($posts);die();
		$data = array();
	    if (count($posts) > 0) {
			foreach ($posts as $res) {
		        $postDetails = getPostDetails($res["id"], $thisUser["id"]);
				array_push($data, $postDetails);
			}
	    }

		$countAllPosts = $postCRUD->getAllPostsCount('Active');

	    require_once("dbmodels/class.crud.php");
		$classCRUD = new ClassCRUD(getConnection());
        $classes = $classCRUD->getAllClasses();
        require_once("dbmodels/subject.crud.php");
     	$subjectCRUD = new SubjectCRUD(getConnection());
		$subjects = $subjectCRUD->getAllSubjects();

	    // print_r($data);die();
		$vars = [
			'page' => [
			'name' => 'posts',
		    'title' => 'Posts | Talank SMS',
			'description' => 'Talank SAS is a next generation school management and automation system. ',
			'posts' => $data,
			'classes' => $classes,
            'subjects' => $subjects,
            'countAllPosts' => $countAllPosts
			]
		];
		return $this->view->render($response, 'posts.twig.php', $vars);
	})->setName('posts');
	

	/************* POST DETAIL PAGE ************/
	$app->get('/post-detail/{post_qcode}', function (Request $request, Response $response, $args){

		require_once("dbmodels/user.crud.php");
        $userCRUD = new UserCRUD(getConnection());
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

		$qcode = $request->getAttribute('post_qcode');
	    require_once("dbmodels/post.crud.php");
	    $postCRUD = new PostCRUD(getConnection());
		if (empty($qcode)) {
	        $uri = $request->getUri()->withPath($this->router->pathFor('404'));
			return $response->withRedirect((string)$uri);
		}	
	    if(!$postCRUD->isQCodeExists($qcode)){
		 	$uri = $request->getUri()->withPath($this->router->pathFor('404'));
			return $response->withRedirect((string)$uri);
		}
		$id = $postCRUD->getIDByQCode($qcode);
		$postDetails = getPostDetails($id, $thisUser["id"], false, true, true);
		// print_r($postDetails);die();
		$postTitle = $postDetails["title"];
		$adminMode = true;
		$showStats = true;

		
		// update views count for this post
		if (!empty($id)) {
			$updateview = updateView($id);
		}
			 
		$vars = [
				'page' => [
				'name' => 'post',
			    'title' => $postTitle.' | Smart School Automation',
				'description' => 'Talank SAS is a next generation school management and automation system. ',
				'post' => $postDetails,
				'showStats' => $showStats,
				'adminMode' => $adminMode
				]
			];
			return $this->view->render($response, 'post-detail.twig.php', $vars);
	})->setName('post-detail');


	/************* POSTS ADMINISTRATION PAGE ***********/
	$app->get('/manage-posts', function ($request, $response, $args){
		require_once("dbmodels/user.crud.php");
		require_once("dbmodels/utils.crud.php");
		$userCRUD = new UserCRUD(getConnection());
		$utilCRUD = new UtilCRUD(getConnection());
		require_once("dbmodels/post.crud.php");
        $postCRUD = new PostCRUD(getConnection());
		$data = array();
		$adminMode = false;
		
		/********** SERVER SESSION CHECK  ***********/
		if(isset($_SESSION["userID"]) && isset($_SESSION["email"]) && isset($_SESSION["api_key"])){
		$thisUser = $userCRUD->getUserByAPIKey($_SESSION["api_key"]);
		//Allow super-admin, schools and teachers to access
		if ($thisUser != null && ($thisUser["role_id"] == 1 || $thisUser["role_id"] == 2 || $thisUser["role_id"] == 4)) {
			if($thisUser["role_id"] == 1){
				$adminMode = true;
			}
		 }else{
			$uri = $request->getUri()->withPath($this->router->pathFor('unauthorized'));
			return $response->withRedirect((string)$uri);
		 }
		}else{
			$uri = $request->getUri()->withPath($this->router->pathFor('login'));
			return $response->withRedirect((string)$uri);
		}
		/********** SERVER SESSION CHECK  ***********/
		

		/******** GET POSTS BASED ON USER LOGIN ********/
		$posts = $postCRUD->getAllPostsbyStatus($thisUser["id"]);
		// $posts = $postCRUD->getPostsForUser($thisUser["id"]);
		switch($thisUser["role_id"]){
			case 1:
			//Super admin can manage all posts
			$posts = $postCRUD->getAllPosts();
			break;
			
			case 2:
			//School admin can manage all posts within the school
			$posts = $postCRUD->getPostsForSchool($thisUser["school_id"]);
			break;
		}
		
	    if (count($posts) > 0) {
			foreach ($posts as $res) {
		        $postProfile = getPostDetails($res["id"], $thisUser["id"], false, true, false);
				array_push($data, $postProfile);
			}
	    }
	    // print_r($data);die();

		$vars = [
				'page' => [
				'name' => 'manage-posts',
				'title' => 'Manage Posts',
				'description' => 'List of all posts',
				'data' => $data,
				'adminMode' => $adminMode
				]
		];
		return $this->view->render($response, 'posts-list.twig', $vars);
		})->setName('manage-posts');
		

	/********** END OF E-CONTENT ROUTE *****************/

    /**************** EDIT POST VIEW ****************/
	$app->get('/edit-post/{post_qcode}', function (Request $request, Response $response, $args){
		$qcode = $request->getAttribute('post_qcode');
		require_once("dbmodels/user.crud.php");
	    $userCRUD = new UserCRUD(getConnection());
	   	require_once("dbmodels/class.crud.php");
	    $classCRUD = new ClassCRUD(getConnection());
        $classes = $classCRUD->getAllClasses();
		require_once("dbmodels/subject.crud.php");
     	$subjectCRUD = new SubjectCRUD(getConnection());
        require_once("dbmodels/topic.crud.php");
		$topicCRUD = new TopicCRUD(getConnection());
	    require_once("dbmodels/post.crud.php");
		$postCRUD = new PostCRUD(getConnection());
		$postTypes = $postCRUD->getAllPostTypes();
		
		/********** SERVER SESSION CHECK  ***********/
		if(isset($_SESSION["userID"]) && isset($_SESSION["email"]) && isset($_SESSION["api_key"])){
			$thisUser = $userCRUD->getUserByAPIKey($_SESSION["api_key"]);
			if ($thisUser != null) {
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

	if (empty($qcode)) {
        $uri = $request->getUri()->withPath($this->router->pathFor('404'));
		return $response->withRedirect((string)$uri);
	}
	
    if(!$postCRUD->isQCodeExists($qcode)){
	$uri = $request->getUri()->withPath($this->router->pathFor('404'));
	return $response->withRedirect((string)$uri);
	}
	
	$id = $postCRUD->getIDByQCode($qcode);
    $postDetails = getPostDetails($id, $thisUser["id"]);
	$class_id = $postDetails['class_id'];
	$subject_id = $postDetails['subject_id'];
	$topic_id = $postDetails['topic_id'];
	$posttype_id = $postDetails['post_type'];
	$subjects = $subjectCRUD->getAllSubjectsForclass($class_id);
	$topics = $topicCRUD->getAllTopicsForclass($subject_id);
	//Check if editing is allowed for this user
	if($thisUser["role_id"] == 1 || ($postDetails['author_id'] == $thisUser["id"])){
		
	}else{
		$uri = $request->getUri()->withPath($this->router->pathFor('unauthorized'));
		return $response->withRedirect((string)$uri);
	}

	$vars = [
			'page' => [
			'name' => 'edit-post',
		    'title' => 'Edit Post | Talank SMS',
			'description' => 'Talank SAS is a next generation school management and automation system. ',
			'post' => $postDetails,
			'classes' => $classes,
			'subjects' => $subjects,
            'topics' => $topics,
			'postTypes' => $postTypes,
			]
		];
		return $this->view->render($response, 'edit-post.twig.php', $vars);
	})->setName('edit-post');

?>