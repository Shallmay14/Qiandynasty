<?php

/**
 * ECSHOP 會員數據處理類
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com
 * ----------------------------------------------------------------------------
 * 這是一個免費開源的軟件；這意味著您可以在不用於商業目的的前提下對程序代碼
 * 進行修改、使用和再發布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: dvbbs.php 17063 2010-03-25 06:35:46Z liuhui $
 */

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

/* 模塊的基本信息 */
if (isset($set_modules) && $set_modules == TRUE)
{
    $i = (isset($modules)) ? count($modules) : 0;

    /* 會員數據整合插件的代碼必須和文件名保持一致 */
    $modules[$i]['code']    = 'dvbbs';

    /* 被整合的第三方程序的名稱 */
    $modules[$i]['name']    = 'DVBBS for PHP';

    /* 被整合的第三方程序的版本 */
    $modules[$i]['version'] = '1.0.x/2.0';

    /* 插件的作者 */
    $modules[$i]['author']  = 'ECSHOP R&D TEAM';

    /* 插件作者的官方網站 */
    $modules[$i]['website'] = 'http://www.ecshop.com';

    /* 插件的初始的默認值 */
    $modules[$i]['default']['db_host'] = 'localhost';
    $modules[$i]['default']['db_user'] = 'root';
    $modules[$i]['default']['prefix'] = 'dv_';
    $modules[$i]['default']['cookie_prefix'] = 'dvbbs_';

    return;
}

require_once(ROOT_PATH . 'includes/modules/integrates/integrate.php');
class dvbbs extends integrate
{
    var $cookie_prefix = '';
    var $authkey = '';

    function __construct($cfg)
    {
        $this->dvbbs($cfg);
    }

    /**
     *
     *
     * @access  public
     * @param
     *
     * @return void
     */
    function dvbbs($cfg)
    {
        parent::integrate($cfg);
        if ($this->error)
        {
            /* 數據庫連接出錯 */
            return false;
        }
        $this->cookie_prefix = $cfg['cookie_prefix'];
        $this->field_id = 'userid';
        $this->field_name = 'username';
        $this->field_email = 'useremail';
        $this->field_gender = 'usersex';
        $this->field_bday = 'userbirthday';
        $this->field_pass = 'userpassword';
        $this->field_reg_date = 'joindate';
        $this->user_table = 'user';


        /* 檢查數據表是否存在 */
        $sql = "SHOW TABLES LIKE '" . $this->prefix . "%'";

        $exist_tables = $this->db->getCol($sql);

        if (empty($exist_tables) || (!in_array($this->prefix.$this->user_table, $exist_tables)))
        {
            $this->error = 2;
            /* 缺少數據表 */
            return false;
        }
    }

    /**
     *  獲取論壇有效積分及單位
     *
     * @access  public
     * @param
     *
     * @return void
     */
    function get_points_name ()
    {
        static $ava_credits = NULL;
        if ($ava_credits === NULL)
        {
            $ava_credits = array();
            $ava_credits['usermoney'] = array('title'=>'金錢', 'unit'=>'');
            $ava_credits['userep'] = array('title'=>'經驗', 'unit'=>'');
            $ava_credits['usercp'] = array('title'=>'魅力', 'unit'=>'');
        }

        return $ava_credits;
    }

    /**
     *  獲取用戶積分
     *
     * @access  public
     * @param
     *
     * @return array
     */
    function get_points($username)
    {
        $credits = $this->get_points_name();
        $fileds = array_keys($credits);
        if ($fileds)
        {
            if ($this->charset != 'UTF8')
            {
                $username = ecs_iconv('UTF8', $this->charset,  $username);
            }
            $sql = "SELECT " . $this->field_id . ', ' . implode(', ',$fileds).
                   " FROM " . $this->table($this->user_table).
                   " WHERE " . $this->field_name . "='$username'";
            $row = $this->db->getRow($sql);
            return $row;
        }
        else
        {
            return false;
        }
    }

    /**
     *
     *
     * @access  public
     * @param
     *
     * @return void
     */
    function set_points ($username, $credits)
    {
        $user_set = array_keys($credits);
        $points_set = array_keys($this->get_points_name());

        $set = array_intersect($user_set, $points_set);

        if ($set)
        {
            if ($this->charset != 'UTF8')
            {
                $username = ecs_iconv('UTF8', $this->charset,  $username);
            }
            $tmp = array();
            foreach ($set as $credit)
            {
               $tmp[] = $credit . '=' . $credit . '+' . $credits[$credit];
            }
            $sql = "UPDATE " . $this->table($this->user_table).
                   " SET " . implode(', ', $tmp).
                   " WHERE " . $this->field_name . " = '$username'";
            $this->db->query($sql);
        }

        return true;
    }

    /**
     *  設置論壇cookie
     *
     * @access  public
     * @param
     *
     * @return void
     */
    function set_cookie ($username="")
    {
        parent::set_cookie($username);
        if (empty($username))
        {
            $time = time() - 3600;
            setcookie($this->cookie_prefix . 'userid', '', $time, $this->cookie_path, $this->cookie_domain);
            setcookie($this->cookie_prefix . 'username', '', $time, $this->cookie_path, $this->cookie_domain);
            setcookie($this->cookie_prefix . 'password', '', $time, $this->cookie_path, $this->cookie_domain);
            setcookie($this->cookie_prefix . 'userhidden', '', $time, $this->cookie_path, $this->cookie_domain);
            setcookie($this->cookie_prefix . 'onlinecachetime', '', $time, $this->cookie_path, $this->cookie_domain);
        }
        else
        {
            if ($this->charset != 'UTF8')
            {
                $username = ecs_iconv('UTF8', $this->charset, $username);
            }
            $sql = "SELECT " . $this->field_id . " AS user_id, truepassword, userhidden ".
                   " FROM " . $this->table($this->user_table) . " WHERE " . $this->field_name . "='$username'";

            $row = $this->db->getRow($sql);

            setcookie($this->cookie_prefix . 'userid', $row['user_id'], time() + 3600 * 24 * 30, $this->cookie_path, $this->cookie_domain);
            setcookie($this->cookie_prefix . 'username', $username, time() + 3600 * 24 * 30, $this->cookie_path, $this->cookie_domain);
            setcookie($this->cookie_prefix . 'password', $row['truepassword'], time() + 3600 * 24 * 30, $this->cookie_path, $this->cookie_domain);
            setcookie($this->cookie_prefix . 'userhidden', $row['userhidden'], time() + 3600 * 24 * 30, $this->cookie_path, $this->cookie_domain);
        }
    }

    /**
     * 檢查cookie
     *
     * @access  public
     * @param
     *
     * @return void
     */
    function check_cookie ()
    {
        if (empty($_COOKIE[$this->cookie_prefix . 'userid']) || empty($_COOKIE[$this->cookie_prefix . 'password']))
        {
            return '';
        }

        $user_id = intval($_COOKIE[$this->cookie_prefix . 'userid']);
        $true_password = addslashes_deep($_COOKIE[$this->cookie_prefix . 'password']);

        $sql = "SELECT  ". $this->field_name . " AS user_name ".
               " FROM " . $this->table($this->user_table) .
               " WHERE " . $this->field_id . "='$user_id' AND truepassword='$true_password'";

        $username = $this->db->getOne($sql);

        if (empty($username))
        {
            return '';
        }

        if ($this->charset != 'UTF8')
        {
            $username = ecs_iconv($this->charset, 'UTF8', $username);
        }

        return $username;
    }

/**
     *  編譯密碼函數
     *
     * @access  public
     * @param   array   $cfg 包含參數為 $password, $md5password, $salt, $type
     *
     * @return void
     */
    function compile_password ($cfg)
    {
       if ((!empty($cfg['password'])) && empty($cfg['md5password']))
       {
            $cfg['md5password'] = md5($cfg['password']);
       }

       if (empty($cfg['md5password']))
       {
           return '';
       }

       return substr($cfg['md5password'], 8, 16);

    }


}