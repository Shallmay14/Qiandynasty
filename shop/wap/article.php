<?php

/**
 * ECSHOP 文章
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: article.php 17063 2010-03-25 06:35:46Z liuhui $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

$act = !empty($_GET['act']) ? $_GET['act'] : '';

/* 文章詳細 */
if ($act == 'detail')
{
    $a_id = !empty($_GET['a_id']) ? intval($_GET['a_id']) : '';
    if ($a_id > 0)
    {
        $article_row = $db->getRow('SELECT title, content FROM ' . $ecs->table('article') . ' WHERE article_id = ' . $a_id . ' AND cat_id > 0 AND is_open = 1');
        if (!empty($article_row))
        {
            $article_row['title'] = encode_output($article_row['title']);
            $replace_tag = array('<br />' , '<br/>' , '<br>' , '</p>');
            $article_row['content'] = htmlspecialchars_decode(encode_output($article_row['content']));
            $article_row['content'] = str_replace($replace_tag, '{br}' , $article_row['content']);
            $article_row['content'] = strip_tags($article_row['content']);
            $article_row['content'] = str_replace('{br}' , '<br />' , $article_row['content']);
            $smarty->assign('article_data', $article_row);
        }
    }
    $smarty->display('article.wml');
}

/* 文章列表 */
else
{
    $article_num = $db->getOne("SELECT count(*) FROM " . $ecs->table('article') . " WHERE cat_id > 0 AND is_open = 1");
    if ($article_num > 0)
    {
        $page_num = '10';
        $page = !empty($_GET['page']) ? intval($_GET['page']) : 1;
        $pages = ceil($article_num / $page_num);
        if ($page <= 0)
        {
            $page = 1;
        }
        if ($pages == 0)
        {
            $pages = 1;
        }
        if ($page > $pages)
        {
            $page = $pages;
        }
        $pagebar = get_wap_pager($article_num, $page_num, $page, 'article.php', 'page');
        $smarty->assign('pagebar', $pagebar);
        include_once(ROOT_PATH . '/includes/lib_article.php');
        $article_array = get_cat_articles(-1, $page, $page_num);
        $i = 1;
        foreach ($article_array as $key => $article_data)
        {
            $article_array[$key]['i'] = $i;
            $article_array[$key]['title'] = encode_output($article_data['title']);
            $i++;
        }
        $smarty->assign('article_array', $article_array);
    }
    $smarty->display('article_list.wml');
}

?>