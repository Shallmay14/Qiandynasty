<?php

/**
 * ECSHOP 付款外掛程式
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
 * $Id: cod.php 3819 2006-12-28 07:10:34Z paulgao $
 */
$payment_lang = ROOT_PATH.'languages/' .$GLOBALS['_CFG']['lang']. '/payment/trans.php';
if (file_exists($payment_lang))
{
    global $_LANG;
    include_once($payment_lang);
}
/**
 * 模塊信息
 */
if (isset($set_modules) && $set_modules == TRUE)
{
    $i = isset($modules) ? count($modules) : 0;
    
    /* 代碼 */
    $modules[$i]['code'] = basename(__FILE__, '.php');
    
    /* 描述對應的語言項 */
    $modules[$i]['desc'] = 'trans_desc';
    
    /* 是否支持貨到付款 */
    $modules[$i]['is_cod'] = '0';
    
    /* 作者 */
    $modules[$i]['author'] = '洛科資訊';
    
    /* 網址 */
    $modules[$i]['website'] = 'http://www.localsoft.tw';
    
    /* 版本號 */
    $modules[$i]['version'] = '1.0.0';
    
    /* 配置信息 */
    $modules[$i]['config'] = array();
    
    return;
}

/**
 * 類
 */
class trans
{
    /**
     * 提交函數
     */
    function get_code()
    {
        return '';
    }
    
    /**
     * 處理函數
     */
    function response()
    {
        return;
    }
}

?>