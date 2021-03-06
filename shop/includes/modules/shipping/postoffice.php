<?php

/**
 * ECSHOP 台灣運送方式外掛
 * ============================================================================
 * 版權所有 (C) 2006-2007 億商互動有限公司，並保留所有權利。
 * 網站地址: http://www.myecshop.com
 * ----------------------------------------------------------------------------
 * 這是一個免費開源的軟件；這意味著您可以在不用於商業目的的前提下對程序代碼
 * 進行修改、使用和再發布。
 * ============================================================================
 * @author:     myecshop <myecshop@myecshop.com>
 * @version:    v1.0
 * ---------------------------------------------
 * $Author: paulgao $
 * $Date: 2006-12-28 15:10:34 +0800 (星期四, 28 十二月 2006) $
 * $Id: postoffice.php 3819 2006-12-28 07:10:34Z paulgao $
*/
$shipping_lang = ROOT_PATH.'languages/' .$GLOBALS['_CFG']['lang']. '/shipping/postoffice.php';
if (file_exists($shipping_lang))
{
    global $_LANG;
    include_once($shipping_lang);
}	
	
	/*
 * 模塊的基本信息
*/

if(isset($set_modules) && $set_modules == TRUE) 
{
    $i = (isset($modules)) ? count($modules) : 0;

    /* 配送方式插件的代碼必須和文件名保持一致 */
    $modules[$i]['code']    = "postoffice";
    
    $modules[$i]['version'] = '1.0.0';
    
    /* 配送方式的描述 */
    $modules[$i]['desc']    = 'postoffice_desc';
    
    /* 配送方式是否支持貨到付款 */
    $modules[$i]['cod']     = TRUE;
    
    /* 插件的作者 */
    $modules[$i]['author']  = "億商互動";
    
    /* 插件作者的官方網站 */
    $modules[$i]['website'] = "http://www.myecshop.com";

    /* 配送接口需要的參數 */
    $modules[$i]['configure'] = array(
                                        array('name' => 'base_fee',     'value'=>100),
                                );

    return;
}

class postoffice
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
    function postoffice($cfg=array())
    {
        foreach ($cfg AS $key=>$val)
        {
            $this->configure[$val['name']] = $val['value'];
        }
    }


    /**
     * 計算訂單的配送費用的函數
     * 
     * @return decimal
     */
    function calculate()
    {
        $cart = cart_weight_price();        // 獲得商品總重量以及總金額

        if ($this->configure['free_money'] > 0 && $cart['amount'] >= $this->configure['free_money'])
        {
            return 0;
        }
        else
        {
            return $this->configure['base_fee'];
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
        $form_str = '商品透過郵局發出<form name="form1" action="http://postserv.post.gov.tw/WebMailNslookup/askBlueStar" method="post" target=_blank>';
        $form_str .= '<input type="hidden" name="LookUptype" value="domestic_bundle_register">';
		$form_str .= '<input type="hidden" name="apID" value="LIQD" >';
        $form_str .= '<input type="hidden" name="kind" value="15" >';
		$form_str .= '<input type="hidden" name="MailNum_1" value='.$invoice_sn.'><input type="Submit" value="投遞進度查詢(開啟在新視窗)"></form>';
        return $form_str;
    }
};

?>