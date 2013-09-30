<?php

/**
 * ECSHOP 支付插件排序文件
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: lib_compositor.php 2009-07-24 09:31:42Z liuhui $
 */

if(isset($modules))
{

    /* 將財付通提升至第二個顯示 */
    foreach ($modules as $k =>$v)
    {
        if($v['pay_code'] == 'tenpay')
        {
            $tenpay = $modules[$k];
            unset($modules[$k]);
            array_unshift($modules, $tenpay);
        }
    }
    /* 將快錢直連銀行顯示在快錢之後 */
    foreach ($modules as $k =>$v)
    {
        if(strpos($v['pay_code'], 'kuaiqian')!== false)
        {
            $tenpay = $modules[$k];
            unset($modules[$k]);
            array_unshift($modules, $tenpay);
        }
    }

    /* 將快錢提升至第一個顯示 */
    foreach ($modules as $k =>$v)
    {
        if($v['pay_code'] == 'kuaiqian')
        {
            $tenpay = $modules[$k];
            unset($modules[$k]);
            array_unshift($modules, $tenpay);
        }
    }

}

?>