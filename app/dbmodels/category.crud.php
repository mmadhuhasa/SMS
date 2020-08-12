<?php
require_once("Constants.php");
class CategoryCRUD
{
 private $db;
 
 function __construct($DB_con)
 {
  $this->db = $DB_con;
 }
 
 public function create($title, $description, $is_published)
 {
  $response = array();
  $response["error"] = true;   
  try
  {
   $stmt = $this->db->prepare("INSERT INTO categories(title, description, is_published)");
   $stmt->bindparam(":title", $title);
   $stmt->bindparam(":description", $description);
   $stmt->bindparam(":is_published", $is_published);
   if($stmt->execute()){
	   $response["error"] = false;  
	   $response["id"] = $this->db->lastInsertId();  
       $response["code"] = INSERT_SUCCESS; 
	   /************* UPLOAD IMAGE **************/
	   try{
		   if(!empty($image)){
			$path = "images/categories/".$response["id"].".jpg";
		    $actualpath = $path;
			
			file_put_contents("uploads/".$path, base64_decode($image));
			$stmt2=$this->db->prepare("UPDATE categories SET image=:image
             WHERE id=:id");
   
            $stmt2->bindparam(":image",$actualpath);
            $stmt2->bindparam(":id",$response["id"]);
            $res = $stmt2->execute();
			}
	   }catch (Exception $e) {}
	   /**************************************/
   }else{
	   $response["error"] = true;  
       $response["code"] = INSERT_FAILURE; 
   }
   return $response;
  }
  catch(PDOException $e)
  {
   echo $e->getMessage();  
   return $response;
  }
  
 }
 
 
 public function update($id, $title, $description, $is_published)
 {
  try
  {
   $stmt=$this->db->prepare("UPDATE categories SET title=:title, 
                description=:description,
				is_published=:is_published
                WHERE id=:id");
   
   $stmt->bindparam(":title",$title);
   $stmt->bindparam(":description",$description);
   $stmt->bindparam(":is_published",$is_published);
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
  $stmt = $this->db->prepare("SELECT * FROM categories WHERE id=:id");
  $stmt->execute(array(":id"=>$id));
  $editRow=$stmt->fetch(PDO::FETCH_ASSOC);
  return $editRow;
 }
 
  public function getNameByID($id)
 {
  $stmt = $this->db->prepare("SELECT title FROM categories WHERE id=:id");
  $stmt->execute(array(":id"=>$id));
  $result = $stmt->fetchColumn();
  return $result;
 }
 
  public function getSubNameByID($id)
 {
  $stmt = $this->db->prepare("SELECT name FROM sub_categories WHERE id=:id");
  $stmt->execute(array(":id"=>$id));
  $result = $stmt->fetchColumn();
  return $result;
 }
 
 public function doNameExists($title)
 { 
  $sql = "SELECT count(*) FROM categories WHERE title=:title";
  $stmt = $this->db->prepare($sql); 
  $stmt->execute(array(":title"=>$title)); 
  $number_of_rows = $stmt->fetchColumn(); 
  return $number_of_rows;
 }
 
  public function getAllCategories()
 {
  $stmt = $this->db->prepare("SELECT * FROM categories ORDER BY id ASC");
  $stmt->execute();
  $editRow=$stmt->fetchAll();
  return $editRow;
 }
 
  public function getSuperCatName($category_id)
 { 
  $sql = "SELECT title FROM categories WHERE id IN(SELECT category_id FROM sub_categories WHERE category_id=:category_id)";
  $stmt = $this->db->prepare($sql); 
  $stmt->execute(array(":category_id"=>$category_id)); 
  $number_of_rows = $stmt->fetchColumn(); 
  return $number_of_rows;
 }
 
 public function getAllSubCategories()
 {
  $stmt = $this->db->prepare("SELECT * FROM sub_categories ORDER BY id ASC");
  $stmt->execute();
  $editRow=$stmt->fetchAll();
  return $editRow;
 }
 
  public function getNumSubCats($category_id)
 { 
  $sql = "SELECT count(*) FROM sub_categories WHERE category_id=:category_id";
  $stmt = $this->db->prepare($sql); 
  $stmt->execute(array(":category_id"=>$category_id)); 
  $number_of_rows = $stmt->fetchColumn(); 
  return $number_of_rows;
 }
 
 public function getAllTestimonials()
 {
  $stmt = $this->db->prepare("SELECT * FROM testimonials");
  $stmt->execute();
  $editRow=$stmt->fetchAll();
  return $editRow;
 }
 
 public function getSubCatsByCategory($category_id)
 {
  $stmt = $this->db->prepare("SELECT * FROM sub_categories WHERE category_id=:category_id ORDER BY id ASC LIMIT 100");
  $stmt->execute(array(":category_id"=>$category_id));
  $editRow=$stmt->fetchAll();
  return $editRow;
 }
 
 public function delete($id)
 {
  $stmt = $this->db->prepare("DELETE FROM categories WHERE id=:id");
  $stmt->bindparam(":id",$id);
  $stmt->execute();
  return true;
 }
}