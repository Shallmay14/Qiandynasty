<?php

/**
 * ECSHOP 模板類
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: cls_template.php 17062 2010-03-25 06:25:22Z liuhui $
 */

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

class template
{
    /**
    * 用來存儲變量的空間
    *
    * @access  private
    * @var     array      $vars
    */
    var $vars = array();

   /**
    * 模板存放的目錄路徑
    *
    * @access  private
    * @var     string      $path
    */
    var $path = '';

    /**
     * 構造函數
     *
     * @access  public
     * @param   string       $path
     * @return  void
     */
    function __construct($path)
    {
        $this->template($path);
    }

    /**
     * 構造函數
     *
     * @access  public
     * @param   string       $path
     * @return  void
     */
    function template($path)
    {
        $this->path = $path;
    }

    /**
     * 模擬smarty的assign函數
     *
     * @access  public
     * @param   string       $name    變量的名字
     * @param   mix           $value   變量的值
     * @return  void
     */
    function assign($name, $value)
    {
        $this->vars[$name] = $value;
    }

    /**
     * 模擬smarty的fetch函數
     *
     * @access  public
     * @param   string       $file   文件相對路徑
     * @return  string      模板的內容(文本格式)
     */
    function fetch($file)
    {
        extract($this->vars);
        ob_start();
        include($this->path . $file);
        $contents = ob_get_contents();
        ob_end_clean();

        return $contents;
    }

    /**
     * 模擬smarty的display函數
     *
     * @access  public
     * @param   string       $file   文件相對路徑
     * @return  void
     */
    function display($file)
    {
        echo $this->fetch($file);
    }
}

?>