<?php
require_once("Constants.php");

class PostViewCRUD
{
	 private $db;
	 
	 function __construct($DB_con)
	 {
	  $this->db = $DB_con;
	 }
	 
	 //Modify these later
	 public function create($post_id, $user_id)
	 {
		  $response = array();	
		  $response["error"] = true;
		  $platform = 'Web';
		  try
		  {
			   $stmt = $this->db->prepare("INSERT INTO post_views(post_id, user_id, platform) VALUES(:post_id, :user_id, :platform)");
			   // $stmt->bindparam(":views",$first_view);
			   $stmt->bindparam(":post_id",$post_id); 
			   $stmt->bindparam(":user_id",$user_id);
			   $stmt->bindparam(":platform",$platform);

			   if($stmt->execute()){
				   $response["error"] = false;  
				   $response["id"] = $this->db->lastInsertId();  
			       $response["code"] = INSERT_SUCCESS; 
			   }else{
				   $response["error"] = true;  
			       $response["code"] = INSERT_FAILURE; 
			   }
		   	   return $response;
	  	  }
		  catch(PDOException $e)
		  {
		   		//echo $e->getMessage();  
		   		$response["error"] = true;  
		        $response["code"] = INSERT_FAILURE; 
		   		return $response;
		  }
	  
	 }
	 
	 public function getID($id)
	 {
		  $stmt = $this->db->prepare("SELECT * FROM post_views WHERE id=:id");
		  $stmt->execute(array(":id"=>$id));
		  $editRow=$stmt->fetch(PDO::FETCH_ASSOC);
		  return $editRow;
	 }


	 
	 public function getPostViewsForUser($user_id, $post_id)
	 {
		  $stmt = $this->db->prepare("SELECT * FROM post_views WHERE post_id=:post_id and user_id=:user_id ORDER BY id DESC");
		  $stmt->execute(array(":post_id"=>$post_id, ":user_id"=>$user_id));
		  $editRow=$stmt->fetchAll();
		  return $editRow;
	 }

    //Post Views generally do not depend upon a user
	 public function getPostViewCount($post_id)
	 {
		  $stmt = $this->db->prepare("SELECT COUNT(*) FROM post_views WHERE post_id=:post_id ORDER BY id DESC");
		  $stmt->execute(array(":post_id"=>$post_id));
		  $stmt->execute();
		  $editRow=$stmt->fetchColumn(); 
		  return $editRow;
	 }

	 	 public function getPostViewCountUnique($post_id)
	 {
		  $stmt = $this->db->prepare("SELECT count(*) FROM post_views WHERE post_id=:post_id ORDER BY id DESC");
		  $stmt->execute(array(":post_id"=>$post_id));

		  $stmt->execute();
		  $editRow=$stmt->fetchColumn(); 
		  return $editRow;
	 }

	  public function deleteAll($post_id)
	 {
	  $stmt = $this->db->prepare("DELETE FROM post_views WHERE post_id=:post_id");
	  $stmt->bindparam(":post_id",$post_id);
	  $stmt->execute();
	  return true;
	 } 

	  public function isViewAvailable($post_id)
	 { 
	  $sql = "SELECT count(*) FROM post_views WHERE post_id=:post_id";
	  $stmt = $this->db->prepare($sql); 
	  $stmt->execute(array(":post_id"=>$post_id)); 
	  $number_of_rows = $stmt->fetchColumn(); 
	  return $number_of_rows > 0;
	 }


	 // public function update($post_id, $user_id)
	 // {
		//   $response = array();	
		//   $response["error"] = true;
		//   try{
		//   		// print_r($post_id."".$user_id);die();
		// 	   $stmt = $this->db->prepare("UPDATE post_views SET views=views + 1 WHERE post_id=:post_id AND user_id=:user_id ");
		// 	   $stmt->bindparam(":user_id",$user_id);
		// 	   $stmt->bindparam(":post_id",$post_id);
		//   		// print_r($stmt);die(); 

		// 	   if($stmt->execute()){
		// 		   $response["error"] = false;  
		// 		   // $response["id"] = $this->db->lastInsertId();  
		// 	       // $response["code"] = INSERT_SUCCESS; 
  //          		   $response["message"] = "Postview has been updated successfully.";

		// 	   }else{
		// 		   $response["error"] = true;  
		// 	       // $response["code"] = INSERT_FAILURE; 
  //            	   $response["message"] = "Failed to update postview. Please try again.";

		// 	   }
		//    	   return $response;
	 //  	  }
		//   catch(PDOException $e)
		//   {
		//    		//echo $e->getMessage();  
		//    		$response["error"] = true;  
		//         // $response["code"] = INSERT_FAILURE; 
  //       		$response["message"] = $e->getMessage();

		//    		return $response;
		//   }
	  
	 // }	   

} 