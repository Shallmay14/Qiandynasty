<?php

/**
 * ECSHOP 用戶級錯誤處理類
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: cls_error.php 17063 2010-03-25 06:35:46Z liuhui $
*/

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

class ecs_error
{
    var $_message   = array();
    var $_template  = '';
    var $error_no   = 0;

    /**
     * 構造函數
     *
     * @access  public
     * @param   string  $tpl
     * @return  void
     */
    function __construct($tpl)
    {
        $this->ecs_error($tpl);
    }

    /**
     * 構造函數
     *
     * @access  public
     * @param   string  $tpl
     * @return  void
     */
    function ecs_error($tpl)
    {
        $this->_template = $tpl;
    }

    /**
     * 添加一條錯誤信息
     *
     * @access  public
     * @param   string  $msg
     * @param   integer $errno
     * @return  void
     */
    function add($msg, $errno=1)
    {
        if (is_array($msg))
        {
            $this->_message = array_merge($this->_message, $msg);
        }
        else
        {
            $this->_message[] = $msg;
        }

        $this->error_no     = $errno;
    }

    /**
     * 清空錯誤信息
     *
     * @access  public
     * @return  void
     */
    function clean()
    {
        $this->_message = array();
        $this->error_no = 0;
    }

    /**
     * 返回所有的錯誤信息的數組
     *
     * @access  public
     * @return  array
     */
    function get_all()
    {
        return $this->_message;
    }

    /**
     * 返回最後一條錯誤信息
     *
     * @access  public
     * @return  void
     */
    function last_message()
    {
        return array_slice($this->_message, -1);
    }

    /**
     * 顯示錯誤信息
     *
     * @access  public
     * @param   string  $link
     * @param   string  $href
     * @return  void
     */
    function show($link = '', $href = '')
    {
        if ($this->error_no > 0)
        {
            $message = array();

            $link = (empty($link)) ? $GLOBALS['_LANG']['back_up_page'] : $link;
            $href = (empty($href)) ? 'javascript:history.back();' : $href;
            $message['url_info'][$link] = $href;
            $message['back_url'] = $href;

            foreach ($this->_message AS $msg)
            {
                $message['content'] = '<div>' . htmlspecialchars($msg) . '</div>';
            }

            if (isset($GLOBALS['smarty']))
            {
                assign_template();
                $GLOBALS['smarty']->assign('auto_redirect', true);
                $GLOBALS['smarty']->assign('message', $message);
                $GLOBALS['smarty']->display($this->_template);
            }
            else
            {
                die($message['content']);
            }

            exit;
        }
    }
}

?>