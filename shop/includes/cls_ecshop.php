<?php

/**
 * ECSHOP 基礎類
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: cls_ecshop.php 17171 2010-06-04 06:14:00Z liuhui $
*/

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

define('APPNAME', 'ECSHOP');
define('VERSION', 'v2.7.2');
define('RELEASE', '20100604');

class ECS
{
    var $db_name = '';
    var $prefix  = 'ecs_';

    /**
     * 構造函數
     *
     * @access  public
     * @param   string      $ver        版本號
     *
     * @return  void
     */
    function ECS($db_name, $prefix)
    {
        $this->db_name = $db_name;
        $this->prefix  = $prefix;
    }

    /**
     * 將指定的表名加上前綴後返回
     *
     * @access  public
     * @param   string      $str        表名
     *
     * @return  string
     */
    function table($str)
    {
        return '`' . $this->db_name . '`.`' . $this->prefix . $str . '`';
    }

    /**
     * ECSHOP 密碼編譯方法;
     *
     * @access  public
     * @param   string      $pass       需要編譯的原始密碼
     *
     * @return  string
     */
    function compile_password($pass)
    {
        return md5($pass);
    }

    /**
     * 取得當前的域名
     *
     * @access  public
     *
     * @return  string      當前的域名
     */
    function get_domain()
    {
        /* 協議 */
        $protocol = $this->http();

        /* 域名或IP地址 */
        if (isset($_SERVER['HTTP_X_FORWARDED_HOST']))
        {
            $host = $_SERVER['HTTP_X_FORWARDED_HOST'];
        }
        elseif (isset($_SERVER['HTTP_HOST']))
        {
            $host = $_SERVER['HTTP_HOST'];
        }
        else
        {
            /* 端口 */
            if (isset($_SERVER['SERVER_PORT']))
            {
                $port = ':' . $_SERVER['SERVER_PORT'];

                if ((':80' == $port && 'http://' == $protocol) || (':443' == $port && 'https://' == $protocol))
                {
                    $port = '';
                }
            }
            else
            {
                $port = '';
            }

            if (isset($_SERVER['SERVER_NAME']))
            {
                $host = $_SERVER['SERVER_NAME'] . $port;
            }
            elseif (isset($_SERVER['SERVER_ADDR']))
            {
                $host = $_SERVER['SERVER_ADDR'] . $port;
            }
        }

        return $protocol . $host;
    }

    /**
     * 獲得 ECSHOP 當前環境的 URL 地址
     *
     * @access  public
     *
     * @return  void
     */
    function url()
    {
        $curr = strpos(PHP_SELF, ADMIN_PATH . '/') !== false ?
                preg_replace('/(.*)(' . ADMIN_PATH . ')(\/?)(.)*/i', '\1', dirname(PHP_SELF)) :
                dirname(PHP_SELF);

        $root = str_replace('\\', '/', $curr);

        if (substr($root, -1) != '/')
        {
            $root .= '/';
        }

        return $this->get_domain() . $root;
    }

    /**
     * 獲得 ECSHOP 當前環境的 HTTP 協議方式
     *
     * @access  public
     *
     * @return  void
     */
    function http()
    {
        return (isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off')) ? 'https://' : 'http://';
    }

    /**
     * 獲得數據目錄的路徑
     *
     * @param int $sid
     *
     * @return string 路徑
     */
    function data_dir($sid = 0)
    {
        if (empty($sid))
        {
            $s = 'data';
        }
        else
        {
            $s = 'user_files/';
            $s .= ceil($sid / 3000) . '/';
            $s .= $sid % 3000;
        }
        return $s;
    }

    /**
     * 獲得圖片的目錄路徑
     *
     * @param int $sid
     *
     * @return string 路徑
     */
    function image_dir($sid = 0)
    {
        if (empty($sid))
        {
            $s = 'images';
        }
        else
        {
            $s = 'user_files/';
            $s .= ceil($sid / 3000) . '/';
            $s .= ($sid % 3000) . '/';
            $s .= 'images';
        }
        return $s;
    }

}

?>