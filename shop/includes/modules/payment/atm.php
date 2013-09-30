<?php

/**
 * ECSHOP 付款外掛程式
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
 * $Id: cod.php 3819 2006-12-28 07:10:34Z paulgao $
 */
$payment_lang = ROOT_PATH.'languages/' .$GLOBALS['_CFG']['lang']. '/payment/atm.php';
if (file_exists($payment_lang))
{
    global $_LANG;
    include_once($payment_lang);
}
/**
 * 模块信息
 */
if (isset($set_modules) && $set_modules == TRUE)
{
    $i = isset($modules) ? count($modules) : 0;
    
    /* 代码 */
    $modules[$i]['code'] = basename(__FILE__, '.php');
    
    /* 描述对应的语言项 */
    $modules[$i]['desc'] = 'atm_desc';
    
    /* 是否支持货到付款 */
    $modules[$i]['is_cod'] = '0';
    
    /* 作者 */
    $modules[$i]['author'] = '億商互動';
    
    /* 网址 */
    $modules[$i]['website'] = 'http://www.myecshop.com';
    
    /* 版本号 */
    $modules[$i]['version'] = '1.0.0';
    
    /* 配置信息 */
    $modules[$i]['config'] = array();
    
    return;
}

/**
 * 类
 */
class atm
{
    /**
     * 提交函数
     */
    function get_code()
    {
        return '';
    }
    
    /**
     * 处理函数
     */
    function response()
    {
        return;
    }
}

?>