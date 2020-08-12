<?php
require_once("Constants.php");
class AssignmentCRUD
{
 private $db;
 function __construct($DB_con)
 {
  $this->db = $DB_con;
 }
 
 public function create($user_id, $title, $class_id, $subject_id, $description ,$image, $date_submission, $is_published, $qcode)   
 {
  $response = array();	
  $response["error"] = true;  
  $image = "";  
  try
  {
   $stmt = $this->db->prepare("INSERT INTO assignments(user_id, title, class_id, subject_id, description ,image, date_submission, is_published, qcode)VALUES(:user_id, :title, :class_id, :subject_id, :description ,:image, :date_submission, :is_published, :qcode)");
   $stmt->bindparam(":user_id", $user_id);
   $stmt->bindparam(":title", $title);
   $stmt->bindparam(":class_id", $class_id);
   $stmt->bindparam(":subject_id", $subject_id);
   $stmt->bindparam(":description", $description);
   $stmt->bindparam(":image", $image);
   $stmt->bindparam(":date_submission", $date_submission);
   $stmt->bindparam(":is_published", $is_published);
      $stmt->bindparam(":qcode", $qcode);
   if($stmt->execute()){
	   $response["error"] = false;  
	   $response["id"] = $this->db->lastInsertId();  
       $response["code"] = INSERT_SUCCESS; 
	   $response["message"] = "Assignment has been created successfully.";
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
	   $response["message"] = "Failed to create assignment. Please try again.";
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
 
 
 public function update($id ,$title, $class_id, $subject_id, $description ,$image, $date_submission, $is_published)
 {
  try
  {
   $stmt=$this->db->prepare("UPDATE assignments SET title=:title, 
                                                    class_id=:class_id,
													subject_id=:subject_id,
                                                    description=:description,
				                                    image=:image,
													date_submission=:date_submission,
				                                    is_published=:is_published WHERE id=:id");
   
   $stmt->bindparam(":title",$title);
   $stmt->bindparam(":class_id",$class_id);
   $stmt->bindparam(":subject_id",$subject_id);
   $stmt->bindparam(":description",$description);
   $stmt->bindparam(":image",$image);
   $stmt->bindparam(":date_submission",$date_submission);
   $stmt->bindparam(":is_published",$is_published);
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
  $stmt = $this->db->prepare("SELECT * FROM assignments WHERE id=:id");
  $stmt->execute(array(":id"=>$id));
  $editRow=$stmt->fetch(PDO::FETCH_ASSOC);
  return $editRow;
 }
 
  public function getNameByID($id)
 {
  $stmt = $this->db->prepare("SELECT title FROM assignments WHERE id=:id");
  $stmt->execute(array(":id"=>$id));
  $result = $stmt->fetchColumn();
  return $result;
 }
 
  public function getAllAssignments()
 {
  $stmt = $this->db->prepare("SELECT * FROM assignments ORDER BY id DESC");
  $stmt->execute();
  $editRow=$stmt->fetchAll();
  return $editRow;
 }
 

 public function getAllAssignmentsForTeacher($user_id)
 {
  $stmt = $this->db->prepare("SELECT * FROM assignments WHERE user_id=:user_id ORDER BY id DESC");
  $stmt->execute(array(":user_id"=>$user_id));
  $editRow=$stmt->fetchAll();
  return $editRow;
 }

   public function getAllAssignmentsForSchool($school_id)
 {
  $stmt = $this->db->prepare("SELECT * FROM assignments WHERE user_id IN(SELECT id FROM users WWHERE school_id=:school_id) ORDER BY id ASC");
  $stmt->execute(array(":school_id"=>$school_id));
  $editRow=$stmt->fetchAll();
  return $editRow;
 }
 
  public function doesIDExist($id)
 {
  $stmt = $this->db->prepare("SELECT count(*) FROM assignments WHERE id=:id");
  $result = $stmt->execute(array(":id"=>$id));
  $rows = $stmt->fetchColumn();
  return $rows > 0;
 }
 

 
 public function updateImage($id, $image)
 {	 
 $image = "images/assignments/".$image;
  $response = array();	
  $response["error"] = true; 
  $stmt2=$this->db->prepare("UPDATE assignments SET image=:image
             WHERE id=:id");
            $stmt2->bindparam(":image",$image);
            $stmt2->bindparam(":id",$id);
            $res = $stmt2->execute();
			if($res){
				 $response["error"] = false; 
				 $response["message"] = "Image uploaded."; 
			}else{
				 $response["error"] = true; 
				 $response["message"] = "Upload a valid image."; 
			}
   return $response; 
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
        $stmt = $this->db->prepare("SELECT id from assignments WHERE qcode = :qcode");
  $result = $stmt->execute(array(":qcode"=>$qcode));
         $editRow=$stmt->fetch(PDO::FETCH_ASSOC);
        return $editRow > 0;
    }
	
 public function isQCodeExists($qcode)
 {
  $stmt = $this->db->prepare("SELECT id FROM assignments WHERE qcode=:qcode");
  $result = $stmt->execute(array(":qcode"=>$qcode));
  $rows = $stmt->fetchAll();
  $num_rows = count($rows);
  return $num_rows > 0;
 }
 
  public function getIDByQCode($qcode)
 {
  $stmt = $this->db->prepare("SELECT id FROM assignments WHERE qcode=:qcode");
  $stmt->execute(array(":qcode"=>$qcode));
  $result = $stmt->fetchColumn();
  return $result;
 }
 
  public function getQCodeByID($qcode)
 {
  $stmt = $this->db->prepare("SELECT qcode FROM assignments WHERE id=:id");
  $stmt->execute(array(":id"=>$id));
  $result = $stmt->fetchColumn();
  return $result;
 }
 
  public function getByQCode($qcode)
 {
  $stmt = $this->db->prepare("SELECT * FROM assignments WHERE qcode=:qcode");
  $stmt->execute(array(":qcode"=>$qcode));
  $editRow=$stmt->fetch(PDO::FETCH_ASSOC);
  return $editRow;
 }
 
  public function delete($id)
 {
  $stmt = $this->db->prepare("DELETE FROM assignments WHERE id=:id");
  $stmt->bindparam(":id",$id);
   return $stmt->execute();
  //return true;
 }
 
}