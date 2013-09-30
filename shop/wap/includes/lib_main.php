<?php

/**
 * ECSHOP wap前台公共函數
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: lib_main.php 17063 2010-03-25 06:35:46Z liuhui $
*/

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

/**
 * 對輸出編碼
 *
 * @access  public
 * @param   string   $str
 * @return  string
 */
function encode_output($str)
{
    if (EC_CHARSET != 'utf-8')
    {
        $str = ecs_iconv(EC_CHARSET, 'utf-8', $str);
    }
    return htmlspecialchars($str);
}

/**
 * wap分頁函數
 *
 * @access      public
 * @param       int     $num        總記錄數
 * @param       int     $perpage    每頁記錄數
 * @param       int     $curr_page  當前頁數
 * @param       string  $mpurl      傳入的連接地址
 * @param       string  $pvar       分頁變量
 */
function get_wap_pager($num, $perpage, $curr_page, $mpurl,$pvar)
{
    $multipage = '';
    if($num > $perpage)
    {
        $page = 2;
        $offset = 1;
        $pages = ceil($num / $perpage);
        $all_pages = $pages;
        $tmp_page = $curr_page;
        $setp = strpos($mpurl, '?') === false ? "?" : '&amp;';
        if($curr_page > 1)
        {
            $multipage .= "<a href=\"$mpurl${setp}${pvar}=".($curr_page-1)."\">上一頁</a>";
        }
        $multipage .= $curr_page."/".$pages;
        if(($curr_page++) < $pages)
        {
            $multipage .= "<a href=\"$mpurl${setp}${pvar}=".$curr_page++."\">下一頁</a><br/>";
        }
        //$multipage .= $pages > $page ? " ... <a href=\"$mpurl&amp;$pvar=$pages\"> [$pages] &gt;&gt;</a>" : " 頁/".$all_pages."頁";
        $url_array = explode("?" , $mpurl);
        $field_str = "";
        if (isset($url_array[1]))
        {
            $filed_array = explode("&amp;" , $url_array[1]);
            if (count($filed_array) > 0)
            {
                foreach ($filed_array AS $data)
                {
                    $value_array = explode("=" , $data);
                    $field_str .= "<postfield name='".$value_array[0]."' value='".encode_output($value_array[1])."'/>\n";
                }
            }
        }
        $multipage .= "跳轉到第<input type='text' name='pageno' format='*N' size='4' value='' maxlength='2' emptyok='true' />頁<anchor>[GO]<go href='{$url_array[0]}' method='get'>{$field_str}<postfield name='".$pvar."' value='$(pageno)'/></go></anchor>";
        //<postfield name='snid' value='".session_id()."'/>
    }
    return $multipage;
}

/**
 * 返回尾文件
 *
 * @return  string
 */
function get_footer()
{
    if (substr($_SERVER['SCRIPT_NAME'], strrpos($_SERVER['SCRIPT_NAME'], '/')) == '/index.php')
    {
        $footer = "<br/>Powered by ECShop[".local_date('H:i')."]";
    }
    else
    {
        $footer = "<br/><select><option onpick='index.php'>快速通道</option><option onpick='goods_list.php?type=best'>精品推薦</option><option onpick='goods_list.php?type=promote'>商家促銷</option><option onpick='goods_list.php?type=hot'>熱門商品</option><option onpick='goods_list.php?type=new'>最新產品</option></select>";
    }

    return $footer;
}

?>