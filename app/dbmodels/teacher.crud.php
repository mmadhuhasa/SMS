<?php
require_once("Constants.php");
class TeacherCRUD
{
 private $db;
 
 function __construct($DB_con)
 {
  $this->db = $DB_con;
 }
  
 public function create($user_id, $designation, $religion, $role, $joining_date)
 {
  $response = array();
  $response["error"] = true;
  $response["code"] = INSERT_FAILURE;
  try
  { 
   $stmt = $this->db->prepare("INSERT INTO teacher_details(user_id, designation, religion, role, joining_date) VALUES(:user_id, :designation, :religion, :role, :joining_date)");
   $stmt->bindparam(":user_id", $user_id);
   $stmt->bindparam(":designation", $designation);
   $stmt->bindparam(":religion", $religion);
   $stmt->bindparam(":role", $role);
   $stmt->bindparam(":joining_date", $joining_date);
   if($stmt->execute()){
	   $response["error"] = false;  
	   $response["id"] = $this->db->lastInsertId();  
       $response["code"] = INSERT_SUCCESS; 
	   $response["message"] = "Teacher profile saved successfully."; 
   }else{
	   $response["error"] = true;  
	   $response["message"] = "Oops! An error occurred while creating teacher profile. Please try again."; 
       $response["code"] = INSERT_FAILURE; 
   }
   return $response;
  }
  catch(PDOException $e)
  {
   $response["error"] = true;  
   $response["message"] = "Exception happened: ".$e->getMessage(); 
   echo $e->getMessage();  
   return $response;
  }
 }
 
  public function getByUserID($user_id)
 {
  $stmt = $this->db->prepare("SELECT * FROM teacher_details WHERE user_id=:user_id");
  $stmt->execute(array(":user_id"=>$user_id));
  $editRow=$stmt->fetch(PDO::FETCH_ASSOC);
  return $editRow;
 }
 
 public function getID($id)
 {
  $stmt = $this->db->prepare("SELECT * FROM teacher_details WHERE id=:id");
  $stmt->execute(array(":id"=>$id));
  $editRow=$stmt->fetch(PDO::FETCH_ASSOC);
  return $editRow;
 }
 
  public function update($user_id, $designation, $religion, $role, $joining_date, $date_updated)
 {
  $response = array();	
  $response["error"] = true; 
  $response["message"] = "Profile update request is received successfully."; 
  $response["note"] = ""; 
  
  try
  {
   $stmt=$this->db->prepare("UPDATE teacher_details SET designation=:designation,
   religion=:religion, role=:role, joining_date=:joining_date, 
				date_updated=:date_updated 
             WHERE user_id=:user_id");
   $stmt->bindparam(":designation", $designation);
   $stmt->bindparam(":religion", $religion);
   $stmt->bindparam(":role", $role);
   $stmt->bindparam(":joining_date", $joining_date);
   $stmt->bindparam(":date_updated", $date_updated);
   $stmt->bindparam(":user_id",$user_id);
   $stmt->execute();
   $response["error"] = false;
   $response["message"] = "Profile updated successfully."; 
   return $response; 
  }
  catch(PDOException $e)
  {
   $response["error"] = true; 
   $response["message"] = "There was an error updating the profile."; 
   $response["debug"] = "".$e->getMessage(); 
   return $response;
  }
 }
 
 public function delete($id)
 {
  $stmt = $this->db->prepare("DELETE FROM teacher_details WHERE id=:id");
  $stmt->bindparam(":id",$id);
  $stmt->execute();
  return true;
 }
 
}