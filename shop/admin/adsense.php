<?php

/**
 * ECSHOP 站外JS投放的統計程序
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發佈。
 * ============================================================================
 * $Author: liuhui $
 * $Id: adsense.php 17063 2010-03-25 06:35:46Z liuhui $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/lib_order.php');
require_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/admin/ads.php');

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
//-- 站外投放廣告的統計
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list' || $_REQUEST['act'] == 'download')
{
    admin_priv('ad_manage');

    /* 獲取廣告數據 */
    $ads_stats = array();
    $sql = "SELECT a.ad_id, a.ad_name, b.* ".
           "FROM " .$ecs->table('ad'). " AS a, " .$ecs->table('adsense'). " AS b ".
           "WHERE b.from_ad = a.ad_id ORDER by a.ad_name DESC";
    $res = $db->query($sql);
    while ($rows = $db->fetchRow($res))
    {
        /* 獲取當前廣告所產生的訂單總數 */
        $sql2 = 'SELECT COUNT(order_id) FROM ' .$ecs->table('order_info'). " WHERE from_ad='$rows[ad_id]' AND referer='$rows[referer]'";
        $rows['order_num'] = $db->getOne($sql2);

        /* 當前廣告所產生的已完成的有效訂單 */
        $sql3 = "SELECT COUNT(order_id) FROM " .$ecs->table('order_info').
               " WHERE from_ad    = '$rows[ad_id]'" .
               " AND referer = '$rows[referer]' ". order_query_sql('finished');
        $rows['order_confirm'] = $db->getOne($sql3);

        $ads_stats[] = $rows;
    }
    $smarty->assign('ads_stats',        $ads_stats);

    /* 站外JS投放商品的統計數據 */
    $goods_stats    = array();
    $goods_sql      = "SELECT from_ad, referer, clicks FROM " .$ecs->table('adsense').
              " WHERE from_ad = '-1' ORDER by referer DESC";
    $goods_res = $db->query($goods_sql);
    while ($rows2 = $db->fetchRow($goods_res))
    {
        /* 獲取當前廣告所產生的訂單總數 */
        $rows2['order_num'] = $db->getOne("SELECT COUNT(order_id) FROM " .$ecs->table('order_info'). " WHERE referer='$rows2[referer]'");

        /* 當前廣告所產生的已完成的有效訂單 */
        $sql = "SELECT COUNT(order_id) FROM " .$ecs->table('order_info').
               " WHERE referer='$rows2[referer]'" . order_query_sql('finished');
        $rows2['order_confirm'] = $db->getOne($sql);

        $rows2['ad_name']  = $_LANG['adsense_js_goods'];
        $goods_stats[]  = $rows2;
    }
    if ($_REQUEST['act'] == 'download')
    {
        header("Content-type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=ad_statistics.xls");
        $data = "$_LANG[adsense_name]\t$_LANG[cleck_referer]\t$_LANG[click_count]\t$_LANG[confirm_order]\t$_LANG[gen_order_amount]\n";
        $res = array_merge($goods_stats, $ads_stats);
        foreach ($res AS $row)
        {
            $data .= "$row[ad_name]\t$row[referer]\t$row[clicks]\t$row[order_confirm]\t$row[order_num]\n";
        }
        echo ecs_iconv(EC_CHARSET, 'GB2312', $data);
        exit;
    }
    $smarty->assign('goods_stats', $goods_stats);

    /* 賦值給模板 */
    $smarty->assign('action_link', array('href' => 'ads.php?act=list', 'text' => $_LANG['ad_list']));
    $smarty->assign('action_link2', array('href' => 'adsense.php?act=download', 'text' => $_LANG['download_ad_statistics']));
    $smarty->assign('ur_here',     $_LANG['adsense_js_stats']);
    $smarty->assign('lang',        $_LANG);

    /* 顯示頁面 */
    assign_query_info();
    $smarty->display('adsense.htm');
}

?>