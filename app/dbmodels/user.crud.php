<?php
//namespace App\Models\User;
require_once("Constants.php");
class UserCRUD
{
 private $db;
 
 function __construct($DB_con)
 {
  $this->db = $DB_con;
 }
  
 public function register($first_name, $last_name, $user_name, $address, $city, $country, $dob, $mobile, $email, $password, $role_id, $school_id, $date_created, $status, $api_key, $ip_address)
 {
  $response = array();	
  $response["error"] = true;
  $response["code"] = INSERT_FAILURE;
  try
  {
   if(!empty($mobile) && $this->isMobileRegistered($mobile)){  
        $response["error"] = true;  
	   $response["message"] = "This mobile number is already associated with an account."; 
       $response["code"] = ALREADY_EXIST;
   }
   
   if(!$this->isEmailRegistered($email)){         
   $stmt = $this->db->prepare("INSERT INTO users(first_name, last_name, user_name, address, city, country, mobile, email, passhash, role_id, school_id, date_created, status, api_key, ip_address) VALUES(:first_name, :last_name, :user_name, :address, :city, :country, :mobile, :email, :passhash, :role_id, :school_id, :date_created, :status, :api_key, :ip_address)");
   $stmt->bindparam(":first_name", $first_name);
   $stmt->bindparam(":last_name", $last_name);
    $stmt->bindparam(":user_name", $user_name);
   $stmt->bindparam(":address", $address);
   $stmt->bindparam(":city", $city);
   $stmt->bindparam(":country", $country);
   $stmt->bindparam(":mobile", $mobile);
   $stmt->bindparam(":email", $email);
   $stmt->bindparam(":passhash", $password);
   $stmt->bindparam(":role_id", $role_id);
   $stmt->bindparam(":school_id", $school_id);
   $stmt->bindparam(":date_created", $date_created);
   $stmt->bindparam(":status", $status);
   $stmt->bindparam(":api_key", $api_key);  
   $stmt->bindparam(":ip_address", $ip_address);  
   if($stmt->execute()){
	   $response["error"] = false;  
	   $response["id"] = $this->db->lastInsertId();  
       $response["code"] = INSERT_SUCCESS; 
	   $response["userName"] = $user_name; 
	   $response["message"] = "Congrats! You are now a registered member of JV Partner. "; 
   }else{
	   $response["error"] = true;  
	   $response["message"] = "Oops! An error occurred while processing your registration request. Please try again."; 
       $response["code"] = INSERT_FAILURE; 
   }
   }
   else{
	   $response["error"] = true;  
	   $response["message"] = "Looks like you are already registered. Please login using your email and password to access your account."; 
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
 
  public function getImageByID($id)
 {
  $stmt = $this->db->prepare("SELECT user_image FROM users WHERE id='$id'");
  $stmt->execute();
  $rows = $stmt->fetchColumn(); 
  return $rows;
 }
 
 public function getID($id)
 {
  $stmt = $this->db->prepare("SELECT * FROM users WHERE id=:id");
  $stmt->execute(array(":id"=>$id));
  $editRow=$stmt->fetch(PDO::FETCH_ASSOC);
  return $editRow;
 }
 
 public function getUserBymobile($mobile)
 {
  $stmt = $this->db->prepare("SELECT * FROM users WHERE mobile=:mobile");
  $stmt->execute(array(":mobile"=>$mobile));
  $editRow=$stmt->fetch(PDO::FETCH_ASSOC);
  return $editRow;
 }
 
  public function getUserByEmail($email)
 {
  $stmt = $this->db->prepare("SELECT * FROM users WHERE email=:email");
  $stmt->execute(array(":email"=>$email));
  $editRow=$stmt->fetch(PDO::FETCH_ASSOC);
  return $editRow;
 }
 
   public function getUserByAPIKey($api_key)
 {
  $stmt = $this->db->prepare("SELECT * FROM users WHERE api_key=:api_key");
  $stmt->execute(array(":api_key"=>$api_key));
  $editRow=$stmt->fetch(PDO::FETCH_ASSOC);
  return $editRow;
 }
 
  public function getUserIDByUsername($user_name)
 {
  $stmt = $this->db->prepare("SELECT id FROM users WHERE user_name=:user_name");
  $result = $stmt->execute(array(":user_name"=>$user_name));
  $rows = $stmt->fetchColumn();
  return $rows;
 }
 
  public function getByUsername($user_name)
 {
  $stmt = $this->db->prepare("SELECT * FROM users WHERE user_name=:user_name");
  $stmt->execute(array(":user_name"=>$user_name));
  $editRow=$stmt->fetch(PDO::FETCH_ASSOC);
  return $editRow;
 }
 
  public function doesIDExist($id)
 {
  //$stmt = $this->db->prepare("SELECT id FROM users WHERE id=:id");
  $stmt = $this->db->prepare("SELECT count(*) FROM users WHERE id=:id");
   $stmt->bindparam(":id",$id);
  $stmt->execute();
  return true;
 }
 
  public function doesUserNameExist($user_name)
 {
  $stmt = $this->db->prepare("SELECT id FROM users WHERE user_name=:user_name");
  $result = $stmt->execute(array(":user_name"=>$user_name));
  $rows = $stmt->fetchAll(); // assuming $result == true
  $num_rows = count($rows);
  return $num_rows > 0;
 }
 
 public function isValidApiKey($api_key)
 {
  $stmt = $this->db->prepare("SELECT id FROM users WHERE api_key=:api_key");
  $result = $stmt->execute(array(":api_key"=>$api_key));
  $rows = $stmt->fetchColumn();
  return $rows > 0;
 }
 
   public function doesEmailExist($email)
 {
  $stmt = $this->db->prepare("SELECT id FROM users WHERE email=:email");
  $result = $stmt->execute(array(":email"=>$email));
  $rows = $stmt->fetchColumn();
  return $rows > 0;
 }
 
 public function getIDBymobile($mobile)
 {
  $stmt = $this->db->prepare("SELECT id FROM users WHERE mobile = :mobile");
  $stmt->execute(array(":mobile"=>$mobile));
  $rows = $stmt->fetchColumn(); 
  return $rows;
 }
 
  public function getAPIKey($id)
 {
  $stmt = $this->db->prepare("SELECT api_key FROM users WHERE id = :id");
  $stmt->execute(array(":id"=>$id));
  $rows = $stmt->fetchColumn(); 
  return $rows;
 }
 
   public function getIDByAPIKey($api_key)
 {
  $stmt = $this->db->prepare("SELECT id FROM users WHERE api_key = :api_key");
  $stmt->execute(array(":api_key"=>$api_key));
  $rows = $stmt->fetchColumn(); 
  return $rows;
 }
 
  public function isMobileRegistered($mobile)
 {
  $stmt = $this->db->prepare("SELECT id FROM users WHERE mobile=:mobile");
  $result = $stmt->execute(array(":mobile"=>$mobile));
  $rows = $stmt->fetchAll(); // assuming $result == true
  $num_rows = count($rows);
  return $num_rows > 0;
 }
 
  public function isEmailRegistered($email)
 {
  $stmt = $this->db->prepare("SELECT id FROM users WHERE email=:email");
  $result = $stmt->execute(array(":email"=>$email));
   $rows = $stmt->fetchColumn();
  if(!empty($rows)){
	  return true;
  }
  return false;
 }
 
 
 public function getmobile($id)
 {
  $stmt = $this->db->prepare("SELECT mobile FROM users WHERE id = :id");
  $stmt->execute(array(":id"=>$id));
  $rows = $stmt->fetchColumn(); 
  return $rows;
 }
 
  public function getEmail($id)
 {
  $stmt = $this->db->prepare("SELECT email FROM users WHERE id = :id");
  $stmt->execute(array(":id"=>$id));
  $rows = $stmt->fetchColumn(); 
  return $rows;
 }
 
 public function getRoleID($id)
 {
  $stmt = $this->db->prepare("SELECT role_id FROM users WHERE id = :id");
  $stmt->execute(array(":id"=>$id));
  $rows = $stmt->fetchColumn(); 
  return $rows;
 }
 
  public function getRoleByAPIKey($api_key)
 {
  $stmt = $this->db->prepare("SELECT role_id FROM users WHERE api_key = :api_key");
  $stmt->execute(array(":api_key"=>$api_key));
  $rows = $stmt->fetchColumn(); 
  return $rows;
 }
  
 public function getRoleName($id)
 {
  $stmt = $this->db->prepare("SELECT name FROM user_roles WHERE id = :id");
  $stmt->execute(array(":id"=>$id));
  $rows = $stmt->fetchColumn(); 
  return $rows;
 }
 
 public function getUserName($id)
 {
  $stmt = $this->db->prepare("SELECT user_name FROM users WHERE id = :id");
  $stmt->execute(array(":id"=>$id));
  $rows = $stmt->fetchColumn(); 
  return $rows;
 }
 
 public function getNameByID($id)
 {
  $stmt = $this->db->prepare("SELECT first_name FROM users WHERE id = :id");
  $stmt->execute(array(":id"=>$id));
  $rows = $stmt->fetchColumn(); 
  return $rows;
 }
 
  public function getSchoolIDByID($id)
 {
  $stmt = $this->db->prepare("SELECT school_id FROM users WHERE id = :id");
  $stmt->execute(array(":id"=>$id));
  $rows = $stmt->fetchColumn(); 
  return $rows;
 }
 
 
 /************ USER STATS FUNCTIONS ************
  public function getCountriesUsersSummary()
 {
  $stmt = $this->db->prepare("SELECT country, COUNT(*) as numUsers FROM users GROUP BY country ORDER BY numUsers DESC LIMIT 10");
  $stmt->execute();
  $rows = $stmt->fetchAll(); 
  return $rows;
 }
 
  public function getCountriesUserTypesSummary($role_id)
 {
//SELECT country, role_id, COUNT(*) as numUsers FROM users WHERE role_id = 3 GROUP BY country
  $stmt = $this->db->prepare("SELECT country, COUNT(*) as numUsers FROM users WHERE role_id =:role_id GROUP BY country ORDER BY numUsers DESC LIMIT 10");
  $stmt->execute(array(":role_id"=>$role_id));
  $rows = $stmt->fetchAll(); 
  return $rows;
 }
 *********************/
 
 public function getNameByEmail($email)
 {
  $stmt = $this->db->prepare("SELECT first_name FROM users WHERE email = :email");
  $stmt->execute(array(":email"=>$email));
  $rows = $stmt->fetchColumn(); 
  
  $stmt = $this->db->prepare("SELECT last_name FROM users WHERE email = :email");
  $stmt->execute(array(":email"=>$email));
  $rows2 = $stmt->fetchColumn(); 
  
  return $rows." ".$rows2;
 }
 
   public function getAllRoles()
 {
  $stmt = $this->db->prepare("SELECT * FROM user_roles ORDER BY id DESC");
  $result = $stmt->execute();
  $editRow=$stmt->fetchAll();
  return $editRow;
 }
 
  public function getUsersByRole($role_id)
 {
  $stmt = $this->db->prepare("SELECT * FROM users WHERE role_id=:role_id ORDER BY id DESC");
  $result = $stmt->execute(array(":role_id"=>$role_id));
  $editRow=$stmt->fetchAll();
  return $editRow;
 }
 
  public function update($id, $first_name, $last_name, $user_name, $address, $city, $country, $pincode, $dob, $mobile, $email, $status, $date_updated)
 {
  $response = array();	
  $response["error"] = true; 
  $response["message"] = "Profile update request is received successfully."; 
  $response["note"] = ""; 
  
  try
  {
   $stmt=$this->db->prepare("UPDATE users SET first_name=:first_name, 
   last_name=:last_name,
   user_name=:user_name,
                address=:address, 
                city=:city, 
                country=:country,
				pincode=:pincode,
				dob=:dob,
				mobile=:mobile,
				email=:email,
				status=:status,
				date_updated=:date_updated 
             WHERE id=:id");
   
   $stmt->bindparam(":first_name", $first_name);
   $stmt->bindparam(":last_name", $last_name);
   $stmt->bindparam(":user_name", $user_name);
   $stmt->bindparam(":address", $address);
   $stmt->bindparam(":city", $city);
   $stmt->bindparam(":country", $country);
   $stmt->bindparam(":pincode", $pincode);
   $stmt->bindparam(":dob", $dob);
   $stmt->bindparam(":mobile", $mobile);
   $stmt->bindparam(":email", $email);
   $stmt->bindparam(":status", $status);
   $stmt->bindparam(":date_updated", $date_updated);
   $stmt->bindparam(":id",$id);
   $stmt->execute();
   $response["error"] = false;
   $response["message"] = "Profile updated successfully."; 
   return $response; 
  }
  catch(PDOException $e)
  {
   //echo $e->getMessage(); 
   //return false;
   $response["error"] = true; 
   $response["message"] = $e->getMessage(); 
   return $response;
  }
 }
 
   public function isUsernameExists($user_name)
 {
  $stmt = $this->db->prepare("SELECT id FROM users WHERE user_name=:user_name");
  $result = $stmt->execute(array(":user_name"=>$user_name));
  $rows = $stmt->fetchColumn();
  $num_rows = count($rows);
  return $rows > 0;
 }
 
  public function isEmailExists($email)
 {
  $stmt = $this->db->prepare("SELECT id FROM users WHERE email=:email");
  $result = $stmt->execute(array(":email"=>$email));
  $rows = $stmt->fetchColumn();
  return $rows > 0;
 }
 
   public function isEmailExistExcept($email, $id)
 {
  $stmt = $this->db->prepare("SELECT id FROM users WHERE email=:email AND id!=:id");
  $result = $stmt->execute(array(":email"=>$email, ":id"=>$id));
  $rows = $stmt->fetchColumn();
  return $rows > 0;
 }
 
   public function isUsernameExistExcept($user_name, $id)
 {
  $stmt = $this->db->prepare("SELECT id FROM users WHERE user_name=:user_name AND id!=:id");
  $result = $stmt->execute(array(":user_name"=>$user_name, ":id"=>$id));
  $rows = $stmt->fetchColumn();
  return $rows > 0;
 }
 
  public function isUsernameTaken($user_name, $id)
 {
  $stmt = $this->db->prepare("SELECT id FROM users WHERE user_name=:user_name AND id !=:id");
  $result = $stmt->execute(array(":user_name"=>$user_name, ":id"=>$id));
  $rows = $stmt->fetchColumn();
  $num_rows = count($rows);
  return $rows > 0;
 }
 
   public function getNumAllUsers($role_id)
 {
  if($role_id == 0){
	  $stmt = $this->db->prepare("SELECT count(*) FROM users WHERE role_id != 1");
	  $stmt->execute();
  }else{
	  $stmt = $this->db->prepare("SELECT count(*) FROM users WHERE role_id=:role_id");
	  $stmt->execute(array(":role_id"=>$role_id));
  }
  $numRow = $stmt->fetchColumn();
  return $numRow;
 }
 
  public function getNumAllUsersIn($school_id)
 {
  $stmt = $this->db->prepare("SELECT count(*) FROM users WHERE school_id=:school_id");
  $stmt->execute(array(":school_id"=>$school_id));
  $numRow = $stmt->fetchColumn();
  return $numRow;
 }
 
  public function getNumUsersIn($school_id, $role_id)
 {
  $stmt = $this->db->prepare("SELECT count(*) FROM users WHERE school_id=:school_id AND role_id=:role_id");
  $stmt->execute(array(":school_id"=>$school_id, ":role_id"=>$role_id));
  $numRow = $stmt->fetchColumn();
  return $numRow;
 }
 
    public function getNumUsers($role_id, $status="Active")
 {
  if($role_id == 0){
	  $stmt = $this->db->prepare("SELECT count(*) FROM users WHERE role_id != 1");
	  $stmt->execute();
  }else{
	  $stmt = $this->db->prepare("SELECT count(*) FROM users WHERE role_id=:role_id AND status=:status");
	  $stmt->execute(array(":role_id"=>$role_id, ":status"=>$status));
  }
  $numRow = $stmt->fetchColumn();
  return $numRow;
 }
 
 public function getLatitude($id)
 {
  $stmt = $this->db->prepare("SELECT latitude FROM users WHERE id='$id'");
  $stmt->execute();
  $rows = $stmt->fetchColumn(); 
  return $rows;
 }
 
 public function getLongitude($id)
 {
  $stmt = $this->db->prepare("SELECT longitude FROM users WHERE id='$id'");
  $stmt->execute();
  $rows = $stmt->fetchColumn(); 
  return $rows;
 }
 
 public function updateImage($id, $user_image)
 {	 
  $response = array();	
  $response["error"] = true; 
  $response["message"] = "Profile photo update request is received successfully."; 
   if(!empty($user_image)){
	   //$user_image = "images/partners/".$user_image;
			$stmt2=$this->db->prepare("UPDATE users SET user_image=:user_image
             WHERE id=:id");
            $stmt2->bindparam(":user_image",$user_image);
            $stmt2->bindparam(":id",$id);
            $res = $stmt2->execute();
			if($res){
				 $response["error"] = false; 
				 $response["message"] = "Image uploaded successfully."; 
				 return $response; 
			}else{
				 $response["error"] = true; 
				 $response["message"] = "Please try again."; 
				 return $response; 
			}
			}
 }
 
 public function delete($id)
 {
  $stmt = $this->db->prepare("DELETE FROM users WHERE id=:id");
  $stmt->bindparam(":id",$id);
  $stmt->execute();
  return true;
 }
 
  public function getAllUsers($role_id, $school_id)
 {
 if($school_id == 0 && $role_id ==0){
	  $stmt = $this->db->prepare("SELECT * FROM users ORDER BY id DESC");
  $stmt->execute();
  $editRow=$stmt->fetchAll();
  return $editRow;
 }else{
  if($school_id == 0){
  $stmt = $this->db->prepare("SELECT * FROM users WHERE role_id =:role_id ORDER BY id DESC");
  $stmt->bindparam(":role_id",$role_id);
  $stmt->execute();
  $editRow=$stmt->fetchAll();
  return $editRow;
 }else{
  $stmt = $this->db->prepare("SELECT * FROM users WHERE school_id =:school_id ORDER BY id DESC");
  $stmt->bindparam(":school_id",$school_id);
  $stmt->execute();
  $editRow=$stmt->fetchAll();
  return $editRow;
 }
 }
 }
 
   public function getAllUsersExcept($user_id)
 {
  $stmt = $this->db->prepare("SELECT * FROM users WHERE role_id != 1 AND id != :user_id ORDER BY id DESC");
  $stmt->bindparam(":user_id",$user_id);
  $editRow=$stmt->fetchAll();
  return $editRow;
 }
 
 public function updateEmail($id, $email)
 {	 
   $stmt=$this->db->prepare("UPDATE users SET email=:email
             WHERE id=:id ");
   $stmt->bindparam(":email", $email);
   $stmt->bindparam(":id",$id);
  if( $stmt->execute()){
	  return true;
  }else{
	   return false;
  }
 }
 
  public function updatePassword($id, $passhash)
 {	 
   $stmt=$this->db->prepare("UPDATE users SET passhash=:passhash
             WHERE id=:id ");
   $stmt->bindparam(":passhash", $passhash);
   $stmt->bindparam(":id",$id);
  if( $stmt->execute()){
	  return true;
  }else{
	   return false;
  }
 }
 
 public function updateLastActive($id, $last_active)
 {	 
   $stmt=$this->db->prepare("UPDATE users SET last_active=:last_active
             WHERE id=:id ");
   $stmt->bindparam(":last_active", $last_active);
   $stmt->bindparam(":id",$id);
  if( $stmt->execute()){
	  return true;
  }else{
	   return false;
  }
 }
 
 public function updateStatus($id, $status)
 {	 
  $response = array();
  try
  {
   $stmt=$this->db->prepare("UPDATE users SET status=:status
             WHERE id=:id ");
   $stmt->bindparam(":status", $status);
   $stmt->bindparam(":id",$id);
   $stmt->execute();
	   
   $response["error"] = false; 
   if($status == "Active"){
   $response["message"] = "User account is activated successfully."; 
  }
  else{
	 $response["message"] = "This User account has been blocked."; 
  }
   return $response; 
  }
  catch(PDOException $e)
  {
   $response["error"] = true; 
   //$response["message"] = $e->getMessage();
   $response["message"] = "An error occurred while processing your request. Try again.";
   return $response;
  }
 }
 
  public function updateProfileStatus($id, $tagline)
 {
  $response = array();
  try
  {
   $stmt=$this->db->prepare("UPDATE users SET tagline=:tagline WHERE id=:id");
   $stmt->bindparam(":tagline", $tagline);
   $stmt->bindparam(":id",$id);
   $stmt->execute();	   
   $response["error"] = false; 
   $response["message"] = "Your profile status has been updated now."; 
   return $response; 
  }
  catch(PDOException $e)
  {
   $response["error"] = true; 
   $response["message"] = "An error occurred while updated your status. Please try again.";
   return $response;
  }
 }
 
 public function updateProfileDescription($id, $description)
 {	 
  $response = array();
  try
  {
   $stmt=$this->db->prepare("UPDATE users SET description=:description WHERE id=:id ");	 
   $stmt->bindparam(":description", $description);
   $stmt->bindparam(":id",$id);
   $stmt->execute();	   
   $response["error"] = false; 
   $response["message"] = "Your profile intro has been updated."; 
   return $response; 
  }
  catch(PDOException $e)
  {
   $response["error"] = true; 
   $response["message"] = "An error occurred while updating your profile intro. Please try again.";
   return $response;
  }
 }
 
 
 public function updateOnlineStatus($id, $online_status)
 {	 
  $response = array();
  try
  {
   $stmt=$this->db->prepare("UPDATE users SET online_status=:online_status
             WHERE id=:id ");
   $stmt->bindparam(":online_status", $online_status);
   $stmt->bindparam(":id",$id);
   $stmt->execute();
	   
   $response["error"] = false; 
   if($online_status == "Online"){
   $response["message"] = "You are now available for work."; 
  }
  else{
	 $response["message"] = "You are off work now. Get online when you are ready."; 
  }
   return $response; 
  }
  catch(PDOException $e)
  {
   $response["error"] = true; 
   $response["message"] = "An error occurred while processing your request. Try again.";
   return $response;
  }
 }
 
 
 
 public function updateLastSeen($id, $online_status)
 {	 
  $response = array();
  try
  {
   $stmt=$this->db->prepare("UPDATE users SET last_seen=:last_seen
             WHERE id=:id ");
   $stmt->bindparam(":last_seen", $last_seen);
   $stmt->bindparam(":id",$id);
   $stmt->execute();
   $response["error"] = false; 
   $response["message"] = "Your last session has been saved."; 
   return $response; 
  }
  catch(PDOException $e)
  {
   $response["error"] = true; 
   $response["message"] = "An error occurred while processing your request. Try again.";
   return $response;
  }
 }
 
 public function checkLogin($email, $password) {
    require_once 'PassHash.php';   
	$stmt = $this->db->prepare("SELECT passhash FROM users WHERE email='$email'");
    $stmt->execute();
    $password_hash = $stmt->fetchColumn(); 

        if ($stmt->rowCount() > 0) {
            if (PassHash::check_password($password_hash, $password)) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

/**************** APP USAGE ******************/
 public function updateUsage($api_key)
 {
   $stmt=$this->db->prepare("UPDATE app_usage SET count=count+1
             WHERE api_key=:api_key");
   $stmt->bindparam(":api_key", $api_key);
  if( $stmt->execute()){
	  return true;
  }else{
	   return false;
  }
 }
 
 public function addToUsage($api_key, $signature)
 {
  $response = array();	
  $response["error"] = true;  
  try
  {
   $stmt = $this->db->prepare("INSERT INTO app_usage(signature, api_key) VALUES(:signature, :api_key)");
   $stmt->bindparam(":signature", $signature);
   $stmt->bindparam(":api_key", $api_key);
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
   $response["error"] = true;  
   $response["code"] = INSERT_FAILURE; 	  
   $response["message"] = $e->getMessage();  
   return $response;
  }
 }
 
  public function getUsage($api_key)
 {
  $stmt = $this->db->prepare("SELECT COUNT(id) FROM app_usage WHERE api_key = :api_key");
  $stmt->execute(array(":api_key"=>$api_key));
  $counts = $stmt->fetchColumn(); 
  if(empty($counts) || $counts <= 0){
	  return 0;
  }else{
	   return $counts;
  }
 }
 
 public function getUsageBySchool($school_id)
 {
  $stmt = $this->db->prepare("SELECT COUNT(id) FROM app_usage WHERE api_key IN(SELECT api_key FROM users WHERE school_id = :school_id)");
  $stmt->execute(array(":school_id"=>$school_id));
  $counts = $stmt->fetchColumn(); 
  if(empty($counts) || $counts <= 0){
	  return 0;
  }else{
	   return $counts;
  }
 }
 /**********************************/	
 
 public function isSchoolAdmin($id, $school_id)
 {
  $stmt = $this->db->prepare("SELECT id FROM users WHERE role_id = 2 AND id=:id AND school_id=:school_id");
  $result = $stmt->execute(array(":id"=>$id, ":school_id"=>$school_id));
  $rows = $stmt->fetchAll();
  $num_rows = count($rows);
  return $num_rows > 0;
 }
 
  public function getFirstSchoolAdmin($school_id)
 {
  $stmt = $this->db->prepare("SELECT * FROM users WHERE role_id = 2 AND school_id = :school_id ORDER BY ID ASC");
  $stmt->execute(array(":school_id"=>$school_id));
  $editRow=$stmt->fetch(PDO::FETCH_ASSOC);
  return $editRow;
 }
 
 public function getNumSchoolAdmins($school_id)
 {
  $stmt = $this->db->prepare("SELECT COUNT(id) FROM users WHERE role_id = 2 AND school_id = :school_id");
  $stmt->execute(array(":school_id"=>$school_id));
  $counts = $stmt->fetchColumn(); 
  if(empty($counts) || $counts <= 0){
	  return 0;
  }else{
	   return $counts;
  }
 }
 
}