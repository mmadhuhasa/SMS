<?php
require_once("Constants.php");
class PeriodCRUD
{
 private $db;
 
 function __construct($DB_con)
 {
  $this->db = $DB_con;
 }
 
 public function create($school_id, $name, $start_time, $end_time)
 {
  $response = array();	
  $response["error"] = true;
  $response["message"] = "";
  try
  {
   $stmt = $this->db->prepare("INSERT INTO timetable_periods(school_id, name, start_time, end_time) VALUES(:school_id, :name, :start_time, :end_time)");
   $stmt->bindparam(":school_id", $school_id);
   $stmt->bindparam(":name", $name);
   $stmt->bindparam(":start_time", $start_time);
   $stmt->bindparam(":end_time", $end_time);
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
   $response["message"] = "Exception while creating new opening.Please try again.";
   return $response;
  }
  
 }
 
  public function getPeriods($school_id)
 {
  $stmt = $this->db->prepare("SELECT * FROM timetable_periods WHERE school_id=:school_id ORDER BY id ASC");
   $stmt->execute(array(":school_id"=>$school_id));
  $editRow=$stmt->fetchAll();
  return $editRow;
 }
 
   public function isIDExists($id)
 {
  $stmt = $this->db->prepare("SELECT * FROM timetable_periods WHERE id=:id");
  $result = $stmt->execute(array(":id"=>$id));
  $rows = $stmt->fetchAll();
  $num_rows = count($rows);
  return $num_rows > 0;
 }
 
 
 public function update($id, $name, $start_time, $end_time)
 {
  try
  {
   $stmt=$this->db->prepare("UPDATE timetable_periods SET name=:name, start_time=:start_time, 
                end_time =:end_time 
                WHERE id=:id");
   
  $stmt->bindparam(":name", $name);
  $stmt->bindparam(":start_time", $start_time);
   $stmt->bindparam(":end_time", $end_time);
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
  $stmt = $this->db->prepare("SELECT * FROM timetable_periods WHERE id=:id");
  $stmt->execute(array(":id"=>$id));
  $editRow=$stmt->fetch(PDO::FETCH_ASSOC);
  return $editRow;
 }
 
  /*
  public function getAlltimetable_periods()
 {
  $stmt = $this->db->prepare("SELECT * FROM timetable_periods ORDER BY id ASC");
  $stmt->execute();
  $editRow=$stmt->fetchAll();
  return $editRow;
 }
 
  public function getAlltimetable_periods($school_id)
 {
  $stmt = $this->db->prepare("SELECT * FROM timetable_periods WHERE school_id=:school_id ORDER BY id ASC");
   $stmt->execute(array(":school_id"=>$school_id));
  $editRow=$stmt->fetchAll();
  return $editRow;
 }*/
 
  public function getSchoolIDByID($id)
 {
  $stmt = $this->db->prepare("SELECT school_id FROM timetable_periods WHERE id = :id");
  $stmt->execute(array(":id"=>$id));
  $rows = $stmt->fetchColumn(); 
  return $rows;
 }
 
  public function delete($id)
 {
  $stmt = $this->db->prepare("DELETE FROM timetable_periods WHERE id=:id");
  $stmt->bindparam(":id",$id);
  $stmt->execute();
  return true;
 }
 
}