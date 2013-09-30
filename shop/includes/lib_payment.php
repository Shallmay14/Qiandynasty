<?php

/**
 * ECSHOP 支付接口函數庫
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發布。
 * ============================================================================
 * $Author: yehuaixiao $
 * $Id: lib_payment.php 17218 2011-01-24 04:10:41Z yehuaixiao $
 */

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

/**
 * 取得返回信息地址
 * @param   string  $code   支付方式代碼
 */
function return_url($code)
{
    return $GLOBALS['ecs']->url() . 'respond.php?code=' . $code;
}

/**
 *  取得某支付方式信息
 *  @param  string  $code   支付方式代碼
 */
function get_payment($code)
{
    $sql = 'SELECT * FROM ' . $GLOBALS['ecs']->table('payment').
           " WHERE pay_code = '$code' AND enabled = '1'";
    $payment = $GLOBALS['db']->getRow($sql);

    if ($payment)
    {
        $config_list = unserialize($payment['pay_config']);

        foreach ($config_list AS $config)
        {
            $payment[$config['name']] = $config['value'];
        }
    }

    return $payment;
}

/**
 *  通過訂單sn取得訂單ID
 *  @param  string  $order_sn   訂單sn
 *  @param  blob    $voucher    是否為會員充值
 */
function get_order_id_by_sn($order_sn, $voucher = 'false')
{
    if ($voucher == 'true')
    {
        if(is_numeric($order_sn))
        {
              return $GLOBALS['db']->getOne("SELECT log_id FROM " . $GLOBALS['ecs']->table('pay_log') . " WHERE order_id=" . $order_sn . ' AND order_type=1');
        }
        else
        {
            return "";
        }
    }
    else
    {
        if(is_numeric($order_sn))
        {
            $sql = 'SELECT order_id FROM ' . $GLOBALS['ecs']->table('order_info'). " WHERE order_sn = '$order_sn'";
            $order_id = $GLOBALS['db']->getOne($sql);
        }
        if (!empty($order_id))
        {
            $pay_log_id = $GLOBALS['db']->getOne("SELECT log_id FROM " . $GLOBALS['ecs']->table('pay_log') . " WHERE order_id='" . $order_id . "'");
            return $pay_log_id;
        }
        else
        {
            return "";
        }
    }
}

/**
 *  通過訂單ID取得訂單商品名稱
 *  @param  string  $order_id   訂單ID
 */
function get_goods_name_by_id($order_id)
{
    $sql = 'SELECT goods_name FROM ' . $GLOBALS['ecs']->table('order_goods'). " WHERE order_id = '$order_id'";
    $goods_name = $GLOBALS['db']->getCol($sql);
    return implode(',', $goods_name);
}

/**
 * 檢查支付的金額是否與訂單相符
 *
 * @access  public
 * @param   string   $log_id      支付編號
 * @param   float    $money       支付接口返回的金額
 * @return  true
 */
function check_money($log_id, $money)
{
    $sql = 'SELECT order_amount FROM ' . $GLOBALS['ecs']->table('pay_log') .
              " WHERE log_id = '$log_id'";
    $amount = $GLOBALS['db']->getOne($sql);

    if ($money == $amount)
    {
        return true;
    }
    else
    {
        return false;
    }
}

/**
 * 修改訂單的支付狀態
 *
 * @access  public
 * @param   string  $log_id     支付編號
 * @param   integer $pay_status 狀態
 * @param   string  $note       備註
 * @return  void
 */
function order_paid($log_id, $pay_status = PS_PAYED, $note = '')
{
    /* 取得支付編號 */
    $log_id = intval($log_id);
    if ($log_id > 0)
    {
        /* 取得要修改的支付記錄信息 */
        $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('pay_log') .
                " WHERE log_id = '$log_id'";
        $pay_log = $GLOBALS['db']->getRow($sql);
        if ($pay_log && $pay_log['is_paid'] == 0)
        {
            /* 修改此次支付操作的狀態為已付款 */
            $sql = 'UPDATE ' . $GLOBALS['ecs']->table('pay_log') .
                    " SET is_paid = '1' WHERE log_id = '$log_id'";
            $GLOBALS['db']->query($sql);

            /* 根據記錄類型做相應處理 */
            if ($pay_log['order_type'] == PAY_ORDER)
            {
                /* 取得訂單信息 */
                $sql = 'SELECT order_id, user_id, order_sn, consignee, address, tel, shipping_id, extension_code, extension_id, goods_amount ' .
                        'FROM ' . $GLOBALS['ecs']->table('order_info') .
                       " WHERE order_id = '$pay_log[order_id]'";
                $order    = $GLOBALS['db']->getRow($sql);
                $order_id = $order['order_id'];
                $order_sn = $order['order_sn'];

                /* 修改訂單狀態為已付款 */
                $sql = 'UPDATE ' . $GLOBALS['ecs']->table('order_info') .
                            " SET order_status = '" . OS_CONFIRMED . "', " .
                                " confirm_time = '" . gmtime() . "', " .
                                " pay_status = '$pay_status', " .
                                " pay_time = '".gmtime()."', " .
                                " money_paid = order_amount," .
                                " order_amount = 0 ".
                       "WHERE order_id = '$order_id'";
                $GLOBALS['db']->query($sql);

                /* 記錄訂單操作記錄 */
                order_action($order_sn, OS_CONFIRMED, SS_UNSHIPPED, $pay_status, $note, $GLOBALS['_LANG']['buyer']);

                /* 如果需要，發短信 */
                if ($GLOBALS['_CFG']['sms_order_payed'] == '1' && $GLOBALS['_CFG']['sms_shop_mobile'] != '')
                {
                    include_once(ROOT_PATH.'includes/cls_sms.php');
                    $sms = new sms();
                    $sms->send($GLOBALS['_CFG']['sms_shop_mobile'],
                        sprintf($GLOBALS['_LANG']['order_payed_sms'], $order_sn, $order['consignee'], $order['tel']), 0);
                }

                /* 對虛擬商品的支持 */
                $virtual_goods = get_virtual_goods($order_id);
                if (!empty($virtual_goods))
                {
                    $msg = '';
                    if (!virtual_goods_ship($virtual_goods, $msg, $order_sn, true))
                    {
                        $GLOBALS['_LANG']['pay_success'] .= '<div style="color:red;">'.$msg.'</div>'.$GLOBALS['_LANG']['virtual_goods_ship_fail'];
                    }

                    /* 如果訂單沒有配送方式，自動完成發貨操作 */
                    if ($order['shipping_id'] == -1)
                    {
                        /* 將訂單標識為已發貨狀態，並記錄發貨記錄 */
                        $sql = 'UPDATE ' . $GLOBALS['ecs']->table('order_info') .
                               " SET shipping_status = '" . SS_SHIPPED . "', shipping_time = '" . gmtime() . "'" .
                               " WHERE order_id = '$order_id'";
                        $GLOBALS['db']->query($sql);

                         /* 記錄訂單操作記錄 */
                        order_action($order_sn, OS_CONFIRMED, SS_SHIPPED, $pay_status, $note, $GLOBALS['_LANG']['buyer']);
                        $integral = integral_to_give($order);
                        log_account_change($order['user_id'], 0, 0, intval($integral['rank_points']), intval($integral['custom_points']), sprintf($GLOBALS['_LANG']['order_gift_integral'], $order['order_sn']));
                    }
                }

            }
            elseif ($pay_log['order_type'] == PAY_SURPLUS)
            {
                /* 更新會員預付款的到款狀態 */
                $sql = 'UPDATE ' . $GLOBALS['ecs']->table('user_account') .
                       " SET paid_time = '" .gmtime(). "', is_paid = 1" .
                       " WHERE id = '$pay_log[order_id]' LIMIT 1";
                $GLOBALS['db']->query($sql);

                /* 取得添加預付款的用戶以及金額 */
                $sql = "SELECT user_id, amount FROM " . $GLOBALS['ecs']->table('user_account') .
                        " WHERE id = '$pay_log[order_id]'";
                $arr = $GLOBALS['db']->getRow($sql);

                /* 修改會員帳戶金額 */
                $_LANG = array();
                include_once(ROOT_PATH . 'languages/' . $GLOBALS['_CFG']['lang'] . '/user.php');
                log_account_change($arr['user_id'], $arr['amount'], 0, 0, 0, $_LANG['surplus_type_0'], ACT_SAVING);
            }
        }
        else
        {
            /* 取得已發貨的虛擬商品信息 */
            $post_virtual_goods = get_virtual_goods($pay_log['order_id'], true);

            /* 有已發貨的虛擬商品 */
            if (!empty($post_virtual_goods))
            {
                $msg = '';
                /* 檢查兩次刷新時間有無超過12小時 */
                $sql = 'SELECT pay_time, order_sn FROM ' . $GLOBALS['ecs']->table('order_info') . " WHERE order_id = '$pay_log[order_id]'";
                $row = $GLOBALS['db']->getRow($sql);
                $intval_time = gmtime() - $row['pay_time'];
                if ($intval_time >= 0 && $intval_time < 3600 * 12)
                {
                    $virtual_card = array();
                    foreach ($post_virtual_goods as $code => $goods_list)
                    {
                        /* 只處理虛擬卡 */
                        if ($code == 'virtual_card')
                        {
                            foreach ($goods_list as $goods)
                            {
                                if ($info = virtual_card_result($row['order_sn'], $goods))
                                {
                                    $virtual_card[] = array('goods_id'=>$goods['goods_id'], 'goods_name'=>$goods['goods_name'], 'info'=>$info);
                                }
                            }

                            $GLOBALS['smarty']->assign('virtual_card',      $virtual_card);
                        }
                    }
                }
                else
                {
                    $msg = '<div>' .  $GLOBALS['_LANG']['please_view_order_detail'] . '</div>';
                }

                $GLOBALS['_LANG']['pay_success'] .= $msg;
            }

           /* 取得未發貨虛擬商品 */
           $virtual_goods = get_virtual_goods($pay_log['order_id'], false);
           if (!empty($virtual_goods))
           {
               $GLOBALS['_LANG']['pay_success'] .= '<br />' . $GLOBALS['_LANG']['virtual_goods_ship_fail'];
           }
        }
    }
}

?>