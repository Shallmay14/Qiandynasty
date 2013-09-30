<?php

/**
 * ECSHOP  管理中心管理員留言程序
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發佈。
 * ============================================================================
 * $Author: liuhui $
 * $Id: message.php 17063 2010-03-25 06:35:46Z liuhui $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/* act操作項的初始化 */
$_REQUEST['act'] = trim($_REQUEST['act']);
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'list';
}

/*------------------------------------------------------ */
//-- 留言列表頁面
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    $smarty->assign('full_page',   1);
    $smarty->assign('ur_here',     $_LANG['msg_list']);
    $smarty->assign('action_link', array('text' => $_LANG['send_msg'], 'href' => 'message.php?act=send'));

    $list = get_message_list();

    $smarty->assign('message_list', $list['item']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);

    $sort_flag  = sort_flag($list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    assign_query_info();
    $smarty->display('message_list.htm');
}

/*------------------------------------------------------ */
//-- 翻頁、排序
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $list = get_message_list();

    $smarty->assign('message_list', $list['item']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);

    $sort_flag  = sort_flag($list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('message_list.htm'), '',
        array('filter' => $list['filter'], 'page_count' => $list['page_count']));
}

/*------------------------------------------------------ */
//-- 留言發送頁面
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'send')
{
    /* 獲取管理員列表 */
    $admin_list = $db->getAll('SELECT user_id, user_name FROM ' .$ecs->table('admin_user'));

    $smarty->assign('ur_here',     $_LANG['send_msg']);
    $smarty->assign('action_link', array('href' => 'message.php?act=list', 'text' => $_LANG['msg_list']));
    $smarty->assign('action',      'add');
    $smarty->assign('form_act',    'insert');
    $smarty->assign('admin_list',  $admin_list);

    assign_query_info();
    $smarty->display('message_info.htm');
}

/*------------------------------------------------------ */
//-- 處理留言的發送
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'insert')
{
    $rec_arr = $_POST['receiver_id'];

    /* 向所有管理員發送留言 */
    if ($rec_arr[0] == 0)
    {
        /* 獲取管理員信息 */
        $result = $db->query('SELECT user_id FROM ' .$ecs->table('admin_user') . 'WHERE user_id !=' . $_SESSION['admin_id']);
        while ($rows = $db->FetchRow($result))
        {
            $sql = "INSERT INTO " .$ecs->table('admin_message'). " (sender_id, receiver_id, sent_time, " .
                            "read_time, readed, deleted, title, message) ".
                        "VALUES ('".$_SESSION['admin_id']."', '".$rows['user_id']."', '" .gmtime(). "', ".
                            "0, '0', '0', '$_POST[title]', '$_POST[message]')";
            $db->query($sql);
        }

        /*添加鏈接*/
        $link[0]['text'] = $_LANG['back_list'];
        $link[0]['href'] = 'message.php?act=list';

        $link[1]['text'] = $_LANG['continue_send_msg'];
        $link[1]['href'] = 'message.php?act=send';

        sys_msg($_LANG['send_msg'] . "&nbsp;" . $_LANG['action_succeed'],0, $link);

        /* 記錄管理員操作 */
        admin_log(admin_log($_LANG['send_msg']), 'add', 'admin_message');
    }
    else
    {
        /* 如果是發送給指定的管理員 */
        foreach ($rec_arr AS $key => $id)
        {
            $sql = "INSERT INTO " .$ecs->table('admin_message'). " (sender_id, receiver_id, ".
                        "sent_time, read_time, readed, deleted, title, message) ".
                   "VALUES ('".$_SESSION['admin_id']."', '$id', '".gmtime()."', ".
                        "'0', '0', '0', '$_POST[title]', '$_POST[message]')";
            $db->query($sql);
        }
        admin_log(addslashes($_LANG['send_msg']), 'add', 'admin_message');

        $link[0]['text'] = $_LANG['back_list'];
        $link[0]['href'] = 'message.php?act=list';
        $link[1]['text'] = $_LANG['continue_send_msg'];
        $link[1]['href'] = 'message.php?act=send';

        sys_msg($_LANG['send_msg'] . "&nbsp;" . $_LANG['action_succeed'],0, $link);
    }
}
/*------------------------------------------------------ */
//-- 留言編輯頁面
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit')
{
    $id = intval($_REQUEST['id']);

    /* 獲取管理員列表 */
    $admin_list = $db->getAll('SELECT user_id, user_name FROM ' .$ecs->table('admin_user'));

    /* 獲得留言數據*/
    $sql = 'SELECT message_id, receiver_id, title, message'.
           'FROM ' .$ecs->table('admin_message'). " WHERE message_id='$id'";
    $msg_arr = $db->getRow($sql);

    $smarty->assign('ur_here',     $_LANG['edit_msg']);
    $smarty->assign('action_link', array('href' => 'message.php?act=list', 'text' => $_LANG['msg_list']));
    $smarty->assign('form_act',    'update');
    $smarty->assign('admin_list',  $admin_list);
    $smarty->assign('msg_arr',     $msg_arr);

    assign_query_info();
    $smarty->display('message_info.htm');
}
elseif ($_REQUEST['act'] == 'update')
{
    /* 獲得留言數據*/
    $msg_arr = array();
    $msg_arr = $db->getRow('SELECT * FROM ' .$ecs->table('admin_message')." WHERE message_id='$_POST[id]'");

    $sql = "UPDATE " .$ecs->table('admin_message'). " SET ".
           "title = '$_POST[title]',".
           "message = '$_POST[message]'".
           "WHERE sender_id = '$msg_arr[sender_id]' AND sent_time='$msg_arr[send_time]'";
    $db->query($sql);

    $link[0]['text'] = $_LANG['back_list'];
    $link[0]['href'] = 'message.php?act=list';

    sys_msg($_LANG['edit_msg'] . ' ' . $_LANG['action_succeed'],0, $link);

    /* 記錄管理員操作 */
    admin_log(addslashes($_LANG['edit_msg']), 'edit', 'admin_message');
}

/*------------------------------------------------------ */
//-- 留言查看頁面
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'view')
{
    $msg_id = intval($_REQUEST['id']);

    /* 獲得管理員留言數據 */
    $msg_arr = array();
    $sql     = "SELECT a.*, b.user_name ".
               "FROM " .$ecs->table('admin_message')." AS a ".
               "LEFT JOIN " .$ecs->table('admin_user')." AS b ON b.user_id = a.sender_id ".
               "WHERE a.message_id = '$msg_id'";
    $msg_arr = $db->getRow($sql);
    $msg_arr['title']   = nl2br(htmlspecialchars($msg_arr['title']));
    $msg_arr['message'] = nl2br(htmlspecialchars($msg_arr['message']));

    /* 如果還未閱讀 */
    if ($msg_arr['readed'] == 0)
    {
        $msg_arr['read_time'] = gmtime(); //閱讀日期為當前日期

        //更新閱讀日期和閱讀狀態
        $sql = "UPDATE " .$ecs->table('admin_message'). " SET ".
               "read_time = '" . $msg_arr['read_time'] . "', ".
               "readed = '1' ".
               "WHERE message_id = '$msg_id'";
        $db->query($sql);
    }

    //模板賦值，顯示
    $smarty->assign('ur_here',     $_LANG['view_msg']);
    $smarty->assign('action_link', array('href' => 'message.php?act=list', 'text' => $_LANG['msg_list']));
    $smarty->assign('admin_user',  $_SESSION['admin_name']);
    $smarty->assign('msg_arr',     $msg_arr);

    assign_query_info();
    $smarty->display('message_view.htm');
}

/*------------------------------------------------------ */
//--留言回復頁面
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'reply')
{
    $msg_id = intval($_REQUEST['id']);

    /* 獲得留言數據 */
    $msg_val = array();
    $sql     = "SELECT a.*, b.user_name ".
               "FROM " .$ecs->table('admin_message')." AS a ".
               "LEFT JOIN " .$ecs->table('admin_user')." AS b ON b.user_id = a.sender_id ".
               "WHERE a.message_id = '$msg_id'";
    $msg_val = $db->getRow($sql);

    $smarty->assign('ur_here',     $_LANG['reply_msg']);
    $smarty->assign('action_link', array('href' => 'message.php?act=list', 'text' => $_LANG['msg_list']));

    $smarty->assign('action',      'reply');
    $smarty->assign('form_act',    're_msg');
    $smarty->assign('msg_val',     $msg_val);

    assign_query_info();
    $smarty->display('message_info.htm');
}

/*------------------------------------------------------ */
//--留言回復的處理
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 're_msg')
{
    $sql = "INSERT INTO " .$ecs->table('admin_message'). " (sender_id, receiver_id, sent_time, ".
                "read_time, readed, deleted, title, message) ".
           "VALUES ('".$_SESSION['admin_id']."', '$_POST[receiver_id]', '" . gmtime() . "', ".
                "0, '0', '0', '$_POST[title]', '$_POST[message]')";
    $db->query($sql);

    $link[0]['text'] = $_LANG['back_list'];
    $link[0]['href'] = 'message.php?act=list';

    sys_msg($_LANG['send_msg'] . ' ' . $_LANG['action_succeed'],0, $link);

    /* 記錄管理員操作 */
    admin_log(addslashes($_LANG['send_msg']), 'add', 'admin_message');
}

/*------------------------------------------------------ */
//-- 批量刪除留言記錄
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'drop_msg')
{
    if (isset($_POST['checkboxes']))
    {
        $count = 0;
        foreach ($_POST['checkboxes'] AS $key => $id)
        {
            $sql = "UPDATE " .$ecs->table('admin_message'). " SET ".
                   "deleted = '1'".
                   "WHERE message_id = '$id' AND (sender_id='$_SESSION[admin_id]' OR receiver_id='$_SESSION[admin_id]')";
            $db->query($sql);

            $count++;
        }

        admin_log('', 'remove', 'admin_message');
        $link[] = array('text' => $_LANG['back_list'], 'href' => 'message.php?act=list');
        sys_msg(sprintf($_LANG['batch_drop_success'], $count), 0, $link);
    }
    else
    {
        sys_msg($_LANG['no_select_msg'], 1);
    }
}

/*------------------------------------------------------ */
//-- 刪除留言
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    $id = intval($_GET['id']);

    $sql = "UPDATE ".$ecs->table('admin_message')." SET deleted=1 ".
            " WHERE message_id=$id AND (sender_id='$_SESSION[admin_id]' OR receiver_id='$_SESSION[admin_id]')";
    $db->query($sql);

    $url = 'message.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}

/**
 *  獲取管理員留言列表
 *
 * @return void
 */
function get_message_list()
{
    /* 查詢條件 */
    $filter['sort_by']    = empty($_REQUEST['sort_by'])    ? 'sent_time' : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
    $filter['msg_type']   = empty($_REQUEST['msg_type'])   ? 0 : intval($_REQUEST['msg_type']);

    /* 查詢條件 */
    switch ($filter['msg_type'])
    {
        case 1:
            $where = " a.receiver_id='" .$_SESSION['admin_id']. "'";
            break;
        case 2:
           $where = " a.sender_id='".$_SESSION['admin_id']."' AND a.deleted='0'";
           break;
        case 3:
           $where = " a.readed='0' AND a.receiver_id='".$_SESSION['admin_id']."' AND a.deleted='0'";
           break;
        case 4:
           $where = " a.readed='1' AND a.receiver_id='".$_SESSION['admin_id']."' AND a.deleted='0'";
           break;
        default:
           $where = " (a.receiver_id='".$_SESSION['admin_id']."' OR a.sender_id='" .$_SESSION['admin_id']. "') AND a.deleted='0'";
    }

    $sql = "SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('admin_message')." AS a WHERE 1 AND ". $where;
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);

    /* 分頁大小 */
    $filter = page_and_size($filter);

    $sql = "SELECT a.message_id,a.sender_id,a.receiver_id,a.sent_time,a.read_time,a.deleted,a.title,a.message,b.user_name".
            " FROM ".$GLOBALS['ecs']->table('admin_message')." AS a,".$GLOBALS['ecs']->table('admin_user')." AS b ".
            " WHERE a.sender_id=b.user_id AND $where ".
            " ORDER BY ".$filter['sort_by']." ".$filter['sort_order'].
            " LIMIT ". $filter['start'] .", $filter[page_size]";
    $row = $GLOBALS['db']->getAll($sql);

    foreach ($row AS $key=>$val)
    {
        $row[$key]['sent_time'] = local_date($GLOBALS['_CFG']['time_format'], $val['sent_time']);
        $row[$key]['read_time'] = local_date($GLOBALS['_CFG']['time_format'], $val['read_time']);
    }

    $arr = array('item' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}

?>
