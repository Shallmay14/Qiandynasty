<?php

/**
 * ECSHOP 站點地圖生成程序
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發佈。
 * ============================================================================
 * $Author: liuhui $
 * $Id: sitemap.php 17063 2010-03-25 06:35:46Z liuhui $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/* 檢查權限 */
admin_priv('sitemap');

if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
    /*------------------------------------------------------ */
    //-- 設置更新頻率
    /*------------------------------------------------------ */
    assign_query_info();
    $config = unserialize($_CFG['sitemap']);
    $smarty->assign('config',           $config);
    $smarty->assign('ur_here',          $_LANG['sitemap']);
    $smarty->assign('arr_changefreq',   array(1,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1));
    $smarty->display('sitemap.htm');
}
else
{
    /*------------------------------------------------------ */
    //-- 生成站點地圖
    /*------------------------------------------------------ */
    include_once('includes/cls_phpzip.php');
    include_once('includes/cls_google_sitemap.php');

    $domain = $ecs->url();
    $today  = local_date('Y-m-d');

    $sm     =& new google_sitemap();
    $smi    =& new google_sitemap_item($domain, $today, $_POST['homepage_changefreq'], $_POST['homepage_priority']);
    $sm->add_item($smi);

    $config = array(
        'homepage_changefreq' => $_POST['homepage_changefreq'],
        'homepage_priority' => $_POST['homepage_priority'],
        'category_changefreq' => $_POST['category_changefreq'],
        'category_priority' => $_POST['category_priority'],
        'content_changefreq' => $_POST['content_changefreq'],
        'content_priority' => $_POST['content_priority'],
        );
    $config = serialize($config);

    $db->query("UPDATE " .$ecs->table('shop_config'). " SET VALUE='$config' WHERE code='sitemap'");

    /* 商品分類 */
    $sql = "SELECT cat_id,cat_name FROM " .$ecs->table('category'). " ORDER BY parent_id";
    $res = $db->query($sql);

    while ($row = $db->fetchRow($res))
    {
        $smi =& new google_sitemap_item($domain . build_uri('category', array('cid' => $row['cat_id']), $row['cat_name']), $today,
            $_POST['category_changefreq'], $_POST['category_priority']);
        $sm->add_item($smi);
    }

    /* 文章分類 */
    $sql = "SELECT cat_id,cat_name FROM " .$ecs->table('article_cat'). " WHERE cat_type=1";
    $res = $db->query($sql);

    while ($row = $db->fetchRow($res))
    {
        $smi =& new google_sitemap_item($domain . build_uri('article_cat', array('acid' => $row['cat_id']), $row['cat_name']), $today,
            $_POST['category_changefreq'], $_POST['category_priority']);
        $sm->add_item($smi);
    }

    /* 商品 */
    $sql = "SELECT goods_id, goods_name FROM " .$ecs->table('goods'). " WHERE is_delete = 0";
    $res = $db->query($sql);

    while ($row = $db->fetchRow($res))
    {
        $smi =& new google_sitemap_item($domain . build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']), $today,
            $_POST['content_changefreq'], $_POST['content_priority']);
        $sm->add_item($smi);
    }

    /* 文章 */
    $sql = "SELECT article_id,title,file_url,open_type FROM " .$ecs->table('article'). " WHERE is_open=1";
    $res = $db->query($sql);

    while ($row = $db->fetchRow($res))
    {
        $article_url=$row['open_type'] != 1 ? build_uri('article', array('aid'=>$row['article_id']), $row['title']) : trim($row['file_url']);
        $smi =& new google_sitemap_item($domain . $article_url,
            $today, $_POST['content_changefreq'], $_POST['content_priority']);
        $sm->add_item($smi);
    }

    clear_cache_files();    // 清除緩存

    $sm_file = '../sitemaps.xml';
    if ($sm->build($sm_file))
    {
        sys_msg(sprintf($_LANG['generate_success'], $ecs->url()."sitemaps.xml"));
    }
    else
    {
        $sm_file = '../' . DATA_DIR . '/sitemaps.xml';
        if ($sm->build($sm_file))
        {
            sys_msg(sprintf($_LANG['generate_success'], $ecs->url(). DATA_DIR . '/sitemaps.xml'));
        }
        else
        {
            sys_msg(sprintf($_LANG['generate_failed']));
        }
    }
}

?>