<?php
require_once("Constants.php");
class SchoolCRUD
{
 private $db;
 
 function __construct($DB_con)
 {
  $this->db = $DB_con;
 }
 
 public function create($name, $tagline, $address, $city, $country, $phone, $email, $website, $registration_no, $date_created, $qcode, $status)
 {
  $response = array();	
  $response["error"] = true;  
  $image = "";  
  try
  {
   $stmt = $this->db->prepare("INSERT INTO schools(name, tagline, address, city, country, phone, email, website, registration_no, date_created, qcode, status)VALUES(:name, :tagline, :address, :city, :country, :phone, :email, :website, :registration_no, :date_created, :qcode, :status)");
   $stmt->bindparam(":name", $name);
   $stmt->bindparam(":tagline", $tagline);
   $stmt->bindparam(":address", $address);
   $stmt->bindparam(":city", $city);
   $stmt->bindparam(":country", $country);
   $stmt->bindparam(":phone", $phone);
   $stmt->bindparam(":email", $email);
   $stmt->bindparam(":website", $website);
   $stmt->bindparam(":registration_no", $registration_no);
   $stmt->bindparam(":date_created", $date_created);
   $stmt->bindparam(":qcode", $qcode);
   $stmt->bindparam(":status", $status);
   if($stmt->execute()){
	   $response["error"] = false;  
	   $response["id"] = $this->db->lastInsertId();  
       $response["code"] = INSERT_SUCCESS; 
	   $response["message"] = "School profile has been created successfully.";
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
	   $response["message"] = "Failed to create School profile. Please try again.";
   }
   return $response;
  }
  catch(PDOException $e)
  {
 $response["error"] = true;   	   
       $response["code"] = INSERT_FAILURE;       
   $response["message"] = $e->getMessage();
   return $response;
  }
  
 }
 
 
 public function update($id, $name, $tagline, $address, $city, $country, $pincode, $phone, $email, $website, $registration_no, $status, $date_updated)
 {
  try
  {
   $stmt=$this->db->prepare("UPDATE schools SET name=:name, 
   				tagline=:tagline,
   				address=:address,
   				city=:city,
                country=:country,
				pincode=:pincode,
				phone=:phone,
				email=:email,
				website=:website,
				registration_no=:registration_no,
				status=:status,
				date_updated=:date_updated 
                WHERE id=:id");
   
   $stmt->bindparam(":name",$name);
   $stmt->bindparam(":tagline", $tagline);
   $stmt->bindparam(":address", $address);
   $stmt->bindparam(":city", $city);
   $stmt->bindparam(":country",$country);
    $stmt->bindparam(":pincode",$pincode);
   $stmt->bindparam(":phone",$phone);
   $stmt->bindparam(":email",$email);
   $stmt->bindparam(":website",$website);
   $stmt->bindparam(":registration_no",$registration_no);
   $stmt->bindparam(":status",$status);
   $stmt->bindparam(":date_updated",$date_updated);
   $stmt->bindparam(":id",$id);
   $stmt->execute();
	   
   return true; 
  }
  catch(PDOException $e)
  {
   //echo $e->getMessage(); 
   return false;
  }
 }
 
 public function getID($id)
 {
  $stmt = $this->db->prepare("SELECT * FROM schools WHERE id=:id");
  $stmt->execute(array(":id"=>$id));
  $editRow=$stmt->fetch(PDO::FETCH_ASSOC);
  return $editRow;
 }
 
  public function getNameByID($id)
 {
  $stmt = $this->db->prepare("SELECT name FROM schools WHERE id=:id");
  $stmt->execute(array(":id"=>$id));
  $result = $stmt->fetchColumn();
  return $result;
 }
 
    public function isIDExists($id)
 {
  $stmt = $this->db->prepare("SELECT name FROM schools WHERE id=:id");
  $result = $stmt->execute(array(":id"=>$id));
  $rows = $stmt->fetchAll();
  $num_rows = count($rows);
  return $num_rows > 0;
 }
 
  public function getAllSchools()
 {
  $stmt = $this->db->prepare("SELECT * FROM schools ORDER BY id DESC");
  $stmt->execute();
  $editRow=$stmt->fetchAll();
  return $editRow;
 }

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
        $stmt = $this->db->prepare("SELECT id from schools WHERE qcode = :qcode");
  $result = $stmt->execute(array(":qcode"=>$qcode));
         $editRow=$stmt->fetch(PDO::FETCH_ASSOC);
        return $editRow > 0;
    }
	
 public function isQCodeExists($qcode)
 {
  $stmt = $this->db->prepare("SELECT id FROM schools WHERE qcode=:qcode");
  $result = $stmt->execute(array(":qcode"=>$qcode));
  $rows = $stmt->fetchAll();
  $num_rows = count($rows);
  return $num_rows > 0;
 }
 
  public function getIDByQCode($qcode)
 {
  $stmt = $this->db->prepare("SELECT id FROM schools WHERE qcode=:qcode");
  $stmt->execute(array(":qcode"=>$qcode));
  $result = $stmt->fetchColumn();
  return $result;
 }
 
  public function getQCodeByID($qcode)
 {
  $stmt = $this->db->prepare("SELECT qcode FROM schools WHERE id=:id");
  $stmt->execute(array(":id"=>$id));
  $result = $stmt->fetchColumn();
  return $result;
 }
 
  public function getByQCode($qcode)
 {
  $stmt = $this->db->prepare("SELECT * FROM schools WHERE qcode=:qcode");
  $stmt->execute(array(":qcode"=>$qcode));
  $editRow=$stmt->fetch(PDO::FETCH_ASSOC);
  return $editRow;
 }
 
  
  public function getSchoolsUnder($user_id)
 {
  $stmt = $this->db->prepare("SELECT * FROM schools WHERE id IN (SELECT id FROM users WHERE id=:user_id) ORDER BY id ASC");
  $stmt->execute(array(":user_id"=>$user_id));
  $editRow=$stmt->fetchAll();
  return $editRow;
 }
 
 public function delete($id)
 {
  $stmt = $this->db->prepare("DELETE FROM schools WHERE id=:id");
  $stmt->bindparam(":id",$id);
  $stmt->execute();
  return true;
 }
 
 public function updateLogo($id, $image)
 {	 
 $image = "images/schools/".$image;
  $response = array();	
  $response["error"] = true; 
  $stmt2=$this->db->prepare("UPDATE schools SET logo=:logo
             WHERE id=:id");
            $stmt2->bindparam(":logo",$image);
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
 
}