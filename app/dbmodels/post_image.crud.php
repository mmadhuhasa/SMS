<?php
require_once("Constants.php");
class PostImageCRUD
{
 private $db;
 function __construct($DB_con)
 {
  $this->db = $DB_con;
 }
 
  public function getPostByItemID($id)
 {
  $stmt = $this->db->prepare("SELECT post_id FROM post_images WHERE id = :id");
  $stmt->execute(array(":id"=>$id));
  $editRow= $stmt->fetchColumn();
  return $editRow;
 }
 
 public function addPostImage($post_id, $image_url)
 {
  $response = array();
  $response["error"] = true;
  $response["message"] = "";
  $image_url = 'images/posts/'.$image_url;

   /************* UPLOAD IMAGE **************/
   try{
       if(!empty($image_url)){
         $stmt = $this->db->prepare("INSERT INTO post_images(post_id, image) VALUES(:post_id, :image_url)");
         $stmt->bindparam(":post_id",$post_id);
         $stmt->bindparam(":image_url",$image_url);

         if($stmt->execute()){
      	   $response["error"] = false;
      	   $response["id"] = $this->db->lastInsertId();
           $response["code"] = INSERT_SUCCESS;
           $response["message"] = "Post Image Inserted";

         }else{
      	   $response["message"] = "Failed to save post Image. Please try again.";
      	   $response["error"] = true;
           $response["code"] = INSERT_FAILURE;
         }
       }
   }catch (Exception $e) {
	   $response["code"] = INSERT_FAILURE;
	   $response["error"] = false;
	   $response["message"] = "Error while uploading Image.";
   }
	   /**************************************/
   return $response;
 }

 public function getID($id)
 {
  $stmt = $this->db->prepare("SELECT * FROM post_images WHERE id=:id");
  $stmt->execute(array(":id"=>$id));
  $editRow=$stmt->fetch(PDO::FETCH_ASSOC);
  return $editRow;
 }
 
  public function delete($id)
 {
  $stmt = $this->db->prepare("DELETE FROM post_images WHERE id=:id");
  $stmt->bindparam(":id",$id);
  $stmt->execute();
  return true;
 }
 
  public function deleteAll($post_id)
 {
  $stmt = $this->db->prepare("DELETE FROM post_images WHERE post_id=:post_id");
  $stmt->bindparam(":post_id",$post_id);
  $stmt->execute();
  return true;
 }

  public function getNumImages($post_id)
 {
  $sql = "SELECT count(*) FROM post_images WHERE post_id=:post_id";
  $stmt = $this->db->prepare($sql);
  $stmt->execute(array(":post_id"=>$post_id));
  $number_of_rows = $stmt->fetchColumn();
  return $number_of_rows;
 }

  public function isImageAvailable($post_id)
 {
  $sql = "SELECT count(*) FROM post_images WHERE post_id=:post_id";
  $stmt = $this->db->prepare($sql);
  $stmt->execute(array(":post_id"=>$post_id));
  $number_of_rows = $stmt->fetchColumn();
  return $number_of_rows > 0;
 }

 public function getImages($post_id)
 {
  $stmt = $this->db->prepare("SELECT * FROM post_images WHERE post_id =:post_id ORDER BY id DESC");
  $stmt->execute(array(":post_id"=>$post_id));
  $editRow=$stmt->fetchAll();
  return $editRow;
 }

 // public function getLatestImages()
 // {
 //  $stmt = $this->db->prepare("SELECT post_id, image FROM post_images GROUP BY post_id ORDER BY id DESC LIMIT 9");
 //  $stmt->execute();
 //  $editRow=$stmt->fetchAll();
 //  return $editRow;
 // }



}