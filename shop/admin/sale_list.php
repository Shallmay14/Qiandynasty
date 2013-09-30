<?php

/**
 * ECSHOP 銷售明細列表程序
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發佈。
 * ============================================================================
 * $Author: liuhui $
 * $Id: sale_list.php 17063 2010-03-25 06:35:46Z liuhui $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/lib_order.php');
require_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/admin/statistic.php');
$smarty->assign('lang', $_LANG);

if (isset($_REQUEST['act']) && ($_REQUEST['act'] == 'query' ||  $_REQUEST['act'] == 'download'))
{
    /* 檢查權限 */
    check_authz_json('sale_order_stats');
    if (strstr($_REQUEST['start_date'], '-') === false)
    {
        $_REQUEST['start_date'] = local_date('Y-m-d', $_REQUEST['start_date']);
        $_REQUEST['end_date'] = local_date('Y-m-d', $_REQUEST['end_date']);
    }
    /*------------------------------------------------------ */
    //--Excel文件下載
    /*------------------------------------------------------ */
    if ($_REQUEST['act'] == 'download')
    {
        $file_name = $_REQUEST['start_date'].'_'.$_REQUEST['end_date'] . '_sale';
        $goods_sales_list = get_sale_list(false);
        header("Content-type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=$file_name.xls");

        /* 文件標題 */
        echo ecs_iconv(EC_CHARSET, 'GB2312', $_REQUEST['start_date']. $_LANG['to'] .$_REQUEST['end_date']. $_LANG['sales_list']) . "\t\n";

        /* 商品名稱,訂單號,商品數量,銷售價格,銷售日期 */
        echo ecs_iconv(EC_CHARSET, 'GB2312', $_LANG['goods_name']) . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', $_LANG['order_sn']) . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', $_LANG['amount']) . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', $_LANG['sell_price']) . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', $_LANG['sell_date']) . "\t\n";

        foreach ($goods_sales_list['sale_list_data'] AS $key => $value)
        {
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['goods_name']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', '[ ' . $value['order_sn'] . ' ]') . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['goods_num']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['sales_price']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['sales_time']) . "\t";
            echo "\n";
        }
        exit;
    }
    $sale_list_data = get_sale_list();
    $smarty->assign('goods_sales_list', $sale_list_data['sale_list_data']);
    $smarty->assign('filter',       $sale_list_data['filter']);
    $smarty->assign('record_count', $sale_list_data['record_count']);
    $smarty->assign('page_count',   $sale_list_data['page_count']);

    make_json_result($smarty->fetch('sale_list.htm'), '', array('filter' => $sale_list_data['filter'], 'page_count' => $sale_list_data['page_count']));
}
/*------------------------------------------------------ */
//--商品明細列表
/*------------------------------------------------------ */
else
{
    /* 權限判斷 */
    admin_priv('sale_order_stats');
    /* 時間參數 */
    if (!isset($_REQUEST['start_date']))
    {
        $start_date = local_strtotime('-7 days');
    }
    if (!isset($_REQUEST['end_date']))
    {
        $end_date = local_strtotime('today');
    }
    
    $sale_list_data = get_sale_list();
    /* 賦值到模板 */
    $smarty->assign('filter',       $sale_list_data['filter']);
    $smarty->assign('record_count', $sale_list_data['record_count']);
    $smarty->assign('page_count',   $sale_list_data['page_count']);
    $smarty->assign('goods_sales_list', $sale_list_data['sale_list_data']);
    $smarty->assign('ur_here',          $_LANG['sell_stats']);
    $smarty->assign('full_page',        1);
    $smarty->assign('start_date',       local_date('Y-m-d', $start_date));
    $smarty->assign('end_date',         local_date('Y-m-d', $end_date));
    $smarty->assign('ur_here',      $_LANG['sale_list']);
    $smarty->assign('cfg_lang',     $_CFG['lang']);
    $smarty->assign('action_link',  array('text' => $_LANG['down_sales'],'href'=>'#download'));

    /* 顯示頁面 */
    assign_query_info();
    $smarty->display('sale_list.htm');
}
/*------------------------------------------------------ */
//--獲取銷售明細需要的函數
/*------------------------------------------------------ */
/**
 * 取得銷售明細數據信息
 * @param   bool  $is_pagination  是否分頁
 * @return  array   銷售明細數據
 */
function get_sale_list($is_pagination = true){

    /* 時間參數 */
    $filter['start_date'] = empty($_REQUEST['start_date']) ? local_strtotime('-7 days') : local_strtotime($_REQUEST['start_date']);
    $filter['end_date'] = empty($_REQUEST['end_date']) ? local_strtotime('today') : local_strtotime($_REQUEST['end_date']);
  
    /* 查詢數據的條件 */
    $where = " WHERE og.order_id = oi.order_id". order_query_sql('finished', 'oi.') .
             " AND oi.add_time >= '".$filter['start_date']."' AND oi.add_time < '" . ($filter['end_date'] + 86400) . "'";
    
    $sql = "SELECT COUNT(og.goods_id) FROM " .
           $GLOBALS['ecs']->table('order_info') . ' AS oi,'.
           $GLOBALS['ecs']->table('order_goods') . ' AS og '.
           $where;
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);

    /* 分頁大小 */
    $filter = page_and_size($filter);

    $sql = 'SELECT og.goods_id, og.goods_sn, og.goods_name, og.goods_number AS goods_num, og.goods_price '.
           'AS sales_price, oi.add_time AS sales_time, oi.order_id, oi.order_sn '.
           "FROM " . $GLOBALS['ecs']->table('order_goods')." AS og, ".$GLOBALS['ecs']->table('order_info')." AS oi ".
           $where. " ORDER BY sales_time DESC, goods_num DESC";
    if ($is_pagination)
    {
        $sql .= " LIMIT " . $filter['start'] . ', ' . $filter['page_size'];
    }

    $sale_list_data = $GLOBALS['db']->getAll($sql);

    foreach ($sale_list_data as $key => $item)
    {
        $sale_list_data[$key]['sales_price'] = price_format($sale_list_data[$key]['sales_price']);
        $sale_list_data[$key]['sales_time']  = local_date($GLOBALS['_CFG']['time_format'], $sale_list_data[$key]['sales_time']);
    }
    $arr = array('sale_list_data' => $sale_list_data, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    return $arr;
}
?>