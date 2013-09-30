<?php

/**
 * ECSHOP 搜索引擎關鍵字統計
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: searchengine_stats.php 17063 2010-03-25 06:35:46Z liuhui $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/admin/statistic.php');

/* act操作項的初始化 */
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'view';
}
else
{
    $_REQUEST['act'] = trim($_REQUEST['act']);
}

if ($_REQUEST['act'] == 'view')
{
    admin_priv('client_flow_stats');

    /* 時間參數 */
    /* TODO: 時間需要改 */
    if (isset($_POST) && !empty($_POST))
    {
        $start_date = $_POST['start_date'];
        $end_date   = $_POST['end_date'];
    }
    else
    {
        $start_date = local_date('Y-m-d', strtotime('-1 week'));
        $end_date   = local_date('Y-m-d');
    }
    /* ------------------------------------- */
    /* --綜合流量
    /* ------------------------------------- */
    $max = 0;
    $general_xml = "<chart caption='$_LANG[tab_keywords]' shownames='1' showvalues='0' decimals='0' numberPrefix='' outCnvBaseFontSize='12' baseFontSize='12'>";
    $sql = "SELECT keyword, count, searchengine ".
            " FROM " .$ecs->table('keywords').
            " WHERE date >= '$start_date' AND date <= '" .$end_date. "'";
    if (isset($_POST['filter']))
    {
        $sql .= ' AND '. db_create_in($_POST['filter'], 'searchengine');
    }
    $res = $db->query($sql);
    $search = array();
    $searchengine = array();
    $keyword = array();

    while ($val = $db->fetchRow($res))
    {
        $keyword[$val['keyword']] = 1;
        $searchengine[$val['searchengine']][$val['keyword']] = $val['count'];
    }

    $general_xml .= "<categories>";
    foreach($keyword AS $key => $val)
    {
        $key = str_replace('&','＆',$key);
        $key = str_replace('>','＞',$key);
        $key = str_replace('<','＜',$key);
        $general_xml .= "<category label='".str_replace('\'','',$key)."' />";
    }
    $general_xml .= "</categories>\n";

    $i = 0;

    foreach($searchengine AS $key => $val)
    {
        $general_xml .= "<dataset seriesName='$key' color='" . chart_color($i) . "' showValues='0'>";
        foreach($keyword AS $k => $v)
        {
            $count = 0;
            if(!empty($searchengine[$key][$k]))
            {
                $count = $searchengine[$key][$k];
            }
            $general_xml .= "<set value='$count' />";
        }
        $general_xml .= "</dataset>";
        $i++;
    }

    $general_xml .= '</chart>';

    /* 模板賦值 */
    $smarty->assign('ur_here',      $_LANG['searchengine_stats']);
    $smarty->assign('general_data', $general_xml);

    $searchengines = array('ecshop'  => false,
                            'MSLIVE'  => false,
                            'BAIDU'  => false,
                            'GOOGLE' => false,
                            'GOOGLE CHINA' => false,
                            'CT114' => false,
                            'SOSO'  => false);

    if (isset($_POST['filter']))
    {
        foreach ($_POST['filter'] AS $v)
        {
            $searchengines[$v] = true;
        }
    }
    $smarty->assign('searchengines', $searchengines);

    /* 顯示日期 */
    $smarty->assign('start_date',   $start_date);
    $smarty->assign('end_date',     $end_date);

    $filename = local_date('Ymd', $start_date) . '_' . local_date('Ymd', $end_date);
    $smarty->assign('action_link',  array('text' => $_LANG['down_search_stats'], 'href' => 'searchengine_stats.php?act=download&start_date=' . $start_date . '&end_date=' . $end_date . '&filename=' . $filename));

    $smarty->assign('lang', $_LANG);
    /* 顯示頁面 */
    assign_query_info();
    $smarty->display('searchengine_stats.htm');
}
elseif ($_REQUEST['act'] == 'download')
{
    $start_date = empty($_REQUEST['start_date']) ? strtotime('-20 day') : intval($_REQUEST['start_date']);
    $end_date   = empty($_REQUEST['end_date']) ? time() : intval($_REQUEST['end_date']);

    $filename = $start_date . '_' . $end_date;
    $sql = "SELECT keyword, count,searchengine ".
            " FROM " .$ecs->table('keywords').
            " WHERE date >= '$start_date' AND date <= '$end_date'";
    $res = $db->query($sql);

    $searchengine = array();
    $keyword = array();

    while ($val = $db->fetchRow($res))
    {
        $keyword[$val['keyword']] = 1;
        $searchengine[$val['searchengine']][$val['keyword']] = $val['count'];
    }
    header("Content-type: application/vnd.ms-excel; charset=utf-8");
    header("Content-Disposition: attachment; filename=$filename.xls");
    $data = "\t";
    foreach ($searchengine AS $k => $v)
    {
        $data .= "$k\t";
    }
    foreach ($keyword AS $kw => $val)
    {
        $data .= "\n$kw\t";
        foreach ($searchengine AS $k => $v)
        {
            if (isset($searchengine[$k][$kw]))
            {
                $data .= $searchengine[$k][$kw] . "\t";
            }
            else
            {
                $data .= "0" . "\t";
            }
        }
    }
    echo ecs_iconv(EC_CHARSET, 'GB2312', $data) . "\t";
}

?>