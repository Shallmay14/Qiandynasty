<?php

/**
 * ECSHOP 台灣運送方式外掛
 * ============================================================================
 * 版权所有 (C) 2006-2007 億商互動有限公司，并保留所有权利。
 * 网站地址: http://www.myecshop.com
 * ----------------------------------------------------------------------------
 * 这是一个免费开源的软件；这意味着您可以在不用于商业目的的前提下对程序代码
 * 进行修改、使用和再发布。
 * ============================================================================
 * @author:     myecshop <myecshop@myecshop.com>
 * @version:    v1.0
 * ---------------------------------------------
 * $Author: paulgao $
 * $Date: 2006-12-28 15:10:34 +0800 (星期四, 28 十二月 2006) $
 * $Id: tjoin.php 3819 2006-12-28 07:10:34Z paulgao $
*/
$shipping_lang = ROOT_PATH.'languages/' .$GLOBALS['_CFG']['lang']. '/shipping/tjoin.php';
if (file_exists($shipping_lang))
{
    global $_LANG;
    include_once($shipping_lang);
}	
/*
 * 模块的基本信息
*/

if(isset($set_modules) && $set_modules == TRUE) 
{
    $i = (isset($modules)) ? count($modules) : 0;

    /* 配送方式插件的代码必须和文件名保持一致 */
    $modules[$i]['code']    = "tjoin";
    
    $modules[$i]['version'] = '1.0.0';
    
    /* 配送方式的描述 */
    $modules[$i]['desc']    = 'tjoin_desc';
    
    /* 配送方式是否支持货到付款 */
    $modules[$i]['cod']     = TRUE;
    
    /* 插件的作者 */
    $modules[$i]['author']  = "億商互動";
    
    /* 插件作者的官方网站 */
    $modules[$i]['website'] = "http://www.myecshop.com";

    /* 配送接口需要的参数 */
    $modules[$i]['configure'] = array(
                                        array('name' => 'base_fee',     'value'=>100),
                                );

    return;
}

class tjoin
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
     * 构造函数
     * 
     * @param: $configure[array]    配送方式的参数的数组
     *
     * @return null
     */
    function tjoin($cfg=array())
    {
        foreach ($cfg AS $key=>$val)
        {
            $this->configure[$val['name']] = $val['value'];
        }
    }


    /**
     * 计算订单的配送费用的函数
     * 
     * @return decimal
     */
    function calculate()
    {
        $cart = cart_weight_price();        // 获得商品总重量以及总金额

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
     * 查询发货状态
     * 该配送方式不支持查询发货状态
     *
     * @access  public
     * @param   string  $invoice_sn     发货单号
     * @return  string
     */
    function query($invoice_sn)
    {
        $form_str = '商品透過大榮貨運發出<br>您的宅配單號如下<form name="frmtjoin" method="post" target=_blank action="http://www.tjoin.com/query/webquery2/queryc2-1.asp" >';
        $form_str .= '<input name="p1" type="hidden" value='.$invoice_sn.'>';
		$form_str .= '<input type="Submit" value="'.$invoice_sn.'"></form>';
        return $form_str;
    }
};

?>