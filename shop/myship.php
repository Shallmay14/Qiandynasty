<?php

/**
 * ECSHOP 支付配送DEMO
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: myship.php 17063 2010-03-25 06:35:46Z liuhui $
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/lib_order.php');
include_once(ROOT_PATH . 'includes/lib_transaction.php');

/* 載入語言文件 */
require_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/shopping_flow.php');
require_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/user.php');

/*------------------------------------------------------ */
//-- INPUT
/*------------------------------------------------------ */

if ($_SESSION['user_id'] > 0)
{
    $consignee_list = get_consignee_list($_SESSION['user_id']);

    $choose['country'] = isset($_POST['country']) ? $_POST['country'] : $consignee_list[0]['country'];
    $choose['province'] = isset($_POST['province']) ? $_POST['province'] : $consignee_list[0]['province'];
    $choose['city'] = isset($_POST['city']) ? $_POST['city'] : $consignee_list[0]['city'];
    $choose['district'] = isset($_POST['district']) ? $_POST['district'] : (isset($consignee_list[0]['district']) ? $consignee_list[0]['district'] : 0 );
}
else
{
    $choose['country'] = isset($_POST['country']) ? $_POST['country'] : $_CFG['shop_country'];
    $choose['province'] = isset($_POST['province']) ? $_POST['province'] : 2;
    $choose['city'] = isset($_POST['city']) ? $_POST['city'] : 35;
    $choose['district'] = isset($_POST['district']) ? $_POST['district'] : 417;
}

/*------------------------------------------------------ */
//-- PROCESSOR
/*------------------------------------------------------ */

assign_template();
assign_dynamic('myship');
$position = assign_ur_here(0, $_LANG['shopping_myship']);
$smarty->assign('page_title', $position['title']);    // 頁面標題
$smarty->assign('ur_here',    $position['ur_here']);  // 當前位置

$smarty->assign('helps',      get_shop_help());       // 網店幫助
$smarty->assign('lang',       $_LANG);

$smarty->assign('choose',     $choose);

$province_list[NULL] = get_regions(1, $choose['country']);
$city_list[NULL]     = get_regions(2, $choose['province']);
$district_list[NULL] = get_regions(3, $choose['city']);

$smarty->assign('province_list', $province_list);
$smarty->assign('city_list',     $city_list);
$smarty->assign('district_list', $district_list);

/* 取得國家列表、商店所在國家、商店所在國家的省列表 */
$smarty->assign('country_list',       get_regions());

/* 取得配送列表 */
$region            = array($choose['country'], $choose['province'], $choose['city'], $choose['district']);
$shipping_list     = available_shipping_list($region);
$cart_weight_price = 0;
$insure_disabled   = true;
$cod_disabled      = true;

foreach ($shipping_list AS $key => $val)
{
    $shipping_cfg = unserialize_config($val['configure']);
    $shipping_fee = shipping_fee($val['shipping_code'], unserialize($val['configure']),
    $cart_weight_price['weight'], $cart_weight_price['amount']);

    $shipping_list[$key]['format_shipping_fee'] = price_format($shipping_fee, false);
    $shipping_list[$key]['fee']        = $shipping_fee;
    $shipping_list[$key]['free_money']          = price_format($shipping_cfg['free_money'], false);
    $shipping_list[$key]['insure_formated']     = strpos($val['insure'], '%') === false ?
    price_format($val['insure'], false) : $val['insure'];
}

$smarty->assign('shipping_list',   $shipping_list);

$smarty->display('myship.dwt');

?>