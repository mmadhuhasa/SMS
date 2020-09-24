<?php
require_once("Constants.php");

class PostTypeCRUD
{
	 private $db;
	 
	 function __construct($DB_con)
	 {
	  $this->db = $DB_con;
	 }
	 
	 public function create($name, $upload_content_banner, $description, $images_upload, $video_link, $post_id, $enabled)
	 {
		  $response = array();	
		  $response["error"] = true;
		  try
		  {
			   $stmt = $this->db->prepare("INSERT INTO post_types(name, upload_content_banner, description, images_upload, video_link, post_id, enabled) VALUES(:post_type, :upload_content_banner, :description, :images_upload, :video_link, :post_id, :enabled)");
			   $stmt->bindparam(":post_type",$name);
			   $stmt->bindparam(":upload_content_banner",$upload_content_banner);
			   $stmt->bindparam(":images_upload",$images_upload);
			   $stmt->bindparam(":description",$description);
			   $stmt->bindparam(":video_link",$video_link);
			   $stmt->bindparam(":post_id",$post_id);
			   $stmt->bindparam(":enabled",$enabled);

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
		  $stmt = $this->db->prepare("SELECT * FROM post_types WHERE id=:id");
		  $stmt->execute(array(":id"=>$id));
		  $editRow=$stmt->fetch(PDO::FETCH_ASSOC);
		  return $editRow;
	 }
	 
	 public function getposttypesFor($post_id)
	 {
		  $stmt = $this->db->prepare("SELECT * FROM post_types WHERE post_id=:post_id ORDER BY id DESC");
		  $stmt->execute(array(":post_id"=>$post_id));
		  $editRow=$stmt->fetchAll();
		  return $editRow;
	 }


	   public function update_postbanner($id, $image_name)
	   {   
	      $image = "images/posts/".$image_name;
	      $response = array();  
	      $response["error"] = true; 
	      $stmt2=$this->db->prepare("UPDATE post_types SET upload_content_banner=:banner WHERE post_id=:id");
	      $stmt2->bindparam(":banner",$image);
	      $stmt2->bindparam(":id",$id);
	      $res = $stmt2->execute();
	      if($res){
	         $response["error"] = false; 
	         $response["message"] = "Image uploaded."; 
	      }else{
	         $response["error"] = true; 
	         $response["message"] = "Upload a valid image."; 
	      }
	      return $response; 
	   }

	   public function update_postimage($id, $image_name)
	   {   
	      $image = "images/posts/".$image_name;
	      $response = array();  
	      $response["error"] = true; 
	      $stmt2=$this->db->prepare("UPDATE post_types SET images_upload=:images WHERE post_id=:id"); 
	      $stmt2->bindparam(":images",$image);
	      $stmt2->bindparam(":id",$id);
	      $res = $stmt2->execute();
	      if($res){
	         $response["error"] = false; 
	         $response["message"] = "Image uploaded."; 
	      }else{
	         $response["error"] = true; 
	         $response["message"] = "Upload a valid image."; 
	      }
	      return $response; 
	   }


	 public function update($name, $upload_content_banner, $description, $images_upload, $video_link, $post_id, $enabled)
	 {
		  $response = array();	
		  $response["error"] = true;
		  try{

			   $stmt = $this->db->prepare("UPDATE post_types SET name=:post_type, upload_content_banner=:upload_content_banner, description=:description, images_upload=:images_upload, video_link=:video_link, post_id=:post_id, enabled=:enabled WHERE post_id=:id");

			   $stmt->bindparam(":id",$post_id);
			   $stmt->bindparam(":post_type",$name);
			   $stmt->bindparam(":upload_content_banner",$upload_content_banner);
			   $stmt->bindparam(":images_upload",$images_upload);
			   $stmt->bindparam(":description",$description);
			   $stmt->bindparam(":video_link",$video_link);
			   $stmt->bindparam(":post_id",$post_id);
			   $stmt->bindparam(":enabled",$enabled);

			   if($stmt->execute()){
				   $response["error"] = false;  
				   // $response["id"] = $this->db->lastInsertId();  
			       // $response["code"] = INSERT_SUCCESS; 
           		   $response["message"] = "Posttype has been updated successfully.";

			   }else{
				   $response["error"] = true;  
			       // $response["code"] = INSERT_FAILURE; 
             	   $response["message"] = "Failed to update posttype. Please try again.";

			   }
		   	   return $response;
	  	  }
		  catch(PDOException $e)
		  {
		   		//echo $e->getMessage();  
		   		$response["error"] = true;  
		        // $response["code"] = INSERT_FAILURE; 
        		$response["message"] = $e->getMessage();

		   		return $response;
		  }
	  
	 }	   




} 