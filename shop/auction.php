<?php

/**
 * ECSHOP 拍賣前台文件
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: auction.php 17063 2010-03-25 06:35:46Z liuhui $
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/*------------------------------------------------------ */
//-- act 操作項的初始化
/*------------------------------------------------------ */
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'list';
}

/*------------------------------------------------------ */
//-- 拍賣活動列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    /* 取得拍賣活動總數 */
    $count = auction_count();
    if ($count > 0)
    {
        /* 取得每頁記錄數 */
        $size = isset($_CFG['page_size']) && intval($_CFG['page_size']) > 0 ? intval($_CFG['page_size']) : 10;

        /* 計算總頁數 */
        $page_count = ceil($count / $size);

        /* 取得當前頁 */
        $page = isset($_REQUEST['page']) && intval($_REQUEST['page']) > 0 ? intval($_REQUEST['page']) : 1;
        $page = $page > $page_count ? $page_count : $page;

        /* 緩存id：語言 - 每頁記錄數 - 當前頁 */
        $cache_id = $_CFG['lang'] . '-' . $size . '-' . $page;
        $cache_id = sprintf('%X', crc32($cache_id));
    }
    else
    {
        /* 緩存id：語言 */
        $cache_id = $_CFG['lang'];
        $cache_id = sprintf('%X', crc32($cache_id));
    }

    /* 如果沒有緩存，生成緩存 */
    if (!$smarty->is_cached('auction_list.dwt', $cache_id))
    {
        if ($count > 0)
        {
            /* 取得當前頁的拍賣活動 */
            $auction_list = auction_list($size, $page);
            $smarty->assign('auction_list',  $auction_list);

            /* 設置分頁鏈接 */
            $pager = get_pager('auction.php', array('act' => 'list'), $count, $page, $size);
            $smarty->assign('pager', $pager);
        }

        /* 模板賦值 */
        $smarty->assign('cfg', $_CFG);
        assign_template();
        $position = assign_ur_here();
        $smarty->assign('page_title', $position['title']);    // 頁面標題
        $smarty->assign('ur_here',    $position['ur_here']);  // 當前位置
        $smarty->assign('categories', get_categories_tree()); // 分類樹
        $smarty->assign('helps',      get_shop_help());       // 網店幫助
        $smarty->assign('top_goods',  get_top10());           // 銷售排行
        $smarty->assign('promotion_info', get_promotion_info());
        $smarty->assign('feed_url',         ($_CFG['rewrite'] == 1) ? "feed-typeauction.xml" : 'feed.php?type=auction'); // RSS URL

        assign_dynamic('auction_list');
    }

    /* 顯示模板 */
    $smarty->display('auction_list.dwt', $cache_id);
}

/*------------------------------------------------------ */
//-- 拍賣商品 --> 商品詳情
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'view')
{
    /* 取得參數：拍賣活動id */
    $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
    if ($id <= 0)
    {
        ecs_header("Location: ./\n");
        exit;
    }

    /* 取得拍賣活動信息 */
    $auction = auction_info($id);
    if (empty($auction))
    {
        ecs_header("Location: ./\n");
        exit;
    }

    /* 緩存id：語言，拍賣活動id，狀態，如果是進行中，還要最後出價的時間（如果有的話） */
    $cache_id = $_CFG['lang'] . '-' . $id . '-' . $auction['status_no'];
    if ($auction['status_no'] == UNDER_WAY)
    {
        if (isset($auction['last_bid']))
        {
            $cache_id = $cache_id . '-' . $auction['last_bid']['bid_time'];
        }
    }
    elseif ($auction['status_no'] == FINISHED && $auction['last_bid']['bid_user'] == $_SESSION['user_id']
        && $auction['order_count'] == 0)
    {
        $auction['is_winner'] = 1;
        $cache_id = $cache_id . '-' . $auction['last_bid']['bid_time'] . '-1';
    }

    $cache_id = sprintf('%X', crc32($cache_id));

    /* 如果沒有緩存，生成緩存 */
    if (!$smarty->is_cached('auction.dwt', $cache_id))
    {
        //取貨品信息
        if ($auction['product_id'] > 0)
        {
            $goods_specifications = get_specifications_list($auction['goods_id']);

            $good_products = get_good_products($auction['goods_id'], 'AND product_id = ' . $auction['product_id']);

            $_good_products = explode('|', $good_products[0]['goods_attr']);
            $products_info = '';
            foreach ($_good_products as $value)
            {
                $products_info .= ' ' . $goods_specifications[$value]['attr_name'] . '：' . $goods_specifications[$value]['attr_value'];
            }
            $smarty->assign('products_info',     $products_info);
            unset($goods_specifications, $good_products, $_good_products,  $products_info);
        }

        $auction['gmt_end_time'] = local_strtotime($auction['end_time']);
        $smarty->assign('auction', $auction);

        /* 取得拍賣商品信息 */
        $goods_id = $auction['goods_id'];
        $goods = goods_info($goods_id);
        if (empty($goods))
        {
            ecs_header("Location: ./\n");
            exit;
        }
        $goods['url'] = build_uri('goods', array('gid' => $goods_id), $goods['goods_name']);
        $smarty->assign('auction_goods', $goods);

        /* 出價記錄 */
        $smarty->assign('auction_log', auction_log($id));

        //模板賦值
        $smarty->assign('cfg', $_CFG);
        assign_template();

        $position = assign_ur_here(0, $goods['goods_name']);
        $smarty->assign('page_title', $position['title']);    // 頁面標題
        $smarty->assign('ur_here',    $position['ur_here']);  // 當前位置

        $smarty->assign('categories', get_categories_tree()); // 分類樹
        $smarty->assign('helps',      get_shop_help());       // 網店幫助
        $smarty->assign('top_goods',  get_top10());           // 銷售排行
        $smarty->assign('promotion_info', get_promotion_info());

        assign_dynamic('auction');
    }

    //更新商品點擊次數
    $sql = 'UPDATE ' . $ecs->table('goods') . ' SET click_count = click_count + 1 '.
           "WHERE goods_id = '" . $auction['goods_id'] . "'";
    $db->query($sql);

    $smarty->assign('now_time',  gmtime());           // 當前系統時間
    $smarty->display('auction.dwt', $cache_id);
}

/*------------------------------------------------------ */
//-- 拍賣商品 --> 出價
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'bid')
{
    include_once(ROOT_PATH . 'includes/lib_order.php');

    /* 取得參數：拍賣活動id */
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    if ($id <= 0)
    {
        ecs_header("Location: ./\n");
        exit;
    }

    /* 取得拍賣活動信息 */
    $auction = auction_info($id);
    if (empty($auction))
    {
        ecs_header("Location: ./\n");
        exit;
    }

    /* 活動是否正在進行 */
    if ($auction['status_no'] != UNDER_WAY)
    {
        show_message($_LANG['au_not_under_way'], '', '', 'error');
    }

    /* 是否登錄 */
    $user_id = $_SESSION['user_id'];
    if ($user_id <= 0)
    {
        show_message($_LANG['au_bid_after_login']);
    }
    $user = user_info($user_id);

    /* 取得出價 */
    $bid_price = isset($_POST['price']) ? round(floatval($_POST['price']), 2) : 0;
    if ($bid_price <= 0)
    {
        show_message($_LANG['au_bid_price_error'], '', '', 'error');
    }

    /* 如果有一口價且出價大於等於一口價，則按一口價算 */
    $is_ok = false; // 出價是否ok
    if ($auction['end_price'] > 0)
    {
        if ($bid_price >= $auction['end_price'])
        {
            $bid_price = $auction['end_price'];
            $is_ok = true;
        }
    }

    /* 出價是否有效：區分第一次和非第一次 */
    if (!$is_ok)
    {
        if ($auction['bid_user_count'] == 0)
        {
            /* 第一次要大於等於起拍價 */
            $min_price = $auction['start_price'];
        }
        else
        {
            /* 非第一次出價要大於等於最高價加上加價幅度，但不能超過一口價 */
            $min_price = $auction['last_bid']['bid_price'] + $auction['amplitude'];
            if ($auction['end_price'] > 0)
            {
                $min_price = min($min_price, $auction['end_price']);
            }
        }

        if ($bid_price < $min_price)
        {
            show_message(sprintf($_LANG['au_your_lowest_price'], price_format($min_price, false)), '', '', 'error');
        }
    }

    /* 檢查聯繫兩次拍賣人是否相同 */
    if ($auction['last_bid']['bid_user'] == $user_id && $bid_price != $auction['end_price'])
    {
        show_message($_LANG['au_bid_repeat_user'], '', '', 'error');
    }

    /* 是否需要保證金 */
    if ($auction['deposit'] > 0)
    {
        /* 可用資金夠嗎 */
        if ($user['user_money'] < $auction['deposit'])
        {
            show_message($_LANG['au_user_money_short'], '', '', 'error');
        }

        /* 如果不是第一個出價，解凍上一個用戶的保證金 */
        if ($auction['bid_user_count'] > 0)
        {
            log_account_change($auction['last_bid']['bid_user'], $auction['deposit'], (-1) * $auction['deposit'],
                0, 0, sprintf($_LANG['au_unfreeze_deposit'], $auction['act_name']));
        }

        /* 凍結當前用戶的保證金 */
        log_account_change($user_id, (-1) * $auction['deposit'], $auction['deposit'],
            0, 0, sprintf($_LANG['au_freeze_deposit'], $auction['act_name']));
    }

    /* 插入出價記錄 */
    $auction_log = array(
        'act_id'    => $id,
        'bid_user'  => $user_id,
        'bid_price' => $bid_price,
        'bid_time'  => gmtime()
    );
    $db->autoExecute($ecs->table('auction_log'), $auction_log, 'INSERT');

    /* 出價是否等於一口價 */
    if ($bid_price == $auction['end_price'])
    {
        /* 結束拍賣活動 */
        $sql = "UPDATE " . $ecs->table('goods_activity') . " SET is_finished = 1 WHERE act_id = '$id' LIMIT 1";
        $db->query($sql);
    }

    /* 跳轉到活動詳情頁 */
    ecs_header("Location: auction.php?act=view&id=$id\n");
    exit;
}

/*------------------------------------------------------ */
//-- 拍賣商品 --> 購買
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'buy')
{
    /* 查詢：取得參數：拍賣活動id */
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    if ($id <= 0)
    {
        ecs_header("Location: ./\n");
        exit;
    }

    /* 查詢：取得拍賣活動信息 */
    $auction = auction_info($id);
    if (empty($auction))
    {
        ecs_header("Location: ./\n");
        exit;
    }

    /* 查詢：活動是否已結束 */
    if ($auction['status_no'] != FINISHED)
    {
        show_message($_LANG['au_not_finished'], '', '', 'error');
    }

    /* 查詢：有人出價嗎 */
    if ($auction['bid_user_count'] <= 0)
    {
        show_message($_LANG['au_no_bid'], '', '', 'error');
    }

    /* 查詢：是否已經有訂單 */
    if ($auction['order_count'] > 0)
    {
        show_message($_LANG['au_order_placed']);
    }

    /* 查詢：是否登錄 */
    $user_id = $_SESSION['user_id'];
    if ($user_id <= 0)
    {
        show_message($_LANG['au_buy_after_login']);
    }

    /* 查詢：最後出價的是該用戶嗎 */
    if ($auction['last_bid']['bid_user'] != $user_id)
    {
        show_message($_LANG['au_final_bid_not_you'], '', '', 'error');
    }

    /* 查詢：取得商品信息 */
    $goods = goods_info($auction['goods_id']);

    /* 查詢：處理規格屬性 */
    $goods_attr = '';
    $goods_attr_id = '';
    if ($auction['product_id'] > 0)
    {
        $product_info = get_good_products($auction['goods_id'], 'AND product_id = ' . $auction['product_id']);

        $goods_attr_id = str_replace('|', ',', $product_info[0]['goods_attr']);

        $attr_list = array();
        $sql = "SELECT a.attr_name, g.attr_value " .
                "FROM " . $ecs->table('goods_attr') . " AS g, " .
                    $ecs->table('attribute') . " AS a " .
                "WHERE g.attr_id = a.attr_id " .
                "AND g.goods_attr_id " . db_create_in($goods_attr_id);
        $res = $db->query($sql);
        while ($row = $db->fetchRow($res))
        {
            $attr_list[] = $row['attr_name'] . ': ' . $row['attr_value'];
        }
        $goods_attr = join(chr(13) . chr(10), $attr_list);
    }
    else
    {
        $auction['product_id'] = 0;
    }

    /* 清空購物車中所有拍賣商品 */
    include_once(ROOT_PATH . 'includes/lib_order.php');
    clear_cart(CART_AUCTION_GOODS);

    /* 加入購物車 */
    $cart = array(
        'user_id'        => $user_id,
        'session_id'     => SESS_ID,
        'goods_id'       => $auction['goods_id'],
        'goods_sn'       => addslashes($goods['goods_sn']),
        'goods_name'     => addslashes($goods['goods_name']),
        'market_price'   => $goods['market_price'],
        'goods_price'    => $auction['last_bid']['bid_price'],
        'goods_number'   => 1,
        'goods_attr'     => $goods_attr,
        'goods_attr_id'  => $goods_attr_id,
        'is_real'        => $goods['is_real'],
        'extension_code' => addslashes($goods['extension_code']),
        'parent_id'      => 0,
        'rec_type'       => CART_AUCTION_GOODS,
        'is_gift'        => 0
    );
    $db->autoExecute($ecs->table('cart'), $cart, 'INSERT');

    /* 記錄購物流程類型：團購 */
    $_SESSION['flow_type'] = CART_AUCTION_GOODS;
    $_SESSION['extension_code'] = 'auction';
    $_SESSION['extension_id'] = $id;

    /* 進入收貨人頁面 */
    ecs_header("Location: ./flow.php?step=consignee\n");
    exit;
}

/**
 * 取得拍賣活動數量
 * @return  int
 */
function auction_count()
{
    $now = gmtime();
    $sql = "SELECT COUNT(*) " .
            "FROM " . $GLOBALS['ecs']->table('goods_activity') .
            "WHERE act_type = '" . GAT_AUCTION . "' " .
            "AND start_time <= '$now' AND end_time >= '$now' AND is_finished < 2";

    return $GLOBALS['db']->getOne($sql);
}

/**
 * 取得某頁的拍賣活動
 * @param   int     $size   每頁記錄數
 * @param   int     $page   當前頁
 * @return  array
 */
function auction_list($size, $page)
{
    $auction_list = array();
    $auction_list['finished'] = $auction_list['finished'] = array();

    $now = gmtime();
    $sql = "SELECT a.*, IFNULL(g.goods_thumb, '') AS goods_thumb " .
            "FROM " . $GLOBALS['ecs']->table('goods_activity') . " AS a " .
                "LEFT JOIN " . $GLOBALS['ecs']->table('goods') . " AS g ON a.goods_id = g.goods_id " .
            "WHERE a.act_type = '" . GAT_AUCTION . "' " .
            "AND a.start_time <= '$now' AND a.end_time >= '$now' AND a.is_finished < 2 ORDER BY a.act_id DESC";
    $res = $GLOBALS['db']->selectLimit($sql, $size, ($page - 1) * $size);
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $ext_info = unserialize($row['ext_info']);
        $auction = array_merge($row, $ext_info);
        $auction['status_no'] = auction_status($auction);

        $auction['start_time'] = local_date($GLOBALS['_CFG']['time_format'], $auction['start_time']);
        $auction['end_time']   = local_date($GLOBALS['_CFG']['time_format'], $auction['end_time']);
        $auction['formated_start_price'] = price_format($auction['start_price']);
        $auction['formated_end_price'] = price_format($auction['end_price']);
        $auction['formated_deposit'] = price_format($auction['deposit']);
        $auction['goods_thumb'] = get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $auction['url'] = build_uri('auction', array('auid'=>$auction['act_id']));

        if($auction['status_no'] < 2)
        {
            $auction_list['under_way'][] = $auction;
        }
        else
        {
            $auction_list['finished'][] = $auction;
        }
    }

    $auction_list = @array_merge($auction_list['under_way'], $auction_list['finished']);

    return $auction_list;
}

?>