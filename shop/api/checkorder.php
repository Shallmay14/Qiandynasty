<?php

/**
 * ECSHOP 檢查訂單 API
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: checkorder.php 17200 2010-10-14 03:02:27Z liuhui $
*/

define('IN_ECS', true);

require('./init.php');
require_once(ROOT_PATH . 'includes/lib_order.php');
require_once('../includes/cls_json.php');

$json = new JSON;

$res = array('error' => 0, 'new_orders' => 0, 'new_paid' => 0);
$_REQUEST['username'] = urlencode(serialize(json_str_iconv($_REQUEST['username'])));

/* 檢查密碼是否正確 */
$sql = "SELECT COUNT(*) ".
        " FROM " . $ecs->table('admin_user') .
        " WHERE user_name = '" . trim($_REQUEST['username']). "' AND password = '" . md5(trim($_REQUEST['password'])) . "'";

if ($db->getOne($sql))
{
    /* 新訂單 */
    $sql = 'SELECT COUNT(*) FROM ' . $ecs->table('order_info').
            " WHERE order_status = " . OS_UNCONFIRMED;
    $res['new_orders'] = $db->getOne($sql);

    /* 待發貨的訂單： */
    $sql   = 'SELECT COUNT(*)'.
                ' FROM ' .$ecs->table('order_info') .
                " WHERE 1 ". order_query_sql('await_ship');
    $res['new_paid']  = $db->getOne($sql);
}
else
{
    $res['error'] = 1;
}

$val = $json->encode($res);

die($val);

?>