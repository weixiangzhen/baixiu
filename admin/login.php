<?php 
  // 定义的数据库常量
  require_once '../common.php';

  // 给用户找一个箱子，有就用之前的，没有就创建新的，  
  // session_start();
  
  // 如果用户重新打开登录页面，则判断session是不是存在，存在就删除
  /*if(isset($_SESSION['cur_login_user'])){
    // unset($_SESSION['isLogin']);
    // echo '已删除';
    session_destroy();
  }*/
  bx_destoryCurUser();

  function login(){
    // 校验
    if(empty($_POST['email'])) {
      $GLOBALS['err_msg'] = '邮箱不能为空！';
      return;
    }

    if(empty($_POST['password'])) {
      $GLOBALS['err_msg'] = '密码不能为空！';
      return;
    }

    $email = $_POST['email'];
    $password = $_POST['password'];
    if(!preg_match('/[\w\-]+\@[\w\-]+\.[\w\-]+/', $email)){
      $GLOBALS['err_msg'] = '无效的Email！';
      return;
    }
    // 持久化    
    // 打开数据连接
    $conn = mysqli_connect(BX_DB_HOST, BX_DB_USER, BX_DB_PASSWORD, BX_DB_NAME);
    // 判断数据库是否连接成功
    if(!$conn){
      $GLOBALS['err_msg'] = '连接服务器失败';     
    } else {  
      do {
        // 查询数据库中的邮箱，因为是唯一的，limit 1：数据库只要找到，就不会再去找
        $result = mysqli_query($conn, "select * from users where email='{$email}' limit 1;");

        if(!$result){
          $GLOBALS['err_msg'] = '登录失败，请重试！';
          break;
        }      
        // 找到的数据
        $user = mysqli_fetch_assoc($result);
        if(!$user) {// 邮箱不存在         
          $GLOBALS['err_msg'] = '邮箱或密码错误！';      
          break;
        }
        // 对密码加密后判断，md5已经不安全了，
        $password = md5($password);
        if($password !== $user['password']){// 密码错误          
          $GLOBALS['err_msg'] = '邮箱或密码错误！';
          break;
        }
        // 添加session，登录成功标识符
        // $_SESSION['cur_login_user'] = $user;
        bx_setCurUser($user);
        // 关闭数据库
        mysqli_close($conn);
        // 响应，跳转到首页
        header('Location: /admin/index.php');
      } while(0);  
      mysqli_close($conn);
    }  
      
  }

  // 判断 请求的方式是不是post
  if($_SERVER['REQUEST_METHOD'] === 'POST'){
    login();
  }
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Sign in &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <link rel="stylesheet" href="/static/assets/vendors/animate/animate.css">
</head>
<body>
  <div class="login">
    <!-- novalidate：取消浏览器的校验功能 -->
    <!-- autocomplete：关闭客户端自动完成的功能 -->
    <form class="login-wrap<?php echo isset($err_msg) ? ' animated shake' : '' ?>" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" novalidate autocomplete="off">
      <img class="avatar" src="/static/assets/img/default.png">
      <!-- 有错误信息时展示 -->
      <?php if(isset($err_msg)) : ?>
      <div class="alert alert-danger">
        <strong>错误！</strong> <?php echo $err_msg ?>
      </div>
      <?php endif ?>
      <div class="form-group">
        <label for="email" class="sr-only">邮箱</label>
        <input id="email" name="email" type="email" class="form-control" 
                value="<?php echo isset($_POST['email']) ? $_POST['email'] : '' ?>" placeholder="邮箱" autofocus>
      </div>
      <div class="form-group">
        <label for="password" class="sr-only">密码</label>
        <input id="password" name="password" type="password" class="form-control" placeholder="密码">
      </div>
      <button class="btn btn-primary btn-block">登 录</button>
    </form>
  </div>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <!-- <script>
    function getdata(data){
      console.log(data);
    }
  </script>
  <script src="/admin/get-user.php?callback=getdata"></script> -->
  <script>    
    $(function($){ 
      // 旧邮箱的值
      let oldValue = '';     
      $('#email').blur(function(){   
        // 当前邮箱的值
        const value = $(this).val();
        if(oldValue === value){
          return;
        }
        oldValue = value;
        // 邮箱正则表达式
        const regexp = /^[\w\-]+\@[\w\-]+\.[\w\-]+$/g;

        const $avatar = $('.avatar'); 
        const defaultImg = '/static/assets/img/default.png';         
        // 忽略文本框为空或非邮箱格式的数据
        if(!value || !regexp.test(value)) {  
          if($avatar.attr('src') === defaultImg)
            return;          
          $avatar.fadeOut(function(){
            $(this).on('load', function(){
              $(this).fadeIn();
            }).attr('src', defaultImg);
          });
          return;
        } 
        
        $.get('/admin/api/avatar.php', {email: value}, function(data){
          if(data.status === 'success' && data.avatar) {
            // if(data.avatar !== $('.avatar').attr('src'))
            $avatar.fadeOut(function(){
              $(this).on('load', function(){
                $(this).fadeIn();
              }).attr('src', data.avatar);
            });
            return;
          }       
          if($avatar.attr('src') === defaultImg)
            return;     
          $avatar.fadeOut(function(){           
            $(this).on('load', function(){
              $(this).fadeIn();
            }).attr('src', defaultImg);
          });
        });
      });
    });
  </script>
</body>
</html>

<!-- const xhr = new XMLHttpRequest();
        xhr.open('get', '/admin/api/avatar.php?email=' + value);
        xhr.onreadystatechange = function(){
          if(this.readyState !== 4) return;
          const obj = JSON.parse(this.responseText);
          if(obj.status === 'failed') {
            $('.avatar').attr('src', '/static/assets/img/default.png');
            return;
          }
          
          if(obj.avatar)
            $('.avatar').attr('src', obj.avatar);
          else
            $('.avatar').attr('src', '/static/assets/img/default.png');        
        }        
        xhr.send(null); -->

