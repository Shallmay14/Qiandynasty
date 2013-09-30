<?php

/**
 * ECSHOP Google sitemap 類
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: cls_google_sitemap.php 17063 2010-03-25 06:35:46Z liuhui $
*/

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

class google_sitemap
{
    var $header = "<\x3Fxml version=\"1.0\" encoding=\"UTF-8\"\x3F>\n\t<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">";
    var $charset = "UTF-8";
    var $footer = "\t</urlset>\n";
    var $items = array();

    /**
     * 增加一個新的子項
     *@access   public
     *@param    google_sitemap  item    $new_item
     */
    function add_item($new_item)
    {
        $this->items[] = $new_item;
    }

    /**
     * 生成XML文檔
     *@access    public
     *@param     string  $file_name  如果提供了文件名則生成文件，否則返回字符串.
     *@return [void|string]
     */
    function build( $file_name = null )
    {
        $map = $this->header . "\n";

        foreach ($this->items AS $item)
        {
            $item->loc = htmlentities($item->loc, ENT_QUOTES);
            $map .= "\t\t<url>\n\t\t\t<loc>$item->loc</loc>\n";

            // lastmod
            if ( !empty( $item->lastmod ) )
                $map .= "\t\t\t<lastmod>$item->lastmod</lastmod>\n";

            // changefreq
            if ( !empty( $item->changefreq ) )
                $map .= "\t\t\t<changefreq>$item->changefreq</changefreq>\n";

            // priority
            if ( !empty( $item->priority ) )
                $map .= "\t\t\t<priority>$item->priority</priority>\n";

            $map .= "\t\t</url>\n\n";
        }

        $map .= $this->footer . "\n";

        if (!is_null($file_name))
        {
            return file_put_contents($file_name, $map);
        }
        else
        {
            return $map;
        }
    }

}

class google_sitemap_item
{
    /**
     *@access   public
     *@param    string  $loc        位置
     *@param    string  $lastmod    日期格式 YYYY-MM-DD
     *@param    string  $changefreq 更新頻率的單位 (always, hourly, daily, weekly, monthly, yearly, never)
     *@param    string  $priority   更新頻率 0-1
     */
    function google_sitemap_item($loc, $lastmod = '', $changefreq = '', $priority = '')
    {
        $this->loc = $loc;
        $this->lastmod = $lastmod;
        $this->changefreq = $changefreq;
        $this->priority = $priority;
    }
}

?>