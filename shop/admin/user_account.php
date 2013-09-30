<?php

/**
 * ECSHOP 會員帳目管理(包括預付款，餘額)
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發佈。
 * ============================================================================
 * $Author: liuhui $
 * $Id: user_account.php 17063 2010-03-25 06:35:46Z liuhui $
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
//-- 會員餘額記錄列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    /* 權限判斷 */
    admin_priv('surplus_manage');

    /* 指定會員的ID為查詢條件 */
    $user_id = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;

    /* 獲得支付方式列表 */
    $payment = array();
    $sql = "SELECT pay_id, pay_name FROM ".$ecs->table('payment').
           " WHERE enabled = 1 AND pay_code != 'cod' ORDER BY pay_id";
    $res = $db->query($sql);

    while ($row = $db->fetchRow($res))
    {
        $payment[$row['pay_name']] = $row['pay_name'];
    }

    /* 模板賦值 */
    if (isset($_REQUEST['process_type']))
    {
        $smarty->assign('process_type_' . intval($_REQUEST['process_type']), 'selected="selected"');
    }
    if (isset($_REQUEST['is_paid']))
    {
        $smarty->assign('is_paid_' . intval($_REQUEST['is_paid']), 'selected="selected"');
    }
    $smarty->assign('ur_here',       $_LANG['09_user_account']);
    $smarty->assign('id',            $user_id);
    $smarty->assign('payment_list',  $payment);
    $smarty->assign('action_link',   array('text' => $_LANG['surplus_add'], 'href'=>'user_account.php?act=add'));

    $list = account_list();
    $smarty->assign('list',         $list['list']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);
    $smarty->assign('full_page',    1);

    assign_query_info();
    $smarty->display('user_account_list.htm');
}

/*------------------------------------------------------ */
//-- 添加/編輯會員餘額頁面
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add' || $_REQUEST['act'] == 'edit')
{
    admin_priv('surplus_manage'); //權限判斷

    $ur_here  = ($_REQUEST['act'] == 'add') ? $_LANG['surplus_add'] : $_LANG['surplus_edit'];
    $form_act = ($_REQUEST['act'] == 'add') ? 'insert' : 'update';
    $id       = isset($_GET['id']) ? intval($_GET['id']) : 0;

    /* 獲得支付方式列表, 不包括「貨到付款」 */
    $user_account = array();
    $payment = array();
    $sql = "SELECT pay_id, pay_name FROM ".$ecs->table('payment').
           " WHERE enabled = 1 AND pay_code != 'cod' ORDER BY pay_id";
    $res = $db->query($sql);

    while ($row = $db->fetchRow($res))
    {
        $payment[$row['pay_name']] = $row['pay_name'];
    }

    if ($_REQUEST['act'] == 'edit')
    {
        /* 取得餘額信息 */
        $user_account = $db->getRow("SELECT * FROM " .$ecs->table('user_account') . " WHERE id = '$id'");

        // 如果是負數，去掉前面的符號
        $user_account['amount'] = str_replace('-', '', $user_account['amount']);

        /* 取得會員名稱 */
        $sql = "SELECT user_name FROM " .$ecs->table('users'). " WHERE user_id = '$user_account[user_id]'";
        $user_name = $db->getOne($sql);
    }
    else
    {
        $surplus_type = '';
        $user_name    = '';
    }

    /* 模板賦值 */
    $smarty->assign('ur_here',          $ur_here);
    $smarty->assign('form_act',         $form_act);
    $smarty->assign('payment_list',     $payment);
    $smarty->assign('action',           $_REQUEST['act']);
    $smarty->assign('user_surplus',     $user_account);
    $smarty->assign('user_name',        $user_name);
    if ($_REQUEST['act'] == 'add')
    {
        $href = 'user_account.php?act=list';
    }
    else
    {
        $href = 'user_account.php?act=list&' . list_link_postfix();
    }
    $smarty->assign('action_link', array('href' => $href, 'text' => $_LANG['09_user_account']));

    assign_query_info();
    $smarty->display('user_account_info.htm');
}

/*------------------------------------------------------ */
//-- 添加/編輯會員餘額的處理部分
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'insert' || $_REQUEST['act'] == 'update')
{
    /* 權限判斷 */
    admin_priv('surplus_manage');

    /* 初始化變量 */
    $id           = isset($_POST['id'])            ? intval($_POST['id'])             : 0;
    $is_paid      = !empty($_POST['is_paid'])      ? intval($_POST['is_paid'])        : 0;
    $amount       = !empty($_POST['amount'])       ? floatval($_POST['amount'])       : 0;
    $process_type = !empty($_POST['process_type']) ? intval($_POST['process_type'])   : 0;
    $user_name    = !empty($_POST['user_id'])      ? trim($_POST['user_id'])          : '';
    $admin_note   = !empty($_POST['admin_note'])   ? trim($_POST['admin_note'])       : '';
    $user_note    = !empty($_POST['user_note'])    ? trim($_POST['user_note'])        : '';
    $payment      = !empty($_POST['payment'])      ? trim($_POST['payment'])          : '';

    $user_id = $db->getOne("SELECT user_id FROM " .$ecs->table('users'). " WHERE user_name = '$user_name'");

    /* 此會員是否存在 */
    if ($user_id == 0)
    {
        $link[] = array('text' => $_LANG['go_back'], 'href'=>'javascript:history.back(-1)');
        sys_msg($_LANG['username_not_exist'], 0, $link);
    }

    /* 退款，檢查餘額是否足夠 */
    if ($process_type == 1)
    {
        $user_account = get_user_surplus($user_id);

        /* 如果扣除的餘額多於此會員擁有的餘額，提示 */
        if ($amount > $user_account)
        {
            $link[] = array('text' => $_LANG['go_back'], 'href'=>'javascript:history.back(-1)');
            sys_msg($_LANG['surplus_amount_error'], 0, $link);
        }
    }

    if ($_REQUEST['act'] == 'insert')
    {
        /* 入庫的操作 */
        if ($process_type == 1)
        {
            $amount = (-1) * $amount;
        }
        $sql = "INSERT INTO " .$ecs->table('user_account').
               " VALUES ('', '$user_id', '$_SESSION[admin_name]', '$amount', '".gmtime()."', '".gmtime()."', '$admin_note', '$user_note', '$process_type', '$payment', '$is_paid')";
        $db->query($sql);
        $id = $db->insert_id();
    }
    else
    {
        /* 更新數據表 */
        $sql = "UPDATE " .$ecs->table('user_account'). " SET ".
               "admin_note   = '$admin_note', ".
               "user_note    = '$user_note', ".
               "payment      = '$payment' ".
              "WHERE id      = '$id'";
        $db->query($sql);
    }

    // 更新會員餘額數量
    if ($is_paid == 1)
    {
        $change_desc = $amount > 0 ? $_LANG['surplus_type_0'] : $_LANG['surplus_type_1'];
        $change_type = $amount > 0 ? ACT_SAVING : ACT_DRAWING;
        log_account_change($user_id, $amount, 0, 0, 0, $change_desc, $change_type);
    }

    //如果是預付款並且未確認，向pay_log插入一條記錄
    if ($process_type == 0 && $is_paid == 0)
    {
        include_once(ROOT_PATH . 'includes/lib_order.php');

        /* 取支付方式信息 */
        $payment_info = array();
        $payment_info = $db->getRow('SELECT * FROM ' . $ecs->table('payment').
                                    " WHERE pay_name = '$payment' AND enabled = '1'");
        //計算支付手續費用
        $pay_fee   = pay_fee($payment_info['pay_id'], $amount, 0);
        $total_fee = $pay_fee + $amount;

        /* 插入 pay_log */
        $sql = 'INSERT INTO ' . $ecs->table('pay_log') . " (order_id, order_amount, order_type, is_paid)" .
                " VALUES ('$id', '$total_fee', '" .PAY_SURPLUS. "', 0)";
        $db->query($sql);
    }

    /* 記錄管理員操作 */
    if ($_REQUEST['act'] == 'update')
    {
        admin_log($user_name, 'edit', 'user_surplus');
    }
    else
    {
        admin_log($user_name, 'add', 'user_surplus');
    }

    /* 提示信息 */
    if ($_REQUEST['act'] == 'insert')
    {
        $href = 'user_account.php?act=list';
    }
    else
    {
        $href = 'user_account.php?act=list&' . list_link_postfix();
    }
    $link[0]['text'] = $_LANG['back_list'];
    $link[0]['href'] = $href;

    $link[1]['text'] = $_LANG['continue_add'];
    $link[1]['href'] = 'user_account.php?act=add';

    sys_msg($_LANG['attradd_succed'], 0, $link);
}

/*------------------------------------------------------ */
//-- 審核會員餘額頁面
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'check')
{
    /* 檢查權限 */
    admin_priv('surplus_manage');

    /* 初始化 */
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    /* 如果參數不合法，返回 */
    if ($id == 0)
    {
        ecs_header("Location: user_account.php?act=list\n");
        exit;
    }

    /* 查詢當前的預付款信息 */
    $account = array();
    $account = $db->getRow("SELECT * FROM " .$ecs->table('user_account'). " WHERE id = '$id'");
    $account['add_time'] = local_date($_CFG['time_format'], $account['add_time']);

    //餘額類型:預付款，退款申請，購買商品，取消訂單
    if ($account['process_type'] == 0)
    {
        $process_type = $_LANG['surplus_type_0'];
    }
    elseif ($account['process_type'] == 1)
    {
        $process_type = $_LANG['surplus_type_1'];
    }
    elseif ($account['process_type'] == 2)
    {
        $process_type = $_LANG['surplus_type_2'];
    }
    else
    {
        $process_type = $_LANG['surplus_type_3'];
    }

    $sql = "SELECT user_name FROM " .$ecs->table('users'). " WHERE user_id = '$account[user_id]'";
    $user_name = $db->getOne($sql);

    /* 模板賦值 */
    $smarty->assign('ur_here',      $_LANG['check']);
    $account['user_note'] = htmlspecialchars($account['user_note']);
    $smarty->assign('surplus',      $account);
    $smarty->assign('process_type', $process_type);
    $smarty->assign('user_name',    $user_name);
    $smarty->assign('id',           $id);
    $smarty->assign('action_link',  array('text' => $_LANG['09_user_account'],
    'href'=>'user_account.php?act=list&' . list_link_postfix()));

    /* 頁面顯示 */
    assign_query_info();
    $smarty->display('user_account_check.htm');
}

/*------------------------------------------------------ */
//-- 更新會員餘額的狀態
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'action')
{
    /* 檢查權限 */
    admin_priv('surplus_manage');

    /* 初始化 */
    $id         = isset($_POST['id'])         ? intval($_POST['id'])             : 0;
    $is_paid    = isset($_POST['is_paid'])    ? intval($_POST['is_paid'])        : 0;
    $admin_note = isset($_POST['admin_note']) ? trim($_POST['admin_note'])       : '';

    /* 如果參數不合法，返回 */
    if ($id == 0 || empty($admin_note))
    {
        ecs_header("Location: user_account.php?act=list\n");
        exit;
    }

    /* 查詢當前的預付款信息 */
    $account = array();
    $account = $db->getRow("SELECT * FROM " .$ecs->table('user_account'). " WHERE id = '$id'");
    $amount  = $account['amount'];

    //如果狀態為未確認
    if ($account['is_paid'] == 0)
    {
        //如果是退款申請, 並且已完成,更新此條記錄,扣除相應的餘額
        if ($is_paid == '1' && $account['process_type'] == '1')
        {
            $user_account = get_user_surplus($account['user_id']);
            $fmt_amount   = str_replace('-', '', $amount);

            //如果扣除的餘額多於此會員擁有的餘額，提示
            if ($fmt_amount > $user_account)
            {
                $link[] = array('text' => $_LANG['go_back'], 'href'=>'javascript:history.back(-1)');
                sys_msg($_LANG['surplus_amount_error'], 0, $link);
            }

            update_user_account($id, $amount, $admin_note, $is_paid);

            //更新會員餘額數量
            log_account_change($account['user_id'], $amount, 0, 0, 0, $_LANG['surplus_type_1'], ACT_DRAWING);
        }
        elseif ($is_paid == '1' && $account['process_type'] == '0')
        {
            //如果是預付款，並且已完成, 更新此條記錄，增加相應的餘額
            update_user_account($id, $amount, $admin_note, $is_paid);

            //更新會員餘額數量
            log_account_change($account['user_id'], $amount, 0, 0, 0, $_LANG['surplus_type_0'], ACT_SAVING);

        }
        elseif ($is_paid == '0')
        {
            /* 否則更新信息 */
            $sql = "UPDATE " .$ecs->table('user_account'). " SET ".
                   "admin_user    = '$_SESSION[admin_name]', ".
                   "admin_note    = '$admin_note', ".
                   "is_paid       = 0 WHERE id = '$id'";
            $db->query($sql);
        }

        /* 記錄管理員日誌 */
        admin_log('(' . addslashes($_LANG['check']) . ')' . $admin_note, 'edit', 'user_surplus');

        /* 提示信息 */
        $link[0]['text'] = $_LANG['back_list'];
        $link[0]['href'] = 'user_account.php?act=list&' . list_link_postfix();

        sys_msg($_LANG['attradd_succed'], 0, $link);
    }
}

/*------------------------------------------------------ */
//-- ajax帳戶信息列表
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $list = account_list();
    $smarty->assign('list',         $list['list']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);

    $sort_flag  = sort_flag($list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('user_account_list.htm'), '', array('filter' => $list['filter'], 'page_count' => $list['page_count']));
}
/*------------------------------------------------------ */
//-- ajax刪除一條信息
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    /* 檢查權限 */
    check_authz_json('surplus_manage');
    $id = @intval($_REQUEST['id']);
    $sql = "SELECT u.user_name FROM " . $ecs->table('users') . " AS u, " .
           $ecs->table('user_account') . " AS ua " .
           " WHERE u.user_id = ua.user_id AND ua.id = '$id' ";
    $user_name = $db->getOne($sql);
    $sql = "DELETE FROM " . $ecs->table('user_account') . " WHERE id = '$id'";
    if ($db->query($sql, 'SILENT'))
    {
       admin_log(addslashes($user_name), 'remove', 'user_surplus');
       $url = 'user_account.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);
       ecs_header("Location: $url\n");
       exit;
    }
    else
    {
        make_json_error($db->error());
    }
}

/*------------------------------------------------------ */
//-- 會員餘額函數部分
/*------------------------------------------------------ */
/**
 * 查詢會員餘額的數量
 * @access  public
 * @param   int     $user_id        會員ID
 * @return  int
 */
function get_user_surplus($user_id)
{
    $sql = "SELECT SUM(user_money) FROM " .$GLOBALS['ecs']->table('account_log').
           " WHERE user_id = '$user_id'";

    return $GLOBALS['db']->getOne($sql);
}

/**
 * 更新會員賬目明細
 *
 * @access  public
 * @param   array     $id          帳目ID
 * @param   array     $admin_note  管理員描述
 * @param   array     $amount      操作的金額
 * @param   array     $is_paid     是否已完成
 *
 * @return  int
 */
function update_user_account($id, $amount, $admin_note, $is_paid)
{
    $sql = "UPDATE " .$GLOBALS['ecs']->table('user_account'). " SET ".
           "admin_user  = '$_SESSION[admin_name]', ".
           "amount      = '$amount', ".
           "paid_time   = '".gmtime()."', ".
           "admin_note  = '$admin_note', ".
           "is_paid     = '$is_paid' WHERE id = '$id'";
    return $GLOBALS['db']->query($sql);
}

/**
 *
 *
 * @access  public
 * @param
 *
 * @return void
 */
function account_list()
{
    $result = get_filter();
    if ($result === false)
    {
        /* 過濾列表 */
        $filter['user_id'] = !empty($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
        $filter['keywords'] = empty($_REQUEST['keywords']) ? '' : trim($_REQUEST['keywords']);
        if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
        {
            $filter['keywords'] = json_str_iconv($filter['keywords']);
        }

        $filter['process_type'] = isset($_REQUEST['process_type']) ? intval($_REQUEST['process_type']) : -1;
        $filter['payment'] = empty($_REQUEST['payment']) ? '' : trim($_REQUEST['payment']);
        $filter['is_paid'] = isset($_REQUEST['is_paid']) ? intval($_REQUEST['is_paid']) : -1;
        $filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'add_time' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
        $filter['start_date'] = empty($_REQUEST['start_date']) ? '' : local_strtotime($_REQUEST['start_date']);
        $filter['end_date'] = empty($_REQUEST['end_date']) ? '' : (local_strtotime($_REQUEST['end_date']) + 86400);

        $where = " WHERE 1 ";
        if ($filter['user_id'] > 0)
        {
            $where .= " AND ua.user_id = '$filter[user_id]' ";
        }
        if ($filter['process_type'] != -1)
        {
            $where .= " AND ua.process_type = '$filter[process_type]' ";
        }
        else
        {
            $where .= " AND ua.process_type " . db_create_in(array(SURPLUS_SAVE, SURPLUS_RETURN));
        }
        if ($filter['payment'])
        {
            $where .= " AND ua.payment = '$filter[payment]' ";
        }
        if ($filter['is_paid'] != -1)
        {
            $where .= " AND ua.is_paid = '$filter[is_paid]' ";
        }

        if ($filter['keywords'])
        {
            $where .= " AND u.user_name LIKE '%" . mysql_like_quote($filter['keywords']) . "%'";
            $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('user_account'). " AS ua, ".
                   $GLOBALS['ecs']->table('users') . " AS u " . $where;
        }
        /*　時間過濾　*/
        if (!empty($filter['start_date']) && !empty($filter['end_date']))
        {
            $where .= "AND paid_time >= " . $filter['start_date']. " AND paid_time < '" . $filter['end_date'] . "'";
        }

        $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('user_account'). " AS ua, ".
                   $GLOBALS['ecs']->table('users') . " AS u " . $where;
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分頁大小 */
        $filter = page_and_size($filter);

        /* 查詢數據 */
        $sql  = 'SELECT ua.*, u.user_name FROM ' .
            $GLOBALS['ecs']->table('user_account'). ' AS ua LEFT JOIN ' .
            $GLOBALS['ecs']->table('users'). ' AS u ON ua.user_id = u.user_id'.
            $where . "ORDER by " . $filter['sort_by'] . " " .$filter['sort_order']. " LIMIT ".$filter['start'].", ".$filter['page_size'];

        $filter['keywords'] = stripslashes($filter['keywords']);
        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }

    $list = $GLOBALS['db']->getAll($sql);
    foreach ($list AS $key => $value)
    {
        $list[$key]['surplus_amount']       = price_format(abs($value['amount']), false);
        $list[$key]['add_date']             = local_date($GLOBALS['_CFG']['time_format'], $value['add_time']);
        $list[$key]['process_type_name']    = $GLOBALS['_LANG']['surplus_type_' . $value['process_type']];
     }
    $arr = array('list' => $list, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}

?>