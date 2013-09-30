<?php

/**
 * ECSHOP 驗證碼圖片類
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: cls_captcha.php 17063 2010-03-25 06:35:46Z liuhui $
*/

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

class captcha
{
    /**
     * 背景圖片所在目錄
     *
     * @var string  $folder
     */
    var $folder     = 'data/captcha';

    /**
     * 圖片的文件類型
     *
     * @var string  $img_type
     */
    var $img_type   = 'png';

    /*------------------------------------------------------ */
    //-- 存在session中的名稱
    /*------------------------------------------------------ */
    var $session_word = 'captcha_word';

    /**
     * 背景圖片以及背景顏色
     *
     * 0 => 背景圖片的文件名
     * 1 => Red, 2 => Green, 3 => Blue
     * @var array   $themes
     */
    var $themes_jpg = array(
        1 => array('captcha_bg1.jpg', 255, 255, 255),
        2 => array('captcha_bg2.jpg', 0, 0, 0),
        3 => array('captcha_bg3.jpg', 0, 0, 0),
        4 => array('captcha_bg4.jpg', 255, 255, 255),
        5 => array('captcha_bg5.jpg', 255, 255, 255),
    );

    var $themes_gif = array(
        1 => array('captcha_bg1.gif', 255, 255, 255),
        2 => array('captcha_bg2.gif', 0, 0, 0),
        3 => array('captcha_bg3.gif', 0, 0, 0),
        4 => array('captcha_bg4.gif', 255, 255, 255),
        5 => array('captcha_bg5.gif', 255, 255, 255),
    );

    /**
     * 圖片的寬度
     *
     * @var integer $width
     */
    var $width      = 130;

    /**
     * 圖片的高度
     *
     * @var integer $height
     */
    var $height     = 20;

    /**
     * 構造函數
     *
     * @access  public
     * @param   string  $folder     背景圖片所在目錄
     * @param   integer $width      圖片寬度
     * @param   integer $height     圖片高度
     * @return  bool
     */
    function captcha($folder = '', $width = 145, $height = 20)
    {
        if (!empty($folder))
        {
            $this->folder = $folder;
        }

        $this->width    = $width;
        $this->height   = $height;

        /* 檢查是否支持 GD */
        if (PHP_VERSION >= '4.3')
        {

            return (function_exists('imagecreatetruecolor') || function_exists('imagecreate'));
        }
        else
        {

            return (((imagetypes() & IMG_GIF) > 0) || ((imagetypes() & IMG_JPG)) > 0 );
        }
    }

    /**
     * 構造函數
     *
     * @access  public
     * @param
     *
     * @return void
     */
    function __construct($folder = '', $width = 145, $height = 20)
    {
        $this->captcha($folder, $width, $height);
    }


    /**
     * 檢查給出的驗證碼是否和session中的一致
     *
     * @access  public
     * @param   string  $word   驗證碼
     * @return  bool
     */
    function check_word($word)
    {
        $recorded = isset($_SESSION[$this->session_word]) ? base64_decode($_SESSION[$this->session_word]) : '';
        $given    = $this->encrypts_word(strtoupper($word));

        return (preg_match("/$given/", $recorded));
    }

    /**
     * 生成圖片並輸出到瀏覽器
     *
     * @access  public
     * @param   string  $word   驗證碼
     * @return  mix
     */
    function generate_image($word = false)
    {
        if (!$word)
        {
            $word = $this->generate_word();
        }

        /* 記錄驗證碼到session */
        $this->record_word($word);

        /* 驗證碼長度 */
        $letters = strlen($word);

        /* 選擇一個隨機的方案 */
        mt_srand((double) microtime() * 1000000);

        if (function_exists('imagecreatefromjpeg') && ((imagetypes() & IMG_JPG) > 0))
        {
            $theme  = $this->themes_jpg[mt_rand(1, count($this->themes_jpg))];
        }
        else
        {
            $theme  = $this->themes_gif[mt_rand(1, count($this->themes_gif))];
        }

        if (!file_exists($this->folder . $theme[0]))
        {
            return false;
        }
        else
        {
            $img_bg    = (function_exists('imagecreatefromjpeg') && ((imagetypes() & IMG_JPG) > 0)) ?
                            imagecreatefromjpeg($this->folder . $theme[0]) : imagecreatefromgif($this->folder . $theme[0]);
            $bg_width  = imagesx($img_bg);
            $bg_height = imagesy($img_bg);

            $img_org   = ((function_exists('imagecreatetruecolor')) && PHP_VERSION >= '4.3') ?
                          imagecreatetruecolor($this->width, $this->height) : imagecreate($this->width, $this->height);

            /* 將背景圖象複製原始圖象並調整大小 */
            if (function_exists('imagecopyresampled') && PHP_VERSION >= '4.3') // GD 2.x
            {
                imagecopyresampled($img_org, $img_bg, 0, 0, 0, 0, $this->width, $this->height, $bg_width, $bg_height);
            }
            else // GD 1.x
            {
                imagecopyresized($img_org, $img_bg, 0, 0, 0, 0, $this->width, $this->height, $bg_width, $bg_height);
            }
            imagedestroy($img_bg);

            $clr = imagecolorallocate($img_org, $theme[1], $theme[2], $theme[3]);

            /* 繪製邊框 */
            //imagerectangle($img_org, 0, 0, $this->width - 1, $this->height - 1, $clr);

            /* 獲得驗證碼的高度和寬度 */
            $x = ($this->width - (imagefontwidth(5) * $letters)) / 2;
            $y = ($this->height - imagefontheight(5)) / 2;
            imagestring($img_org, 5, $x, $y, $word, $clr);

            header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');

            // HTTP/1.1
            header('Cache-Control: private, no-store, no-cache, must-revalidate');
            header('Cache-Control: post-check=0, pre-check=0, max-age=0', false);

            // HTTP/1.0
            header('Pragma: no-cache');
            if ($this->img_type == 'jpeg' && function_exists('imagecreatefromjpeg'))
            {
                header('Content-type: image/jpeg');
                imageinterlace($img_org, 1);
                imagejpeg($img_org, false, 95);
            }
            else
            {
                header('Content-type: image/png');
                imagepng($img_org);
            }

            imagedestroy($img_org);

            return true;
        }
    }

    /*------------------------------------------------------ */
    //-- PRIVATE METHODs
    /*------------------------------------------------------ */

    /**
     * 對需要記錄的串進行加密
     *
     * @access  private
     * @param   string  $word   原始字符串
     * @return  string
     */
    function encrypts_word($word)
    {
        return substr(md5($word), 1, 10);
    }

    /**
     * 將驗證碼保存到session
     *
     * @access  private
     * @param   string  $word   原始字符串
     * @return  void
     */
    function record_word($word)
    {
        $_SESSION[$this->session_word] = base64_encode($this->encrypts_word($word));
    }

    /**
     * 生成隨機的驗證碼
     *
     * @access  private
     * @param   integer $length     驗證碼長度
     * @return  string
     */
    function generate_word($length = 4)
    {
        $chars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';

        for ($i = 0, $count = strlen($chars); $i < $count; $i++)
        {
            $arr[$i] = $chars[$i];
        }

        mt_srand((double) microtime() * 1000000);
        shuffle($arr);

        return substr(implode('', $arr), 5, $length);
    }
}

?>