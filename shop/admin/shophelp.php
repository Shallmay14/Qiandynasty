<?php

/**
 * ECSHOP 幫助信息管理程序
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發佈。
 * ============================================================================
 * $Author: liuhui $
 * $Id: shophelp.php 17063 2010-03-25 06:35:46Z liuhui $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . "includes/fckeditor/fckeditor.php");

/*初始化數據交換對像 */
$exc_article = new exchange($ecs->table("article"), $db, 'article_id', 'title');
$exc_cat     = new exchange($ecs->table("article_cat"), $db, 'cat_id', 'cat_name');

/*------------------------------------------------------ */
//-- 列出所有文章分類
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list_cat')
{
    $smarty->assign('action_link', array('text' => $_LANG['article_add'], 'href' => 'shophelp.php?act=add'));
    $smarty->assign('ur_here',     $_LANG['cat_list']);
    $smarty->assign('full_page',   1);
    $smarty->assign('list',        get_shophelp_list());

    assign_query_info();
    $smarty->display('shophelp_cat_list.htm');
}

/*------------------------------------------------------ */
//-- 分類下的文章
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list_article')
{
    $smarty->assign('ur_here',     $_LANG['article_list']);
    $smarty->assign('action_link', array('text' => $_LANG['article_add'], 'href' => 'shophelp.php?act=add&cat_id=' . $_REQUEST['cat_id']));
    $smarty->assign('full_page',   1);
    $smarty->assign('cat',         article_cat_list($_REQUEST['cat_id'], true, 'cat_id', 0, "onchange=\"location.href='?act=list_article&cat_id='+this.value\""));
    $smarty->assign('list',        shophelp_article_list($_REQUEST['cat_id']));

    assign_query_info();
    $smarty->display('shophelp_article_list.htm');
}

/*------------------------------------------------------ */
//-- 查詢分類下的文章
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query_art')
{
    $cat_id = intval($_GET['cat']);

    $smarty->assign('list', shophelp_article_list($cat_id));
    make_json_result($smarty->fetch('shophelp_article_list.htm'));
}

/*------------------------------------------------------ */
//-- 查詢
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $smarty->assign('list', get_shophelp_list());

    make_json_result($smarty->fetch('shophelp_cat_list.htm'));
}

/*------------------------------------------------------ */
//-- 添加文章
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'add')
{
    /* 權限判斷 */
    admin_priv('shophelp_manage');

    /* 創建 html editor */
    create_html_editor('FCKeditor1');

    if (empty($_REQUEST['cat_id']))
    {
        $selected = 0;
    }
    else
    {
        $selected = $_REQUEST['cat_id'];
    }
    $cat_list = article_cat_list($selected,true, 'cat_id', 0);
    $cat_list = str_replace('select please', $_LANG['select_plz'], $cat_list);
    $smarty->assign('cat_list',    $cat_list);
    $smarty->assign('ur_here',     $_LANG['article_add']);
    $smarty->assign('action_link', array('text' => $_LANG['cat_list'], 'href' => 'shophelp.php?act=list_cat'));
    $smarty->assign('form_action', 'insert');
    $smarty->display('shophelp_info.htm');
}
if ($_REQUEST['act'] == 'insert')
{
    /* 權限判斷 */
    admin_priv('shophelp_manage');

    /* 判斷是否重名 */
    $exc_article->is_only('title', $_POST['title'], $_LANG['title_exist']);

    /* 插入數據 */
    $add_time = gmtime();
    $sql = "INSERT INTO ".$ecs->table('article')."(title, cat_id, article_type, content, add_time, author) VALUES('$_POST[title]', '$_POST[cat_id]', '$_POST[article_type]','$_POST[FCKeditor1]','$add_time', '_SHOPHELP' )";
    $db ->query($sql);

    $link[0]['text'] = $_LANG['back_list'];
    $link[0]['href'] = 'shophelp.php?act=list_article&cat_id=' . $_POST['cat_id'];
    $link[1]['text'] = $_LANG['continue_add'];
    $link[1]['href'] = 'shophelp.php?act=add&cat_id=' . $_POST['cat_id'];

    /* 清除緩存 */
    clear_cache_files();

    admin_log($_POST['title'], 'add', 'shophelp');
    sys_msg($_LANG['articleadd_succeed'], 0, $link);
}

/*------------------------------------------------------ */
//-- 編輯文章
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'edit')
{
    /* 權限判斷 */
    admin_priv('shophelp_manage');

    /* 取文章數據 */
    $sql = "SELECT article_id,title, cat_id, article_type, is_open, author, author_email, keywords, content FROM " .$ecs->table('article'). " WHERE article_id='$_REQUEST[id]'";
    $article = $db->GetRow($sql);

    /* 創建 html editor */
    create_html_editor('FCKeditor1', $article['content']);

    $smarty->assign('cat_list',    article_cat_list($article['cat_id'], true, 'cat_id', 0));
    $smarty->assign('ur_here',     $_LANG['article_add']);
    $smarty->assign('action_link', array('text' => $_LANG['article_list'], 'href' => 'shophelp.php?act=list_article&cat_id='.$article['cat_id']));
    $smarty->assign('article',     $article);
    $smarty->assign('form_action', 'update');

    assign_query_info();
    $smarty->display('shophelp_info.htm');

}
if ($_REQUEST['act'] == 'update')
{
    /* 權限判斷 */
    admin_priv('shophelp_manage');

    /* 檢查重名 */
    if ($_POST['title'] != $_POST['old_title'] )
    {
        $exc_article->is_only('title', $_POST['title'], $_LANG['articlename_exist'], $_POST['id']);
    }
    /* 更新 */
    if ($exc_article->edit("title = '$_POST[title]', cat_id = '$_POST[cat_id]', article_type = '$_POST[article_type]', content = '$_POST[FCKeditor1]'", $_POST['id']))
    {
        /* 清除緩存 */
        clear_cache_files();

        $link[0]['text'] = $_LANG['back_list'];
        $link[0]['href'] = 'shophelp.php?act=list_article&cat_id='.$_POST['cat_id'];

        sys_msg(sprintf($_LANG['articleedit_succeed'], $_POST['title']), 0, $link);
        admin_log($_POST['title'], 'edit', 'shophelp');
    }
}

/*------------------------------------------------------ */
//-- 編輯分類的名稱
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_catname')
{
    check_authz_json('shophelp_manage');

    $id       = intval($_POST['id']);
    $cat_name = json_str_iconv(trim($_POST['val']));

    /* 檢查分類名稱是否重複 */
    if ($exc_cat->num("cat_name", $cat_name, $id) != 0)
    {
        make_json_error(sprintf($_LANG['catname_exist'], $cat_name));
    }
    else
    {
        if ($exc_cat->edit("cat_name = '$cat_name'", $id))
        {
            clear_cache_files();
            admin_log($cat_name, 'edit', 'shophelpcat');
            make_json_result(stripslashes($cat_name));
        }
        else
        {
            make_json_error($db->error());
        }
    }
}

/*------------------------------------------------------ */
//-- 編輯分類的排序
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_cat_order')
{
    check_authz_json('shophelp_manage');

    $id    = intval($_POST['id']);
    $order = json_str_iconv(trim($_POST['val']));

    /* 檢查輸入的值是否合法 */
    if (!preg_match("/^[0-9]+$/", $order))
    {
        make_json_result('', sprintf($_LANG['enter_int'], $order));
    }
    else
    {
        if ($exc_cat->edit("sort_order = '$order'", $id))
        {
            clear_cache_files();
            make_json_result(stripslashes($order));
        }
    }
}

/*------------------------------------------------------ */
//-- 刪除分類
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    check_authz_json('shophelp_manage');

    $id = intval($_GET['id']);

    /* 非空的分類不允許刪除 */
    if ($exc_article->num('cat_id', $id) != 0)
    {
        make_json_error(sprintf($_LANG['not_emptycat']));
    }
    else
    {
        $exc_cat->drop($id);
        clear_cache_files();
        admin_log('', 'remove', 'shophelpcat');
    }

    $url = 'shophelp.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}

/*------------------------------------------------------ */
//-- 刪除分類下的某文章
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove_art')
{
    check_authz_json('shophelp_manage');

    $id     = intval($_GET['id']);
    $cat_id = $db->getOne('SELECT cat_id FROM ' .$ecs->table('article'). " WHERE article_id='$id'");

    if ($exc_article->drop($id))
    {
        /* 清除緩存 */
        clear_cache_files();
        admin_log('', 'remove', 'shophelp');
    }
    else
    {
        make_json_error(sprintf($_LANG['remove_fail']));
    }

    $url = 'shophelp.php?act=query_art&cat='.$cat_id.'&' . str_replace('act=remove_art', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");

    exit;
}

/*------------------------------------------------------ */
//-- 添加一個新分類
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add_catname')
{
    check_authz_json('shophelp_manage');

    $cat_name = trim($_POST['cat_name']);

    if (!empty($cat_name))
    {
        if ($exc_cat->num("cat_name", $cat_name) != 0)
        {
            make_json_error($_LANG['catname_exist']);
        }
        else
        {
            $sql = "INSERT INTO " .$ecs->table('article_cat'). " (cat_name, cat_type) VALUES ('$cat_name', 0)";
            $db->query($sql);

            admin_log($cat_name, 'add', 'shophelpcat');

            ecs_header("Location: shophelp.php?act=query\n");
            exit;
        }
    }
    else
    {
        make_json_error($_LANG['js_languages']['no_catname']);
    }

    ecs_header("Location: shophelp.php?act=list_cat\n");
    exit;
}

/*------------------------------------------------------ */
//-- 編輯文章標題
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_title')
{
    check_authz_json('shophelp_manage');

    $id    = intval($_POST['id']);
    $title = json_str_iconv(trim($_POST['val']));

    /* 檢查文章標題是否有重名 */
    if ($exc_article->num('title', $title, $id) == 0)
    {
        if ($exc_article->edit("title = '$title'", $id))
        {
            clear_cache_files();
            admin_log($title, 'edit', 'shophelp');
            make_json_result(stripslashes($title));
        }
    }
    else
    {
        make_json_error(sprintf($_LANG['articlename_exist'], $title));
    }
}

/* 獲得網店幫助文章分類 */
function get_shophelp_list()
{
    $list = array();
    $sql = 'SELECT cat_id, cat_name, sort_order'.
           ' FROM ' .$GLOBALS['ecs']->table('article_cat').
           ' WHERE cat_type = 0 ORDER BY sort_order';
    $res = $GLOBALS['db']->query($sql);
    while ($rows = $GLOBALS['db']->fetchRow($res))
    {
        $sql = 'SELECT COUNT(*) FROM ' .$GLOBALS['ecs']->table('article'). " WHERE cat_id = '$rows[cat_id]'";
        $rows['num'] = $GLOBALS['db']->getOne($sql);

        $list[] = $rows;
    }

    return $list;
}

/* 獲得網店幫助某分類下的文章 */
function shophelp_article_list($cat_id)
{
    $list=array();
    $sql = 'SELECT article_id, title, article_type , add_time'.
           ' FROM ' .$GLOBALS['ecs']->table('article').
           " WHERE cat_id = '$cat_id' ORDER BY article_type DESC";
    $res = $GLOBALS['db']->query($sql);
    while ($rows = $GLOBALS['db']->fetchRow($res))
    {
        $rows['add_time'] = local_date($GLOBALS['_CFG']['time_format'], $rows['add_time']);

        $list[] = $rows;
    }

    return $list;
}

?>