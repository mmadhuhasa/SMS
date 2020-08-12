<?php
//namespace App\Models\Student;
require_once("Constants.php");
class StudentCRUD
{
 private $db;
 
 function __construct($DB_con)
 {
  $this->db = $DB_con;
 }
 
    public function isUserProfileRegistered($user_id)
 {
  $stmt = $this->db->prepare("SELECT id FROM student_details WHERE user_id=:user_id");
  $result = $stmt->execute(array(":user_id"=>$user_id));
  $rows = $stmt->fetchColumn();
  $num_rows = count($rows);
  return $rows > 0;
 }
  
 public function create($user_id, $admission_no, $roll_no, $class, $section, $religion, $caste, $blood_group, $admission_date)
 {
  $response = array();
  $response["error"] = true;
  $response["code"] = INSERT_FAILURE;
  try
  {
   if(!$this->isUserProfileRegistered($user_id)){
   $stmt = $this->db->prepare("INSERT INTO student_details(user_id, admission_no, roll_no, class, section, religion, caste, blood_group, admission_date) VALUES(:user_id, :admission_no, :roll_no, :class, :section, :religion, :caste, :blood_group, :admission_date)");
   $stmt->bindparam(":user_id", $user_id);
   $stmt->bindparam(":admission_no", $admission_no);
   $stmt->bindparam(":roll_no", $roll_no);
    $stmt->bindparam(":class", $class);
   $stmt->bindparam(":section", $section);
   $stmt->bindparam(":religion", $religion);
   $stmt->bindparam(":caste", $caste);
   $stmt->bindparam(":blood_group", $blood_group);
   $stmt->bindparam(":admission_date", $admission_date); 
   if($stmt->execute()){
	   $response["error"] = false;  
	   $response["id"] = $this->db->lastInsertId();  
       $response["code"] = INSERT_SUCCESS; 
	   $response["message"] = "You are now a registered as student successfully."; 
   }else{
	   $response["error"] = true;  
	   $response["message"] = "Oops! An error occurred while registering student. Please try again."; 
       $response["code"] = INSERT_FAILURE; 
   }
   }
   else{
	   $response["error"] = true;  
	   $response["message"] = "Looks like you are already registered your profile."; 
       $response["code"] = ALREADY_EXIST;
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
 
 public function getID($id)
 {
  $stmt = $this->db->prepare("SELECT * FROM student_details WHERE id=:id");
  $stmt->execute(array(":id"=>$id));
  $editRow=$stmt->fetch(PDO::FETCH_ASSOC);
  return $editRow;
 }
 
 public function getByUserID($user_id)
 {
  $stmt = $this->db->prepare("SELECT * FROM student_details WHERE user_id=:user_id");
  $stmt->execute(array(":user_id"=>$user_id));
  $editRow=$stmt->fetch(PDO::FETCH_ASSOC);
  return $editRow;
 }
 
  public function update($user_id, $admission_no, $roll_no, $class, $section, $religion, $caste, $blood_group, $admission_date, $date_updated)
 {
  $response = array();	
  $response["error"] = true; 
  $response["message"] = "Profile update request is received successfully."; 
  $response["note"] = ""; 
  
  try
  {
   $stmt=$this->db->prepare("UPDATE student_details SET admission_no=:admission_no, roll_no=:roll_no, 
   class=:class,
   section=:section,
                religion=:religion, 
                caste=:caste, 
                blood_group=:blood_group,
				admission_date=:admission_date,
				date_updated=:date_updated 
             WHERE user_id=:user_id");
   $stmt->bindparam(":admission_no", $admission_no);
   $stmt->bindparam(":roll_no", $roll_no);
   $stmt->bindparam(":class", $class);
   $stmt->bindparam(":section", $section);
   $stmt->bindparam(":religion", $religion);
   $stmt->bindparam(":caste", $caste);
   $stmt->bindparam(":blood_group", $blood_group);
   $stmt->bindparam(":admission_date", $admission_date);
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
  $stmt = $this->db->prepare("DELETE FROM student_details WHERE id=:id");
  $stmt->bindparam(":id",$id);
  $stmt->execute();
  return true;
 }
 
}