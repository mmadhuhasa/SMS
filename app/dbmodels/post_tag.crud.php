<?php
require_once("Constants.php");
class PostTagCRUD
{
 private $db;
 
 function __construct($DB_con)
 {
  $this->db = $DB_con;
 }
 
 public function create($post_id, $tag)
 {
  $response = array();	
  $response["error"] = true;   
  try
  {
   $stmt = $this->db->prepare("INSERT INTO post_tags(post_id, tag) VALUES(:post_id, :tag)");
   $stmt->bindparam(":post_id", $post_id);
   $stmt->bindparam(":tag", $tag);
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
  $response["error"] = true;  
  $response["code"] = INSERT_FAILURE; 	  
   //echo $e->getMessage();  
   return $response;
  }
  
 }
 
 public function getID($id)
 {
  $stmt = $this->db->prepare("SELECT * FROM post_tags WHERE id=:id");
  $stmt->execute(array(":id"=>$id));
  $editRow=$stmt->fetch(PDO::FETCH_ASSOC);
  return $editRow;
 }
 
 public function getNumTags($post_id)
 { 
  $sql = "SELECT count(*) FROM post_tags WHERE post_id=:post_id";
  $stmt = $this->db->prepare($sql); 
  $stmt->execute(array(":post_id"=>$post_id)); 
  $number_of_rows = $stmt->fetchColumn(); 
  return $number_of_rows;
 }
 
  public function getNumPostsForTag($tag)
 { 
  $sql = "SELECT count(*) FROM posts WHERE id IN(SELECT post_id FROM post_tags WHERE tag=:tag)";
  $stmt = $this->db->prepare($sql); 
  $stmt->execute(array(":tag"=>$tag)); 
  $number_of_rows = $stmt->fetchColumn(); 
  return $number_of_rows;
 }
 
 public function isTagged($tag, $post_id)
 { 
  $sql = "SELECT count(*) FROM post_tags WHERE post_id=:post_id AND tag =:tag";
  $stmt = $this->db->prepare($sql); 
  $stmt->execute(array(":post_id"=>$post_id, ":tag"=>$tag)); 
  $number_of_rows = $stmt->fetchColumn(); 
  return $number_of_rows > 0;
 }
 
 public function getTagsForPost($post_id)
 {
	  $stmt = $this->db->prepare("SELECT * FROM post_tags WHERE post_id=:post_id ORDER BY id DESC");
	  $stmt->execute(array(":post_id"=>$post_id));
	  $editRow=$stmt->fetchAll();
	  return $editRow;
 }	

 public function getOnlyTagsForPost($post_id)
 {
    $stmt = $this->db->prepare("SELECT tag FROM post_tags WHERE post_id=:post_id ORDER BY id DESC");
    $stmt->execute(array(":post_id"=>$post_id));
    $editRow=$stmt->fetchAll();
    return $editRow;
 }  

 public function getActionRecordID($tag, $post_id)
 { 
  $sql = "SELECT id FROM post_tags WHERE post_id=:post_id AND tag =:tag";
  $stmt = $this->db->prepare($sql); 
  $stmt->execute(array(":post_id"=>$post_id, ":tag"=>$tag)); 
  $number_of_rows = $stmt->fetchColumn(); 
  return $number_of_rows;
 }
 
 public function delete($id)
 {
  $stmt = $this->db->prepare("DELETE FROM post_tags WHERE id=:id");
  $stmt->bindparam(":id",$id);
  $stmt->execute();
  return true;
 }
 
  public function deleteTag($tag, $post_id)
 {
  $stmt = $this->db->prepare("DELETE FROM post_tags WHERE tag=:tag AND post_id=:post_id");
  $stmt->bindparam(":tag",$tag);
  $stmt->bindparam(":post_id",$post_id);
  $stmt->execute();
  return true;
 }

  public function deleteAll($post_id)
 {
  $stmt = $this->db->prepare("DELETE FROM post_tags WHERE post_id=:post_id");
  $stmt->bindparam(":post_id",$post_id);
  $stmt->execute();
  return true;
 }

  public function isTagAvailable($post_id)
 { 
  $sql = "SELECT count(*) FROM post_tags WHERE post_id=:post_id";
  $stmt = $this->db->prepare($sql); 
  $stmt->execute(array(":post_id"=>$post_id)); 
  $number_of_rows = $stmt->fetchColumn(); 
  return $number_of_rows > 0;
 }



 
}