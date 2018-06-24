<?php 
  require_once '../common.php';

  if(!bx_isExistCurUser()){    
    header('Location: /admin/login.php');
  }
  // 添加分类
  function add_category(){
    $name = $_POST['name'];
    $slug = $_POST['slug'];
    if(bx_execture("insert into categories values(NULL,'{$slug}','{$name}');") > 0) {
      $GLOBALS['err_msg'] = '添加成功！';
      $GLOBALS['issuccess'] = true;
    } else {
      $GLOBALS['err_msg'] = '添加失败！';
      $GLOBALS['issuccess'] = false;
    }  
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
    if(!empty($_POST['name']) && !empty($_POST['slug'])){      
      empty($_GET['id']) ? add_category() : edit_category();   
    } else {
      $GLOBALS['err_msg'] = '请填写完整的表单！';
      $GLOBALS['issuccess'] = false;
    }
  } else if(!empty($_GET['id']) && !empty($_GET['req'])){    
    $id = $_GET['id'];
    $req = $_GET['req'];
    
    if($req === 'edit') {      
      $GLOBALS['cur_category'] = bx_fetch_one("select * from categories where id = '{$id}' limit 1"); 
    } else if($req === 'del') {   
      bx_execture("delete from categories where id in ({$id})");  
      echo '重复执行了';
    }   
  }  
  
  // 查找所有的分类
  $categories = bx_fetch_all('select * from categories');

?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Categories &laquo; Admin</title>
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
        <h1>分类目录</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if(isset($err_msg)) : ?>
        <?php if($issuccess) : ?>
        <div class="alert alert-success">
          <strong>成功！</strong><?php echo $err_msg ?>
        </div>
        <?php else : ?>
        <div class="alert alert-danger">
          <strong>错误！</strong><?php echo $err_msg ?>
        </div>
        <?php endif ?>      
      <?php endif ?>
      <div class="row">
        <div class="col-md-4">          
          <form action="<?php echo $_SERVER['PHP_SELF'].(empty($cur_category['id']) ? '' : '?id='.$cur_category['id']) ?>" method="POST">
            <h2><?php echo empty($cur_category['name']) ? '添加新分类目录' : '编辑：'.$cur_category['name'] ?></h2>
            <div class="form-group">
              <label for="name">名称</label>
              <input id="name" class="form-control" name="name" type="text" placeholder="分类名称"
                              value="<?php echo empty($cur_category['name']) ? '' : $cur_category['name'] ?>">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug"
                              value="<?php echo empty($cur_category['slug']) ? '' : $cur_category['slug'] ?>">
              <p class="help-block">http://www.baixiu.com/admin/</p>
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit"><?php echo empty($cur_category) ? '添加' : '保存' ?></button>
            </div>
          </form>         
        </div>        
        <div class="col-md-8">
        <?php if(!empty($categories)) : ?>
          <div class="page-action">
            <!-- show when multiple checked -->
            <a class="btn btn-danger btn-sm" href="<?php echo $_SERVER['PHP_SELF'] ?>" style="display: none">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th class="text-center" width="40"><input type="checkbox" data-id="0"></th>
                <th>名称</th>
                <th>Slug</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($categories as $item) : ?>
              <tr>
                <td class="text-center"><input type="checkbox" data-id="<?php echo $item['id'] ?>"></td>
                <td><?php echo $item['name'] ?></td>
                <td><?php echo $item['slug'] ?></td>
                <td class="text-center">
                  <a href="<?php echo $_SERVER['PHP_SELF'].'?id='.$item['id'] ?>&req=edit" class="btn btn-info btn-xs">编辑</a>
                  <a href="<?php echo $_SERVER['PHP_SELF'].'?id='.$item['id'] ?>&req=del" class="btn btn-danger btn-xs">删除</a>
                </td>
              </tr>
              <?php endforeach ?>              
            </tbody>
          </table>
          <?php else : ?>
          <div class="alert alert-info">
            <strong>提示！</strong><?php echo '数据出现异常！请稍候再试！'; ?>
          </div>
          <?php endif ?>
        </div>
        
      </div>
    </div>
  </div>

  <?php $num=4; include_once 'inc/sidebar.php' ?>

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
        // $btn_delete.attr('href', '/admin/categories.php?id='+cur_checked.join());
        $btn_delete.prop('search', '?id='+cur_checkeds+'&req=del');
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
<!-- // 全选
          if(value === 0){ 
            $checkboxs.each(function(i , item){
              if(i > 0){
                const $checkbox = $(item);
                $checkbox.prop('checked', true);
                cur_checked.push($checkbox.attr('data-id'));
              }            
            }); 
          } else 

           // 全选
          if(value === 0){                
            $checkboxs.each(function(i , item){
              if(i > 0){
                const $checkbox = $(item);
                $checkbox.prop('checked', false);
              }            
            }); 
            cur_checked = [];
          } else {  -->