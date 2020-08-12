<?php
	// Psr-7 Request and Response interfaces
	use Psr\Http\Message\ServerRequestInterface as Request;
	use Psr\Http\Message\ResponseInterface as Response;
    
	/********** START OF E-CONTENT ROUTE *****************/
	$app->get('/create-post', function (Request $request, Response $response, $args){
        require_once("dbmodels/user.crud.php");
        $userCRUD = new UserCRUD(getConnection());
		require_once("dbmodels/class.crud.php");
		$classCRUD = new ClassCRUD(getConnection());
        $classes = $classCRUD->getAllClasses();
		$vars = [
			'page' => [
            'name' => 'posts',
            'classes' => $classes,
		    'title' => 'Create E-Content | Talank SMS',
			'description' => 'Talank SAS is a next generation school management and automation system. '
			]
		];
		return $this->view->render($response, 'post-create.twig', $vars);
	})->setName('create-post');

	$app->get('/posts', function (Request $request, Response $response, $args){
		$vars = [
			'page' => [
			'name' => 'posts',
		    'title' => 'Create E-Content | Talank SMS',
			'description' => 'Talank SAS is a next generation school management and automation system. '
			]
		];
		return $this->view->render($response, 'post.twig', $vars);
	})->setName('posts');

	$app->get('/post-detail', function (Request $request, Response $response, $args){
		$vars = [
			'page' => [
			'name' => 'posts',
		    'title' => 'Post Detail | Talank SMS',
			'description' => 'Talank SAS is a next generation school management and automation system. '
			]
		];
		return $this->view->render($response, 'post-detail.twig', $vars);
	})->setName('post-detail');


	$app->get('/manage-posts', function ($request, $response, $args){
		require_once("dbmodels/user.crud.php");
		require_once("dbmodels/utils.crud.php");
		 $userCRUD = new UserCRUD(getConnection());
		$utilCRUD = new UtilCRUD(getConnection());
		//ADMIN ONLY	
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
				'description' => 'List of all posts'
				]
			];	
			
			return $this->view->render($response, 'posts-list.twig', $vars);
		})->setName('manage-posts');

	/********** END OF E-CONTENT ROUTE *****************/
    
    
    ?>