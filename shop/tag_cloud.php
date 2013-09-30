<?php

/**
 * ECSHOP 標籤雲
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: tag_cloud.php 17063 2010-03-25 06:35:46Z liuhui $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
    assign_template();
    $position = assign_ur_here(0, $_LANG['tag_cloud']);
    $smarty->assign('page_title', $position['title']);    // 頁面標題
    $smarty->assign('ur_here',    $position['ur_here']);  // 當前位置
    $smarty->assign('categories', get_categories_tree()); // 分類樹
    $smarty->assign('helps',      get_shop_help());       // 網店幫助
    $smarty->assign('top_goods',  get_top10());           // 銷售排行
    $smarty->assign('promotion_info', get_promotion_info());

    /* 調查 */
    $vote = get_vote();
    if (!empty($vote))
    {
        $smarty->assign('vote_id', $vote['id']);
        $smarty->assign('vote',    $vote['content']);
    }

    assign_dynamic('tag_cloud');

    $tags = get_tags();

    if (!empty($tags))
    {
        include_once(ROOT_PATH . 'includes/lib_clips.php');
        color_tag($tags);
    }

    $smarty->assign('tags', $tags);

    $smarty->display('tag_cloud.dwt');
?>