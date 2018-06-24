<?php 
  require_once '../common.php';

  if(!bx_isExistCurUser()){    
    header('Location: /admin/login.php');
  }

  // 添加文章
  function addPost(){    
    if(empty($_POST['title']) || empty($_POST['content']) || empty($_POST['slug']) || empty($_FILES['feature']) 
          || empty($_POST['category']) || empty($_POST['created']) || empty($_POST['status'])){
      $GLOBALS['err_msg'] = '请填写完整的表单';
      return;
    }
    
    $title = $_POST['title'];
    $content = $_POST['content'];
    $slug = $_POST['slug'];
    $categoryId = $_POST['category'];
    
    $feature = $_FILES['feature'];  
    $created = date_format(date_create($_POST['created']), "Y-m-d H:i:s"); 
    $status = $_POST['status'];
    
    $featurePath = '/static/uploads/2018/'.uniqid().'.'.pathinfo($feature['name'], PATHINFO_EXTENSION);    
    if(!move_uploaded_file($feature['tmp_name'], '..'.$featurePath)){
      $GLOBALS['err_msg'] = '图片保存失败';
      return;
    }
    $email = bx_getCurUser()['email'];   
    $userId = bx_fetch_one("select id from users where email='{$email}'")['id'];
    
    $b = bx_execture(
      "insert into posts values(NULL,'{$slug}','{$title}','{$featurePath}','{$created}','{$content}',0,0,'{$status}',{$userId},{$categoryId})");
    if(!$b){
      $GLOBALS['err_msg'] = '保存失败！';
      return;
    }
    var_dump($b);
    
    exit();
  }

  if($_SERVER['REQUEST_METHOD'] === 'POST'){
    addPost();
  }

  // 查找所有的分类
  $categories = bx_fetch_all('select * from categories');
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Add new post &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    <?php include_once 'inc/navbar.php' ?>

    <div class="container-fluid">
      <div class="page-title">
        <h1>写文章</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if(isset($err_msg)) : ?>
      <div class="alert alert-danger">
        <strong>错误！</strong><?php echo $err_msg ?>
      </div>
      <?php endif ?>
      <form class="row" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" enctype="multipart/form-data">
        <div class="col-md-9">
          <div class="form-group">
            <label for="title">标题</label>
            <input id="title" class="form-control input-lg" name="title" type="text" placeholder="文章标题">
          </div>
          <div class="form-group">
            <label for="content">内容</label>
            <textarea id="content" class="form-control input-lg" name="content" cols="30" rows="10" placeholder="内容"></textarea>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="slug">别名</label>
            <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
            <p class="help-block">https://zce.me/post/<strong>slug</strong></p>
          </div>
          <div class="form-group">
            <label for="feature">特色图像</label>
            <!-- show when image chose -->
            <img class="help-block thumbnail" style="display: none">
            <input id="feature" class="form-control" name="feature" type="file" accept="image/*">
          </div>
          <div class="form-group">
            <label for="category">所属分类</label>
            <select id="category" class="form-control" name="category">
              <?php foreach ($categories as $item) : ?>             
              <option value="<?php echo $item['id'] ?>"><?php echo $item['name'] ?></option>             
              <?php endforeach ?>
            </select>
          </div>
          <div class="form-group">
            <label for="created">发布时间</label>
            <input id="created" class="form-control" name="created" type="datetime-local">
          </div>
          <div class="form-group">
            <label for="status">状态</label>
            <select id="status" class="form-control" name="status">
              <option value="drafted">草稿</option>
              <option value="published">已发布</option>
            </select>
          </div>
          <div class="form-group">
            <button class="btn btn-primary" type="submit">保存</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <?php $num=3; include_once 'inc/sidebar.php' ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>

  <<script>
    $(function($){
        $('#feature').on('change', function(){
          // console.log('aaaaaaaaaaaaaaaaaaaaaaaaaaa');
          /* console.log($(this).val());
          console.log($(this).text());
          console.log($(this));
          $('.thumbnail').on('load', function () {
            $(this).fadeIn();
          }).attr('src', $(this).val()); */
        });
    });  
  </script>

  <script>NProgress.done()</script>
</body>
</html>
