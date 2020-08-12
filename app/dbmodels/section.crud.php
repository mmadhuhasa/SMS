<?php
require_once("Constants.php");
class SectionCRUD
{
 private $db;
 
 function __construct($DB_con)
 {
  $this->db = $DB_con;
 }
 
 public function create($name, $class_id, $school_id, $strength)
 {
  $response = array();	
  $response["error"] = true;
  $response["message"] = "";
  try
  {
   $stmt = $this->db->prepare("INSERT INTO sections(name, class_id, school_id, strength) VALUES(:name, :class_id, :school_id, :strength)");
   $stmt->bindparam(":name", $name);
   $stmt->bindparam(":class_id", $class_id);
   $stmt->bindparam(":school_id", $school_id);
   $stmt->bindparam(":strength", $strength);
   if($stmt->execute()){
	   $response["error"] = false;  
	   $response["id"] = $this->db->lastInsertId();  
       $response["code"] = INSERT_SUCCESS; 
	   $prodID = $response["id"];
   }else{
	   $response["error"] = true;  
       $response["code"] = INSERT_FAILURE; 
	   $response["message"] = "Error while adding new section.";
   }
   return $response;
  }
  catch(PDOException $e)
  {
   //echo $e->getMessage();  
   $response["error"] = true;  
   $response["code"] = INSERT_FAILURE; 
   $response["message"] = "Exception while creating new section.Please try again.";
   return $response;
  }
  
 }
 
 
 public function update($id, $name, $class_id, $school_id, $strength)
 {
  try
  {
   $stmt=$this->db->prepare("UPDATE sections SET name=:name, 
                class_id =:class_id,
				school_id =:school_id,
				strength =:strength 
                WHERE id=:id");
   
  $stmt->bindparam(":name", $name);
   $stmt->bindparam(":class_id", $class_id);
   $stmt->bindparam(":school_id", $school_id);
   $stmt->bindparam(":strength", $strength);
   $stmt->bindparam(":id",$id);
   if($stmt->execute()){
	   return true;
   }
   return false; 
  }
  catch(PDOException $e)
  {
   //echo $e->getMessage(); 
   return false;
  }
 }
 
 public function getID($id)
 {
  $stmt = $this->db->prepare("SELECT * FROM sections WHERE id=:id");
  $stmt->execute(array(":id"=>$id));
  $editRow=$stmt->fetch(PDO::FETCH_ASSOC);
  return $editRow;
 }
 
   public function getNameByID($id)
 {
  $stmt = $this->db->prepare("SELECT name FROM classes WHERE id = :id");
  $stmt->execute(array(":id"=>$id));
  $rows = $stmt->fetchColumn(); 
  return $rows;
 }
 
  public function getAllSections()
 {
  $stmt = $this->db->prepare("SELECT * FROM sections ORDER BY id ASC");
  $stmt->execute();
  $editRow=$stmt->fetchAll();
  return $editRow;
 }
 
  public function doesIDExist($id)
 {
  $stmt = $this->db->prepare("SELECT count(*) FROM sections WHERE id=:id");
  $result = $stmt->execute(array(":id"=>$id));
  $rows = $stmt->fetchColumn();
  return $rows > 0;
 }
 
    public function getAllSectionsForSchool($school_id)
 {
  $stmt = $this->db->prepare("SELECT * FROM sections WHERE school_id=:school_id ORDER BY id ASC");
  $stmt->execute(array(":school_id"=>$school_id));
  $editRow=$stmt->fetchAll();
  return $editRow;
 }
 
   public function getAllSectionsForClass($class_id)
 {
  $stmt = $this->db->prepare("SELECT * FROM sections WHERE class_id=:class_id ORDER BY id ASC");
  $stmt->execute(array(":class_id"=>$class_id));
  $editRow=$stmt->fetchAll();
  return $editRow;
 }
 
    public function getAllSectionsFor($school_id, $class_id)
 {
  $stmt = $this->db->prepare("SELECT * FROM sections WHERE school_id=:school_id AND class_id=:class_id ORDER BY id ASC");
  $stmt->execute(array(":school_id"=>$school_id, ":class_id"=>$class_id));
  $editRow=$stmt->fetchAll();
  return $editRow;
 }
 
  public function delete($id)
 {
  $stmt = $this->db->prepare("DELETE FROM sections WHERE id=:id");
  $stmt->bindparam(":id",$id);
  return $stmt->execute();
  //return true;
 }
 
}