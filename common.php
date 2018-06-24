<?php
  /* 目录根路径 */
  define('BX_ROOT', dirname('F:\www\baixiu\common.php'));

  /* 数据库主机名称 */
  define('BX_DB_HOST', 'localhost');
  /* 数据库用户名 */
  define('BX_DB_USER', 'root');
  /* 数据库密码 */
  define('BX_DB_PASSWORD', 'root');
  /* 数据库名称 */
  define('BX_DB_NAME', 'baixiu');
  
  /* 初始化数据库 */
  function bx_init_sql(){
    $conn = mysqli_connect(BX_DB_HOST, BX_DB_USER, BX_DB_PASSWORD, BX_DB_NAME);    
    if(!$conn)
      return FALSE;
    return $conn;
  }  

  /* 通过一个数据库查询获取数据 */
  function bx_fetch_all($sql){
    $conn = bx_init_sql();
    if(!$conn) return FALSE;

    $query = mysqli_query($conn, $sql);   
    if(!$query){
      mysqli_close($conn);
      return FALSE;
    }
   
    $arr = array();
    while($result = mysqli_fetch_assoc($query)){
      $arr[] = $result;
    }
    
    mysqli_free_result($query);
    mysqli_close($conn);
    return $arr;
  }

  function bx_fetch_one($sql){      
    return ($rel = bx_fetch_all($sql)) ? $rel[0] : $rel;     
  }

  /* 添加类查询方法 */
  function bx_execture($sql){
    $conn = bx_init_sql();
    if(!$conn) return FALSE;

    $query = mysqli_query($conn, $sql); 
    // var_dump($query);  
    if(!$query){
      mysqli_close($conn);
      return FALSE;
    }

    $affected_rows = mysqli_affected_rows($conn);

    mysqli_close($conn);
    return $affected_rows;
  }

  /*function bx_query_count($conn, $tname, $cond='') {
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
  }*/

  /* 登录用户的session ID */
  define('BX_CUR_LOGIN_USER', 'cur_login_user');
  /* 判断session是否启动 */
  function is_session_started()
  {
    if ( php_sapi_name() !== 'cli' ) {
        if ( version_compare(phpversion(), '5.4.0', '>=') ) {
            return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
        } else {
            return session_id() === '' ? FALSE : TRUE;
        }
    }
    return FALSE;
  }

  // 判断当前用户的session是否存在
  function bx_isExistCurUser(){
    if ( is_session_started() === FALSE ) session_start();

    return isset($_SESSION[BX_CUR_LOGIN_USER]);
  }  

  // 获取当前登录的用户的session
  function bx_getCurUser(){
    if ( is_session_started() === FALSE ) session_start();
    
    return $_SESSION[BX_CUR_LOGIN_USER];
  }

  // 设置当前登录的用户的session
  function bx_setCurUser($_value){
    if ( is_session_started() === FALSE ) session_start();
    
    $_SESSION[BX_CUR_LOGIN_USER] = $_value;
  }

  // 销毁登录的用户的session
  function bx_destoryCurUser(){
    if(bx_isExistCurUser()){
      unset($_SESSION[BX_CUR_LOGIN_USER]);
    }
  }