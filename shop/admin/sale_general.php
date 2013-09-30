<?php

/**
 * ECSHOP 銷售概況
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發佈。
 * ============================================================================
 * $Author: liuhui $
 * $Id: sale_general.php 17063 2010-03-25 06:35:46Z liuhui $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'languages/' . $_CFG['lang'] . '/admin/statistic.php');
$smarty->assign('lang', $_LANG);

/* 權限判斷 */
admin_priv('sale_order_stats');

/* act操作項的初始化 */
if (empty($_REQUEST['act']) || !in_array($_REQUEST['act'], array('list', 'download')))
{
    $_REQUEST['act'] = 'list';
}

/* 取得查詢類型和查詢時間段 */
if (empty($_POST['query_by_year']) && empty($_POST['query_by_month']))
{
    if (empty($_GET['query_type']))
    {
        /* 默認當年的月走勢 */
        $query_type = 'month';
        $start_time = local_mktime(0, 0, 0, 1, 1, intval(date('Y')));
        $end_time   = gmtime();
    }
    else
    {
        /* 下載時的參數 */
        $query_type = $_GET['query_type'];
        $start_time = $_GET['start_time'];
        $end_time   = $_GET['end_time'];
    }
}
else
{
    if (isset($_POST['query_by_year']))
    {
        /* 年走勢 */
        $query_type = 'year';
        $start_time = local_mktime(0, 0, 0, 1, 1, intval($_POST['year_beginYear']));
        $end_time   = local_mktime(23, 59, 59, 12, 31, intval($_POST['year_endYear']));
    }
    else
    {
        /* 月走勢 */
        $query_type = 'month';
        $start_time = local_mktime(0, 0, 0, intval($_POST['month_beginMonth']), 1, intval($_POST['month_beginYear']));
        $end_time   = local_mktime(23, 59, 59, intval($_POST['month_endMonth']), 1, intval($_POST['month_endYear']));
        $end_time   = local_mktime(23, 59, 59, intval($_POST['month_endMonth']), date('t', $end_time), intval($_POST['month_endYear']));

    }
}

/* 分組統計訂單數和銷售額：已發貨時間為準 */
$format = ($query_type == 'year') ? '%Y' : '%Y-%m';
$sql = "SELECT DATE_FORMAT(FROM_UNIXTIME(shipping_time), '$format') AS period, COUNT(*) AS order_count, " .
            "SUM(goods_amount + shipping_fee + insure_fee + pay_fee + pack_fee + card_fee - discount) AS order_amount " .
        "FROM " . $ecs->table('order_info') .
        " WHERE (order_status = '" . OS_CONFIRMED . "' OR order_status >= '" . OS_SPLITED . "')" .
        " AND (pay_status = '" . PS_PAYED . "' OR pay_status = '" . PS_PAYING . "') " .
        " AND (shipping_status = '" . SS_SHIPPED . "' OR shipping_status = '" . SS_RECEIVED . "') " .
        " AND shipping_time >= '$start_time' AND shipping_time <= '$end_time'" .
        " GROUP BY period ";
$data_list = $db->getAll($sql);

/*------------------------------------------------------ */
//-- 顯示統計信息
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    /* 賦值查詢時間段 */
    $smarty->assign('start_time',   local_date('Y-m-d', $start_time));
    $smarty->assign('end_time',     local_date('Y-m-d', $end_time));

    /* 賦值統計數據 */
    $xml = "<chart caption='' xAxisName='%s' showValues='0' decimals='0' formatNumberScale='0'>%s</chart>";
    $set = "<set label='%s' value='%s' />";
    $i = 0;
    $data_count  = '';
    $data_amount = '';
    foreach ($data_list as $data)
    {
        $data_count  .= sprintf($set, $data['period'], $data['order_count'], chart_color($i));
        $data_amount .= sprintf($set, $data['period'], $data['order_amount'], chart_color($i));
        $i++;
    }

    $smarty->assign('data_count',  sprintf($xml, '', $data_count)); // 訂單數統計數據
    $smarty->assign('data_amount', sprintf($xml, '', $data_amount));    // 銷售額統計數據
    
    $smarty->assign('data_count_name',  $_LANG['order_count_trend']); 
    $smarty->assign('data_amount_name',  $_LANG['order_amount_trend']); 

    /* 根據查詢類型生成文件名 */
    if ($query_type == 'year')
    {
        $filename = date('Y', $start_time) . "_" . date('Y', $end_time) . '_report';
    }
    else
    {
       $filename = date('Ym', $start_time) . "_" . date('Ym', $end_time) . '_report';
    }
    $smarty->assign('action_link',
    array('text' => $_LANG['down_sales_stats'],
          'href'=>'sale_general.php?act=download&filename=' . $filename .
            '&query_type=' . $query_type . '&start_time=' . $start_time . '&end_time=' . $end_time));

    /* 顯示模板 */
    $smarty->assign('ur_here', $_LANG['report_sell']);
    assign_query_info();
    $smarty->display('sale_general.htm');
}

/*------------------------------------------------------ */
//-- 下載EXCEL報表
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'download')
{
    /* 文件名 */
    $filename = !empty($_REQUEST['filename']) ? trim($_REQUEST['filename']) : '';

    header("Content-type: application/vnd.ms-excel; charset=utf-8");
    header("Content-Disposition: attachment; filename=$filename.xls");

    /* 文件標題 */
    echo ecs_iconv(EC_CHARSET, 'GB2312', $filename . $_LANG['sales_statistics']) . "\t\n";

    /* 訂單數量, 銷售出商品數量, 銷售金額 */
    echo ecs_iconv(EC_CHARSET, 'GB2312', $_LANG['period']) ."\t";
    echo ecs_iconv(EC_CHARSET, 'GB2312', $_LANG['order_count_trend']) ."\t";
    echo ecs_iconv(EC_CHARSET, 'GB2312', $_LANG['order_amount_trend']) . "\t\n";

    foreach ($data_list AS $data)
    {
        echo ecs_iconv(EC_CHARSET, 'GB2312', $data['period']) . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', $data['order_count']) . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', $data['order_amount']) . "\t";
        echo "\n";
    }
}

?>