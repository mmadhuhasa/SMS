<?php
require_once("Constants.php");
class SubjectCRUD
{
 private $db;
 function __construct($DB_con)
 {
  $this->db = $DB_con;
 }
 
 public function create($title, $description,$school_id ,$image)   
 {
  $response = array();	
  $response["error"] = true;  
  $image = "";  
  try
  {
   $stmt = $this->db->prepare("INSERT INTO subjects(title, description, school_id, image)VALUES(:title, :description,:school_id, :image)");
   $stmt->bindparam(":title", $title);
   $stmt->bindparam(":description", $description);
   $stmt->bindparam(":school_id", $school_id);
   $stmt->bindparam(":image", $image);
   if($stmt->execute()){
	   $response["error"] = false;  
	   $response["id"] = $this->db->lastInsertId();  
       $response["code"] = INSERT_SUCCESS; 
	   $response["message"] = "Subject has been created successfully.";
	   /************* UPLOAD IMAGE *************
	   try{
		   if(!empty($image)){
			$path = "images/projects/".$response["id"].".jpg";
		    $actualpath = $path;
			
			file_put_contents("uploads/".$path, base64_decode($image));
			$stmt2=$this->db->prepare("UPDATE projects SET image=:image
             WHERE id=:id");
   
            $stmt2->bindparam(":image",$actualpath);
            $stmt2->bindparam(":id",$response["id"]);
            $res = $stmt2->execute();
			}
	   }catch (Exception $e) {
		   $response["error"] = true;   
		   $response["message"] = $e->getMessage();
           return $response;
	   }
	   *************************************/
   }else{
	   $response["error"] = true;   	   
       $response["code"] = INSERT_FAILURE; 
	   $response["message"] = "Failed to create subject. Please try again.";
   }
   return $response;
  }
  catch(PDOException $e)
  {
 $response["error"] = true;   	   
       $response["code"] = INSERT_FAILURE;       
   $response["message"] = $e->getMessage();
   return $response;
  }
  
 }
 
 
 public function update($id, $title, $description,$school_id, $image)
 {
  try
  {
   $stmt=$this->db->prepare("UPDATE subjects SET title=:title, 
                description=:description,
				school_id=:school_id,
				image=:image WHERE id=:id");
   
   $stmt->bindparam(":title",$title);
   $stmt->bindparam(":description",$description);
   $stmt->bindparam(":school_id",$school_id);
   $stmt->bindparam(":image",$image);
   $stmt->bindparam(":id",$id);
   $stmt->execute();
   return true; 
  }
  catch(PDOException $e)
  {
   //echo $e->getMessage(); 
   return false;
  }
  return false;
 }
 
 public function getID($id)
 {
  $stmt = $this->db->prepare("SELECT * FROM subjects WHERE id=:id");
  $stmt->execute(array(":id"=>$id));
  $editRow=$stmt->fetch(PDO::FETCH_ASSOC);
  return $editRow;
 }
 
  public function getNameByID($id)
 {
  $stmt = $this->db->prepare("SELECT title FROM subjects WHERE id=:id");
  $stmt->execute(array(":id"=>$id));
  $result = $stmt->fetchColumn();
  return $result;
 }
 
  public function getAllSubjects()
 {
  $stmt = $this->db->prepare("SELECT * FROM subjects ORDER BY id DESC");
  $stmt->execute();
  $editRow=$stmt->fetchAll();
  return $editRow;
 }
 
   public function getAllSubjectsForSchool($school_id)
 {
  $stmt = $this->db->prepare("SELECT * FROM subjects WHERE school_id=:school_id ORDER BY id ASC");
  $stmt->execute(array(":school_id"=>$school_id));
  $editRow=$stmt->fetchAll();
  return $editRow;
 }
 
  public function doesIDExist($id)
 {
  $stmt = $this->db->prepare("SELECT count(*) FROM subjects WHERE id=:id");
  $result = $stmt->execute(array(":id"=>$id));
  $rows = $stmt->fetchColumn();
  return $rows > 0;
 }
 
 public function delete($id)
 {
  $stmt = $this->db->prepare("DELETE FROM subjects WHERE id=:id");
  $stmt->bindparam(":id",$id);
  $stmt->execute();
  return true;
 }
 
 public function updateImage($id, $image)
 {	 
 $image = "images/subjects/".$image;
  $response = array();	
  $response["error"] = true; 
  $stmt2=$this->db->prepare("UPDATE subjects SET image=:image
             WHERE id=:id");
            $stmt2->bindparam(":image",$image);
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
 
}