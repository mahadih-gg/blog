<?php

session_start();

require('connect.php');

function dd($value) //to be deleted
{
echo"<pre>",print_r($value,true), "<pre>";
die();
}


function executeQuery($sql,$data)
{
   global $conn;
  $stmt=$conn->prepare($sql);
  $values=array_values($data);
  $types= str_repeat('s',count($values));
  $stmt->bind_param($types,...$values);
  $stmt->execute();
  return $stmt;
}

function selectAll($table,$conditions=[])
{
global $conn;
$sql="SELECT *FROM $table";
  if(empty($conditions)){
                $stmt=$conn->prepare($sql);
                $stmt->execute();
                $records=$stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                return $records;
} else{
   //$sql="SELECT * FROM $table WHERE username='Adarsh' AND admin=1";
   $i=0;
    foreach($conditions as $key => $value){
    if($i===0){
     $sql= $sql . " WHERE $key=?";
   }else{
     $sql=$sql . " AND $key=?";
 }
  $i++;
 }
 
                $stmt=executeQuery($sql,$conditions);
                $records=$stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                return $records;
 }

 }

function selectOne($table,$conditions)
{
global $conn;
$sql="SELECT *FROM $table";
  
  
   $i=0;
    foreach($conditions as $key => $value){
    if($i===0){
     $sql= $sql . " WHERE $key=?";
   }else{
     $sql=$sql . " AND $key=?";
 }
  $i++;
 }
     //$sql="SELECT * FROM users WHERE admin=0 AND username='Adarsh' LIMIT 1";
    $sql= $sql . " LIMIT 1 ";
    $stmt=executeQuery($sql,$conditions);
    $records=$stmt->get_result()->fetch_assoc();
    return $records;
 

 }

function create($table,$data){
  global $conn;
        //sql="INSERT INTO users SET username=?,admin=?,email=?,password=?"
$sql=" INSERT INTO $table SET ";

$i=0;
    foreach($data as $key => $value){
    if($i===0){
     $sql= $sql . " $key=?";
   }else{
     $sql=$sql . ", $key=?";
 }
  $i++;
 }
 
$stmt=executeQuery($sql,$data);
$id=$stmt->insert_id;
return $id;

}

function update($table,$id,$data){
  global $conn;
        //sql="UPDATE users SET username=?,admin=?,email=?,password=? WHERE id=?"
$sql=" UPDATE $table SET ";

$i=0;
    foreach($data as $key => $value){
    if($i===0){
     $sql= $sql . " $key=?";
   }else{
     $sql=$sql . ", $key=?";
 }
  $i++;
 }
 
$sql=$sql . " WHERE id=? ";
$data['id']=$id;
$stmt=executeQuery($sql,$data);

return $stmt->affected_rows;

}

function delete($table,$id){
  global $conn;
        //sql="DELETE FROM users WHERE id=?"
$sql=" DELETE FROM $table WHERE id=? ";

$stmt=executeQuery($sql,['id'=>$id]);
return $stmt->affected_rows;

}



//$id=delete('users',2);
//dd($id);


//$data=[
//'admin' =>1,
//'username'=> "Adarsh Basil Sunny",
//'email'=> "aby456@gmail.com",
//'password'=> "aby"
//
//];
//$id=update('users',2,$data);


function getPublishedPosts()
{
  global $conn;
  //SELECT * FROM posts where published=1; 
  $sql = "SELECT p.*, u.username FROM posts AS p JOIN users AS u ON p.user_id=u.id WHERE p.published=?";
  
  $stmt=executeQuery($sql, ['published' => 1]);
  $records=$stmt->get_result()->fetch_all(MYSQLI_ASSOC);
  return $records;
}


function getPostsByTopicId($topic_id)
{
  global $conn;
  //SELECT * FROM posts where published=1; 
  $sql = "SELECT p.*, u.username FROM posts AS p JOIN users AS u ON p.user_id=u.id WHERE p.published=? AND topic_id=?";
  
  $stmt=executeQuery($sql, ['published' => 1, 'topic_id' => $topic_id]);
  $records=$stmt->get_result()->fetch_all(MYSQLI_ASSOC);
  return $records;
}


function searchPosts($term)
{ 
  $match = '%' . $term . '%';
  global $conn;
  //SELECT * FROM posts where published=1; 
  $sql = "SELECT 
  p.*, u.username 
  FROM posts AS p 
  JOIN users AS u 
  ON p.user_id=u.id 
  WHERE p.published=?
  AND p.title LIKE ? OR p.body LIKE ?";
  
  $stmt=executeQuery($sql, ['published' => 1, 'title' => $match, 'body' => $match]);
  $records=$stmt->get_result()->fetch_all(MYSQLI_ASSOC);
  return $records;
}