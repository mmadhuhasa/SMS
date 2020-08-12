<?php
require_once("Constants.php");
class UserOffsetCRUD
{
 private $db;
 
 function __construct($DB_con)
 {
  $this->db = $DB_con;
 }
 
 
  public function createOffset($user_id, $offset_amount, $project_id, $price, $certificate, $qcode, $status, $note, $date_created)
 {
  $response = array();	
  $response["error"] = true;   
  $response["msg"] = "";
  $response["code"] = INSERT_FAILURE; 
  try
  {
   $stmt = $this->db->prepare("INSERT INTO user_offsets(user_id, offset_amount, project_id, price, certificate, qcode, status, note, date_created) VALUES(:user_id, :offset_amount, :project_id, :price, :certificate, :qcode, :status, :note, :date_created)");
   $stmt->bindparam(":user_id",$user_id);
   $stmt->bindparam(":offset_amount",$offset_amount);
   $stmt->bindparam(":project_id",$project_id);
   $stmt->bindparam(":price",$price);
   $stmt->bindparam(":certificate",$certificate);
   $stmt->bindparam(":qcode",$qcode);
   $stmt->bindparam(":status",$status);
   $stmt->bindparam(":note",$note);
   $stmt->bindparam(":date_created",$date_created);
   if($stmt->execute()){
	   $response["error"] = false;  
	   $response["id"] = $this->db->lastInsertId();  
       $response["code"] = INSERT_SUCCESS; 
   }else{
	   $response["error"] = true;  
       $response["code"] = INSERT_FAILURE; 
   }
   return $response;
  }
  catch(PDOException $e)
  {
   $response["msg"] = $e->getMessage();  
   $response["error"] = true;  
   $response["code"] = INSERT_FAILURE; 
   return $response;
  }
 }
 

 public function getID($id)
 {
  $stmt = $this->db->prepare("SELECT * FROM user_offsets WHERE id=:id");
  $stmt->execute(array(":id"=>$id));
  $editRow=$stmt->fetch(PDO::FETCH_ASSOC);
  return $editRow;
 }
 
  public function getByQCode($qcode)
 {
  $stmt = $this->db->prepare("SELECT * FROM user_offsets WHERE qcode=:qcode");
  $stmt->execute(array(":qcode"=>$qcode));
  $editRow=$stmt->fetch(PDO::FETCH_ASSOC);
  return $editRow;
 }
  
   public function getAllUserOffsets($status)
 {
  $stmt = $this->db->prepare("SELECT * FROM user_offsets WHERE status=:status");
  $stmt->execute(array(":status"=>$status));
  $editRow=$stmt->fetchAll();
  return $editRow;
 }
 
 public function updateStatus($id, $status)
 {	 
  $response = array();
  try
  {
   $stmt=$this->db->prepare("UPDATE user_offsets SET status=:status
             WHERE id=:id ");
   $stmt->bindparam(":status", $status);
   $stmt->bindparam(":id",$id);
   $stmt->execute();
   if($stmt->execute()){
   $response["error"] = false; 
   $response["message"] = "Offset record status changed successfully."; 
  }
  else{
  $response["error"] = true; 
	 $response["message"] = "Fails to change Offset record status."; 
  }
   return $response; 
  }
  catch(PDOException $e)
  {
   $response["error"] = true; 
   $response["message"] = $e->getMessage();
   //$response["message"] = "An error occurred while processing your request. Try again.";
   return $response;
  }
 }

  public function getNumAllOffsets($status)
 {
  $stmt = $this->db->prepare("SELECT count(*) FROM user_offsets WHERE status=:status");
  $stmt->execute(array(":status"=>$status));
  $editRow=$stmt->fetchColumn();
  return $editRow;
 }
 
 public function getNumAllMyOffsets($user_id, $status)
 {
  $stmt = $this->db->prepare("SELECT count(*) FROM user_offsets WHERE user_id=:user_id AND status=:status");
  $stmt->execute(array(":user_id"=>$user_id, ":status"=>$status));
  $editRow=$stmt->fetchColumn();
  return $editRow;
 }
 
   public function getAllMyOffsets($user_id, $status)
 {
  $stmt = $this->db->prepare("SELECT * FROM user_offsets WHERE user_id=:user_id AND status=:status");
  $stmt->execute(array(":user_id"=>$user_id, ":status"=>$status));
  $editRow=$stmt->fetchAll();
  return $editRow;
 }
 
 
  public function getCompletedOffsetsForProject($project_id)
 {
  $status = "Completed";     
  $stmt = $this->db->prepare("SELECT * FROM user_offsets WHERE project_id=:project_id AND status=:status");
  $stmt->execute(array(":project_id"=>$project_id, ":status"=>$status));
  $editRow=$stmt->fetchAll();
  return $editRow;
 }
 
  public function getNumCompletedOffsetsForProject($project_id)
 {
  $status = "Completed";     
  $stmt = $this->db->prepare("SELECT count(*) FROM user_offsets WHERE project_id=:project_id AND status=:status");
  $stmt->execute(array(":project_id"=>$project_id, ":status"=>$status));
  $editRow=$stmt->fetchColumn();
  return $editRow;
 }
 
  public function getSumTotalOffsetForProject($project_id)
 {
  $status = "Completed";     
  $stmt = $this->db->prepare("SELECT SUM(price) FROM user_offsets WHERE project_id=:project_id AND status=:status");
  $result = $stmt->execute(array(":project_id"=>$project_id, ":status"=>$status));
  $rows = $stmt->fetchColumn();
 if(empty($rows)){
      return 0;
  }
  return $rows;
 }
 
 public function getSumTotalOffsets()
 {
  $status = "Completed";     
  $stmt = $this->db->prepare("SELECT SUM(price) FROM user_offsets WHERE status=:status");
  $result = $stmt->execute(array(":status"=>$status));
  $rows = $stmt->fetchColumn();
  if(empty($rows)){
      return 0;
  }
  return $rows;
 }
 
  public function getSumTotalOffsetsForUser($user_id)
 {
  $status = "Completed";     
  $stmt = $this->db->prepare("SELECT SUM(price) FROM user_offsets WHERE user_id=:user_id AND status=:status");
  $result = $stmt->execute(array(":user_id"=>$user_id, ":status"=>$status));
  $rows = $stmt->fetchColumn();
   if(empty($rows)){
      return 0;
  }
  return $rows;
 }
 
 
 /************* CARBON AMOUNT SUM **************/
  public function getSumCo2AmountInTotalOffsets()
 {
  $status = "Completed";     
  $stmt = $this->db->prepare("SELECT SUM(offset_amount) FROM user_offsets WHERE status=:status");
  $result = $stmt->execute(array(":status"=>$status));
  $rows = $stmt->fetchColumn();
   if(empty($rows)){
      return 0;
  }
  return $rows;
 }
 
  public function getSumCo2AmountForUser($user_id)
 {
  $status = "Completed";     
  $stmt = $this->db->prepare("SELECT SUM(offset_amount) FROM user_offsets WHERE user_id=:user_id AND status=:status");
  $result = $stmt->execute(array(":user_id"=>$user_id, ":status"=>$status));
  $rows = $stmt->fetchColumn();
  if(empty($rows)){
      return 0;
  }
  return $rows;
 }
  /************* CARBON AMOUNT SUM **************/
 
  public function doesItemExists($id)
 {
  $stmt = $this->db->prepare("SELECT count(*) FROM user_offsets WHERE id=:id");
  $result = $stmt->execute(array(":id"=>$id));
  $rows = $stmt->fetchColumn();
  return $rows > 0;
 }
 
 
 /**********************************/
 
 public function generateCode(){
		require_once("CodeGenerator.php");
		 $generator = new CouponGenerator;
         $tokenLength = 16;
         $voucherNum = $generator->generate($tokenLength);
		if($this->isCodeValid($voucherNum) > 0){
			generateCode();
		}
		return $voucherNum;
	}
	
	public function isCodeValid($qcode) {
        $stmt = $this->db->prepare("SELECT id from user_offsets WHERE qcode = ?");
        $stmt->execute("s", $qcode);
         $editRow=$stmt->fetch(PDO::FETCH_ASSOC);
        return $editRow > 0;
    }
    
 public function isQCodeExists($qcode)
 {
  $stmt = $this->db->prepare("SELECT id FROM user_offsets WHERE qcode=:qcode");
  $result = $stmt->execute(array(":qcode"=>$qcode));
  $rows = $stmt->fetchAll();
  $num_rows = count($rows);
  return $num_rows > 0;
 }
 
  public function getIDByQCode($qcode)
 {
  $stmt = $this->db->prepare("SELECT id FROM user_offsets WHERE qcode=:qcode");
  $stmt->execute(array(":qcode"=>$qcode));
  $result = $stmt->fetchColumn();
  return $result;
 }
 
   public function getQCodeByID($id)
 {
  $stmt = $this->db->prepare("SELECT qcode FROM user_offsets WHERE id=:id");
  $stmt->execute(array(":id"=>$id));
  $result = $stmt->fetchColumn();
  return $result;
 }    
    
}