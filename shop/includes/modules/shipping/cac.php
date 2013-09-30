<?php

/**
 * ECSHOP 上門取貨插件
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: cac.php 17063 2010-03-25 06:35:46Z liuhui $
 */

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

$shipping_lang = ROOT_PATH.'languages/' .$GLOBALS['_CFG']['lang']. '/shipping/cac.php';
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
    $modules[$i]['code']    = 'cac';

    $modules[$i]['version'] = '1.0.0';

    /* 配送方式的描述 */
    $modules[$i]['desc']    = 'cac_desc';

    /* 不支持保價 */
    $modules[$i]['insure']  = false;

    /* 配送方式是否支持貨到付款 */
    $modules[$i]['cod']     = TRUE;

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

class cac
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
    function cac($cfg = array())
    {
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
        return 0;
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