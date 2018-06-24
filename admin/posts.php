<?php 
  require_once '../common.php';

  if(!bx_isExistCurUser()){    
    header('Location: /admin/login.php');
  }

  // 编辑分类
  function edit_category(){
    $id = $_GET['id'];
    $name = $_POST['name'];
    $slug = $_POST['slug'];    
    if(bx_execture("update categories set name='{$name}',slug='{$slug}' where id = '{$id}' limit 1") > 0){
      $GLOBALS['err_msg'] = '保存成功！';
      $GLOBALS['issuccess'] = true;     
    }else{
      $GLOBALS['err_msg'] = '保存失败！';
      $GLOBALS['issuccess'] = false;
    }
  }
  // 判断请求方式，如果是post则定为添加或保存分类，否则为编辑和删除
  if($_SERVER['REQUEST_METHOD'] === 'POST'){
      edit_category();     
  } else if(!empty($_GET['id']) && !empty($_GET['req'])){    
    $id = $_GET['id'];
    $req = $_GET['req'];
    
    if($req === 'edit') {      
      $GLOBALS['cur_category'] = bx_fetch_one("select * from posts where id = '{$id}' limit 1"); 
    } else if($req === 'del') {   
      bx_execture("delete from posts where id in ({$id})");  
      echo '重复执行了';
    }   
  }

  // 接受筛选参数
  $where = '1=1';
  $categoryId = '';
  if(!empty($_GET['category'])){
    $categoryId = $_GET['category'];
    $where .= ' and posts.category_id='.$categoryId;
  }
  $currentStatus = '';
  if(!empty($_GET['status'])){
    $currentStatus = $_GET['status'];
    $where .= " and posts.status='{$currentStatus}'";
  }
  // echo $where;

  // 处理分页

  // 从第1页数据开始，打印5条
  $size = 5;
  $variable = 5;// 1/2/3/4/5共5个选择页
  $begin = 1;
  $end = $variable; 
  // 获取总条数
  $total_count = bx_fetch_one('select count(1) as num
        from posts inner join categories on posts.category_id = categories.id
          inner join users on posts.user_id = users.id where '.$where);          
  // 获取总页数
  $total_page = (int)ceil($total_count['num'] / $size);  
  var_dump($total_page);
  // 查找文章，一页显示5条  
  $page = empty($_GET['page']) ? 1 : (int)$_GET['page'];  
  if($page > $total_page){
    $page = $page - 1 > 0 ? ($page - 1) : 1;
  }
  $posts = bx_fetch_all('select posts.id,
                    posts.title,categories.name as category_name,	
                    users.nickname as user_name,posts.created,posts.status
                    from posts inner join categories on posts.category_id = categories.id
                    inner join users on posts.user_id = users.id
                    where '.$where.'
                    order by posts.created asc
                    limit '.(($page-1)*$size).','.$size);
  
  
  if($total_page <= $variable){
    $begin = 1;
    $end = $total_page;
  }else if($page >= $total_page - 2){
    $begin = $total_page - 4;// 10 - 10 + 6; 10 - 9 + 5;// 10 - 10 + 10 - 4
    $end = $total_page;                 
  }else if($page >= 4) { 
    $begin = $page - 2;
    $end = $page + 2;
  }      
  
  /*function getUserAuthor($user_id){
    $user = bx_fetch_one('select nickname from users where id='.$user_id);
    return $user ? $user['nickname'] : '匿名';
  }  

  function getCategory($category_id){
    $category = bx_fetch_one('select name from categories where id='.$category_id);
    return $category ? $category['name'] : '未分类';
  }  */


  function convertStatus($status){
    $dict = array(
      'published'=>'已发布',
      'drafted'=>'草稿',
      'trashed'=>'回收站'
    );
    return isset($dict[$status]) ? $dict[$status] : '未知';
  }

  function convertDate($date){    
    return date('Y年m月d日<b\r> h:i:s', strtotime($date));
  }
  // var_dump($posts);

  // 查找所有的分类
  $categories = bx_fetch_all('select * from categories');

  // 获取page category status 的url地址
  function getPCSUrl($page_num = '', $category_id = '', $status = ''){
    if($category_id === '' && $page_num === '' && $status === ''){
      return $_SERVER['PHP_SELF'];
    }
    return $_SERVER['PHP_SELF']."?page={$page_num}&category={$category_id}&status={$status}";
  }
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Posts &laquo; Admin</title>
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
        <h1>所有文章</h1>
        <a href="post-add.php" class="btn btn-primary btn-xs">写文章</a>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <a class="btn btn-danger btn-sm" href="<?php echo getPCSUrl($page,$categoryId,$currentStatus) ?>" style="display: none">批量删除</a>
        <form class="form-inline" method="<?php echo getPCSUrl() ?>">
          <select name="category" class="form-control input-sm">
            <option value="">所有分类</option>
            <?php foreach ($categories as $item) : ?>
            <option value="<?php echo $item['id'] ?>"<?php echo $categoryId === $item['id'] ? ' selected':'' ?>><?php echo $item['name'] ?></option>           
            <?php endforeach ?>            
          </select>
          <select name="status" class="form-control input-sm">
            <option value="">所有状态</option>
            <option value="drafted"<?php echo $currentStatus === 'drafted' ? ' selected':'' ?>>草稿</option>
            <option value="published"<?php echo $currentStatus === 'published' ? ' selected':'' ?>>已发布</option>
            <option value="trashed"<?php echo $currentStatus === 'trashed' ? ' selected':'' ?>>回收站</option>
          </select>
          <button class="btn btn-default btn-sm">筛选</button>
        </form>
        <ul class="pagination pagination-sm pull-right">
          <?php if($page > 1) : ?><li><a href="<?php echo getPCSUrl($page-1, $categoryId,$currentStatus) ?>">上一页</a></li><?php endif ?>
          
          <?php if($page >= $variable) : ?>
          <li><a href="<?php echo getPCSUrl($page - $variable + 1,$categoryId,$currentStatus) ?>">...</a></li>
          <?php endif ?>          
          
          <?php for ($i = $begin; $i <= $end; $i++) : ?>          
          <li class="<?php echo $page === $i ? 'active' : '' ?>"><a href="<?php echo getPCSUrl($i,$categoryId,$currentStatus) ?>"><?php echo $i ?></a></li>
          <?php endfor ?>

          <?php if($page <= $total_page - $variable) : ?>
          <li><a href="<?php echo getPCSUrl(($page+$variable-1>=$total_page)?$total_page:($page+4),$categoryId,$currentStatus) ?>">...</a></li>
          <?php endif ?>    

          <?php if($page < $total_page) : ?><li><a href="<?php echo getPCSUrl($page + 1,$categoryId,$currentStatus) ?>">下一页</a></li><?php endif ?>
        </ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>标题</th>
            <th>作者</th>
            <th>分类</th>
            <th class="text-center">发表时间</th>
            <th class="text-center">状态</th>
            <th class="text-center" width="100">操作</th>
          </tr>
        </thead>
        <tbody>
          <?php if(!empty($posts)) : ?>
          <?php foreach ($posts as $item) : ?>          
          <tr>
            <td class="text-center"><input type="checkbox" data-id="<?php echo $item['id'] ?>"></td>
            <td><?php echo $item['title'] ?></td>
            <td><?php echo $item['user_name'] ?></td>
            <td><?php echo $item['category_name'] ?></td>
            <td class="text-center"><?php echo convertDate($item['created']) ?></td>
            <td class="text-center"><?php echo convertStatus($item['status']) ?></td>
            <td class="text-center">
              <a href="<?php echo getPCSUrl($page,$categoryId,$currentStatus).'&id='.$item['id'] ?>&req=edit" class="btn btn-default btn-xs">编辑</a>
              <a href="<?php echo getPCSUrl($page,$categoryId,$currentStatus).'&id='.$item['id'] ?>&req=del" class="btn btn-danger btn-xs">删除</a>
            </td>
          </tr>
          <?php endforeach ?>
          <?php endif ?>
        </tbody>
      </table>
    </div>
  </div>

  <?php $num=2; include_once 'inc/sidebar.php' ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>

  <script>
    $(function($){    
      // 定义一个数组
      let cur_checkeds = [];
      // 为每一个复选框增加一个点击事件
      const $tbodyCheckBoxs = $('tbody input');
      
      const $btn_delete = $('.page-action > a');

      $tbodyCheckBoxs.on('change', function(){ 
        const id = $(this).data('id');// 会转换成对应类型，这里转换成了数字类型
        // const value = $(this).attr('data-id'); //只会返回字符串类型
       //单选被选中与没有选中的处理 
        if($(this).prop('checked')) {          
          cur_checkeds.length !== 0 || $btn_delete.fadeIn();              
          cur_checkeds.includes(id) || cur_checkeds.push(id);                                
        } else {                     
          cur_checkeds.splice(cur_checkeds.indexOf(id), 1);  
          cur_checkeds.length || $btn_delete.fadeOut();          
        }
        let str = $btn_delete.attr('href');
        const index = str.indexOf('&id');
        if(index !== -1)
          str = str.substring(0,index);
        $btn_delete.prop('href', str+'&id='+cur_checkeds+'&req=del');
        // $btn_delete.prop('search', '?id='+cur_checkeds);
      }); 

      $('thead input').on('change', function(){
        // 获取当前被选中的状态
        const checked = $(this).prop('checked');
        // 设置每一个复选框的属性
        $tbodyCheckBoxs.prop('checked', checked).trigger('change');
      });    
    });
  </script>

  <script>NProgress.done()</script>
</body>
</html>
