<?php
require_once("Constants.php");
class ClassCRUD
{
 private $db;
 function __construct($DB_con)
 {
  $this->db = $DB_con;
 }
 
 public function create($name, $symbol, $school_id)
 {
  $response = array();	
  $response["error"] = true;
  $response["message"] = "";
  try
  {
   $stmt = $this->db->prepare("INSERT INTO classes(name, symbol, school_id) VALUES(:name, :symbol, :school_id)");
   $stmt->bindparam(":name", $name);
   $stmt->bindparam(":symbol", $symbol);
   $stmt->bindparam(":school_id", $school_id);
   if($stmt->execute()){
	   $response["error"] = false;  
	   $response["id"] = $this->db->lastInsertId();  
       $response["code"] = INSERT_SUCCESS; 
	   $prodID = $response["id"];
   }else{
	   $response["error"] = true;  
       $response["code"] = INSERT_FAILURE; 
	   $response["message"] = "Error while inserting class.";
   }
   return $response;
  }
  catch(PDOException $e)
  {
   //echo $e->getMessage();  
   $response["error"] = true;  
   $response["code"] = INSERT_FAILURE; 
   $response["message"] = "Exception while creating class. Please try again.";
   return $response;
  }
  
 }
 
 
 public function update($id, $name, $symbol, $school_id)
 {
  try
  {
   $stmt=$this->db->prepare("UPDATE classes SET name=:name, 
                symbol =:symbol,
				school_id =:school_id 
                WHERE id=:id");
   
  $stmt->bindparam(":name", $name);
   $stmt->bindparam(":symbol", $symbol);
   $stmt->bindparam(":school_id", $school_id);
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
  $stmt = $this->db->prepare("SELECT * FROM classes WHERE id=:id");
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
 
  public function getAllClasses()
 {
  $stmt = $this->db->prepare("SELECT * FROM classes ORDER BY id DESC");
  $stmt->execute();
  $editRow=$stmt->fetchAll();
  return $editRow;
 }
 
 
    public function isIDExists($id)
 {
  $stmt = $this->db->prepare("SELECT id FROM classes WHERE id=:id");
  $result = $stmt->execute(array(":id"=>$id));
  $rows = $stmt->fetchColumn();
  $num_rows = count($rows);
  return $rows > 0;
 }
 
  
  public function doesIDExist($id)
 {
  $stmt = $this->db->prepare("SELECT count(*) FROM classes WHERE id=:id");
  $result = $stmt->execute(array(":id"=>$id));
  $rows = $stmt->fetchColumn();
  return $rows > 0;
 }
 
  public function getSchoolByID($id)
 {
  $stmt = $this->db->prepare("SELECT school_id FROM classes WHERE id = :id");
  $stmt->execute(array(":id"=>$id));
  $rows = $stmt->fetchColumn(); 
  return $rows;
 }
 
   public function getAllOurClasses($school_id)
 {
  $stmt = $this->db->prepare("SELECT * FROM classes WHERE school_id=:school_id ORDER BY id DESC");
  $stmt->execute(array(":school_id"=>$school_id));
  $editRow=$stmt->fetchAll();
  return $editRow;
 }
 
  public function delete($id)
 {
  $stmt = $this->db->prepare("DELETE FROM classes WHERE id=:id");
  $stmt->bindparam(":id",$id);
   return $stmt->execute();
  //return true;
 }
 
}