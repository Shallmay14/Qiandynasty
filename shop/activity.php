<?php

/**
 * ECSHOP 活動列表
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: activity.php 17063 2010-03-25 06:35:46Z liuhui $
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/lib_order.php');
include_once(ROOT_PATH . 'includes/lib_transaction.php');

/* 載入語言文件 */
require_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/shopping_flow.php');
require_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/user.php');

/*------------------------------------------------------ */
//-- PROCESSOR
/*------------------------------------------------------ */

assign_template();
assign_dynamic('activity');
$position = assign_ur_here(0, $_LANG['shopping_activity']);
$smarty->assign('page_title',       $position['title']);    // 頁面標題
$smarty->assign('ur_here',          $position['ur_here']);  // 當前位置

// 數據準備

    /* 取得用戶等級 */
    $user_rank_list = array();
    $user_rank_list[0] = $_LANG['not_user'];
    $sql = "SELECT rank_id, rank_name FROM " . $ecs->table('user_rank');
    $res = $db->query($sql);
    while ($row = $db->fetchRow($res))
    {
        $user_rank_list[$row['rank_id']] = $row['rank_name'];
    }


// 開始工作

$sql = "SELECT * FROM " . $ecs->table('favourable_activity'). " ORDER BY `sort_order` ASC,`end_time` DESC";
$res = $db->query($sql);

$list = array();
while ($row = $db->fetchRow($res))
{
    $row['start_time']  = local_date('Y-m-d H:i', $row['start_time']);
    $row['end_time']    = local_date('Y-m-d H:i', $row['end_time']);

    //享受優惠會員等級
    $user_rank = explode(',', $row['user_rank']);
    $row['user_rank'] = array();
    foreach($user_rank as $val)
    {
        if (isset($user_rank_list[$val]))
        {
            $row['user_rank'][] = $user_rank_list[$val];
        }
    }

    //優惠範圍類型、內容
    if ($row['act_range'] != FAR_ALL && !empty($row['act_range_ext']))
    {
        if ($row['act_range'] == FAR_CATEGORY)
        {
            $row['act_range'] = $_LANG['far_category'];
            $row['program'] = 'category.php?id=';
            $sql = "SELECT cat_id AS id, cat_name AS name FROM " . $ecs->table('category') .
                " WHERE cat_id " . db_create_in($row['act_range_ext']);
        }
        elseif ($row['act_range'] == FAR_BRAND)
        {
            $row['act_range'] = $_LANG['far_brand'];
            $row['program'] = 'brand.php?id=';
            $sql = "SELECT brand_id AS id, brand_name AS name FROM " . $ecs->table('brand') .
                " WHERE brand_id " . db_create_in($row['act_range_ext']);
        }
        else
        {
            $row['act_range'] = $_LANG['far_goods'];
            $row['program'] = 'goods.php?id=';
            $sql = "SELECT goods_id AS id, goods_name AS name FROM " . $ecs->table('goods') .
                " WHERE goods_id " . db_create_in($row['act_range_ext']);
        }
        $act_range_ext = $db->getAll($sql);
        $row['act_range_ext'] = $act_range_ext;
    }
    else
    {
        $row['act_range'] = $_LANG['far_all'];
    }

    //優惠方式

    switch($row['act_type'])
    {
        case 0:
            $row['act_type'] = $_LANG['fat_goods'];
            $row['gift'] = unserialize($row['gift']);
            if(is_array($row['gift']))
            {
                foreach($row['gift'] as $k=>$v)
                {
                    $row['gift'][$k]['thumb'] = get_image_path($v['id'], $db->getOne("SELECT goods_thumb FROM " . $ecs->table('goods') . " WHERE goods_id = '" . $v['id'] . "'"), true);
                }
            }
            break;
        case 1:
            $row['act_type'] = $_LANG['fat_price'];
            $row['act_type_ext'] .= $_LANG['unit_yuan'];
            $row['gift'] = array();
            break;
        case 2:
            $row['act_type'] = $_LANG['fat_discount'];
            $row['act_type_ext'] .= "%";
            $row['gift'] = array();
            break;
    }

    $list[] = $row;
}

//print_r($list);
$smarty->assign('list',             $list);

$smarty->assign('helps',            get_shop_help());       // 網店幫助
$smarty->assign('lang',             $_LANG);

$smarty->assign('feed_url',         ($_CFG['rewrite'] == 1) ? "feed-typeactivity.xml" : 'feed.php?type=activity'); // RSS URL
$smarty->display('activity.dwt');

