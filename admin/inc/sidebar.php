<?php 
  require_once '../common.php';

  $num = isset($num) ? $num : 0; 
  
  $cur_user = bx_getCurUser();

  // var_dump($cur_user);

?>

  <div class="aside">
    <div class="profile">
      <img class="avatar" src="<?php echo $cur_user['avatar'] ? $cur_user['avatar'] : '/static/assets/img/default.png' ?>">
      <h3 class="name"><?php echo $cur_user['nickname'] ?></h3>
    </div>
    <ul class="nav">
      <li<?php if($num===1)echo ' class="active"'; ?>>
        <a href="index.php"><i class="fa fa-dashboard"></i>仪表盘</a>
      </li>
      <li<?php if($num>=2&&$num<=4) echo ' class="active"' ?>>
        <a href="#menu-posts"<?php if($num<2||$num>4) echo 'class="collapsed"' ?> data-toggle="collapse">
          <i class="fa fa-thumb-tack"></i>文章<i class="fa fa-angle-right"></i>
        </a>
        <ul id="menu-posts" class="collapse<?php if($num>=2&&$num<=4) echo ' in' ?>">
          <li<?php if($num===2)echo ' class="active"'; ?>><a href="posts.php">所有文章</a></li>
          <li<?php if($num===3)echo ' class="active"'; ?>><a href="post-add.php">写文章</a></li>
          <li<?php if($num===4)echo ' class="active"'; ?>><a href="categories.php">分类目录</a></li>
        </ul>
      </li>
      <li<?php if($num===5)echo ' class="active"'; ?>>
        <a href="comments.php"><i class="fa fa-comments"></i>评论</a>
      </li>
      <li<?php if($num===6)echo ' class="active"'; ?>>
        <a href="users.php"><i class="fa fa-users"></i>用户</a>
      </li>
      <li<?php if($num>=7&&$num<=9) echo ' class="active"' ?>>
        <a href="#menu-settings"<?php if($num<7||$num>9) echo 'class="collapsed"' ?> data-toggle="collapse">
          <i class="fa fa-cogs"></i>设置<i class="fa fa-angle-right"></i>
        </a>
        <ul id="menu-settings" class="collapse<?php if($num>=7&&$num<=9) echo ' in' ?>">
          <li<?php if($num===7)echo ' class="active"'; ?>><a href="nav-menus.php">导航菜单</a></li>
          <li<?php if($num===8)echo ' class="active"'; ?>><a href="slides.php">图片轮播</a></li>
          <li<?php if($num===9)echo ' class="active"'; ?>><a href="settings.php">网站设置</a></li>
        </ul>
      </li>
    </ul>
  </div> 