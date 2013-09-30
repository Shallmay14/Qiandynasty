<?php

/**
 * ECSHOP 掛號印刷品插件
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: presswork.php 17063 2010-03-25 06:35:46Z liuhui $
 */

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

$shipping_lang = ROOT_PATH.'languages/' .$GLOBALS['_CFG']['lang']. '/shipping/presswork.php';
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
    $modules[$i]['desc']    = 'presswork_desc';

    /* 保價比例,如果不支持保價則填入false,支持則還需加入calculate_insure()函數。固定價格直接填入固定數字，按商品總價則在數值後加上%  */
    $modules[$i]['insure']  = '1%';

    /* 配送方式是否支持貨到付款 */
    $modules[$i]['cod']     = false;

    /* 插件的作者 */
    $modules[$i]['author']  = 'ECSHOP TEAM';

    /* 插件作者的官方網站 */
    $modules[$i]['website'] = 'http://www.ecshop.com';

    /* 配送接口需要的參數 */
    $modules[$i]['configure'] = array();

    /* 模式編輯器 */
    $modules[$i]['print_model'] = 2;

    /* 打印單背景 */
    $modules[$i]['print_bg'] = '';

   /* 打印快遞單標籤位置信息 */
    $modules[$i]['config_lable'] = '';

    return;
}

class presswork
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
    function presswork($cfg=array())
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
     * @return  decimal
     */
    function calculate($goods_weight, $goods_amount)
    {
        if ($this->configure['free_money'] > 0 && $goods_amount >= $this->configure['free_money'])
        {
            return 0;
        }
        else
        {
            $fee    = $goods_weight * 4 + 3.4;

            if ($goods_weight > 0.1)
            {
                $fee += (ceil(($goods_weight - 0.1) / 0.1)) * 0.4;
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

    /**
     *  當保價比例以%出現時，計算保價費用
     *
     * @access  public
     * @param   decimal $tatal_price  需要保價的商品總價
     * @param   decimal $insure_rate  保價計算比例
     *
     * @return  decimal $price        保價費用
     */
    function calculate_insure($total_price, $insure_rate)
    {
        $total_price = ceil($total_price);
        $price = $total_price * $insure_rate;
        if ($price < 1)
        {
            $price = 1;
        }
        return ceil($price);
    }
}

?>