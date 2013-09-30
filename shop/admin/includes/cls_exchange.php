<?php

/**
 * ECSHOP 後台自動操作數據庫的類文件
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: cls_exchange.php 17063 2010-03-25 06:35:46Z liuhui $
*/

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

/*------------------------------------------------------ */
//-- 該類用於與數據庫數據進行交換
/*------------------------------------------------------ */
class exchange
{
    var $table;
    var $db;
    var $id;
    var $name;
    var $error_msg;

    /**
     * 構造函數
     *
     * @access  public
     * @param   string       $table       數據庫表名
     * @param   dbobject     $db          aodb的對象
     * @param   string       $id          數據表主鍵字段名
     * @param   string       $name        數據表重要段名
     *
     * @return void
     */
    function exchange($table, &$db , $id, $name)
    {
        $this->table     = $table;
        $this->db        = &$db;
        $this->id        = $id;
        $this->name      = $name;
        $this->error_msg = '';
    }

    /**
     * 判斷表中某字段是否重複，若重複則中止程序，並給出錯誤信息
     *
     * @access  public
     * @param   string  $col    字段名
     * @param   string  $name   字段值
     * @param   integer $id
     *
     * @return void
     */
    function is_only($col, $name, $id = 0, $where='')
    {
        $sql = 'SELECT COUNT(*) FROM ' .$this->table. " WHERE $col = '$name'";
        $sql .= empty($id) ? '' : ' AND ' . $this->id . " <> '$id'";
        $sql .= empty($where) ? '' : ' AND ' .$where;

        return ($this->db->getOne($sql) == 0);
    }

    /**
     * 返回指定名稱記錄再數據表中記錄個數
     *
     * @access  public
     * @param   string      $col        字段名
     * @param   string      $name       字段內容
     *
     * @return   int        記錄個數
     */
    function num($col, $name, $id = 0)
    {
        $sql = 'SELECT COUNT(*) FROM ' .$this->table. " WHERE $col = '$name'";
        $sql .= empty($id) ? '' : ' AND '. $this->id ." != '$id' ";

        return $this->db->getOne($sql);
    }

    /**
     * 編輯某個字段
     *
     * @access  public
     * @param   string      $set        要更新集合如" col = '$name', value = '$value'"
     * @param   int         $id         要更新的記錄編號
     *
     * @return bool     成功或失敗
     */
    function edit($set, $id)
    {
        $sql = 'UPDATE ' . $this->table . ' SET ' . $set . " WHERE $this->id = '$id'";

        if ($this->db->query($sql))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * 取得某個字段的值
     *
     * @access  public
     * @param   int     $id     記錄編號
     * @param   string  $id     字段名
     *
     * @return string   取出的數據
     */
    function get_name($id, $name = '')
    {
        if (empty($name))
        {
            $name = $this->name;
        }

        $sql = "SELECT `$name` FROM " . $this->table . " WHERE $this->id = '$id'";

        return $this->db->getOne($sql);
    }

    /**
     * 刪除條記錄
     *
     * @access  public
     * @param   int         $id         記錄編號
     *
     * @return bool
     */
    function drop($id)
    {
        $sql = 'DELETE FROM ' . $this->table . " WHERE $this->id = '$id'";

        return $this->db->query($sql);
    }
}

?>