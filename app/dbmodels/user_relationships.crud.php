<?php
require_once("Constants.php");
class UserRelationshipCRUD
{
 private $db;
 
 function __construct($DB_con)
 {
  $this->db = $DB_con;
 }
 
 public function create($user_id, $parent_id, $relation, $is_guardian, $note)
 {
  $response = array();	
  $response["error"] = true;
  $response["message"] = "";
  try
  {
   $stmt = $this->db->prepare("INSERT INTO user_relationships(user_id, parent_id, relation, is_guardian, note) VALUES(:user_id, :parent_id, :relation, :is_guardian, :note)");
   $stmt->bindparam(":user_id", $user_id);
   $stmt->bindparam(":parent_id", $parent_id);
   $stmt->bindparam(":relation", $relation);
   $stmt->bindparam(":is_guardian", $is_guardian);
   $stmt->bindparam(":note", $note);
   if($stmt->execute()){
	   $response["error"] = false;  
	   $response["id"] = $this->db->lastInsertId();  
       $response["code"] = INSERT_SUCCESS; 
	   $prodID = $response["id"];
   }else{
	   $response["error"] = true;  
       $response["code"] = INSERT_FAILURE; 
	   $response["message"] = "Error while adding relationship.";
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
 
 
 public function update($id, $user_id, $parent_id, $relation, $is_guardian, $note)
 {
  try
  {
   $stmt=$this->db->prepare("UPDATE user_relationships SET user_id=:user_id,
				parent_id =:parent_id,
				relation =:relation,
				is_guardian =:is_guardian,
				 note =:note 
                WHERE id=:id");
   
   $stmt->bindparam(":user_id", $user_id);
   $stmt->bindparam(":parent_id", $parent_id);
   $stmt->bindparam(":relation", $relation);
   $stmt->bindparam(":is_guardian", $is_guardian);
   $stmt->bindparam(":note", $note);
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
  $stmt = $this->db->prepare("SELECT * FROM user_relationships WHERE id=:id");
  $stmt->execute(array(":id"=>$id));
  $editRow=$stmt->fetch(PDO::FETCH_ASSOC);
  return $editRow;
 }
 
  public function getAllUserRelationships()
 {
  $stmt = $this->db->prepare("SELECT * FROM user_relationships ORDER BY id ASC");
  $stmt->execute();
  $editRow=$stmt->fetchAll();
  return $editRow;
 }
 
   public function getParentsFor($user_id)
 {
  $stmt = $this->db->prepare("SELECT u.first_name, u.last_name, u.mobile, u.email, r.relationship, r.is_guardian FROM users u INNER JOIN user_relationships r ON u.id = r.parent_id AND r.user_id= :user_id");
  //$stmt = $this->db->prepare("SELECT u.first_name, u.last_name, u.mobile, r.relationship, r.is_guardian FROM users u LEFT JOIN user_relationships r WHERE u.id IN (SELECT parent_id FROM user_relationships WHERE user_id=:user_id)");
  $stmt->execute(array(":user_id"=>$user_id));
  $editRow=$stmt->fetchAll();
  return $editRow;
 }
 
  public function getMyWards($parent_id)
 {
  $stmt = $this->db->prepare("SELECT * FROM users WHERE id IN (SELECT user_id FROM user_relationships WHERE parent_id=:parent_id)");
  $stmt->execute(array(":parent_id"=>$parent_id));
  $editRow=$stmt->fetchColumn();
  return $editRow;
 }
 
  public function getNumParents($user_id)
 { 
  $sql = "SELECT count(*) FROM user_relationships WHERE user_id =:user_id";
  $stmt = $this->db->prepare($sql); 
  $stmt->execute(array(":user_id"=>$user_id)); 
  $number_of_rows = $stmt->fetchColumn(); 
  return $number_of_rows;
 }
 
    public function getRelation($user_id, $parent_id)
 {
  $stmt = $this->db->prepare("SELECT relation FROM user_relationships WHERE user_id=:user_id AND parent_id=:parent_id ORDER BY id ASC");
  $stmt->execute(array(":user_id"=>$user_id, ":parent_id"=>$parent_id));
  $editRow=$stmt->fetchColumn();
  return $editRow;
 }
 
  public function isAlreadyRelated($user_id, $parent_id)
 {
  $stmt = $this->db->prepare("SELECT count(*) FROM user_relationships WHERE user_id=:user_id AND parent_id=:parent_id ORDER BY id ASC");
  $stmt->execute(array(":user_id"=>$user_id, ":parent_id"=>$parent_id));
  $editRow=$stmt->fetchColumn();
  return $editRow > 0;
 }
 
  public function numMainGuardian($user_id)
 {
  $stmt = $this->db->prepare("SELECT count(*) FROM user_relationships WHERE user_id=:user_id AND is_guardian=1 ORDER BY id ASC");
  $stmt->execute(array(":user_id"=>$user_id));
  $editRow=$stmt->fetchColumn();
  return $editRow;
 }
 
}