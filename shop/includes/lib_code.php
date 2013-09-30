<?php

/**
 * ECSHOP 加密解密類
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: lib_code.php 17063 2010-03-25 06:35:46Z liuhui $
 */

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}


/**
 * 加密函數
 * @param   string  $str    加密前的字符串
 * @param   string  $key    密鑰
 * @return  string  加密後的字符串
 */
function encrypt($str, $key = AUTH_KEY)
{
    $coded = '';
    $keylength = strlen($key);

    for ($i = 0, $count = strlen($str); $i < $count; $i += $keylength)
    {
        $coded .= substr($str, $i, $keylength) ^ $key;
    }

    return str_replace('=', '', base64_encode($coded));
}

/**
 * 解密函數
 * @param   string  $str    加密後的字符串
 * @param   string  $key    密鑰
 * @return  string  加密前的字符串
 */
function decrypt($str, $key = AUTH_KEY)
{
    $coded = '';
    $keylength = strlen($key);
    $str = base64_decode($str);

    for ($i = 0, $count = strlen($str); $i < $count; $i += $keylength)
    {
        $coded .= substr($str, $i, $keylength) ^ $key;
    }

    return $coded;
}

?>