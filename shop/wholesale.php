<?php

/**
 * ECSHOP 批發前台文件
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發布。
 * ============================================================================
 * @author:     scott ye <scott.yell@gmail.com>
 * @version:    v2.x
 * ---------------------------------------------
 * $Author: liuhui $
 * $Id: wholesale.php 17063 2010-03-25 06:35:46Z liuhui $
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/* 如果沒登錄，提示登錄 */
if ($_SESSION['user_rank'] <= 0)
{
    show_message($_LANG['ws_user_rank'], $_LANG['ws_return_home'], 'index.php');
}

/*------------------------------------------------------ */
//-- act 操作項的初始化
/*------------------------------------------------------ */
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'list';
}

/*------------------------------------------------------ */
//-- 批發活動列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    $search_category = empty($_REQUEST['search_category']) ? 0 : intval($_REQUEST['search_category']);
    $search_keywords = isset($_REQUEST['search_keywords']) ? trim($_REQUEST['search_keywords']) : '';
    $param = array(); // 翻頁鏈接所帶參數列表

    /* 查詢條件：當前用戶的會員等級（搜索關鍵字） */
    $where = " WHERE g.goods_id = w.goods_id
               AND w.enabled = 1
               AND CONCAT(',', w.rank_ids, ',') LIKE '" . '%,' . $_SESSION['user_rank'] . ',%' . "' ";

    /* 搜索 */
    /* 搜索類別 */
    if ($search_category)
    {
        $where .= " AND g.cat_id = '$search_category' ";
        $param['search_category'] = $search_category;
        $smarty->assign('search_category', $search_category);
    }
    /* 搜索商品名稱和關鍵字 */
    if ($search_keywords)
    {
        $where .= " AND (g.keywords LIKE '%$search_keywords%'
                    OR g.goods_name LIKE '%$search_keywords%') ";
        $param['search_keywords'] = $search_keywords;
        $smarty->assign('search_keywords', $search_keywords);
    }

    /* 取得批發商品總數 */
    $sql = "SELECT COUNT(*) FROM " . $ecs->table('wholesale') . " AS w, " . $ecs->table('goods') . " AS g " . $where;
    $count = $db->getOne($sql);

    if ($count > 0)
    {
        $default_display_type = $_CFG['show_order_type'] == '0' ? 'list' : 'text';
        $display  = (isset($_REQUEST['display']) && in_array(trim(strtolower($_REQUEST['display'])), array('list', 'text'))) ? trim($_REQUEST['display']) : (isset($_COOKIE['ECS']['display']) ? $_COOKIE['ECS']['display'] : $default_display_type);
        $display  = in_array($display, array('list', 'text')) ? $display : 'text';
        setcookie('ECS[display]', $display, gmtime() + 86400 * 7);

        /* 取得每頁記錄數 */
        $size = isset($_CFG['page_size']) && intval($_CFG['page_size']) > 0 ? intval($_CFG['page_size']) : 10;

        /* 計算總頁數 */
        $page_count = ceil($count / $size);

        /* 取得當前頁 */
        $page = isset($_REQUEST['page']) && intval($_REQUEST['page']) > 0 ? intval($_REQUEST['page']) : 1;
        $page = $page > $page_count ? $page_count : $page;

        /* 取得當前頁的批發商品 */
        $wholesale_list = wholesale_list($size, $page, $where);
        $smarty->assign('wholesale_list', $wholesale_list);

        $param['act'] = 'list';
        $pager = get_pager('wholesale.php', array_reverse ($param, TRUE), $count, $page, $size);
        $pager['display'] = $display;
        $smarty->assign('pager', $pager);

        /* 批發商品購物車 */
        $smarty->assign('cart_goods', isset($_SESSION['wholesale_goods']) ? $_SESSION['wholesale_goods'] : array());
    }

    /* 模板賦值 */
    assign_template();
    $position = assign_ur_here();
    $smarty->assign('page_title', $position['title']);    // 頁面標題
    $smarty->assign('ur_here',    $position['ur_here']);  // 當前位置
    $smarty->assign('categories', get_categories_tree()); // 分類樹
    $smarty->assign('helps',      get_shop_help());       // 網店幫助
    $smarty->assign('top_goods',  get_top10());           // 銷售排行

    assign_dynamic('wholesale');

    /* 顯示模板 */
    $smarty->display('wholesale_list.dwt');
}

/*------------------------------------------------------ */
//-- 下載價格單
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'price_list')
{
    $data = $_LANG['goods_name'] . "\t" . $_LANG['goods_attr'] . "\t" . $_LANG['number'] . "\t" . $_LANG['ws_price'] . "\t\n";
    $sql = "SELECT * FROM " . $ecs->table('wholesale') .
            "WHERE enabled = 1 AND CONCAT(',', rank_ids, ',') LIKE '" . '%,' . $_SESSION['user_rank'] . ',%' . "'";
    $res = $db->query($sql);
    while ($row = $db->fetchRow($res))
    {
        $price_list = unserialize($row['prices']);
        foreach ($price_list as $attr_price)
        {
            if ($attr_price['attr'])
            {
                $sql = "SELECT attr_value FROM " . $ecs->table('goods_attr') .
                        " WHERE goods_attr_id " . db_create_in($attr_price['attr']);
                $goods_attr = join(',', $db->getCol($sql));
            }
            else
            {
                $goods_attr = '';
            }
            foreach ($attr_price['qp_list'] as $qp)
            {
                $data .= $row['goods_name'] . "\t" . $goods_attr . "\t" . $qp['quantity'] . "\t" . $qp['price'] . "\t\n";
            }
        }
    }

    header("Content-type: application/vnd.ms-excel; charset=utf-8");
    header("Content-Disposition: attachment; filename=price_list.xls");
    if (EC_CHARSET == 'utf-8')
    {
        echo ecs_iconv('UTF8', 'GB2312', $data);
    }
    else
    {
        echo $data;
    }
}

/*------------------------------------------------------ */
//-- 加入購物車
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add_to_cart')
{
    /* 取得參數 */
    $act_id         = intval($_POST['act_id']);
    $goods_number   = $_POST['goods_number'][$act_id];
    $attr_id        = isset($_POST['attr_id']) ? $_POST['attr_id'] : array();
    if(isset($attr_id[$act_id]))
    {
        $goods_attr     = $attr_id[$act_id];
    }

    /* 用戶提交必須全部通過檢查，才能視為完成操作 */

    /* 檢查數量 */
    if (empty($goods_number) || (is_array($goods_number) && array_sum($goods_number) <= 0))
    {
        show_message($_LANG['ws_invalid_goods_number']);
    }

    /* 確定購買商品列表 */
    $goods_list = array();
    if (is_array($goods_number))
    {
        foreach ($goods_number as $key => $value)
        {
            if (!$value)
            {
                unset($goods_number[$key], $goods_attr[$key]);
                continue;
            }

            $goods_list[] = array('number' => $goods_number[$key], 'goods_attr' => $goods_attr[$key]);
        }
    }
    else
    {
        $goods_list[0] = array('number' => $goods_number, 'goods_attr' => '');
    }

    /* 取批發相關數據 */
    $wholesale = wholesale_info($act_id);

    /* 檢查session中該商品，該屬性是否存在 */
    if (isset($_SESSION['wholesale_goods']))
    {
        foreach ($_SESSION['wholesale_goods'] as $goods)
        {
            if ($goods['goods_id'] == $wholesale['goods_id'])
            {
                if (empty($goods_attr))
                {
                    show_message($_LANG['ws_goods_attr_exists']);
                }
                elseif (in_array($goods['goods_attr_id'], $goods_attr))
                {
                    show_message($_LANG['ws_goods_attr_exists']);
                }
            }
        }
    }

    /* 獲取購買商品的批發方案的價格階梯 （一個方案多個屬性組合、一個屬性組合、一個屬性、無屬性） */
    $attr_matching = false;
    foreach ($wholesale['price_list'] as $attr_price)
    {
        // 沒有屬性
        if (empty($attr_price['attr']))
        {
            $attr_matching = true;
            $goods_list[0]['qp_list'] = $attr_price['qp_list'];
            break;
        }
        // 有屬性
        elseif (($key = is_attr_matching($goods_list, $attr_price['attr'])) !== false)
        {
            $attr_matching = true;
            $goods_list[$key]['qp_list'] = $attr_price['qp_list'];
        }
    }
    if (!$attr_matching)
    {
        show_message($_LANG['ws_attr_not_matching']);
    }

    /* 檢查數量是否達到最低要求 */
    foreach ($goods_list as $goods_key => $goods)
    {
        if ($goods['number'] < $goods['qp_list'][0]['quantity'])
        {
            show_message($_LANG['ws_goods_number_not_enough']);
        }
        else
        {
            $goods_price = 0;
            foreach ($goods['qp_list'] as $qp)
            {
                if ($goods['number'] >= $qp['quantity'])
                {
                    $goods_list[$goods_key]['goods_price'] = $qp['price'];
                }
                else
                {
                    break;
                }
            }
        }
    }

    /* 寫入session */
    foreach ($goods_list as $goods_key => $goods)
    {
        // 屬性名稱
        $goods_attr_name = '';
        if (!empty($goods['goods_attr']))
        {
            foreach ($goods['goods_attr'] as $attr)
            {
                $goods_attr_name .= $attr['attr_name'] . '：' . $attr['attr_val'] . '&nbsp;';
            }
        }

        // 總價
        $total = $goods['number'] * $goods['goods_price'];

        $_SESSION['wholesale_goods'][] = array(
            'goods_id'      => $wholesale['goods_id'],
            'goods_name'    => $wholesale['goods_name'],
            'goods_attr_id' => $goods['goods_attr'],
            'goods_attr'    => $goods_attr_name,
            'goods_number'  => $goods['number'],
            'goods_price'   => $goods['goods_price'],
            'subtotal'      => $total,
            'formated_goods_price'  => price_format($goods['goods_price'], false),
            'formated_subtotal'     => price_format($total, false),
            'goods_url'     => build_uri('goods', array('gid' => $wholesale['goods_id']), $wholesale['goods_name']),
        );
    }

    unset($goods_attr, $attr_id, $goods_list, $wholesale, $goods_attr_name);

    /* 刷新頁面 */
    ecs_header("Location: ./wholesale.php\n");
    exit;
}

/*------------------------------------------------------ */
//-- 從購物車刪除
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'drop_goods')
{
    $key = intval($_REQUEST['key']);
    if (isset($_SESSION['wholesale_goods'][$key]))
    {
        unset($_SESSION['wholesale_goods'][$key]);
    }

    /* 刷新頁面 */
    ecs_header("Location: ./wholesale.php\n");
    exit;
}

/*------------------------------------------------------ */
//-- 提交訂單
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'submit_order')
{
    include_once(ROOT_PATH . 'includes/lib_order.php');

    /* 檢查購物車中是否有商品 */
    if (count($_SESSION['wholesale_goods']) == 0)
    {
        show_message($_LANG['no_goods_in_cart']);
    }

    /* 檢查備註信息 */
    if (empty($_POST['remark']))
    {
        show_message($_LANG['ws_remark']);
    }

    /* 計算商品總額 */
    $goods_amount = 0;
    foreach ($_SESSION['wholesale_goods'] as $goods)
    {
        $goods_amount += $goods['subtotal'];
    }

    $order = array(
        'postscript'      => htmlspecialchars($_POST['remark']),
        'user_id'         => $_SESSION['user_id'],
        'add_time'        => gmtime(),
        'order_status'    => OS_UNCONFIRMED,
        'shipping_status' => SS_UNSHIPPED,
        'pay_status'      => PS_UNPAYED,
        'goods_amount'    => $goods_amount,
        'order_amount'    => $goods_amount,
    );

    /* 插入訂單表 */
    $error_no = 0;
    do
    {
        $order['order_sn'] = get_order_sn(); //獲取新訂單號
        $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('order_info'), $order, 'INSERT');

        $error_no = $GLOBALS['db']->errno();

        if ($error_no > 0 && $error_no != 1062)
        {
            die($GLOBALS['db']->errorMsg());
        }
    }
    while ($error_no == 1062); //如果是訂單號重複則重新提交數據

    $new_order_id = $db->insert_id();
    $order['order_id'] = $new_order_id;

    /* 插入訂單商品 */
    foreach ($_SESSION['wholesale_goods'] as $goods)
    {
        //如果存在貨品
        $product_id = 0;
        if (!empty($goods['goods_attr_id']))
        {
            $goods_attr_id = array();
            foreach ($goods['goods_attr_id'] as $value)
            {
                $goods_attr_id[$value['attr_id']] = $value['attr_val_id'];
            }

            ksort($goods_attr_id);
            $goods_attr = implode('|', $goods_attr_id);

            $sql = "SELECT product_id FROM " . $ecs->table('products') . " WHERE goods_attr = '$goods_attr' AND goods_id = '" . $goods['goods_id'] . "'";
            $product_id = $db->getOne($sql);
        }

        $sql = "INSERT INTO " . $ecs->table('order_goods') . "( " .
                    "order_id, goods_id, goods_name, goods_sn, product_id, goods_number, market_price, ".
                    "goods_price, goods_attr, is_real, extension_code, parent_id, is_gift) ".
                " SELECT '$new_order_id', goods_id, goods_name, goods_sn, '$product_id','$goods[goods_number]', market_price, ".
                    "'$goods[goods_price]', '$goods[goods_attr]', is_real, extension_code, 0, 0 ".
                " FROM " .$ecs->table('goods') .
                " WHERE goods_id = '$goods[goods_id]'";
        $db->query($sql);
    }

    /* 給商家發郵件 */
    if ($_CFG['service_email'] != '')
    {
        $tpl = get_mail_template('remind_of_new_order');
        $smarty->assign('order', $order);
        $smarty->assign('shop_name', $_CFG['shop_name']);
        $smarty->assign('send_date', date($_CFG['time_format']));
        $content = $smarty->fetch('str:' . $tpl['template_content']);
        send_mail($_CFG['shop_name'], $_CFG['service_email'], $tpl['template_subject'], $content, $tpl['is_html']);
    }

    /* 如果需要，發短信 */
    if ($_CFG['sms_order_placed'] == '1' && $_CFG['sms_shop_mobile'] != '')
    {
        include_once('includes/cls_sms.php');
        $sms = new sms();
        $msg = $_LANG['order_placed_sms'];
        $sms->send($_CFG['sms_shop_mobile'], sprintf($msg, $order['consignee'], $order['tel']), 0);
    }

    /* 清空購物車 */
    unset($_SESSION['wholesale_goods']);

    /* 提示 */
    show_message(sprintf($_LANG['ws_order_submitted'], $order['order_sn']), $_LANG['ws_return_home'], 'index.php');
}

/**
 * 取得某頁的批發商品
 * @param   int     $size   每頁記錄數
 * @param   int     $page   當前頁
 * @param   string  $where  查詢條件
 * @return  array
 */
function wholesale_list($size, $page, $where)
{
    $list = array();
    $sql = "SELECT w.*, g.goods_thumb, g.goods_name as goods_name " .
            "FROM " . $GLOBALS['ecs']->table('wholesale') . " AS w, " .
                      $GLOBALS['ecs']->table('goods') . " AS g " . $where .
            " AND w.goods_id = g.goods_id ";
    $res = $GLOBALS['db']->selectLimit($sql, $size, ($page - 1) * $size);
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        if (empty($row['goods_thumb']))
        {
            $row['goods_thumb'] = $GLOBALS['_CFG']['no_picture'];
        }
        $row['goods_url'] = build_uri('goods', array('gid'=>$row['goods_id']), $row['goods_name']);

        $properties = get_goods_properties($row['goods_id']);
        $row['goods_attr'] = $properties['pro'];

        $price_ladder = get_price_ladder($row['goods_id']);
        $row['price_ladder'] = $price_ladder;

        $list[] = $row;
    }

    return $list;
}

/**
 * 商品價格階梯
 * @param   int     $goods_id     商品ID
 * @return  array
 */
function get_price_ladder($goods_id)
{
    /* 顯示商品規格 */
    $goods_attr_list = array_values(get_goods_attr($goods_id));
    $sql = "SELECT prices FROM " . $GLOBALS['ecs']->table('wholesale') .
            "WHERE goods_id = " . $goods_id;
    $row = $GLOBALS['db']->getRow($sql);

    $arr = array();
    $_arr = unserialize($row['prices']);
    if (is_array($_arr))
    {
        foreach(unserialize($row['prices']) as $key => $val)
        {
            // 顯示屬性
            if (!empty($val['attr']))
            {
                foreach ($val['attr'] as $attr_key => $attr_val)
                {
                    // 獲取當前屬性 $attr_key 的信息
                    $goods_attr = array();
                    foreach ($goods_attr_list as $goods_attr_val)
                    {
                        if ($goods_attr_val['attr_id'] == $attr_key)
                        {
                            $goods_attr = $goods_attr_val;
                            break;
                        }
                    }

                    // 重寫商品規格的價格階梯信息
                    if (!empty($goods_attr))
                    {
                        $arr[$key]['attr'][] = array(
                            'attr_id'       => $goods_attr['attr_id'],
                            'attr_name'     => $goods_attr['attr_name'],
                            'attr_val'      => (isset($goods_attr['goods_attr_list'][$attr_val]) ? $goods_attr['goods_attr_list'][$attr_val] : ''),
                            'attr_val_id'   => $attr_val
                        );
                    }
                }
            }

            // 顯示數量與價格
            foreach($val['qp_list'] as $index => $qp)
            {
                $arr[$key]['qp_list'][$qp['quantity']] = price_format($qp['price']);
            }
        }
    }

    return $arr;
}

/**
 * 商品屬性是否匹配
 * @param   array   $goods_list     用戶選擇的商品
 * @param   array   $reference      參照的商品屬性
 * @return  bool
 */
function is_attr_matching(&$goods_list, $reference)
{
    foreach ($goods_list as $key => $goods)
    {
        // 需要相同的元素個數
        if (count($goods['goods_attr']) != count($reference))
        {
            break;
        }

        // 判斷用戶提交與批發屬性是否相同
        $is_check = true;
        if (is_array($goods['goods_attr']))
        {
            foreach ($goods['goods_attr'] as $attr)
            {
                if (!(array_key_exists($attr['attr_id'], $reference) && $attr['attr_val_id'] == $reference[$attr['attr_id']]))
                {
                    $is_check = false;
                    break;
                }
            }
        }
        if ($is_check)
        {
            return $key;
            break;
        }
    }


//    foreach ($goods_attr as $attr_id => $goods_attr_id)
//    {
//        if (isset($reference[$attr_id]) && $reference[$attr_id] != 0 && $reference[$attr_id] != $goods_attr_id)
//        {
//            return false;
//        }
//    }

    return false;
}

///**
// * 購物車中的商品屬性與當前購買的商品屬性是否匹配
// * @param   array   $goods_attr     用戶選擇的商品屬性
// * @param   array   $reference      參照的商品屬性
// * @return  bool
// */
//function is_attr_same($goods_attr, $reference)
//{
//    /* 比較元素個數是否相同 */
//    if (count($goods_attr) == count($reference)) {
//    }
//
//    return true;
//}
?>
