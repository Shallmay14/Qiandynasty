<?php

/**
 * ECSHOP SOHU BLOG widget
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: blog_sohu.php 17063 2010-03-25 06:35:46Z liuhui $
*/

define('IN_ECS', true);
define('INIT_NO_USERS', true);
define('INIT_NO_SMARTY', true);
require(dirname(dirname(__FILE__)) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/cls_json.php');

$num = !empty($_GET['num']) ? intval($_GET['num']) : 10;
if ($num <= 0 || $num > 10)
{
    $num = 10;
}
$json = new JSON;
$result = $db->getAll("SELECT goods_id, goods_name, shop_price, promote_price, promote_start_date, promote_end_date, goods_brief, goods_thumb FROM " . $ecs->table('goods') . " WHERE is_on_sale = 1 AND is_alone_sale = 1 AND is_delete = 0 ORDER BY rand() LIMIT 0, $num");
$idx = 0;
$content['domain'] = 'http://' . $_SERVER['SERVER_NAME'];
$goods = array();
foreach ($result as $row)
{
    if ($row['promote_price'] > 0)
    {
        $promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
        $goods[$idx]['promote_price'] = $promote_price > 0 ? price_format($promote_price) : '';
    }
    else
    {
        $goods[$idx]['promote_price'] = '';
    }
    $goods[$idx]['goods_id'] = $row['goods_id'];
    $goods[$idx]['goods_name'] = $row['goods_name'];
    $goods[$idx]['shop_price'] = price_format($row['shop_price']);
    $goods[$idx]['goods_brief'] = $row['goods_brief'];
    $goods[$idx]['goods_thumb'] = empty($row['goods_thumb']) ? $GLOBALS['_CFG']['no_picture'] : $row['goods_thumb'];
    $idx++;
}
$content['goods'] = $goods;
die($json->encode($content));

?>