<?php

/**
 * ECSHOP 用戶中心
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: user.php 17063 2010-03-25 06:35:46Z liuhui $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

$act = !empty($_GET['act']) ? $_GET['act'] : 'login';

$smarty->assign('footer', get_footer());
/* 用戶登陸 */
if ($act == 'do_login')
{
    $user_name = !empty($_POST['username']) ? $_POST['username'] : '';
    $pwd = !empty($_POST['pwd']) ? $_POST['pwd'] : '';
    if (empty($user_name) || empty($pwd))
    {
        $login_faild = 1;
    }
    else
    {
        if ($user->check_user($user_name, $pwd) > 0)
        {
            $user->set_session($user_name);
            show_user_center();
        }
        else
        {
            $login_faild = 1;
        }
    }

    if (!empty($login_faild))
    {
        $smarty->assign('login_faild', 1);
        $smarty->display('login.wml');
    }
}

elseif ($act == 'order_list')
{
    $record_count = $db->getOne("SELECT COUNT(*) FROM " .$ecs->table('order_info'). " WHERE user_id = {$_SESSION['user_id']}");
    if ($record_count > 0)
    {
        include_once(ROOT_PATH . 'includes/lib_transaction.php');
        $page_num = '10';
        $page = !empty($_GET['page']) ? intval($_GET['page']) : 1;
        $pages = ceil($record_count / $page_num);
        if ($page <= 0)
        {
            $page = 1;
        }
        if ($pages == 0)
        {
            $pages = 1;
        }
        if ($page > $pages)
        {
            $page = $pages;
        }
        $pagebar = get_wap_pager($record_count, $page_num, $page, 'user.php?act=order_list', 'page');
        $smarty->assign('pagebar' , $pagebar);
        /* 訂單狀態 */
        $_LANG['os'][OS_UNCONFIRMED] = '未確認';
        $_LANG['os'][OS_CONFIRMED] = '已確認';
        $_LANG['os'][OS_CANCELED] = '已取消';
        $_LANG['os'][OS_INVALID] = '無效';
        $_LANG['os'][OS_RETURNED] = '退貨';
        $_LANG['ss'][SS_UNSHIPPED] = '未發貨';
        $_LANG['ss'][SS_SHIPPED] = '已發貨';
        $_LANG['ss'][SS_RECEIVED] = '收貨確認';
        $_LANG['ps'][PS_UNPAYED] = '未付款';
        $_LANG['ps'][PS_PAYING] = '付款中';
        $_LANG['ps'][PS_PAYED] = '已付款';
        $_LANG['confirm_cancel'] = '您確認要取消該訂單嗎？取消後此訂單將視為無效訂單';
        $_LANG['cancel'] = '取消訂單';
        $orders = get_user_orders($_SESSION['user_id'], $page_num, ($page_num * ($page - 1)));
        if (!empty($orders))
        {
            foreach ($orders as $key => $val)
            {
                $orders[$key]['total_fee'] = encode_output($val['total_fee']);
            }
        }
        //$merge  = get_user_merge($_SESSION['user_id']);
        $smarty->assign('orders', $orders);
    }

    $smarty->display('order_list.wml');
}

/* 用戶中心 */
else
{
    if ($_SESSION['user_id'] > 0)
    {
        show_user_center();
    }
    else
    {
        $smarty->display('login.wml');
    }
}

/**
 * 用戶中心顯示
 */
function show_user_center()
{
    $best_goods = get_recommend_goods('best');
    if (count($best_goods) > 0)
    {
        foreach  ($best_goods as $key => $best_data)
        {
            $best_goods[$key]['shop_price'] = encode_output($best_data['shop_price']);
            $best_goods[$key]['name'] = encode_output($best_data['name']);
        }
    }
    $GLOBALS['smarty']->assign('best_goods' , $best_goods);
    $GLOBALS['smarty']->display('user.wml');
}

?>