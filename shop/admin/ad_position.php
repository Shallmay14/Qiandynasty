<?php

/**
 * ECSHOP 廣告位置管理程序
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發佈。
 * ============================================================================
 * $Author: liuhui $
 * $Id: ad_position.php 17063 2010-03-25 06:35:46Z liuhui $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/admin/ads.php');

/* act操作項的初始化 */
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'list';
}
else
{
    $_REQUEST['act'] = trim($_REQUEST['act']);
}

$smarty->assign('lang', $_LANG);
$exc = new exchange($ecs->table("ad_position"), $db, 'position_id', 'position_name');

/*------------------------------------------------------ */
//-- 廣告位置列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    $smarty->assign('ur_here',     $_LANG['ad_position']);
    $smarty->assign('action_link', array('text' => $_LANG['position_add'], 'href' => 'ad_position.php?act=add'));
    $smarty->assign('full_page',   1);

    $position_list = ad_position_list();

    $smarty->assign('position_list',   $position_list['position']);
    $smarty->assign('filter',          $position_list['filter']);
    $smarty->assign('record_count',    $position_list['record_count']);
    $smarty->assign('page_count',      $position_list['page_count']);

    assign_query_info();
    $smarty->display('ad_position_list.htm');
}

/*------------------------------------------------------ */
//-- 添加廣告位頁面
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add')
{
    admin_priv('ad_manage');

    /* 模板賦值 */
    $smarty->assign('ur_here',     $_LANG['position_add']);
    $smarty->assign('form_act',    'insert');

    $smarty->assign('action_link', array('href' => 'ad_position.php?act=list', 'text' => $_LANG['ad_position']));
    $smarty->assign('posit_arr',   array('position_style' => '<table cellpadding="0" cellspacing="0">' ."\n". '{foreach from=$ads item=ad}' ."\n". '<tr><td>{$ad}</td></tr>' ."\n". '{/foreach}' ."\n". '</table>'));

    assign_query_info();
    $smarty->display('ad_position_info.htm');
}
elseif ($_REQUEST['act'] == 'insert')
{
    admin_priv('ad_manage');

    /* 對POST上來的值進行處理並去除空格 */
    $position_name = !empty($_POST['position_name']) ? trim($_POST['position_name']) : '';
    $position_desc = !empty($_POST['position_desc']) ? nl2br(htmlspecialchars($_POST['position_desc'])) : '';
    $ad_width      = !empty($_POST['ad_width'])      ? intval($_POST['ad_width'])  : 0;
    $ad_height     = !empty($_POST['ad_height'])     ? intval($_POST['ad_height']) : 0;

    /* 查看廣告位是否有重複 */
    if ($exc->num("position_name", $position_name) == 0)
    {
        /* 將廣告位置的信息插入數據表 */
        $sql = 'INSERT INTO '.$ecs->table('ad_position').' (position_name, ad_width, ad_height, position_desc, position_style) '.
               "VALUES ('$position_name', '$ad_width', '$ad_height', '$position_desc', '$_POST[position_style]')";

        $db->query($sql);
        /* 記錄管理員操作 */
        admin_log($position_name, 'add', 'ads_position');

        /* 提示信息 */
        $link[0]['text'] = $_LANG['ads_add'];
        $link[0]['href'] = 'ads.php?act=add';

        $link[1]['text'] = $_LANG['continue_add_position'];
        $link[1]['href'] = 'ad_position.php?act=add';

        $link[2]['text'] = $_LANG['back_position_list'];
        $link[2]['href'] = 'ad_position.php?act=list';

        sys_msg($_LANG['add'] . "&nbsp;" . stripslashes($position_name) . "&nbsp;" . $_LANG['attradd_succed'], 0, $link);
    }
    else
    {
        $link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
        sys_msg($_LANG['posit_name_exist'], 0, $link);
    }
}

/*------------------------------------------------------ */
//-- 廣告位編輯頁面
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit')
{
    admin_priv('ad_manage');

    $id = !empty($_GET['id']) ? intval($_GET['id']) : 0;

    /* 獲取廣告位數據 */
    $sql = 'SELECT * FROM ' .$ecs->table('ad_position'). " WHERE position_id='$id'";
    $posit_arr = $db->getRow($sql);

    $smarty->assign('ur_here',     $_LANG['position_edit']);
    $smarty->assign('action_link', array('href' => 'ad_position.php?act=list', 'text' => $_LANG['ad_position']));
    $smarty->assign('posit_arr',   $posit_arr);
    $smarty->assign('form_act',    'update');

    assign_query_info();
    $smarty->display('ad_position_info.htm');
}
elseif ($_REQUEST['act'] == 'update')
{
    admin_priv('ad_manage');

    /* 對POST上來的值進行處理並去除空格 */
    $position_name = !empty($_POST['position_name']) ? trim($_POST['position_name']) : '';
    $position_desc = !empty($_POST['position_desc']) ? nl2br(htmlspecialchars($_POST['position_desc'])) : '';
    $ad_width      = !empty($_POST['ad_width'])      ? intval($_POST['ad_width'])  : 0;
    $ad_height     = !empty($_POST['ad_height'])     ? intval($_POST['ad_height']) : 0;
    $position_id   = !empty($_POST['id'])            ? intval($_POST['id'])        : 0;
    /* 查看廣告位是否與其它有重複 */
    $sql = 'SELECT COUNT(*) FROM ' .$ecs->table('ad_position').
           " WHERE position_name = '$position_name' AND position_id <> '$position_id'";
    if ($db->getOne($sql) == 0)
    {
        $sql = "UPDATE " .$ecs->table('ad_position'). " SET ".
               "position_name    = '$position_name', ".
               "ad_width         = '$ad_width', ".
               "ad_height        = '$ad_height', ".
               "position_desc    = '$position_desc', ".
               "position_style   = '$_POST[position_style]' ".
               "WHERE position_id = '$position_id'";
        if ($db->query($sql))
        {
           /* 記錄管理員操作 */
           admin_log($position_name, 'edit', 'ads_position');

           /* 清除緩存 */
           clear_cache_files();

           /* 提示信息 */
           $link[] = array('text' => $_LANG['back_position_list'], 'href' => 'ad_position.php?act=list');
           sys_msg($_LANG['edit'] . ' ' .stripslashes($position_name).' '. $_LANG['attradd_succed'], 0, $link);
        }
    }
    else
    {
        $link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
        sys_msg($_LANG['posit_name_exist'], 0, $link);
    }
}

/*------------------------------------------------------ */
//-- 排序、分頁、查詢
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $position_list = ad_position_list();

    $smarty->assign('position_list',   $position_list['position']);
    $smarty->assign('filter',          $position_list['filter']);
    $smarty->assign('record_count',    $position_list['record_count']);
    $smarty->assign('page_count',      $position_list['page_count']);

    make_json_result($smarty->fetch('ad_position_list.htm'), '',
        array('filter' => $position_list['filter'], 'page_count' => $position_list['page_count']));
}

/*------------------------------------------------------ */
//-- 編輯廣告位置名稱
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_position_name')
{
    check_authz_json('ad_manage');

    $id     = intval($_POST['id']);
    $position_name   = json_str_iconv(trim($_POST['val']));

    /* 檢查名稱是否重複 */
    if ($exc->num("position_name", $position_name, $id) != 0)
    {
        make_json_error(sprintf($_LANG['posit_name_exist'], $position_name));
    }
    else
    {
        if ($exc->edit("position_name = '$position_name'", $id))
        {
            admin_log($position_name,'edit','ads_position');
            make_json_result(stripslashes($position_name));
        }
        else
        {
            make_json_result(sprintf($_LANG['brandedit_fail'], $position_name));
        }
    }
}

/*------------------------------------------------------ */
//-- 編輯廣告位寬高
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_ad_width')
{
    check_authz_json('ad_manage');

    $id         = intval($_POST['id']);
    $ad_width   = json_str_iconv(trim($_POST['val']));

    /* 寬度值必須是數字 */
    if (!preg_match("/^[\.0-9]+$/",$ad_width))
    {
        make_json_error($_LANG['width_number']);
    }

    /* 廣告位寬度應在1-1024之間 */
    if ($ad_width > 1024 || $ad_width < 1)
    {
        make_json_error($_LANG['width_value']);
    }

    if ($exc->edit("ad_width = '$ad_width'", $id))
    {
        clear_cache_files(); // 清除模版緩存
        admin_log($ad_width, 'edit', 'ads_position');
        make_json_result(stripslashes($ad_width));
    }
    else
    {
        make_json_error($db->error());
    }
}

/*------------------------------------------------------ */
//-- 編輯廣告位寬高
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_ad_height')
{
    check_authz_json('ad_manage');

    $id         = intval($_POST['id']);
    $ad_height  = json_str_iconv(trim($_POST['val']));

    /* 高度值必須是數字 */
    if (!preg_match("/^[\.0-9]+$/",$ad_height))
    {
        make_json_error($_LANG['height_number']);
    }

    /* 廣告位寬度應在1-1024之間 */
    if ($ad_height > 1024 || $ad_height < 1)
    {
        make_json_error($_LANG['height_value']);
    }

    if ($exc->edit("ad_height = '$ad_height'", $id))
    {
        clear_cache_files(); // 清除模版緩存
        admin_log($ad_height, 'edit', 'ads_position');
        make_json_result(stripslashes($ad_height));
    }
    else
    {
        make_json_error($db->error());
    }
}

/*------------------------------------------------------ */
//-- 刪除廣告位置
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    check_authz_json('ad_manage');

    $id = intval($_GET['id']);

    /* 查詢廣告位下是否有廣告存在 */
    $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('ad'). " WHERE position_id = '$id'";

    if ($db->getOne($sql) > 0)
    {
        make_json_error($_LANG['not_del_adposit']);
    }
    else
    {
        $exc->drop($id);
        admin_log('', 'remove', 'ads_position');
    }

    $url = 'ad_position.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}

/* 獲取廣告位置列表 */
function ad_position_list()
{
    $filter = array();

    /* 記錄總數以及頁數 */
    $sql = 'SELECT COUNT(*) FROM ' . $GLOBALS['ecs']->table('ad_position');
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);

    $filter = page_and_size($filter);

    /* 查詢數據 */
    $arr = array();
    $sql = 'SELECT * FROM ' .$GLOBALS['ecs']->table('ad_position'). ' ORDER BY position_id DESC';
    $res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);
    while ($rows = $GLOBALS['db']->fetchRow($res))
    {
        $position_desc = !empty($rows['position_desc']) ? sub_str($rows['position_desc'], 50, true) : '';
        $rows['position_desc'] = nl2br(htmlspecialchars($position_desc));

        $arr[] = $rows;
    }

    return array('position' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}

?>