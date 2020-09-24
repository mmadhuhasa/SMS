<?php
require_once("Constants.php");
class PostCRUD
{
  private $db;
 function __construct($DB_con)
 {
  $this->db = $DB_con;
 }
 
  public function isIDExists($id)
 {
  $stmt = $this->db->prepare("SELECT id FROM posts WHERE id=:id");
  $result = $stmt->execute(array(":id"=>$id));
  $rows = $stmt->fetchColumn();
  $num_rows = count($rows);
  return $rows > 0;
 }
 
  public function getID($id)
 {
  $stmt = $this->db->prepare("SELECT * FROM posts WHERE id=:id");
  $stmt->execute(array(":id"=>$id));
  $editRow=$stmt->fetch(PDO::FETCH_ASSOC);
  return $editRow;
 }

 public function create($post_title, $post_type, $author_id, $class_id, $subject_id, $topic_id, $post_description, $post_body, $post_status, $post_qcode, $date_created, $link)   
 {
    $response = array();	
    $response["error"] = true;  
    $image = "";  
    try{
     $stmt = $this->db->prepare(" INSERT INTO posts(title, post_type, author_id, class_id, subject_id, topic_id, status, qcode, description, body, link, date_created)VALUES(:title, :post_type, :author_id, :class_id, :subject_id, :topic_id, :status, :qcode, :description, :body, :link, :date_created) ");

     $stmt->bindparam(":title", $post_title);
     $stmt->bindparam(":post_type", $post_type);
     $stmt->bindparam(":author_id", $author_id);
     $stmt->bindparam(":class_id", $class_id);
     $stmt->bindparam(":subject_id", $subject_id);
     $stmt->bindparam(":topic_id", $topic_id);
	   $stmt->bindparam(":description", $post_description);  
	   $stmt->bindparam(":body", $post_body);
     $stmt->bindparam(":status", $post_status);
     $stmt->bindparam(":qcode", $post_qcode);
     $stmt->bindparam(":date_created", $date_created);
     $stmt->bindparam(":link", $link);

     if($stmt->execute()){
    	   $response["error"] = false;  
    	   $response["id"] = $this->db->lastInsertId();  
           $response["code"] = INSERT_SUCCESS; 
    	   $response["message"] = "Post has been created successfully.";
       }
       else{
    	   $response["error"] = true;   	   
           $response["code"] = INSERT_FAILURE; 
    	   $response["message"] = "Failed to create post. Please try again.";
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

  public function getAllPosts($status = "")
  {
  	if(empty($status)){
  	  $stmt = $this->db->prepare("SELECT * FROM posts ORDER BY id DESC");
      $stmt->execute();
      $editRow=$stmt->fetchAll();
      return $editRow;
  	}else{
  	  $stmt = $this->db->prepare("SELECT * FROM posts WHERE status =:status ORDER BY id DESC");
      $stmt->execute(array(":status"=>$status));
      $editRow=$stmt->fetchAll();
      return $editRow;
  	}
  }

  public function getFilteredPosts($status = "", $text_search = "", $post_class, $post_subject, $post_extra)
  {
    if($post_extra == 1){   // Filtered also with Most Relevant Posts
      $stmt = $this->db->prepare("SELECT id FROM posts WHERE status =:status AND IF(".$post_class." != 0, class_id =".$post_class.", class_id > 0) AND IF(".$post_subject." != 0, subject_id =".$post_subject.", subject_id > 0) AND CONCAT(title, ' ',description) LIKE '%".$text_search."%' ORDER BY id DESC");  
    } 
    else if ($post_extra == 2) {   // Filtered also with Most Popular Posts
      $stmt = $this->db->prepare("SELECT p.id as id FROM posts p, post_views v WHERE p.id=v.post_id AND p.status =:status AND IF(".$post_class." != 0, p.class_id =".$post_class.", p.class_id > 0) AND IF(".$post_subject." != 0, p.subject_id =".$post_subject.", p.subject_id > 0) AND CONCAT(p.title, ' ',p.description) LIKE '%".$text_search."%' GROUP BY v.post_id ORDER BY count(v.post_id) DESC LIMIT 40");
    }
    else if ($post_extra == 3) {   // Filtered also with Most Commented Posts
      $stmt = $this->db->prepare("SELECT p.id as id FROM posts p, post_comments c WHERE p.id=c.post_id AND p.status =:status AND IF(".$post_class." != 0, p.class_id =".$post_class.", p.class_id > 0) AND IF(".$post_subject." != 0, p.subject_id =".$post_subject.", p.subject_id > 0) AND CONCAT(p.title, ' ',p.description) LIKE '%".$text_search."%' GROUP BY c.post_id ORDER BY count(c.post_id) DESC LIMIT 40");
    }
    else{   // Filtered only by class , subject and text search 
      $stmt = $this->db->prepare("SELECT id FROM posts WHERE status =:status AND IF(".$post_class." != 0, class_id =".$post_class.", class_id > 0) AND IF(".$post_subject." != 0, subject_id =".$post_subject.", subject_id > 0) AND CONCAT(title, ' ',description) LIKE '%".$text_search."%' ORDER BY id DESC");
    }

    $stmt->execute(array(":status"=>$status));
    $editRow=$stmt->fetchAll();
    return $editRow; 
  }

  //why this
 public function getAllPostsByStatus($author_id)
  {
    $status = 'Active'; 
    $stmt = $this->db->prepare("SELECT * FROM posts WHERE author_id =:author_id AND status =:status ORDER BY id DESC");
    $stmt->execute(array(":author_id"=>$author_id, ":status"=>$status));

    $editRow=$stmt->fetchAll();
    return $editRow;
  }

  public function getPostsForUser($author_id)
  {
    $stmt = $this->db->prepare("SELECT * FROM posts WHERE author_id=:author_id ORDER BY id DESC");
    $stmt->execute(array(":author_id"=>$author_id));
    $editRow=$stmt->fetchAll();
    return $editRow;
  }
  
  public function getPostsForSchool($school_id)
  {
    $stmt = $this->db->prepare("SELECT * FROM posts WHERE author_id IN(SELECT user_id FROM users WHERE school_id =:school_id) AND status =:status ORDER BY id DESC");
    $stmt->execute(array(":school_id"=>$school_id, ":status"=>$status));
    $editRow=$stmt->fetchAll();
    return $editRow;
  }


 public function update($post_id, $post_title, $post_type, $author_id, $class_id, $subject_id, $topic_id, $post_description, $post_body, $post_status, $post_qcode, $date_created, $link)   
 {
    $response = array();  
    $response["error"] = true;  
    $image = "";  
    try{

       $stmt = $this->db->prepare("UPDATE posts SET title=:title, post_type=:post_type, author_id=:author_id, class_id=:class_id, subject_id=:subject_id, topic_id=:topic_id, status=:status, qcode=:qcode, description=:description, body=:body, link=:link, date_created=:date_created WHERE id=:id");

       $stmt->bindparam(":title", $post_title);
       $stmt->bindparam(":post_type", $post_type);
       $stmt->bindparam(":author_id", $author_id);
       $stmt->bindparam(":class_id", $class_id);
       $stmt->bindparam(":subject_id", $subject_id);
       $stmt->bindparam(":topic_id", $topic_id);
       $stmt->bindparam(":description", $post_description);  
       $stmt->bindparam(":body", $post_body);
       $stmt->bindparam(":status", $post_status);
       $stmt->bindparam(":qcode", $post_qcode);
       $stmt->bindparam(":date_created", $date_created);
       $stmt->bindparam(":link", $link);
       $stmt->bindparam(":id", $post_id);
 

       if($stmt->execute()){
           $response["error"] = false;  
           // $response["id"] = $this->db->lastInsertId();  
           // $response["code"] = INSERT_SUCCESS; 
           $response["message"] = "Post has been updated successfully.";

        }
        else{
             $response["error"] = true;        
             // $response["code"] = INSERT_FAILURE; 
             $response["message"] = "Failed to update post. Please try again.";
        }
        return $response;
    }
    catch(PDOException $e)
    {
        $response["error"] = true;       
        // $response["code"] = INSERT_FAILURE;       
        $response["message"] = $e->getMessage();
        return $response;
    }
  
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
        $stmt = $this->db->prepare("SELECT id from posts WHERE qcode = :qcode");
        $result = $stmt->execute(array(":qcode"=>$qcode));
        $editRow=$stmt->fetch(PDO::FETCH_ASSOC);
        return $editRow > 0;
    }
	
 public function isQCodeExists($qcode)
 {
  $stmt = $this->db->prepare("SELECT id FROM posts WHERE qcode=:qcode");
  $result = $stmt->execute(array(":qcode"=>$qcode));
  $rows = $stmt->fetchAll();
  $num_rows = count($rows);
  return $num_rows > 0;
 }
 
  public function getIDByQCode($qcode)
 {
  $stmt = $this->db->prepare("SELECT id FROM posts WHERE qcode=:qcode");
  $stmt->execute(array(":qcode"=>$qcode));
  $result = $stmt->fetchColumn();
  return $result;
 }
 
  public function getQCodeByID($qcode)
 {
  $stmt = $this->db->prepare("SELECT qcode FROM posts WHERE id=:id");
  $stmt->execute(array(":id"=>$id));
  $result = $stmt->fetchColumn();
  return $result;
 }
 
  public function getByQCode($qcode)
 {
  $stmt = $this->db->prepare("SELECT * FROM posts WHERE qcode=:qcode");
  $stmt->execute(array(":qcode"=>$qcode));
  $editRow=$stmt->fetch(PDO::FETCH_ASSOC);
  return $editRow;
 }
 
 
  public function getAllPostTypes()
  {
    $stmt = $this->db->prepare("SELECT * FROM post_types ORDER BY id DESC");
    $stmt->execute();
    $editRow=$stmt->fetchAll();
    return $editRow;
  }


  public function deletePost($qcode)
  {
      $stmt = $this->db->prepare("DELETE FROM posts WHERE qcode=:qcode");
      $stmt->bindparam(":qcode",$qcode);
      // $status = 'Pending';
      // $stmt = $this->db->prepare("UPDATE posts SET status=:status WHERE qcode=:qcode");
      // $stmt->bindparam(":status",$status);
      // $stmt->bindparam(":qcode",$qcode);

      $stmt->execute();
      return true;
  }

  public function getRecentPosts()
  {
    $stmt = $this->db->prepare("SELECT p.id as id, p.title as title, p.description as description, i.image as image, p.qcode as qcode FROM posts p, post_images i WHERE p.id=i.post_id GROUP BY p.id ORDER BY p.id DESC LIMIT 5");
    $stmt->execute();
    $editRow=$stmt->fetchAll();
    return $editRow;
  }

  public function getLatestImages()
 {
  $stmt = $this->db->prepare("SELECT p.id as id, p.qcode as qcode, i.image as image FROM posts p, post_images i WHERE p.id=i.post_id GROUP BY p.id ORDER BY p.id DESC LIMIT 8");
  $stmt->execute();
  $editRow=$stmt->fetchAll();
  return $editRow;
 }


  public function getAllPostsCount($status)
 {
    $stmt = $this->db->prepare("SELECT count(*) FROM posts WHERE status=:status"); 
    $stmt->execute(array(":status"=>$status));

    $editRow=$stmt->fetchColumn(); 
    return $editRow;
 }



}  