<?php 
  require_once '../../common.php';

  // 根据邮箱获取用户部分信息
  function getData() {  
    $arr = array('status'=>'failed'); 
    if(empty($_GET['email'])){       
      return $arr;
    }     
    $email = $_GET['email'];
    // 打开数据连接
    $conn = mysqli_connect(BX_DB_HOST, BX_DB_USER, BX_DB_PASSWORD, BX_DB_NAME);
    // 判断数据库是否连接成功
    if($conn){        
      // 查询数据库中的邮箱，因为是唯一的，limit 1：数据库只要找到，就不会再去找     
      if($result = mysqli_query($conn, "select * from users where email='{$email}' limit 1;")){     
        if($user = mysqli_fetch_assoc($result)) {// 找到的数据              
          $arr['status'] = 'success';
          $arr['email'] = $user['email'];
          $arr['avatar'] = $user['avatar'];
          $arr['nickname'] = $user['nickname'];
        }  
        mysqli_free_result($result);         
      }
      mysqli_close($conn); 
    }
    return $arr;
  }

  $arr = getData(); 

  if(empty($_GET['callback'])){    
    header('Content-Type: application/json');    
    echo json_encode($arr);
    return;
  } 

  header('Content-Type: application/javascript');  
  $json = json_encode($arr);
  echo $_GET['callback']."({$json})";
