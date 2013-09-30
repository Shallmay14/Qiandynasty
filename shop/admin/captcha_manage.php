<?php

/**
 * ECSHOP
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發佈。
 * ============================================================================
 * $Author: liuhui $
 * $Id: captcha_manage.php 17063 2010-03-25 06:35:46Z liuhui $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/* 檢查權限 */
admin_priv('shop_config');

/*------------------------------------------------------ */
//-- 驗證碼設置
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'main')
{
    if (gd_version() == 0)
    {
        sys_msg($_LANG['captcha_note'], 1);
    }

    assign_query_info();
    $captcha = intval($_CFG['captcha']);

    $captcha_check = array();
    if ($captcha & CAPTCHA_REGISTER)
    {
        $captcha_check['register']          = 'checked="checked"';
    }
    if ($captcha & CAPTCHA_LOGIN)
    {
        $captcha_check['login']             = 'checked="checked"';
    }
    if ($captcha & CAPTCHA_COMMENT)
    {
        $captcha_check['comment']           = 'checked="checked"';
    }
    if ($captcha & CAPTCHA_ADMIN)
    {
        $captcha_check['admin']             = 'checked="checked"';
    }
    if ($captcha & CAPTCHA_MESSAGE)
    {
        $captcha_check['message']    = 'checked="checked"';
    }
    if ($captcha & CAPTCHA_LOGIN_FAIL)
    {
        $captcha_check['login_fail_yes']    = 'checked="checked"';
    }
    else
    {
        $captcha_check['login_fail_no']     = 'checked="checked"';
    }

    $smarty->assign('captcha',          $captcha_check);
    $smarty->assign('captcha_width',    $_CFG['captcha_width']);
    $smarty->assign('captcha_height',   $_CFG['captcha_height']);
    $smarty->assign('ur_here',          $_LANG['captcha_manage']);
    $smarty->display('captcha_manage.htm');
}

/*------------------------------------------------------ */
//-- 保存設置
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'save_config')
{
    $captcha = 0;
    $captcha = empty($_POST['captcha_register'])    ? $captcha : $captcha | CAPTCHA_REGISTER;
    $captcha = empty($_POST['captcha_login'])       ? $captcha : $captcha | CAPTCHA_LOGIN;
    $captcha = empty($_POST['captcha_comment'])     ? $captcha : $captcha | CAPTCHA_COMMENT;
    $captcha = empty($_POST['captcha_tag'])         ? $captcha : $captcha | CAPTCHA_TAG;
    $captcha = empty($_POST['captcha_admin'])       ? $captcha : $captcha | CAPTCHA_ADMIN;
    $captcha = empty($_POST['captcha_login_fail'])  ? $captcha : $captcha | CAPTCHA_LOGIN_FAIL;
    $captcha = empty($_POST['captcha_message'])     ? $captcha : $captcha | CAPTCHA_MESSAGE;

    $captcha_width = empty($_POST['captcha_width'])     ? 145 : intval($_POST['captcha_width']);
    $captcha_height = empty($_POST['captcha_height'])   ? 20 : intval($_POST['captcha_height']);

    $sql = "UPDATE " . $ecs->table('shop_config') . " SET value='$captcha' WHERE code='captcha'";
    $db->query($sql);
    $sql = "UPDATE " . $ecs->table('shop_config') . " SET value='$captcha_width' WHERE code='captcha_width'";
    $db->query($sql);
    $sql = "UPDATE " . $ecs->table('shop_config') . " SET value='$captcha_height' WHERE code='captcha_height'";
    $db->query($sql);

    clear_cache_files();

    sys_msg($_LANG['save_ok'], 0, array(array('href'=>'captcha_manage.php?act=main', 'text'=>$_LANG['captcha_manage'])));
}


?>