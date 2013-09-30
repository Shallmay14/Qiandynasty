<?php

/**
 * ECSHOP 郵局平郵插件
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: post_mail.php 17063 2010-03-25 06:35:46Z liuhui $
 */

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

$shipping_lang = ROOT_PATH.'languages/' .$GLOBALS['_CFG']['lang']. '/shipping/post_mail.php';
if (file_exists($shipping_lang))
{
    global $_LANG;
    include_once($shipping_lang);
}

/* 模塊的基本信息 */
if (isset($set_modules) && $set_modules == TRUE)
{
    $i = (isset($modules)) ? count($modules) : 0;

    /* 配送方式插件的代碼必須和文件名保持一致 */
    $modules[$i]['code']    = basename(__FILE__, '.php');

    $modules[$i]['version'] = '1.0.0';

    /* 配送方式的描述 */
    $modules[$i]['desc']    = 'post_mail_desc';

    /* 配送方式是否支持貨到付款 */
    $modules[$i]['cod']     = false;

    /* 插件的作者 */
    $modules[$i]['author']  = 'ECSHOP TEAM';

    /* 插件作者的官方網站 */
    $modules[$i]['website'] = 'http://www.ecshop.com';

    /* 配送接口需要的參數 */
    $modules[$i]['configure'] = array(
                                    array('name' => 'item_fee',          'value'=>4),
                                    array('name' => 'base_fee',          'value'=>3.5),
                                    array('name' => 'step_fee',          'value'=>2),
                                    array('name' => 'step_fee1',          'value'=>2.5),
                                    array('name' => 'pack_fee',           'value'=>0),
                                );

    /* 模式編輯器 */
    $modules[$i]['print_model'] = 2;

    /* 打印單背景 */
    $modules[$i]['print_bg'] = '';

   /* 打印快遞單標籤位置信息 */
    $modules[$i]['config_lable'] = '';

    return;
}

/**
 * 郵局平郵費用計算方式: 每公斤資費 × 包裹重量 + 掛號費3.00 + 郵單費0.5 + 包裝費(按實際收取) ＋ 保價費
 *
 * 保價費 由客戶自願選擇，保價費為訂單產品價值的1％。客戶選擇不保價，則保價費＝0
 *
 */
class post_mail
{
    /*------------------------------------------------------ */
    //-- PUBLIC ATTRIBUTEs
    /*------------------------------------------------------ */

    /**
     * 配置信息
     */
    var $configure;

    /*------------------------------------------------------ */
    //-- PUBLIC METHODs
    /*------------------------------------------------------ */

    /**
     * 構造函數
     *
     * @param: $configure[array]    配送方式的參數的數組
     *
     * @return null
     */
    function post_mail($cfg=array())
    {
        foreach ($cfg AS $key=>$val)
        {
            $this->configure[$val['name']] = $val['value'];
        }
    }

    /**
     * 計算訂單的配送費用的函數
     *
     * @param   float   $goods_weight   商品重量
     * @param   float   $goods_amount   商品金額
     * @param   float   $goods_number   商品件數
     * @return  decimal
     */
    function calculate($goods_weight, $goods_amount, $goods_number)
    {
        if ($this->configure['free_money'] > 0 && $goods_amount >= $this->configure['free_money'])
        {
            return 0;
        }
        else
        {
            /* 基本費用 */
            $fee = $this->configure['base_fee'] + $this->configure['pack_fee'];
            $this->configure['fee_compute_mode'] = !empty($this->configure['fee_compute_mode']) ? $this->configure['fee_compute_mode'] : 'by_weight';

            if ($this->configure['fee_compute_mode'] == 'by_number')
            {
                $fee = $goods_number * ($this->configure['item_fee'] + $this->configure['pack_fee']);
            }
            else
            {
                if ($goods_weight > 5)
                {
                    $fee += 4 * $this->configure['step_fee'];
                    $fee += (ceil(($goods_weight - 5))) * $this->configure['step_fee1'];
                }
                else
                {
                    if ($goods_weight > 1)
                    {
                        $fee += (ceil(($goods_weight - 1))) * $this->configure['step_fee'];
                    }
                }
            }


            return $fee;
        }
    }

    /**
     * 查詢發貨狀態
     * 該配送方式不支持查詢發貨狀態
     *
     * @access  public
     * @param   string  $invoice_sn     發貨單號
     * @return  string
     */
    function query($invoice_sn)
    {
        return $invoice_sn;
    }
}

?>