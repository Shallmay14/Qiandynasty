<?php

/**
 * ECSHOP 超值禮包管理程序
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發佈。
 * ============================================================================
 * $Author: liuhui $
 * $Id: package.php 17063 2010-03-25 06:35:46Z liuhui $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
$exc = new exchange($ecs->table("goods_activity"), $db, 'act_id', 'act_name');

/*------------------------------------------------------ */
//-- 添加活動
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'add')
{
    /* 權限判斷 */
    admin_priv('package_manage');

    /* 組合商品 */
    $group_goods_list = array();
    $sql = "DELETE FROM " .$ecs->table('package_goods') .
            " WHERE package_id = 0 AND admin_id = '$_SESSION[admin_id]'";

    $db->query($sql);

    /* 初始化信息 */
    $start_time = local_date('Y-m-d H:i');
    $end_time   = local_date('Y-m-d H:i', strtotime('+1 month'));
    $package     = array('package_price'=>'', 'start_time' => $start_time, 'end_time' => $end_time);

    $smarty->assign('package',      $package);
    $smarty->assign('ur_here',      $_LANG['package_add']);
    $smarty->assign('action_link',  array('text' => $_LANG['14_package_list'], 'href'=>'package.php?act=list'));
    $smarty->assign('cat_list',     cat_list());
    $smarty->assign('brand_list',   get_brand_list());
    $smarty->assign('form_action',  'insert');

    assign_query_info();
    $smarty->display('package_info.htm');
}

elseif ($_REQUEST['act'] =='insert')
{
    /* 權限判斷 */
    admin_priv('package_manage');

    $sql = "SELECT COUNT(*) ".
           " FROM " . $ecs->table('goods_activity').
           " WHERE act_type='" . GAT_PACKAGE . "' AND act_name='" . $_POST['package_name'] . "'" ;
    if ($db->getOne($sql))
    {
        sys_msg(sprintf($_LANG['package_exist'],  $_POST['package_name']) , 1);
    }


    /* 將時間轉換成整數 */
    $_POST['start_time'] = local_strtotime($_POST['start_time']);
    $_POST['end_time']   = local_strtotime($_POST['end_time']);

    /* 處理提交數據 */
    if (empty($_POST['package_price']))
    {
        $_POST['package_price'] = 0;
    }

    $info = array('package_price'=>$_POST['package_price']);

    /* 插入數據 */
    $record = array('act_name'=>$_POST['package_name'], 'act_desc'=>$_POST['desc'],
                    'act_type'=>GAT_PACKAGE, 'start_time'=>$_POST['start_time'],
                    'end_time'=>$_POST['end_time'], 'is_finished'=>0, 'ext_info'=>serialize($info));

    $db->AutoExecute($ecs->table('goods_activity'),$record,'INSERT');

    /* 禮包編號 */
    $package_id = $db->insert_id();

    handle_packagep_goods($package_id);

    admin_log($_POST['package_name'],'add','package');
    $link[] = array('text' => $_LANG['back_list'], 'href'=>'package.php?act=list');
    $link[] = array('text' => $_LANG['continue_add'], 'href'=>'package.php?act=add');
    sys_msg($_LANG['add_succeed'],0,$link);
}

/*------------------------------------------------------ */
//-- 編輯活動
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit')
{
    /* 權限判斷 */
    admin_priv('package_manage');

    $package            = get_package_info($_REQUEST['id']);
    $package_goods_list = get_package_goods($_REQUEST['id']); // 禮包商品

    $smarty->assign('package',           $package);
    $smarty->assign('ur_here',           $_LANG['package_edit']);
    $smarty->assign('action_link',       array('text' => $_LANG['14_package_list'], 'href'=>'package.php?act=list&' . list_link_postfix()));
    $smarty->assign('cat_list',     cat_list());
    $smarty->assign('brand_list',   get_brand_list());
    $smarty->assign('form_action',       'update');
    $smarty->assign('package_goods_list', $package_goods_list);

    assign_query_info();
    $smarty->display('package_info.htm');

}
elseif ($_REQUEST['act'] =='update')
{
    /* 權限判斷 */
    admin_priv('package_manage');

    /* 將時間轉換成整數 */
    $_POST['start_time'] = local_strtotime($_POST['start_time']);
    $_POST['end_time']   = local_strtotime($_POST['end_time']);

    /* 處理提交數據 */
    if (empty($_POST['package_price']))
    {
        $_POST['package_price'] = 0;
    }

    /* 檢查活動重名 */
    $sql = "SELECT COUNT(*) ".
           " FROM " . $ecs->table('goods_activity').
           " WHERE act_type='" . GAT_PACKAGE . "' AND act_name='" . $_POST['package_name'] . "' AND act_id <> '" .  $_POST['id'] . "'" ;
    if ($db->getOne($sql))
    {
        sys_msg(sprintf($_LANG['package_exist'],  $_POST['package_name']) , 1);
    }


    $info = array('package_price'=>$_POST['package_price']);

    /* 更新數據 */
    $record = array('act_name' => $_POST['package_name'], 'start_time' => $_POST['start_time'], 'end_time' => $_POST['end_time'],
                    'act_desc' => $_POST['desc'], 'ext_info'=>serialize($info));
    $db->autoExecute($ecs->table('goods_activity'), $record, 'UPDATE', "act_id = '" . $_POST['id'] . "' AND act_type = " . GAT_PACKAGE );

    admin_log($_POST['package_name'],'edit','package');
    $link[] = array('text' => $_LANG['back_list'], 'href'=>'package.php?act=list&' . list_link_postfix());
    sys_msg($_LANG['edit_succeed'],0,$link);
}

/*------------------------------------------------------ */
//-- 刪除指定的活動
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'remove')
{
    check_authz_json('package_manage');

    $id = intval($_GET['id']);

    $exc->drop($id);

    $sql = "DELETE FROM " .$ecs->table('package_goods') .
            " WHERE package_id='$id'";
    $db->query($sql);

    $url = 'package.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}

/*------------------------------------------------------ */
//-- 活動列表
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'list')
{
    $smarty->assign('ur_here',      $_LANG['14_package_list']);
    $smarty->assign('action_link',  array('text' => $_LANG['package_add'], 'href'=>'package.php?act=add'));

    $packages = get_packagelist();

    $smarty->assign('package_list', $packages['packages']);
    $smarty->assign('filter',       $packages['filter']);
    $smarty->assign('record_count', $packages['record_count']);
    $smarty->assign('page_count',   $packages['page_count']);

    $sort_flag  = sort_flag($packages['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    $smarty->assign('full_page',    1);
    assign_query_info();
    $smarty->display('package_list.htm');
}

/*------------------------------------------------------ */
//-- 查詢、翻頁、排序
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'query')
{
    $packages = get_packagelist();

    $smarty->assign('package_list', $packages['packages']);
    $smarty->assign('filter',       $packages['filter']);
    $smarty->assign('record_count', $packages['record_count']);
    $smarty->assign('page_count',   $packages['page_count']);

    $sort_flag  = sort_flag($packages['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('package_list.htm'), '',
        array('filter' => $packages['filter'], 'page_count' => $packages['page_count']));
}

/*------------------------------------------------------ */
//-- 編輯活動名稱
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'edit_package_name')
{
    check_authz_json('package_manage');

    $id = intval($_POST['id']);
    $val = json_str_iconv(trim($_POST['val']));

    /* 檢查活動重名 */
    $sql = "SELECT COUNT(*) ".
           " FROM " . $ecs->table('goods_activity').
           " WHERE act_type='" . GAT_PACKAGE . "' AND act_name='$val' AND act_id <> '$id'" ;
    if ($db->getOne($sql))
    {
        make_json_error(sprintf($_LANG['package_exist'],  $val));
    }

    $exc->edit("act_name='$val'", $id);
    make_json_result(stripslashes($val));
}

/*------------------------------------------------------ */
//-- 搜索商品
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'search_goods')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;

    $filters = $json->decode($_GET['JSON']);

    $arr = get_goods_list($filters);

    $opt = array();
    foreach ($arr AS $key => $val)
    {
        $opt[$key] = array('value' => $val['goods_id'],
                        'text' => $val['goods_name'],
                        'data' => $val['shop_price']);

        $opt[$key]['products'] = get_good_products($val['goods_id']);
    }

    make_json_result($opt);
}

/*------------------------------------------------------ */
//-- 搜索商品，僅返回名稱及ID
/*------------------------------------------------------ */

//elseif ($_REQUEST['act'] == 'get_goods_list')
//{
//    include_once(ROOT_PATH . 'includes/cls_json.php');
//    $json = new JSON;
//
//    $filters = $json->decode($_GET['JSON']);
//
//    $arr = get_goods_list($filters);
//
//    $opt = array();
//    foreach ($arr AS $key => $val)
//    {
//        $opt[$key] = array('value' => $val['goods_id'],
//                        'text' => $val['goods_name'],
//                        'data' => $val['shop_price']);
//
//        $opt[$key]['products'] = get_good_products($val['goods_id']);
//    }
//
//    make_json_result($opt);
//}

/*------------------------------------------------------ */
//-- 增加一個商品
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'add_package_goods')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;

    check_authz_json('package_manage');

    $fittings   = $json->decode($_GET['add_ids']);
    $arguments  = $json->decode($_GET['JSON']);
    $package_id = $arguments[0];
    $number     = $arguments[1];

    foreach ($fittings AS $val)
    {
        $val_array = explode('_', $val);
        if (!isset($val_array[1]) || $val_array[1] <= 0)
        {
            $val_array[1] = 0;
        }

        $sql = "INSERT INTO " . $ecs->table('package_goods') . " (package_id, goods_id, product_id, goods_number, admin_id) " .
                "VALUES ('$package_id', '" . $val_array[0] . "', '" . $val_array[1] . "', '$number', '$_SESSION[admin_id]')";
        $db->query($sql, 'SILENT');
    }

    $arr = get_package_goods($package_id);
    $opt = array();

    foreach ($arr AS $val)
    {
        $opt[] = array('value'      => $val['g_p'],
                        'text'      => $val['goods_name'],
                        'data'      => '');
    }

    clear_cache_files();
    make_json_result($opt);
}

/*------------------------------------------------------ */
//-- 刪除一個商品
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'drop_package_goods')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;

    check_authz_json('package_manage');

    $fittings   = $json->decode($_GET['drop_ids']);
    $arguments  = $json->decode($_GET['JSON']);
    $package_id = $arguments[0];

    $goods  = array();
    $g_p    = array();
    foreach ($fittings AS $val)
    {
        $val_array = explode('_', $val);
        if (isset($val_array[1]) && $val_array[1] > 0)
        {
            $g_p['product_id'][] = $val_array[1];
            $g_p['goods_id'][] = $val_array[0];
        }
        else
        {
            $goods[] = $val_array[0];
        }
    }

    if (!empty($goods))
    {
        $sql = "DELETE FROM " .$ecs->table('package_goods') .
                " WHERE package_id='$package_id' AND " .db_create_in($goods, 'goods_id');
        if ($package_id == 0)
        {
            $sql .= " AND admin_id = '$_SESSION[admin_id]'";
        }
        $db->query($sql);
    }

    if (!empty($g_p))
    {
        $sql = "DELETE FROM " .$ecs->table('package_goods') .
                " WHERE package_id='$package_id' AND " .db_create_in($g_p['goods_id'], 'goods_id') . " AND " . db_create_in($g_p['product_id'], 'product_id');
        if ($package_id == 0)
        {
            $sql .= " AND admin_id = '$_SESSION[admin_id]'";
        }
        $db->query($sql);
    }

    $arr = get_package_goods($package_id);
    $opt = array();

    foreach ($arr AS $val)
    {
        $opt[] = array('value'      => $val['goods_id'],
                        'text'      => $val['goods_name'],
                        'data'      => '');
    }

    clear_cache_files();
    make_json_result($opt);
}


/**
 * 獲取活動列表
 *
 * @access  public
 *
 * @return void
 */
function get_packagelist()
{
    $result = get_filter();
    if ($result === false)
    {
        /* 查詢條件 */
        $filter['keywords']   = empty($_REQUEST['keywords']) ? '' : trim($_REQUEST['keywords']);
        if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
        {
            $filter['keywords'] = json_str_iconv($filter['keywords']);
        }
        $filter['sort_by']    = empty($_REQUEST['sort_by']) ? 'act_id' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

        $where = (!empty($filter['keywords'])) ? " AND act_name like '%". mysql_like_quote($filter['keywords']) ."%'" : '';

        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('goods_activity') .
               " WHERE act_type =" . GAT_PACKAGE . $where;
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        $filter = page_and_size($filter);

        /* 獲活動數據 */
        $sql = "SELECT act_id, act_name AS package_name, start_time, end_time, is_finished, ext_info ".
               " FROM " . $GLOBALS['ecs']->table('goods_activity') .
               " WHERE act_type = " . GAT_PACKAGE . $where .
               " ORDER by $filter[sort_by] $filter[sort_order] LIMIT ". $filter['start'] .", " . $filter['page_size'];

        $filter['keywords'] = stripslashes($filter['keywords']);
        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }

    $row = $GLOBALS['db']->getAll($sql);

    foreach ($row AS $key => $val)
    {
        $row[$key]['start_time'] = local_date($GLOBALS['_CFG']['time_format'], $val['start_time']);
        $row[$key]['end_time']   = local_date($GLOBALS['_CFG']['time_format'], $val['end_time']);
        $info = unserialize($row[$key]['ext_info']);
        unset($row[$key]['ext_info']);
        if ($info)
        {
            foreach ($info as $info_key => $info_val)
            {
                $row[$key][$info_key] = $info_val;
            }
        }
    }

    $arr = array('packages' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}

/**
 * 保存某禮包的商品
 * @param   int     $package_id
 * @return  void
 */
function handle_packagep_goods($package_id)
{
    $sql = "UPDATE " . $GLOBALS['ecs']->table('package_goods') . " SET " .
            " package_id = '$package_id' " .
            " WHERE package_id = '0'" .
            " AND admin_id = '$_SESSION[admin_id]'";
    $GLOBALS['db']->query($sql);
}

?>