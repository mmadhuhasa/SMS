<?php
require_once("Constants.php");
class UserRoleCRUD
{
 private $db;
 
 function __construct($DB_con)
 {
  $this->db = $DB_con;
 }
 
 public function create($name, $description)
 {
  $response = array();
  $response["error"] = true;
  $response["message"] = "";
  try
  {
   $stmt = $this->db->prepare("INSERT INTO user_roles(name, description) VALUES(:name, :description)");
   $stmt->bindparam(":name", $name);
   $stmt->bindparam(":description", $description);
   if($stmt->execute()){
	   $response["error"] = false;  
	   $response["id"] = $this->db->lastInsertId();  
       $response["code"] = INSERT_SUCCESS; 
	   $prodID = $response["id"];
   }else{
	   $response["error"] = true;  
       $response["code"] = INSERT_FAILURE; 
	   $response["message"] = "Error while creating new user role.";
   }
   return $response;
  }
  catch(PDOException $e)
  {
   //echo $e->getMessage();  
   $response["error"] = true;  
   $response["code"] = INSERT_FAILURE; 
   $response["message"] = "Exception while creating new opening.Please try again.";
   return $response;
  }
  
 }
 
 
 public function update($id, $name, $description)
 {
  $response = array();	
  $response["error"] = true;
  $response["message"] = "";
  try
  {
   $stmt=$this->db->prepare("UPDATE user_roles SET name=:name, 
                description =:description 
                WHERE id=:id");
   
  $stmt->bindparam(":name", $name);
   $stmt->bindparam(":description", $description);
   $stmt->bindparam(":id",$id);
   if($stmt->execute()){
    $response["error"] = false;  
	$response["message"] = "Role has been updated successfully.";
   }
  }
  catch(PDOException $e)
  {
    $response["error"] = true;   
	$response["message"] = "Error while updating user role.".$e->getMessage();
	return $response;
  }
  return $response;
 }
 
 public function getID($id)
 {
  $stmt = $this->db->prepare("SELECT * FROM user_roles WHERE id=:id");
  $stmt->execute(array(":id"=>$id));
  $editRow=$stmt->fetch(PDO::FETCH_ASSOC);
  return $editRow;
 }
 
 public function getNameByID($id)
 {
  $stmt = $this->db->prepare("SELECT name FROM user_roles WHERE id = :id");
  $stmt->execute(array(":id"=>$id));
  $rows = $stmt->fetchColumn(); 
  return $rows;
 }
 
   public function doesIDExist($id)
 {
  $stmt = $this->db->prepare("SELECT count(*) FROM user_roles WHERE id=:id");
  $result = $stmt->execute(array(":id"=>$id));
  $rows = $stmt->fetchColumn();
  //$num_rows = count($rows);
  return $rows > 0;
 }
 
   public function getAllUserRoles()
 {
  $stmt = $this->db->prepare("SELECT * FROM user_roles ORDER BY id DESC");
   $stmt->execute();
  $editRow=$stmt->fetchAll();
  return $editRow;
 }
 
  public function getAllUserRolesForSchool($school_id)
 {
  $stmt = $this->db->prepare("SELECT * FROM user_roles WHERE class_id IN(SELECT id FROM classes WHERE school_id=:school_id) ORDER BY id ASC");
  $stmt->execute(array(":school_id"=>$school_id));
  $editRow=$stmt->fetchAll();
  return $editRow;
 }
 
  public function delete($id)
 {
  $stmt = $this->db->prepare("DELETE FROM user_roles WHERE id=:id");
  $stmt->bindparam(":id",$id);
  $stmt->execute();
  return true;
 }
}