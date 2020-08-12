<?php
require_once("Constants.php");
class SiteSettingsCRUD
{
 private $db;
 
 function __construct($DB_con)
 {
  $this->db = $DB_con;
 }
 
 public function update($id, $name, $link, $about, $address, $latitude, $longitude, $phone, $email, $facebook_link, $twitter_link)
 {
  try
  {
   $stmt=$this->db->prepare("UPDATE site_settings SET name=:name, 
   link =:link,
                about=:about,
				address=:address,
				latitude=:latitude,
				longitude=:longitude,
				phone=:phone,
				email=:email,
				facebook_link=:facebook_link,
				twitter_link=:twitter_link 
                WHERE id=:id");
   
   $stmt->bindparam(":name",$name);
   $stmt->bindparam(":link",$link);
   $stmt->bindparam(":about",$about);
   $stmt->bindparam(":address",$address);
   $stmt->bindparam(":latitude",$latitude);
   $stmt->bindparam(":longitude",$longitude);
   $stmt->bindparam(":phone",$phone);
   $stmt->bindparam(":email",$email);
   $stmt->bindparam(":facebook_link",$facebook_link);
   $stmt->bindparam(":twitter_link",$twitter_link);
   $stmt->bindparam(":id",$id);
   $stmt->execute();
	   
   return true; 
  }
  catch(PDOException $e)
  {
   echo $e->getMessage(); 
   return false;
  }
 }
 
 public function getID($id)
 {
  $stmt = $this->db->prepare("SELECT * FROM site_settings WHERE id=:id");
  $stmt->execute(array(":id"=>$id));
  $editRow=$stmt->fetch(PDO::FETCH_ASSOC);
  return $editRow;
 }
 
 public function updateImage($id, $image)
 {	 
  $response = array();	
  $response["error"] = true; 
  $response["message"] = "No Image found.";
   /************* UPLOAD IMAGE **************/
	   try{
		   if(!empty($image)){
			$path = "images/services/".$id.".jpg";
		    $actualpath = $path;
			
			//file_put_contents("http://localhost/ARMORBEAREARSPORT/".$path, base64_decode($image));
			
			file_put_contents("../../".$path, base64_decode($image));
			
			$stmt2=$this->db->prepare("UPDATE site_settings SET logo=:logo
             WHERE id=:id");
            $stmt2->bindparam(":logo",$actualpath);
            $stmt2->bindparam(":id",$id);
            $res = $stmt2->execute();
			if($res){
				 $response["error"] = false; 
				 $response["message"] = "Image uploaded => ".$actualpath; 
			}
			}else{
				 $response["error"] = true; 
				 $response["message"] = "Upload a valid image."; 
			}
	   }catch (Exception $e) {
		    $response["error"] = true; 
		   $response["message"] = "Could not upload Image.".$e->getMessage();
	   }
	   /**************************************/
   return $response; 
 }
 
}