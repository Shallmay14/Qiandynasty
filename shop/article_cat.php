<?php

/**
 * ECSHOP 文章分類
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發布。
 * ============================================================================
 * $Author: sxc_shop $
 * $Id: article_cat.php 17074 2010-03-26 10:38:21Z sxc_shop $
*/


define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}

/* 清除緩存 */
clear_cache_files();

/*------------------------------------------------------ */
//-- INPUT
/*------------------------------------------------------ */

/* 獲得指定的分類ID */
if (!empty($_GET['id']))
{
    $cat_id = intval($_GET['id']);
}
elseif (!empty($_GET['category']))
{
    $cat_id = intval($_GET['category']);
}
else
{
    ecs_header("Location: ./\n");

    exit;
}

/* 獲得當前頁碼 */
$page   = !empty($_REQUEST['page'])  && intval($_REQUEST['page'])  > 0 ? intval($_REQUEST['page'])  : 1;

/*------------------------------------------------------ */
//-- PROCESSOR
/*------------------------------------------------------ */

/* 獲得頁面的緩存ID */
$cache_id = sprintf('%X', crc32($cat_id . '-' . $page . '-' . $_CFG['lang']));

if (!$smarty->is_cached('article_cat.dwt', $cache_id))
{
    /* 如果頁面沒有被緩存則重新獲得頁面的內容 */

    assign_template('a', array($cat_id));
    $position = assign_ur_here($cat_id);
    $smarty->assign('page_title',           $position['title']);     // 頁面標題
    $smarty->assign('ur_here',              $position['ur_here']);   // 當前位置

    $smarty->assign('categories',           get_categories_tree(0)); // 分類樹
    $smarty->assign('article_categories',   article_categories_tree($cat_id)); //文章分類樹
    $smarty->assign('helps',                get_shop_help());        // 網店幫助
    $smarty->assign('top_goods',            get_top10());            // 銷售排行

    $smarty->assign('best_goods',           get_recommend_goods('best'));
    $smarty->assign('new_goods',            get_recommend_goods('new'));
    $smarty->assign('hot_goods',            get_recommend_goods('hot'));
    $smarty->assign('promotion_goods',      get_promote_goods());
    $smarty->assign('promotion_info', get_promotion_info());

    /* Meta */
    $meta = $db->getRow("SELECT keywords, cat_desc FROM " . $ecs->table('article_cat') . " WHERE cat_id = '$cat_id'");

    if ($meta === false || empty($meta))
    {
        /* 如果沒有找到任何記錄則返回首頁 */
        ecs_header("Location: ./\n");
        exit;
    }

    $smarty->assign('keywords',    htmlspecialchars($meta['keywords']));
    $smarty->assign('description', htmlspecialchars($meta['cat_desc']));

    /* 獲得文章總數 */
    $size   = isset($_CFG['article_page_size']) && intval($_CFG['article_page_size']) > 0 ? intval($_CFG['article_page_size']) : 20;
    $count  = get_article_count($cat_id);
    $pages  = ($count > 0) ? ceil($count / $size) : 1;

    if ($page > $pages)
    {
        $page = $pages;
    }
    $pager['search']['id'] = $cat_id;
    $keywords = '';
    $goon_keywords = ''; //繼續傳遞的搜索關鍵詞

    /* 獲得文章列表 */
    if (isset($_POST['keywords']))
    {
        $keywords = addslashes(urldecode(trim($_POST['keywords'])));
        $pager['search']['keywords'] = $keywords;
        $search_url = substr(strrchr($_POST['cur_url'], '/'), 1);

        $smarty->assign('search_value',     $keywords);
        $smarty->assign('search_url',       $search_url);
        $count  = get_article_count($cat_id, $keywords);
        $pages  = ($count > 0) ? ceil($count / $size) : 1;
        if ($page > $pages)
        {
            $page = $pages;
        }

        $goon_keywords = urlencode($_POST['keywords']);
    }
    $smarty->assign('artciles_list',    get_cat_articles($cat_id, $page, $size ,$keywords));
    $smarty->assign('cat_id',    $cat_id);
    /* 分頁 */
    assign_pager('article_cat', $cat_id, $count, $size, '', '', $page, $goon_keywords);
    assign_dynamic('article_cat');
}

$smarty->assign('feed_url',         ($_CFG['rewrite'] == 1) ? "feed-typearticle_cat" . $cat_id . ".xml" : 'feed.php?type=article_cat' . $cat_id); // RSS URL

$smarty->display('article_cat.dwt', $cache_id);

?>