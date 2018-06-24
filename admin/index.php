<?php 
  require_once '../common.php';

  if(!bx_isExistCurUser()){    
    header('Location: /admin/login.php');
  }
  
  function bx_query_count($conn, $tname, $cond='') {
    $sql = 'select count(1) from '.$tname;   
    if($cond !== ''){
      $sql = $sql.' where '.$cond;
    }
    $query = mysqli_query($conn, $sql);
    if($query) {
      if($result = mysqli_fetch_assoc($query)){
        mysqli_free_result($query);
        return $result['count(1)'];
      }
    }
    return FALSE;
  }

  function bx_getAllItemCount(){
    $conn = bx_init_sql();
    if(!$conn) return FALSE;

    $items = array();    
    if($rel = bx_query_count($conn, 'posts')){
      $items['count_posts'] = $rel;
    }    
   
    if($rel = bx_query_count($conn, 'posts', 'status="drafted"')){
      $items['count_posts_drafted'] = $rel;
    }
    
    if($rel = bx_query_count($conn, 'categories')){
      $items['count_categories'] = $rel;
    }
    
    if($rel = bx_query_count($conn, 'comments')){
      $items['count_comments'] = $rel;
    }
   
    if($rel = bx_query_count($conn, 'comments', 'status="held"')){
      $items['count_comments_held'] = $rel;
    }
    mysqli_close($conn);
    return $items;
  }

  $items = bx_getAllItemCount();

  if(!$items)    
    exit('数据库连接失败');
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Dashboard &laquo; Admin</title>
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
      <div class="jumbotron text-center">
        <h1>千学不如一看，千看不如一练。</h1>
        <p>不下水，一辈子不会游泳；不扬帆，一辈子不会撑船。</p>
        <p><a class="btn btn-primary btn-lg" href="post-add.php" role="button">写文章</a></p>
      </div>
      <div class="row">
        <div class="col-md-4">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title">站点内容统计：</h3>
            </div>
            <ul class="list-group">
              <li class="list-group-item"><strong><?php echo isset($items['count_posts']) ? $items['count_posts'] : 0 ?>
                  </strong>篇文章（<strong><?php echo isset($items['count_posts_drafted']) ? $items['count_posts_drafted'] : 0 ?>
                  </strong>篇草稿）</li>
              <li class="list-group-item"><strong><?php echo isset($items['count_categories']) ? $items['count_categories'] : 0 ?>
                  </strong>个分类</li>
              <li class="list-group-item"><strong><?php echo isset($items['count_comments']) ? $items['count_comments'] : 0 ?>
                  </strong>条评论（<strong><?php echo isset($items['count_comments_held']) ? $items['count_comments_held'] : 0 ?>
                  </strong>条待审核）</li>
            </ul>
          </div>
        </div>
        <div class="col-md-4"></div>
        <div class="col-md-4"></div>
      </div>
    </div>
  </div>

  <?php $num = 1; include_once 'inc/sidebar.php' ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script> 
  <script>NProgress.done()</script>
</body>
</html>
