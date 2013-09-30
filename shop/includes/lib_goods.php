<?php

/**
 * ECSHOP 商品相關函數庫
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: lib_goods.php 17113 2010-04-16 03:44:19Z liuhui $
*/

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

/**
 * 商品推薦usort用自定義排序行數
 */
function goods_sort($goods_a, $goods_b)
{
    if ($goods_a['sort_order'] == $goods_b['sort_order']) {
        return 0;
    }
    return ($goods_a['sort_order'] < $goods_b['sort_order']) ? -1 : 1;

}

/**
 * 獲得指定分類同級的所有分類以及該分類下的子分類
 *
 * @access  public
 * @param   integer     $cat_id     分類編號
 * @return  array
 */
function get_categories_tree($cat_id = 0)
{
    if ($cat_id > 0)
    {
        $sql = 'SELECT parent_id FROM ' . $GLOBALS['ecs']->table('category') . " WHERE cat_id = '$cat_id'";
        $parent_id = $GLOBALS['db']->getOne($sql);
    }
    else
    {
        $parent_id = 0;
    }

    /*
     判斷當前分類中全是是否是底級分類，
     如果是取出底級分類上級分類，
     如果不是取當前分類及其下的子分類
    */
    $sql = 'SELECT count(*) FROM ' . $GLOBALS['ecs']->table('category') . " WHERE parent_id = '$parent_id' AND is_show = 1 ";
    if ($GLOBALS['db']->getOne($sql) || $parent_id == 0)
    {
        /* 獲取當前分類及其子分類 */
        $sql = 'SELECT cat_id,cat_name ,parent_id,is_show ' .
                'FROM ' . $GLOBALS['ecs']->table('category') .
                "WHERE parent_id = '$parent_id' AND is_show = 1 ORDER BY sort_order ASC, cat_id ASC";

        $res = $GLOBALS['db']->getAll($sql);

        foreach ($res AS $row)
        {
            if ($row['is_show'])
            {
                $cat_arr[$row['cat_id']]['id']   = $row['cat_id'];
                $cat_arr[$row['cat_id']]['name'] = $row['cat_name'];
                $cat_arr[$row['cat_id']]['url']  = build_uri('category', array('cid' => $row['cat_id']), $row['cat_name']);

                if (isset($row['cat_id']) != NULL)
                {
                    $cat_arr[$row['cat_id']]['cat_id'] = get_child_tree($row['cat_id']);
                }
            }
        }
    }
    if(isset($cat_arr))
    {
        return $cat_arr;
    }
}

function get_child_tree($tree_id = 0)
{
    $three_arr = array();
    $sql = 'SELECT count(*) FROM ' . $GLOBALS['ecs']->table('category') . " WHERE parent_id = '$tree_id' AND is_show = 1 ";
    if ($GLOBALS['db']->getOne($sql) || $tree_id == 0)
    {
        $child_sql = 'SELECT cat_id, cat_name, parent_id, is_show ' .
                'FROM ' . $GLOBALS['ecs']->table('category') .
                "WHERE parent_id = '$tree_id' AND is_show = 1 ORDER BY sort_order ASC, cat_id ASC";
        $res = $GLOBALS['db']->getAll($child_sql);
        foreach ($res AS $row)
        {
            if ($row['is_show'])

               $three_arr[$row['cat_id']]['id']   = $row['cat_id'];
               $three_arr[$row['cat_id']]['name'] = $row['cat_name'];
               $three_arr[$row['cat_id']]['url']  = build_uri('category', array('cid' => $row['cat_id']), $row['cat_name']);

               if (isset($row['cat_id']) != NULL)
                   {
                       $three_arr[$row['cat_id']]['cat_id'] = get_child_tree($row['cat_id']);

            }
        }
    }
    return $three_arr;
}

/**
 * 調用當前分類的銷售排行榜
 *
 * @access  public
 * @param   string  $cats   查詢的分類
 * @return  array
 */
function get_top10($cats = '')
{
    $cats = get_children($cats);
    $where = !empty($cats) ? "AND ($cats OR " . get_extension_goods($cats) . ") " : '';

    /* 排行統計的時間 */
    switch ($GLOBALS['_CFG']['top10_time'])
    {
        case 1: // 一年
            $top10_time = "AND o.order_sn >= '" . date('Ymd', gmtime() - 365 * 86400) . "'";
        break;
        case 2: // 半年
            $top10_time = "AND o.order_sn >= '" . date('Ymd', gmtime() - 180 * 86400) . "'";
        break;
        case 3: // 三個月
            $top10_time = "AND o.order_sn >= '" . date('Ymd', gmtime() - 90 * 86400) . "'";
        break;
        case 4: // 一個月
            $top10_time = "AND o.order_sn >= '" . date('Ymd', gmtime() - 30 * 86400) . "'";
        break;
        default:
            $top10_time = '';
    }

    $sql = 'SELECT g.goods_id, g.goods_name, g.shop_price, g.goods_thumb, SUM(og.goods_number) as goods_number ' .
           'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g, ' .
                $GLOBALS['ecs']->table('order_info') . ' AS o, ' .
                $GLOBALS['ecs']->table('order_goods') . ' AS og ' .
           "WHERE g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 $where $top10_time " ;
    //判斷是否啟用庫存，庫存數量是否大於0
    if ($GLOBALS['_CFG']['use_storage'] == 1)
    {
        $sql .= " AND g.goods_number > 0 ";
    }
    $sql .= ' AND og.order_id = o.order_id AND og.goods_id = g.goods_id ' .
           "AND (o.order_status = '" . OS_CONFIRMED .  "' OR o.order_status = '" . OS_SPLITED . "') " .
           "AND (o.pay_status = '" . PS_PAYED . "' OR o.pay_status = '" . PS_PAYING . "') " .
           "AND (o.shipping_status = '" . SS_SHIPPED . "' OR o.shipping_status = '" . SS_RECEIVED . "') " .
           'GROUP BY g.goods_id ORDER BY goods_number DESC, g.goods_id DESC LIMIT ' . $GLOBALS['_CFG']['top_number'];
           
    $arr = $GLOBALS['db']->getAll($sql);

    for ($i = 0, $count = count($arr); $i < $count; $i++)
    {
        $arr[$i]['short_name'] = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
                                    sub_str($arr[$i]['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $arr[$i]['goods_name'];
        $arr[$i]['url']        = build_uri('goods', array('gid' => $arr[$i]['goods_id']), $arr[$i]['goods_name']);
        $arr[$i]['thumb'] = get_image_path($arr[$i]['goods_id'], $arr[$i]['goods_thumb'],true);
        $arr[$i]['price'] = price_format($arr[$i]['shop_price']);
    }

    return $arr;
}

/**
 * 獲得推薦商品
 *
 * @access  public
 * @param   string      $type       推薦類型，可以是 best, new, hot
 * @return  array
 */
function get_recommend_goods($type = '', $cats = '')
{
    if (!in_array($type, array('best', 'new', 'hot')))
    {
        return array();
    }

    //取不同推薦對應的商品
    static $type_goods = array();
    if (empty($type_goods[$type]))
    {
        //初始化數據
        $type_goods['best'] = array();
        $type_goods['new'] = array();
        $type_goods['hot'] = array();
        $data = read_static_cache('recommend_goods');
        if ($data === false)
        {
            $sql = 'SELECT g.goods_id, g.is_best, g.is_new, g.is_hot, g.is_promote, b.brand_name,g.sort_order ' .
               ' FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
               ' LEFT JOIN ' . $GLOBALS['ecs']->table('brand') . ' AS b ON b.brand_id = g.brand_id ' .
               ' WHERE g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 AND (g.is_best = 1 OR g.is_new =1 OR g.is_hot = 1)'.
               ' ORDER BY g.sort_order, g.last_update DESC';
            $goods_res = $GLOBALS['db']->getAll($sql);
            //定義推薦,最新，熱門，促銷商品
            $goods_data['best'] = array();
            $goods_data['new'] = array();
            $goods_data['hot'] = array();
            $goods_data['brand'] = array();
            if (!empty($goods_res))
            {
                foreach($goods_res as $data)
                {
                    if ($data['is_best'] == 1)
                    {
                        $goods_data['best'][] = array('goods_id' => $data['goods_id'], 'sort_order' => $data['sort_order']);
                    }
                    if ($data['is_new'] == 1)
                    {
                        $goods_data['new'][] = array('goods_id' => $data['goods_id'], 'sort_order' => $data['sort_order']);
                    }
                    if ($data['is_hot'] == 1)
                    {
                        $goods_data['hot'][] = array('goods_id' => $data['goods_id'], 'sort_order' => $data['sort_order']);
                    }
                    if ($data['brand_name'] != '')
                    {
                        $goods_data['brand'][$data['goods_id']] = $data['brand_name'];
                    }
                }
            }
            write_static_cache('recommend_goods', $goods_data);
        }
        else
        {
            $goods_data = $data;
        }

        $time = gmtime();
        $order_type = $GLOBALS['_CFG']['recommend_order'];

        //按推薦數量及排序取每一項推薦顯示的商品 order_type可以根據後台設定進行各種條件顯示
        static $type_array = array();
        $type2lib = array('best'=>'recommend_best', 'new'=>'recommend_new', 'hot'=>'recommend_hot');
        if (empty($type_array))
        {
            foreach($type2lib as $key => $data)
            {
                if (!empty($goods_data[$key]))
                {
                    $num = get_library_number($data);
                    $data_count = count($goods_data[$key]);
                    $num = $data_count > $num  ? $num : $data_count;
                    if ($order_type == 0)
                    {
                        //usort($goods_data[$key], 'goods_sort');
                        $rand_key = array_slice($goods_data[$key], 0, $num);
                        foreach($rand_key as $key_data)
                        {
                            $type_array[$key][] = $key_data['goods_id'];
                        }
                    }
                    else
                    {
                        $rand_key = array_rand($goods_data[$key], $num);
                        if ($num == 1)
                        {
                            $type_array[$key][] = $goods_data[$key][$rand_key]['goods_id'];
                        }
                        else
                        {
                            foreach($rand_key as $key_data)
                            {
                                $type_array[$key][] = $goods_data[$key][$key_data]['goods_id'];
                            }
                        }
                    }
                }
                else
                {
                    $type_array[$key] = array();
                }
            }
        }

        //取出所有符合條件的商品數據，並將結果存入對應的推薦類型數組中
        $sql = 'SELECT g.goods_id, g.goods_name, g.goods_name_style, g.market_price, g.shop_price AS org_price, g.promote_price, ' .
                "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, ".
                "promote_start_date, promote_end_date, g.goods_brief, g.goods_thumb, g.goods_img, RAND() AS rnd " .
                'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
                "LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp ".
                "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' ";
        $type_merge = array_merge($type_array['new'], $type_array['best'], $type_array['hot']);
        $type_merge = array_unique($type_merge);
        $sql .= ' WHERE g.goods_id ' . db_create_in($type_merge);
        $sql .= ' ORDER BY g.sort_order, g.last_update DESC';

        $result = $GLOBALS['db']->getAll($sql);
        foreach ($result AS $idx => $row)
        {
            if ($row['promote_price'] > 0)
            {
                $promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
                $goods[$idx]['promote_price'] = $promote_price > 0 ? price_format($promote_price) : '';
            }
            else
            {
                $goods[$idx]['promote_price'] = '';
            }

            $goods[$idx]['id']           = $row['goods_id'];
            $goods[$idx]['name']         = $row['goods_name'];
            $goods[$idx]['brief']        = $row['goods_brief'];
            $goods[$idx]['brand_name']   = isset($goods_data['brand'][$row['goods_id']]) ? $goods_data['brand'][$row['goods_id']] : '';
            $goods[$idx]['goods_style_name']   = add_style($row['goods_name'],$row['goods_name_style']);

            $goods[$idx]['short_name']   = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
                                               sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
            $goods[$idx]['short_style_name']   = add_style($goods[$idx]['short_name'],$row['goods_name_style']);
            $goods[$idx]['market_price'] = price_format($row['market_price']);
            $goods[$idx]['shop_price']   = price_format($row['shop_price']);
            $goods[$idx]['thumb']        = get_image_path($row['goods_id'], $row['goods_thumb'], true);
            $goods[$idx]['goods_img']    = get_image_path($row['goods_id'], $row['goods_img']);
            $goods[$idx]['url']          = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);
            if (in_array($row['goods_id'], $type_array['best']))
            {
                $type_goods['best'][] = $goods[$idx];
            }
            if (in_array($row['goods_id'], $type_array['new']))
            {
                $type_goods['new'][] = $goods[$idx];
            }
            if (in_array($row['goods_id'], $type_array['hot']))
            {
                $type_goods['hot'][] = $goods[$idx];
            }
        }
    }
    return $type_goods[$type];
}

/**
 * 獲得促銷商品
 *
 * @access  public
 * @return  array
 */
function get_promote_goods($cats = '')
{
    $time = gmtime();
    $order_type = $GLOBALS['_CFG']['recommend_order'];

    /* 取得促銷lbi的數量限制 */
    $num = get_library_number("recommend_promotion");
    $sql = 'SELECT g.goods_id, g.goods_name, g.goods_name_style, g.market_price, g.shop_price AS org_price, g.promote_price, ' .
                "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, ".
                "promote_start_date, promote_end_date, g.goods_brief, g.goods_thumb, goods_img, b.brand_name, " .
                "g.is_best, g.is_new, g.is_hot, g.is_promote, RAND() AS rnd " .
            'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('brand') . ' AS b ON b.brand_id = g.brand_id ' .
            "LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp ".
                "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' ".
            'WHERE g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 ' .
            " AND g.is_promote = 1 AND promote_start_date <= '$time' AND promote_end_date >= '$time' ";
    $sql .= $order_type == 0 ? ' ORDER BY g.sort_order, g.last_update DESC' : ' ORDER BY rnd';
    $sql .= " LIMIT $num ";
    $result = $GLOBALS['db']->getAll($sql);

    $goods = array();
    foreach ($result AS $idx => $row)
    {
        if ($row['promote_price'] > 0)
        {
            $promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
            $goods[$idx]['promote_price'] = $promote_price > 0 ? price_format($promote_price) : '';
        }
        else
        {
            $goods[$idx]['promote_price'] = '';
        }

        $goods[$idx]['id']           = $row['goods_id'];
        $goods[$idx]['name']         = $row['goods_name'];
        $goods[$idx]['brief']        = $row['goods_brief'];
        $goods[$idx]['brand_name']   = $row['brand_name'];
        $goods[$idx]['goods_style_name']   = add_style($row['goods_name'],$row['goods_name_style']);
        $goods[$idx]['short_name']   = $GLOBALS['_CFG']['goods_name_length'] > 0 ? sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
        $goods[$idx]['short_style_name']   = add_style($goods[$idx]['short_name'],$row['goods_name_style']);
        $goods[$idx]['market_price'] = price_format($row['market_price']);
        $goods[$idx]['shop_price']   = price_format($row['shop_price']);
        $goods[$idx]['thumb']        = get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $goods[$idx]['goods_img']    = get_image_path($row['goods_id'], $row['goods_img']);
        $goods[$idx]['url']          = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);
    }

    return $goods;
}

/**
 * 獲得指定分類下的推薦商品
 *
 * @access  public
 * @param   string      $type       推薦類型，可以是 best, new, hot, promote
 * @param   string      $cats       分類的ID
 * @param   integer     $brand      品牌的ID
 * @param   integer     $min        商品價格下限
 * @param   integer     $max        商品價格上限
 * @param   string      $ext        商品擴展查詢
 * @return  array
 */
function get_category_recommend_goods($type = '', $cats = '', $brand = 0, $min =0,  $max = 0, $ext='')
{
    $brand_where = ($brand > 0) ? " AND g.brand_id = '$brand'" : '';

    $price_where = ($min > 0) ? " AND g.shop_price >= $min " : '';
    $price_where .= ($max > 0) ? " AND g.shop_price <= $max " : '';

    $sql =  'SELECT g.goods_id, g.goods_name, g.goods_name_style, g.market_price, g.shop_price AS org_price, g.promote_price, ' .
                "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, ".
                'promote_start_date, promote_end_date, g.goods_brief, g.goods_thumb, goods_img, b.brand_name ' .
            'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('brand') . ' AS b ON b.brand_id = g.brand_id ' .
            "LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp ".
                    "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' ".
            'WHERE g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 ' . $brand_where . $price_where . $ext;
    $num = 0;
    $type2lib = array('best'=>'recommend_best', 'new'=>'recommend_new', 'hot'=>'recommend_hot', 'promote'=>'recommend_promotion');
    $num = get_library_number($type2lib[$type]);

    switch ($type)
    {
        case 'best':
            $sql .= ' AND is_best = 1';
            break;
        case 'new':
            $sql .= ' AND is_new = 1';
            break;
        case 'hot':
            $sql .= ' AND is_hot = 1';
            break;
        case 'promote':
            $time = gmtime();
            $sql .= " AND is_promote = 1 AND promote_start_date <= '$time' AND promote_end_date >= '$time'";
            break;
    }

    if (!empty($cats))
    {
        $sql .= " AND (" . $cats . " OR " . get_extension_goods($cats) .")";
    }

    $order_type = $GLOBALS['_CFG']['recommend_order'];
    $sql .= ($order_type == 0) ? ' ORDER BY g.sort_order, g.last_update DESC' : ' ORDER BY RAND()';
    $res = $GLOBALS['db']->selectLimit($sql, $num);

    $idx = 0;
    $goods = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        if ($row['promote_price'] > 0)
        {
            $promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
            $goods[$idx]['promote_price'] = $promote_price > 0 ? price_format($promote_price) : '';
        }
        else
        {
            $goods[$idx]['promote_price'] = '';
        }

        $goods[$idx]['id']           = $row['goods_id'];
        $goods[$idx]['name']         = $row['goods_name'];
        $goods[$idx]['brief']        = $row['goods_brief'];
        $goods[$idx]['brand_name']   = $row['brand_name'];
        $goods[$idx]['short_name']   = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
                                       sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
        $goods[$idx]['market_price'] = price_format($row['market_price']);
        $goods[$idx]['shop_price']   = price_format($row['shop_price']);
        $goods[$idx]['thumb']        = get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $goods[$idx]['goods_img']    = get_image_path($row['goods_id'], $row['goods_img']);
        $goods[$idx]['url']          = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);

        $goods[$idx]['short_style_name'] = add_style($goods[$idx]['short_name'], $row['goods_name_style']);
        $idx++;
    }

    return $goods;
}

/**
 * 獲得商品的詳細信息
 *
 * @access  public
 * @param   integer     $goods_id
 * @return  void
 */
function get_goods_info($goods_id)
{
    $time = gmtime();
    $sql = 'SELECT g.*, c.measure_unit, b.brand_id, b.brand_name AS goods_brand, m.type_money AS bonus_money, ' .
                'IFNULL(AVG(r.comment_rank), 0) AS comment_rank, ' .
                "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS rank_price " .
            'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('category') . ' AS c ON g.cat_id = c.cat_id ' .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('brand') . ' AS b ON g.brand_id = b.brand_id ' .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('comment') . ' AS r '.
                'ON r.id_value = g.goods_id AND comment_type = 0 AND r.parent_id = 0 AND r.status = 1 ' .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('bonus_type') . ' AS m ' .
                "ON g.bonus_type_id = m.type_id AND m.send_start_date <= '$time' AND m.send_end_date >= '$time'" .
            " LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp ".
                    "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' ".
            "WHERE g.goods_id = '$goods_id' AND g.is_delete = 0 " .
            "GROUP BY g.goods_id";
    $row = $GLOBALS['db']->getRow($sql);

    if ($row !== false)
    {
        /* 用戶評論級別取整 */
        $row['comment_rank']  = ceil($row['comment_rank']) == 0 ? 5 : ceil($row['comment_rank']);

        /* 獲得商品的銷售價格 */
        $row['market_price']        = price_format($row['market_price']);
        $row['shop_price_formated'] = price_format($row['shop_price']);

        /* 修正促銷價格 */
        if ($row['promote_price'] > 0)
        {
            $promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
        }
        else
        {
            $promote_price = 0;
        }

        /* 處理商品水印圖片 */
        $watermark_img = '';

        if ($promote_price != 0)
        {
            $watermark_img = "watermark_promote";
        }
        elseif ($row['is_new'] != 0)
        {
            $watermark_img = "watermark_new";
        }
        elseif ($row['is_best'] != 0)
        {
            $watermark_img = "watermark_best";
        }
        elseif ($row['is_hot'] != 0)
        {
            $watermark_img = 'watermark_hot';
        }

        if ($watermark_img != '')
        {
            $row['watermark_img'] =  $watermark_img;
        }

        $row['promote_price_org'] =  $promote_price;
        $row['promote_price'] =  price_format($promote_price);

        /* 修正重量顯示 */
        $row['goods_weight']  = (intval($row['goods_weight']) > 0) ?
            $row['goods_weight'] . $GLOBALS['_LANG']['kilogram'] :
            ($row['goods_weight'] * 1000) . $GLOBALS['_LANG']['gram'];

        /* 修正上架時間顯示 */
        $row['add_time']      = local_date($GLOBALS['_CFG']['date_format'], $row['add_time']);

        /* 促銷時間倒計時 */
        $time = gmtime();
        if ($time >= $row['promote_start_date'] && $time <= $row['promote_end_date'])
        {
             $row['gmt_end_time']  = $row['promote_end_date'];
        }
        else
        {
            $row['gmt_end_time'] = 0;
        }

        /* 是否顯示商品庫存數量 */
        $row['goods_number']  = ($GLOBALS['_CFG']['use_storage'] == 1) ? $row['goods_number'] : '';

        /* 修正積分：轉換為可使用多少積分（原來是可以使用多少錢的積分） */
        $row['integral']      = $GLOBALS['_CFG']['integral_scale'] ? round($row['integral'] * 100 / $GLOBALS['_CFG']['integral_scale']) : 0;

        /* 修正優惠券 */
        $row['bonus_money']   = ($row['bonus_money'] == 0) ? 0 : price_format($row['bonus_money'], false);

        /* 修正商品圖片 */
        $row['goods_img']   = get_image_path($goods_id, $row['goods_img']);
        $row['goods_thumb'] = get_image_path($goods_id, $row['goods_thumb'], true);

        return $row;
    }
    else
    {
        return false;
    }
}

/**
 * 獲得商品的屬性和規格
 *
 * @access  public
 * @param   integer $goods_id
 * @return  array
 */
function get_goods_properties($goods_id)
{
    /* 對屬性進行重新排序和分組 */
    $sql = "SELECT attr_group ".
            "FROM " . $GLOBALS['ecs']->table('goods_type') . " AS gt, " . $GLOBALS['ecs']->table('goods') . " AS g ".
            "WHERE g.goods_id='$goods_id' AND gt.cat_id=g.goods_type";
    $grp = $GLOBALS['db']->getOne($sql);

    if (!empty($grp))
    {
        $groups = explode("\n", strtr($grp, "\r", ''));
    }

    /* 獲得商品的規格 */
    $sql = "SELECT a.attr_id, a.attr_name, a.attr_group, a.is_linked, a.attr_type, ".
                "g.goods_attr_id, g.attr_value, g.attr_price " .
            'FROM ' . $GLOBALS['ecs']->table('goods_attr') . ' AS g ' .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('attribute') . ' AS a ON a.attr_id = g.attr_id ' .
            "WHERE g.goods_id = '$goods_id' " .
            'ORDER BY a.sort_order, g.attr_price, g.goods_attr_id';
    $res = $GLOBALS['db']->getAll($sql);

    $arr['pro'] = array();     // 屬性
    $arr['spe'] = array();     // 規格
    $arr['lnk'] = array();     // 關聯的屬性

    foreach ($res AS $row)
    {
        $row['attr_value'] = str_replace("\n", '<br />', $row['attr_value']);

        if ($row['attr_type'] == 0)
        {
            $group = (isset($groups[$row['attr_group']])) ? $groups[$row['attr_group']] : $GLOBALS['_LANG']['goods_attr'];

            $arr['pro'][$group][$row['attr_id']]['name']  = $row['attr_name'];
            $arr['pro'][$group][$row['attr_id']]['value'] = $row['attr_value'];
        }
        else
        {
            $arr['spe'][$row['attr_id']]['attr_type'] = $row['attr_type'];
            $arr['spe'][$row['attr_id']]['name']     = $row['attr_name'];
            $arr['spe'][$row['attr_id']]['values'][] = array(
                                                        'label'        => $row['attr_value'],
                                                        'price'        => $row['attr_price'],
                                                        'format_price' => price_format(abs($row['attr_price']), false),
                                                        'id'           => $row['goods_attr_id']);
        }

        if ($row['is_linked'] == 1)
        {
            /* 如果該屬性需要關聯，先保存下來 */
            $arr['lnk'][$row['attr_id']]['name']  = $row['attr_name'];
            $arr['lnk'][$row['attr_id']]['value'] = $row['attr_value'];
        }
    }

    return $arr;
}

/**
 * 獲得屬性相同的商品
 *
 * @access  public
 * @param   array   $attr   // 包含了屬性名稱,ID的數組
 * @return  array
 */
function get_same_attribute_goods($attr)
{
    $lnk = array();

    if (!empty($attr))
    {
        foreach ($attr['lnk'] AS $key => $val)
        {
            $lnk[$key]['title'] = sprintf($GLOBALS['_LANG']['same_attrbiute_goods'], $val['name'], $val['value']);

            /* 查找符合條件的商品 */
            $sql = 'SELECT g.goods_id, g.goods_name, g.goods_thumb, g.goods_img, g.shop_price AS org_price, ' .
                        "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, ".
                        'g.market_price, g.promote_price, g.promote_start_date, g.promote_end_date ' .
                    'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
                    'LEFT JOIN ' . $GLOBALS['ecs']->table('goods_attr') . ' as a ON g.goods_id = a.goods_id ' .
                    "LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp ".
                        "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' ".
                    "WHERE a.attr_id = '$key' AND g.is_on_sale=1 AND a.attr_value = '$val[value]' AND g.goods_id <> '$_REQUEST[id]' " .
                    'LIMIT ' . $GLOBALS['_CFG']['attr_related_number'];
            $res = $GLOBALS['db']->getAll($sql);

            foreach ($res AS $row)
            {
                $lnk[$key]['goods'][$row['goods_id']]['goods_id']      = $row['goods_id'];
                $lnk[$key]['goods'][$row['goods_id']]['goods_name']    = $row['goods_name'];
                $lnk[$key]['goods'][$row['goods_id']]['short_name']    = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
                    sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
                $lnk[$key]['goods'][$row['goods_id']]['goods_thumb']     = (empty($row['goods_thumb'])) ? $GLOBALS['_CFG']['no_picture'] : $row['goods_thumb'];
                $lnk[$key]['goods'][$row['goods_id']]['market_price']  = price_format($row['market_price']);
                $lnk[$key]['goods'][$row['goods_id']]['shop_price']    = price_format($row['shop_price']);
                $lnk[$key]['goods'][$row['goods_id']]['promote_price'] = bargain_price($row['promote_price'],
                    $row['promote_start_date'], $row['promote_end_date']);
                $lnk[$key]['goods'][$row['goods_id']]['url']           = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);
            }
        }
    }

    return $lnk;
}

/**
 * 獲得指定商品的相冊
 *
 * @access  public
 * @param   integer     $goods_id
 * @return  array
 */
function get_goods_gallery($goods_id)
{
    $sql = 'SELECT img_id, img_url, thumb_url, img_desc' .
        ' FROM ' . $GLOBALS['ecs']->table('goods_gallery') .
        " WHERE goods_id = '$goods_id' LIMIT " . $GLOBALS['_CFG']['goods_gallery_number'];
    $row = $GLOBALS['db']->getAll($sql);
    /* 格式化相冊圖片路徑 */
    foreach($row as $key => $gallery_img)
    {
        $row[$key]['img_url'] = get_image_path($goods_id, $gallery_img['img_url'], false, 'gallery');
        $row[$key]['thumb_url'] = get_image_path($goods_id, $gallery_img['thumb_url'], true, 'gallery');
    }
    return $row;
}

/**
 * 獲得指定分類下的商品
 *
 * @access  public
 * @param   integer     $cat_id     分類ID
 * @param   integer     $num        數量
 * @param   string      $from       來自web/wap的調用
 * @param   string      $order_rule 指定商品排序規則
 * @return  array
 */
function assign_cat_goods($cat_id, $num = 0, $from = 'web', $order_rule = '')
{
    $children = get_children($cat_id);

    $sql = 'SELECT g.goods_id, g.goods_name, g.market_price, g.shop_price AS org_price, ' .
                "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, ".
               'g.promote_price, promote_start_date, promote_end_date, g.goods_brief, g.goods_thumb, g.goods_img ' .
            "FROM " . $GLOBALS['ecs']->table('goods') . ' AS g '.
            "LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp ".
                    "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' ".
            'WHERE g.is_on_sale = 1 AND g.is_alone_sale = 1 AND '.
                'g.is_delete = 0 AND (' . $children . 'OR ' . get_extension_goods($children) . ') ';

    $order_rule = empty($order_rule) ? 'ORDER BY g.sort_order, g.goods_id DESC' : $order_rule;
    $sql .= $order_rule;
    if ($num > 0)
    {
        $sql .= ' LIMIT ' . $num;
    }
    $res = $GLOBALS['db']->getAll($sql);

    $goods = array();
    foreach ($res AS $idx => $row)
    {
        if ($row['promote_price'] > 0)
        {
            $promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
            $goods[$idx]['promote_price'] = $promote_price > 0 ? price_format($promote_price) : '';
        }
        else
        {
            $goods[$idx]['promote_price'] = '';
        }

        $goods[$idx]['id']           = $row['goods_id'];
        $goods[$idx]['name']         = $row['goods_name'];
        $goods[$idx]['brief']        = $row['goods_brief'];
        $goods[$idx]['market_price'] = price_format($row['market_price']);
        $goods[$idx]['short_name']   = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
                                        sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
        $goods[$idx]['shop_price']   = price_format($row['shop_price']);
        $goods[$idx]['thumb']        = get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $goods[$idx]['goods_img']    = get_image_path($row['goods_id'], $row['goods_img']);
        $goods[$idx]['url']          = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);
    }

    if ($from == 'web')
    {
        $GLOBALS['smarty']->assign('cat_goods_' . $cat_id, $goods);
    }
    elseif ($from == 'wap')
    {
        $cat['goods'] = $goods;
    }

    /* 分類信息 */
    $sql = 'SELECT cat_name FROM ' . $GLOBALS['ecs']->table('category') . " WHERE cat_id = '$cat_id'";
    $cat['name'] = $GLOBALS['db']->getOne($sql);
    $cat['url']  = build_uri('category', array('cid' => $cat_id), $cat['name']);
    $cat['id']   = $cat_id;

    return $cat;
}

/**
 * 獲得指定的品牌下的商品
 *
 * @access  public
 * @param   integer     $brand_id       品牌的ID
 * @param   integer     $num            數量
 * @param   integer     $cat_id         分類編號
 * @param   string      $order_rule     指定商品排序規則
 * @return  void
 */
function assign_brand_goods($brand_id, $num = 0, $cat_id = 0,$order_rule = '')
{
    $sql =  'SELECT g.goods_id, g.goods_name, g.market_price, g.shop_price AS org_price, ' .
                "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, ".
                'g.promote_price, g.promote_start_date, g.promote_end_date, g.goods_brief, g.goods_thumb, g.goods_img ' .
            'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
            "LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp ".
                    "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' ".
            "WHERE g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 AND g.brand_id = '$brand_id'";

    if ($cat_id > 0)
    {
        $sql .= get_children($cat_id);
    }

    $order_rule = empty($order_rule) ? ' ORDER BY g.sort_order, g.goods_id DESC' : $order_rule;
    $sql .= $order_rule;
    if ($num > 0)
    {
        $res = $GLOBALS['db']->selectLimit($sql, $num);
    }
    else
    {
        $res = $GLOBALS['db']->query($sql);
    }

    $idx = 0;
    $goods = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        if ($row['promote_price'] > 0)
        {
            $promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
        }
        else
        {
            $promote_price = 0;
        }

        $goods[$idx]['id']            = $row['goods_id'];
        $goods[$idx]['name']          = $row['goods_name'];
        $goods[$idx]['short_name']    = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
            sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
        $goods[$idx]['market_price']  = price_format($row['market_price']);
        $goods[$idx]['shop_price']    = price_format($row['shop_price']);
        $goods[$idx]['promote_price'] = $promote_price > 0 ? price_format($promote_price) : '';
        $goods[$idx]['brief']         = $row['goods_brief'];
        $goods[$idx]['thumb']         = get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $goods[$idx]['goods_img']     = get_image_path($row['goods_id'], $row['goods_img']);
        $goods[$idx]['url']           = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);

        $idx++;
    }

    /* 分類信息 */
    $sql = 'SELECT brand_name FROM ' . $GLOBALS['ecs']->table('brand') . " WHERE brand_id = '$brand_id'";

    $brand['id']   = $brand_id;
    $brand['name'] = $GLOBALS['db']->getOne($sql);
    $brand['url']  = build_uri('brand', array('bid' => $brand_id), $brand['name']);

    $brand_goods = array('brand' => $brand, 'goods' => $goods);

    return $brand_goods;
}

/**
 * 獲得所有擴展分類屬於指定分類的所有商品ID
 *
 * @access  public
 * @param   string $cat_id     分類查詢字符串
 * @return  string
 */
function get_extension_goods($cats)
{
    $extension_goods_array = '';
    $sql = 'SELECT goods_id FROM ' . $GLOBALS['ecs']->table('goods_cat') . " AS g WHERE $cats";
    $extension_goods_array = $GLOBALS['db']->getCol($sql);
    return db_create_in($extension_goods_array, 'g.goods_id');
}

/**
 * 判斷某個商品是否正在特價促銷期
 *
 * @access  public
 * @param   float   $price      促銷價格
 * @param   string  $start      促銷開始日期
 * @param   string  $end        促銷結束日期
 * @return  float   如果還在促銷期則返回促銷價，否則返回0
 */
function bargain_price($price, $start, $end)
{
    if ($price == 0)
    {
        return 0;
    }
    else
    {
        $time = gmtime();
        if ($time >= $start && $time <= $end)
        {
            return $price;
        }
        else
        {
            return 0;
        }
    }
}

/**
 * 獲得指定的規格的價格
 *
 * @access  public
 * @param   mix     $spec   規格ID的數組或者逗號分隔的字符串
 * @return  void
 */
function spec_price($spec)
{
    if (!empty($spec))
    {
        $where = db_create_in($spec, 'goods_attr_id');

        $sql = 'SELECT SUM(attr_price) AS attr_price FROM ' . $GLOBALS['ecs']->table('goods_attr') . " WHERE $where";
        $price = floatval($GLOBALS['db']->getOne($sql));
    }
    else
    {
        $price = 0;
    }

    return $price;
}

/**
 * 取得團購活動信息
 * @param   int     $group_buy_id   團購活動id
 * @param   int     $current_num    本次購買數量（計算當前價時要加上的數量）
 * @return  array
 *                  status          狀態：
 */
function group_buy_info($group_buy_id, $current_num = 0)
{
    /* 取得團購活動信息 */
    $group_buy_id = intval($group_buy_id);
    $sql = "SELECT *, act_id AS group_buy_id, act_desc AS group_buy_desc, start_time AS start_date, end_time AS end_date " .
            "FROM " . $GLOBALS['ecs']->table('goods_activity') .
            "WHERE act_id = '$group_buy_id' " .
            "AND act_type = '" . GAT_GROUP_BUY . "'";
    $group_buy = $GLOBALS['db']->getRow($sql);

    /* 如果為空，返回空數組 */
    if (empty($group_buy))
    {
        return array();
    }

    $ext_info = unserialize($group_buy['ext_info']);
    $group_buy = array_merge($group_buy, $ext_info);

    /* 格式化時間 */
    $group_buy['formated_start_date'] = local_date('Y-m-d H:i', $group_buy['start_time']);
    $group_buy['formated_end_date'] = local_date('Y-m-d H:i', $group_buy['end_time']);

    /* 格式化保證金 */
    $group_buy['formated_deposit'] = price_format($group_buy['deposit'], false);

    /* 處理價格階梯 */
    $price_ladder = $group_buy['price_ladder'];
    if (!is_array($price_ladder) || empty($price_ladder))
    {
        $price_ladder = array(array('amount' => 0, 'price' => 0));
    }
    else
    {
        foreach ($price_ladder as $key => $amount_price)
        {
            $price_ladder[$key]['formated_price'] = price_format($amount_price['price'], false);
        }
    }
    $group_buy['price_ladder'] = $price_ladder;

    /* 統計信息 */
    $stat = group_buy_stat($group_buy_id, $group_buy['deposit']);
    $group_buy = array_merge($group_buy, $stat);

    /* 計算當前價 */
    $cur_price  = $price_ladder[0]['price']; // 初始化
    $cur_amount = $stat['valid_goods'] + $current_num; // 當前數量
    foreach ($price_ladder as $amount_price)
    {
        if ($cur_amount >= $amount_price['amount'])
        {
            $cur_price = $amount_price['price'];
        }
        else
        {
            break;
        }
    }
    $group_buy['cur_price'] = $cur_price;
    $group_buy['formated_cur_price'] = price_format($cur_price, false);

    /* 最終價 */
    $group_buy['trans_price'] = $group_buy['cur_price'];
    $group_buy['formated_trans_price'] = $group_buy['formated_cur_price'];
    $group_buy['trans_amount'] = $group_buy['valid_goods'];

    /* 狀態 */
    $group_buy['status'] = group_buy_status($group_buy);
    if (isset($GLOBALS['_LANG']['gbs'][$group_buy['status']]))
    {
        $group_buy['status_desc'] = $GLOBALS['_LANG']['gbs'][$group_buy['status']];
    }

    $group_buy['start_time'] = $group_buy['formated_start_date'];
    $group_buy['end_time'] = $group_buy['formated_end_date'];

    return $group_buy;
}

/*
 * 取得某團購活動統計信息
 * @param   int     $group_buy_id   團購活動id
 * @param   float   $deposit        保證金
 * @return  array   統計信息
 *                  total_order     總訂單數
 *                  total_goods     總商品數
 *                  valid_order     有效訂單數
 *                  valid_goods     有效商品數
 */
function group_buy_stat($group_buy_id, $deposit)
{
    $group_buy_id = intval($group_buy_id);

    /* 取得團購活動商品ID */
    $sql = "SELECT goods_id " .
           "FROM " . $GLOBALS['ecs']->table('goods_activity') .
           "WHERE act_id = '$group_buy_id' " .
           "AND act_type = '" . GAT_GROUP_BUY . "'";
    $group_buy_goods_id = $GLOBALS['db']->getOne($sql);

    /* 取得總訂單數和總商品數 */
    $sql = "SELECT COUNT(*) AS total_order, SUM(g.goods_number) AS total_goods " .
            "FROM " . $GLOBALS['ecs']->table('order_info') . " AS o, " .
                $GLOBALS['ecs']->table('order_goods') . " AS g " .
            " WHERE o.order_id = g.order_id " .
            "AND o.extension_code = 'group_buy' " .
            "AND o.extension_id = '$group_buy_id' " .
            "AND g.goods_id = '$group_buy_goods_id' " .
            "AND (order_status = '" . OS_CONFIRMED . "' OR order_status = '" . OS_UNCONFIRMED . "')";
    $stat = $GLOBALS['db']->getRow($sql);
    if ($stat['total_order'] == 0)
    {
        $stat['total_goods'] = 0;
    }

    /* 取得有效訂單數和有效商品數 */
    $deposit = floatval($deposit);
    if ($deposit > 0 && $stat['total_order'] > 0)
    {
        $sql .= " AND (o.money_paid + o.surplus) >= '$deposit'";
        $row = $GLOBALS['db']->getRow($sql);
        $stat['valid_order'] = $row['total_order'];
        if ($stat['valid_order'] == 0)
        {
            $stat['valid_goods'] = 0;
        }
        else
        {
            $stat['valid_goods'] = $row['total_goods'];
        }
    }
    else
    {
        $stat['valid_order'] = $stat['total_order'];
        $stat['valid_goods'] = $stat['total_goods'];
    }

    return $stat;
}

/**
 * 獲得團購的狀態
 *
 * @access  public
 * @param   array
 * @return  integer
 */
function group_buy_status($group_buy)
{
    $now = gmtime();
    if ($group_buy['is_finished'] == 0)
    {
        /* 未處理 */
        if ($now < $group_buy['start_time'])
        {
            $status = GBS_PRE_START;
        }
        elseif ($now > $group_buy['end_time'])
        {
            $status = GBS_FINISHED;
        }
        else
        {
            if ($group_buy['restrict_amount'] == 0 || $group_buy['valid_goods'] < $group_buy['restrict_amount'])
            {
                $status = GBS_UNDER_WAY;
            }
            else
            {
                $status = GBS_FINISHED;
            }
        }
    }
    elseif ($group_buy['is_finished'] == GBS_SUCCEED)
    {
        /* 已處理，團購成功 */
        $status = GBS_SUCCEED;
    }
    elseif ($group_buy['is_finished'] == GBS_FAIL)
    {
        /* 已處理，團購失敗 */
        $status = GBS_FAIL;
    }

    return $status;
}

/**
 * 取得拍賣活動信息
 * @param   int     $act_id     活動id
 * @return  array
 */
function auction_info($act_id, $config = false)
{
    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('goods_activity') . " WHERE act_id = '$act_id'";
    $auction = $GLOBALS['db']->getRow($sql);
    if ($auction['act_type'] != GAT_AUCTION)
    {
        return array();
    }
    $auction['status_no'] = auction_status($auction);
    if ($config == true)
    {

        $auction['start_time'] = local_date('Y-m-d H:i', $auction['start_time']);
        $auction['end_time'] = local_date('Y-m-d H:i', $auction['end_time']);
    }
    else
    {
        $auction['start_time'] = local_date($GLOBALS['_CFG']['time_format'], $auction['start_time']);
        $auction['end_time'] = local_date($GLOBALS['_CFG']['time_format'], $auction['end_time']);
    }
    $ext_info = unserialize($auction['ext_info']);
    $auction = array_merge($auction, $ext_info);
    $auction['formated_start_price'] = price_format($auction['start_price']);
    $auction['formated_end_price'] = price_format($auction['end_price']);
    $auction['formated_amplitude'] = price_format($auction['amplitude']);
    $auction['formated_deposit'] = price_format($auction['deposit']);

    /* 查詢出價用戶數和最後出價 */
    $sql = "SELECT COUNT(DISTINCT bid_user) FROM " . $GLOBALS['ecs']->table('auction_log') .
            " WHERE act_id = '$act_id'";
    $auction['bid_user_count'] = $GLOBALS['db']->getOne($sql);
    if ($auction['bid_user_count'] > 0)
    {
        $sql = "SELECT a.*, u.user_name " .
                "FROM " . $GLOBALS['ecs']->table('auction_log') . " AS a, " .
                        $GLOBALS['ecs']->table('users') . " AS u " .
                "WHERE a.bid_user = u.user_id " .
                "AND act_id = '$act_id' " .
                "ORDER BY a.log_id DESC";
        $row = $GLOBALS['db']->getRow($sql);
        $row['formated_bid_price'] = price_format($row['bid_price'], false);
        $row['bid_time'] = local_date($GLOBALS['_CFG']['time_format'], $row['bid_time']);
        $auction['last_bid'] = $row;
    }

    /* 查詢已確認訂單數 */
    if ($auction['status_no'] > 1)
    {
        $sql = "SELECT COUNT(*)" .
                " FROM " . $GLOBALS['ecs']->table('order_info') .
                " WHERE extension_code = 'auction'" .
                " AND extension_id = '$act_id'" .
                " AND order_status " . db_create_in(array(OS_CONFIRMED, OS_UNCONFIRMED));
        $auction['order_count'] = $GLOBALS['db']->getOne($sql);
    }
    else
    {
        $auction['order_count'] = 0;
    }

    /* 當前價 */
    $auction['current_price'] = isset($auction['last_bid']) ? $auction['last_bid']['bid_price'] : $auction['start_price'];
    $auction['formated_current_price'] = price_format($auction['current_price'], false);

    return $auction;
}

/**
 * 取得拍賣活動出價記錄
 * @param   int     $act_id     活動id
 * @return  array
 */
function auction_log($act_id)
{
    $log = array();
    $sql = "SELECT a.*, u.user_name " .
            "FROM " . $GLOBALS['ecs']->table('auction_log') . " AS a," .
                      $GLOBALS['ecs']->table('users') . " AS u " .
            "WHERE a.bid_user = u.user_id " .
            "AND act_id = '$act_id' " .
            "ORDER BY a.log_id DESC";
    $res = $GLOBALS['db']->query($sql);
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $row['bid_time'] = local_date($GLOBALS['_CFG']['time_format'], $row['bid_time']);
        $row['formated_bid_price'] = price_format($row['bid_price'], false);
        $log[] = $row;
    }

    return $log;
}

/**
 * 計算拍賣活動狀態（注意參數一定是原始信息）
 * @param   array   $auction    拍賣活動原始信息
 * @return  int
 */
function auction_status($auction)
{
    $now = gmtime();
    if ($auction['is_finished'] == 0)
    {
        if ($now < $auction['start_time'])
        {
            return PRE_START; // 未開始
        }
        elseif ($now > $auction['end_time'])
        {
            return FINISHED; // 已結束，未處理
        }
        else
        {
            return UNDER_WAY; // 進行中
        }
    }
    elseif ($auction['is_finished'] == 1)
    {
        return FINISHED; // 已結束，未處理
    }
    else
    {
        return SETTLED; // 已結束，已處理
    }
}

/**
 * 取得商品信息
 * @param   int     $goods_id   商品id
 * @return  array
 */
function goods_info($goods_id)
{
    $sql = "SELECT g.*, b.brand_name " .
            "FROM " . $GLOBALS['ecs']->table('goods') . " AS g " .
                "LEFT JOIN " . $GLOBALS['ecs']->table('brand') . " AS b ON g.brand_id = b.brand_id " .
            "WHERE g.goods_id = '$goods_id'";
    $row = $GLOBALS['db']->getRow($sql);
    if (!empty($row))
    {
        /* 修正重量顯示 */
        $row['goods_weight'] = (intval($row['goods_weight']) > 0) ?
            $row['goods_weight'] . $GLOBALS['_LANG']['kilogram'] :
            ($row['goods_weight'] * 1000) . $GLOBALS['_LANG']['gram'];

        /* 修正圖片 */
        $row['goods_img'] = get_image_path($goods_id, $row['goods_img']);
    }

    return $row;
}

/**
 * 取得優惠活動信息
 * @param   int     $act_id     活動id
 * @return  array
 */
function favourable_info($act_id)
{
    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('favourable_activity') .
            " WHERE act_id = '$act_id'";
    $row = $GLOBALS['db']->getRow($sql);
    if (!empty($row))
    {
        $row['start_time'] = local_date($GLOBALS['_CFG']['time_format'], $row['start_time']);
        $row['end_time'] = local_date($GLOBALS['_CFG']['time_format'], $row['end_time']);
        $row['formated_min_amount'] = price_format($row['min_amount']);
        $row['formated_max_amount'] = price_format($row['max_amount']);
        $row['gift'] = unserialize($row['gift']);
        if ($row['act_type'] == FAT_GOODS)
        {
            $row['act_type_ext'] = round($row['act_type_ext']);
        }
    }

    return $row;
}

/**
 * 批發信息
 * @param   int     $act_id     活動id
 * @return  array
 */
function wholesale_info($act_id)
{
    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('wholesale') .
            " WHERE act_id = '$act_id'";
    $row = $GLOBALS['db']->getRow($sql);
    if (!empty($row))
    {
        $row['price_list'] = unserialize($row['prices']);
    }

    return $row;
}

/**
 * 添加商品名樣式
 * @param   string     $goods_name     商品名稱
 * @param   string     $style          樣式參數
 * @return  string
 */
function add_style($goods_name, $style)
{
    $goods_style_name = $goods_name;

    $arr   = explode('+', $style);

    $font_color     = !empty($arr[0]) ? $arr[0] : '';
    $font_style = !empty($arr[1]) ? $arr[1] : '';

    if ($font_color!='')
    {
        $goods_style_name = '<font color=' . $font_color . '>' . $goods_style_name . '</font>';
    }
    if ($font_style != '')
    {
        $goods_style_name = '<' . $font_style .'>' . $goods_style_name . '</' . $font_style . '>';
    }
    return $goods_style_name;
}

/**
 * 取得商品屬性
 * @param   int     $goods_id   商品id
 * @return  array
 */
function get_goods_attr($goods_id)
{
    $attr_list = array();
    $sql = "SELECT a.attr_id, a.attr_name " .
            "FROM " . $GLOBALS['ecs']->table('goods') . " AS g, " . $GLOBALS['ecs']->table('attribute') . " AS a " .
            "WHERE g.goods_id = '$goods_id' " .
            "AND g.goods_type = a.cat_id " .
            "AND a.attr_type = 1";
    $attr_id_list = $GLOBALS['db']->getCol($sql);
    $res = $GLOBALS['db']->query($sql);
    while ($attr = $GLOBALS['db']->fetchRow($res))
    {
        if (defined('ECS_ADMIN'))
        {
            $attr['goods_attr_list'] = array(0 => $GLOBALS['_LANG']['select_please']);
        }
        else
        {
            $attr['goods_attr_list'] = array();
        }
        $attr_list[$attr['attr_id']] = $attr;
    }

    $sql = "SELECT attr_id, goods_attr_id, attr_value " .
            "FROM " . $GLOBALS['ecs']->table('goods_attr') .
            " WHERE goods_id = '$goods_id' " .
            "AND attr_id " . db_create_in($attr_id_list);
    $res = $GLOBALS['db']->query($sql);
    while ($goods_attr = $GLOBALS['db']->fetchRow($res))
    {
        $attr_list[$goods_attr['attr_id']]['goods_attr_list'][$goods_attr['goods_attr_id']] = $goods_attr['attr_value'];
    }

    return $attr_list;
}

/**
 * 獲得購物車中商品的配件
 *
 * @access  public
 * @param   array     $goods_list
 * @return  array
 */
function get_goods_fittings($goods_list = array())
{
    $temp_index = 0;
    $arr        = array();

    $sql = 'SELECT gg.parent_id, ggg.goods_name AS parent_name, gg.goods_id, gg.goods_price, g.goods_name, g.goods_thumb, g.goods_img, g.shop_price AS org_price, ' .
                "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price ".
            'FROM ' . $GLOBALS['ecs']->table('group_goods') . ' AS gg ' .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('goods') . 'AS g ON g.goods_id = gg.goods_id ' .
            "LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp ".
                    "ON mp.goods_id = gg.goods_id AND mp.user_rank = '$_SESSION[user_rank]' ".
            "LEFT JOIN " . $GLOBALS['ecs']->table('goods') . " AS ggg ON ggg.goods_id = gg.parent_id ".
            "WHERE gg.parent_id " . db_create_in($goods_list) . " AND g.is_delete = 0 AND g.is_on_sale = 1 ".
            "ORDER BY gg.parent_id, gg.goods_id";

    $res = $GLOBALS['db']->query($sql);

    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $arr[$temp_index]['parent_id']         = $row['parent_id'];//配件的基本件ID
        $arr[$temp_index]['parent_name']       = $row['parent_name'];//配件的基本件的名稱
        $arr[$temp_index]['parent_short_name'] = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
            sub_str($row['parent_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['parent_name'];//配件的基本件顯示的名稱
        $arr[$temp_index]['goods_id']          = $row['goods_id'];//配件的商品ID
        $arr[$temp_index]['goods_name']        = $row['goods_name'];//配件的名稱
        $arr[$temp_index]['short_name']        = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
            sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];//配件顯示的名稱
        $arr[$temp_index]['fittings_price']    = price_format($row['goods_price']);//配件價格
        $arr[$temp_index]['shop_price']        = price_format($row['shop_price']);//配件原價格
        $arr[$temp_index]['goods_thumb']       = get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $arr[$temp_index]['goods_img']         = get_image_path($row['goods_id'], $row['goods_img']);
        $arr[$temp_index]['url']               = build_uri('goods', array('gid'=>$row['goods_id']), $row['goods_name']);
        $temp_index ++;
    }

    return $arr;
}

/**
 * 取指定規格的貨品信息
 *
 * @access      public
 * @param       string      $goods_id
 * @param       array       $spec_goods_attr_id
 * @return      array
 */
function get_products_info($goods_id, $spec_goods_attr_id)
{
    $return_array = array();

    if (empty($spec_goods_attr_id) || !is_array($spec_goods_attr_id) || empty($goods_id))
    {
        return $return_array;
    }

    $goods_attr_array = sort_goods_attr_id_array($spec_goods_attr_id);

    if(isset($goods_attr_array['sort']))
    {
        $goods_attr = implode('|', $goods_attr_array['sort']);

        $sql = "SELECT * FROM " .$GLOBALS['ecs']->table('products'). " WHERE goods_id = '$goods_id' AND goods_attr = '$goods_attr' LIMIT 0, 1";
        $return_array = $GLOBALS['db']->getRow($sql);
    }
    return $return_array;
}
?>