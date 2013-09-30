<?php

/**
 * ECSHOP 網店信息管理頁面
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發佈。
 * ============================================================================
 * $Author: liuhui $
 * $Id: shopinfo.php 17063 2010-03-25 06:35:46Z liuhui $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . "includes/fckeditor/fckeditor.php");

$exc = new exchange($ecs->table("article"), $db, 'article_id', 'title');

/*------------------------------------------------------ */
//-- 文章列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    $smarty->assign('ur_here',      $_LANG['shop_info']);
    $smarty->assign('action_link',  array('text' => $_LANG['shopinfo_add'], 'href'=>'shopinfo.php?act=add'));
    $smarty->assign('full_page',    1);
    $smarty->assign('list',         shopinfo_article_list());

    assign_query_info();
    $smarty->display('shopinfo_list.htm');
}

/*------------------------------------------------------ */
//-- 查詢
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $smarty->assign('list',     shopinfo_article_list());

    make_json_result($smarty->fetch('shopinfo_list.htm'));
}

/*------------------------------------------------------ */
//-- 添加新文章
/*------------------------------------------------------ */
if ($_REQUEST['act'] =='add')
{
    /* 權限判斷 */
    admin_priv('shopinfo_manage');

    /* 創建 html editor */
    create_html_editor('FCKeditor1');

    /* 初始化 */
    $article['article_type'] = 0;

    $smarty->assign('ur_here',      $_LANG['shopinfo_add']);
    $smarty->assign('action_link',  array('text' => $_LANG['shopinfo_list'], 'href'=>'shopinfo.php?act=list'));
    $smarty->assign('form_action','insert');

    assign_query_info();
    $smarty->display('shopinfo_info.htm');
}
if ($_REQUEST['act'] == 'insert')
{
    /* 權限判斷 */
    admin_priv('shopinfo_manage');

    /* 判斷是否重名 */
    $is_only = $exc->is_only('title', $_POST['title']);

    if (!$is_only)
    {
        sys_msg(sprintf($_LANG['title_exist'], stripslashes($_POST['title'])), 1);
    }

    /* 插入數據 */
    $add_time = gmtime();
    $sql = "INSERT INTO ".$ecs->table('article')."(title, cat_id, content, add_time) VALUES('$_POST[title]', '0', '$_POST[FCKeditor1]','$add_time' )";
    $db->query($sql);

    $link[0]['text'] = $_LANG['continue_add'];
    $link[0]['href'] = 'shopinfo.php?act=add';

    $link[1]['text'] = $_LANG['back_list'];
    $link[1]['href'] = 'shopinfo.php?act=list';

    /* 清除緩存 */
    clear_cache_files();

    admin_log($_POST['title'], 'add', 'shopinfo');
    sys_msg($_LANG['articleadd_succeed'],0, $link);
}

/*------------------------------------------------------ */
//-- 文章編輯
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'edit')
{
    /* 權限判斷 */
    admin_priv('shopinfo_manage');

    /* 取得文章數據 */
    $sql = "SELECT article_id, title, content FROM ".$ecs->table('article')."WHERE article_id =".$_REQUEST['id'];
    $article = $db->GetRow($sql);

    /* 創建 html editor */
    create_html_editor('FCKeditor1', $article['content']);

    $smarty->assign('ur_here',     $_LANG['article_add']);
    $smarty->assign('action_link', array('text' => $_LANG['shopinfo_list'], 'href'=>'shopinfo.php?act=list'));
    $smarty->assign('article',     $article);
    $smarty->assign('form_action', 'update');
    $smarty->display('shopinfo_info.htm');
}
if ($_REQUEST['act'] == 'update')
{
    /* 權限判斷 */
    admin_priv('shopinfo_manage');

    /* 檢查重名 */
    if ($_POST['title'] != $_POST['old_title'])
    {
        $is_only = $exc->is_only('title', $_POST['title'], $_POST['id']);

        if (!$is_only)
        {
            sys_msg(sprintf($_LANG['title_exist'], stripslashes($_POST['title'])), 1);
        }
    }

    /* 更新數據 */
    $cur_time = gmtime();
    if ($exc->edit("title='$_POST[title]', content='$_POST[FCKeditor1]',add_time ='$cur_time'",$_POST['id']))
    {
        /* 清除緩存 */
        clear_cache_files();

        $link[0]['text'] = $_LANG['back_list'];
        $link[0]['href'] = 'shopinfo.php?act=list';

        sys_msg(sprintf($_LANG['articleedit_succeed'], $_POST['title']), 0, $link);
        admin_log($_POST['title'], 'edit', 'shopinfo');
    }
}

/*------------------------------------------------------ */
//-- 編輯文章主題
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_title')
{
    check_authz_json('shopinfo_manage');

    $id    = intval($_POST['id']);
    $title = json_str_iconv(trim($_POST['val']));

    /* 檢查文章標題是否有重名 */
    if ($exc->num('title', $title, $id) == 0)
    {
        if ($exc->edit("title = '$title'", $id))
        {
            clear_cache_files();
            admin_log($title, 'edit', 'shopinfo');
            make_json_result(stripslashes($title));
        }
    }
    else
    {
        make_json_error(sprintf($_LANG['title_exist'], $title));
    }
}

/*------------------------------------------------------ */
//-- 刪除文章
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    check_authz_json('shopinfo_manage');

    $id = intval($_GET['id']);

    /* 獲得文章主題 */
    $title = $exc->get_name($id);
    if ($exc->drop($id))
    {
        clear_cache_files();
        admin_log(addslashes($title), 'remove', 'shopinfo');
    }

    $url = 'shopinfo.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}

/* 獲取網店信息文章數據 */
function shopinfo_article_list()
{
    $list = array();
    $sql  = 'SELECT article_id, title ,add_time'.
            ' FROM ' .$GLOBALS['ecs']->table('article').
            ' WHERE cat_id = 0 ORDER BY article_id';
    $res = $GLOBALS['db']->query($sql);
    while ($rows = $GLOBALS['db']->fetchRow($res))
    {
        $rows['add_time'] = local_date($GLOBALS['_CFG']['time_format'], $rows['add_time']);

        $list[] = $rows;
    }

    return $list;
}

?>