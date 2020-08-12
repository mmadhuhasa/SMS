<?php
require_once("Constants.php");
class TimetableCRUD
{
 private $db;
 
 function __construct($DB_con)
 {
  $this->db = $DB_con;
 }
 
 public function create($school_id, $class_id, $day_id, $period_id, $faculty, $subject, $title)
 {
  $response = array();	
  $response["error"] = true;
  $response["message"] = "";
  try
  {
   $stmt = $this->db->prepare("INSERT INTO timetable(school_id, class_id, day_id, period_id, faculty, subject, title) VALUES(:school_id, :class_id, :day_id, :period_id, :faculty, :subject, :title)");
   $stmt->bindparam(":school_id", $school_id);
   $stmt->bindparam(":class_id", $class_id);
   $stmt->bindparam(":day_id", $day_id);
   $stmt->bindparam(":period_id", $period_id);
   $stmt->bindparam(":faculty", $faculty);
   $stmt->bindparam(":subject", $subject);
   $stmt->bindparam(":title", $title);
   if($stmt->execute()){
	   $response["error"] = false;  
	   $response["id"] = $this->db->lastInsertId();  
       $response["code"] = INSERT_SUCCESS; 
	   $prodID = $response["id"];
   }else{
	   $response["error"] = true;  
       $response["code"] = INSERT_FAILURE; 
	   $response["message"] = "Error while inserting period.";
   }
   return $response;
  }
  catch(PDOException $e)
  {
   //echo $e->getMessage();  
   $response["error"] = true;  
   $response["code"] = INSERT_FAILURE; 
   $response["message"] = "Exception while creating new timetable entry.Please try again.";
   return $response;
  }
  
 }
 
 
 public function update($id, $class_id, $day_id, $period_id, $faculty, $subject, $title)
 {
   $stmt=$this->db->prepare("UPDATE timetable SET class_id=:class_id,
   day_id=:day_id,
   period_id=:period_id,
   faculty=:faculty,
   subject=:subject,
   title=:title WHERE id=:id ");
   
    $stmt->bindparam(":class_id",$class_id);
    $stmt->bindparam(":day_id", $day_id);
    $stmt->bindparam(":period_id", $period_id);
	$stmt->bindparam(":faculty", $faculty);
	$stmt->bindparam(":subject", $subject);
	$stmt->bindparam(":title", $title);
   $stmt->bindparam(":id", $id);
   if($stmt->execute()){
	    return true;
   }
 return false;
 }
 
 
 public function getID($id)
 {
  $stmt = $this->db->prepare("SELECT * FROM timetable WHERE id=:id");
  $stmt->execute(array(":id"=>$id));
  $editRow=$stmt->fetch(PDO::FETCH_ASSOC);
  return $editRow;
 }
 
  public function getAlltimetableBy($school_id)
 {
  $stmt = $this->db->prepare("SELECT * FROM timetable WHERE school_id=:school_id ORDER BY id ASC");
   $stmt->execute(array(":school_id"=>$school_id));
  $editRow=$stmt->fetchAll();
  return $editRow;
 }

 public function isPeriodForClassAssigned($school_id, $class_id, $period_id){
	 $stmt = $this->db->prepare("SELECT id FROM timetable WHERE school_id=:school_id AND class_id=:class_id AND period_id=:period_id");
  $result = $stmt->execute(array(":school_id"=>$school_id, ":class_id"=>$class_id, ":period_id"=>$period_id));
  $rows = $stmt->fetchAll();
  $num_rows = count($rows);
  return $num_rows > 0;
 }
 
   public function getPeriodRow($school_id, $class_id, $period_id, $day_id){
  $stmt = $this->db->prepare("SELECT * FROM timetable WHERE school_id=:school_id AND class_id=:class_id AND period_id=:period_id AND day_id=:day_id");
  $result = $stmt->execute(array(":school_id"=>$school_id, ":class_id"=>$class_id, ":period_id"=>$period_id, ":day_id"=>$day_id));
  $rows = $stmt->fetch(PDO::FETCH_ASSOC);
  return $rows;
 }
 
}