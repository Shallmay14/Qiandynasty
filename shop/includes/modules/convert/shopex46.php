<?php

/**
 * shopex4.6轉換程序插件
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: shopex46.php 17063 2010-03-25 06:35:46Z liuhui $
 */

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

/**
 * 模塊信息
 */
if (isset($set_modules) && $set_modules == TRUE)
{
    $i = isset($modules) ? count($modules) : 0;

    /* 代碼 */
    $modules[$i]['code'] = basename(__FILE__, '.php');

    /* 描述對應的語言項 */
    $modules[$i]['desc'] = 'shopex46_desc';

    /* 作者 */
    $modules[$i]['author'] = 'ECSHOP R&D TEAM';

    return;
}

/**
 * 類
 */
class shopex46
{
    /**
     * 數據庫連接 ADOConnection 對象
     */
    var $sdb;

    /**
     * 表前綴
     */
    var $sprefix;

    /**
     * 原系統根目錄
     */
    var $sroot;

    /**
     * 新系統根目錄
     */
    var $troot;

    /**
     * 新系統網站根目錄
     */
    var $tdocroot;

    /**
     * 原系統字符集
     */
    var $scharset;

    /**
     * 新系統字符集
     */
    var $tcharset;

    /**
     * 構造函數
     */
    function shopex46(&$sdb, $sprefix, $sroot, $scharset = 'UTF8')
    {
        $this->sdb = $sdb;
        $this->sprefix = $sprefix;
        $this->sroot = $sroot;
        $this->troot = str_replace('/includes/modules/convert', '', str_replace('\\', '/', dirname(__FILE__)));
        $this->tdocroot = str_replace('/' . ADMIN_PATH, '', dirname(PHP_SELF));
        $this->scharset = $scharset;
        if (EC_CHARSET == 'utf-8')
        {
            $tcharset = 'UTF8';
        }
        elseif (EC_CHARSET == 'gbk')
        {
            $tcharset = 'GB2312';
        }
        $this->tcharset = $tcharset;
    }

    /**
     * 需要轉換的表（用於檢查數據庫是否完整）
     * @return  array
     */
    function required_tables()
    {
        return array(
            $this->sprefix.'mall_offer_pcat',$this->sprefix.'mall_goods',$this->sprefix.'mall_offer_linkgoods',$this->sprefix.'mall_member_level',
            $this->sprefix.'mall_member',$this->sprefix.'mall_offer_p',$this->sprefix.'mall_offer_deliverarea',$this->sprefix.'mall_offer_t',
            $this->sprefix.'mall_offer_ncat',$this->sprefix.'mall_offer_ncon',$this->sprefix.'mall_offer_link',$this->sprefix.'mall_orders',
            $this->sprefix.'mall_items',$this->sprefix.'mall_offer',
        );
    }

    /**
     * 比需的目錄
     * @return  array
     */
    function required_dirs()
    {
        return array(
            '/syssite/home/shop/1/pictures/newsimg/',
            '/syssite/home/shop/1/pictures/productsimg/big/',
            '/syssite/home/shop/1/pictures/productsimg/small/',
            '/syssite/home/shop/1/pictures/linkimg/',
            '/cert/',
        );
    }

    /**
     * 下一步操作：空表示結束
     * @param   string  $step  當前操作：空表示開始
     * @return  string
     */
    function next_step($step)
    {
        /* 所有操作 */
        $steps = array(
            ''              => 'step_file',
            'step_file'     => 'step_cat',
            'step_cat'      => 'step_brand',
            'step_brand'    => 'step_goods',
            'step_goods'    => 'step_users',
            'step_users'    => 'step_article',
            'step_article'  => 'step_order',
            'step_order'    => 'step_config',
            'step_config'   => '',
        );

        return $steps[$step];
    }

    /**
     * 執行某個步驟
     * @param   string  $step
     */
    function process($step)
    {
        $func = str_replace('step', 'process', $step);
        return $this->$func();
    }

    /**
     * 複製文件
     * @return  成功返回true，失敗返回錯誤信息
     */
    function process_file()
    {
        /* 複製 html 編輯器的圖片 */
        $from = $this->sroot . '/syssite/home/shop/1/pictures/newsimg/';
        $to   = $this->troot . '/images/upload/';
        copy_files($from, $to);

        /* 複製商品圖片 */
        $to   = $this->troot . '/images/' . date('Ym') . '/';

        $from = $this->sroot . '/syssite/home/shop/1/pictures/productsimg/big/';
        copy_files($from, $to, 'big_');

        $from = $this->sroot . '/syssite/home/shop/1/pictures/productsimg/small/';
        copy_files($from, $to, 'small_');

        $from = $this->sroot . '/syssite/home/shop/1/pictures/productsimg/big/';
        copy_files($from, $to, 'original_');

        /* 複製友情鏈接圖片 */
        $from = $this->sroot . '/syssite/home/shop/1/pictures/linkimg/';
        $to   = $this->troot . '/data/afficheimg/';

        /* 複製證書 */
        $from = $this->sroot . '/cert/';
        $to   = $this->troot . '/cert/';

        return TRUE;
    }

    /**
     * 商品分類
     * @return  成功返回true，失敗返回錯誤信息
     */
    function process_cat()
    {
        global $db, $ecs;

        /* 清空分類、商品類型、屬性 */
        truncate_table('category');
        truncate_table('goods_type');
        truncate_table('attribute');

        /* 查詢分類並循環處理 */
        $sql = "SELECT * FROM ".$this->sprefix."mall_offer_pcat";
        $res = $this->sdb->query($sql);
        while ($row = $this->sdb->fetchRow($res))
        {
            $cat = array();
            $cat['cat_id']      = $row['catid'];
            $cat['cat_name']    = ecs_iconv($this->scharset, $this->tcharset, addslashes($row['cat']));
            $cat['parent_id']   = $row['pid'];
            $cat['sort_order']  = $row['catord'];

            /* 插入分類 */
            if (!$db->autoExecute($ecs->table('category'), $cat, 'INSERT', '', 'SILENT'))
            {
                //return $db->error();
            }

            /* 檢查該分類是否有屬性 */
            $has_attr = false;
            for ($i = 1; $i <= 40; $i++)
            {
                if (trim($row["attr".$i]) != '')
                {
                    $has_attr = TRUE;
                    break;
                }
            }

            /* 如果該分類有屬性，插入商品類型，類型名稱取分類名稱 */
            if ($has_attr)
            {
                if (!$db->autoExecute($ecs->table('goods_type'), $cat, 'INSERT', '', 'SILENT'))
                {
                    //return $db->error();
                }
            }

            /* 插入屬性 */
            $attr = array();
            $attr['cat_id']          = $row['catid'];
            $attr['attr_input_type'] = ATTR_INPUT;
            $attr['attr_type']       = ATTR_NOT_NEED_SELECT;
            for ($i = 1; $i <= 40; $i++)
            {
                if (trim($row["attr".$i]) != '')
                {
                    $attr['attr_name']  = ecs_iconv($this->scharset, $this->tcharset, $row["attr".$i]);
                    $attr['sort_order'] = $i;
                    if (!$db->autoExecute($ecs->table('attribute'), $attr, 'INSERT', '', 'SILENT'))
                    {
                        //return $db->error();
                    }
                }
            }
        }

        /* 返回成功 */
        return TRUE;
    }

    /**
     * 品牌
     * @return  成功返回true，失敗返回錯誤信息
     */
    function process_brand()
    {
        global $db, $ecs;

        /* 清空品牌 */
        truncate_table('brand');

        /* 查詢品牌並插入 */
        $sql = "SELECT DISTINCT brand FROM ".$this->sprefix."mall_goods WHERE TRIM(brand) <> ''";
        $res = $this->sdb->query($sql);
        while ($row = $this->sdb->fetchRow($res))
        {
            $brand = array(
                'brand_name' => ecs_iconv($this->scharset, $this->tcharset, addslashes($row['brand'])),
                'brand_desc' => '',
            );
            if (!$db->autoExecute($ecs->table('brand'), $brand, 'INSERT', '', 'SILENT'))
            {
                //return $db->error();
            }
        }

        /* 返回成功 */
        return TRUE;
    }

    /**
     * 商品
     * @return  成功返回true，失敗返回錯誤信息
     */
    function process_goods()
    {
        global $db, $ecs;

        /* 清空商品、商品擴展分類、商品屬性、商品相冊、關聯商品、組合商品、贈品 */
        truncate_table('goods');
        truncate_table('goods_cat');
        truncate_table('goods_attr');
        truncate_table('goods_gallery');
        truncate_table('link_goods');
        truncate_table('group_goods');

        /* 查詢品牌列表 name => id */
        $brand_list = array();
        $sql = "SELECT brand_id, brand_name FROM " . $ecs->table('brand');
        $res = $db->query($sql);
        while ($row = $db->fetchRow($res))
        {
            $brand_list[$row['brand_name']] = $row['brand_id'];
        }

        /* 取得商店設置 */
        $sql = "SELECT offer_pointtype, offer_pointnum FROM ".$this->sprefix."mall_offer WHERE offerid = '1'";
        $config = $this->sdb->getRow($sql);

        /* 查詢商品並處理 */
        $sql = "SELECT * FROM ".$this->sprefix."mall_goods";
        $res = $this->sdb->query($sql);
        while ($row = $this->sdb->fetchRow($res))
        {
            $goods = array();
            $goods['goods_id']      = $row['gid'];
            $goods['cat_id']        = $row['catid'];
            $goods['goods_sn']      = ecs_iconv($this->scharset, $this->tcharset, addslashes($row['bn']));
            $goods['goods_name']    = ecs_iconv($this->scharset, $this->tcharset, addslashes($row['goods']));
            $goods['brand_id']      = trim($row['brand']) == '' ? '0' : $brand_list[ecs_iconv($this->scharset, $this->tcharset, addslashes($row['brand']))];
            $goods['goods_number']  = $row['storage'];
            $goods['goods_weight']  = $row['weight'];
            $goods['market_price']  = $row['priceintro'];
            $goods['shop_price']    = $row['ifdiscreteness'] == '1' ? $row['basicprice'] : $row['price'];
            if ($row['tejia2'] == '1')
            {
                $goods['promote_price'] = $goods['shop_price'];
                $goods['promote_start_date'] = gmtime();
                $goods['promote_end_date']   = local_strtotime('+1 weeks');
            }
            $goods['warn_number']   = $row['ifalarm'] == '1' ? $row['alarmnum'] : '0';
            $goods['goods_brief']   = ecs_iconv($this->scharset, $this->tcharset, addslashes($row['intro']));
            $goods['goods_desc']    = str_replace('pictures/newsimg/', $this->tdocroot . '/images/upload/', ecs_iconv($this->scharset, $this->tcharset, addslashes($row['memo'])));
            $goods['is_real']       = '1';
            $goods['is_on_sale']    = $row['shop_iffb'];
            $goods['is_alone_sale'] = '1';
            $goods['add_time']      = $row['uptime'];
            $goods['sort_order']    = $row['offer_ord'];
            $goods['is_delete']     = '0';
            $goods['is_best']       = $row['recommand2'];
            $goods['is_new']        = $row['new2'];
            $goods['is_hot']        = $row['hot2'];
            $goods['is_promote']    = $row['tejia2'];
            $goods['goods_type']    = $row['catid'];
            $goods['last_update'] = gmtime();

            /* 圖片：如果沒有本地文件，取遠程圖片 */
            $file = $this->troot . '/images/' . date('Ym') . '/small_' . $row['gid'];
            if (file_exists($file. '.jpg'))
            {
                $goods['goods_thumb'] = 'images/' . date('Ym') . '/small_' . $row['gid'] . '.jpg';
            }
            elseif (file_exists($file. '.jpeg'))
            {
                $goods['goods_thumb'] = 'images/' . date('Ym') . '/small_' . $row['gid'] . '.jpeg';
            }
            elseif (file_exists($file. '.gif'))
            {
                $goods['goods_thumb'] = 'images/' . date('Ym') . '/small_' . $row['gid'] . '.gif';
            }
            elseif (file_exists($file. '.png'))
            {
                $goods['goods_thumb'] = 'images/' . date('Ym') . '/small_' . $row['gid'] . '.png';
            }
            else
            {
                $goods['goods_thumb'] = $row['smallimgremote'];
            }

            $file = $this->troot . '/images/' . date('Ym') . '/big_' . $row['gid'];
            if (file_exists($file. '.jpg'))
            {
                $goods['goods_img'] = 'images/' . date('Ym') . '/big_' . $row['gid'] . '.jpg';
                $goods['original_img'] = 'images/' . date('Ym') . '/original_' . $row['gid'] . '.jpg';
            }
            elseif (file_exists($file. '.jpeg'))
            {
                $goods['goods_img'] = 'images/' . date('Ym') . '/big_' . $row['gid'] . '.jpeg';
                $goods['original_img'] = 'images/' . date('Ym') . '/original_' . $row['gid'] . '.jpeg';
            }
            elseif (file_exists($file. '.gif'))
            {
                $goods['goods_img'] = 'images/' . date('Ym') . '/big_' . $row['gid'] . '.gif';
                $goods['original_img'] = 'images/' . date('Ym') . '/original_' . $row['gid'] . '.gif';
            }
            elseif (file_exists($file. '.png'))
            {
                $goods['goods_img'] = 'images/' . date('Ym') . '/big_' . $row['gid'] . '.png';
                $goods['orinigal_img'] = 'images/' . date('Ym') . '/original_' . $row['gid'] . '.png';
            }
            else
            {
                $goods['goods_img'] = $row['bigimgremote'];
            }

            /* 積分：根據商店設置 */
            if ($config['offer_pointtype'] == '0')
            {
                /* 不使用積分 */
                $goods['integral'] = '0';
            }
            elseif ($config['offer_pointtype'] == '1')
            {
                /* 按比例 */
                $goods['integral'] = round($goods['shop_price'] * $config['offer_pointnum']);
            }
            else
            {
                /* 自定義 */
                $goods['integral'] = $row['point'];
            }

            /* 插入 */
            if (!$db->autoExecute($ecs->table('goods'), $goods, 'INSERT', '', 'SILENT'))
            {
                //return $db->error();
            }

            /* 擴展分類 */
            if ($row['linkclass'] != '')
            {
                $goods_cat = array();
                $goods_cat['goods_id'] = $row['gid'];
                $cat_id_list = explode(',', trim($row['linkclass'], ','));
                foreach ($cat_id_list as $cat_id)
                {
                    $goods_cat['cat_id'] = $cat_id;
                    if (!$db->autoExecute($ecs->table('goods_cat'), $goods_cat, 'INSERT', '', 'SILENT'))
                    {
                        //return $db->error();
                    }
                }
            }

            /* 取得該分類的所有屬性 */
            $attr_list = array();
            $sql = "SELECT * FROM " . $ecs->table('attribute') . " WHERE cat_id = '$row[catid]'";
            $res1 = $db->query($sql);
            while ($attr = $db->fetchRow($res1))
            {
                $attr_list[$attr['sort_order']] = $attr['attr_id'];
            }

            /* 商品屬性 */
            if ($attr_list)
            {
                $goods_attr = array();
                $goods_attr['goods_id'] = $row['gid'];
                for ($i = 1; $i <= 40; $i++)
                {
                    if (trim($row['attr' . $i]) != '')
                    {
                        $goods_attr['attr_id'] = $attr_list[$i];
                        $goods_attr['attr_value'] = trim(ecs_iconv($this->scharset, $this->tcharset, $row['attr' . $i]));
                        if (!$db->autoExecute($ecs->table('goods_attr'), $goods_attr, 'INSERT', '', 'SILENT'))
                        {
                            //return $db->error();
                        }
                    }
                }
            }

            /* 商品相冊 */
            if ($row['multi_image'])
            {
                $goods_gallery = array();
                $goods_gallery['goods_id'] = $row['gid'];
                $img_list = explode('&&&', $row['multi_image']);
                foreach ($img_list as $img)
                {
                    if (substr($img, 0, 7) == 'http://')
                    {
                        $goods_gallery['img_url'] = $img;
                    }
                    else
                    {
                        make_dir('images/' . date('Ym') . '/');
                        $goods_gallery['img_url'] = 'images/' . date('Ym') . '/big_' . $img;
                        $goods_gallery['img_original'] = 'images/' . date('Ym') . '/original_' . $img;
                    }

                    if (!$db->autoExecute($ecs->table('goods_gallery'), $goods_gallery, 'INSERT', '', 'SILENT'))
                    {
                        //return $db->error();
                    }
                }
            }
        }

        /* 關聯商品 */
        $sql = "SELECT * FROM ".$this->sprefix."mall_offer_linkgoods";
        $res = $this->sdb->query($sql);
        while ($row = $this->sdb->fetchRow($res))
        {
            $link_goods = array();
            $link_goods['goods_id']         = $row['pgid'];
            $link_goods['link_goods_id']    = $row['sgid'];
            $link_goods['is_double']        = $row['type'];

            if (!$db->autoExecute($ecs->table('link_goods'), $link_goods, 'INSERT', '', 'SILENT'))
            {
                //return $db->error();
            }

            if ($row['type'] == '1')
            {
                $link_goods = array();
                $link_goods['goods_id']         = $row['sgid'];
                $link_goods['link_goods_id']    = $row['pgid'];
                $link_goods['is_double']        = $row['type'];

                if (!$db->autoExecute($ecs->table('link_goods'), $link_goods, 'INSERT', '', 'SILENT'))
                {
                    //return $db->error();
                }
            }
        }

        /* 組合商品 */

        /* 返回成功 */
        return TRUE;
    }

    /**
     * 會員等級、會員、會員價格
     */
    function process_users()
    {
        global $db, $ecs;

        /* 清空會員、會員等級、會員價格、用戶紅包、用戶地址 */
        truncate_table('user_rank');
        truncate_table('users');
        truncate_table('user_address');
        truncate_table('user_bonus');
        truncate_table('member_price');
        truncate_table('user_account');

        /* 查詢並插入會員等級 */
        $sql = "SELECT * FROM ".$this->sprefix."mall_member_level order by point desc";
        $res = $this->sdb->query($sql);
        $max_points = 50000;
        while ($row = $this->sdb->fetchRow($res))
        {
            $user_rank = array();
            $user_rank['rank_id']       = $row['levelid'];
            $user_rank['rank_name']     = ecs_iconv($this->scharset, $this->tcharset, addslashes($row['name']));
            $user_rank['min_points']    = $row['point'];
            $user_rank['max_points']    = $max_points;
            $user_rank['discount']      = round($row['discount'] * 100);
            $user_rank['show_price']    = '1';
            $user_rank['special_rank']  = '0';

            if (!$db->autoExecute($ecs->table('user_rank'), $user_rank, 'INSERT', '', 'SILENT'))
            {
                //return $db->error();
            }

            $max_points = $row['point'] - 1;
        }

        /* 查詢並插入會員 */
        $sql = "SELECT * FROM ".$this->sprefix."mall_member";
        $res = $this->sdb->query($sql);
        while ($row = $this->sdb->fetchRow($res))
        {
            $user = array();
            $user['user_id']        = $row['userid'];
            $user['email']          = $row['email'];
            $user['user_name']      = ecs_iconv($this->scharset, $this->tcharset, addslashes($row['user']));
            $user['password']       = $row['password'];
            $user['question']       = ecs_iconv($this->scharset, $this->tcharset, addslashes($row['pw_question']));
            $user['answer']         = ecs_iconv($this->scharset, $this->tcharset, addslashes($row['pw_answer']));
            $user['sex']            = $row['sex'];
            if (!empty($row['birthday']))
            {
                $birthday           = strtotime($row['birthday']);
                if ($birthday != -1 && $birthday !== false)
                {
                    $user['birthday']   = date('Y-m-d', $birthday);
                }
            }
            $user['user_money']     = $row['advance'];
            $user['pay_points']     = $row['point'];
            $user['rank_points']    = $row['point'];
            $user['reg_time']       = $row['regtime'];
            $user['last_login']     = $row['regtime'];
            $user['last_ip']        = $row['ip'];
            $user['visit_count']    = '1';
            $user['user_rank']      = '0';

            if (!$db->autoExecute($ecs->table('users'), $user, 'INSERT', '', 'SILENT'))
            {
                //return $db->error();
            }
            uc_call('uc_user_register', array($user['user_name'], $user['password'], $user['email']));
        }

        /* 收貨人地址 */
        $sql = "SELECT * FROM ".$this->sprefix."mall_member_receiver";
        $res = $this->sdb->query($sql);
        while ($row = $this->sdb->fetchRow($res))
        {
            $address = array();
            $address['address_id']      = $row['receiveid'];
            $address['address_name']    = ecs_iconv($this->scharset, $this->tcharset, addslashes($row['name']));
            $address['user_id']         = $row['memberid'];
            $address['consignee']       = ecs_iconv($this->scharset, $this->tcharset, addslashes($row['name']));
            $address['email']           = $row['email'];
            $address['address']         = ecs_iconv($this->scharset, $this->tcharset, addslashes($row['address']));
            $address['zipcode']         = $row['zipcode'];
            $address['tel']             = $row['telphone'];
            $address['mobile']          = $row['mobile'];

            if (!$db->autoExecute($ecs->table('user_address'), $address, 'INSERT', '', 'SILENT'))
            {
                //return $db->error();
            }
        }

        /* 會員價格 */
        $temp_arr = array();
        $sql = "SELECT * FROM ".$this->sprefix."mall_member_price";
        $res = $this->sdb->query($sql);
        while ($row = $this->sdb->fetchRow($res))
        {
            if ($row['gid'] > 0 && $row['levelid'] > 0 && !isset($temp_arr[$row['gid']][$row['levelid']]))
            {
                $temp_arr[$row['gid']][$row['levelid']] = true;

                $member_price = array();
                $member_price['goods_id']   = $row['gid'];
                $member_price['user_rank']  = $row['levelid'];
                $member_price['user_price'] = $row['price'];

                if (!$db->autoExecute($ecs->table('member_price'), $member_price, 'INSERT', '', 'SILENT'))
                {
                    //return $db->error();
                }
            }
        }
        unset($temp_arr);

        /* 帳戶明細 */
        $sql = "SELECT * FROM ".$this->sprefix."mall_member_advance";
        $res = $this->sdb->query($sql);
        while ($row = $this->sdb->fetchRow($res))
        {
            $user_account = array();
            $user_account['user_id']        = $row['memberid'];
            $user_account['admin_user']     = $row['doman'];
            $user_account['amount']         = $row['money'];
            $user_account['add_time']       = $row['date'];
            $user_account['paid_time']      = $row['date'];
            $user_account['admin_note']     = $row['description'];
            $user_account['process_type']   = $row['money'] >= 0 ? SURPLUS_SAVE : SURPLUS_RETURN;
            $user_account['is_paid']        = '1';

            if (!$db->autoExecute($ecs->table('user_account'), $user_account, 'INSERT', '', 'SILENT'))
            {
                //return $db->error();
            }
        }

        /* 返回 */
        return TRUE;
    }

    /**
     * 文章
     */
    function process_article()
    {
        global $db, $ecs;

        /* 清空文章類型、文章、友情鏈接 */
        truncate_table('article_cat');
        truncate_table('article');
        truncate_table('friend_link');

        /* 文章類型 */
        $sql = "SELECT * FROM ".$this->sprefix."mall_offer_ncat";
        $res = $this->sdb->query($sql);
        while ($row = $this->sdb->fetchRow($res))
        {
            $cat = array();
            $cat['cat_id']      = $row['catid'];
            $cat['cat_name']    = ecs_iconv($this->scharset, $this->tcharset, addslashes($row['cat']));
            $cat['cat_type']    = '1';
            $cat['sort_order']  = $row['pid'];
            $cat['is_open']     = '1';

            if (!$db->autoExecute($ecs->table('article_cat'), $cat, 'INSERT', '', 'SILENT'))
            {
                //return $db->error();
            }
        }

        /* 文章 */
        $sql = "SELECT * FROM ".$this->sprefix."mall_offer_ncon";
        $res = $this->sdb->query($sql);
        while ($row = $this->sdb->fetchRow($res))
        {
            $article = array();
            $article['article_id']  = $row['newsid'];
            $article['cat_id']      = $row['catid'];
            $article['title']       = ecs_iconv($this->scharset, $this->tcharset, addslashes($row['title']));
            $article['content']     = ecs_iconv($this->scharset, $this->tcharset, addslashes($row['con']));
            $article['article_type']= '0';
            $article['is_open']     = $row['ifpub'];
            $article['add_time']    = $row['uptime'];

            if (!$db->autoExecute($ecs->table('article'), $article, 'INSERT', '', 'SILENT'))
            {
                //return $db->error();
            }
        }

        /* 友情鏈接 */
        $sql = "SELECT * FROM ".$this->sprefix."mall_offer_link";
        $res = $this->sdb->query($sql);
        while ($row = $this->sdb->fetchRow($res))
        {
            $link = array();
            $link['link_id']     = $row['linkid'];
            $link['link_name']   = ecs_iconv($this->scharset, $this->tcharset, addslashes($row['linktitle']));
            $link['link_url']    = $row['linkurl'];
            $link['show_order']  = '0';

            if ($row['linktype'] == 'img')
            {
                $link['link_logo']   = $row['imgurl'];
            }

            if (!$db->autoExecute($ecs->table('friend_link'), $link, 'INSERT', '', 'SILENT'))
            {
                //return $db->error();
            }
        }

        /* 返回 */
        return TRUE;
    }

    /**
     * 訂單
     */
    function process_order()
    {
        global $db, $ecs;

        /* 清空訂單、訂單商品 */
        truncate_table('order_info');
        truncate_table('order_goods');
        truncate_table('order_action');

        /* 訂單 */
        $sql = "SELECT o.*, t.tmethod, p.payment FROM ".$this->sprefix."mall_orders AS o " .
                "LEFT JOIN ".$this->sprefix."mall_offer_t AS t ON o.ttype = t.id " .
                "LEFT JOIN ".$this->sprefix."mall_offer_p AS p ON o.ptype = p.id";
        $res = $this->sdb->query($sql);
        while ($row = $this->sdb->fetchRow($res))
        {
            $order = array();
            $order['order_sn']          = $row['orderid'];
            $order['user_id']           = $row['userid'];
            $order['add_time']          = $row['ordertime'];
            $order['consignee']         = ecs_iconv($this->scharset, $this->tcharset, addslashes($row['name']));
            $order['address']           = ecs_iconv($this->scharset, $this->tcharset, addslashes($row['addr']));
            $order['zipcode']           = $row['zip'];
            $order['tel']               = $row['tel'];
            $order['mobile']            = $row['mobile'];
            $order['email']             = $row['email'];
            $order['postscript']        = ecs_iconv($this->scharset, $this->tcharset, addslashes($row['memo']));
            $order['shipping_name']     = is_null($row['tmethod']) ? ' ' : ecs_iconv($this->scharset, $this->tcharset, addslashes($row['tmethod']));
            $order['pay_name']          = is_null($row['payment']) ? ' ' : ecs_iconv($this->scharset, $this->tcharset, addslashes($row['payment']));
            $order['inv_payee']         = ecs_iconv($this->scharset, $this->tcharset, addslashes($row['invoiceform']));
            $order['goods_amount']      = $row['item_amount'];
            $order['shipping_fee']      = $row['freight'];
            $order['order_amount']      = $row['total_amount'];
            $order['pay_time']          = $row['paytime'];
            $order['shipping_time']     = $row['sendtime'];

            /* 狀態 */
            if ($row['ordstate'] == '0')
            {
                $order['order_status']      = OS_UNCONFIRMED;
                $order['shipping_status']   = SS_UNSHIPPED;
            }
            elseif ($row['ordstate'] == '1')
            {
                $order['order_status']      = OS_CONFIRMED;
                $order['shipping_status']   = SS_UNSHIPPED;
            }
            elseif ($row['ordstate'] == '9')
            {
                $order['order_status']      = OS_INVALID;
                $order['shipping_status']   = SS_UNSHIPPED;
            }
            else // 3 發貨 4 歸檔
            {
                $order['order_status']      = OS_CONFIRMED;
                $order['shipping_status']   = SS_SHIPPED;
            }

            if ($row['ifsk'] == '1')
            {
                $order['pay_status']        = PS_PAYED;
            }
            else // 0 未付款 5 退款
            {
                $order['pay_status']        = PS_UNPAYED;
            }

            if ($row['userrecsts'] == '1') // 用戶操作了
            {
                if ($row['recsts'] == '1') // 到貨
                {
                    if ($order['shipping_status'] == SS_SHIPPED)
                    {
                        $order['shipping_status'] = SS_RECEIVED;
                    }
                }
                elseif ($row['recsts'] == '2') // 取消
                {
                    $order['order_status']      = OS_CANCELED;
                    $order['pay_status']        = PS_UNPAYED;
                    $order['shipping_status']   = SS_UNSHIPPED;
                }
            }

            /* 如果已付款，修改已付款金額為訂單總金額，修改訂單總金額為0 */
            if ($order['pay_status'] > PS_UNPAYED)
            {
                $order['money_paid']    = $order['order_amount'];
                $order['order_amount']  = 0;
            }

            if (!$db->autoExecute($ecs->table('order_info'), $order, 'INSERT', '', 'SILENT'))
            {
                //return $db->error();
            }

            /* 訂單商品 */
            $order_id = $db->insert_id();
            $sql = "SELECT i.*, g.priceintro FROM ".$this->sprefix."mall_items AS i " .
                    "LEFT JOIN ".$this->sprefix."mall_goods AS g ON i.gid = g.gid " .
                    "WHERE orderid = '$row[orderid]'";
            $res1 = $this->sdb->query($sql);
            while ($row = $this->sdb->fetchRow($res1))
            {
                $goods = array();
                $goods['order_id']      = $order_id;
                $goods['goods_id']      = $row['gid'];
                $goods['goods_name']    = ecs_iconv($this->scharset, $this->tcharset, addslashes($row['goods']));
                $goods['goods_sn']      = ecs_iconv($this->scharset, $this->tcharset, addslashes($row['bn']));
                $goods['goods_number']  = $row['nums'];
                $goods['goods_price']   = $row['price'];
                $goods['market_price']  = is_null($row['priceintro']) ? $row['goods_price'] : $row['priceintro'];
                $goods['is_real']       = 1;
                $goods['parent_id']     = 0;
                $goods['is_gift']       = 0;

                if (!$db->autoExecute($ecs->table('order_goods'), $goods, 'INSERT', '', 'SILENT'))
                {
                    //return $db->error();
                }
            }
        }

        /* 返回 */
        return TRUE;
    }

    /**
     * 商店設置
     */
    function process_config()
    {
        global $ecs, $db;

        /* 查詢設置 */
        $sql = "SELECT * FROM ".$this->sprefix."mall_offer " .
                "WHERE offerid = '1'";
        $row = $this->sdb->getRow($sql);

        $config = array();
        $config['shop_name']        = ecs_iconv($this->scharset, $this->tcharset, addslashes($row['offer_name']));
        $config['shop_title']       = ecs_iconv($this->scharset, $this->tcharset, addslashes($row['offer_shoptitle']));
        $config['shop_desc']        = ecs_iconv($this->scharset, $this->tcharset, addslashes($row['offer_metadesc']));
        $config['shop_address']     = ecs_iconv($this->scharset, $this->tcharset, addslashes($row['offer_addr']));
        $config['service_email']    = $row['offer_email'];
        $config['service_phone']    = $row['offer_tel'];
        $config['icp_number']       = ecs_iconv($this->scharset, $this->tcharset, addslashes($row['offer_certtext']));
        //$config['integral_scale']   = $row['offer_pointtype'] == '0' ? '0' : $row['offer_pointnum'] * 100;
        $config['thumb_width']      = $row['offer_smallsize_w'];
        $config['thumb_height']     = $row['offer_smallsize_h'];
        $config['image_width']      = $row['offer_bigsize_w'];
        $config['image_height']     = $row['offer_bigsize_h'];
        $config['promote_number']   = $row['offer_tejianums'];
        $config['best_number']      = $row['offer_tjnums'];
        $config['new_number']       = $row['offer_newgoodsnums'];
        $config['hot_number']       = $row['offer_hotnums'];
        $config['smtp_host']        = $row['offer_smtp_server'];
        $config['smtp_port']        = $row['offer_smtp_port'];
        $config['smtp_user']        = $row['offer_smtp_user'];
        $config['smtp_pass']        = $row['offer_smtp_password'];
        $config['smtp_mail']        = $row['offer_smtp_email'];

        /* 更新 */
        foreach ($config as $code => $value)
        {
            $sql = "UPDATE " . $ecs->table('shop_config') . " SET " .
                    "value = '$value' " .
                    "WHERE code = '$code' LIMIT 1";
            if (!$db->query($sql, 'SILENT'))
            {
                //return $db->error();
            }
        }

        /* 返回 */
        return TRUE;
    }
}

?>