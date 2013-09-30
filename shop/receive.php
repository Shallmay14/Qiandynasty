<?php

/**
 * ECSHOP 處理收回確認的頁面
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: receive.php 17063 2010-03-25 06:35:46Z liuhui $
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/* 取得參數 */
$order_id  = !empty($_REQUEST['id'])  ? intval($_REQUEST['id'])              : 0;  // 訂單號
$consignee = !empty($_REQUEST['con']) ? rawurldecode(trim($_REQUEST['con'])) : ''; // 收貨人

/* 查詢訂單信息 */
$sql   = 'SELECT * FROM ' . $ecs->table('order_info') . " WHERE order_id = '$order_id'";
$order = $db->getRow($sql);

if (empty($order))
{
    $msg = $_LANG['order_not_exists'];
}
/* 檢查訂單 */
elseif ($order['shipping_status'] == SS_RECEIVED)
{
    $msg = $_LANG['order_already_received'];
}
elseif ($order['shipping_status'] != SS_SHIPPED)
{
    $msg = $_LANG['order_invalid'];
}
elseif ($order['consignee'] != $consignee)
{
    $msg = $_LANG['order_invalid'];
}
else
{
    /* 修改訂單發貨狀態為“確認收貨” */
    $sql = "UPDATE " . $ecs->table('order_info') . " SET shipping_status = '" . SS_RECEIVED . "' WHERE order_id = '$order_id'";
    $db->query($sql);

    /* 記錄日誌 */
    order_action($order['order_sn'], $order['order_status'], SS_RECEIVED, $order['pay_status'], '', $_LANG['buyer']);

    $msg = $_LANG['act_ok'];
}

/* 顯示模板 */
assign_template();
$position = assign_ur_here();
$smarty->assign('page_title', $position['title']);    // 頁面標題
$smarty->assign('ur_here',    $position['ur_here']);  // 當前位置

$smarty->assign('categories', get_categories_tree()); // 分類樹
$smarty->assign('helps',      get_shop_help());       // 網店幫助

assign_dynamic('receive');

$smarty->assign('msg', $msg);
$smarty->display('receive.dwt');

?>