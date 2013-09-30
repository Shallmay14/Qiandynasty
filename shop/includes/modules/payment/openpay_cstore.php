<?php

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

$payment_lang = ROOT_PATH . 'languages/' .$GLOBALS['_CFG']['lang']. '/payment/openpay_cstore.php';

if (file_exists($payment_lang))
{
    global $_LANG;

    include_once($payment_lang);
}

if (isset($set_modules) && $set_modules == TRUE)
{
    $i = isset($modules) ? count($modules) : 0;

    $modules[$i]['code']    = basename(__FILE__, '.php');
    $modules[$i]['desc']    = 'openpay_cstore_desc';
    $modules[$i]['is_cod']  = '0';
    $modules[$i]['is_online']  = '1';
    $modules[$i]['author']  = 'TWV';
    $modules[$i]['website'] = 'https://www.twv.com.tw/';
    $modules[$i]['version'] = '2.1.4';
    $modules[$i]['config'] = array(
        array('name' => 'openpay_cstore_mid',  'type' => 'text', 'value' => 'TEST'),
        array('name' => 'openpay_cstore_key1', 'type' => 'text', 'value' => '21216879a5d9e1c51b3cbc1c68b22399'),
        array('name' => 'openpay_cstore_key2', 'type' => 'text', 'value' => '6d4b111610073f9c1105d3f852a3d039')
    );

    return;
}

class openpay_cstore
{
    function openpay_cstore()
    {
        $this->form_action_url  = 'https://www.twv.com.tw/openpay/pay.php';
        $this->version          = '2.1';
        $this->prefer_paymethod = '8';
        $this->iid              = '0';
        $this->charset          = 'UTF-8';
        $this->language         = 'tchinese';
        $this->cart_type        = $GLOBALS['_CFG']['ecs_version'].'-openpay2.1.4';
    }

    function __construct()
    {
        $this->openpay_cstore();
    }

    function get_code($order, $payment)
    {
        $mid        = $payment['openpay_cstore_mid'];
        $key1       = $payment['openpay_cstore_key1'];
        $key2       = $payment['openpay_cstore_key2'];
        $txid       = $order['order_sn']."-".$order['log_id'];
        $amount     = round( $order['order_amount'] );
        $verify     = md5(implode( '|', array($key1, $mid, $txid, $amount, $key2) ));
        $return_url = return_url(basename(__FILE__, '.php'));
        $cname      = $order['consignee'];
        $caddress   = $order['address'];
        $ctel       = $order['tel'];
        $cemail     = $order['email'];
        $xname      = $order['consignee'];
        $xaddress   = $order['address'];

        $pay_url  = "\n"
          ."<form method='post' action='".$this->form_action_url."'>\n"
          ."<input type='hidden' name='version' value='".$this->version."' />\n"
          ."<input type='hidden' name='mid' value='".$mid."' />\n"
          ."<input type='hidden' name='prefer_paymethod' value='".$this->prefer_paymethod."' />\n"
          ."<input type='hidden' name='iid' value='".$this->iid."' />\n"
          ."<input type='hidden' name='txid' value='".$txid."' />\n"
          ."<input type='hidden' name='amount' value='".$amount."' />\n"
          ."<input type='hidden' name='verify' value='".$verify."' />\n"
          ."<input type='hidden' name='return_url' value='".$return_url."' />\n"
          ."<input type='hidden' name='charset' value='".$this->charset."' />\n"
          ."<input type='hidden' name='language' value='".$this->language."' />\n"
          ."<input type='hidden' name='cart_type' value='".$this->cart_type."' />\n"
          ."<input type='hidden' name='cname' value='".$cname."' />\n"
          ."<input type='hidden' name='caddress' value='".$caddress."' />\n"
          ."<input type='hidden' name='ctel' value='".$ctel."' />\n"
          ."<input type='hidden' name='cemail' value='".$cemail."' />\n"
          ."<input type='hidden' name='xname' value='".$xname."' />\n"
          ."<input type='hidden' name='xaddress' value='".$xaddress."' />\n"
          ."<input type='submit' value='".$GLOBALS['_LANG']['openpay_cstore_button']."' />\n"
          ."</form>\n";

        return $pay_url;
    }

    function respond()
    {
        $payment    = get_payment(basename(__FILE__, '.php'));
        $mid        = $payment['openpay_cstore_mid'];
        $key1       = $payment['openpay_cstore_key1'];
        $key2       = $payment['openpay_cstore_key2'];

        $txid       = $_REQUEST['txid'];
        list( $order_sn, $log_id ) = explode( "-", $txid );
        $amount     = $_REQUEST['amount'];
        $pay_type   = $_REQUEST['pay_type'];
        $status     = $_REQUEST['status'];
        $tid        = $_REQUEST['tid'];
        $verify     = $_REQUEST['verify'];

        $verify_digest = md5(implode( '|', array($key1, $txid, $amount, $pay_type, $status, $tid, $key2) ));
        if( $verify != $verify_digest ) {
            return false;
        }

        // load pay_log
        $log_id += 0;
        $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('pay_log') .
                " WHERE log_id = ".$log_id;
        $pay_log = $GLOBALS['db']->getRow($sql);

        // check reload
        if( $pay_log['is_paid'] ) {
            return true;
        }

        // check amount
        $order_amount = round($pay_log['order_amount']);
        if( $order_amount != $amount ) {
            return false;
        }

        // process payment
        switch( $status ) {
        case '1':
          $pay_status = PS_PAYED;
          break;
        case '3':
          $pay_status = PS_PAYING;
          break;
        case '2':
        default:
          $pay_status = PS_UNPAYED;
          break;
        }
        order_paid($log_id, $pay_status, "tid=".$tid);

        return true;
    }
}

?>