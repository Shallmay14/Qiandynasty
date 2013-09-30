<?php

/**
 * ECSHOP 訪問購買比例
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發佈。
 * ============================================================================
 * $Author: liuhui $
 * $Id: visit_sold.php 17063 2010-03-25 06:35:46Z liuhui $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/lib_order.php');
require_once('../languages/' .$_CFG['lang']. '/admin/statistic.php');
$smarty->assign('lang',    $_LANG);

/* act操作項的初始化 */
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'list';
}
else
{
    $_REQUEST['act'] = trim($_REQUEST['act']);
}

admin_priv('client_flow_stats');

/*------------------------------------------------------ */
//--訪問購買比例
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list' || $_REQUEST['act'] == 'download')
{
    /* 變量的初始化 */
    $cat_id   = (!empty($_REQUEST['cat_id']))   ? intval($_REQUEST['cat_id'])   : 0;
    $brand_id = (!empty($_REQUEST['brand_id'])) ? intval($_REQUEST['brand_id']) : 0;
    $show_num = (!empty($_REQUEST['show_num'])) ? intval($_REQUEST['show_num']) : 15;

    /* 獲取訪問購買的比例數據 */
    $click_sold_info = click_sold_info($cat_id, $brand_id, $show_num);

    /* 下載報表 */
    if ($_REQUEST['act'] == "download")
    {
        $filename = 'visit_sold';
        header("Content-type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=$filename.xls");
        $data = "$_LANG[visit_buy]\t\n";
        $data .= "$_LANG[order_by]\t$_LANG[goods_name]\t$_LANG[fav_exponential]\t$_LANG[buy_times]\t$_LANG[visit_buy]\n";
        foreach ($click_sold_info AS $k => $row)
        {
            $order_by = $k + 1;
            $data .= "$order_by\t$row[goods_name]\t$row[click_count]\t$row[sold_times]\t$row[scale]\n";
        }
        echo ecs_iconv(EC_CHARSET, 'GB2312', $data);
        exit;
    }

    /* 賦值到模板 */
    $smarty->assign('ur_here',      $_LANG['visit_buy_per']);

    $smarty->assign('show_num',         $show_num);
    $smarty->assign('brand_id',         $brand_id);
    $smarty->assign('click_sold_info',  $click_sold_info);

    $smarty->assign('cat_list',     cat_list(0, $cat_id));
    $smarty->assign('brand_list',   get_brand_list());

    $filename = 'visit_sold';
    $smarty->assign('action_link',  array('text' => $_LANG['download_visit_buy'], 'href' => 'visit_sold.php?act=download&show_num=' . $show_num . '&cat_id=' . $cat_id . '&brand_id=' . $brand_id . '&show_num=' . $show_num ));

    /* 顯示頁面 */
    assign_query_info();
    $smarty->display('visit_sold.htm');
}

/*------------------------------------------------------ */
//--訂單統計需要的函數
/*------------------------------------------------------ */
/**
 * 取得訪問和購買次數統計數據
 *
 * @param   int             $cat_id          分類編號
 * @param   int             $brand_id        品牌編號
 * @param   int             $show_num        顯示個數
 * @return  array           $click_sold_info  訪問購買比例數據
 */
 function click_sold_info($cat_id, $brand_id, $show_num)
 {
    global $db, $ecs;

    $where = " WHERE o.order_id = og.order_id AND g.goods_id = og.goods_id " . order_query_sql('finished', 'o.');
    $limit = " LIMIT " .$show_num;

    if ($cat_id > 0)
    {
        $where .= " AND " . get_children($cat_id);
    }
    if ($brand_id > 0)
    {
        $where .= " AND g.brand_id = '$brand_id' ";
    }

    $click_sold_info = array();
    $sql = "SELECT og.goods_id, g.goods_sn, g.goods_name, g.click_count,  COUNT(og.goods_id) AS sold_times ".
        " FROM ". $ecs->table('goods') ." AS g, ". $ecs->table('order_goods') ." AS og, " .$ecs->table('order_info') . " AS o " . $where .
        " GROUP BY og.goods_id ORDER BY g.click_count DESC " . $limit;
    $res = $db->query($sql);

    while ($item = $db->fetchRow($res))
    {
        if ($item['click_count'] <= 0)
        {
            $item['scale'] = 0;
        }
        else
        {
            /* 每一百個點擊的訂單比率 */
            $item['scale'] = sprintf("%0.2f", ($item['sold_times'] / $item['click_count']) * 100) .'%';
        }

        $click_sold_info[] = $item;
    }

    return $click_sold_info;
}

?>