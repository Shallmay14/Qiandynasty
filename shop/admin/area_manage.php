<?php

/**
 * ECSHOP 地區列表管理文件
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發佈。
 * ============================================================================
 * $Author: liuhui $
 * $Id: area_manage.php 17063 2010-03-25 06:35:46Z liuhui $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
$exc = new exchange($ecs->table('region'), $db, 'region_id', 'region_name');

/* act操作項的初始化 */
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'list';
}
else
{
    $_REQUEST['act'] = trim($_REQUEST['act']);
}

/*------------------------------------------------------ */
//-- 列出某地區下的所有地區列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    admin_priv('area_manage');

    /* 取得參數：上級地區id */
    $region_id = empty($_REQUEST['pid']) ? 0 : intval($_REQUEST['pid']);
    $smarty->assign('parent_id',    $region_id);

    /* 取得列表顯示的地區的類型 */
    if ($region_id == 0)
    {
        $region_type = 0;
    }
    else
    {
        $region_type = $exc->get_name($region_id, 'region_type') + 1;
    }
    $smarty->assign('region_type',  $region_type);

    /* 獲取地區列表 */
    $region_arr = area_list($region_id);
    $smarty->assign('region_arr',   $region_arr);

    /* 當前的地區名稱 */
    if ($region_id > 0)
    {
        $area_name = $exc->get_name($region_id);
        $area = '[ '. $area_name . ' ] ';
        if ($region_arr)
        {
            $area .= $region_arr[0]['type'];
        }
    }
    else
    {
        $area = $_LANG['country'];
    }
    $smarty->assign('area_here',    $area);

    /* 返回上一級的鏈接 */
    if ($region_id > 0)
    {
        $parent_id = $exc->get_name($region_id, 'parent_id');
        $action_link = array('text' => $_LANG['back_page'], 'href' => 'area_manage.php?act=list&&pid=' . $parent_id);
    }
    else
    {
        $action_link = '';
    }
    $smarty->assign('action_link',  $action_link);

    /* 賦值模板顯示 */
    $smarty->assign('ur_here',      $_LANG['05_area_list']);
    $smarty->assign('full_page',    1);

    assign_query_info();
    $smarty->display('area_list.htm');
}

/*------------------------------------------------------ */
//-- 添加新的地區
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'add_area')
{
    check_authz_json('area_manage');

    $parent_id      = intval($_POST['parent_id']);
    $region_name    = json_str_iconv(trim($_POST['region_name']));
    $region_type    = intval($_POST['region_type']);

    if (empty($region_name))
    {
        make_json_error($_LANG['region_name_empty']);
    }

    /* 查看區域是否重複 */
    if (!$exc->is_only('region_name', $region_name, 0, "parent_id = '$parent_id'"))
    {
        make_json_error($_LANG['region_name_exist']);
    }

    $sql = "INSERT INTO " . $ecs->table('region') . " (parent_id, region_name, region_type) ".
           "VALUES ('$parent_id', '$region_name', '$region_type')";
    if ($GLOBALS['db']->query($sql, 'SILENT'))
    {
        admin_log($region_name, 'add','area');

        /* 獲取地區列表 */
        $region_arr = area_list($parent_id);
        $smarty->assign('region_arr',   $region_arr);

        $smarty->assign('region_type', $region_type);

        make_json_result($smarty->fetch('area_list.htm'));
    }
    else
    {
        make_json_error($_LANG['add_area_error']);
    }
}

/*------------------------------------------------------ */
//-- 編輯區域名稱
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'edit_area_name')
{
    check_authz_json('area_manage');

    $id = intval($_POST['id']);
    $region_name = json_str_iconv(trim($_POST['val']));

    if (empty($region_name))
    {
        make_json_error($_LANG['region_name_empty']);
    }

    $msg = '';

    /* 查看區域是否重複 */
    $parent_id = $exc->get_name($id, 'parent_id');
    if (!$exc->is_only('region_name', $region_name, $id, "parent_id = '$parent_id'"))
    {
        make_json_error($_LANG['region_name_exist']);
    }

    if ($exc->edit("region_name = '$region_name'", $id))
    {
        admin_log($region_name, 'edit', 'area');
        make_json_result(stripslashes($region_name));
    }
    else
    {
        make_json_error($db->error());
    }
}

/*------------------------------------------------------ */
//-- 刪除區域
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'drop_area')
{
    check_authz_json('area_manage');

    $id = intval($_REQUEST['id']);

    $sql = "SELECT * FROM " . $ecs->table('region') . " WHERE region_id = '$id'";
    $region = $db->getRow($sql);

    /* 如果底下有下級區域,不能刪除 */
    $sql = "SELECT COUNT(*) FROM " . $ecs->table('region') . " WHERE parent_id = '$id'";
    if ($db->getOne($sql) > 0)
    {
        make_json_error($_LANG['parent_id_exist']);
    }

    if ($exc->drop($id))
    {
        admin_log(addslashes($region['region_name']), 'remove', 'area');

        /* 獲取地區列表 */
        $region_arr = area_list($region['parent_id']);
        $smarty->assign('region_arr',   $region_arr);
        $smarty->assign('region_type', $region['region_type']);

        make_json_result($smarty->fetch('area_list.htm'));
    }
    else
    {
        make_json_error($db->error());
    }
}

?>