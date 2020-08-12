<?php
require_once("Constants.php");
class AppModuleCRUD
{
 private $db;
 
 function __construct($DB_con)
 {
  $this->db = $DB_con;
 }
 
 public function createUserPermission($user_id, $permission_id, $granted)
 {
  $response = array();
  $response["error"] = true;
  $response["message"] = "";
  try
  {
   $stmt = $this->db->prepare("INSERT INTO user_permissions(user_id, permission_id, granted) VALUES(:user_id, :permission_id, :granted)");
   $stmt->bindparam(":user_id", $user_id);
   $stmt->bindparam(":permission_id", $permission_id);
   $stmt->bindparam(":granted", $granted);
   if($stmt->execute()){
	   $response["error"] = false;  
	   $response["id"] = $this->db->lastInsertId();  
       $response["code"] = INSERT_SUCCESS; 
	   $prodID = $response["id"];
	   $response["message"] = "Permission saved successfully.";
   }else{
	   $response["error"] = true;  
       $response["code"] = INSERT_FAILURE; 
	   $response["message"] = "Error while granting permission.";
   }
   return $response;
  }
  catch(PDOException $e)
  {
   //echo $e->getMessage();  
   $response["error"] = true;  
   $response["code"] = INSERT_FAILURE; 
   $response["message"] = "Exception while granting or revoking permission. Please try again.";
   return $response;
  }
  
 }
 
 
 public function updateUserPermission($id, $user_id, $permission_id, $granted)
 {
  $response = array();	
  $response["error"] = true;
  $response["message"] = "";
  try
  {
   $stmt=$this->db->prepare("UPDATE user_permissions SET user_id=:user_id, 
                permission_id =:permission_id,
				granted =:granted 
                WHERE id=:id");
   
   $stmt->bindparam(":user_id", $user_id);
   $stmt->bindparam(":permission_id", $permission_id);
   $stmt->bindparam(":granted", $granted);
   $stmt->bindparam(":id",$id);
   if($stmt->execute()){
    $response["error"] = false;  
	$response["message"] = "Permission updated successfully.";
   }
  }
  catch(PDOException $e)
  {
    $response["error"] = true;   
	$response["message"] = "Error while updating Permission. ".$e->getMessage();
	return $response;
  }
  return $response;
 }
 
  public function isPermitted($user_id, $permission_id)
 {
  $sql = "SELECT granted FROM user_permissions WHERE permission_id=:permission_id AND user_id =:user_id";
  $stmt = $this->db->prepare($sql); 
  $stmt->execute(array(":permission_id"=>$permission_id, ":user_id"=>$user_id)); 
  $number_of_rows = $stmt->fetchColumn(); 
  return $number_of_rows > 0;
 }
 
   public function getUserPermissionRecordID($user_id, $permission_id)
 { 
  $sql = "SELECT id FROM user_permissions WHERE permission_id=:permission_id AND user_id =:user_id";
  $stmt = $this->db->prepare($sql); 
  $stmt->execute(array(":permission_id"=>$permission_id, ":user_id"=>$user_id)); 
  $number_of_rows = $stmt->fetchColumn(); 
  return $number_of_rows;
 }
 
 /*******************************************/
 
 
 public function getID($id)
 {
  $stmt = $this->db->prepare("SELECT * FROM app_modules WHERE id=:id");
  $stmt->execute(array(":id"=>$id));
  $editRow=$stmt->fetch(PDO::FETCH_ASSOC);
  return $editRow;
 }
 
 public function getModuleNameByID($id)
 {
  $stmt = $this->db->prepare("SELECT name FROM app_modules WHERE id = :id");
  $stmt->execute(array(":id"=>$id));
  $rows = $stmt->fetchColumn(); 
  return $rows;
 }
 
    public function getAllAppModules()
 {
  $stmt = $this->db->prepare("SELECT * FROM app_modules ORDER BY id ASC");
   $stmt->execute();
  $editRow=$stmt->fetchAll();
  return $editRow;
 }
 
  public function getAppModulesFor($module_id)
 {
  $stmt = $this->db->prepare("SELECT * FROM app_module_permissions WHERE module_id=:module_id ORDER BY id ASC");
  $stmt->execute(array(":module_id"=>$module_id));
  $editRow=$stmt->fetchAll();
  return $editRow;
 }
 
}