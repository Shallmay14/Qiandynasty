<?php

/**
 * ECSHOP 管理中心公用函數庫
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發布。
 * ============================================================================
 * $Author: sxc_shop $
 * $Id: lib_main.php 17081 2010-03-29 10:27:50Z sxc_shop $
*/

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

/**
 * 獲得所有模塊的名稱以及鏈接地址
 *
 * @access      public
 * @param       string      $directory      插件存放的目錄
 * @return      array
 */
function read_modules($directory = '.')
{
    global $_LANG;

    $dir         = @opendir($directory);
    $set_modules = true;
    $modules     = array();

    while (false !== ($file = @readdir($dir)))
    {
        if (preg_match("/^.*?\.php$/", $file))
        {
            include_once($directory. '/' .$file);
        }
    }
    @closedir($dir);
    unset($set_modules);

    foreach ($modules AS $key => $value)
    {
        ksort($modules[$key]);
    }
    ksort($modules);

    return $modules;
}

/**
 * 系統提示信息
 *
 * @access      public
 * @param       string      msg_detail      消息內容
 * @param       int         msg_type        消息類型， 0消息，1錯誤，2詢問
 * @param       array       links           可選的鏈接
 * @param       boolen      $auto_redirect  是否需要自動跳轉
 * @return      void
 */
function sys_msg($msg_detail, $msg_type = 0, $links = array(), $auto_redirect = true)
{
    if (count($links) == 0)
    {
        $links[0]['text'] = $GLOBALS['_LANG']['go_back'];
        $links[0]['href'] = 'javascript:history.go(-1)';
    }

    assign_query_info();

    $GLOBALS['smarty']->assign('ur_here',     $GLOBALS['_LANG']['system_message']);
    $GLOBALS['smarty']->assign('msg_detail',  $msg_detail);
    $GLOBALS['smarty']->assign('msg_type',    $msg_type);
    $GLOBALS['smarty']->assign('links',       $links);
    $GLOBALS['smarty']->assign('default_url', $links[0]['href']);
    $GLOBALS['smarty']->assign('auto_redirect', $auto_redirect);

    $GLOBALS['smarty']->display('message.htm');

    exit;
}

/**
 * 記錄管理員的操作內容
 *
 * @access  public
 * @param   string      $sn         數據的唯一值
 * @param   string      $action     操作的類型
 * @param   string      $content    操作的內容
 * @return  void
 */
function admin_log($sn = '', $action, $content)
{
    $log_info = $GLOBALS['_LANG']['log_action'][$action] . $GLOBALS['_LANG']['log_action'][$content] .': '. addslashes($sn);

    $sql = 'INSERT INTO ' . $GLOBALS['ecs']->table('admin_log') . ' (log_time, user_id, log_info, ip_address) ' .
            " VALUES ('" . gmtime() . "', $_SESSION[admin_id], '" . stripslashes($log_info) . "', '" . real_ip() . "')";
    $GLOBALS['db']->query($sql);
}

/**
 * 將通過表單提交過來的年月日變量合成為"2004-05-10"的格式。
 *
 * 此函數適用於通過smarty函數html_select_date生成的下拉日期。
 *
 * @param  string $prefix      年月日變量的共同的前綴。
 * @return date                日期變量。
 */
function sys_joindate($prefix)
{
    /* 返回年-月-日的日期格式 */
    $year  = empty($_POST[$prefix . 'Year']) ? '0' :  $_POST[$prefix . 'Year'];
    $month = empty($_POST[$prefix . 'Month']) ? '0' : $_POST[$prefix . 'Month'];
    $day   = empty($_POST[$prefix . 'Day']) ? '0' : $_POST[$prefix . 'Day'];

    return $year . '-' . $month . '-' . $day;
}

/**
 * 設置管理員的session內容
 *
 * @access  public
 * @param   integer $user_id        管理員編號
 * @param   string  $username       管理員姓名
 * @param   string  $action_list    權限列表
 * @param   string  $last_time      最後登錄時間
 * @return  void
 */
function set_admin_session($user_id, $username, $action_list, $last_time)
{
    $_SESSION['admin_id']    = $user_id;
    $_SESSION['admin_name']  = $username;
    $_SESSION['action_list'] = $action_list;
    $_SESSION['last_check']  = $last_time; // 用於保存最後一次檢查訂單的時間
}

/**
 * 插入一個配置信息
 *
 * @access  public
 * @param   string      $parent     分組的code
 * @param   string      $code       該配置信息的唯一標識
 * @param   string      $value      該配置信息值
 * @return  void
 */
function insert_config($parent, $code, $value)
{
    global $ecs, $db, $_LANG;

    $sql = 'SELECT id FROM ' . $ecs->table('shop_config') . " WHERE code = '$parent' AND type = 1";
    $parent_id = $db->getOne($sql);

    $sql = 'INSERT INTO ' . $ecs->table('shop_config') . ' (parent_id, code, value) ' .
            "VALUES('$parent_id', '$code', '$value')";
    $db->query($sql);
}

/**
 * 判斷管理員對某一個操作是否有權限。
 *
 * 根據當前對應的action_code，然後再和用戶session裡面的action_list做匹配，以此來決定是否可以繼續執行。
 * @param     string    $priv_str    操作對應的priv_str
 * @param     string    $msg_type       返回的類型
 * @return true/false
 */
function admin_priv($priv_str, $msg_type = '' , $msg_output = true)
{
    global $_LANG;

    if ($_SESSION['action_list'] == 'all')
    {
        return true;
    }

    if (strpos(',' . $_SESSION['action_list'] . ',', ',' . $priv_str . ',') === false)
    {
        $link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
        if ( $msg_output)
        {
            sys_msg($_LANG['priv_error'], 0, $link);
        }
        return false;
    }
    else
    {
        return true;
    }
}

/**
 * 檢查管理員權限
 *
 * @access  public
 * @param   string  $authz
 * @return  boolean
 */
function check_authz($authz)
{
    return (preg_match('/,*'.$authz.',*/', $_SESSION['action_list']) || $_SESSION['action_list'] == 'all');
}

/**
 * 檢查管理員權限，返回JSON格式數劇
 *
 * @access  public
 * @param   string  $authz
 * @return  void
 */
function check_authz_json($authz)
{
    if (!check_authz($authz))
    {
        make_json_error($GLOBALS['_LANG']['priv_error']);
    }
}

/**
 * 取得紅包類型數組（用於生成下拉列表）
 *
 * @return  array       分類數組 bonus_typeid => bonus_type_name
 */
function get_bonus_type()
{
    $bonus = array();
    $sql = 'SELECT type_id, type_name, type_money FROM ' . $GLOBALS['ecs']->table('bonus_type') .
           ' WHERE send_type = 3';
    $res = $GLOBALS['db']->query($sql);

    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $bonus[$row['type_id']] = $row['type_name'].' [' .sprintf($GLOBALS['_CFG']['currency_format'], $row['type_money']).']';
    }

    return $bonus;
}

/**
 * 取得用戶等級數組,按用戶級別排序
 * @param   bool      $is_special      是否只顯示特殊會員組
 * @return  array     rank_id=>rank_name
 */
function get_rank_list($is_special = false)
{
    $rank_list = array();
    $sql = 'SELECT rank_id, rank_name, min_points FROM ' . $GLOBALS['ecs']->table('user_rank');
    if ($is_special)
    {
        $sql .= ' WHERE special_rank = 1';
    }
    $sql .= ' ORDER BY min_points';

    $res = $GLOBALS['db']->query($sql);

    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $rank_list[$row['rank_id']] = $row['rank_name'];
    }

    return $rank_list;
}

/**
 * 按等級取得用戶列表（用於生成下拉列表）
 *
 * @return  array       分類數組 user_id => user_name
 */
function get_user_rank($rankid, $where)
{
    $user_list = array();
    $sql = 'SELECT user_id, user_name FROM ' . $GLOBALS['ecs']->table('users') . $where.
           ' ORDER BY user_id DESC';
    $res = $GLOBALS['db']->query($sql);

    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $user_list[$row['user_id']] = $row['user_name'];
    }

    return $user_list;
}

/**
 * 取得廣告位置數組（用於生成下拉列表）
 *
 * @return  array       分類數組 position_id => position_name
 */
function get_position_list()
{
    $position_list = array();
    $sql = 'SELECT position_id, position_name, ad_width, ad_height '.
           'FROM ' . $GLOBALS['ecs']->table('ad_position');
    $res = $GLOBALS['db']->query($sql);

    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $position_list[$row['position_id']] = addslashes($row['position_name']). ' [' .$row['ad_width']. 'x' .$row['ad_height']. ']';
    }

    return $position_list;
}

/**
 * 生成編輯器
 * @param   string  input_name  輸入框名稱
 * @param   string  input_value 輸入框值
 */
function create_html_editor($input_name, $input_value = '')
{
    global $smarty;

    $editor = new FCKeditor($input_name);
    $editor->BasePath   = '../includes/fckeditor/';
    $editor->ToolbarSet = 'Normal';
    $editor->Width      = '100%';
    $editor->Height     = '320';
    $editor->Value      = $input_value;
    $FCKeditor = $editor->CreateHtml();
    $smarty->assign('FCKeditor', $FCKeditor);
}

/**
 * 取得商品列表：用於把商品添加到組合、關聯類、贈品類
 * @param   object  $filters    過濾條件
 */
function get_goods_list($filter)
{
    $filter->keyword = json_str_iconv($filter->keyword);
    $where = get_where_sql($filter); // 取得過濾條件

    /* 取得數據 */
    $sql = 'SELECT goods_id, goods_name, shop_price '.
           'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' . $where .
           'LIMIT 50';
    $row = $GLOBALS['db']->getAll($sql);

    return $row;
}

/**
 * 取得文章列表：用於商品關聯文章
 * @param   object  $filters    過濾條件
 */
function get_article_list($filter)
{
    /* 創建數據容器對象 */
    $ol = new OptionList();

    /* 取得過濾條件 */
    $where = ' WHERE a.cat_id = c.cat_id AND c.cat_type = 1 ';
    $where .= isset($filter->title) ? " AND a.title LIKE '%" . mysql_like_quote($filter->title) . "%'" : '';

    /* 取得數據 */
    $sql = 'SELECT a.article_id, a.title '.
           'FROM ' .$GLOBALS['ecs']->table('article'). ' AS a, ' .$GLOBALS['ecs']->table('article_cat'). ' AS c ' . $where;
    $res = $GLOBALS['db']->query($sql);

    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $ol->add_option($row['article_id'], $row['title']);
    }

    /* 生成列表 */
    $ol->build_select();
}

/**
 * 返回是否
 * @param   int     $var    變量 1, 0
 */
function get_yes_no($var)
{
    return empty($var) ? '<img src="images/no.gif" border="0" />' : '<img src="images/yes.gif" border="0" />';
}

/**
 * 生成過濾條件：用於 get_goodslist 和 get_goods_list
 * @param   object  $filter
 * @return  string
 */
function get_where_sql($filter)
{
    $time = date('Y-m-d');

    $where  = isset($filter->is_delete) && $filter->is_delete == '1' ?
        ' WHERE is_delete = 1 ' : ' WHERE is_delete = 0 ';
    $where .= (isset($filter->real_goods) && ($filter->real_goods > -1)) ? ' AND is_real = ' . intval($filter->real_goods) : '';
    $where .= isset($filter->cat_id) && $filter->cat_id > 0 ? ' AND ' . get_children($filter->cat_id) : '';
    $where .= isset($filter->brand_id) && $filter->brand_id > 0 ? " AND brand_id = '" . $filter->brand_id . "'" : '';
    $where .= isset($filter->intro_type) && $filter->intro_type != '0' ? ' AND ' . $filter->intro_type . " = '1'" : '';
    $where .= isset($filter->intro_type) && $filter->intro_type == 'is_promote' ?
        " AND promote_start_date <= '$time' AND promote_end_date >= '$time' " : '';
    $where .= isset($filter->keyword) && trim($filter->keyword) != '' ?
        " AND (goods_name LIKE '%" . mysql_like_quote($filter->keyword) . "%' OR goods_sn LIKE '%" . mysql_like_quote($filter->keyword) . "%' OR goods_id LIKE '%" . mysql_like_quote($filter->keyword) . "%') " : '';
    $where .= isset($filter->suppliers_id) && trim($filter->suppliers_id) != '' ?
        " AND (suppliers_id = '" . $filter->suppliers_id . "') " : '';

    $where .= isset($filter->in_ids) ? ' AND goods_id ' . db_create_in($filter->in_ids) : '';
    $where .= isset($filter->exclude) ? ' AND goods_id NOT ' . db_create_in($filter->exclude) : '';
    $where .= isset($filter->stock_warning) ? ' AND goods_number <= warn_number' : '';

    return $where;
}

/**
 * 獲取地區列表的函數。
 *
 * @access  public
 * @param   int     $region_id  上級地區id
 * @return  void
 */
function area_list($region_id)
{
    $area_arr = array();

    $sql = 'SELECT * FROM ' . $GLOBALS['ecs']->table('region').
           " WHERE parent_id = '$region_id' ORDER BY region_id";
    $res = $GLOBALS['db']->query($sql);
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $row['type']  = ($row['region_type'] == 0) ? $GLOBALS['_LANG']['country']  : '';
        $row['type'] .= ($row['region_type'] == 1) ? $GLOBALS['_LANG']['province'] : '';
        $row['type'] .= ($row['region_type'] == 2) ? $GLOBALS['_LANG']['city']     : '';
        $row['type'] .= ($row['region_type'] == 3) ? $GLOBALS['_LANG']['cantonal'] : '';

        $area_arr[] = $row;
    }

    return $area_arr;
}

/**
 * 取得圖表顏色
 *
 * @access  public
 * @param   integer $n  顏色順序
 * @return  void
 */
function chart_color($n)
{
    /* 隨機顯示顏色代碼 */
    $arr = array('33FF66', 'FF6600', '3399FF', '009966', 'CC3399', 'FFCC33', '6699CC', 'CC3366', '33FF66', 'FF6600', '3399FF');

    if ($n > 8)
    {
        $n = $n % 8;
    }

    return $arr[$n];
}

/**
 * 獲得商品類型的列表
 *
 * @access  public
 * @param   integer     $selected   選定的類型編號
 * @return  string
 */
function goods_type_list($selected)
{
    $sql = 'SELECT cat_id, cat_name FROM ' . $GLOBALS['ecs']->table('goods_type') . ' WHERE enabled = 1';
    $res = $GLOBALS['db']->query($sql);

    $lst = '';
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $lst .= "<option value='$row[cat_id]'";
        $lst .= ($selected == $row['cat_id']) ? ' selected="true"' : '';
        $lst .= '>' . htmlspecialchars($row['cat_name']). '</option>';
    }

    return $lst;
}

/**
 * 取得貨到付款和非貨到付款的支付方式
 * @return  array('is_cod' => '', 'is_not_cod' => '')
 */
function get_pay_ids()
{
    $ids = array('is_cod' => '0', 'is_not_cod' => '0');
    $sql = 'SELECT pay_id, is_cod FROM ' .$GLOBALS['ecs']->table('payment'). ' WHERE enabled = 1';
    $res = $GLOBALS['db']->query($sql);

    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        if ($row['is_cod'])
        {
            $ids['is_cod'] .= ',' . $row['pay_id'];
        }
        else
        {
            $ids['is_not_cod'] .= ',' . $row['pay_id'];
        }
    }

    return $ids;
}

/**
 * 清空表數據
 * @param   string  $table_name 表名稱
 */
function truncate_table($table_name)
{
    $sql = 'TRUNCATE TABLE ' .$GLOBALS['ecs']->table($table_name);

    return $GLOBALS['db']->query($sql);
}

/**
 *  返回字符集列表數組
 *
 * @access  public
 * @param
 *
 * @return void
 */
function get_charset_list()
{
    return array(
        'UTF8'   => 'UTF-8',
        'GB2312' => 'GB2312/GBK',
        'BIG5'   => 'BIG5',
    );
}


/**
 * 創建一個JSON格式的數據
 *
 * @access  public
 * @param   string      $content
 * @param   integer     $error
 * @param   string      $message
 * @param   array       $append
 * @return  void
 */
function make_json_response($content='', $error="0", $message='', $append=array())
{
    include_once(ROOT_PATH . 'includes/cls_json.php');

    $json = new JSON;

    $res = array('error' => $error, 'message' => $message, 'content' => $content);

    if (!empty($append))
    {
        foreach ($append AS $key => $val)
        {
            $res[$key] = $val;
        }
    }

    $val = $json->encode($res);

    exit($val);
}

/**
 *
 *
 * @access  public
 * @param
 * @return  void
 */
function make_json_result($content, $message='', $append=array())
{
    make_json_response($content, 0, $message, $append);
}

/**
 * 創建一個JSON格式的錯誤信息
 *
 * @access  public
 * @param   string  $msg
 * @return  void
 */
function make_json_error($msg)
{
    make_json_response('', 1, $msg);
}

/**
 * 根據過濾條件獲得排序的標記
 *
 * @access  public
 * @param   array   $filter
 * @return  array
 */
function sort_flag($filter)
{
    $flag['tag']    = 'sort_' . preg_replace('/^.*\./', '', $filter['sort_by']);
    $flag['img']    = '<img src="images/' . ($filter['sort_order'] == "DESC" ? 'sort_desc.gif' : 'sort_asc.gif') . '"/>';

    return $flag;
}

/**
 * 分頁的信息加入條件的數組
 *
 * @access  public
 * @return  array
 */
function page_and_size($filter)
{
    if (isset($_REQUEST['page_size']) && intval($_REQUEST['page_size']) > 0)
    {
        $filter['page_size'] = intval($_REQUEST['page_size']);
    }
    elseif (isset($_COOKIE['ECSCP']['page_size']) && intval($_COOKIE['ECSCP']['page_size']) > 0)
    {
        $filter['page_size'] = intval($_COOKIE['ECSCP']['page_size']);
    }
    else
    {
        $filter['page_size'] = 15;
    }

    /* 每頁顯示 */
    $filter['page'] = (empty($_REQUEST['page']) || intval($_REQUEST['page']) <= 0) ? 1 : intval($_REQUEST['page']);

    /* page 總數 */
    $filter['page_count'] = (!empty($filter['record_count']) && $filter['record_count'] > 0) ? ceil($filter['record_count'] / $filter['page_size']) : 1;

    /* 邊界處理 */
    if ($filter['page'] > $filter['page_count'])
    {
        $filter['page'] = $filter['page_count'];
    }

    $filter['start'] = ($filter['page'] - 1) * $filter['page_size'];

    return $filter;
}

/**
 *  將含有單位的數字轉成字節
 *
 * @access  public
 * @param   string      $val        帶單位的數字
 *
 * @return  int         $val
 */
function return_bytes($val)
{
    $val = trim($val);
    $last = strtolower($val{strlen($val)-1});
    switch($last)
    {
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }

    return $val;
}

/**
 * 獲得指定的商品類型下所有的屬性分組
 *
 * @param   integer     $cat_id     商品類型ID
 *
 * @return  array
 */
function get_attr_groups($cat_id)
{
    $sql = "SELECT attr_group FROM " . $GLOBALS['ecs']->table('goods_type') . " WHERE cat_id='$cat_id'";
    $grp = str_replace("\r", '', $GLOBALS['db']->getOne($sql));

    if ($grp)
    {
        return explode("\n", $grp);
    }
    else
    {
        return array();
    }
}

/**
 * 生成鏈接後綴
 */
function list_link_postfix()
{
    return 'uselastfilter=1';
}

/**
 * 保存過濾條件
 * @param   array   $filter     過濾條件
 * @param   string  $sql        查詢語句
 * @param   string  $param_str  參數字符串，由list函數的參數組成
 */
function set_filter($filter, $sql, $param_str = '')
{
    $filterfile = basename(PHP_SELF, '.php');
    if ($param_str)
    {
        $filterfile .= $param_str;
    }
    setcookie('ECSCP[lastfilterfile]', sprintf('%X', crc32($filterfile)), time() + 600);
    setcookie('ECSCP[lastfilter]',     urlencode(serialize($filter)), time() + 600);
    setcookie('ECSCP[lastfiltersql]',  base64_encode($sql), time() + 600);
}

/**
 * 取得上次的過濾條件
 * @param   string  $param_str  參數字符串，由list函數的參數組成
 * @return  如果有，返回array('filter' => $filter, 'sql' => $sql)；否則返回false
 */
function get_filter($param_str = '')
{
    $filterfile = basename(PHP_SELF, '.php');
    if ($param_str)
    {
        $filterfile .= $param_str;
    }
    if (isset($_GET['uselastfilter']) && isset($_COOKIE['ECSCP']['lastfilterfile'])
        && $_COOKIE['ECSCP']['lastfilterfile'] == sprintf('%X', crc32($filterfile)))
    {
        return array(
            'filter' => unserialize(urldecode($_COOKIE['ECSCP']['lastfilter'])),
            'sql'    => base64_decode($_COOKIE['ECSCP']['lastfiltersql'])
        );
    }
    else
    {
        return false;
    }
}

/**
 * URL過濾
 * @param   string  $url  參數字符串，一個urld地址,對url地址進行校正
 * @return  返回校正過的url;
 */
function sanitize_url($url , $check = 'http://')
{
    if (strpos( $url, $check ) === false)
    {
        $url = $check . $url;
    }
    return $url;
}

/**
 * 檢查分類是否已經存在
 *
 * @param   string      $cat_name       分類名稱
 * @param   integer     $parent_cat     上級分類
 * @param   integer     $exclude        排除的分類ID
 *
 * @return  boolean
 */
function cat_exists($cat_name, $parent_cat, $exclude = 0)
{
    $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('category').
    " WHERE parent_id = '$parent_cat' AND cat_name = '$cat_name' AND cat_id<>'$exclude'";
    return ($GLOBALS['db']->getOne($sql) > 0) ? true : false;
}

function brand_exists($brand_name)
{
    $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('brand').
    " WHERE brand_name = '" . $brand_name . "'";
    return ($GLOBALS['db']->getOne($sql) > 0) ? true : false;
}

/**
 * 獲取當前管理員信息
 *
 * @access  public
 * @param
 *
 * @return  Array
 */
function admin_info()
{
    $sql = "SELECT * FROM ". $GLOBALS['ecs']->table('admin_user')."
            WHERE user_id = '$_SESSION[admin_id]'
            LIMIT 0, 1";
    $admin_info = $GLOBALS['db']->getRow($sql);

    if (empty($admin_info))
    {
        return $admin_info = array();
    }

    return $admin_info;
}

/**
 * 供貨商列表信息
 *
 * @param       string      $conditions
 * @return      array
 */
function suppliers_list_info($conditions = '')
{
    $where = '';
    if (!empty($conditions))
    {
        $where .= 'WHERE ';
        $where .= $conditions;
    }

    /* 查詢 */
    $sql = "SELECT suppliers_id, suppliers_name, suppliers_desc
            FROM " . $GLOBALS['ecs']->table("suppliers") . "
            $where";

    return $GLOBALS['db']->getAll($sql);
}

/**
 * 供貨商名
 *
 * @return  array
 */
function suppliers_list_name()
{
    /* 查詢 */
    $suppliers_list = suppliers_list_info(' is_check = 1 ');

    /* 供貨商名字 */
    $suppliers_name = array();
    if (count($suppliers_list) > 0)
    {
        foreach ($suppliers_list as $suppliers)
        {
            $suppliers_name[$suppliers['suppliers_id']] = $suppliers['suppliers_name'];
        }
    }

    return $suppliers_name;
}
?>