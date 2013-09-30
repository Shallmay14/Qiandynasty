<?php

/**
 * ECSHOP 商品頁
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發佈。
 * ============================================================================
 * $Author: testyang $
 * $Id: buy.php 15013 2008-10-23 09:31:42Z testyang $
*/

define('IN_ECS', true);

include_once(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/lib_order.php');

$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';
if ($_SESSION['user_id'] > 0)
{
    $smarty->assign('user_name', $_SESSION['user_name']);

}

if($act != 'checkout' && $act != 'consignee')
{
    $goods_id = isset($_REQUEST['id']) ? $_REQUEST['id']:'';
    if($goods_id)
    {
        clear_cart();
        $_LANG['shortage'] = "對不起，該商品已經庫存不足暫停銷售。\n你現在要進行缺貨登記來預訂該商品嗎？";
        if(!addto_cart($goods_id))
        {
             echo '購買失敗，請重新購買!';
        }
        else
        {
            $goods_order = 1;
            ecs_header("Location: buy.php?act=checkout");
            exit;
        }

    }
    else
    {
        echo '參數錯誤！';
        $Loaction = 'index.php';
        ecs_header("Location: $Loaction\n");
    }

}
elseif($act == 'checkout' || $act == 'consignee')
{
    if ($_SESSION['user_id'] > 0)
    {
        $act = 'consignee';
    }
    if($act == 'consignee')
    {
        include_once('includes/lib_transaction.php');

            /*
             * 收貨人信息填寫界面
             */

            if (isset($_REQUEST['direct_shopping']))
            {
                $_SESSION['direct_shopping'] = 1;
            }

            /* 取得國家列表、商店所在國家、商店所在國家的省列表 */
            $smarty->assign('country_list',       get_regions());
            $smarty->assign('shop_country',       $_CFG['shop_country']);
            $smarty->assign('shop_province_list', get_regions(1, $_CFG['shop_country']));
            $consignee_list = get_consignee_list($_SESSION['user_id']);
            /* 取得每個收貨地址的省市區列表 */
            $province_list = array();
            $city_list = array();
            $district_list = array();
            foreach ($consignee_list as $region_id => $consignee)
            {
                $consignee['country']  = isset($consignee['country'])  ? intval($consignee['country'])  : 0;
                $consignee['province'] = isset($consignee['province']) ? intval($consignee['province']) : 0;
                $consignee['city']     = isset($consignee['city'])     ? intval($consignee['city'])     : 0;

                $province_list = get_regions(1, $consignee['country']);
                $city_list     = get_regions(2, $consignee['province']);
                $district_list = get_regions(3, $consignee['city']);
            }
            $smarty->assign('buy_type', 1);
            $smarty->assign('province_list', $province_list);
            $smarty->assign('city_list',     $city_list);
            $smarty->assign('district_list', $district_list);
    }
}
$smarty->assign('footer', get_footer());
$smarty->display('buy.html');

?>