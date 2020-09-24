<?php
require_once("Constants.php");
class PostLikeCRUD
{
 private $db;
 
 function __construct($DB_con)
 {
  $this->db = $DB_con;
 }
 
 public function create($post_id, $user_id)
 {
  $response = array();	
  $response["error"] = true;  
  $date_created = date('Y-m-d H:i:s');

  try
  {
   $stmt = $this->db->prepare("INSERT INTO post_likes(post_id, user_id, date_created) VALUES(:post_id, :user_id, :date_created)");
   $stmt->bindparam(":post_id", $post_id);
   $stmt->bindparam(":user_id", $user_id);
   $stmt->bindparam(":date_created", $date_created);
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
   echo $e->getMessage();  
   return $response;
  }
  
 }
 
 public function getID($id)
 {
  $stmt = $this->db->prepare("SELECT * FROM post_likes WHERE id=:id");
  $stmt->execute(array(":id"=>$id));
  $editRow=$stmt->fetch(PDO::FETCH_ASSOC);
  return $editRow;
 }
 
 public function getNumLikes($post_id)
 { 
  $sql = "SELECT count(*) FROM post_likes WHERE post_id=:post_id";
  $stmt = $this->db->prepare($sql); 
  $stmt->execute(array(":post_id"=>$post_id)); 
  $number_of_rows = $stmt->fetchColumn(); 
  return $number_of_rows;
 }
 
  public function getNumLikesForUser($user_id)
 { 
  $sql = "SELECT count(*) FROM post_likes WHERE post_id IN(SELECT id FROM posts WHERE user_id=:user_id)";
  $stmt = $this->db->prepare($sql); 
  $stmt->execute(array(":user_id"=>$user_id)); 
  $number_of_rows = $stmt->fetchColumn(); 
  return $number_of_rows;
 }
 
 public function isLikedBy($user_id, $post_id)
 { 
  $sql = "SELECT count(*) FROM post_likes WHERE post_id=:post_id AND user_id =:user_id";
  $stmt = $this->db->prepare($sql); 
  $stmt->execute(array(":post_id"=>$post_id, ":user_id"=>$user_id)); 
  $number_of_rows = $stmt->fetchColumn(); 
  return $number_of_rows > 0;
 }
 
 public function getpostlikeFor($user_id, $post_id)
 {
	  $stmt = $this->db->prepare("SELECT * FROM post_likes WHERE post_id=:post_id AND user_id =:user_id ORDER BY id DESC");
	  $stmt->execute(array(":post_id"=>$post_id, ":user_id"=>$user_id));
	  $editRow=$stmt->fetchAll();
	  return $editRow;
 }	

 public function getActionRecordID($user_id, $post_id)
 { 
  $sql = "SELECT id FROM post_likes WHERE post_id=:post_id AND user_id =:user_id";
  $stmt = $this->db->prepare($sql); 
  $stmt->execute(array(":post_id"=>$post_id, ":user_id"=>$user_id)); 
  $number_of_rows = $stmt->fetchColumn(); 
  return $number_of_rows;
 }
 
 public function delete($id)
 {
  $stmt = $this->db->prepare("DELETE FROM post_likes WHERE id=:id");
  $stmt->bindparam(":id",$id);
  $stmt->execute();
  return true;
 }
 
  public function deleteFav($user_id, $post_id)
 {
  $stmt = $this->db->prepare("DELETE FROM post_likes WHERE user_id=:user_id AND post_id=:post_id");
  $stmt->bindparam(":user_id",$user_id);
  $stmt->bindparam(":post_id",$post_id);
  $stmt->execute();
  return true;
 }

  public function deleteAll($post_id)
 {
  $stmt = $this->db->prepare("DELETE FROM post_likes WHERE post_id=:post_id");
  $stmt->bindparam(":post_id",$post_id);
  $stmt->execute();
  return true;
 } 

  public function isLikeAvailable($post_id)
 { 
  $sql = "SELECT count(*) FROM post_likes WHERE post_id=:post_id";
  $stmt = $this->db->prepare($sql); 
  $stmt->execute(array(":post_id"=>$post_id)); 
  $number_of_rows = $stmt->fetchColumn(); 
  return $number_of_rows > 0;
 }


 
}