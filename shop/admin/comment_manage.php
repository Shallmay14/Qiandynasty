<?php

/**
 * ECSHOP 用戶評論管理程序
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發佈。
 * ============================================================================
 * $Author: liuhui $
 * $Id: comment_manage.php 17123 2010-04-22 07:28:54Z liuhui $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/* act操作項的初始化 */
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'list';
}
else
{
    $_REQUEST['act'] = trim($_REQUEST['act']);
}

/*------------------------------------------------------ */
//-- 獲取沒有回復的評論列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    /* 檢查權限 */
    admin_priv('comment_priv');

    $smarty->assign('ur_here',      $_LANG['05_comment_manage']);
    $smarty->assign('full_page',    1);

    $list = get_comment_list();

    $smarty->assign('comment_list', $list['item']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);

    $sort_flag  = sort_flag($list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    assign_query_info();
    $smarty->display('comment_list.htm');
}

/*------------------------------------------------------ */
//-- 翻頁、搜索、排序
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'query')
{
    $list = get_comment_list();

    $smarty->assign('comment_list', $list['item']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);

    $sort_flag  = sort_flag($list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('comment_list.htm'), '',
        array('filter' => $list['filter'], 'page_count' => $list['page_count']));
}

/*------------------------------------------------------ */
//-- 回復用戶評論(同時查看評論詳情)
/*------------------------------------------------------ */
if ($_REQUEST['act']=='reply')
{
    /* 檢查權限 */
    admin_priv('comment_priv');

    $comment_info = array();
    $reply_info   = array();
    $id_value     = array();

    /* 獲取評論詳細信息並進行字符處理 */
    $sql = "SELECT * FROM " .$ecs->table('comment'). " WHERE comment_id = '$_REQUEST[id]'";
    $comment_info = $db->getRow($sql);
    $comment_info['content']  = str_replace('\r\n', '<br />', htmlspecialchars($comment_info['content']));
    $comment_info['content']  = nl2br(str_replace('\n', '<br />', $comment_info['content']));
    $comment_info['add_time'] = local_date($_CFG['time_format'], $comment_info['add_time']);

    /* 獲得評論回復內容 */
    $sql = "SELECT * FROM ".$ecs->table('comment'). " WHERE parent_id = '$_REQUEST[id]'";
    $reply_info = $db->getRow($sql);

    if (empty($reply_info))
    {
        $reply_info['content']  = '';
        $reply_info['add_time'] = '';
    }
    else
    {
        $reply_info['content']  = nl2br(htmlspecialchars($reply_info['content']));
        $reply_info['add_time'] = local_date($_CFG['time_format'], $reply_info['add_time']);
    }
    /* 獲取管理員的用戶名和Email地址 */
    $sql = "SELECT user_name, email FROM ". $ecs->table('admin_user').
           " WHERE user_id = '$_SESSION[admin_id]'";
    $admin_info = $db->getRow($sql);

    /* 取得評論的對象(文章或者商品) */
    if ($comment_info['comment_type'] == 0)
    {
        $sql = "SELECT goods_name FROM ".$ecs->table('goods').
               " WHERE goods_id = '$comment_info[id_value]'";
        $id_value = $db->getOne($sql);
    }
    else
    {
        $sql = "SELECT title FROM ".$ecs->table('article').
               " WHERE article_id='$comment_info[id_value]'";
        $id_value = $db->getOne($sql);
    }

    /* 模板賦值 */
    $smarty->assign('msg',          $comment_info); //評論信息
    $smarty->assign('admin_info',   $admin_info);   //管理員信息
    $smarty->assign('reply_info',   $reply_info);   //回復的內容
    $smarty->assign('id_value',     $id_value);  //評論的對象
    $smarty->assign('send_fail',   !empty($_REQUEST['send_ok']));

    $smarty->assign('ur_here',      $_LANG['comment_info']);
    $smarty->assign('action_link',  array('text' => $_LANG['05_comment_manage'],
    'href' => 'comment_manage.php?act=list'));

    /* 頁面顯示 */
    assign_query_info();
    $smarty->display('comment_info.htm');
}
/*------------------------------------------------------ */
//-- 處理 回復用戶評論
/*------------------------------------------------------ */
if ($_REQUEST['act']=='action')
{
    admin_priv('comment_priv');

    /* 獲取IP地址 */
    $ip     = real_ip();

    /* 獲得評論是否有回復 */
    $sql = "SELECT comment_id, content, parent_id FROM ".$ecs->table('comment').
           " WHERE parent_id = '$_REQUEST[comment_id]'";
    $reply_info = $db->getRow($sql);

    if (!empty($reply_info['content']))
    {
        /* 更新回復的內容 */
        $sql = "UPDATE ".$ecs->table('comment')." SET ".
               "email     = '$_POST[email]', ".
               "user_name = '$_POST[user_name]', ".
               "content   = '$_POST[content]', ".
               "add_time  =  '" . gmtime() . "', ".
               "ip_address= '$ip', ".
               "status    = 0".
               " WHERE comment_id = '".$reply_info['comment_id']."'";
    }
    else
    {
        /* 插入回復的評論內容 */
        $sql = "INSERT INTO ".$ecs->table('comment')." (comment_type, id_value, email, user_name , ".
                    "content, add_time, ip_address, status, parent_id) ".
               "VALUES('$_POST[comment_type]', '$_POST[id_value]','$_POST[email]', " .
                    "'$_SESSION[admin_name]','$_POST[content]','" . gmtime() . "', '$ip', '0', '$_POST[comment_id]')";
    }
    $db->query($sql);

    /* 更新當前的評論狀態為已回復並且可以顯示此條評論 */
    $sql = "UPDATE " .$ecs->table('comment'). " SET status = 1 WHERE comment_id = '$_POST[comment_id]'";
    $db->query($sql);

    /* 郵件通知處理流程 */
    if (!empty($_POST['send_email_notice']) or isset($_POST['remail']))
    {
        //獲取郵件中的必要內容
        $sql = 'SELECT user_name, email, content ' .
               'FROM ' .$ecs->table('comment') .
               " WHERE comment_id ='$_REQUEST[comment_id]'";
        $comment_info = $db->getRow($sql);

        /* 設置留言回復模板所需要的內容信息 */
        $template    = get_mail_template('recomment');

        $smarty->assign('user_name',   $comment_info['user_name']);
        $smarty->assign('recomment', $_POST['content']);
        $smarty->assign('comment', $comment_info['content']);
        $smarty->assign('shop_name',   "<a href='".$ecs->url()."'>" . $_CFG['shop_name'] . '</a>');
        $smarty->assign('send_date',   date('Y-m-d'));

        $content = $smarty->fetch('str:' . $template['template_content']);

        /* 發送郵件 */
        if (send_mail($comment_info['user_name'], $comment_info['email'], $template['template_subject'], $content, $template['is_html']))
        {
            $send_ok = 0;
        }
        else
        {
            $send_ok = 1;
        }
    }

    /* 清除緩存 */
    clear_cache_files();

    /* 記錄管理員操作 */
    admin_log(addslashes($_LANG['reply']), 'edit', 'users_comment');

    ecs_header("Location: comment_manage.php?act=reply&id=$_REQUEST[comment_id]&send_ok=$send_ok\n");
    exit;
}
/*------------------------------------------------------ */
//-- 更新評論的狀態為顯示或者禁止
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'check')
{
    if ($_REQUEST['check'] == 'allow')
    {
        /* 允許評論顯示 */
        $sql = "UPDATE " .$ecs->table('comment'). " SET status = 1 WHERE comment_id = '$_REQUEST[id]'";
        $db->query($sql);

        //add_feed($_REQUEST['id'], COMMENT_GOODS);

        /* 清除緩存 */
        clear_cache_files();

        ecs_header("Location: comment_manage.php?act=reply&id=$_REQUEST[id]\n");
        exit;
    }
    else
    {
        /* 禁止評論顯示 */
        $sql = "UPDATE " .$ecs->table('comment'). " SET status = 0 WHERE comment_id = '$_REQUEST[id]'";
        $db->query($sql);

        /* 清除緩存 */
        clear_cache_files();

        ecs_header("Location: comment_manage.php?act=reply&id=$_REQUEST[id]\n");
        exit;
    }
}

/*------------------------------------------------------ */
//-- 刪除某一條評論
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    check_authz_json('comment_priv');

    $id = intval($_GET['id']);

    $sql = "DELETE FROM " .$ecs->table('comment'). " WHERE comment_id = '$id'";
    $res = $db->query($sql);
    if ($res)
    {
        $db->query("DELETE FROM " .$ecs->table('comment'). " WHERE parent_id = '$id'");
    }

    admin_log('', 'remove', 'ads');

    $url = 'comment_manage.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}

/*------------------------------------------------------ */
//-- 批量刪除用戶評論
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'batch')
{
    admin_priv('comment_priv');
    $action = isset($_POST['sel_action']) ? trim($_POST['sel_action']) : 'deny';

    if (isset($_POST['checkboxes']))
    {
        switch ($action)
        {
            case 'remove':
                $db->query("DELETE FROM " . $ecs->table('comment') . " WHERE " . db_create_in($_POST['checkboxes'], 'comment_id'));
                $db->query("DELETE FROM " . $ecs->table('comment') . " WHERE " . db_create_in($_POST['checkboxes'], 'parent_id'));
                break;

           case 'allow' :
               $db->query("UPDATE " . $ecs->table('comment') . " SET status = 1  WHERE " . db_create_in($_POST['checkboxes'], 'comment_id'));
               break;

           case 'deny' :
               $db->query("UPDATE " . $ecs->table('comment') . " SET status = 0  WHERE " . db_create_in($_POST['checkboxes'], 'comment_id'));
               break;

           default :
               break;
        }

        clear_cache_files();
        $action = ($action == 'remove') ? 'remove' : 'edit';
        admin_log('', $action, 'adminlog');

        $link[] = array('text' => $_LANG['back_list'], 'href' => 'comment_manage.php?act=list');
        sys_msg(sprintf($_LANG['batch_drop_success'], count($_POST['checkboxes'])), 0, $link);
    }
    else
    {
        /* 提示信息 */
        $link[] = array('text' => $_LANG['back_list'], 'href' => 'comment_manage.php?act=list');
        sys_msg($_LANG['no_select_comment'], 0, $link);
    }
}

/**
 * 獲取評論列表
 * @access  public
 * @return  array
 */
function get_comment_list()
{
    /* 查詢條件 */
    $filter['keywords']     = empty($_REQUEST['keywords']) ? 0 : trim($_REQUEST['keywords']);
    if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
    {
        $filter['keywords'] = json_str_iconv($filter['keywords']);
    }
    $filter['sort_by']      = empty($_REQUEST['sort_by']) ? 'add_time' : trim($_REQUEST['sort_by']);
    $filter['sort_order']   = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

    $where = (!empty($filter['keywords'])) ? " AND content LIKE '%" . mysql_like_quote($filter['keywords']) . "%' " : '';

    $sql = "SELECT count(*) FROM " .$GLOBALS['ecs']->table('comment'). " WHERE parent_id = 0 $where";
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);

    /* 分頁大小 */
    $filter = page_and_size($filter);

    /* 獲取評論數據 */
    $arr = array();
    $sql  = "SELECT * FROM " .$GLOBALS['ecs']->table('comment'). " WHERE parent_id = 0 $where " .
            " ORDER BY $filter[sort_by] $filter[sort_order] ".
            " LIMIT ". $filter['start'] .", $filter[page_size]";
    $res  = $GLOBALS['db']->query($sql);

    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $sql = ($row['comment_type'] == 0) ?
            "SELECT goods_name FROM " .$GLOBALS['ecs']->table('goods'). " WHERE goods_id='$row[id_value]'" :
            "SELECT title FROM ".$GLOBALS['ecs']->table('article'). " WHERE article_id='$row[id_value]'";
        $row['title'] = $GLOBALS['db']->getOne($sql);

        /* 標記是否回復過 */
//        $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('comment'). " WHERE parent_id = '$row[comment_id]'";
//        $row['is_reply'] =  ($GLOBALS['db']->getOne($sql) > 0) ?
//            $GLOBALS['_LANG']['yes_reply'] : $GLOBALS['_LANG']['no_reply'];

        $row['add_time'] = local_date($GLOBALS['_CFG']['time_format'], $row['add_time']);

        $arr[] = $row;
    }
    $filter['keywords'] = stripslashes($filter['keywords']);
    $arr = array('item' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}

?>