<?php
require_once("Constants.php");
class MasterCRUD
{
 private $db;
 
 function __construct($DB_con)
 {
  $this->db = $DB_con;
 }
 
  public function getAllSubjects()
 {
  $stmt = $this->db->prepare("SELECT * FROM subjects ORDER BY title DESC");
  $result = $stmt->execute();
  $editRow=$stmt->fetchAll();
  return $editRow;
 }
 
  public function getAllCountries()
 {
  $stmt = $this->db->prepare("SELECT * FROM country ORDER BY name ASC");
  $result = $stmt->execute();
  $editRow=$stmt->fetchAll();
  return $editRow;
 }
 
  public function getAllDays()
 {
  $stmt = $this->db->prepare("SELECT * FROM days ORDER BY id ASC");
  $result = $stmt->execute();
  $editRow=$stmt->fetchAll();
  return $editRow;
 }
 
  public function getCurrencies()
 {
  $stmt = $this->db->prepare("SELECT * FROM currencies ORDER BY id DESC");
  $result = $stmt->execute();
  $editRow=$stmt->fetchAll();
  return $editRow;
 }
 
  public function getEducationLevels()
 {
  $stmt = $this->db->prepare("SELECT * FROM education_levels ORDER BY id DESC");
  $result = $stmt->execute();
  $editRow=$stmt->fetchAll();
  return $editRow;
 }
 
 public function getAllClasses()
 {
  $stmt = $this->db->prepare("SELECT * FROM classes ORDER BY id DESC");
  $result = $stmt->execute();
  $editRow=$stmt->fetchAll();
  return $editRow;
 }

    public function getNumJobAreas($enabled)
 { 
  $sql = "SELECT count(*) FROM job_areas WHERE enabled=:enabled";
  $stmt = $this->db->prepare($sql); 
  $stmt->execute(array(":enabled"=>$enabled)); 
  $number_of_rows = $stmt->fetchColumn(); 
  return $number_of_rows;
 }
 
 
 
  public function getJobTypes()
 {
  $stmt = $this->db->prepare("SELECT * FROM job_types ORDER BY id ASC");
  $result = $stmt->execute();
  $editRow=$stmt->fetchAll();
  return $editRow;
 }
 
  public function getJobTypeNameByID($id)
 {
  $stmt = $this->db->prepare("SELECT title FROM job_types WHERE id = :id");
  $stmt->execute(array(":id"=>$id));
  $rows = $stmt->fetchColumn(); 
  return $rows;
 }
 
 public function getNotificationType()
 {
  $stmt = $this->db->prepare("SELECT * FROM notification_types ORDER BY id DESC");
  $result = $stmt->execute();
  $editRow=$stmt->fetchAll();
  return $editRow;
 }
 
 public function getPayoutModes()
 {
  $stmt = $this->db->prepare("SELECT * FROM payout_modes ORDER BY id DESC");
  $result = $stmt->execute();
  $editRow=$stmt->fetchAll();
  return $editRow;
 }
 
  public function getRelocationInputs()
 {
  $stmt = $this->db->prepare("SELECT * FROM relocation_inputs ORDER BY id ASC");
  $result = $stmt->execute();
  $editRow=$stmt->fetchAll();
  return $editRow;
 }
 
 
}