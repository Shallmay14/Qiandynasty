<?php

/**
 * ECSHOP 貝寶插件
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: paypal.php 17063 2010-03-25 06:35:46Z liuhui $
 */

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

$payment_lang = ROOT_PATH . 'languages/' .$GLOBALS['_CFG']['lang']. '/payment/paypal.php';

if (file_exists($payment_lang))
{
    global $_LANG;

    include_once($payment_lang);
}

/* 模塊的基本信息 */
if (isset($set_modules) && $set_modules == TRUE)
{
    $i = isset($modules) ? count($modules) : 0;

    /* 代碼 */
    $modules[$i]['code']    = basename(__FILE__, '.php');

    /* 描述對應的語言項 */
    $modules[$i]['desc']    = 'paypal_desc';

    /* 是否支持貨到付款 */
    $modules[$i]['is_cod']  = '0';

    /* 是否支持在線支付 */
    $modules[$i]['is_online']  = '1';

    /* 作者 */
    $modules[$i]['author']  = 'ECSHOP TEAM';

    /* 網址 */
    $modules[$i]['website'] = 'http://www.paypal.com';

    /* 版本號 */
    $modules[$i]['version'] = '1.0.0';

    /* 配置信息 */
    $modules[$i]['config'] = array(
        array('name' => 'paypal_account', 'type' => 'text', 'value' => ''),
        array('name' => 'paypal_currency', 'type' => 'select', 'value' => 'USD')
    );

    return;
}

/**
 * 類
 */
class paypal
{
    /**
     * 構造函數
     *
     * @access  public
     * @param
     *
     * @return void
     */
    function paypal()
    {
    }

    function __construct()
    {
        $this->paypal();
    }

    /**
     * 生成支付代碼
     * @param   array   $order  訂單信息
     * @param   array   $payment    支付方式信息
     */
    function get_code($order, $payment)
    {
        $data_order_id      = $order['log_id'];
        $data_amount        = $order['order_amount'];
        $data_return_url    = return_url(basename(__FILE__, '.php'));
        $data_pay_account   = $payment['paypal_account'];
        $currency_code      = $payment['paypal_currency'];
        $data_notify_url    = return_url(basename(__FILE__, '.php'));
        $cancel_return      = $GLOBALS['ecs']->url();

        $def_url  = '<br /><form style="text-align:center;" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">' .   // 不能省略
            "<input type='hidden' name='cmd' value='_xclick'>" .                             // 不能省略
            "<input type='hidden' name='business' value='$data_pay_account'>" .                 // 貝寶帳號
            "<input type='hidden' name='item_name' value='$order[order_sn]'>" .                 // payment for
            "<input type='hidden' name='amount' value='$data_amount'>" .                        // 訂單金額
            "<input type='hidden' name='currency_code' value='$currency_code'>" .            // 貨幣
            "<input type='hidden' name='return' value='$data_return_url'>" .                    // 付款後頁面
            "<input type='hidden' name='invoice' value='$data_order_id'>" .                      // 訂單號
            "<input type='hidden' name='charset' value='utf-8'>" .                              // 字符集
            "<input type='hidden' name='no_shipping' value='1'>" .                              // 不要求客戶提供收貨地址
            "<input type='hidden' name='no_note' value=''>" .                                  // 付款說明
            "<input type='hidden' name='notify_url' value='$data_notify_url'>" .
            "<input type='hidden' name='rm' value='2'>" .
            "<input type='hidden' name='cancel_return' value='$cancel_return'>" .
            "<input type='submit' value='" . $GLOBALS['_LANG']['paypal_button'] . "'>" .                      // 按鈕
            "</form><br />";

        return $def_url;
    }

    /**
     * 響應操作
     */
    function respond()
    {
        $payment        = get_payment('paypal');
        $merchant_id    = $payment['paypal_account'];               ///獲取商戶編號

        // read the post from PayPal system and add 'cmd'
        $req = 'cmd=_notify-validate';
        foreach ($_POST as $key => $value)
        {
            $value = urlencode(stripslashes($value));
            $req .= "&$key=$value";
        }

        // post back to PayPal system to validate
        $header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Content-Length: " . strlen($req) ."\r\n\r\n";
        $fp = fsockopen ('www.paypal.com', 80, $errno, $errstr, 30);

        // assign posted variables to local variables
        $item_name = $_POST['item_name'];
        $item_number = $_POST['item_number'];
        $payment_status = $_POST['payment_status'];
        $payment_amount = $_POST['mc_gross'];
        $payment_currency = $_POST['mc_currency'];
        $txn_id = $_POST['txn_id'];
        $receiver_email = $_POST['receiver_email'];
        $payer_email = $_POST['payer_email'];
        $order_sn = $_POST['invoice'];
        $memo = !empty($_POST['memo']) ? $_POST['memo'] : '';
        $action_note = $txn_id . '（' . $GLOBALS['_LANG']['paypal_txn_id'] . '）' . $memo;

        if (!$fp)
        {
            fclose($fp);

            return false;
        }
        else
        {
            fputs($fp, $header . $req);
            while (!feof($fp))
            {
                $res = fgets($fp, 1024);
                if (strcmp($res, 'VERIFIED') == 0)
                {
                    // check the payment_status is Completed
                    if ($payment_status != 'Completed' && $payment_status != 'Pending')
                    {
                        fclose($fp);

                        return false;
                    }

                    // check that txn_id has not been previously processed
                    /*$sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('order_action') . " WHERE action_note LIKE '" . mysql_like_quote($txn_id) . "%'";
                    if ($GLOBALS['db']->getOne($sql) > 0)
                    {
                        fclose($fp);

                        return false;
                    }*/

                    // check that receiver_email is your Primary PayPal email
                    if ($receiver_email != $merchant_id)
                    {
                        fclose($fp);

                        return false;
                    }

                    // check that payment_amount/payment_currency are correct
                    $sql = "SELECT order_amount FROM " . $GLOBALS['ecs']->table('pay_log') . " WHERE log_id = '$order_sn'";
                    if ($GLOBALS['db']->getOne($sql) != $payment_amount)
                    {
                        fclose($fp);

                        return false;
                    }
                    if ($payment['paypal_currency'] != $payment_currency)
                    {
                        fclose($fp);

                        return false;
                    }

                    // process payment
                    order_paid($order_sn, PS_PAYED, $action_note);
                    fclose($fp);

                    return true;
                }
                elseif (strcmp($res, 'INVALID') == 0)
                {
                    // log for manual investigation
                    fclose($fp);

                    return false;
                }
            }
        }
    }
}

?>