<?php

/**
 * ECSHOP 配送方式管理程序
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發佈。
 * ============================================================================
 * $Author: liuhui $
 * $Id: shipping.php 17063 2010-03-25 06:35:46Z liuhui $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
$exc = new exchange($ecs->table('shipping'), $db, 'shipping_code', 'shipping_name');

/*------------------------------------------------------ */
//-- 配送方式列表
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'list')
{
    $modules = read_modules('../includes/modules/shipping');

    for ($i = 0; $i < count($modules); $i++)
    {
        $lang_file = ROOT_PATH.'languages/' .$_CFG['lang']. '/shipping/' .$modules[$i]['code']. '.php';

        if (file_exists($lang_file))
        {
            include_once($lang_file);
        }

        /* 檢查該插件是否已經安裝 */
        $sql = "SELECT shipping_id, shipping_name, shipping_desc, insure, support_cod FROM " .$ecs->table('shipping'). " WHERE shipping_code='" .$modules[$i]['code']. "'";
        $row = $db->GetRow($sql);

        if ($row)
        {
            /* 插件已經安裝了，獲得名稱以及描述 */
            $modules[$i]['id']      = $row['shipping_id'];
            $modules[$i]['name']    = $row['shipping_name'];
            $modules[$i]['desc']    = $row['shipping_desc'];
            $modules[$i]['insure_fee']  = $row['insure'];
            $modules[$i]['cod']     = $row['support_cod'];
            $modules[$i]['install'] = 1;

            if (isset($modules[$i]['insure']) && ($modules[$i]['insure'] === false))
            {
                $modules[$i]['is_insure']  = 0;
            }
            else
            {
                $modules[$i]['is_insure']  = 1;
            }
        }
        else
        {
            $modules[$i]['name']    = $_LANG[$modules[$i]['code']];
            $modules[$i]['desc']    = $_LANG[$modules[$i]['desc']];
            $modules[$i]['insure_fee']  = empty($modules[$i]['insure'])? 0 : $modules[$i]['insure'];
            $modules[$i]['cod']     = $modules[$i]['cod'];
            $modules[$i]['install'] = 0;
        }
    }

    $smarty->assign('ur_here', $_LANG['03_shipping_list']);
    $smarty->assign('modules', $modules);
    assign_query_info();
    $smarty->display('shipping_list.htm');
}

/*------------------------------------------------------ */
//-- 安裝配送方式
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'install')
{
    admin_priv('ship_manage');

    $set_modules = true;
    include_once(ROOT_PATH . 'includes/modules/shipping/' . $_GET['code'] . '.php');

    /* 檢查該配送方式是否已經安裝 */
    $sql = "SELECT shipping_id FROM " .$ecs->table('shipping'). " WHERE shipping_code = '$_GET[code]'";
    $id = $db->GetOne($sql);

    if ($id > 0)
    {
        /* 該配送方式已經安裝過, 將該配送方式的狀態設置為 enable */
        $db->query("UPDATE " .$ecs->table('shipping'). " SET enabled = 1 WHERE shipping_code = '$_GET[code]' LIMIT 1");
    }
    else
    {
        /* 該配送方式沒有安裝過, 將該配送方式的信息添加到數據庫 */
        $insure = empty($modules[0]['insure']) ? 0 : $modules[0]['insure'];
        $sql = "INSERT INTO " . $ecs->table('shipping') . " (" .
                    "shipping_code, shipping_name, shipping_desc, insure, support_cod, enabled, print_bg, config_lable, print_model" .
                ") VALUES (" .
                    "'" . addslashes($modules[0]['code']). "', '" . addslashes($_LANG[$modules[0]['code']]) . "', '" .
                    addslashes($_LANG[$modules[0]['desc']]) . "', '$insure', '" . intval($modules[0]['cod']) . "', 1, '" . addslashes($modules[0]['print_bg']) . "', '" . addslashes($modules[0]['config_lable']) . "', '" . $modules[0]['print_model'] . "')";
        $db->query($sql);
        $id = $db->insert_Id();
    }

    /* 記錄管理員操作 */
    admin_log(addslashes($_LANG[$modules[0]['code']]), 'install', 'shipping');

    /* 提示信息 */
    $lnk[] = array('text' => $_LANG['add_shipping_area'], 'href' => 'shipping_area.php?act=add&shipping=' . $id);
    $lnk[] = array('text' => $_LANG['go_back'], 'href' => 'shipping.php?act=list');
    sys_msg(sprintf($_LANG['install_succeess'], $_LANG[$modules[0]['code']]), 0, $lnk);
}

/*------------------------------------------------------ */
//-- 卸載配送方式
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'uninstall')
{
    global $ecs, $_LANG;

    admin_priv('ship_manage');

    /* 獲得該配送方式的ID */
    $row = $db->GetRow("SELECT shipping_id, shipping_name, print_bg FROM " .$ecs->table('shipping'). " WHERE shipping_code='$_GET[code]'");
    $shipping_id = $row['shipping_id'];
    $shipping_name = $row['shipping_name'];

    /* 刪除 shipping_fee 以及 shipping 表中的數據 */
    if ($row)
    {
        $all = $db->getCol("SELECT shipping_area_id FROM " .$ecs->table('shipping_area'). " WHERE shipping_id='$shipping_id'");
        $in  = db_create_in(join(',', $all));

        $db->query("DELETE FROM " .$ecs->table('area_region'). " WHERE shipping_area_id $in");
        $db->query("DELETE FROM " .$ecs->table('shipping_area'). " WHERE shipping_id='$shipping_id'");
        $db->query("DELETE FROM " .$ecs->table('shipping'). " WHERE shipping_id='$shipping_id'");

        //刪除上傳的非默認快遞單
        if (($row['print_bg'] != '') && (!is_print_bg_default($row['print_bg'])))
        {
            @unlink(ROOT_PATH . $row['print_bg']);
        }

        //記錄管理員操作
        admin_log(addslashes($shipping_name), 'uninstall', 'shipping');

        $lnk[] = array('text' => $_LANG['go_back'], 'href'=>'shipping.php?act=list');
        sys_msg(sprintf($_LANG['uninstall_success'], $shipping_name), 0, $lnk);
    }
}

/*------------------------------------------------------ */
//-- 模板Flash編輯器
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'print_index')
{
    //檢查登錄權限
    admin_priv('ship_manage');

    $shipping_id = !empty($_GET['shipping']) ? intval($_GET['shipping']) : 0;

    /* 檢查該插件是否已經安裝 取值 */
    $sql = "SELECT * FROM " .$ecs->table('shipping'). " WHERE shipping_id = '$shipping_id' LIMIT 0,1";
    $row = $db->GetRow($sql);
    if ($row)
    {
        include_once(ROOT_PATH . 'includes/modules/shipping/' . $row['shipping_code'] . '.php');
        $row['shipping_print'] = !empty($row['shipping_print']) ? $row['shipping_print'] : '';
        $row['print_bg'] = empty($row['print_bg']) ? '' : get_site_root_url() . $row['print_bg'];
    }
    $smarty->assign('shipping', $row);
    $smarty->assign('shipping_id', $shipping_id);

    $smarty->display('print_index.htm');
}

/*------------------------------------------------------ */
//-- 模板Flash編輯器
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'recovery_default_template')
{
    /* 檢查登錄權限 */
    admin_priv('ship_manage');

    $shipping_id = !empty($_POST['shipping']) ? intval($_POST['shipping']) : 0;

    /* 取配送代碼 */
    $sql = "SELECT shipping_code FROM " .$ecs->table('shipping'). " WHERE shipping_id = '$shipping_id'";
    $code = $db->GetOne($sql);

    $set_modules = true;
    include_once(ROOT_PATH . 'includes/modules/shipping/' . $code . '.php');

    /* 恢復默認 */
    $db->query("UPDATE " .$ecs->table('shipping'). " SET print_bg = '" . addslashes($modules[0]['print_bg']) . "',  config_lable = '" . addslashes($modules[0]['config_lable']) . "' WHERE shipping_code = '$code' LIMIT 1");

    $url = "shipping.php?act=edit_print_template&shipping=$shipping_id";
    ecs_header("Location: $url\n");
}

/*------------------------------------------------------ */
//-- 模板Flash編輯器 上傳圖片
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'print_upload')
{
    //檢查登錄權限
    admin_priv('ship_manage');

    //設置上傳文件類型
    $allow_suffix = array('jpg', 'png', 'jpeg');

    $shipping_id = !empty($_POST['shipping']) ? intval($_POST['shipping']) : 0;

    //接收上傳文件
    if (!empty($_FILES['bg']['name']))
    {
        if(!get_file_suffix($_FILES['bg']['name'], $allow_suffix))
        {
            echo '<script language="javascript">';
            echo 'parent.alert("' . sprintf($_LANG['js_languages']['upload_falid'], implode('，', $allow_suffix)) . '");';
            echo '</script>';
            exit;
        }

        $name = date('Ymd');
        for ($i = 0; $i < 6; $i++)
        {
            $name .= chr(mt_rand(97, 122));
        }
        $name .= '.' . end(explode('.', $_FILES['bg']['name']));
        $target = ROOT_PATH . '/images/receipt/' . $name;

        if (move_upload_file($_FILES['bg']['tmp_name'], $target))
        {
            $src = '/images/receipt/' . $name;
        }
    }

    //保存
    $sql = "UPDATE " .$ecs->table('shipping'). " SET print_bg = '$src' WHERE shipping_id = '$shipping_id'";
    $res = $db->query($sql);
    if ($res)
    {
        echo '<script language="javascript">';
        echo 'parent.call_flash("bg_add", "' . get_site_root_url() . $src . '");';
        echo '</script>';
    }
}

/*------------------------------------------------------ */
//-- 模板Flash編輯器 刪除圖片
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'print_del')
{
    /* 檢查權限 */
    check_authz_json('ship_manage');

    $shipping_id = !empty($_GET['shipping']) ? intval($_GET['shipping']) : 0;
    $shipping_id = json_str_iconv($shipping_id);

    /* 檢查該插件是否已經安裝 取值 */
    $sql = "SELECT print_bg FROM " .$ecs->table('shipping'). " WHERE shipping_id = '$shipping_id' LIMIT 0,1";
    $row = $db->GetRow($sql);
    if ($row)
    {
        if (($row['print_bg'] != '') && (!is_print_bg_default($row['print_bg'])))
        {
            @unlink(ROOT_PATH . $row['print_bg']);
        }

        $sql = "UPDATE " .$ecs->table('shipping'). " SET print_bg = '' WHERE shipping_id = '$shipping_id'";
        $res = $db->query($sql);
    }
    else
    {
        make_json_error($_LANG['js_languages']['upload_del_falid']);
    }

    make_json_result($shipping_id);
}

/*------------------------------------------------------ */
//-- 編輯打印模板
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'edit_print_template')
{
    admin_priv('ship_manage');

    $shipping_id = !empty($_GET['shipping']) ? intval($_GET['shipping']) : 0;

    /* 檢查該插件是否已經安裝 */
    $sql = "SELECT * FROM " .$ecs->table('shipping'). " WHERE shipping_id=$shipping_id";
    $row = $db->GetRow($sql);
    if ($row)
    {
        include_once(ROOT_PATH . 'includes/modules/shipping/' . $row['shipping_code'] . '.php');
        $row['shipping_print'] = !empty($row['shipping_print']) ? $row['shipping_print'] : '';
        $row['print_model'] = empty($row['print_model']) ? 1 : $row['print_model']; //兼容以前版本

        $smarty->assign('shipping', $row);
    }
    else
    {
        $lnk[] = array('text' => $_LANG['go_back'], 'href'=>'shipping.php?act=list');
        sys_msg($_LANG['no_shipping_install'] , 0, $lnk);
    }

    $smarty->assign('ur_here', $_LANG['03_shipping_list'] .' - '. $row['shipping_name'] .' - '. $_LANG['shipping_print_template']);
    $smarty->assign('action_link', array('text' => $_LANG['03_shipping_list'], 'href' => 'shipping.php?act=list'));
    $smarty->assign('shipping_id', $shipping_id);

    assign_query_info();

    $smarty->display('shipping_template.htm');
}

/*------------------------------------------------------ */
//-- 編輯打印模板
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'do_edit_print_template')
{
    /* 檢查權限 */
    admin_priv('ship_manage');

    /* 參數處理 */
    $print_model = !empty($_POST['print_model']) ? intval($_POST['print_model']) : 0;
    $shipping_id = !empty($_REQUEST['shipping']) ? intval($_REQUEST['shipping']) : 0;

    /* 處理不同模式編輯的表單 */
    if ($print_model == 2)
    {
        //所見即所得模式
        $db->query("UPDATE " . $ecs->table('shipping'). " SET config_lable = '" . $_POST['config_lable'] . "', print_model = '$print_model'  WHERE shipping_id = '$shipping_id'");
    }
    elseif ($print_model == 1)
    {
        //代碼模式
        $template = !empty($_POST['shipping_print']) ? $_POST['shipping_print'] : '';

        $db->query("UPDATE " . $ecs->table('shipping'). " SET shipping_print = '" . $template . "', print_model = '$print_model' WHERE shipping_id = '$shipping_id'");
    }

    /* 記錄管理員操作 */
    admin_log(addslashes($_POST['shipping_name']), 'edit', 'shipping');

    $lnk[] = array('text' => $_LANG['go_back'], 'href'=>'shipping.php?act=list');
    sys_msg($_LANG['edit_template_success'], 0, $lnk);

}

/*------------------------------------------------------ */
//-- 編輯配送方式名稱
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'edit_name')
{
    /* 檢查權限 */
    check_authz_json('ship_manage');

    /* 取得參數 */
    $id  = json_str_iconv(trim($_POST['id']));
    $val = json_str_iconv(trim($_POST['val']));

    /* 檢查名稱是否為空 */
    if (empty($val))
    {
        make_json_error($_LANG['no_shipping_name']);
    }

    /* 檢查名稱是否重複 */
    if (!$exc->is_only('shipping_name', $val, $id))
    {
        make_json_error($_LANG['repeat_shipping_name']);
    }

    /* 更新支付方式名稱 */
    $exc->edit("shipping_name = '$val'", $id);
    make_json_result(stripcslashes($val));
}

/*------------------------------------------------------ */
//-- 編輯配送方式描述
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'edit_desc')
{
    /* 檢查權限 */
    check_authz_json('ship_manage');

    /* 取得參數 */
    $id = json_str_iconv(trim($_POST['id']));
    $val = json_str_iconv(trim($_POST['val']));

    /* 更新描述 */
    $exc->edit("shipping_desc = '$val'", $id);
    make_json_result(stripcslashes($val));
}

/*------------------------------------------------------ */
//-- 修改配送方式保價費
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'edit_insure')
{
    /* 檢查權限 */
    check_authz_json('ship_manage');

    /* 取得參數 */
    $id = json_str_iconv(trim($_POST['id']));
    $val = json_str_iconv(trim($_POST['val']));
    if (empty($val))
    {
        $val = 0;
    }
    else
    {
        $val = make_semiangle($val); //全角轉半角
        if (strpos($val, '%') === false)
        {
            $val = floatval($val);
        }
        else
        {
            $val = floatval($val) . '%';
        }
    }

    /* 檢查該插件是否支持保價 */
    $set_modules = true;
    include_once(ROOT_PATH . 'includes/modules/shipping/' .$id. '.php');
    if (isset($modules[0]['insure']) && $modules[0]['insure'] === false)
    {
        make_json_error($_LANG['not_support_insure']);
    }

    /* 更新保價費用 */
    $exc->edit("insure = '$val'", $id);
    make_json_result(stripcslashes($val));
}
elseif($_REQUEST['act'] == 'shipping_priv')
{
    check_authz_json('ship_manage');

    make_json_result('');
}

/**
 * 獲取站點根目錄網址
 *
 * @access  private
 * @return  Bool
 */
function get_site_root_url()
{
    return 'http://' . $_SERVER['HTTP_HOST'] . str_replace('/' . ADMIN_PATH . '/shipping.php', '', PHP_SELF);

}

/**
 * 判斷是否為默認安裝快遞單背景圖片
 *
 * @param   string      $print_bg      快遞單背景圖片路徑名
 * @access  private
 *
 * @return  Bool
 */
function is_print_bg_default($print_bg)
{
    $_bg = basename($print_bg);

    $_bg_array = explode('.', $_bg);

    if (count($_bg_array) != 2)
    {
        return false;
    }

    if (strpos('|' . $_bg_array[0], 'dly_') != 1)
    {
        return false;
    }

    $_bg_array[0] = ltrim($_bg_array[0], 'dly_');
    $list = explode('|', SHIP_LIST);

    if (in_array($_bg_array[0], $list))
    {
        return true;
    }

    return false;
}
?>