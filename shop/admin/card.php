<?php

/**
 * ECSHOP 賀卡管理程序
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發佈。
 * ============================================================================
 * $Author: liuhui $
 * $Id: card.php 17063 2010-03-25 06:35:46Z liuhui $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);

$exc = new exchange($ecs->table("card"), $db, 'card_id', 'card_name');

/*------------------------------------------------------ */
//-- 包裝列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    assign_query_info();
    $smarty->assign('ur_here',     $_LANG['07_card_list']);
    $smarty->assign('action_link', array('text' => $_LANG['card_add'], 'href' => 'card.php?act=add'));
    $smarty->assign('full_page',   1);

    $cards_list = cards_list();

    $smarty->assign('card_list',    $cards_list['card_list']);
    $smarty->assign('filter',       $cards_list['filter']);
    $smarty->assign('record_count', $cards_list['record_count']);
    $smarty->assign('page_count',   $cards_list['page_count']);

    $smarty->display('card_list.htm');
}

/*------------------------------------------------------ */
//-- ajax列表
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $cards_list = cards_list();
    $smarty->assign('card_list',    $cards_list['card_list']);
    $smarty->assign('filter',       $cards_list['filter']);
    $smarty->assign('record_count', $cards_list['record_count']);
    $smarty->assign('page_count',   $cards_list['page_count']);

    $sort_flag  = sort_flag($cards_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('card_list.htm'), '', array('filter' => $cards_list['filter'], 'page_count' => $cards_list['page_count']));
}
/*------------------------------------------------------ */
//-- 刪除賀卡
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    /* 檢查權限 */
    check_authz_json('card_manage');

    $card_id = empty($_REQUEST['id']) ? 0 : intval($_REQUEST['id']);

    $name = $exc->get_name($card_id);
    $img = $exc->get_name($card_id, 'card_img');

    if ($exc->drop($card_id))
    {
        /* 刪除圖片 */
        if (!empty($img))
        {
             @unlink('../' . DATA_DIR . '/cardimg/'.$img);
        }
        admin_log(addslashes($name),'remove','card');

        $url = 'card.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

        ecs_header("Location: $url\n");
        exit;
    }
    else
    {
        make_json_error($db->error());
    }
}
/*------------------------------------------------------ */
//-- 添加新包裝
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add')
{
    /* 權限判斷 */
    admin_priv('card_manage');

    /*初始化顯示*/
    $card['card_fee']   = 0;
    $card['free_money'] = 0;

    $smarty->assign('card',        $card);
    $smarty->assign('ur_here',     $_LANG['card_add']);
    $smarty->assign('action_link', array('text' => $_LANG['07_card_list'], 'href' => 'card.php?act=list'));
    $smarty->assign('form_action', 'insert');

    assign_query_info();
    $smarty->display('card_info.htm');

}
elseif ($_REQUEST['act'] == 'insert')
{
    /* 權限判斷 */
    admin_priv('card_manage');

    /*檢查包裝名是否重複*/
    $is_only = $exc->is_only('card_name', $_POST['card_name']);

    if (!$is_only)
    {
        sys_msg(sprintf($_LANG['cardname_exist'], stripslashes($_POST['card_name'])), 1);
    }

     /*處理圖片*/
    $img_name = basename($image->upload_image($_FILES['card_img'],"cardimg"));

    /*插入數據*/
    $sql = "INSERT INTO ".$ecs->table('card')."(card_name, card_fee, free_money, card_desc, card_img)
            VALUES ('$_POST[card_name]', '$_POST[card_fee]', '$_POST[free_money]', '$_POST[card_desc]', '$img_name')";
    $db->query($sql);

    admin_log($_POST['card_name'],'add','card');

    /*添加鏈接*/
    $link[0]['text'] = $_LANG['continue_add'];
    $link[0]['href'] = 'card.php?act=add';

    $link[1]['text'] = $_LANG['back_list'];
    $link[1]['href'] = 'card.php?act=list';

    sys_msg($_POST['card_name'].$_LANG['cardadd_succeed'],0, $link);
}

/*------------------------------------------------------ */
//-- 編輯包裝
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit')
{
    /* 權限判斷 */
    admin_priv('card_manage');

    $sql = "SELECT card_id, card_name, card_fee, free_money, card_desc, card_img FROM " .$ecs->table('card'). " WHERE card_id='$_REQUEST[id]'";
    $card = $db->GetRow($sql);

    $smarty->assign('ur_here',     $_LANG['card_edit']);
    $smarty->assign('action_link', array('text' => $_LANG['07_card_list'], 'href' => 'card.php?act=list&' . list_link_postfix()));
    $smarty->assign('card',        $card);
    $smarty->assign('form_action', 'update');

    assign_query_info();
    $smarty->display('card_info.htm');
}
elseif ($_REQUEST['act'] == 'update')
{
    /* 權限判斷 */
    admin_priv('card_manage');

    if ($_POST['card_name'] != $_POST['old_cardname'])
    {
        /*檢查品牌名是否相同*/
        $is_only = $exc->is_only('card_name', $_POST['card_name'], $_POST['id']);

        if (!$is_only)
        {
            sys_msg(sprintf($_LANG['cardname_exist'], stripslashes($_POST['card_name'])), 1);
        }
    }
    $param = "card_name = '$_POST[card_name]', card_fee = '$_POST[card_fee]', free_money= $_POST[free_money], card_desc = '$_POST[card_desc]'";
    /* 處理圖片 */
    $img_name = basename($image->upload_image($_FILES['card_img'],"cardimg", $_POST['old_cardimg']));
    if ($img_name)
    {
        $param .= "  ,card_img ='$img_name' ";
    }

    if ($exc->edit($param,  $_POST['id']))
    {
        admin_log($_POST['card_name'], 'edit', 'card');

        $link[0]['text'] = $_LANG['back_list'];
        $link[0]['href'] = 'card.php?act=list&' . list_link_postfix();

        $note = sprintf($_LANG['cardedit_succeed'], $_POST['card_name']);
        sys_msg($note, 0, $link);
    }
    else
    {
        die($db->error());
    }
}

/* 刪除卡片圖片 */
elseif ($_REQUEST['act'] == 'drop_card_img')
{
    /* 權限判斷 */
    admin_priv('card_manage');
    $card_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    /* 取得logo名稱 */
    $sql = "SELECT card_img FROM " .$ecs->table('card'). " WHERE card_id = '$card_id'";
    $img_name = $db->getOne($sql);

    if (!empty($img_name))
    {
        @unlink(ROOT_PATH . DATA_DIR . '/cardimg/' .$img_name);
        $sql = "UPDATE " .$ecs->table('card'). " SET card_img = '' WHERE card_id = '$card_id'";
        $db->query($sql);
    }
    $link= array(array('text' => $_LANG['card_edit_lnk'], 'href'=>'card.php?act=edit&id=' .$card_id), array('text' => $_LANG['card_list_lnk'], 'href'=>'brand.php?act=list'));
    sys_msg($_LANG['drop_card_img_success'], 0, $link);
}
/*------------------------------------------------------ */
//-- ajax編輯卡片名字
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_card_name')
{
    check_authz_json('card_manage');
    $card_id = empty($_REQUEST['id']) ? 0 : intval($_REQUEST['id']);
    $card_name = empty($_REQUEST['val']) ? '' : json_str_iconv(trim($_REQUEST['val']));

    if (!$exc->is_only('card_name', $card_name, $card_id))
    {
        make_json_error(sprintf($_LANG['cardname_exist'], $card_name));
    }
    $old_card_name = $exc->get_name($card_id);
    if ($exc->edit("card_name='$card_name'", $card_id))
    {
        admin_log(addslashes($old_card_name), 'edit', 'card');
        make_json_result(stripcslashes($card_name));
    }
    else
    {
        make_json_error($db->error());
    }
}
/*------------------------------------------------------ */
//-- ajax編輯卡片費用
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_card_fee')
{
    check_authz_json('card_manage');
    $card_id = empty($_REQUEST['id']) ? 0 : intval($_REQUEST['id']);
    $card_fee = empty($_REQUEST['val']) ? 0.00 : floatval($_REQUEST['val']);

    $card_name = $exc->get_name($card_id);
    if ($exc->edit("card_fee ='$card_fee'", $card_id))
    {
        admin_log(addslashes($card_name), 'edit', 'card');
        make_json_result($card_fee);
    }
    else
    {
        make_json_error($db->error());
    }
}
/*------------------------------------------------------ */
//-- ajax編輯免費額度
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_free_money')
{
    check_authz_json('card_manage');
    $card_id = empty($_REQUEST['id']) ? 0 : intval($_REQUEST['id']);
    $free_money = empty($_REQUEST['val']) ? 0.00 : floatval($_REQUEST['val']);

    $card_name = $exc->get_name($card_id);
    if ($exc->edit("free_money ='$free_money'", $card_id))
    {
        admin_log(addslashes($card_name), 'edit', 'card');
        make_json_result($free_money);
    }
    else
    {
        make_json_error($db->error());
    }
}

function cards_list()
{
    $result = get_filter();
    if ($result === false)
    {
        $filter['sort_by']    = empty($_REQUEST['sort_by']) ? 'card_id' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

        /* 分頁大小 */
        $sql = "SELECT count(*) FROM " .$GLOBALS['ecs']->table('card');
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        $filter = page_and_size($filter);

        /* 查詢 */
        $sql = "SELECT card_id, card_name, card_img, card_fee, free_money, card_desc".
               " FROM ".$GLOBALS['ecs']->table('card').
               " ORDER by " . $filter['sort_by'] . ' ' . $filter['sort_order'] .
               " LIMIT " . $filter['start'] . ',' . $filter['page_size'];

        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }

    $card_list = $GLOBALS['db']->getAll($sql);

    $arr = array('card_list' => $card_list, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}

?>