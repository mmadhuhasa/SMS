<?php
require_once("Constants.php");

class PostPreferenceCRUD
{
	 private $db;
	 
	 function __construct($DB_con)
	 {
	  $this->db = $DB_con;
	 }
	 
	 public function create($post_id, $is_private, $is_restricted, $display_author, $display_organization, $allow_likes, $allow_comments)
	 {
		  $response = array();	
		  $response["error"] = true;
		  try
		  {
			   $stmt = $this->db->prepare("INSERT INTO post_preferences(post_id, is_private, is_restricted, display_author, display_organization, allow_likes, allow_comments) VALUES(:post_id, :is_private, :is_restricted, :display_author, :display_organization, :allow_likes, :allow_comments)");
			   $stmt->bindparam(":post_id",$post_id);
			   $stmt->bindparam(":is_private",$is_private);
			   $stmt->bindparam(":is_restricted",$is_restricted);
			   $stmt->bindparam(":display_author",$display_author);
			   $stmt->bindparam(":display_organization",$display_organization);
			   $stmt->bindparam(":allow_likes",$allow_likes);
			   $stmt->bindparam(":allow_comments",$allow_comments);


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
		  $stmt = $this->db->prepare("SELECT * FROM post_preferences WHERE id=:id");
		  $stmt->execute(array(":id"=>$id));
		  $editRow=$stmt->fetch(PDO::FETCH_ASSOC);
		  return $editRow;
	 }
	 
	  public function isPrefsAvailable($post_id)
 { 
  $sql = "SELECT count(*) FROM post_preferences WHERE post_id=:post_id";
  $stmt = $this->db->prepare($sql); 
  $stmt->execute(array(":post_id"=>$post_id)); 
  $number_of_rows = $stmt->fetchColumn(); 
  return $number_of_rows > 0;
 }
	 
	 public function getPrefsFor($post_id)
	 {
		  $stmt = $this->db->prepare("SELECT * FROM post_preferences WHERE post_id=:post_id ORDER BY id DESC");
		  $stmt->execute(array(":post_id"=>$post_id));
		  $editRow=$stmt->fetchAll();
		  return $editRow;
	 }


	public function update($post_id, $is_private, $is_restricted, $display_author, $display_organization, $allow_likes, $allow_comments)
	 {
		  $response = array();	
		  $response["error"] = true;
		  try
		  {

			   $stmt = $this->db->prepare("UPDATE post_preferences SET post_id=:post_id, is_private=:is_private, is_restricted=:is_restricted, display_author=:display_author, display_organization=:display_organization, allow_likes=:allow_likes, allow_comments=:allow_comments WHERE post_id=:id");

			   $stmt->bindparam(":id",$post_id);
			   $stmt->bindparam(":post_id",$post_id);
			   $stmt->bindparam(":is_private",$is_private);
			   $stmt->bindparam(":is_restricted",$is_restricted);
			   $stmt->bindparam(":display_author",$display_author);
			   $stmt->bindparam(":display_organization",$display_organization);
			   $stmt->bindparam(":allow_likes",$allow_likes);
			   $stmt->bindparam(":allow_comments",$allow_comments);
			   if($stmt->execute()){
				   $response["error"] = false;
           		   $response["message"] = "Posttype has been updated successfully.";
			   }else{
				   $response["error"] = true;
             	   $response["message"] = "Failed to update posttype. Please try again.";
			   }
		   	   return $response;
	  	  }
		  catch(PDOException $e)
		  {  
		   		$response["error"] = true;  
        		$response["message"] = $e->getMessage();
		   		return $response;
		  }
	  
	 }

	  public function deleteAll($post_id)
	 {
	  $stmt = $this->db->prepare("DELETE FROM post_preferences WHERE post_id=:post_id");
	  $stmt->bindparam(":post_id",$post_id);
	  $stmt->execute();
	  return true;
	 }
} 