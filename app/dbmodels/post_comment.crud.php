<?php
require_once("Constants.php");
class PostCommentCRUD
{
 private $db;
 
 function __construct($DB_con)
 {
  $this->db = $DB_con;
 }
 
 public function create($comment, $post_id, $user_id)
 {
  $response = array();	
  $response["error"] = true;
  $visible = 1;
  $date_created = date('Y-m-d H:i:s');
  
  try
  {
     $stmt = $this->db->prepare("INSERT INTO post_comments(comment, post_id, user_id, visible, date_created) VALUES(:comment, :post_id, :user_id, :visible, :date_created)");
     $stmt->bindparam(":comment",$comment);
     $stmt->bindparam(":post_id",$post_id);
     $stmt->bindparam(":user_id",$user_id);
     $stmt->bindparam(":visible",$visible);
     $stmt->bindparam(":date_created",$date_created);

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
  $stmt = $this->db->prepare("SELECT * FROM post_comments WHERE id=:id");
  $stmt->execute(array(":id"=>$id));
  $editRow=$stmt->fetch(PDO::FETCH_ASSOC);
  return $editRow;
 }
 
 public function getCommentsFor($post_id)
 {
  $stmt = $this->db->prepare("SELECT * FROM post_comments WHERE post_id=:post_id ORDER BY id DESC LIMIT 6");
  $stmt->execute(array(":post_id"=>$post_id));
  $editRow=$stmt->fetchAll();
  return $editRow;
 }
 
  public function getAllCommentsForUser($user_id)
 {
  $stmt = $this->db->prepare("SELECT * FROM post_comments WHERE post_id IN (SELECT id FROM posts WHERE author_id =:user_id) ORDER BY id DESC");
  $stmt->execute(array(":user_id"=>$user_id));
  $editRow=$stmt->fetchAll();
  return $editRow;
 } 

  public function getNumCommentsFor($post_id)
 { 
  $sql = "SELECT count(*) FROM post_comments WHERE post_id=:post_id";
  $stmt = $this->db->prepare($sql); 
  $stmt->execute(array(":post_id"=>$post_id)); 
  $number_of_rows = $stmt->fetchColumn(); 
  return $number_of_rows;
 }
 
 public function delete($id)
 {
  $stmt = $this->db->prepare("DELETE FROM post_comments WHERE id=:id");
  $stmt->bindparam(":id",$id);
  $stmt->execute();
  return true;
 }

  public function deleteAll($post_id)
 {
  $stmt = $this->db->prepare("DELETE FROM post_comments WHERE post_id=:post_id");
  $stmt->bindparam(":post_id",$post_id);
  $stmt->execute();
  return true;
 } 

  public function isCommentAvailable($post_id)
 { 
  $sql = "SELECT count(*) FROM post_comments WHERE post_id=:post_id";
  $stmt = $this->db->prepare($sql); 
  $stmt->execute(array(":post_id"=>$post_id)); 
  $number_of_rows = $stmt->fetchColumn(); 
  return $number_of_rows > 0;
 }


}