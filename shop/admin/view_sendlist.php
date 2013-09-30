<?php

/**
 * ECSHOP 程序說明
 * ===========================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發佈。
 * ==========================================================
 * $Author: liuhui $
 * $Id: view_sendlist.php 17063 2010-03-25 06:35:46Z liuhui $
 */

define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
admin_priv('view_sendlist');
if ($_REQUEST['act'] == 'list')
{
    $listdb = get_sendlist();
    $smarty->assign('ur_here',      $_LANG['view_sendlist']);
    $smarty->assign('full_page',  1);

    $smarty->assign('listdb',      $listdb['listdb']);
    $smarty->assign('filter',       $listdb['filter']);
    $smarty->assign('record_count', $listdb['record_count']);
    $smarty->assign('page_count',   $listdb['page_count']);

    assign_query_info();
    $smarty->display('view_sendlist.htm');
}
elseif ($_REQUEST['act'] == 'query')
{
    $listdb = get_sendlist();
    $smarty->assign('listdb',      $listdb['listdb']);
    $smarty->assign('filter',       $listdb['filter']);
    $smarty->assign('record_count', $listdb['record_count']);
    $smarty->assign('page_count',   $listdb['page_count']);

    $sort_flag  = sort_flag($listdb['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('view_sendlist.htm'), '', array('filter' => $listdb['filter'], 'page_count' => $listdb['page_count']));
}
elseif ($_REQUEST['act'] == 'del')
{
    $id = (int)$_REQUEST['id'];
    $sql = "DELETE FROM " . $GLOBALS['ecs']->table('email_sendlist') . " WHERE id = '$id' LIMIT 1";
    $db->query($sql);
    $links[] = array('text' => $_LANG['view_sendlist'], 'href' => 'view_sendlist.php?act=list');
    sys_msg($_LANG['del_ok'], 0, $links);
}

/*------------------------------------------------------ */
//-- 批量刪除
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'batch_remove')
{
    /* 檢查權限 */
    if (isset($_POST['checkboxes']))
    {
        $sql = "DELETE FROM " . $ecs->table('email_sendlist') . " WHERE id " . db_create_in($_POST['checkboxes']);
        $db->query($sql);

        $links[] = array('text' => $_LANG['view_sendlist'], 'href' => 'view_sendlist.php?act=list');
        sys_msg($_LANG['del_ok'], 0, $links);
    }
    else
    {
        $links[] = array('text' => $_LANG['view_sendlist'], 'href' => 'view_sendlist.php?act=list');
        sys_msg($_LANG['no_select'], 0, $links);
    }
}

/*------------------------------------------------------ */
//-- 批量發送
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'batch_send')
{
    /* 檢查權限 */
    if (isset($_POST['checkboxes']))
    {
        $sql = "SELECT * FROM " . $ecs->table('email_sendlist') . "WHERE id " . db_create_in($_POST['checkboxes']) . " ORDER BY pri DESC, last_send ASC LIMIT 1";
        $row = $db->getRow($sql);

        //發送列表為空
        if (empty($row['id']))
        {
            $links[] = array('text' => $_LANG['view_sendlist'], 'href' => 'view_sendlist.php?act=list');
            sys_msg($_LANG['mailsend_null'], 0, $links);
        }

        $sql = "SELECT * FROM " . $ecs->table('email_sendlist') . "WHERE id " . db_create_in($_POST['checkboxes']) . " ORDER BY pri DESC, last_send ASC";
        $res = $db->query($sql);
        while ($row = $db->fetchRow($res))
        {
            //發送列表不為空，郵件地址為空
            if (!empty($row['id']) && empty($row['email']))
            {
                $sql = "DELETE FROM " . $ecs->table('email_sendlist') . " WHERE id = '$row[id]'";
                $db->query($sql);
                continue;
            }

            //查詢相關模板
            $sql = "SELECT * FROM " . $ecs->table('mail_templates') . " WHERE template_id = '$row[template_id]'";
            $rt = $db->getRow($sql);

            //如果是模板，則將已存入email_sendlist的內容作為郵件內容
            //否則即是雜質，將mail_templates調出的內容作為郵件內容
            if ($rt['type'] == 'template')
            {
                $rt['template_content'] = $row['email_content'];
            }

            if ($rt['template_id'] && $rt['template_content'])
            {
                if (send_mail('', $row['email'], $rt['template_subject'], $rt['template_content'], $rt['is_html']))
                {
                    //發送成功

                    //從列表中刪除
                    $sql = "DELETE FROM " . $ecs->table('email_sendlist') . " WHERE id = '$row[id]'";
                    $db->query($sql);
                }
                else
                {
                    //發送出錯

                    if ($row['error'] < 3)
                    {
                        $time = time();
                        $sql = "UPDATE " . $ecs->table('email_sendlist') . " SET error = error + 1, pri = 0, last_send = '$time' WHERE id = '$row[id]'";
                    }
                    else
                    {
                        //將出錯超次的紀錄刪除
                        $sql = "DELETE FROM " . $ecs->table('email_sendlist') . " WHERE id = '$row[id]'";
                    }
                    $db->query($sql);
                }
            }
            else
            {
                //無效的郵件隊列
                $sql = "DELETE FROM " . $ecs->table('email_sendlist') . " WHERE id = '$row[id]'";
                $db->query($sql);
            }
        }

        $links[] = array('text' => $_LANG['view_sendlist'], 'href' => 'view_sendlist.php?act=list');
        sys_msg($_LANG['mailsend_finished'], 0, $links);
    }
    else
    {
        $links[] = array('text' => $_LANG['view_sendlist'], 'href' => 'view_sendlist.php?act=list');
        sys_msg($_LANG['no_select'], 0, $links);
    }
}

/*------------------------------------------------------ */
//-- 全部發送
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'all_send')
{
    $sql = "SELECT * FROM " . $ecs->table('email_sendlist') . " ORDER BY pri DESC, last_send ASC LIMIT 1";
    $row = $db->getRow($sql);

    //發送列表為空
    if (empty($row['id']))
    {
        $links[] = array('text' => $_LANG['view_sendlist'], 'href' => 'view_sendlist.php?act=list');
        sys_msg($_LANG['mailsend_null'], 0, $links);
    }

    $sql = "SELECT * FROM " . $ecs->table('email_sendlist') . " ORDER BY pri DESC, last_send ASC";
    $res = $db->query($sql);
    while ($row = $db->fetchRow($res))
    {
        //發送列表不為空，郵件地址為空
        if (!empty($row['id']) && empty($row['email']))
        {
            $sql = "DELETE FROM " . $ecs->table('email_sendlist') . " WHERE id = '$row[id]'";
            $db->query($sql);
            continue;
        }

        //查詢相關模板
        $sql = "SELECT * FROM " . $ecs->table('mail_templates') . " WHERE template_id = '$row[template_id]'";
        $rt = $db->getRow($sql);

        //如果是模板，則將已存入email_sendlist的內容作為郵件內容
        //否則即是雜質，將mail_templates調出的內容作為郵件內容
        if ($rt['type'] == 'template')
        {
            $rt['template_content'] = $row['email_content'];
        }

        if ($rt['template_id'] && $rt['template_content'])
        {
            if (send_mail('', $row['email'], $rt['template_subject'], $rt['template_content'], $rt['is_html']))
            {
                //發送成功

                //從列表中刪除
                $sql = "DELETE FROM " . $ecs->table('email_sendlist') . " WHERE id = '$row[id]'";
                $db->query($sql);
            }
            else
            {
                //發送出錯

                if ($row['error'] < 3)
                {
                    $time = time();
                    $sql = "UPDATE " . $ecs->table('email_sendlist') . " SET error = error + 1, pri = 0, last_send = '$time' WHERE id = '$row[id]'";
                }
                else
                {
                    //將出錯超次的紀錄刪除
                    $sql = "DELETE FROM " . $ecs->table('email_sendlist') . " WHERE id = '$row[id]'";
                }
                $db->query($sql);
            }
        }
        else
        {
            //無效的郵件隊列
            $sql = "DELETE FROM " . $ecs->table('email_sendlist') . " WHERE id = '$row[id]'";
            $db->query($sql);
        }
    }

    $links[] = array('text' => $_LANG['view_sendlist'], 'href' => 'view_sendlist.php?act=list');
    sys_msg($_LANG['mailsend_finished'], 0, $links);
}

function get_sendlist()
{
    $result = get_filter();
    if ($result === false)
    {
        $filter['sort_by']      = empty($_REQUEST['sort_by']) ? 'pri' : trim($_REQUEST['sort_by']);
        $filter['sort_order']   = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

        $sql = "SELECT count(*) FROM " . $GLOBALS['ecs']->table('email_sendlist') . " e LEFT JOIN " . $GLOBALS['ecs']->table('mail_templates') . " m ON e.template_id = m.template_id";
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分頁大小 */
        $filter = page_and_size($filter);

        /* 查詢 */
        $sql = "SELECT e.id, e.email, e.pri, e.error, FROM_UNIXTIME(e.last_send) AS last_send, m.template_subject, m.type FROM " . $GLOBALS['ecs']->table('email_sendlist') . " e LEFT JOIN " . $GLOBALS['ecs']->table('mail_templates') . " m ON e.template_id = m.template_id" .
            " ORDER by " . $filter['sort_by'] . ' ' . $filter['sort_order'] .
           " LIMIT " . $filter['start'] . ",$filter[page_size]";
        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }

    $listdb = $GLOBALS['db']->getAll($sql);

    $arr = array('listdb' => $listdb, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}
?>