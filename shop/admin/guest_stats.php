<?php

/**
 * ECSHOP 客戶統計
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發佈。
 * ============================================================================
 * $Author: liuhui $
 * $Id: guest_stats.php 17063 2010-03-25 06:35:46Z liuhui $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/lib_order.php');
require_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/admin/statistic.php');

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
//-- 客戶統計列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    /* 權限判斷 */
    admin_priv('client_flow_stats');

    /* 取得會員總數 */
    $users      =& init_users();
    $sql = "SELECT COUNT(*) FROM " . $ecs->table("users");
    $res = $db->getCol($sql);
    $user_num   = $res[0];


    /* 計算訂單各種費用之和的語句 */
    $total_fee = " SUM(" . order_amount_field() . ") AS turnover ";

    /* 有過訂單的會員數 */
    $sql = 'SELECT COUNT(DISTINCT user_id) FROM ' .$ecs->table('order_info').
           " WHERE user_id > 0 " . order_query_sql('finished');
    $have_order_usernum = $db->getOne($sql);

    /* 會員訂單總數和訂單總購物額 */
    $user_all_order = array();
    $sql = "SELECT COUNT(*) AS order_num, " . $total_fee.
           "FROM " .$ecs->table('order_info').
           " WHERE user_id > 0 " . order_query_sql('finished');
    $user_all_order = $db->getRow($sql);
    $user_all_order['turnover'] = floatval($user_all_order['turnover']);

    /* 匿名會員訂單總數和總購物額 */
    $guest_all_order = array();
    $sql = "SELECT COUNT(*) AS order_num, " . $total_fee.
           "FROM " .$ecs->table('order_info').
           " WHERE user_id = 0 " . order_query_sql('finished');
    $guest_all_order = $db->getRow($sql);

    /* 匿名會員平均訂單額: 購物總額/訂單數 */
    $guest_order_amount = ($guest_all_order['order_num'] > 0) ? floatval($guest_all_order['turnover'] / $guest_all_order['order_num']) : '0.00';

    $_GET['flag'] = isset($_GET['flag']) ? 'download' : '';
    if($_GET['flag'] == 'download')
    {
        $filename = ecs_iconv(EC_CHARSET, 'GB2312', $_LANG['guest_statistics']);

        header("Content-type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=$filename.xls");

        /* 生成會員購買率 */
        $data  = $_LANG['percent_buy_member'] . "\t\n";
        $data .= $_LANG['member_count'] . "\t" . $_LANG['order_member_count'] . "\t" .
                $_LANG['member_order_count'] . "\t" . $_LANG['percent_buy_member'] . "\n";

        $data .= $user_num . "\t" . $have_order_usernum . "\t" .
                $user_all_order['order_num'] . "\t" . sprintf("%0.2f", ($user_num > 0 ? $have_order_usernum / $user_num : 0) * 100) . "\n\n";

        /* 每會員平均訂單數及購物額 */
        $data .= $_LANG['order_turnover_peruser'] . "\t\n";

        $data .= $_LANG['member_sum'] . "\t" . $_LANG['average_member_order'] . "\t" .
                $_LANG['member_order_sum'] . "\n";

        $ave_user_ordernum = $user_num > 0 ? sprintf("%0.2f", $user_all_order['order_num'] / $user_num) : 0;
        $ave_user_turnover = $user_num > 0 ? price_format($user_all_order['turnover'] / $user_num) : 0;

        $data .= price_format($user_all_order['turnover']) . "\t" . $ave_user_ordernum . "\t" . $ave_user_turnover . "\n\n";

        /* 每會員平均訂單數及購物額 */
        $data .= $_LANG['order_turnover_percus'] . "\t\n";
        $data .= $_LANG['guest_member_orderamount'] . "\t" . $_LANG['guest_member_ordercount'] . "\t" .
                $_LANG['guest_order_sum'] . "\n";

        $order_num = $guest_all_order['order_num'] > 0 ? price_format($guest_all_order['turnover'] / $guest_all_order['order_num']) : 0;
        $data .= price_format($guest_all_order['turnover']) . "\t" . $guest_all_order['order_num'] . "\t" .
                $order_num;

        echo ecs_iconv(EC_CHARSET, 'GB2312', $data) . "\t";
        exit;
    }

    /* 賦值到模板 */
    $smarty->assign('user_num',            $user_num);                    // 會員總數
    $smarty->assign('have_order_usernum',  $have_order_usernum);          // 有過訂單的會員數
    $smarty->assign('user_order_turnover', $user_all_order['order_num']); // 會員總訂單數
    $smarty->assign('user_all_turnover',   price_format($user_all_order['turnover']));  //會員購物總額
    $smarty->assign('guest_all_turnover',  price_format($guest_all_order['turnover'])); //匿名會員購物總額
    $smarty->assign('guest_order_num',     $guest_all_order['order_num']);              //匿名會員訂單總數

    /* 每會員訂單數 */
    $smarty->assign('ave_user_ordernum',  $user_num > 0 ? sprintf("%0.2f", $user_all_order['order_num'] / $user_num) : 0);

    /* 每會員購物額 */
    $smarty->assign('ave_user_turnover',  $user_num > 0 ? price_format($user_all_order['turnover'] / $user_num) : 0);

    /* 註冊會員購買率 */
    $smarty->assign('user_ratio', sprintf("%0.2f", ($user_num > 0 ? $have_order_usernum / $user_num : 0) * 100));

     /* 匿名會員平均訂單額 */
    $smarty->assign('guest_order_amount', $guest_all_order['order_num'] > 0 ? price_format($guest_all_order['turnover'] / $guest_all_order['order_num']) : 0);

    $smarty->assign('all_order',          $user_all_order);    //所有訂單總數以及所有購物總額
    $smarty->assign('ur_here',            $_LANG['report_guest']);
    $smarty->assign('lang',               $_LANG);

    $smarty->assign('action_link',  array('text' => $_LANG['down_guest_stats'],
          'href'=>'guest_stats.php?flag=download'));

    assign_query_info();
    $smarty->display('guest_stats.htm');
}

?>