<?php

/**
 * ECSHOP 購物流程函數庫
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發布。
 * ============================================================================
 * $Author: yehuaixiao $
 * $Id: lib_order.php 17161 2010-05-18 09:13:15Z yehuaixiao $
 */

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

/**
 * 處理序列化的支付、配送的配置參數
 * 返回一個以name為索引的數組
 *
 * @access  public
 * @param   string       $cfg
 * @return  void
 */
function unserialize_config($cfg)
{
    if (is_string($cfg) && ($arr = unserialize($cfg)) !== false)
    {
        $config = array();

        foreach ($arr AS $key => $val)
        {
            $config[$val['name']] = $val['value'];
        }

        return $config;
    }
    else
    {
        return false;
    }
}
/**
 * 取得已安裝的配送方式
 * @return  array   已安裝的配送方式
 */
function shipping_list()
{
    $sql = 'SELECT shipping_id, shipping_name ' .
            'FROM ' . $GLOBALS['ecs']->table('shipping') .
            ' WHERE enabled = 1';

    return $GLOBALS['db']->getAll($sql);
}

/**
 * 取得配送方式信息
 * @param   int     $shipping_id    配送方式id
 * @return  array   配送方式信息
 */
function shipping_info($shipping_id)
{
    $sql = 'SELECT * FROM ' . $GLOBALS['ecs']->table('shipping') .
            " WHERE shipping_id = '$shipping_id' " .
            'AND enabled = 1';

    return $GLOBALS['db']->getRow($sql);
}

/**
 * 取得可用的配送方式列表
 * @param   array   $region_id_list     收貨人地區id數組（包括國家、省、市、區）
 * @return  array   配送方式數組
 */
function available_shipping_list($region_id_list)
{
    $sql = 'SELECT s.shipping_id, s.shipping_code, s.shipping_name, ' .
                's.shipping_desc, s.insure, s.support_cod, a.configure ' .
            'FROM ' . $GLOBALS['ecs']->table('shipping') . ' AS s, ' .
                $GLOBALS['ecs']->table('shipping_area') . ' AS a, ' .
                $GLOBALS['ecs']->table('area_region') . ' AS r ' .
            'WHERE r.region_id ' . db_create_in($region_id_list) .
            ' AND r.shipping_area_id = a.shipping_area_id AND a.shipping_id = s.shipping_id AND s.enabled = 1';

    return $GLOBALS['db']->getAll($sql);
}

/**
 * 取得某配送方式對應於某收貨地址的區域信息
 * @param   int     $shipping_id        配送方式id
 * @param   array   $region_id_list     收貨人地區id數組
 * @return  array   配送區域信息（config 對應著反序列化的 configure）
 */
function shipping_area_info($shipping_id, $region_id_list)
{
    $sql = 'SELECT s.shipping_code, s.shipping_name, ' .
                's.shipping_desc, s.insure, s.support_cod, a.configure ' .
            'FROM ' . $GLOBALS['ecs']->table('shipping') . ' AS s, ' .
                $GLOBALS['ecs']->table('shipping_area') . ' AS a, ' .
                $GLOBALS['ecs']->table('area_region') . ' AS r ' .
            "WHERE s.shipping_id = '$shipping_id' " .
            'AND r.region_id ' . db_create_in($region_id_list) .
            ' AND r.shipping_area_id = a.shipping_area_id AND a.shipping_id = s.shipping_id AND s.enabled = 1';
    $row = $GLOBALS['db']->getRow($sql);

    if (!empty($row))
    {
        $shipping_config = unserialize_config($row['configure']);
        if (isset($shipping_config['pay_fee']))
        {
            if (strpos($shipping_config['pay_fee'], '%') !== false)
            {
                $row['pay_fee'] = floatval($shipping_config['pay_fee']) . '%';
            }
            else
            {
                 $row['pay_fee'] = floatval($shipping_config['pay_fee']);
            }
        }
        else
        {
            $row['pay_fee'] = 0.00;
        }
    }

    return $row;
}

/**
 * 計算運費
 * @param   string  $shipping_code      配送方式代碼
 * @param   mix     $shipping_config    配送方式配置信息
 * @param   float   $goods_weight       商品重量
 * @param   float   $goods_amount       商品金額
 * @param   float   $goods_number       商品數量
 * @return  float   運費
 */
function shipping_fee($shipping_code, $shipping_config, $goods_weight, $goods_amount, $goods_number='')
{
    if (!is_array($shipping_config))
    {
        $shipping_config = unserialize($shipping_config);
    }

    $filename = ROOT_PATH . 'includes/modules/shipping/' . $shipping_code . '.php';
    if (file_exists($filename))
    {
        include_once($filename);

        $obj = new $shipping_code($shipping_config);

        return $obj->calculate($goods_weight, $goods_amount, $goods_number);
    }
    else
    {
        return 0;
    }
}

/**
 * 獲取指定配送的保價費用
 *
 * @access  public
 * @param   string      $shipping_code  配送方式的code
 * @param   float       $goods_amount   保價金額
 * @param   mix         $insure         保價比例
 * @return  float
 */
function shipping_insure_fee($shipping_code, $goods_amount, $insure)
{
    if (strpos($insure, '%') === false)
    {
        /* 如果保價費用不是百分比則直接返回該數值 */
        return floatval($insure);
    }
    else
    {
        $path = ROOT_PATH . 'includes/modules/shipping/' . $shipping_code . '.php';

        if (file_exists($path))
        {
            include_once($path);

            $shipping = new $shipping_code;
            $insure   = floatval($insure) / 100;

            if (method_exists($shipping, 'calculate_insure'))
            {
                return $shipping->calculate_insure($goods_amount, $insure);
            }
            else
            {
                return ceil($goods_amount * $insure);
            }
        }
        else
        {
            return false;
        }
    }
}

/**
 * 取得已安裝的支付方式列表
 * @return  array   已安裝的配送方式列表
 */
function payment_list()
{
    $sql = 'SELECT pay_id, pay_name ' .
            'FROM ' . $GLOBALS['ecs']->table('payment') .
            ' WHERE enabled = 1';

    return $GLOBALS['db']->getAll($sql);
}

/**
 * 取得支付方式信息
 * @param   int     $pay_id     支付方式id
 * @return  array   支付方式信息
 */
function payment_info($pay_id)
{
    $sql = 'SELECT * FROM ' . $GLOBALS['ecs']->table('payment') .
            " WHERE pay_id = '$pay_id' AND enabled = 1";

    return $GLOBALS['db']->getRow($sql);
}

/**
 * 獲得訂單需要支付的支付費用
 *
 * @access  public
 * @param   integer $payment_id
 * @param   float   $order_amount
 * @param   mix     $cod_fee
 * @return  float
 */
function pay_fee($payment_id, $order_amount, $cod_fee=null)
{
    $pay_fee = 0;
    $payment = payment_info($payment_id);
    $rate    = ($payment['is_cod'] && !is_null($cod_fee)) ? $cod_fee : $payment['pay_fee'];

    if (strpos($rate, '%') !== false)
    {
        /* 支付費用是一個比例 */
        $val     = floatval($rate) / 100;
        $pay_fee = $val > 0 ? $order_amount * $val /(1- $val) : 0;
    }
    else
    {
        $pay_fee = floatval($rate);
    }

    return round($pay_fee, 2);
}

/**
 * 取得可用的支付方式列表
 * @param   bool    $support_cod        配送方式是否支持貨到付款
 * @param   int     $cod_fee            貨到付款手續費（當配送方式支持貨到付款時才傳此參數）
 * @param   int     $is_online          是否支持在線支付
 * @return  array   配送方式數組
 */
function available_payment_list($support_cod, $cod_fee = 0, $is_online = false)
{
    $sql = 'SELECT pay_id, pay_code, pay_name, pay_fee, pay_desc, pay_config, is_cod' .
            ' FROM ' . $GLOBALS['ecs']->table('payment') .
            ' WHERE enabled = 1 ';
    if (!$support_cod)
    {
        $sql .= 'AND is_cod = 0 '; // 如果不支持貨到付款
    }
    if ($is_online)
    {
        $sql .= "AND is_online = '1' ";
    }
    $sql .= 'ORDER BY pay_order'; // 排序
    $res = $GLOBALS['db']->query($sql);

    $pay_list = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        if ($row['is_cod'] == '1')
        {
            $row['pay_fee'] = $cod_fee;
        }

        $row['format_pay_fee'] = strpos($row['pay_fee'], '%') !== false ? $row['pay_fee'] :
        price_format($row['pay_fee'], false);
        $modules[] = $row;
    }

    include_once(ROOT_PATH.'includes/lib_compositor.php');

    if(isset($modules))
    {
        return $modules;
    }
}

/**
 * 取得包裝列表
 * @return  array   包裝列表
 */
function pack_list()
{
    $sql = 'SELECT * FROM ' . $GLOBALS['ecs']->table('pack');
    $res = $GLOBALS['db']->query($sql);

    $list = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $row['format_pack_fee'] = price_format($row['pack_fee'], false);
        $row['format_free_money'] = price_format($row['free_money'], false);
        $list[] = $row;
    }

    return $list;
}

/**
 * 取得包裝信息
 * @param   int     $pack_id    包裝id
 * @return  array   包裝信息
 */
function pack_info($pack_id)
{
    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('pack') .
            " WHERE pack_id = '$pack_id'";

    return $GLOBALS['db']->getRow($sql);
}

/**
 * 根據訂單中的商品總額來獲得包裝的費用
 *
 * @access  public
 * @param   integer $pack_id
 * @param   float   $goods_amount
 * @return  float
 */
function pack_fee($pack_id, $goods_amount)
{
    $pack = pack_info($pack_id);

    $val = (floatval($pack['free_money']) <= $goods_amount && $pack['free_money'] > 0) ? 0 : floatval($pack['pack_fee']);

    return $val;
}

/**
 * 取得賀卡列表
 * @return  array   賀卡列表
 */
function card_list()
{
    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('card');
    $res = $GLOBALS['db']->query($sql);

    $list = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $row['format_card_fee'] = price_format($row['card_fee'], false);
        $row['format_free_money'] = price_format($row['free_money'], false);
        $list[] = $row;
    }

    return $list;
}

/**
 * 取得賀卡信息
 * @param   int     $card_id    賀卡id
 * @return  array   賀卡信息
 */
function card_info($card_id)
{
    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('card') .
            " WHERE card_id = '$card_id'";

    return $GLOBALS['db']->getRow($sql);
}

/**
 * 根據訂單中商品總額獲得需要支付的賀卡費用
 *
 * @access  public
 * @param   integer $card_id
 * @param   float   $goods_amount
 * @return  float
 */
function card_fee($card_id, $goods_amount)
{
    $card = card_info($card_id);

    return ($card['free_money'] <= $goods_amount && $card['free_money'] > 0) ? 0 : $card['card_fee'];
}

/**
 * 取得訂單信息
 * @param   int     $order_id   訂單id（如果order_id > 0 就按id查，否則按sn查）
 * @param   string  $order_sn   訂單號
 * @return  array   訂單信息（金額都有相應格式化的字段，前綴是formated_）
 */
function order_info($order_id, $order_sn = '')
{
    /* 計算訂單各種費用之和的語句 */
    $total_fee = " (goods_amount - discount + tax + shipping_fee + insure_fee + pay_fee + pack_fee + card_fee) AS total_fee ";
    $order_id = intval($order_id);
    if ($order_id > 0)
    {
        $sql = "SELECT *, " . $total_fee . " FROM " . $GLOBALS['ecs']->table('order_info') .
                " WHERE order_id = '$order_id'";
    }
    else
    {
        $sql = "SELECT *, " . $total_fee . "  FROM " . $GLOBALS['ecs']->table('order_info') .
                " WHERE order_sn = '$order_sn'";
    }
    $order = $GLOBALS['db']->getRow($sql);

    /* 格式化金額字段 */
    if ($order)
    {
        $order['formated_goods_amount']   = price_format($order['goods_amount'], false);
        $order['formated_discount']       = price_format($order['discount'], false);
        $order['formated_tax']            = price_format($order['tax'], false);
        $order['formated_shipping_fee']   = price_format($order['shipping_fee'], false);
        $order['formated_insure_fee']     = price_format($order['insure_fee'], false);
        $order['formated_pay_fee']        = price_format($order['pay_fee'], false);
        $order['formated_pack_fee']       = price_format($order['pack_fee'], false);
        $order['formated_card_fee']       = price_format($order['card_fee'], false);
        $order['formated_total_fee']      = price_format($order['total_fee'], false);
        $order['formated_money_paid']     = price_format($order['money_paid'], false);
        $order['formated_bonus']          = price_format($order['bonus'], false);
        $order['formated_integral_money'] = price_format($order['integral_money'], false);
        $order['formated_surplus']        = price_format($order['surplus'], false);
        $order['formated_order_amount']   = price_format(abs($order['order_amount']), false);
        $order['formated_add_time']       = local_date($GLOBALS['_CFG']['time_format'], $order['add_time']);
    }

    return $order;
}

/**
 * 判斷訂單是否已完成
 * @param   array   $order  訂單信息
 * @return  bool
 */
function order_finished($order)
{
    return $order['order_status']  == OS_CONFIRMED &&
        ($order['shipping_status'] == SS_SHIPPED || $order['shipping_status'] == SS_RECEIVED) &&
        ($order['pay_status']      == PS_PAYED   || $order['pay_status'] == PS_PAYING);
}

/**
 * 取得訂單商品
 * @param   int     $order_id   訂單id
 * @return  array   訂單商品數組
 */
function order_goods($order_id)
{
    $sql = "SELECT rec_id, goods_id, goods_name, goods_sn, market_price, goods_number, " .
            "goods_price, goods_attr, is_real, parent_id, is_gift, " .
            "goods_price * goods_number AS subtotal, extension_code " .
            "FROM " . $GLOBALS['ecs']->table('order_goods') .
            " WHERE order_id = '$order_id'";

    $res = $GLOBALS['db']->query($sql);

    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        if ($row['extension_code'] == 'package_buy')
        {
            $row['package_goods_list'] = get_package_goods($row['goods_id']);
        }
        $goods_list[] = $row;
    }

    //return $GLOBALS['db']->getAll($sql);
    return $goods_list;
}

/**
 * 取得訂單總金額
 * @param   int     $order_id   訂單id
 * @param   bool    $include_gift   是否包括贈品
 * @return  float   訂單總金額
 */
function order_amount($order_id, $include_gift = true)
{
    $sql = "SELECT SUM(goods_price * goods_number) " .
            "FROM " . $GLOBALS['ecs']->table('order_goods') .
            " WHERE order_id = '$order_id'";
    if (!$include_gift)
    {
        $sql .= " AND is_gift = 0";
    }

    return floatval($GLOBALS['db']->getOne($sql));
}

/**
 * 取得某訂單商品總重量和總金額（對應 cart_weight_price）
 * @param   int     $order_id   訂單id
 * @return  array   ('weight' => **, 'amount' => **, 'formated_weight' => **)
 */
function order_weight_price($order_id)
{
    $sql = "SELECT SUM(g.goods_weight * o.goods_number) AS weight, " .
                "SUM(o.goods_price * o.goods_number) AS amount ," .
                "SUM(o.goods_number) AS number " .
            "FROM " . $GLOBALS['ecs']->table('order_goods') . " AS o, " .
                $GLOBALS['ecs']->table('goods') . " AS g " .
            "WHERE o.order_id = '$order_id' " .
            "AND o.goods_id = g.goods_id";

    $row = $GLOBALS['db']->getRow($sql);
    $row['weight'] = floatval($row['weight']);
    $row['amount'] = floatval($row['amount']);
    $row['number'] = intval($row['number']);

    /* 格式化重量 */
    $row['formated_weight'] = formated_weight($row['weight']);

    return $row;
}

/**
 * 獲得訂單中的費用信息
 *
 * @access  public
 * @param   array   $order
 * @param   array   $goods
 * @param   array   $consignee
 * @param   bool    $is_gb_deposit  是否團購保證金（如果是，應付款金額只計算商品總額和支付費用，可以獲得的積分取 $gift_integral）
 * @return  array
 */
function order_fee($order, $goods, $consignee)
{
    /* 初始化訂單的擴展code */
    if (!isset($order['extension_code']))
    {
        $order['extension_code'] = '';
    }

    if ($order['extension_code'] == 'group_buy')
    {
        $group_buy = group_buy_info($order['extension_id']);
    }

    $total  = array('real_goods_count' => 0,
                    'gift_amount'      => 0,
                    'goods_price'      => 0,
                    'market_price'     => 0,
                    'discount'         => 0,
                    'pack_fee'         => 0,
                    'card_fee'         => 0,
                    'shipping_fee'     => 0,
                    'shipping_insure'  => 0,
                    'integral_money'   => 0,
                    'bonus'            => 0,
                    'surplus'          => 0,
                    'cod_fee'          => 0,
                    'pay_fee'          => 0,
                    'tax'              => 0);
    $weight = 0;

    /* 商品總價 */
    foreach ($goods AS $val)
    {
        /* 統計實體商品的個數 */
        if ($val['is_real'])
        {
            $total['real_goods_count']++;
        }

        $total['goods_price']  += $val['goods_price'] * $val['goods_number'];
        $total['market_price'] += $val['market_price'] * $val['goods_number'];
    }

    $total['saving']    = $total['market_price'] - $total['goods_price'];
    $total['save_rate'] = $total['market_price'] ? round($total['saving'] * 100 / $total['market_price']) . '%' : 0;

    $total['goods_price_formated']  = price_format($total['goods_price'], false);
    $total['market_price_formated'] = price_format($total['market_price'], false);
    $total['saving_formated']       = price_format($total['saving'], false);

    /* 折扣 */
    if ($order['extension_code'] != 'group_buy')
    {
        $discount = compute_discount();
        $total['discount'] = $discount['discount'];
        if ($total['discount'] > $total['goods_price'])
        {
            $total['discount'] = $total['goods_price'];
        }
    }
    $total['discount_formated'] = price_format($total['discount'], false);

    /* 稅額 */
    if (!empty($order['need_inv']) && $order['inv_type'] != '')
    {
        /* 查稅率 */
        $rate = 0;
        foreach ($GLOBALS['_CFG']['invoice_type']['type'] as $key => $type)
        {
            if ($type == $order['inv_type'])
            {
                $rate = floatval($GLOBALS['_CFG']['invoice_type']['rate'][$key]) / 100;
                break;
            }
        }
        if ($rate > 0)
        {
            $total['tax'] = $rate * $total['goods_price'];
        }
    }
    $total['tax_formated'] = price_format($total['tax'], false);

    /* 包裝費用 */
    if (!empty($order['pack_id']))
    {
        $total['pack_fee']      = pack_fee($order['pack_id'], $total['goods_price']);
    }
    $total['pack_fee_formated'] = price_format($total['pack_fee'], false);

    /* 賀卡費用 */
    if (!empty($order['card_id']))
    {
        $total['card_fee']      = card_fee($order['card_id'], $total['goods_price']);
    }
    $total['card_fee_formated'] = price_format($total['card_fee'], false);

    /* 紅包 */

    if (!empty($order['bonus_id']))
    {
        $bonus          = bonus_info($order['bonus_id']);
        $total['bonus'] = $bonus['type_money'];
    }
    $total['bonus_formated'] = price_format($total['bonus'], false);

    /* 線下紅包 */
     if (!empty($order['bonus_kill']))
    {
        $bonus          = bonus_info(0,$order['bonus_kill']);
        $total['bonus_kill'] = $order['bonus_kill'];
        $total['bonus_kill_formated'] = price_format($total['bonus_kill'], false);
    }



    /* 配送費用 */
    $shipping_cod_fee = NULL;

    if ($order['shipping_id'] > 0 && $total['real_goods_count'] > 0)
    {
        $region['country']  = $consignee['country'];
        $region['province'] = $consignee['province'];
        $region['city']     = $consignee['city'];
        $region['district'] = $consignee['district'];
        $shipping_info = shipping_area_info($order['shipping_id'], $region);

        if (!empty($shipping_info))
        {
            if ($order['extension_code'] == 'group_buy')
            {
                $weight_price = cart_weight_price(CART_GROUP_BUY_GOODS);
            }
            else
            {
                $weight_price = cart_weight_price();
            }

            // 查看購物車中是否全為免運費商品，若是則把運費賦為零
            $sql = 'SELECT count(*) FROM ' . $GLOBALS['ecs']->table('cart') . " WHERE  `session_id` = '" . SESS_ID. "' AND `extension_code` != 'package_buy' AND `is_shipping` = 0";
            $shipping_count = $GLOBALS['db']->getOne($sql);

            $total['shipping_fee'] = ($shipping_count == 0 AND $weight_price['free_shipping'] == 1) ?0 :  shipping_fee($shipping_info['shipping_code'],$shipping_info['configure'], $weight_price['weight'], $total['goods_price'], $weight_price['number']);

            if (!empty($order['need_insure']) && $shipping_info['insure'] > 0)
            {
                $total['shipping_insure'] = shipping_insure_fee($shipping_info['shipping_code'],
                    $total['goods_price'], $shipping_info['insure']);
            }
            else
            {
                $total['shipping_insure'] = 0;
            }

            if ($shipping_info['support_cod'])
            {
                $shipping_cod_fee = $shipping_info['pay_fee'];
            }
        }
    }

    $total['shipping_fee_formated']    = price_format($total['shipping_fee'], false);
    $total['shipping_insure_formated'] = price_format($total['shipping_insure'], false);

    // 購物車中的商品能享受紅包支付的總額
    $bonus_amount = compute_discount_amount();
    // 紅包和積分最多能支付的金額為商品總額
    $max_amount = $total['goods_price'] == 0 ? $total['goods_price'] : $total['goods_price'] - $bonus_amount;

    /* 計算訂單總額 */
    if ($order['extension_code'] == 'group_buy' && $group_buy['deposit'] > 0)
    {
        $total['amount'] = $total['goods_price'];
    }
    else
    {
        $total['amount'] = $total['goods_price'] - $total['discount'] + $total['tax'] + $total['pack_fee'] + $total['card_fee'] +
            $total['shipping_fee'] + $total['shipping_insure'] + $total['cod_fee'];

        // 減去紅包金額
        $use_bonus        = min($total['bonus'], $max_amount); // 實際減去的紅包金額
        if(isset($total['bonus_kill']))
        {
            $use_bonus_kill   = min($total['bonus_kill'], $max_amount);
            $total['amount'] -=  $price = number_format($total['bonus_kill'], 2, '.', ''); // 還需要支付的訂單金額
        }

        $total['bonus']   = $use_bonus;
        $total['bonus_formated'] = price_format($total['bonus'], false);

        $total['amount'] -= $use_bonus; // 還需要支付的訂單金額
        $max_amount      -= $use_bonus; // 積分最多還能支付的金額

    }

    /* 余額 */
    $order['surplus'] = $order['surplus'] > 0 ? $order['surplus'] : 0;
    if ($total['amount'] > 0)
    {
        if (isset($order['surplus']) && $order['surplus'] > $total['amount'])
        {
            $order['surplus'] = $total['amount'];
            $total['amount']  = 0;
        }
        else
        {
            $total['amount'] -= floatval($order['surplus']);
        }
    }
    else
    {
        $order['surplus'] = 0;
        $total['amount']  = 0;
    }
    $total['surplus'] = $order['surplus'];
    $total['surplus_formated'] = price_format($order['surplus'], false);

    /* 積分 */
    $order['integral'] = $order['integral'] > 0 ? $order['integral'] : 0;
    if ($total['amount'] > 0 && $max_amount > 0 && $order['integral'] > 0)
    {
        $integral_money = value_of_integral($order['integral']);

        // 使用積分支付
        $use_integral            = min($total['amount'], $max_amount, $integral_money); // 實際使用積分支付的金額
        $total['amount']        -= $use_integral;
        $total['integral_money'] = $use_integral;
        $order['integral']       = integral_of_value($use_integral);
    }
    else
    {
        $total['integral_money'] = 0;
        $order['integral']       = 0;
    }
    $total['integral'] = $order['integral'];
    $total['integral_formated'] = price_format($total['integral_money'], false);

    /* 保存訂單信息 */
    $_SESSION['flow_order'] = $order;

    $se_flow_type = isset($_SESSION['flow_type']) ? $_SESSION['flow_type'] : '';
    
    /* 支付費用 */
    if (!empty($order['pay_id']) && ($total['real_goods_count'] > 0 || $se_flow_type != CART_EXCHANGE_GOODS))
    {
        $total['pay_fee']      = pay_fee($order['pay_id'], $total['amount'], $shipping_cod_fee);
    }

    $total['pay_fee_formated'] = price_format($total['pay_fee'], false);

    $total['amount']           += $total['pay_fee']; // 訂單總額累加上支付費用
    $total['amount_formated']  = price_format($total['amount'], false);

    /* 取得可以得到的積分和紅包 */
    if ($order['extension_code'] == 'group_buy')
    {
        $total['will_get_integral'] = $group_buy['gift_integral'];
    }
    elseif ($order['extension_code'] == 'exchange_goods')
    {
        $total['will_get_integral'] = 0;
    }
    else
    {
        $total['will_get_integral'] = get_give_integral($goods);
    }
    $total['will_get_bonus']        = $order['extension_code'] == 'exchange_goods' ? 0 : price_format(get_total_bonus(), false);
    $total['formated_goods_price']  = price_format($total['goods_price'], false);
    $total['formated_market_price'] = price_format($total['market_price'], false);
    $total['formated_saving']       = price_format($total['saving'], false);

    if ($order['extension_code'] == 'exchange_goods')
    {
        $sql = 'SELECT SUM(eg.exchange_integral) '.
               'FROM ' . $GLOBALS['ecs']->table('cart') . ' AS c,' . $GLOBALS['ecs']->table('exchange_goods') . 'AS eg '.
               "WHERE c.goods_id = eg.goods_id AND c.session_id= '" . SESS_ID . "' " .
               "  AND c.rec_type = '" . CART_EXCHANGE_GOODS . "' " .
               '  AND c.is_gift = 0 AND c.goods_id > 0 ' .
               'GROUP BY eg.goods_id';
        $exchange_integral = $GLOBALS['db']->getOne($sql);
        $total['exchange_integral'] = $exchange_integral;
    }

    return $total;
}

/**
 * 修改訂單
 * @param   int     $order_id   訂單id
 * @param   array   $order      key => value
 * @return  bool
 */
function update_order($order_id, $order)
{
    return $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('order_info'),
        $order, 'UPDATE', "order_id = '$order_id'");
}

/**
 * 得到新訂單號
 * @return  string
 */
function get_order_sn()
{
    /* 選擇一個隨機的方案 */
    mt_srand((double) microtime() * 1000000);

    return date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
}

/**
 * 取得購物車商品
 * @param   int     $type   類型：默認普通商品
 * @return  array   購物車商品數組
 */
function cart_goods($type = CART_GENERAL_GOODS)
{
    $sql = "SELECT rec_id, user_id, goods_id, goods_name, goods_sn, goods_number, " .
            "market_price, goods_price, goods_attr, is_real, extension_code, parent_id, is_gift, is_shipping, " .
            "goods_price * goods_number AS subtotal " .
            "FROM " . $GLOBALS['ecs']->table('cart') .
            " WHERE session_id = '" . SESS_ID . "' " .
            "AND rec_type = '$type'";

    $arr = $GLOBALS['db']->getAll($sql);

    /* 格式化價格及禮包商品 */
    foreach ($arr as $key => $value)
    {
        $arr[$key]['formated_market_price'] = price_format($value['market_price'], false);
        $arr[$key]['formated_goods_price']  = price_format($value['goods_price'], false);
        $arr[$key]['formated_subtotal']     = price_format($value['subtotal'], false);

        if ($value['extension_code'] == 'package_buy')
        {
            $arr[$key]['package_goods_list'] = get_package_goods($value['goods_id']);
        }
    }

    return $arr;
}

/**
 * 取得購物車總金額
 * @params  boolean $include_gift   是否包括贈品
 * @param   int     $type           類型：默認普通商品
 * @return  float   購物車總金額
 */
function cart_amount($include_gift = true, $type = CART_GENERAL_GOODS)
{
    $sql = "SELECT SUM(goods_price * goods_number) " .
            " FROM " . $GLOBALS['ecs']->table('cart') .
            " WHERE session_id = '" . SESS_ID . "' " .
            "AND rec_type = '$type' ";

    if (!$include_gift)
    {
        $sql .= ' AND is_gift = 0 AND goods_id > 0';
    }

    return floatval($GLOBALS['db']->getOne($sql));
}

/**
 * 檢查某商品是否已經存在於購物車
 *
 * @access  public
 * @param   integer     $id
 * @param   array       $spec
 * @param   int         $type   類型：默認普通商品
 * @return  boolean
 */
function cart_goods_exists($id, $spec, $type = CART_GENERAL_GOODS)
{
    /* 檢查該商品是否已經存在在購物車中 */
    $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('cart').
            "WHERE session_id = '" .SESS_ID. "' AND goods_id = '$id' ".
            "AND parent_id = 0 AND goods_attr = '" .get_goods_attr_info($spec). "' " .
            "AND rec_type = '$type'";

    return ($GLOBALS['db']->getOne($sql) > 0);
}

/**
 * 獲得購物車中商品的總重量、總價格、總數量
 *
 * @access  public
 * @param   int     $type   類型：默認普通商品
 * @return  array
 */
function cart_weight_price($type = CART_GENERAL_GOODS)
{
    $package_row['weight'] = 0;
    $package_row['amount'] = 0;
    $package_row['number'] = 0;

    $packages_row['free_shipping'] = 1;

    /* 計算超值禮包內商品的相關配送參數 */
    $sql = 'SELECT goods_id, goods_number, goods_price FROM ' . $GLOBALS['ecs']->table('cart') . " WHERE extension_code = 'package_buy' AND session_id = '" . SESS_ID . "'";
    $row = $GLOBALS['db']->getAll($sql);

    if ($row)
    {
        $packages_row['free_shipping'] = 0;
        $free_shipping_count = 0;

        foreach ($row as $val)
        {
            // 如果商品全為免運費商品，設置一個標識變量
            $sql = 'SELECT count(*) FROM ' .
                    $GLOBALS['ecs']->table('package_goods') . ' AS pg, ' .
                    $GLOBALS['ecs']->table('goods') . ' AS g ' .
                    "WHERE g.goods_id = pg.goods_id AND g.is_shipping = 0 AND pg.package_id = '"  . $val['goods_id'] . "'";
            $shipping_count = $GLOBALS['db']->getOne($sql);

            if ($shipping_count > 0)
            {
                // 循環計算每個超值禮包商品的重量和數量，注意一個禮包中可能包換若干個同一商品
                $sql = 'SELECT SUM(g.goods_weight * pg.goods_number) AS weight, ' .
                    'SUM(pg.goods_number) AS number FROM ' .
                    $GLOBALS['ecs']->table('package_goods') . ' AS pg, ' .
                    $GLOBALS['ecs']->table('goods') . ' AS g ' .
                    "WHERE g.goods_id = pg.goods_id AND g.is_shipping = 0 AND pg.package_id = '"  . $val['goods_id'] . "'";

                $goods_row = $GLOBALS['db']->getRow($sql);
                $package_row['weight'] += floatval($goods_row['weight']) * $val['goods_number'];
                $package_row['amount'] += floatval($val['goods_price']) * $val['goods_number'];
                $package_row['number'] += intval($goods_row['number']) * $val['goods_number'];
            }
            else
            {
                $free_shipping_count++;
            }
        }

        $packages_row['free_shipping'] = $free_shipping_count == count($row) ? 1 : 0;
    }

    /* 獲得購物車中非超值禮包商品的總重量 */
    $sql    = 'SELECT SUM(g.goods_weight * c.goods_number) AS weight, ' .
                    'SUM(c.goods_price * c.goods_number) AS amount, ' .
                    'SUM(c.goods_number) AS number '.
                'FROM ' . $GLOBALS['ecs']->table('cart') . ' AS c '.
                'LEFT JOIN ' . $GLOBALS['ecs']->table('goods') . ' AS g ON g.goods_id = c.goods_id '.
                "WHERE c.session_id = '" . SESS_ID . "' " .
                "AND rec_type = '$type' AND g.is_shipping = 0 AND c.extension_code != 'package_buy'";
    $row = $GLOBALS['db']->getRow($sql);

    $packages_row['weight'] = floatval($row['weight']) + $package_row['weight'];
    $packages_row['amount'] = floatval($row['amount']) + $package_row['amount'];
    $packages_row['number'] = intval($row['number']) + $package_row['number'];
    /* 格式化重量 */
    $packages_row['formated_weight'] = formated_weight($packages_row['weight']);

    return $packages_row;
}

/**
 * 添加商品到購物車
 *
 * @access  public
 * @param   integer $goods_id   商品編號
 * @param   integer $num        商品數量
 * @param   array   $spec       規格值對應的id數組
 * @param   integer $parent     基本件
 * @return  boolean
 */
function addto_cart($goods_id, $num = 1, $spec = array(), $parent = 0)
{
    $GLOBALS['err']->clean();
    $_parent_id = $parent;

    /* 取得商品信息 */
    $sql = "SELECT g.goods_name, g.goods_sn, g.is_on_sale, g.is_real, ".
                "g.market_price, g.shop_price AS org_price, g.promote_price, g.promote_start_date, ".
                "g.promote_end_date, g.goods_weight, g.integral, g.extension_code, ".
                "g.goods_number, g.is_alone_sale, g.is_shipping,".
                "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price ".
            " FROM " .$GLOBALS['ecs']->table('goods'). " AS g ".
            " LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp ".
                    "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' ".
            " WHERE g.goods_id = '$goods_id'" .
            " AND g.is_delete = 0";
    $goods = $GLOBALS['db']->getRow($sql);

    if (empty($goods))
    {
        $GLOBALS['err']->add($GLOBALS['_LANG']['goods_not_exists'], ERR_NOT_EXISTS);

        return false;
    }

    /* 如果是作為配件添加到購物車的，需要先檢查購物車裡面是否已經有基本件 */
    if ($parent > 0)
    {
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('cart') .
                " WHERE goods_id='$parent' AND session_id='" . SESS_ID . "' AND extension_code <> 'package_buy'";
        if ($GLOBALS['db']->getOne($sql) == 0)
        {
            $GLOBALS['err']->add($GLOBALS['_LANG']['no_basic_goods'], ERR_NO_BASIC_GOODS);

            return false;
        }
    }

    /* 是否正在銷售 */
    if ($goods['is_on_sale'] == 0)
    {
        $GLOBALS['err']->add($GLOBALS['_LANG']['not_on_sale'], ERR_NOT_ON_SALE);

        return false;
    }

    /* 不是配件時檢查是否允許單獨銷售 */
    if (empty($parent) && $goods['is_alone_sale'] == 0)
    {
        $GLOBALS['err']->add($GLOBALS['_LANG']['cannt_alone_sale'], ERR_CANNT_ALONE_SALE);

        return false;
    }

    /* 如果商品有規格則取規格商品信息 配件除外 */
    $sql = "SELECT * FROM " .$GLOBALS['ecs']->table('products'). " WHERE goods_id = '$goods_id' LIMIT 0, 1";
    $prod = $GLOBALS['db']->getRow($sql);

    if (is_spec($spec) && !empty($prod))
    {
        $product_info = get_products_info($goods_id, $spec);
    }
    if (empty($product_info))
    {
        $product_info = array('product_number' => '', 'product_id' => 0);
    }

    /* 檢查：庫存 */
    if ($GLOBALS['_CFG']['use_storage'] == 1)
    {
        //檢查：商品購買數量是否大於總庫存
        if ($num > $goods['goods_number'])
        {
            $GLOBALS['err']->add(sprintf($GLOBALS['_LANG']['shortage'], $goods['goods_number']), ERR_OUT_OF_STOCK);

            return false;
        }

        //商品存在規格 是貨品 檢查該貨品庫存
        if (is_spec($spec) && !empty($prod))
        {
            if (!empty($spec))
            {
                /* 取規格的貨品庫存 */
                if ($num > $product_info['product_number'])
                {
                    $GLOBALS['err']->add(sprintf($GLOBALS['_LANG']['shortage'], $product_info['product_number']), ERR_OUT_OF_STOCK);
    
                    return false;
                }
            }
        }       
    }

    /* 計算商品的促銷價格 */
    $spec_price             = spec_price($spec);
    $goods_price            = get_final_price($goods_id, $num, true, $spec);
    $goods['market_price'] += $spec_price;
    $goods_attr             = get_goods_attr_info($spec);
    $goods_attr_id          = join(',', $spec);

    /* 初始化要插入購物車的基本件數據 */
    $parent = array(
        'user_id'       => $_SESSION['user_id'],
        'session_id'    => SESS_ID,
        'goods_id'      => $goods_id,
        'goods_sn'      => addslashes($goods['goods_sn']),
        'product_id'    => $product_info['product_id'],
        'goods_name'    => addslashes($goods['goods_name']),
        'market_price'  => $goods['market_price'],
        'goods_attr'    => addslashes($goods_attr),
        'goods_attr_id' => $goods_attr_id,
        'is_real'       => $goods['is_real'],
        'extension_code'=> $goods['extension_code'],
        'is_gift'       => 0,
        'is_shipping'   => $goods['is_shipping'],
        'rec_type'      => CART_GENERAL_GOODS
    );

    /* 如果該配件在添加為基本件的配件時，所設置的“配件價格”比原價低，即此配件在價格上提供了優惠， */
    /* 則按照該配件的優惠價格賣，但是每一個基本件只能購買一個優惠價格的“該配件”，多買的“該配件”不享 */
    /* 受此優惠 */
    $basic_list = array();
    $sql = "SELECT parent_id, goods_price " .
            "FROM " . $GLOBALS['ecs']->table('group_goods') .
            " WHERE goods_id = '$goods_id'" .
            " AND goods_price < '$goods_price'" .
            " AND parent_id = '$_parent_id'" .
            " ORDER BY goods_price";
    $res = $GLOBALS['db']->query($sql);
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $basic_list[$row['parent_id']] = $row['goods_price'];
    }

    /* 取得購物車中該商品每個基本件的數量 */
    $basic_count_list = array();
    if ($basic_list)
    {
        $sql = "SELECT goods_id, SUM(goods_number) AS count " .
                "FROM " . $GLOBALS['ecs']->table('cart') .
                " WHERE session_id = '" . SESS_ID . "'" .
                " AND parent_id = 0" .
                " AND extension_code <> 'package_buy' " .
                " AND goods_id " . db_create_in(array_keys($basic_list)) .
                " GROUP BY goods_id";
        $res = $GLOBALS['db']->query($sql);
        while ($row = $GLOBALS['db']->fetchRow($res))
        {
            $basic_count_list[$row['goods_id']] = $row['count'];
        }
    }

    /* 取得購物車中該商品每個基本件已有該商品配件數量，計算出每個基本件還能有幾個該商品配件 */
    /* 一個基本件對應一個該商品配件 */
    if ($basic_count_list)
    {
        $sql = "SELECT parent_id, SUM(goods_number) AS count " .
                "FROM " . $GLOBALS['ecs']->table('cart') .
                " WHERE session_id = '" . SESS_ID . "'" .
                " AND goods_id = '$goods_id'" .
                " AND extension_code <> 'package_buy' " .
                " AND parent_id " . db_create_in(array_keys($basic_count_list)) .
                " GROUP BY parent_id";
        $res = $GLOBALS['db']->query($sql);
        while ($row = $GLOBALS['db']->fetchRow($res))
        {
            $basic_count_list[$row['parent_id']] -= $row['count'];
        }
    }

    /* 循環插入配件 如果是配件則用其添加數量依次為購物車中所有屬於其的基本件添加足夠數量的該配件 */
    foreach ($basic_list as $parent_id => $fitting_price)
    {
        /* 如果已全部插入，退出 */
        if ($num <= 0)
        {
            break;
        }

        /* 如果該基本件不再購物車中，執行下一個 */
        if (!isset($basic_count_list[$parent_id]))
        {
            continue;
        }

        /* 如果該基本件的配件數量已滿，執行下一個基本件 */
        if ($basic_count_list[$parent_id] <= 0)
        {
            continue;
        }

        /* 作為該基本件的配件插入 */
        $parent['goods_price']  = max($fitting_price, 0) + $spec_price; //允許該配件優惠價格為0
        $parent['goods_number'] = min($num, $basic_count_list[$parent_id]);
        $parent['parent_id']    = $parent_id;

        /* 添加 */
        $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('cart'), $parent, 'INSERT');

        /* 改變數量 */
        $num -= $parent['goods_number'];
    }

    /* 如果數量不為0，作為基本件插入 */
    if ($num > 0)
    {
        /* 檢查該商品是否已經存在在購物車中 */
        $sql = "SELECT goods_number FROM " .$GLOBALS['ecs']->table('cart').
                " WHERE session_id = '" .SESS_ID. "' AND goods_id = '$goods_id' ".
                " AND parent_id = 0 AND goods_attr = '" .get_goods_attr_info($spec). "' " .
                " AND extension_code <> 'package_buy' " .
                " AND rec_type = 'CART_GENERAL_GOODS'";

        $row = $GLOBALS['db']->getRow($sql);

        if($row) //如果購物車已經有此物品，則更新
        {
            $num += $row['goods_number'];
            if ($GLOBALS['_CFG']['use_storage'] == 0 || $num <= $goods['goods_number'])
            {
                $goods_price = get_final_price($goods_id, $num, true, $spec);
                $sql = "UPDATE " . $GLOBALS['ecs']->table('cart') . " SET goods_number = '$num'" .
                       " , goods_price = '$goods_price'".
                       " WHERE session_id = '" .SESS_ID. "' AND goods_id = '$goods_id' ".
                       " AND parent_id = 0 AND goods_attr = '" .get_goods_attr_info($spec). "' " .
                       " AND extension_code <> 'package_buy' " .
                       "AND rec_type = 'CART_GENERAL_GOODS'";
                $GLOBALS['db']->query($sql);
            }
            else
            {
               $GLOBALS['err']->add(sprintf($GLOBALS['_LANG']['shortage'], $num), ERR_OUT_OF_STOCK);

                return false;
            }
        }
        else //購物車沒有此物品，則插入
        {
            $goods_price = get_final_price($goods_id, $num, true, $spec);
            $parent['goods_price']  = max($goods_price, 0);
            $parent['goods_number'] = $num;
            $parent['parent_id']    = 0;
            $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('cart'), $parent, 'INSERT');
        }
    }

    /* 把贈品刪除 */
    $sql = "DELETE FROM " . $GLOBALS['ecs']->table('cart') . " WHERE session_id = '" . SESS_ID . "' AND is_gift <> 0";
    $GLOBALS['db']->query($sql);

    return true;
}

/**
 * 清空購物車
 * @param   int     $type   類型：默認普通商品
 */
function clear_cart($type = CART_GENERAL_GOODS)
{
    $sql = "DELETE FROM " . $GLOBALS['ecs']->table('cart') .
            " WHERE session_id = '" . SESS_ID . "' AND rec_type = '$type'";
    $GLOBALS['db']->query($sql);
}

/**
 * 獲得指定的商品屬性
 *
 * @access      public
 * @param       array       $arr        規格、屬性ID數組
 * @param       type        $type       設置返回結果類型：pice，顯示價格，默認；no，不顯示價格
 *
 * @return      string
 */
function get_goods_attr_info($arr, $type = 'pice')
{
    $attr   = '';

    if (!empty($arr))
    {
        $fmt = "%s:%s[%s] \n";

        $sql = "SELECT a.attr_name, ga.attr_value, ga.attr_price ".
                "FROM ".$GLOBALS['ecs']->table('goods_attr')." AS ga, ".
                    $GLOBALS['ecs']->table('attribute')." AS a ".
                "WHERE " .db_create_in($arr, 'ga.goods_attr_id')." AND a.attr_id = ga.attr_id";
        $res = $GLOBALS['db']->query($sql);

        while ($row = $GLOBALS['db']->fetchRow($res))
        {
            $attr_price = round(floatval($row['attr_price']), 2);
            $attr .= sprintf($fmt, $row['attr_name'], $row['attr_value'], $attr_price);
        }

        $attr = str_replace('[0]', '', $attr);
    }

    return $attr;
}

/**
 * 取得用戶信息
 * @param   int     $user_id    用戶id
 * @return  array   用戶信息
 */
function user_info($user_id)
{
    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('users') .
            " WHERE user_id = '$user_id'";
    $user = $GLOBALS['db']->getRow($sql);

    unset($user['question']);
    unset($user['answer']);

    /* 格式化帳戶余額 */
    if ($user)
    {
//        if ($user['user_money'] < 0)
//        {
//            $user['user_money'] = 0;
//        }
        $user['formated_user_money'] = price_format($user['user_money'], false);
        $user['formated_frozen_money'] = price_format($user['frozen_money'], false);
    }

    return $user;
}

/**
 * 修改用戶
 * @param   int     $user_id   訂單id
 * @param   array   $user      key => value
 * @return  bool
 */
function update_user($user_id, $user)
{
    return $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('users'),
        $user, 'UPDATE', "user_id = '$user_id'");
}

/**
 * 取得用戶地址列表
 * @param   int     $user_id    用戶id
 * @return  array
 */
function address_list($user_id)
{
    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('user_address') .
            " WHERE user_id = '$user_id'";

    return $GLOBALS['db']->getAll($sql);
}

/**
 * 取得用戶地址信息
 * @param   int     $address_id     地址id
 * @return  array
 */
function address_info($address_id)
{
    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('user_address') .
            " WHERE address_id = '$address_id'";

    return $GLOBALS['db']->getRow($sql);
}

/**
 * 取得用戶當前可用紅包
 * @param   int     $user_id        用戶id
 * @param   float   $goods_amount   訂單商品金額
 * @return  array   紅包數組
 */
function user_bonus($user_id, $goods_amount = 0)
{
    $day    = getdate();
    $today  = local_mktime(23, 59, 59, $day['mon'], $day['mday'], $day['year']);

    $sql = "SELECT t.type_id, t.type_name, t.type_money, b.bonus_id " .
            "FROM " . $GLOBALS['ecs']->table('bonus_type') . " AS t," .
                $GLOBALS['ecs']->table('user_bonus') . " AS b " .
            "WHERE t.type_id = b.bonus_type_id " .
            "AND t.use_start_date <= '$today' " .
            "AND t.use_end_date >= '$today' " .
            "AND t.min_goods_amount <= '$goods_amount' " .
            "AND b.user_id<>0 " .
            "AND b.user_id = '$user_id' " .
            "AND b.order_id = 0";
    return $GLOBALS['db']->getAll($sql);
}

/**
 * 取得紅包信息
 * @param   int     $bonus_id   紅包id
 * @param   string  $bonus_sn   紅包序列號
 * @param   array   紅包信息
 */
function bonus_info($bonus_id, $bonus_sn = '')
{
    $sql = "SELECT t.*, b.* " .
            "FROM " . $GLOBALS['ecs']->table('bonus_type') . " AS t," .
                $GLOBALS['ecs']->table('user_bonus') . " AS b " .
            "WHERE t.type_id = b.bonus_type_id ";
    if ($bonus_id > 0)
    {
        $sql .= "AND b.bonus_id = '$bonus_id'";
    }
    else
    {
        $sql .= "AND b.bonus_sn = '$bonus_sn'";
    }

    return $GLOBALS['db']->getRow($sql);
}

/**
 * 檢查紅包是否已使用
 * @param   int $bonus_id   紅包id
 * @return  bool
 */
function bonus_used($bonus_id)
{
    $sql = "SELECT order_id FROM " . $GLOBALS['ecs']->table('user_bonus') .
            " WHERE bonus_id = '$bonus_id'";

    return  $GLOBALS['db']->getOne($sql) > 0;
}

/**
 * 設置紅包為已使用
 * @param   int     $bonus_id   紅包id
 * @param   int     $order_id   訂單id
 * @return  bool
 */
function use_bonus($bonus_id, $order_id)
{
    $sql = "UPDATE " . $GLOBALS['ecs']->table('user_bonus') .
            " SET order_id = '$order_id', used_time = '" . gmtime() . "' " .
            "WHERE bonus_id = '$bonus_id' LIMIT 1";

    return  $GLOBALS['db']->query($sql);
}

/**
 * 設置紅包為未使用
 * @param   int     $bonus_id   紅包id
 * @param   int     $order_id   訂單id
 * @return  bool
 */
function unuse_bonus($bonus_id)
{
    $sql = "UPDATE " . $GLOBALS['ecs']->table('user_bonus') .
            " SET order_id = 0, used_time = 0 " .
            "WHERE bonus_id = '$bonus_id' LIMIT 1";

    return  $GLOBALS['db']->query($sql);
}

/**
 * 計算積分的價值（能抵多少錢）
 * @param   int     $integral   積分
 * @return  float   積分價值
 */
function value_of_integral($integral)
{
    $scale = floatval($GLOBALS['_CFG']['integral_scale']);

    return $scale > 0 ? round(($integral / 100) * $scale, 2) : 0;
}

/**
 * 計算指定的金額需要多少積分
 *
 * @access  public
 * @param   integer $value  金額
 * @return  void
 */
function integral_of_value($value)
{
    $scale = floatval($GLOBALS['_CFG']['integral_scale']);

    return $scale > 0 ? round($value / $scale * 100) : 0;
}

/**
 * 訂單退款
 * @param   array   $order          訂單
 * @param   int     $refund_type    退款方式 1 到帳戶余額 2 到退款申請（先到余額，再申請提款） 3 不處理
 * @param   string  $refund_note    退款說明
 * @param   float   $refund_amount  退款金額（如果為0，取訂單已付款金額）
 * @return  bool
 */
function order_refund($order, $refund_type, $refund_note, $refund_amount = 0)
{
    /* 檢查參數 */
    $user_id = $order['user_id'];
    if ($user_id == 0 && $refund_type == 1)
    {
        die('anonymous, cannot return to account balance');
    }

    $amount = $refund_amount > 0 ? $refund_amount : $order['money_paid'];
    if ($amount <= 0)
    {
        return true;
    }

    if (!in_array($refund_type, array(1, 2, 3)))
    {
        die('invalid params');
    }

    /* 備註信息 */
    if ($refund_note)
    {
        $change_desc = $refund_note;
    }
    else
    {
        include_once(ROOT_PATH . 'languages/' .$GLOBALS['_CFG']['lang']. '/admin/order.php');
        $change_desc = sprintf($GLOBALS['_LANG']['order_refund'], $order['order_sn']);
    }

    /* 處理退款 */
    if (1 == $refund_type)
    {
        log_account_change($user_id, $amount, 0, 0, 0, $change_desc);

        return true;
    }
    elseif (2 == $refund_type)
    {
        /* 如果非匿名，退回余額 */
        if ($user_id > 0)
        {
            log_account_change($user_id, $amount, 0, 0, 0, $change_desc);
        }

        /* user_account 表增加提款申請記錄 */
        $account = array(
            'user_id'      => $user_id,
            'amount'       => (-1) * $amount,
            'add_time'     => gmtime(),
            'user_note'    => $refund_note,
            'process_type' => SURPLUS_RETURN,
            'admin_user'   => $_SESSION['admin_name'],
            'admin_note'   => sprintf($GLOBALS['_LANG']['order_refund'], $order['order_sn']),
            'is_paid'      => 0
        );
        $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('user_account'), $account, 'INSERT');

        return true;
    }
    else
    {
        return true;
    }
}

/**
 * 獲得購物車中的商品
 *
 * @access  public
 * @return  array
 */
function get_cart_goods()
{
    /* 初始化 */
    $goods_list = array();
    $total = array(
        'goods_price'  => 0, // 本店售價合計（有格式）
        'market_price' => 0, // 市場售價合計（有格式）
        'saving'       => 0, // 節省金額（有格式）
        'save_rate'    => 0, // 節省百分比
        'goods_amount' => 0, // 本店售價合計（無格式）
    );

    /* 循環、統計 */
    $sql = "SELECT *, IF(parent_id, parent_id, goods_id) AS pid " .
            " FROM " . $GLOBALS['ecs']->table('cart') . " " .
            " WHERE session_id = '" . SESS_ID . "' AND rec_type = '" . CART_GENERAL_GOODS . "'" .
            " ORDER BY pid, parent_id";
    $res = $GLOBALS['db']->query($sql);

    /* 用於統計購物車中實體商品和虛擬商品的個數 */
    $virtual_goods_count = 0;
    $real_goods_count    = 0;

    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $total['goods_price']  += $row['goods_price'] * $row['goods_number'];
        $total['market_price'] += $row['market_price'] * $row['goods_number'];

        $row['subtotal']     = price_format($row['goods_price'] * $row['goods_number'], false);
        $row['goods_price']  = price_format($row['goods_price'], false);
        $row['market_price'] = price_format($row['market_price'], false);

        /* 統計實體商品和虛擬商品的個數 */
        if ($row['is_real'])
        {
            $real_goods_count++;
        }
        else
        {
            $virtual_goods_count++;
        }

        /* 查詢規格 */
        if (trim($row['goods_attr']) != '')
        {
            $sql = "SELECT attr_value FROM " . $GLOBALS['ecs']->table('goods_attr') . " WHERE goods_attr_id " .
            db_create_in($row['goods_attr']);
            $attr_list = $GLOBALS['db']->getCol($sql);
            foreach ($attr_list AS $attr)
            {
                $row['goods_name'] .= ' [' . $attr . '] ';
            }
        }
        /* 增加是否在購物車裡顯示商品圖 */
        if (($GLOBALS['_CFG']['show_goods_in_cart'] == "2" || $GLOBALS['_CFG']['show_goods_in_cart'] == "3") && $row['extension_code'] != 'package_buy')
        {
            $goods_thumb = $GLOBALS['db']->getOne("SELECT `goods_thumb` FROM " . $GLOBALS['ecs']->table('goods') . " WHERE `goods_id`='{$row['goods_id']}'");
            $row['goods_thumb'] = get_image_path($row['goods_id'], $goods_thumb, true);
        }
        if ($row['extension_code'] == 'package_buy')
        {
            $row['package_goods_list'] = get_package_goods($row['goods_id']);
        }
        $goods_list[] = $row;
    }
    $total['goods_amount'] = $total['goods_price'];
    $total['saving']       = price_format($total['market_price'] - $total['goods_price'], false);
    if ($total['market_price'] > 0)
    {
        $total['save_rate'] = $total['market_price'] ? round(($total['market_price'] - $total['goods_price']) *
        100 / $total['market_price']).'%' : 0;
    }
    $total['goods_price']  = price_format($total['goods_price'], false);
    $total['market_price'] = price_format($total['market_price'], false);
    $total['real_goods_count']    = $real_goods_count;
    $total['virtual_goods_count'] = $virtual_goods_count;

    return array('goods_list' => $goods_list, 'total' => $total);
}

/**
 * 取得收貨人信息
 * @param   int     $user_id    用戶編號
 * @return  array
 */
function get_consignee($user_id)
{
    if (isset($_SESSION['flow_consignee']))
    {
        /* 如果存在session，則直接返回session中的收貨人信息 */

        return $_SESSION['flow_consignee'];
    }
    else
    {
        /* 如果不存在，則取得用戶的默認收貨人信息 */
        $arr = array();

        if ($user_id > 0)
        {
            /* 取默認地址 */
            $sql = "SELECT ua.*".
                    " FROM " . $GLOBALS['ecs']->table('user_address') . "AS ua, ".$GLOBALS['ecs']->table('users').' AS u '.
                    " WHERE u.user_id='$user_id' AND ua.address_id = u.address_id";

            $arr = $GLOBALS['db']->getRow($sql);
        }

        return $arr;
    }
}

/**
 * 查詢購物車（訂單id為0）或訂單中是否有實體商品
 * @param   int     $order_id   訂單id
 * @param   int     $flow_type  購物流程類型
 * @return  bool
 */
function exist_real_goods($order_id = 0, $flow_type = CART_GENERAL_GOODS)
{
    if ($order_id <= 0)
    {
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('cart') .
                " WHERE session_id = '" . SESS_ID . "' AND is_real = 1 " .
                "AND rec_type = '$flow_type'";
    }
    else
    {
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('order_goods') .
                " WHERE order_id = '$order_id' AND is_real = 1";
    }

    return $GLOBALS['db']->getOne($sql) > 0;
}

/**
 * 檢查收貨人信息是否完整
 * @param   array   $consignee  收貨人信息
 * @param   int     $flow_type  購物流程類型
 * @return  bool    true 完整 false 不完整
 */
function check_consignee_info($consignee, $flow_type)
{
    if (exist_real_goods(0, $flow_type))
    {
        /* 如果存在實體商品 */
        $res = !empty($consignee['consignee']) &&
            !empty($consignee['country']) &&
            !empty($consignee['email']) &&
            !empty($consignee['tel']);

        if ($res)
        {
            if (empty($consignee['province']))
            {
                /* 沒有設置省份，檢查當前國家下面有沒有設置省份 */
                $pro = get_regions(1, $consignee['country']);
                $res = empty($pro);
            }
            elseif (empty($consignee['city']))
            {
                /* 沒有設置城市，檢查當前省下面有沒有城市 */
                $city = get_regions(2, $consignee['province']);
                $res = empty($city);
            }
            elseif (empty($consignee['district']))
            {
                $dist = get_regions(3, $consignee['city']);
                $res = empty($dist);
            }
        }

        return $res;
    }
    else
    {
        /* 如果不存在實體商品 */
        return !empty($consignee['consignee']) &&
            !empty($consignee['email']) &&
            !empty($consignee['tel']);
    }
}

/**
 * 獲得上一次用戶採用的支付和配送方式
 *
 * @access  public
 * @return  void
 */
function last_shipping_and_payment()
{
    $sql = "SELECT shipping_id, pay_id " .
            " FROM " . $GLOBALS['ecs']->table('order_info') .
            " WHERE user_id = '$_SESSION[user_id]' " .
            " ORDER BY order_id DESC LIMIT 1";
    $row = $GLOBALS['db']->getRow($sql);

    if (empty($row))
    {
        /* 如果獲得是一個空數組，則返回默認值 */
        $row = array('shipping_id' => 0, 'pay_id' => 0);
    }

    return $row;
}

/**
 * 取得當前用戶應該得到的紅包總額
 */
function get_total_bonus()
{
    $day    = getdate();
    $today  = local_mktime(23, 59, 59, $day['mon'], $day['mday'], $day['year']);

    /* 按商品發的紅包 */
    $sql = "SELECT SUM(c.goods_number * t.type_money)" .
            "FROM " . $GLOBALS['ecs']->table('cart') . " AS c, "
                    . $GLOBALS['ecs']->table('bonus_type') . " AS t, "
                    . $GLOBALS['ecs']->table('goods') . " AS g " .
            "WHERE c.session_id = '" . SESS_ID . "' " .
            "AND c.is_gift = 0 " .
            "AND c.goods_id = g.goods_id " .
            "AND g.bonus_type_id = t.type_id " .
            "AND t.send_type = '" . SEND_BY_GOODS . "' " .
            "AND t.send_start_date <= '$today' " .
            "AND t.send_end_date >= '$today' " .
            "AND c.rec_type = '" . CART_GENERAL_GOODS . "'";
    $goods_total = floatval($GLOBALS['db']->getOne($sql));

    /* 取得購物車中非贈品總金額 */
    $sql = "SELECT SUM(goods_price * goods_number) " .
            "FROM " . $GLOBALS['ecs']->table('cart') .
            " WHERE session_id = '" . SESS_ID . "' " .
            " AND is_gift = 0 " .
            " AND rec_type = '" . CART_GENERAL_GOODS . "'";
    $amount = floatval($GLOBALS['db']->getOne($sql));

    /* 按訂單發的紅包 */
    $sql = "SELECT FLOOR('$amount' / min_amount) * type_money " .
            "FROM " . $GLOBALS['ecs']->table('bonus_type') .
            " WHERE send_type = '" . SEND_BY_ORDER . "' " .
            " AND send_start_date <= '$today' " .
            "AND send_end_date >= '$today' " .
            "AND min_amount > 0 ";
    $order_total = floatval($GLOBALS['db']->getOne($sql));

    return $goods_total + $order_total;
}

/**
 * 處理紅包（下訂單時設為使用，取消（無效，退貨）訂單時設為未使用
 * @param   int     $bonus_id   紅包編號
 * @param   int     $order_id   訂單號
 * @param   int     $is_used    是否使用了
 */
function change_user_bonus($bonus_id, $order_id, $is_used = true)
{
    if ($is_used)
    {
        $sql = 'UPDATE ' . $GLOBALS['ecs']->table('user_bonus') . ' SET ' .
                'used_time = ' . gmtime() . ', ' .
                "order_id = '$order_id' " .
                "WHERE bonus_id = '$bonus_id'";
    }
    else
    {
        $sql = 'UPDATE ' . $GLOBALS['ecs']->table('user_bonus') . ' SET ' .
                'used_time = 0, ' .
                'order_id = 0 ' .
                "WHERE bonus_id = '$bonus_id'";
    }
    $GLOBALS['db']->query($sql);
}

/**
 * 獲得訂單信息
 *
 * @access  private
 * @return  array
 */
function flow_order_info()
{
    $order = isset($_SESSION['flow_order']) ? $_SESSION['flow_order'] : array();

    /* 初始化配送和支付方式 */
    if (!isset($order['shipping_id']) || !isset($order['pay_id']))
    {
        /* 如果還沒有設置配送和支付 */
        if ($_SESSION['user_id'] > 0)
        {
            /* 用戶已經登錄了，則獲得上次使用的配送和支付 */
            $arr = last_shipping_and_payment();

            if (!isset($order['shipping_id']))
            {
                $order['shipping_id'] = $arr['shipping_id'];
            }
            if (!isset($order['pay_id']))
            {
                $order['pay_id'] = $arr['pay_id'];
            }
        }
        else
        {
            if (!isset($order['shipping_id']))
            {
                $order['shipping_id'] = 0;
            }
            if (!isset($order['pay_id']))
            {
                $order['pay_id'] = 0;
            }
        }
    }

    if (!isset($order['pack_id']))
    {
        $order['pack_id'] = 0;  // 初始化包裝
    }
    if (!isset($order['card_id']))
    {
        $order['card_id'] = 0;  // 初始化賀卡
    }
    if (!isset($order['bonus']))
    {
        $order['bonus'] = 0;    // 初始化紅包
    }
    if (!isset($order['integral']))
    {
        $order['integral'] = 0; // 初始化積分
    }
    if (!isset($order['surplus']))
    {
        $order['surplus'] = 0;  // 初始化余額
    }

    /* 擴展信息 */
    if (isset($_SESSION['flow_type']) && intval($_SESSION['flow_type']) != CART_GENERAL_GOODS)
    {
        $order['extension_code'] = $_SESSION['extension_code'];
        $order['extension_id'] = $_SESSION['extension_id'];
    }

    return $order;
}

/**
 * 合併訂單
 * @param   string  $from_order_sn  從訂單號
 * @param   string  $to_order_sn    主訂單號
 * @return  成功返回true，失敗返回錯誤信息
 */
function merge_order($from_order_sn, $to_order_sn)
{
    /* 訂單號不能為空 */
    if (trim($from_order_sn) == '' || trim($to_order_sn) == '')
    {
        return $GLOBALS['_LANG']['order_sn_not_null'];
    }

    /* 訂單號不能相同 */
    if ($from_order_sn == $to_order_sn)
    {
        return $GLOBALS['_LANG']['two_order_sn_same'];
    }

    /* 取得訂單信息 */
    $from_order = order_info(0, $from_order_sn);
    $to_order   = order_info(0, $to_order_sn);

    /* 檢查訂單是否存在 */
    if (!$from_order)
    {
        return sprintf($GLOBALS['_LANG']['order_not_exist'], $from_order_sn);
    }
    elseif (!$to_order)
    {
        return sprintf($GLOBALS['_LANG']['order_not_exist'], $to_order_sn);
    }

    /* 檢查合併的訂單是否為普通訂單，非普通訂單不允許合併 */
    if ($from_order['extension_code'] != '' || $to_order['extension_code'] != 0)
    {
        return $GLOBALS['_LANG']['merge_invalid_order'];
    }

    /* 檢查訂單狀態是否是已確認或未確認、未付款、未發貨 */
    if ($from_order['order_status'] != OS_UNCONFIRMED && $from_order['order_status'] != OS_CONFIRMED)
    {
        return sprintf($GLOBALS['_LANG']['os_not_unconfirmed_or_confirmed'], $from_order_sn);
    }
    elseif ($from_order['pay_status'] != PS_UNPAYED)
    {
        return sprintf($GLOBALS['_LANG']['ps_not_unpayed'], $from_order_sn);
    }
    elseif ($from_order['shipping_status'] != SS_UNSHIPPED)
    {
        return sprintf($GLOBALS['_LANG']['ss_not_unshipped'], $from_order_sn);
    }

    if ($to_order['order_status'] != OS_UNCONFIRMED && $to_order['order_status'] != OS_CONFIRMED)
    {
        return sprintf($GLOBALS['_LANG']['os_not_unconfirmed_or_confirmed'], $to_order_sn);
    }
    elseif ($to_order['pay_status'] != PS_UNPAYED)
    {
        return sprintf($GLOBALS['_LANG']['ps_not_unpayed'], $to_order_sn);
    }
    elseif ($to_order['shipping_status'] != SS_UNSHIPPED)
    {
        return sprintf($GLOBALS['_LANG']['ss_not_unshipped'], $to_order_sn);
    }

    /* 檢查訂單用戶是否相同 */
    if ($from_order['user_id'] != $to_order['user_id'])
    {
        return $GLOBALS['_LANG']['order_user_not_same'];
    }

    /* 合併訂單 */
    $order = $to_order;
    $order['order_id']  = '';
    $order['add_time']  = gmtime();

    // 合併商品總額
    $order['goods_amount'] += $from_order['goods_amount'];

    // 合併折扣
    $order['discount'] += $from_order['discount'];

    if ($order['shipping_id'] > 0)
    {
        // 重新計算配送費用
        $weight_price       = order_weight_price($to_order['order_id']);
        $from_weight_price  = order_weight_price($from_order['order_id']);
        $weight_price['weight'] += $from_weight_price['weight'];
        $weight_price['amount'] += $from_weight_price['amount'];
        $weight_price['number'] += $from_weight_price['number'];

        $region_id_list = array($order['country'], $order['province'], $order['city'], $order['district']);
        $shipping_area = shipping_area_info($order['shipping_id'], $region_id_list);

        $order['shipping_fee'] = shipping_fee($shipping_area['shipping_code'],
            unserialize($shipping_area['configure']), $weight_price['weight'], $weight_price['amount'], $weight_price['number']);

        // 如果保價了，重新計算保價費
        if ($order['insure_fee'] > 0)
        {
            $order['insure_fee'] = shipping_insure_fee($shipping_area['shipping_code'], $order['goods_amount'], $shipping_area['insure']);
        }
    }

    // 重新計算包裝費、賀卡費
    if ($order['pack_id'] > 0)
    {
        $pack = pack_info($order['pack_id']);
        $order['pack_fee'] = $pack['free_money'] > $order['goods_amount'] ? $pack['pack_fee'] : 0;
    }
    if ($order['card_id'] > 0)
    {
        $card = card_info($order['card_id']);
        $order['card_fee'] = $card['free_money'] > $order['goods_amount'] ? $card['card_fee'] : 0;
    }

    // 紅包不變，合併積分、余額、已付款金額
    $order['integral']      += $from_order['integral'];
    $order['integral_money'] = value_of_integral($order['integral']);
    $order['surplus']       += $from_order['surplus'];
    $order['money_paid']    += $from_order['money_paid'];

    // 計算應付款金額（不包括支付費用）
    $order['order_amount'] = $order['goods_amount'] - $order['discount']
                           + $order['shipping_fee']
                           + $order['insure_fee']
                           + $order['pack_fee']
                           + $order['card_fee']
                           - $order['bonus']
                           - $order['integral_money']
                           - $order['surplus']
                           - $order['money_paid'];

    // 重新計算支付費
    if ($order['pay_id'] > 0)
    {
        // 貨到付款手續費
        $cod_fee          = $shipping_area ? $shipping_area['pay_fee'] : 0;
        $order['pay_fee'] = pay_fee($order['pay_id'], $order['order_amount'], $cod_fee);

        // 應付款金額加上支付費
        $order['order_amount'] += $order['pay_fee'];
    }

    /* 插入訂單表 */
    do
    {
        $order['order_sn'] = get_order_sn();
        if ($GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('order_info'), addslashes_deep($order), 'INSERT'))
        {
            break;
        }
        else
        {
            if ($GLOBALS['db']->errno() != 1062)
            {
                die($GLOBALS['db']->errorMsg());
            }
        }
    }
    while (true); // 防止訂單號重複

    /* 訂單號 */
    $order_id = $GLOBALS['db']->insert_id();

    /* 更新訂單商品 */
    $sql = 'UPDATE ' . $GLOBALS['ecs']->table('order_goods') .
            " SET order_id = '$order_id' " .
            "WHERE order_id " . db_create_in(array($from_order['order_id'], $to_order['order_id']));
    $GLOBALS['db']->query($sql);

    include_once(ROOT_PATH . 'includes/lib_clips.php');
    /* 插入支付日誌 */
    insert_pay_log($order_id, $order['order_amount'], PAY_ORDER);

    /* 刪除原訂單 */
    $sql = 'DELETE FROM ' . $GLOBALS['ecs']->table('order_info') .
            " WHERE order_id " . db_create_in(array($from_order['order_id'], $to_order['order_id']));
    $GLOBALS['db']->query($sql);

    /* 刪除原訂單支付日誌 */
    $sql = 'DELETE FROM ' . $GLOBALS['ecs']->table('pay_log') .
            " WHERE order_id " . db_create_in(array($from_order['order_id'], $to_order['order_id']));
    $GLOBALS['db']->query($sql);

    /* 返還 from_order 的紅包，因為只使用 to_order 的紅包 */
    if ($from_order['bonus_id'] > 0)
    {
        unuse_bonus($from_order['bonus_id']);
    }

    /* 返回成功 */
    return true;
}

/**
 * 查詢配送區域屬於哪個辦事處管轄
 * @param   array   $regions    配送區域（1、2、3、4級按順序）
 * @return  int     辦事處id，可能為0
 */
function get_agency_by_regions($regions)
{
    if (!is_array($regions) || empty($regions))
    {
        return 0;
    }

    $arr = array();
    $sql = "SELECT region_id, agency_id " .
            "FROM " . $GLOBALS['ecs']->table('region') .
            " WHERE region_id " . db_create_in($regions) .
            " AND region_id > 0 AND agency_id > 0";
    $res = $GLOBALS['db']->query($sql);
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $arr[$row['region_id']] = $row['agency_id'];
    }
    if (empty($arr))
    {
        return 0;
    }

    $agency_id = 0;
    for ($i = count($regions) - 1; $i >= 0; $i--)
    {
        if (isset($arr[$regions[$i]]))
        {
            return $arr[$regions[$i]];
        }
    }
}

/**
 * 獲取配送插件的實例
 * @param   int   $shipping_id    配送插件ID
 * @return  object     配送插件對象實例
 */
function &get_shipping_object($shipping_id)
{
    $shipping  = shipping_info($shipping_id);
    if (!$shipping)
    {
        $object = new stdClass();
        return $object;
    }

    $file_path = ROOT_PATH.'includes/modules/shipping/' . $shipping['shipping_code'] . '.php';

    include_once($file_path);

    $object = new $shipping['shipping_code'];
    return $object;
}

/**
 * 改變訂單中商品庫存
 * @param   int     $order_id   訂單號
 * @param   bool    $is_dec     是否減少庫存
 * @param   bool    $storage     減庫存的時機，1，下訂單時；0，發貨時；
 */
function change_order_goods_storage($order_id, $is_dec = true, $storage = 0)
{
    /* 查詢訂單商品信息 */
    switch ($storage)
    {
        case 0 :
            $sql = "SELECT goods_id, SUM(send_number) AS num, MAX(extension_code) AS extension_code, product_id FROM " . $GLOBALS['ecs']->table('order_goods') .
                    " WHERE order_id = '$order_id' AND is_real = 1 GROUP BY goods_id, product_id";
        break;

        case 1 :
            $sql = "SELECT goods_id, SUM(goods_number) AS num, MAX(extension_code) AS extension_code, product_id FROM " . $GLOBALS['ecs']->table('order_goods') .
                    " WHERE order_id = '$order_id' AND is_real = 1 GROUP BY goods_id, product_id";
        break;
    }

    $res = $GLOBALS['db']->query($sql);
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        if ($row['extension_code'] != "package_buy")
        {
            if ($is_dec)
            {
                change_goods_storage($row['goods_id'], $row['product_id'], - $row['num']);
            }
            else
            {
                change_goods_storage($row['goods_id'], $row['product_id'], $row['num']);
            }
            $GLOBALS['db']->query($sql);
        }
        else
        {
            $sql = "SELECT goods_id, goods_number" .
                   " FROM " . $GLOBALS['ecs']->table('package_goods') .
                   " WHERE package_id = '" . $row['goods_id'] . "'";
            $res_goods = $GLOBALS['db']->query($sql);
            while ($row_goods = $GLOBALS['db']->fetchRow($res_goods))
            {
                $sql = "SELECT is_real" .
                   " FROM " . $GLOBALS['ecs']->table('goods') .
                   " WHERE goods_id = '" . $row_goods['goods_id'] . "'";
                $real_goods = $GLOBALS['db']->query($sql);
                $is_goods = $GLOBALS['db']->fetchRow($real_goods);

                if ($is_dec)
                {
                    change_goods_storage($row_goods['goods_id'], $row['product_id'], - ($row['num'] * $row_goods['goods_number']));
                }
                elseif ($is_goods['is_real'])
                {
                    change_goods_storage($row_goods['goods_id'], $row['product_id'], ($row['num'] * $row_goods['goods_number']));
                }
            }
        }
    }

}

/**
 * 商品庫存增與減 貨品庫存增與減
 *
 * @param   int    $good_id         商品ID
 * @param   int    $product_id      貨品ID
 * @param   int    $number          增減數量，默認0；
 *
 * @return  bool               true，成功；false，失敗；
 */
function change_goods_storage($good_id, $product_id, $number = 0)
{
    if ($number == 0)
    {
        return true; // 值為0即不做、增減操作，返回true
    }

    if (empty($good_id) || empty($number))
    {
        return false;
    }

    $number = ($number > 0) ? '+ ' . $number : $number;

    /* 處理貨品庫存 */
    $products_query = true;
    if (!empty($product_id))
    {
        $sql = "UPDATE " . $GLOBALS['ecs']->table('products') ."
                SET product_number = product_number $number
                WHERE goods_id = '$good_id'
                AND product_id = '$product_id'
                LIMIT 1";
        $products_query = $GLOBALS['db']->query($sql);
    }

    /* 處理商品庫存 */
    $sql = "UPDATE " . $GLOBALS['ecs']->table('goods') ."
            SET goods_number = goods_number $number
            WHERE goods_id = '$good_id'
            LIMIT 1";
    $query = $GLOBALS['db']->query($sql);

    if ($query && $products_query)
    {
        return true;
    }
    else
    {
        return false;
    }
}

/**
 * 取得支付方式id列表
 * @param   bool    $is_cod 是否貨到付款
 * @return  array
 */
function payment_id_list($is_cod)
{
    $sql = "SELECT pay_id FROM " . $GLOBALS['ecs']->table('payment');
    if ($is_cod)
    {
        $sql .= " WHERE is_cod = 1";
    }
    else
    {
        $sql .= " WHERE is_cod = 0";
    }

    return $GLOBALS['db']->getCol($sql);
}

/**
 * 生成查詢訂單的sql
 * @param   string  $type   類型
 * @param   string  $alias  order表的別名（包括.例如 o.）
 * @return  string
 */
function order_query_sql($type = 'finished', $alias = '')
{
    /* 已完成訂單 */
    if ($type == 'finished')
    {
        return " AND {$alias}order_status " . db_create_in(array(OS_CONFIRMED, OS_SPLITED)) .
               " AND {$alias}shipping_status " . db_create_in(array(SS_SHIPPED, SS_RECEIVED)) .
               " AND {$alias}pay_status " . db_create_in(array(PS_PAYED, PS_PAYING)) . " ";
    }
    /* 待發貨訂單 */
    elseif ($type == 'await_ship')
    {
        return " AND   {$alias}order_status " .
                 db_create_in(array(OS_CONFIRMED, OS_SPLITED, OS_SPLITING_PART)) .
               " AND   {$alias}shipping_status " .
                 db_create_in(array(SS_UNSHIPPED, SS_PREPARING, SS_SHIPPED_ING)) .
               " AND ( {$alias}pay_status " . db_create_in(array(PS_PAYED, PS_PAYING)) . " OR {$alias}pay_id " . db_create_in(payment_id_list(true)) . ") ";
    }
    /* 待付款訂單 */
    elseif ($type == 'await_pay')
    {
        return " AND   {$alias}order_status " . db_create_in(array(OS_CONFIRMED, OS_SPLITED)) .
               " AND   {$alias}pay_status = '" . PS_UNPAYED . "'" .
               " AND ( {$alias}shipping_status " . db_create_in(array(SS_SHIPPED, SS_RECEIVED)) . " OR {$alias}pay_id " . db_create_in(payment_id_list(false)) . ") ";
    }
    /* 未確認訂單 */
    elseif ($type == 'unconfirmed')
    {
        return " AND {$alias}order_status = '" . OS_UNCONFIRMED . "' ";
    }
    /* 未處理訂單：用戶可操作 */
    elseif ($type == 'unprocessed')
    {
        return " AND {$alias}order_status " . db_create_in(array(OS_UNCONFIRMED, OS_CONFIRMED)) .
               " AND {$alias}shipping_status = '" . SS_UNSHIPPED . "'" .
               " AND {$alias}pay_status = '" . PS_UNPAYED . "' ";
    }
    /* 未付款未發貨訂單：管理員可操作 */
    elseif ($type == 'unpay_unship')
    {
        return " AND {$alias}order_status " . db_create_in(array(OS_UNCONFIRMED, OS_CONFIRMED)) .
               " AND {$alias}shipping_status " . db_create_in(array(SS_UNSHIPPED, SS_PREPARING)) .
               " AND {$alias}pay_status = '" . PS_UNPAYED . "' ";
    }
    /* 已發貨訂單：不論是否付款 */
    elseif ($type == 'shipped')
    {
        return " AND {$alias}order_status = '" . OS_CONFIRMED . "'" .
               " AND {$alias}shipping_status " . db_create_in(array(SS_SHIPPED, SS_RECEIVED)) . " ";
    }
    else
    {
        die('函數 order_query_sql 參數錯誤');
    }
}

/**
 * 生成查詢訂單總金額的字段
 * @param   string  $alias  order表的別名（包括.例如 o.）
 * @return  string
 */
function order_amount_field($alias = '')
{
    return "   {$alias}goods_amount + {$alias}tax + {$alias}shipping_fee" .
           " + {$alias}insure_fee + {$alias}pay_fee + {$alias}pack_fee" .
           " + {$alias}card_fee ";
}

/**
 * 生成計算應付款金額的字段
 * @param   string  $alias  order表的別名（包括.例如 o.）
 * @return  string
 */
function order_due_field($alias = '')
{
    return order_amount_field($alias) .
            " - {$alias}money_paid - {$alias}surplus - {$alias}integral_money" .
            " - {$alias}bonus - {$alias}discount ";
}

/**
 * 計算折扣：根據購物車和優惠活動
 * @return  float   折扣
 */
function compute_discount()
{
    /* 查詢優惠活動 */
    $now = gmtime();
    $user_rank = ',' . $_SESSION['user_rank'] . ',';
    $sql = "SELECT *" .
            "FROM " . $GLOBALS['ecs']->table('favourable_activity') .
            " WHERE start_time <= '$now'" .
            " AND end_time >= '$now'" .
            " AND CONCAT(',', user_rank, ',') LIKE '%" . $user_rank . "%'" .
            " AND act_type " . db_create_in(array(FAT_DISCOUNT, FAT_PRICE));
    $favourable_list = $GLOBALS['db']->getAll($sql);
    if (!$favourable_list)
    {
        return 0;
    }

    /* 查詢購物車商品 */
    $sql = "SELECT c.goods_id, c.goods_price * c.goods_number AS subtotal, g.cat_id, g.brand_id " .
            "FROM " . $GLOBALS['ecs']->table('cart') . " AS c, " . $GLOBALS['ecs']->table('goods') . " AS g " .
            "WHERE c.goods_id = g.goods_id " .
            "AND c.session_id = '" . SESS_ID . "' " .
            "AND c.parent_id = 0 " .
            "AND c.is_gift = 0 " .
            "AND rec_type = '" . CART_GENERAL_GOODS . "'";
    $goods_list = $GLOBALS['db']->getAll($sql);
    if (!$goods_list)
    {
        return 0;
    }

    /* 初始化折扣 */
    $discount = 0;
    $favourable_name = array();

    /* 循環計算每個優惠活動的折扣 */
    foreach ($favourable_list as $favourable)
    {
        $total_amount = 0;
        if ($favourable['act_range'] == FAR_ALL)
        {
            foreach ($goods_list as $goods)
            {
                $total_amount += $goods['subtotal'];
            }
        }
        elseif ($favourable['act_range'] == FAR_CATEGORY)
        {
            /* 找出分類id的子分類id */
            $id_list = array();
            $raw_id_list = explode(',', $favourable['act_range_ext']);
            foreach ($raw_id_list as $id)
            {
                $id_list = array_merge($id_list, array_keys(cat_list($id, 0, false)));
            }
            $ids = join(',', array_unique($id_list));

            foreach ($goods_list as $goods)
            {
                if (strpos(',' . $ids . ',', ',' . $goods['cat_id'] . ',') !== false)
                {
                    $total_amount += $goods['subtotal'];
                }
            }
        }
        elseif ($favourable['act_range'] == FAR_BRAND)
        {
            foreach ($goods_list as $goods)
            {
                if (strpos(',' . $favourable['act_range_ext'] . ',', ',' . $goods['brand_id'] . ',') !== false)
                {
                    $total_amount += $goods['subtotal'];
                }
            }
        }
        elseif ($favourable['act_range'] == FAR_GOODS)
        {
            foreach ($goods_list as $goods)
            {
                if (strpos(',' . $favourable['act_range_ext'] . ',', ',' . $goods['goods_id'] . ',') !== false)
                {
                    $total_amount += $goods['subtotal'];
                }
            }
        }
        else
        {
            continue;
        }

        /* 如果金額滿足條件，累計折扣 */
        if ($total_amount > 0 && $total_amount >= $favourable['min_amount'] && ($total_amount <= $favourable['max_amount'] || $favourable['max_amount'] == 0))
        {
            if ($favourable['act_type'] == FAT_DISCOUNT)
            {
                $discount += $total_amount * (1 - $favourable['act_type_ext'] / 100);

                $favourable_name[] = $favourable['act_name'];
            }
            elseif ($favourable['act_type'] == FAT_PRICE)
            {
                $discount += $favourable['act_type_ext'];

                $favourable_name[] = $favourable['act_name'];
            }
        }
    }

    return array('discount' => $discount, 'name' => $favourable_name);
}

/**
 * 取得購物車該贈送的積分數
 * @return  int     積分數
 */
function get_give_integral()
{
        $sql = "SELECT SUM(c.goods_number * IF(g.give_integral > -1, g.give_integral, c.goods_price))" .
                "FROM " . $GLOBALS['ecs']->table('cart') . " AS c, " .
                          $GLOBALS['ecs']->table('goods') . " AS g " .
                "WHERE c.goods_id = g.goods_id " .
                "AND c.session_id = '" . SESS_ID . "' " .
                "AND c.goods_id > 0 " .
                "AND c.parent_id = 0 " .
                "AND c.rec_type = 0 " .
                "AND c.is_gift = 0";

        return intval($GLOBALS['db']->getOne($sql));
}

/**
 * 取得某訂單應該贈送的積分數
 * @param   array   $order  訂單
 * @return  int     積分數
 */
function integral_to_give($order)
{
    /* 判斷是否團購 */
    if ($order['extension_code'] == 'group_buy')
    {
        include_once(ROOT_PATH . 'includes/lib_goods.php');
        $group_buy = group_buy_info(intval($order['extension_id']));

        return array('custom_points' => $group_buy['gift_integral'], 'rank_points' => $order['goods_amount']);
    }
    else
    {
        $sql = "SELECT SUM(og.goods_number * IF(g.give_integral > -1, g.give_integral, og.goods_price)) AS custom_points, SUM(og.goods_number * IF(g.rank_integral > -1, g.rank_integral, og.goods_price)) AS rank_points " .
                "FROM " . $GLOBALS['ecs']->table('order_goods') . " AS og, " .
                          $GLOBALS['ecs']->table('goods') . " AS g " .
                "WHERE og.goods_id = g.goods_id " .
                "AND og.order_id = '$order[order_id]' " .
                "AND og.goods_id > 0 " .
                "AND og.parent_id = 0 " .
                "AND og.is_gift = 0 AND og.extension_code != 'package_buy'";

        return $GLOBALS['db']->getRow($sql);
    }
}

/**
 * 發紅包：發貨時發紅包
 * @param   int     $order_id   訂單號
 * @return  bool
 */
function send_order_bonus($order_id)
{
    /* 取得訂單應該發放的紅包 */
    $bonus_list = order_bonus($order_id);

    /* 如果有紅包，統計併發送 */
    if ($bonus_list)
    {
        /* 用戶信息 */
        $sql = "SELECT u.user_id, u.user_name, u.email " .
                "FROM " . $GLOBALS['ecs']->table('order_info') . " AS o, " .
                          $GLOBALS['ecs']->table('users') . " AS u " .
                "WHERE o.order_id = '$order_id' " .
                "AND o.user_id = u.user_id ";
        $user = $GLOBALS['db']->getRow($sql);

        /* 統計 */
        $count = 0;
        $money = '';
        foreach ($bonus_list AS $bonus)
        {
            $count += $bonus['number'];
            $money .= price_format($bonus['type_money']) . ' [' . $bonus['number'] . '], ';

            /* 修改用戶紅包 */
            $sql = "INSERT INTO " . $GLOBALS['ecs']->table('user_bonus') . " (bonus_type_id, user_id) " .
                    "VALUES('$bonus[type_id]', '$user[user_id]')";
            for ($i = 0; $i < $bonus['number']; $i++)
            {
                if (!$GLOBALS['db']->query($sql))
                {
                    return $GLOBALS['db']->errorMsg();
                }
            }
        }

        /* 如果有紅包，發送郵件 */
        if ($count > 0)
        {
            $tpl = get_mail_template('send_bonus');
            $GLOBALS['smarty']->assign('user_name', $user['user_name']);
            $GLOBALS['smarty']->assign('count', $count);
            $GLOBALS['smarty']->assign('money', $money);
            $GLOBALS['smarty']->assign('shop_name', $GLOBALS['_CFG']['shop_name']);
            $GLOBALS['smarty']->assign('send_date', local_date($GLOBALS['_CFG']['date_format']));
            $GLOBALS['smarty']->assign('sent_date', local_date($GLOBALS['_CFG']['date_format']));
            $content = $GLOBALS['smarty']->fetch('str:' . $tpl['template_content']);
            send_mail($user['user_name'], $user['email'], $tpl['template_subject'], $content, $tpl['is_html']);
        }
    }

    return true;
}

/**
 * 返回訂單發放的紅包
 * @param   int     $order_id   訂單id
 */
function return_order_bonus($order_id)
{
    /* 取得訂單應該發放的紅包 */
    $bonus_list = order_bonus($order_id);

    /* 刪除 */
    if ($bonus_list)
    {
        /* 取得訂單信息 */
        $order = order_info($order_id);
        $user_id = $order['user_id'];

        foreach ($bonus_list AS $bonus)
        {
            $sql = "DELETE FROM " . $GLOBALS['ecs']->table('user_bonus') .
                    " WHERE bonus_type_id = '$bonus[type_id]' " .
                    "AND user_id = '$user_id' " .
                    "AND order_id = '0' LIMIT " . $bonus['number'];
            $GLOBALS['db']->query($sql);
        }
    }
}

/**
 * 取得訂單應該發放的紅包
 * @param   int     $order_id   訂單id
 * @return  array
 */
function order_bonus($order_id)
{
    /* 查詢按商品發的紅包 */
    $day    = getdate();
    $today  = local_mktime(23, 59, 59, $day['mon'], $day['mday'], $day['year']);

    $sql = "SELECT b.type_id, b.type_money, SUM(o.goods_number) AS number " .
            "FROM " . $GLOBALS['ecs']->table('order_goods') . " AS o, " .
                      $GLOBALS['ecs']->table('goods') . " AS g, " .
                      $GLOBALS['ecs']->table('bonus_type') . " AS b " .
            " WHERE o.order_id = '$order_id' " .
            " AND o.is_gift = 0 " .
            " AND o.goods_id = g.goods_id " .
            " AND g.bonus_type_id = b.type_id " .
            " AND b.send_type = '" . SEND_BY_GOODS . "' " .
            " AND b.send_start_date <= '$today' " .
            " AND b.send_end_date >= '$today' " .
            " GROUP BY b.type_id ";
    $list = $GLOBALS['db']->getAll($sql);

    /* 查詢定單中非贈品總金額 */
    $amount = order_amount($order_id, false);

    /* 查詢訂單日期 */
    $sql = "SELECT add_time " .
            " FROM " . $GLOBALS['ecs']->table('order_info') .
            " WHERE order_id = '$order_id' LIMIT 1";
    $order_time = $GLOBALS['db']->getOne($sql);

    /* 查詢按訂單發的紅包 */
    $sql = "SELECT type_id, type_money, IFNULL(FLOOR('$amount' / min_amount), 1) AS number " .
            "FROM " . $GLOBALS['ecs']->table('bonus_type') .
            "WHERE send_type = '" . SEND_BY_ORDER . "' " .
            "AND send_start_date <= '$order_time' " .
            "AND send_end_date >= '$order_time' ";
    $list = array_merge($list, $GLOBALS['db']->getAll($sql));

    return $list;
}

/**
 * 計算購物車中的商品能享受紅包支付的總額
 * @return  float   享受紅包支付的總額
 */
function compute_discount_amount()
{
    /* 查詢優惠活動 */
    $now = gmtime();
    $user_rank = ',' . $_SESSION['user_rank'] . ',';
    $sql = "SELECT *" .
            "FROM " . $GLOBALS['ecs']->table('favourable_activity') .
            " WHERE start_time <= '$now'" .
            " AND end_time >= '$now'" .
            " AND CONCAT(',', user_rank, ',') LIKE '%" . $user_rank . "%'" .
            " AND act_type " . db_create_in(array(FAT_DISCOUNT, FAT_PRICE));
    $favourable_list = $GLOBALS['db']->getAll($sql);
    if (!$favourable_list)
    {
        return 0;
    }

    /* 查詢購物車商品 */
    $sql = "SELECT c.goods_id, c.goods_price * c.goods_number AS subtotal, g.cat_id, g.brand_id " .
            "FROM " . $GLOBALS['ecs']->table('cart') . " AS c, " . $GLOBALS['ecs']->table('goods') . " AS g " .
            "WHERE c.goods_id = g.goods_id " .
            "AND c.session_id = '" . SESS_ID . "' " .
            "AND c.parent_id = 0 " .
            "AND c.is_gift = 0 " .
            "AND rec_type = '" . CART_GENERAL_GOODS . "'";
    $goods_list = $GLOBALS['db']->getAll($sql);
    if (!$goods_list)
    {
        return 0;
    }

    /* 初始化折扣 */
    $discount = 0;
    $favourable_name = array();

    /* 循環計算每個優惠活動的折扣 */
    foreach ($favourable_list as $favourable)
    {
        $total_amount = 0;
        if ($favourable['act_range'] == FAR_ALL)
        {
            foreach ($goods_list as $goods)
            {
                $total_amount += $goods['subtotal'];
            }
        }
        elseif ($favourable['act_range'] == FAR_CATEGORY)
        {
            /* 找出分類id的子分類id */
            $id_list = array();
            $raw_id_list = explode(',', $favourable['act_range_ext']);
            foreach ($raw_id_list as $id)
            {
                $id_list = array_merge($id_list, array_keys(cat_list($id, 0, false)));
            }
            $ids = join(',', array_unique($id_list));

            foreach ($goods_list as $goods)
            {
                if (strpos(',' . $ids . ',', ',' . $goods['cat_id'] . ',') !== false)
                {
                    $total_amount += $goods['subtotal'];
                }
            }
        }
        elseif ($favourable['act_range'] == FAR_BRAND)
        {
            foreach ($goods_list as $goods)
            {
                if (strpos(',' . $favourable['act_range_ext'] . ',', ',' . $goods['brand_id'] . ',') !== false)
                {
                    $total_amount += $goods['subtotal'];
                }
            }
        }
        elseif ($favourable['act_range'] == FAR_GOODS)
        {
            foreach ($goods_list as $goods)
            {
                if (strpos(',' . $favourable['act_range_ext'] . ',', ',' . $goods['goods_id'] . ',') !== false)
                {
                    $total_amount += $goods['subtotal'];
                }
            }
        }
        else
        {
            continue;
        }
        if ($total_amount > 0 && $total_amount >= $favourable['min_amount'] && ($total_amount <= $favourable['max_amount'] || $favourable['max_amount'] == 0))
        {
            if ($favourable['act_type'] == FAT_DISCOUNT)
            {
                $discount += $total_amount * (1 - $favourable['act_type_ext'] / 100);
            }
            elseif ($favourable['act_type'] == FAT_PRICE)
            {
                $discount += $favourable['act_type_ext'];
            }
        }
    }


    return $discount;
}

/**
 * 添加禮包到購物車
 *
 * @access  public
 * @param   integer $package_id   禮包編號
 * @param   integer $num          禮包數量
 * @return  boolean
 */
function add_package_to_cart($package_id, $num = 1)
{
    $GLOBALS['err']->clean();

    /* 取得禮包信息 */
    $package = get_package_info($package_id);

    if (empty($package))
    {
        $GLOBALS['err']->add($GLOBALS['_LANG']['goods_not_exists'], ERR_NOT_EXISTS);

        return false;
    }

    /* 是否正在銷售 */
    if ($package['is_on_sale'] == 0)
    {
        $GLOBALS['err']->add($GLOBALS['_LANG']['not_on_sale'], ERR_NOT_ON_SALE);

        return false;
    }

    /* 現有庫存是否還能湊齊一個禮包 */
    if ($GLOBALS['_CFG']['use_storage'] == '1' && judge_package_stock($package_id))
    {
        $GLOBALS['err']->add(sprintf($GLOBALS['_LANG']['shortage'], 1), ERR_OUT_OF_STOCK);

        return false;
    }

    /* 檢查庫存 */
//    if ($GLOBALS['_CFG']['use_storage'] == 1 && $num > $package['goods_number'])
//    {
//        $num = $goods['goods_number'];
//        $GLOBALS['err']->add(sprintf($GLOBALS['_LANG']['shortage'], $num), ERR_OUT_OF_STOCK);
//
//        return false;
//    }

    /* 初始化要插入購物車的基本件數據 */
    $parent = array(
        'user_id'       => $_SESSION['user_id'],
        'session_id'    => SESS_ID,
        'goods_id'      => $package_id,
        'goods_sn'      => '',
        'goods_name'    => addslashes($package['package_name']),
        'market_price'  => $package['market_package'],
        'goods_price'   => $package['package_price'],
        'goods_number'  => $num,
        'goods_attr'    => '',
        'goods_attr_id' => '',
        'is_real'       => $package['is_real'],
        'extension_code'=> 'package_buy',
        'is_gift'       => 0,
        'rec_type'      => CART_GENERAL_GOODS
    );

    /* 如果數量不為0，作為基本件插入 */
    if ($num > 0)
    {
         /* 檢查該商品是否已經存在在購物車中 */
        $sql = "SELECT goods_number FROM " .$GLOBALS['ecs']->table('cart').
                " WHERE session_id = '" .SESS_ID. "' AND goods_id = '" . $package_id . "' ".
                " AND parent_id = 0 AND extension_code = 'package_buy' " .
                " AND rec_type = '" . CART_GENERAL_GOODS . "'";

        $row = $GLOBALS['db']->getRow($sql);

        if($row) //如果購物車已經有此物品，則更新
        {
            $num += $row['goods_number'];
            if ($GLOBALS['_CFG']['use_storage'] == 0 || $num > 0)
            {
                $sql = "UPDATE " . $GLOBALS['ecs']->table('cart') . " SET goods_number = '" . $num . "'" .
                       " WHERE session_id = '" .SESS_ID. "' AND goods_id = '$package_id' ".
                       " AND parent_id = 0 AND extension_code = 'package_buy' " .
                       " AND rec_type = '" . CART_GENERAL_GOODS . "'";
                $GLOBALS['db']->query($sql);
            }
            else
            {
                $GLOBALS['err']->add(sprintf($GLOBALS['_LANG']['shortage'], $num), ERR_OUT_OF_STOCK);
                return false;
            }
        }
        else //購物車沒有此物品，則插入
        {
            $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('cart'), $parent, 'INSERT');
        }
    }

    /* 把贈品刪除 */
    $sql = "DELETE FROM " . $GLOBALS['ecs']->table('cart') . " WHERE session_id = '" . SESS_ID . "' AND is_gift <> 0";
    $GLOBALS['db']->query($sql);

    return true;
}

/**
 * 得到新發貨單號
 * @return  string
 */
function get_delivery_sn()
{
    /* 選擇一個隨機的方案 */
    mt_srand((double) microtime() * 1000000);

    return date('YmdHi') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
}

/**
 * 檢查禮包內商品的庫存
 * @return  boolen
 */
function judge_package_stock($package_id, $package_num = 1)
{
    $sql = "SELECT goods_id, product_id, goods_number
            FROM " . $GLOBALS['ecs']->table('package_goods') . "
            WHERE package_id = '" . $package_id . "'";
    $row = $GLOBALS['db']->getAll($sql);
    if (empty($row))
    {
        return true;
    }

    /* 分離貨品與商品 */
    $goods = array('product_ids' => '', 'goods_ids' => '');
    foreach ($row as $value)
    {
        if ($value['product_id'] > 0)
        {
            $goods['product_ids'] .= ',' . $value['product_id'];
            continue;
        }

        $goods['goods_ids'] .= ',' . $value['goods_id'];
    }

    /* 檢查貨品庫存 */
    if ($goods['product_ids'] != '')
    {
        $sql = "SELECT p.product_id
                FROM " . $GLOBALS['ecs']->table('products') . " AS p, " . $GLOBALS['ecs']->table('package_goods') . " AS pg
                WHERE pg.product_id = p.product_id
                AND pg.package_id = '$package_id'
                AND pg.goods_number * $package_num > p.product_number
                AND p.product_id IN (" . trim($goods['product_ids'], ',') . ")";
        $row = $GLOBALS['db']->getAll($sql);

        if (!empty($row))
        {
            return true;
        }
    }

    /* 檢查商品庫存 */
    if ($goods['goods_ids'] != '')
    {
        $sql = "SELECT g.goods_id
                FROM " . $GLOBALS['ecs']->table('goods') . "AS g, " . $GLOBALS['ecs']->table('package_goods') . " AS pg
                WHERE pg.goods_id = g.goods_id
                AND pg.goods_number * $package_num > g.goods_number
                AND pg.package_id = '" . $package_id . "'
                AND pg.goods_id IN (" . trim($goods['goods_ids'], ',') . ")";
        $row = $GLOBALS['db']->getAll($sql);

        if (!empty($row))
        {
            return true;
        }
    }

    return false;
}
?>