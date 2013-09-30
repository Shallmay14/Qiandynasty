<?php

/**
 * ECSHOP SQL語句執行類。在調用該類方法之前，請參看相應方法的說明。
 *
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: cls_sql_executor.php 17063 2010-03-25 06:35:46Z liuhui $
 */

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

class sql_executor
{
    /**
     * 記錄程序執行過程中最後產生的那條錯誤信息
     *
     * @access  public
     * @var     string       $error
     */
    var $error = '';

    /**
     * 存儲將被忽略的錯誤號，這些錯誤不會記錄在$error屬性中，
     * 但仍然會記錄在錯誤日誌文件當中。
     *
     * @access  private
     * @var     array       $ignored_errors
     */
    var $ignored_errors = array();

    /**
     * MySQL對象
     *
     * @access  private
     * @var     object      $db
     */
    var $db = '';

    /**
     * 數據庫字符編碼
     *
     * @access   private
     * @var      string     $charset
     */
    var $db_charset = '';

    /**
     * 替換前表前綴
     *
     * @access  private
     * @var     string      $source_prefix
     */
    var $source_prefix = '';

    /**
     * 替換後表前綴
     *
     * @access  private
     * @var     string      $target_prefix
     */
    var $target_prefix = '';

    /**
     * 當發生錯誤時，程序將把日誌記錄在該指定的文件中
     *
     * @access  private
     * @var     string       $log_path
     */
    var $log_path = '';

    /**
     * 開啟此選項後，程序將進行智能化地查詢操作，即使重複運行本程序，也不會引起數據庫的查詢衝突。這點在瀏覽器
     * 和服務器之間進行通訊時是非常有必要的，因為網絡很有可能在您不經意間發生中斷。不過，由於用到了大量的正則
     * 表達式，開啟該選項後將非常耗費服務器的資源。
     *
     * @access  private
     * @var     boolean      $auto_match
     */
    var $auto_match = false;

    /**
     * 記錄當前正在執行的SQL文件名
     *
     * @access  private
     * @var     string       $current_file
     */
    var $current_file = 'Not a file, but a string.';

    /**
     * 構造函數
     *
     * @access  public
     * @param   mysql       $db             mysql類對象
     * @param   string      $charset        字符集
     * @param   string      $sprefix        替換前表前綴
     * @param   string      $tprefix        替換後表前綴
     * @param   string      $log_path       日誌路徑
     * @param   boolean     $auto_match     是否進行智能化查詢
     * @param   array       $ignored_errors 忽略的錯誤號數組
     * @return  void
     */
    function __construct($db, $charset = 'gbk', $sprefix = 'ecs_', $tprefix = 'ecs_', $log_path = '', $auto_match = false, $ignored_errors = array())
    {
        $this->sql_executor($db, $charset, $sprefix, $tprefix, $log_path, $auto_match, $ignored_errors);
    }

    /**
     * 構造函數
     *
     * @access  public
     * @param   mysql       $db             mysql類對象
     * @param   string      $charset        字符集
     * @param   string      $sprefix        替換前表前綴
     * @param   string      $tprefix        替換後表前綴
     * @param   string      $log_path       日誌路徑
     * @param   boolean     $auto_match     是否進行智能化查詢
     * @param   array       $ignored_errors 忽略的錯誤號數組
     * @return  void
     */
    function sql_executor($db, $charset = 'gbk', $sprefix = 'ecs_', $tprefix = 'ecs_', $log_path = '', $auto_match = false, $ignored_errors = array())
    {
        $this->db = $db;
        $this->db_charset = $charset;
        $this->source_prefix = $sprefix;
        $this->target_prefix = $tprefix;
        $this->log_path = $log_path;
        $this->auto_match = $auto_match;
        $this->ignored_errors = $ignored_errors;
    }

    /**
     * 執行所有SQL文件中所有的SQL語句
     *
     * @access  public
     * @param   array       $sql_files     文件絕對路徑組成的一維數組
     * @return  boolean     執行成功返回true，失敗返回false。
     */
    function run_all($sql_files)
    {
        /* 如果傳入參數不是數組，程序直接返回 */
        if (!is_array($sql_files))
        {
            return false;
        }

        foreach ($sql_files AS $sql_file)
        {
            $query_items = $this->parse_sql_file($sql_file);

            /* 如果解析失敗，則跳過 */
            if (!$query_items)
            {
                continue;
            }

            foreach ($query_items AS $query_item)
            {
                /* 如果查詢項為空，則跳過 */
                if (!$query_item)
                {
                    continue;
                }

                if (!$this->query($query_item))
                {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * 獲得分散的查詢項
     *
     * @access  public
     * @param   string      $file_path      文件的絕對路徑
     * @return  mixed       解析成功返回分散的查詢項數組，失敗返回false。
     */
    function parse_sql_file($file_path)
    {
        /* 如果SQL文件不存在則返回false */
        if (!file_exists($file_path))
        {
            return false;
        }

        /* 記錄當前正在運行的SQL文件 */
        $this->current_file = $file_path;

        /* 讀取SQL文件 */
        $sql = implode('', file($file_path));

        /* 刪除SQL注釋，由於執行的是replace操作，所以不需要進行檢測。下同。 */
        $sql = $this->remove_comment($sql);

        /* 刪除SQL串首尾的空白符 */
        $sql = trim($sql);

        /* 如果SQL文件中沒有查詢語句則返回false */
        if (!$sql)
        {
            return false;
        }

        /* 替換表前綴 */
        $sql = $this->replace_prefix($sql);

        /* 解析查詢項 */
        $sql = str_replace("\r", '', $sql);
        $query_items = explode(";\n", $sql);

        return $query_items;
    }

    /**
     * 執行某一個查詢項
     *
     * @access  public
     * @param   string      $query_item      查詢項
     * @return  boolean     成功返回true，失敗返回false。
     */
    function query($query_item)
    {
        /* 刪除查詢項首尾的空白符 */
        $query_item = trim($query_item);

        /* 如果查詢項為空則返回false */
        if (!$query_item)
        {
            return false;
        }

        /* 處理建表操作 */
        if (preg_match('/^\s*CREATE\s+TABLE\s*/i', $query_item))
        {
            if (!$this->create_table($query_item))
            {
                return false;
            }
        }
        /* 處理ALTER TABLE語句，此時程序將對表的結構進行修改 */
        elseif ($this->auto_match && preg_match('/^\s*ALTER\s+TABLE\s*/i', $query_item))
        {
            if (!$this->alter_table($query_item))
            {
                return false;
            }
        }
        /* 處理其它修改操作，如數據添加、更新、刪除等 */
        else
        {
            if (!$this->do_other($query_item))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * 過濾SQL查詢串中的注釋。該方法只過濾SQL文件中獨占一行或一塊的那些注釋。
     *
     * @access  public
     * @param   string      $sql        SQL查詢串
     * @return  string      返回已過濾掉注釋的SQL查詢串。
     */
    function remove_comment($sql)
    {
        /* 刪除SQL行注釋，行注釋不匹配換行符 */
        $sql = preg_replace('/^\s*(?:--|#).*/m', '', $sql);

        /* 刪除SQL塊注釋，匹配換行符，且為非貪婪匹配 */
        //$sql = preg_replace('/^\s*\/\*(?:.|\n)*\*\//m', '', $sql);
        $sql = preg_replace('/^\s*\/\*.*?\*\//ms', '', $sql);

        return $sql;
    }

    /**
     * 替換查詢串中數據表的前綴。該方法只對下列查詢有效：CREATE TABLE,
     * DROP TABLE, ALTER TABLE, UPDATE, REPLACE INTO, INSERT INTO
     *
     * @access  public
     * @param   string      $sql        SQL查詢串
     * @return  string      返回已替換掉前綴的SQL查詢串。
     */
    function replace_prefix($sql)
    {
        $keywords = 'CREATE\s+TABLE(?:\s+IF\s+NOT\s+EXISTS)?|'
                  . 'DROP\s+TABLE(?:\s+IF\s+EXISTS)?|'
                  . 'ALTER\s+TABLE|'
                  . 'UPDATE|'
                  . 'REPLACE\s+INTO|'
                  . 'DELETE\s+FROM|'
                  . 'INSERT\s+INTO';

        $pattern = '/(' . $keywords . ')(\s*)`?' . $this->source_prefix . '(\w+)`?(\s*)/i';
        $replacement = '\1\2`' . $this->target_prefix . '\3`\4';
        $sql = preg_replace($pattern, $replacement, $sql);

        $pattern = '/(UPDATE.*?WHERE)(\s*)`?' . $this->source_prefix . '(\w+)`?(\s*\.)/i';
        $replacement = '\1\2`' . $this->target_prefix . '\3`\4';
        $sql = preg_replace($pattern, $replacement, $sql);

        return $sql;
    }

    /**
     * 獲取表的名字。該方法只對下列查詢有效：CREATE TABLE,
     * DROP TABLE, ALTER TABLE, UPDATE, REPLACE INTO, INSERT INTO
     *
     * @access  public
     * @param   string      $query_item     SQL查詢項
     * @param   string      $query_type     查詢類型
     * @return  mixed       成功返回表的名字，失敗返回false。
     */
    function get_table_name($query_item, $query_type = '')
    {
        $pattern = '';
        $matches = array();
        $table_name = '';

        /* 如果沒指定$query_type，則自動獲取 */
        if (!$query_type && preg_match('/^\s*(\w+)/', $query_item, $matches))
        {
            $query_type = $matches[1];
        }

        /* 獲取相應的正則表達式 */
        $query_type = strtoupper($query_type);
        switch ($query_type)
        {
        case 'ALTER' :
            $pattern = '/^\s*ALTER\s+TABLE\s*`?(\w+)/i';
            break;
        case 'CREATE' :
            $pattern = '/^\s*CREATE\s+TABLE(?:\s+IF\s+NOT\s+EXISTS)?\s*`?(\w+)/i';
            break;
        case 'DROP' :
            $pattern = '/^\s*DROP\s+TABLE(?:\s+IF\s+EXISTS)?\s*`?(\w+)/i';
            break;
        case 'INSERT' :
            $pattern = '/^\s*INSERT\s+INTO\s*`?(\w+)/i';
            break;
        case 'REPLACE' :
            $pattern = '/^\s*REPLACE\s+INTO\s*`?(\w+)/i';
            break;
        case 'UPDATE' :
            $pattern = '/^\s*UPDATE\s*`?(\w+)/i';
            break;
        default :
            return false;
        }

        if (!preg_match($pattern, $query_item, $matches))
        {
            return false;
        }
        $table_name = $matches[1];

        return $table_name;
    }

    /**
     *   獲得SQL文件中指定的查詢項
     *
     * @access  public
     * @param   string    $file_path       SQL查詢項
     * @param   int       $pos             查詢項的索引號
     * @return  mixed     成功返回該查詢項，失敗返回false。
     */
    function get_spec_query_item($file_path, $pos)
    {
        $query_items = $this->parse_sql_file($file_path);

        if (empty($query_items)
                || empty($query_items[$pos]))
        {
            return false;
        }

        return $query_items[$pos];
    }

    /**
     * 概據MYSQL版本，創建數據表
     *
     * @access  public
     * @param   string      $query_item     SQL查詢項
     * @return  boolean     成功返回true，失敗返回false。
     */
    function create_table($query_item)
    {
        /* 獲取建表主體串以及表屬性聲明串，不區分大小寫，匹配換行符，且為貪婪匹配 */
        $pattern = '/^\s*(CREATE\s+TABLE[^(]+\(.*\))(.*)$/is';
        if (!preg_match($pattern, $query_item, $matches))
        {
            return false;
        }
        $main = $matches[1];
        $postfix = $matches[2];

        /* 從表屬性聲明串中查找表的類型 */
        $pattern = '/.*(?:ENGINE|TYPE)\s*=\s*([a-z]+).*$/is';
        $type = preg_match($pattern, $postfix, $matches) ? $matches[1] : 'MYISAM';

        /* 從表屬性聲明串中查找自增語句 */
        $pattern = '/.*(AUTO_INCREMENT\s*=\s*\d+).*$/is';
        $auto_incr = preg_match($pattern, $postfix, $matches) ? $matches[1] : '';

        /* 重新設置表屬性聲明串 */
        $postfix = $this->db->version() > '4.1' ? " ENGINE=$type DEFAULT CHARACTER SET " . $this->db_charset
                                                : " TYPE=$type";
        $postfix .= ' ' . $auto_incr;

        /* 重新構造建表語句 */
        $sql = $main . $postfix;

        /* 開始創建表 */
        if (!$this->db->query($sql, 'SILENT'))
        {
            $this->handle_error($sql);
            return false;
        }

        return true;
    }

    /**
     * 修改數據表的方法。算法設計思路：
     * 1. 先進行字段修改操作。CHANGE
     * 2. 然後進行字段移除操作。DROP [COLUMN]
     * 3. 接著進行字段添加操作。ADD [COLUMN]
     * 4. 進行索引移除操作。DROP INDEX
     * 5. 進行索引添加操作。ADD INDEX
     * 6. 最後進行其它操作。
     *
     * @access  public
     * @param   string      $query_item     SQL查詢項
     * @return  boolean     修改成功返回true，否則返回false
     */
    function alter_table($query_item)
    {
        /* 獲取表名 */
        $table_name = $this->get_table_name($query_item, 'ALTER');
        if (!$table_name)
        {
            return false;
        }

        /* 先把CHANGE操作提取出來執行，再過濾掉它們 */
        $result = $this->parse_change_query($query_item, $table_name);
        if ($result[0] && !$this->db->query($result[0], 'SILENT'))
        {
            $this->handle_error($result[0]);
            return false;
        }
        if (!$result[1])
        {
            return true;
        }

        /* 把DROP [COLUMN]提取出來執行，再過濾掉它們 */
        $result = $this->parse_drop_column_query($result[1], $table_name);
        if ($result[0] && !$this->db->query($result[0], 'SILENT'))
        {
            $this->handle_error($result[0]);
            return false;
        }
        if (!$result[1])
        {
            return true;
        }

        /* 把ADD [COLUMN]提取出來執行，再過濾掉它們 */
        $result = $this->parse_add_column_query($result[1], $table_name);
        if ($result[0] && !$this->db->query($result[0], 'SILENT'))
        {
            $this->handle_error($result[0]);
            return false;
        }
        if (!$result[1])
        {
            return true;
        }

        /* 把DROP INDEX提取出來執行，再過濾掉它們 */
        $result = $this->parse_drop_index_query($result[1], $table_name);
        if ($result[0] && !$this->db->query($result[0], 'SILENT'))
        {
            $this->handle_error($result[0]);
            return false;
        }
        if (!$result[1])
        {
            return true;
        }

        /* 把ADD INDEX提取出來執行，再過濾掉它們 */
        $result = $this->parse_add_index_query($result[1], $table_name);
        if ($result[0] && !$this->db->query($result[0], 'SILENT'))
        {
            $this->handle_error($result[0]);
            return false;
        }
        /* 執行其它的修改操作 */
        if ($result[1] && !$this->db->query($result[1], 'SILENT'))
        {
            $this->handle_error($result[1]);
            return false;
        }

        return true;
    }

    /**
     * 解析出CHANGE操作
     *
     * @access  public
     * @param   string      $query_item     SQL查詢項
     * @param   string      $table_name     表名
     * @return  array       返回一個以CHANGE操作串和其它操作串組成的數組
     */
    function parse_change_query($query_item, $table_name = '')
    {
        $result = array('', $query_item);

        if (!$table_name)
        {
            $table_name = $this->get_table_name($query_item, 'ALTER');
        }

        $matches = array();
        /* 第1個子模式匹配old_col_name，第2個子模式匹配column_definition，第3個子模式匹配new_col_name */
        $pattern = '/\s*CHANGE\s*`?(\w+)`?\s*`?(\w+)`?([^,(]+\([^,]+?(?:,[^,)]+)*\)[^,]+|[^,;]+)\s*,?/i';
        if (preg_match_all($pattern, $query_item, $matches, PREG_SET_ORDER))
        {
            $fields = $this->get_fields($table_name);
            $num = count($matches);
            $sql = '';
            for ($i = 0; $i < $num; $i++)
            {
                /* 如果表中存在原列名 */
                if (in_array($matches[$i][1], $fields))
                {
                    $sql .= $matches[$i][0];
                }
                /* 如果表中存在新列名 */
                elseif (in_array($matches[$i][2], $fields))
                {
                    $sql .= 'CHANGE ' . $matches[$i][2] . ' ' . $matches[$i][2] . ' ' . $matches[$i][3] . ',';
                }
                else /* 如果兩個列名都不存在 */
                {
                    $sql .= 'ADD ' . $matches[$i][2] . ' ' . $matches[$i][3] . ',';
                    $sql = preg_replace('/(\s+AUTO_INCREMENT)/i', '\1 PRIMARY KEY', $sql);
                }
            }
            $sql = 'ALTER TABLE ' . $table_name . ' ' . $sql;
            $result[0] = preg_replace('/\s*,\s*$/', '', $sql);//存儲CHANGE操作，已過濾末尾的逗號
            $result[0] = $this->insert_charset($result[0]);//加入字符集設置
            $result[1] = preg_replace($pattern, '', $query_item);//存儲其它操作
            $result[1] = $this->has_other_query($result[1]) ? $result[1]: '';
        }

        return $result;
    }

    /**
     * 解析出DROP COLUMN操作
     *
     * @access  public
     * @param   string      $query_item     SQL查詢項
     * @param   string      $table_name     表名
     * @return  array       返回一個以DROP COLUMN操作和其它操作組成的數組
     */
    function parse_drop_column_query($query_item, $table_name = '')
    {
        $result = array('', $query_item);

        if (!$table_name)
        {
            $table_name = $this->get_table_name($query_item, 'ALTER');
        }

        $matches = array();
        /* 子模式存儲列名 */
        $pattern = '/\s*DROP(?:\s+COLUMN)?(?!\s+(?:INDEX|PRIMARY))\s*`?(\w+)`?\s*,?/i';
        if (preg_match_all($pattern, $query_item, $matches, PREG_SET_ORDER))
        {
            $fields = $this->get_fields($table_name);
            $num = count($matches);
            $sql = '';
            for ($i = 0; $i < $num; $i++)
            {
                if (in_array($matches[$i][1], $fields))
                {
                    $sql .= 'DROP ' . $matches[$i][1] . ',';
                }
            }
            if ($sql)
            {
                $sql = 'ALTER TABLE ' . $table_name . ' ' . $sql;
                $result[0] = preg_replace('/\s*,\s*$/', '', $sql);//過濾末尾的逗號
            }
            $result[1] = preg_replace($pattern, '', $query_item);//過濾DROP COLUMN操作
            $result[1] = $this->has_other_query($result[1]) ? $result[1] : '';
        }

        return $result;
    }

    /**
     * 解析出ADD [COLUMN]操作
     *
     * @access  public
     * @param   string      $query_item     SQL查詢項
     * @param   string      $table_name     表名
     * @return  array       返回一個以ADD [COLUMN]操作和其它操作組成的數組
     */
    function parse_add_column_query($query_item, $table_name = '')
    {
        $result = array('', $query_item);

        if (!$table_name)
        {
            $table_name = $this->get_table_name($query_item, 'ALTER');
        }

        $matches = array();
        /* 第1個子模式存儲列定義，第2個子模式存儲列名 */
        $pattern = '/\s*ADD(?:\s+COLUMN)?(?!\s+(?:INDEX|UNIQUE|PRIMARY))\s*(`?(\w+)`?(?:[^,(]+\([^,]+?(?:,[^,)]+)*\)[^,]+|[^,;]+))\s*,?/i';
        if (preg_match_all($pattern, $query_item, $matches, PREG_SET_ORDER))
        {
            $fields = $this->get_fields($table_name);
            $mysql_ver = $this->db->version();
            $num = count($matches);
            $sql = '';
            for ($i = 0; $i < $num; $i++)
            {
                if (in_array($matches[$i][2], $fields))
                {
                    /* 如果為低版本MYSQL，則把非法關鍵字過濾掉 */
                    if  ($mysql_ver < '4.0.1' )
                    {
                        $matches[$i][1] = preg_replace('/\s*(?:AFTER|FIRST)\s*.*$/i', '', $matches[$i][1]);
                    }
                    $sql .= 'CHANGE ' . $matches[$i][2] . ' ' . $matches[$i][1] . ',';
                }
                else
                {
                    $sql .= 'ADD ' . $matches[$i][1] . ',';
                }
            }
            $sql = 'ALTER TABLE ' . $table_name . ' ' . $sql;
            $result[0] = preg_replace('/\s*,\s*$/', '', $sql);//過濾末尾的逗號
            $result[0] = $this->insert_charset($result[0]);//加入字符集設置
            $result[1] = preg_replace($pattern, '', $query_item);//過濾ADD COLUMN操作
            $result[1] = $this->has_other_query($result[1]) ? $result[1] : '';
        }

        return $result;
    }

    /**
     * 解析出DROP INDEX操作
     *
     * @access  public
     * @param   string      $query_item     SQL查詢項
     * @param   string      $table_name     表名
     * @return  array       返回一個以DROP INDEX操作和其它操作組成的數組
     */
    function parse_drop_index_query($query_item, $table_name = '')
    {
        $result = array('', $query_item);

        if (!$table_name)
        {
            $table_name = $this->get_table_name($query_item, 'ALTER');
        }

        /* 子模式存儲鍵名 */
        $pattern = '/\s*DROP\s+(?:PRIMARY\s+KEY|INDEX\s*`?(\w+)`?)\s*,?/i';
        if (preg_match_all($pattern, $query_item, $matches, PREG_SET_ORDER))
        {
            $indexes = $this->get_indexes($table_name);
            $num = count($matches);
            $sql = '';
            for ($i = 0; $i < $num; $i++)
            {
                /* 如果子模式為空，刪除主鍵 */
                if (empty($matches[$i][1]))
                {
                    $sql .= 'DROP PRIMARY KEY,';
                }
                /* 否則刪除索引 */
                elseif (in_array($matches[$i][1], $indexes))
                {
                    $sql .= 'DROP INDEX ' . $matches[$i][1] . ',';
                }
            }
            if ($sql)
            {
                $sql = 'ALTER TABLE ' . $table_name . ' ' . $sql;
                $result[0] = preg_replace('/\s*,\s*$/', '', $sql);//存儲DROP INDEX操作，已過濾末尾的逗號
            }
            $result[1] = preg_replace($pattern, '', $query_item);//存儲其它操作
            $result[1] = $this->has_other_query($result[1]) ? $result[1] : '';
        }

        return $result;
    }

    /**
     * 解析出ADD INDEX操作
     *
     * @access  public
     * @param   string      $query_item     SQL查詢項
     * @param   string      $table_name     表名
     * @return  array       返回一個以ADD INDEX操作和其它操作組成的數組
     */
    function parse_add_index_query($query_item, $table_name = '')
    {
        $result = array('', $query_item);

        if (!$table_name)
        {
            $table_name = $this->get_table_name($query_item, 'ALTER');
        }

        /* 第1個子模式存儲索引定義，第2個子模式存儲"PRIMARY KEY"，第3個子模式存儲鍵名，第4個子模式存儲列名 */
        $pattern = '/\s*ADD\s+((?:INDEX|UNIQUE|(PRIMARY\s+KEY))\s*(?:`?(\w+)`?)?\s*\(\s*`?(\w+)`?\s*(?:,[^,)]+)*\))\s*,?/i';
        if (preg_match_all($pattern, $query_item, $matches, PREG_SET_ORDER))
        {
            $indexes = $this->get_indexes($table_name);
            $num = count($matches);
            $sql = '';
            for ($i = 0; $i < $num; $i++)
            {
                $index = !empty($matches[$i][3]) ? $matches[$i][3] : $matches[$i][4];
                if (!empty($matches[$i][2]) && in_array('PRIMARY', $indexes))
                {
                    $sql .= 'DROP PRIMARY KEY,';
                }
                elseif (in_array($index, $indexes))
                {
                    $sql .= 'DROP INDEX ' . $index . ',';
                }
                $sql .= 'ADD ' . $matches[$i][1] . ',';
            }
            $sql = 'ALTER TABLE ' . $table_name . ' ' . $sql;
            $result[0] = preg_replace('/\s*,\s*$/', '', $sql);//存儲ADD INDEX操作，已過濾末尾的逗號
            $result[1] = preg_replace($pattern, '', $query_item);//存儲其它的操作
            $result[1] = $this->has_other_query($result[1]) ? $result[1] : '';
        }

        return $result;
    }

    /**
     * 獲取所有的indexes
     *
     * @access  public
     * @param   string      $table_name      數據表名
     * @return  array
     */
    function get_indexes($table_name)
    {
        $indexes = array();

        $result = $this->db->query("SHOW INDEX FROM $table_name", 'SILENT');

        if ($result)
        {
            while ($row = $this->db->fetchRow($result))
            {
                $indexes[] = $row['Key_name'];
            }
        }

        return $indexes;
    }

    /**
     * 獲取所有的fields
     *
     * @access  public
     * @param   string      $table_name      數據表名
     * @return  array
     */
    function get_fields($table_name)
    {
        $fields = array();

        $result = $this->db->query("SHOW FIELDS FROM $table_name", 'SILENT');

        if ($result)
        {
            while ($row = $this->db->fetchRow($result))
            {
                $fields[] = $row['Field'];
            }
        }

        return $fields;
    }

    /**
     * 判斷是否還有其它的查詢
     *
     * @access  private
     * @param   string      $sql_string     SQL查詢串
     * @return  boolean     有返回true，否則返回false
     */
    function has_other_query($sql_string)
    {
        return preg_match('/^\s*ALTER\s+TABLE\s*`\w+`\s*\w+/i', $sql_string);
    }

    /**
     * 在查詢串中加入字符集設置
     *
     * @access  private
     * @param  string      $sql_string     SQL查詢串
     * @return  string     含有字符集設置的SQL查詢串
     */
    function insert_charset($sql_string)
    {
        if ($this->db->version() > '4.1')
        {
            $sql_string = preg_replace('/(TEXT|CHAR\(.*?\)|VARCHAR\(.*?\))\s+/i',
                    '\1 CHARACTER SET ' . $this->db_charset . ' ',
                    $sql_string);
        }

        return $sql_string;
    }

    /**
     * 處理其它的數據庫操作
     *
     * @access  private
     * @param   string      $query_item     SQL查詢項
     * @return  boolean     成功返回true，失敗返回false。
     */
    function do_other($query_item)
    {
        if (!$this->db->query($query_item, 'SILENT'))
        {
            $this->handle_error($query_item);
            return false;
        }

        return true;
    }

    /**
     * 處理錯誤信息
     *
     * @access  private
     * @param   string      $query_item     SQL查詢項
     * @return  boolean     成功返回true，失敗返回false。
     */
    function handle_error($query_item)
    {
        $mysql_error = 'ERROR NO: ' . $this->db->errno()
                    . "\r\nERROR MSG: " . $this->db->error();

        $error_str = "SQL Error:\r\n " . $mysql_error
                . "\r\n\r\n"
                . "Query String:\r\n ". $query_item
                . "\r\n\r\n"
                . "File Path:\r\n ". $this->current_file
                . "\r\n\r\n\r\n\r\n";

        /* 過濾一些錯誤 */
        if (!in_array($this->db->errno(), $this->ignored_errors))
        {
            $this->error = $error_str;
        }

        if ($this->log_path)
        {
            $f = @fopen($this->log_path, 'ab+');
            if (!$f)
            {
                return false;
            }
            if (!@fwrite($f, $error_str))
            {
                return false;
            }
        }

        return true;
    }
}

?>