<?php
require_once("Constants.php");
class FAQCRUD
{
 private $db;
 
 function __construct($DB_con)
 {
  $this->db = $DB_con;
 }
 
 public function create($question, $answer)
 {
  $response = array();	
  $response["error"] = true;
  $response["message"] = "";
  try
  {
   $stmt = $this->db->prepare("INSERT INTO faqs(question, answer) VALUES(:question, :answer)");
   $stmt->bindparam(":question", $question);
   $stmt->bindparam(":answer", $answer);
   if($stmt->execute()){
	   $response["error"] = false;  
	   $response["id"] = $this->db->lastInsertId();  
       $response["code"] = INSERT_SUCCESS; 
	   $prodID = $response["id"];
   }else{
	   $response["error"] = true;  
       $response["code"] = INSERT_FAILURE; 
	   $response["message"] = "Error while inserting entry.";
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
 
 
 public function update($id, $question, $answer)
 {
  try
  {
   $stmt=$this->db->prepare("UPDATE faqs SET question=:question, 
                answer =:answer 
                WHERE id=:id");
   
  $stmt->bindparam(":question", $question);
   $stmt->bindparam(":answer", $answer);
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
  $stmt = $this->db->prepare("SELECT * FROM faqs WHERE id=:id");
  $stmt->execute(array(":id"=>$id));
  $editRow=$stmt->fetch(PDO::FETCH_ASSOC);
  return $editRow;
 }
 
  public function getAllFAQs()
 {
  $stmt = $this->db->prepare("SELECT * FROM faqs ORDER BY id ASC");
  $stmt->execute();
  $editRow=$stmt->fetchAll();
  return $editRow;
 }
 
}