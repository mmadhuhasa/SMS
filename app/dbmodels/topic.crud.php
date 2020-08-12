<?php
require_once("Constants.php");
class TopicCRUD
{
 private $db;
 function __construct($DB_con)
 {
  $this->db = $DB_con;
 }
 
 public function create($title, $subject_id, $class_id, $description,$image)   
 {
  $response = array();	
  $response["error"] = true;  
  $image = "";  
  try
  {
   $stmt = $this->db->prepare("INSERT INTO topics(title, subject_id, class_id, description, image)VALUES(:title, :subject_id,:class_id,:description,:image)");
   $stmt->bindparam(":title", $title);
   $stmt->bindparam(":subject_id", $subject_id);
   $stmt->bindparam(":class_id", $class_id);
   $stmt->bindparam(":description", $description);
   $stmt->bindparam(":image", $image);
   if($stmt->execute()){
	   $response["error"] = false;  
	   $response["id"] = $this->db->lastInsertId();  
     $response["code"] = INSERT_SUCCESS; 
	   $response["message"] = "Topic has been created successfully.";
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
	   $response["message"] = "Failed to create topic. Please try again.";
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
 
 
 public function update($id, $title, $subject_id, $class_id,$description,$image)
 {
  try
  {
   $stmt=$this->db->prepare("UPDATE topics SET class=:class, 
                subject=:subject,
				topic_name=:topic_name WHERE id=:id");
   
   $stmt->bindparam(":title",$title);
   $stmt->bindparam(":subject_id",$subject_id);
   $stmt->bindparam(":class_id",$class_id);
   $stmt->bindparam(":description",$description);
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
 }
 
 public function getID($id)
 {
  $stmt = $this->db->prepare("SELECT * FROM topics WHERE id=:id");
  $stmt->execute(array(":id"=>$id));
  $editRow=$stmt->fetch(PDO::FETCH_ASSOC);
  return $editRow;
 }
 
  public function getNameByID($id)
 {
  $stmt = $this->db->prepare("SELECT title FROM topics WHERE id=:id");
  $stmt->execute(array(":id"=>$id));
  $result = $stmt->fetchColumn();
  return $result;
 }
 
  public function getAllTopics()
 {
  $stmt = $this->db->prepare("SELECT * FROM topics ORDER BY id DESC");
  $stmt->execute();
  $editRow=$stmt->fetchAll();
  return $editRow;
 }
 
   public function getAllTopicsForSchool($school_id)
 {
  $stmt = $this->db->prepare("SELECT * FROM topics WHERE class_id IN(SELECT id FROM classes WHERE school_id=:school_id) ORDER BY id ASC");
  $stmt->execute(array(":school_id"=>$school_id));
  $editRow=$stmt->fetchAll();
  return $editRow;
 }

 public function doesIDExist($id)
 {
  $stmt = $this->db->prepare("SELECT count(*) FROM user_roles WHERE id=:id");
  $result = $stmt->execute(array(":id"=>$id));
  $rows = $stmt->fetchColumn();
  //$num_rows = count($rows);
  return $rows > 0;
 }
 
 public function delete($id)
 {
  $stmt = $this->db->prepare("DELETE FROM topics WHERE id=:id");
  $stmt->bindparam(":id",$id);
  $stmt->execute();
  return true;
 }
 
  public function updateImage($id, $image)
 {	 
 $image = "images/topics/".$image;
  $response = array();	
  $response["error"] = true; 
  $stmt2=$this->db->prepare("UPDATE topics SET image=:image
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