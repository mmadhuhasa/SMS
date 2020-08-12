<?php
require_once("Constants.php");
class AssignmentSubmissionCRUD
{
 private $db;
 function __construct($DB_con)
 {
  $this->db = $DB_con;
 }
 
 public function create($assignment_id, $user_id, $title, $content , $status, $qcode)   
 {
  $response = array();	
  $response["error"] = true;  
  $image = "";  
  try
  {
   $stmt = $this->db->prepare("INSERT INTO assignment_submissions(assignment_id, user_id, title, content ,status, qcode)VALUES(:assignment_id, :user_id, :title, :content ,:status, :qcode)");
   $stmt->bindparam(":assignment_id", $assignment_id);
   $stmt->bindparam(":user_id", $user_id);
   $stmt->bindparam(":title", $title);
   $stmt->bindparam(":content", $content);
   $stmt->bindparam(":status", $status);
   $stmt->bindparam(":qcode", $qcode);
   if($stmt->execute()){
	   $response["error"] = false;  
	   $response["id"] = $this->db->lastInsertId();  
    $response["code"] = INSERT_SUCCESS; 
	   $response["message"] = "Your entry has been submitted successfully.";
	   /************* UPLOAD IMAGE *************
	   try{
		   if(!empty($image)){
			$path = "images/projects/".$response["id"].".jpg";
		    $actualpath = $path;
			
			file_put_contents("uploads/".$path, base64_decode($image));
			$stmt2=$this->db->prepare("UPDATE projects SET image=:image
             WHERE id=:id");
   
            $stmt2->bindparam(":image",$actualpath);
            $stmt2->bindparam(":id",$response["id"]);
            $res = $stmt2->execute();
			}
	   }catch (Exception $e) {
		   $response["error"] = true;   
		   $response["message"] = $e->getMessage();
           return $response;
	   }
	   *************************************/
   }else{
	   $response["error"] = true;   	   
       $response["code"] = INSERT_FAILURE; 
	   $response["message"] = "Failed to submit assignment. Please try again.";
   }
   return $response;
  }
  catch(PDOException $e)
  {
   $response["error"] = true;   	   
   $response["code"] = INSERT_FAILURE;       
   $response["message"] = "Exception => ".$e->getMessage();
   return $response;
  }
  
 }
 
 
 public function update($id, $assignment_id, $user_id, $title, $content , $date_updated, $status)
 {
  try
  {
   $stmt=$this->db->prepare("UPDATE assignment_submissions SET assignment_id=:assignment_id, 
                                                    user_id=:user_id,
													                          title=:title,
                                                    content=:content,
                                                    date_updated=:date_updated, status=:status WHERE id=:id");
   $stmt->bindparam(":assignment_id", $assignment_id);
   $stmt->bindparam(":user_id", $user_id);
   $stmt->bindparam(":title", $title);
   $stmt->bindparam(":content", $content);
   $stmt->bindparam(":date_updated", $date_updated);
   $stmt->bindparam(":status", $status);
   $stmt->bindparam(":id",$id);
   $stmt->execute();
   return true; 
  }
  catch(PDOException $e)
  {
   //echo $e->getMessage(); 
   return false;
  }
  return false;
 }
 
 public function getID($id)
 {
  $stmt = $this->db->prepare("SELECT * FROM assignment_submissions WHERE id=:id");
  $stmt->execute(array(":id"=>$id));
  $editRow=$stmt->fetch(PDO::FETCH_ASSOC);
  return $editRow;
 }
 
  public function getNameByID($id)
 {
  $stmt = $this->db->prepare("SELECT title FROM assignment_submissions WHERE id=:id");
  $stmt->execute(array(":id"=>$id));
  $result = $stmt->fetchColumn();
  return $result;
 }
 
  public function getAllSubmissions()
 {
  $stmt = $this->db->prepare("SELECT * FROM assignment_submissions ORDER BY id DESC");
  $stmt->execute();
  $editRow=$stmt->fetchAll();
  return $editRow;
 }
 
 public function getAllSubmissionsForAssignment($assignment_id)
 {
  $stmt = $this->db->prepare("SELECT * FROM assignment_submissions WHERE assignment_id=:assignment_id ORDER BY id DESC");
  $stmt->execute(array(":assignment_id"=>$assignment_id));
  $editRow=$stmt->fetchAll();
  return $editRow;
 }
 
  public function doesIDExist($id)
 {
  $stmt = $this->db->prepare("SELECT count(*) FROM assignment_submissions WHERE id=:id");
  $result = $stmt->execute(array(":id"=>$id));
  $rows = $stmt->fetchColumn();
  return $rows > 0;
 }
 
 /**********************/
   public function generateCode(){
		require_once("CodeGenerator.php");
		 $generator = new CouponGenerator;
         $tokenLength = 10;
         $voucherNum = $generator->generate($tokenLength);
		if($this->isCodeValid($voucherNum) > 0){
			generateCode();
		}
		return $voucherNum;
	}
	
  public function isCodeValid($qcode) {
        $stmt = $this->db->prepare("SELECT id from assignment_submissions WHERE qcode = :qcode");
  $result = $stmt->execute(array(":qcode"=>$qcode));
         $editRow=$stmt->fetch(PDO::FETCH_ASSOC);
        return $editRow > 0;
    }
	
 public function isQCodeExists($qcode)
 {
  $stmt = $this->db->prepare("SELECT id FROM assignment_submissions WHERE qcode=:qcode");
  $result = $stmt->execute(array(":qcode"=>$qcode));
  $rows = $stmt->fetchAll();
  $num_rows = count($rows);
  return $num_rows > 0;
 }
 
  public function getIDByQCode($qcode)
 {
  $stmt = $this->db->prepare("SELECT id FROM assignment_submissions WHERE qcode=:qcode");
  $stmt->execute(array(":qcode"=>$qcode));
  $result = $stmt->fetchColumn();
  return $result;
 }
 
  public function getQCodeByID($qcode)
 {
  $stmt = $this->db->prepare("SELECT qcode FROM assignment_submissions WHERE id=:id");
  $stmt->execute(array(":id"=>$id));
  $result = $stmt->fetchColumn();
  return $result;
 }
 
  public function getByQCode($qcode)
 {
  $stmt = $this->db->prepare("SELECT * FROM assignment_submissions WHERE qcode=:qcode");
  $stmt->execute(array(":qcode"=>$qcode));
  $editRow=$stmt->fetch(PDO::FETCH_ASSOC);
  return $editRow;
 }
 
  public function delete($id)
 {
  $stmt = $this->db->prepare("DELETE FROM assignment_submissions WHERE id=:id");
  $stmt->bindparam(":id",$id);
   return $stmt->execute();
  //return true;
 }
 
}