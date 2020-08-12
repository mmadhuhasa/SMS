<?php
require_once("Constants.php");
class UtilCRUD
{
 private $db;
 
 function __construct($DB_con)
 {
  $this->db = $DB_con;
 }
 
  public function getFormattedDate($date_created){
	   $month = date("F", strtotime($date_created));
       $this_date = new DateTime($date_created);
       $day = $this_date->format('d');
	   $year = $this_date->format('Y');	
	   return $day." ".$month." ".$year;
 }
 
   public function validateEmail($email)
 {
  return true;
 }
 
   public function getAllCountries()
 {
  $stmt = $this->db->prepare("SELECT * FROM country");
  $stmt->execute();
  $editRow=$stmt->fetchAll();
  return $editRow;
 }
 
    public function createNewUsername($length){
    $characters = "abcdefghijklmnopqrstuvwxyz0123456789";
    $name = "";

    for($i = 0; $i < $length; $i++){
        $name .= $characters[mt_rand(0,strlen($characters) - 1)];
        }
		
		if($this->isUserNameExists($name) > 0){
			createNewUsername($length);
		}
		return $name;
    }
	
	 public function createFileName(){
	$length = 8;
    $characters = "abcdefghijklmnopqrstuvwxyz0123456789";
    $name = "";

    for($i = 0; $i < $length; $i++){
        $name .= $characters[mt_rand(0,strlen($characters) - 1)];
        }
		
		if($this->isUserNameExists($name) > 0){
			createNewUsername($length);
		}
		return $name;
    }
 
 public function isUserNameExists($user_name)
 {
  $stmt = $this->db->prepare("SELECT id FROM users WHERE user_name=:user_name");
  $result = $stmt->execute(array(":user_name"=>$user_name));
  $rows = $stmt->fetchAll(); // assuming $result == true
  $num_rows = count($rows);
  return $num_rows > 0;
 }
 
 public function generateApiKey() {
        return md5(uniqid(rand(), true));
    }
	
	private function getIntervalFromSeconds($init) {
		 $days = floor($init / (24*60*60));
		 $hours = floor(($init / (24*60*60))%24);
         $minutes = floor(($init / 60) % 60);
         $seconds = $init % 60;
		 if($days > 0){
			 if($days >= 30){
				 $months = floor($days /30);
				 return $months." months";
			 }else{
				return $days." days";
			 }
		 }else{
			 if($hours > 0){
				 if($hours >= 1){
					  return $hours." hour";
				 }else{
					  return $hours." hours";
				 }
			 }else{
				 if($minutes >= 1){
					 return $minutes." minutes";
				 }else{
					 return "few seconds";
				 }
			 }
		 }
    }
	
	public function getFormalDate($date_created){
    try{
    $month  = date("F", strtotime($date_created));
    $this_date = new DateTime($date_created);
    $day = $this_date->format('d');
	$year = $this_date->format('Y');
	return $day." ".$month." ".$year;
}catch(Exception $e){
			return $date_created;
		}
}
	
	function getTimeDifference($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}
 
}