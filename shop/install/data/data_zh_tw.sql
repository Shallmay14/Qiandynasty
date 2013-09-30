-- phpMyAdmin SQL Dump
-- version 2.8.0.3
-- http://www.phpmyadmin.net
--
-- 主機: localhost
-- 生成日期: 2006 年 11 月 02 日 16:55
-- 服務器版本: 3.23.58
-- PHP 版本: 4.4.2
--
-- 數據庫: `ecs_liuw`
--


--
-- 導出表中的數據 `ecs_admin_action`
--

INSERT INTO `ecs_admin_action` (`action_id`, `parent_id`, `action_code`, `relevance`) VALUES
(1, 0, 'goods', ''),
(2, 0, 'cms_manage', ''),
(3, 0, 'users_manage', ''),
(4, 0, 'priv_manage', ''),
(5, 0, 'sys_manage', ''),
(6, 0, 'order_manage', ''),
(7, 0, 'promotion', ''),
(8, 0, 'email', ''),
(9, 0, 'templates_manage', ''),
(10, 0, 'db_manage', ''),
(11, 0, 'sms_manage', ''),
(21, 1, 'goods_manage', ''),
(22, 1, 'remove_back', ''),
(23, 1, 'cat_manage', ''),
(24, 1, 'cat_drop', 'cat_manage'),
(25, 1, 'attr_manage', ''),
(26, 1, 'brand_manage', ''),
(27, 1, 'comment_priv', ''),
(84, 1, 'tag_manage', ''),
(30, 2, 'article_cat', ''),
(31, 2, 'article_manage', ''),
(32, 2, 'shopinfo_manage', ''),
(33, 2, 'shophelp_manage', ''),
(34, 2, 'vote_priv', ''),
(35, 7, 'topic_manage', ''),
(74, 4, 'template_manage', ''),
(73, 3, 'feedback_priv', ''),
(38, 3, 'integrate_users', ''),
(39, 3, 'sync_users', 'integrate_users'),
(40, 3, 'users_manage', ''),
(41, 3, 'users_drop', 'users_manage'),
(42, 3, 'user_rank', ''),
(85, 3, 'surplus_manage', 'account_manage'),
(43, 4, 'admin_manage', ''),
(44, 4, 'admin_drop', 'admin_manage'),
(45, 4, 'allot_priv', 'admin_manage'),
(46, 4, 'logs_manage', ''),
(47, 4, 'logs_drop', 'logs_manage'),
(48, 5, 'shop_config', ''),
(49, 5, 'ship_manage', ''),
(50, 5, 'payment', ''),
(51, 5, 'shiparea_manage', ''),
(52, 5, 'area_manage', ''),
(53, 6, 'order_os_edit', ''),
(54, 6, 'order_ps_edit', 'order_os_edit'),
(55, 6, 'order_ss_edit', 'order_os_edit'),
(56, 6, 'order_edit', 'order_os_edit'),
(57, 6, 'order_view', ''),
(58, 6, 'order_view_finished', ''),
(59, 6, 'repay_manage', ''),
(60, 6, 'booking', ''),
(61, 6, 'sale_order_stats', ''),
(62, 6, 'client_flow_stats', ''),
(78, 7, 'snatch_manage', ''),
(83, 7, 'ad_manage', ''),
(80, 7, 'gift_manage', ''),
(81, 7, 'card_manage', ''),
(70, 1, 'goods_type', ''),
(82, 7, 'pack', ''),
(79, 7, 'bonus_manage', ''),
(75, 5, 'friendlink', ''),
(76, 5, 'db_backup', ''),
(77, 5, 'db_renew', 'db_backup'),
(86, 4, 'agency_manage', ''),
(87, 3, 'account_manage', ''),
(88, 5, 'flash_manage', ''),
(89, 5, 'navigator', ''),
(90, 7, 'auction', ''),
(91, 7, 'group_by', ''),
(92, 7, 'favourable', ''),
(93, 7, 'whole_sale', ''),
(94, 1, 'goods_auto', ''),
(95, 2, 'article_auto', ''),
(96, 5, 'cron', ''),
(97, 5, 'affiliate', ''),
(98, 5, 'affiliate_ck', ''),
(99, 8, 'attention_list', ''),
(100, 8, 'email_list', ''),
(101, 8, 'magazine_list', ''),
(102, 8, 'view_sendlist', ''),
(103, 1, 'virualcard', ''),
(104, 7, 'package_manage', ''),
(105, 1, 'picture_batch', ''),
(106, 1, 'goods_export', ''),
(107, 1, 'goods_batch', ''),
(108, 1, 'gen_goods_script', ''),
(109, 5, 'sitemap', ''),
(110, 5, 'file_priv', ''),
(111, 5, 'file_check', ''),
(112, 9, 'template_select', ''),
(113, 9, 'template_setup', ''),
(114, 9, 'library_manage', ''),
(115, 9, 'lang_edit', ''),
(116, 9, 'backup_setting', ''),
(117, 9, 'mail_template', ''),
(118, 10, 'db_backup', ''),
(119, 10, 'db_renew', ''),
(120, 10, 'db_optimize', ''),
(121, 10, 'sql_query', ''),
(122, 10, 'convert', ''),
(124, 11, 'sms_send', ''),
(128, 7, 'exchange_goods', ''),
(129, 6, 'delivery_view', ''),
(130, 6, 'back_view', ''),
(131, 5, 'reg_fields', ''),
(132, 5, 'shop_authorized', ''),
(133, 5, 'webcollect_manage', ''),
(134, 4, 'suppliers_manage', ''),
(135, 4, 'role_manage', '');


--
--  `ecs_mail_templates`
--
INSERT INTO `ecs_mail_templates` (`template_id`, `template_code`, `is_html`, `template_subject`, `template_content`, `last_modify`, `last_send`, `type`) VALUES
(1, 'send_password', 1, '密碼找回', '{$user_name}您好！<br>\n<br>\n您已經進行了密碼重置的操作，請點擊以下鏈接(或者復制到您的瀏覽器):<br>\n<br>\n<a href="{$reset_email}" target="_blank">{$reset_email}</a><br>\n<br>\n以確認您的新密碼重置操作！<br>\n<br>\n{$shop_name}<br>\n{$send_date}', 1194824789, 0, 'template'),
(2, 'order_confirm', 0, '訂單確認通知', '親愛的{$order.consignee}，妳好！ \n\n我們已經收到您於 {$order.formated_add_time} 提交的訂單，該訂單編號為：{$order.order_sn} 請記住這個編號以便日後的查詢。\n\n{$shop_name}\n{$sent_date}\n\n\n', 1158226370, 0, 'template'),
(3, 'deliver_notice', 1, '發貨通知', '親愛的{$order.consignee}。妳好！</br></br>\n\n您的訂單{$order.order_sn}已於{$send_time}按照您預定的配送方式給您發貨了。</br>\n</br>\n{if $order.invoice_no}發貨單號是{$order.invoice_no}。</br>{/if}\n</br>\n在您收到貨物之後請點擊下面的鏈接確認您已經收到貨物：</br>\n<a href="{$confirm_url}" target="_blank">{$confirm_url}</a></br></br>\n如果您還沒有收到貨物可以點擊以下鏈接給我們留言：</br></br>\n<a href="{$send_msg_url}" target="_blank">{$send_msg_url}</a></br>\n<br>\n再次感謝您對我們的支持。歡迎您的再次光臨。 <br>\n<br>\n{$shop_name} </br>\n{$send_date}', 1194823291, 0, 'template'),
(4, 'order_cancel', 0, '訂單取消', '親愛的{$order.consignee}，妳好！ \n\n您的編號為：{$order.order_sn}的訂單已取消。\n\n{$shop_name}\n{$send_date}', 1156491130, 0, 'template'),
(5, 'order_invalid', 0, '訂單無效', '親愛的{$order.consignee}，妳好！\n\n您的編號為：{$order.order_sn}的訂單無效。\n\n{$shop_name}\n{$send_date}', 1156491164, 0, 'template'),
(6, 'send_bonus', 0, '發紅包', '親愛的{$user_name}您好！\n\n恭喜您獲得了{$count}個紅包，金額{if $count > 1}分別{/if}為{$money}\n\n{$shop_name}\n{$send_date}\n', 1156491184, 0, 'template'),
(7, 'group_buy', 1, '團購商品', '親愛的{$consignee}，您好！<br>\n<br>\n您於{$order_time}在本店參加團購商品活動，所購買的商品名稱為：{$goods_name}，數量：{$goods_number}，訂單號為：{$order_sn}，訂單金額為：{$order_amount}<br>\n<br>\n此團購商品現在已到結束日期，並達到最低價格，您現在可以對該訂單付款。<br>\n<br>\n請點擊下面的鏈接：<br>\n<a href="{$shop_url}" target="_blank">{$shop_url}</a><br>\n<br>\n請盡快登錄到用戶中心，查看您的訂單詳情信息。 <br>\n<br>\n{$shop_name} <br>\n<br>\n{$send_date}', 1194824668, 0, 'template'),
(8, 'register_validate', 1, '郵件驗證', '{$user_name}您好！<br><br>\r\n\r\n這封郵件是 {$shop_name} 發送的。妳收到這封郵件是為了驗證妳註冊郵件地址是否有效。如果您已經通過驗證了，請忽略這封郵件。<br>\r\n請點擊以下鏈接(或者復制到您的瀏覽器)來驗證妳的郵件地址:<br>\r\n<a href="{$validate_email}" target="_blank">{$validate_email}</a><br><br>\r\n\r\n{$shop_name}<br>\r\n{$send_date}', 1162201031, 0, 'template'),
(9, 'virtual_card', 0, '虛擬卡片', '親愛的{$order.consignee}\r\n妳好！您的訂單{$order.order_sn}中{$goods.goods_name} 商品的詳細信息如下:\r\n{foreach from=$virtual_card item=card}\r\n{if $card.card_sn}卡號：{$card.card_sn}{/if}{if $card.card_password}卡片密碼：{$card.card_password}{/if}{if $card.end_date}截至日期：{$card.end_date}{/if}\r\n{/foreach}\r\n再次感謝您對我們的支持。歡迎您的再次光臨。\r\n\r\n{$shop_name} \r\n{$send_date}', 1162201031, 0, 'template'),
(10, 'attention_list', 0, '關註商品', '親愛的{$user_name}您好~\n\n您關註的商品 : {$goods_name} 最近已經更新,請您查看最新的商品信息\n\n{$goods_url}\r\n\r\n{$shop_name} \r\n{$send_date}', 1183851073, 0, 'template'),
(11, 'remind_of_new_order', 0, '新訂單通知', '親愛的店長，您好：\n   快來看看吧，又有新訂單了。\n    訂單號:{$order.order_sn} \n 訂單金額:{$order.order_amount}，\n 用戶購買商品:{foreach from=$goods_list item=goods_data}{$goods_data.goods_name}(貨號:{$goods_data.goods_sn})    {/foreach} \n\n 收貨人:{$order.consignee}， \n 收貨人地址:{$order.address}，\n 收貨人電話:{$order.tel} {$order.mobile}, \n 配送方式:{$order.shipping_name}(費用:{$order.shipping_fee}), \n 付款方式:{$order.pay_name}(費用:{$order.pay_fee})。\n\n               系統提醒\n               {$send_date}', 1196239170, 0, 'template'),
(12, 'goods_booking', 1, '缺貨回復', '親愛的{$user_name}。你好！</br></br>{$dispose_note}</br></br>您提交的缺貨商品鏈接為</br></br><a href="{$goods_link}" target="_blank">{$goods_name}</a></br><br>{$shop_name} </br>{$send_date}', 0, 0, 'template'),
(13, 'user_message', 1, '留言回復', '親愛的{$user_name}。你好！</br></br>對您的留言：</br>{$message_content}</br></br>店主作了如下回復：</br>{$message_note}</br></br>您可以隨時回到店中和店主繼續溝通。</br>{$shop_name}</br>{$send_date}', 0, 0, 'template'),
(14, 'recomment', 1, '用戶評論回復', '親愛的{$user_name}。你好！</br></br>對您的評論：</br>“{$comment}”</br></br>店主作了如下回復：</br>“{$recomment}”</br></br>您可以隨時回到店中和店主繼續溝通。</br>{$shop_name}</br>{$send_date}', 0, 0, 'template');

--
--  `ecs_region`
--

INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('1', '0', '中華民國', '0', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('2', '1', '北部地區', '1', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('3', '1', '中部地區', '1', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('4', '1', '南部地區', '1', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('5', '1', '東部地區', '1', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('6', '1', '離島地區', '1', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('7', '2', '基隆市', '2', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('8', '2', '台北市', '2', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('9', '2', '新北市', '2', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('10', '2', '桃園縣', '2', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('11', '2', '新竹市', '2', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('12', '2', '新竹縣', '2', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('13', '2', '苗栗縣', '2', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('14', '3', '台中市', '2', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('16', '3', '彰化縣', '2', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('17', '3', '南投縣', '2', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('18', '3', '雲林縣', '2', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('19', '4', '嘉義市', '2', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('20', '4', '嘉義縣', '2', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('21', '4', '台南市', '2', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('23', '4', '高雄市', '2', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('25', '4', '屏東縣', '2', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('26', '5', '台東縣', '2', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('27', '5', '花蓮縣', '2', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('28', '5', '宜蘭縣', '2', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('29', '6', '澎湖縣', '2', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('30', '6', '金門縣', '2', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('31', '6', '連江縣', '2', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('32', '7', '仁愛區 200', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('33', '7', '信義區 201', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('34', '7', '中正區 202', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('35', '7', '中山區 203', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('36', '7', '安樂區 204', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('37', '7', '暖暖區 205', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('38', '7', '七堵區 206', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('39', '8', '中正區 100', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('40', '8', '大同區 103', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('41', '8', '中山區 104', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('42', '8', '松山區 105', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('43', '8', '大安區 106', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('44', '8', '萬華區 108', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('45', '8', '信義區 110', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('46', '8', '士林區 111', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('47', '8', '北投區 112', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('48', '8', '內湖區 114', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('49', '8', '南港區 115', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('50', '8', '文山區 116', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('51', '9', '萬里區 207', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('52', '9', '金山區 208', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('53', '9', '板橋區 220', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('54', '9', '汐止區 221', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('55', '9', '深坑區 222', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('56', '9', '石碇區 223', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('57', '9', '瑞芳區 224', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('58', '9', '平溪區 226', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('59', '9', '雙溪區 227', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('60', '9', '貢寮區 228', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('61', '9', '新店區 231', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('62', '9', '坪林區 232', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('63', '9', '烏來區 233', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('64', '9', '永和區 234', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('65', '9', '中和區 235', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('66', '9', '土城區 236', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('67', '9', '三峽區 237', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('68', '9', '樹林區 238', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('69', '9', '三重區 241', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('70', '9', '新莊區 242', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('71', '9', '泰山區 243', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('72', '9', '林口區 244', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('73', '9', '蘆洲區 247', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('74', '9', '五股區 248', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('75', '9', '八里區 249', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('76', '9', '淡水區 251', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('77', '9', '三芝區 252', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('78', '9', '石門區 253', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('79', '10', '中壢市 320', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('80', '10', '平鎮市 324', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('81', '10', '龍潭鄉 325', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('82', '10', '楊梅鎮 326', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('83', '10', '新屋鄉 327', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('84', '10', '觀音鄉 328', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('85', '10', '桃園市 330', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('86', '10', '八德市 334', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('87', '10', '大溪鎮 335', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('88', '10', '龜山鄉 333', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('89', '10', '蘆竹鄉 338', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('90', '10', '復興鄉 336', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('91', '10', '大園鄉 337', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('92', '11', '東區 300', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('93', '11', '香山區 300', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('94', '11', '北區 300', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('95', '12', '竹北市 302', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('96', '12', '湖口鄉 303', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('97', '12', '新豐鄉 304', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('98', '12', '新埔鎮 305', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('99', '12', '關西鎮 306', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('100', '12', '芎林鄉 307', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('101', '12', '寶山鄉 308', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('102', '12', '竹東鎮 310', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('103', '12', '五峰鄉 311', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('104', '12', '橫山鄉 312', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('105', '12', '尖石鄉 313', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('106', '12', '北埔鄉 314', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('107', '12', '峨眉鄉 315', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('108', '13', '竹南鎮 350', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('109', '13', '頭份鎮 351', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('110', '13', '三灣鄉 352', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('111', '13', '南莊鄉 353', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('112', '13', '獅潭鄉 354', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('113', '13', '後龍鎮 356', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('114', '13', '通霄鎮 357', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('115', '13', '苑裡鎮 358', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('116', '13', '苗栗市 360', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('117', '13', '造橋鄉 361', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('118', '13', '頭屋鄉 362', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('119', '13', '公館鄉 363', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('120', '13', '大湖鄉 364', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('121', '13', '泰安鄉 365', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('122', '13', '銅鑼鄉 366', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('123', '13', '三義鄉 367', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('124', '13', '西湖鄉 368', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('125', '13', '卓蘭鎮 369', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('126', '14', '中區 400', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('127', '14', '東區 401', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('128', '14', '南區 402', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('129', '14', '西區 403', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('130', '14', '北區 404', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('131', '14', '北屯區 406', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('132', '14', '西屯區 407', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('133', '14', '南屯區 408', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('134', '14', '太平區 411', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('135', '14', '大裡區 412', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('136', '14', '霧峰區 413', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('137', '14', '烏日區 414', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('138', '14', '豐原區 420', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('139', '14', '後裡區 421', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('140', '14', '石岡區 422', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('141', '14', '東勢區 423', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('142', '14', '和平區 424', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('143', '14', '新社區 426', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('144', '14', '潭子區 427', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('145', '14', '大雅區 428', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('146', '14', '神岡區 429', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('147', '14', '大肚區 432', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('148', '14', '沙鹿區 433', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('149', '14', '龍井區 434', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('150', '14', '梧棲區 435', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('151', '14', '清水區 436', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('152', '14', '大甲區 437', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('153', '14', '外埔區 438', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('154', '14', '大安區 439', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('155', '16', '彰化市 500', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('156', '16', '芬園鄉 502', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('157', '16', '花壇鄉 503', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('158', '16', '秀水鄉 504', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('159', '16', '鹿港鎮 505', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('160', '16', '福興鄉 506', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('161', '16', '線西鄉 507', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('162', '16', '和美鎮 508', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('163', '16', '伸港鄉 509', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('164', '16', '員林鎮 510', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('165', '16', '社頭鄉 511', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('166', '16', '永靖鄉 512', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('167', '16', '埔心鄉 513', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('168', '16', '溪湖鎮 514', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('169', '16', '大村鄉 515', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('170', '16', '埔鹽鄉 516', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('171', '16', '田中鎮 520', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('172', '16', '北斗鎮 521', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('173', '16', '田尾鄉 522', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('174', '16', '埤頭鄉 523', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('175', '16', '溪州鄉 524', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('176', '16', '竹塘鄉 525', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('177', '16', '二林鎮 526', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('178', '16', '大城鄉 527', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('179', '16', '芳苑鄉 528', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('180', '16', '二水鄉 530', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('181', '17', '南投市 540', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('182', '17', '中寮鄉 541', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('183', '17', '草屯鎮 542', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('184', '17', '國姓鄉 544', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('185', '17', '埔里鎮 545', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('186', '17', '仁愛鄉 546', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('187', '17', '名間鄉 551', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('188', '17', '集集鎮 552', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('189', '17', '水裡鄉 553', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('190', '17', '魚池鄉 555', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('191', '17', '信義鄉 556', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('192', '17', '竹山鎮 557', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('193', '17', '鹿谷鄉 558', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('194', '18', '斗南鎮 630', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('195', '18', '大埤鄉 631', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('196', '18', '虎尾鎮 632', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('197', '18', '土庫鎮 633', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('198', '18', '褒忠鄉 634', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('199', '18', '東勢鄉 635', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('200', '18', '台西鄉 636', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('201', '18', '崙背鄉 637', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('202', '18', '麥寮鄉 638', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('203', '18', '斗六市 640', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('204', '18', '林內鄉 643', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('205', '18', '古坑鄉 646', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('206', '18', '荊桐鄉 647', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('207', '18', '西螺鎮 648', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('208', '18', '二崙鄉 649', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('209', '18', '北港鎮 651', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('210', '18', '水林鄉 652', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('211', '18', '口湖鄉 653', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('212', '18', '四湖鄉 654', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('213', '18', '元長鄉 655', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('214', '19', '東區 600', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('215', '19', '西區 600', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('216', '20', '番路鄉 602', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('217', '20', '梅山鄉 603', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('218', '20', '竹崎鄉 604', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('219', '20', '阿里山鄉 605', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('220', '20', '中埔鄉 606', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('221', '20', '大埔鄉 607', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('222', '20', '水上鄉 608', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('223', '20', '鹿草鄉 611', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('224', '20', '太保市 612', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('225', '20', '朴子市 613', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('226', '20', '東石鄉 614', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('227', '20', '六腳鄉 615', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('228', '20', '新港鄉 616', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('229', '20', '民雄鄉 621', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('230', '20', '大林鎮 622', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('231', '20', '溪口鄉 623', '4', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('232', '20', '義竹鄉 624', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('233', '20', '布袋鎮 625', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('234', '21', '中西區 700', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('235', '21', '東區 701', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('236', '21', '南區 702', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('237', '21', '北區 704', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('238', '21', '安平區 708', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('239', '21', '安南區 709', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('240', '21', '永康區 710', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('241', '21', '歸仁區 711', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('242', '21', '新化區 712', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('243', '21', '左鎮區 713', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('244', '21', '玉井區 714', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('245', '21', '楠西區 715', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('246', '21', '南化區 716', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('247', '21', '仁德區 717', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('248', '21', '關廟區 718', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('249', '21', '龍崎區 719', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('250', '21', '官田區 720', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('251', '21', '麻豆區 721', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('252', '21', '佳里區 722', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('253', '21', '西港區 723', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('254', '21', '七股區 724', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('255', '21', '將軍區 725', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('256', '21', '學甲區 726', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('257', '21', '北門區 727', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('258', '21', '新營區 730', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('259', '21', '後壁區 731', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('260', '21', '白河區 732', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('261', '21', '東山區 733', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('262', '21', '六甲區 734', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('263', '21', '下營區 735', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('264', '21', '柳營區 736', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('265', '21', '鹽水區 737', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('266', '21', '善化區 741', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('267', '21', '大內區 742', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('268', '21', '山上區 743', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('269', '21', '新市區 744', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('270', '21', '安定區 745', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('271', '23', '新興區 800', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('272', '23', '前金區 801', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('273', '23', '苓雅區 802', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('274', '23', '鹽埕區 803', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('275', '23', '鼓山區 804', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('276', '23', '旗津區 805', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('277', '23', '前鎮區 806', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('278', '23', '三民區 807', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('279', '23', '楠梓區 811', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('280', '23', '小港區 812', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('281', '23', '左營區 813', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('282', '23', '仁武區 814', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('283', '23', '大社區 815', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('284', '23', '岡山區 820', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('285', '23', '路竹區 821', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('286', '23', '阿蓮區 822', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('287', '23', '田寮區 823', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('288', '23', '燕巢區 824', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('289', '23', '橋頭區 825', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('290', '23', '梓官區 826', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('291', '23', '彌陀區 827', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('292', '23', '永安區 828', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('293', '23', '湖內區 829', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('294', '23', '鳳山區 830', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('295', '23', '大寮區 831', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('296', '23', '林園區 832', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('297', '23', '鳥松區 833', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('298', '23', '大樹區 840', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('299', '23', '旗山區 842', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('300', '23', '美濃區 843', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('301', '23', '六龜區 844', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('302', '23', '內門區 845', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('303', '23', '甲仙區 847', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('304', '23', '桃源區 848', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('305', '23', '那瑪夏區 849', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('306', '23', '茂林區 852', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('307', '23', '茄萣區 853', '3', '0');

INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('308', '25', '屏東市 900', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('309', '25', '三地門鄉 901', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('310', '25', '霧台鄉 902', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('311', '25', '瑪家鄉 903', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('312', '25', '九如鄉 904', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('313', '25', '裡港鄉 905', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('314', '25', '高樹鄉 906', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('315', '25', '鹽埔鄉 907', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('316', '25', '長治鄉 908', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('317', '25', '麟洛鄉 909', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('318', '25', '竹田鄉 911', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('319', '25', '內埔鄉 912', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('320', '25', '萬丹鄉 913', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('321', '25', '潮州鎮 920', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('322', '25', '泰武鄉 921', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('323', '25', '來義鄉 922', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('324', '25', '萬巒鄉 923', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('325', '25', '崁頂鄉 924', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('326', '25', '新埤鄉 925', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('327', '25', '南州鄉 926', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('328', '25', '林邊鄉 927', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('329', '25', '東港鄉 928', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('330', '25', '琉球鄉 929', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('331', '25', '佳冬鄉 931', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('332', '25', '新園鄉 932', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('333', '25', '枋寮鄉 940', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('334', '25', '枋山鄉 941', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('335', '25', '春日鄉 942', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('336', '25', '獅子鄉 943', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('337', '25', '車城鄉 944', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('338', '25', '牡丹鄉 945', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('339', '25', '恆春鎮 946', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('340', '25', '滿州鄉 947', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('341', '26', '台東市 950', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('342', '26', '綠島鄉 951', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('343', '26', '蘭嶼鄉 952', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('344', '26', '延平鄉 953', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('345', '26', '卑南鄉 954', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('346', '26', '鹿野鄉 955', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('347', '26', '關山鎮 956', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('348', '26', '海端鄉 957', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('349', '26', '池上鄉 958', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('350', '26', '東河鄉 959', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('351', '26', '成功鎮 961', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('352', '26', '長濱鄉 962', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('353', '26', '太麻里鄉 963', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('354', '26', '金鋒鄉 964', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('355', '26', '大武鄉 965', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('356', '26', '達仁鄉 966', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('357', '27', '花蓮市 970', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('358', '27', '新城鄉 971', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('359', '27', '秀林鄉 972', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('360', '27', '吉安鄉 973', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('361', '27', '壽豐鄉 974', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('362', '27', '鳳林鎮 975', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('363', '27', '光復鄉 976', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('364', '27', '豐濱鄉 977', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('365', '27', '瑞穗鄉 978', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('366', '27', '萬榮鄉 979', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('367', '27', '玉裡鎮 981', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('368', '27', '卓溪鄉 982', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('369', '27', '富裡鄉 983', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('370', '28', '宜蘭市 260', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('371', '28', '頭城鎮 261', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('372', '28', '礁溪鄉 262', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('373', '28', '壯圍鄉 263', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('374', '28', '羅東鎮 265', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('375', '28', '三星鄉 266', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('376', '28', '五結鄉 268', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('377', '28', '冬山鄉 269', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('378', '29', '馬公市 880', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('379', '29', '西嶼鄉 881', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('380', '29', '望安鄉 882', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('381', '29', '七美鄉 883', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('382', '29', '白沙鄉 884', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('383', '29', '湖西鄉 885', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('384', '30', '金沙鎮 890', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('385', '30', '金湖鎮 891', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('386', '30', '金寧鄉 892', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('387', '30', '金城鎮 893', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('388', '30', '烈嶼鄉 894', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('389', '30', '烏坵鄉 896', '2', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('390', '31', '南竿鄉', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('391', '31', '北竿鄉', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('392', '31', '莒光鄉', '3', '0');
INSERT INTO `ecs_region` ( `region_id`, `parent_id`, `region_name`, `region_type`, `agency_id` ) VALUES  ('393', '31', '東引鄉', '3', '0');

--
-- `ecs_shop_config`
--

INSERT INTO `ecs_shop_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`) VALUES
(1, 0, 'shop_info', 'group', '', '', '', '1'),
(2, 0, 'basic', 'group', '', '', '', '1'),
(3, 0, 'display', 'group', '', '', '', '1'),
(4, 0, 'shopping_flow', 'group', '', '', '', '1'),
(5, 0, 'smtp', 'group', '', '', '', '1'),
(6, 0, 'hidden', 'hidden', '', '', '', '1'),
(7, 0, 'goods', 'group', '', '', '', '1'),
(8, 0, 'sms', 'group', '', '', '', '1'),
(9, 0, 'wap', 'group', '', '', '', '1'),
(101, 1, 'shop_name', 'text', '', '', 'ECSHOP', '1'),
(102, 1, 'shop_title', 'text', '', '', 'ECSHOP演示站', '1'),
(103, 1, 'shop_desc', 'text', '', '', 'ECSHOP演示站', '1'),
(104, 1, 'shop_keywords', 'text', '', '', 'ECSHOP演示站', '1'),
(105, 1, 'shop_country', 'manual', '', '', '1', '1'),
(106, 1, 'shop_province', 'manual', '', '', '2', '1'),
(107, 1, 'shop_city', 'manual', '', '', '52', '1'),
(108, 1, 'shop_address', 'text', '', '', '', '1'),
(109, 1, 'qq', 'text', '', '', '', '1'),
(110, 1, 'ww', 'text', '', '', '', '1'),
(111, 1, 'skype', 'text', '', '', '', '1'),
(112, 1, 'ym', 'text', '', '', '', '1'),
(113, 1, 'msn', 'text', '', '', '', '1'),
(114, 1, 'service_email', 'text', '', '', '', '1'),
(115, 1, 'service_phone', 'text', '', '', '', '1'),
(116, 1, 'shop_closed', 'select', '0,1', '', '0', '1'),
(117, 1, 'close_comment', 'textarea', '', '', '', '1'),
(118, 1, 'shop_logo', 'file', '', '../themes/{$template}/images/', '', '1'),
(119, 1, 'licensed', 'select', '0,1', '', '0', '1'),
(120, 1, 'user_notice', 'textarea', '', '', '用戶中心公告！', '1'),
(121, 1, 'shop_notice', 'textarea', '', '', '', '1'),
(122, 1, 'shop_reg_closed', 'select', '1,0', '', '0', '1'),
(201, 2, 'lang', 'manual', '', '', 'zh_tw', '1'),
(202, 2, 'icp_number', 'text', '', '', '', '1'),
(203, 2, 'icp_file', 'file', '', '../cert/', '', '1'),
(204, 2, 'watermark', 'file', '', '../images/', '', '1'),
(205, 2, 'watermark_place', 'select', '0,1,2,3,4,5', '', '1', '1'),
(206, 2, 'watermark_alpha', 'text', '', '', '65', '1'),
(207, 2, 'use_storage', 'select', '1,0', '', '1', '1'),
(208, 2, 'market_price_rate', 'text', '', '', '1.2', '1'),
(209, 2, 'rewrite', 'select', '0,1,2', '', '0', '1'),
(210, 2, 'integral_name', 'text', '', '', '積分', '1'),
(211, 2, 'integral_scale', 'text', '', '', '1', '1'),
(212, 2, 'integral_percent', 'text', '', '', '1', '1'),
(213, 2, 'sn_prefix', 'text', '', '', 'ECS', '1'),
(214, 2, 'comment_check', 'select', '0,1', '', '1', '1'),
(215, 2, 'no_picture', 'file', '', '../images/', '', '1'),
(218, 2, 'stats_code', 'textarea', '', '', '', '1'),
(219, 2, 'cache_time', 'text', '', '', '3600', '1'),
(220, 2, 'register_points', 'text', '', '', '0', '1'),
(221, 2, 'enable_gzip', 'select', '0,1', '', '0', '1'),
(222, 2, 'top10_time', 'select', '0,1,2,3,4', '', '0', '1'),
(223, 2, 'timezone', 'options', '-12,-11,-10,-9,-8,-7,-6,-5,-4,-3.5,-3,-2,-1,0,1,2,3,3.5,4,4.5,5,5.5,5.75,6,6.5,7,8,9,9.5,10,11,12', '', '8', '1'),
(224, 2, 'upload_size_limit', 'options', '-1,0,64,128,256,512,1024,2048,4096', '', '64', '1'),
(226, 2, 'cron_method', 'select', '0,1', '', '0', '1'),
(227, 2, 'comment_factor', 'select', '0,1,2,3', '', '0', '1'),
(228, 2, 'enable_order_check', 'select', '0,1', '', '1', '1'),
(229, 2, 'default_storage', 'text', '', '', '1', '1'),
(230, 2, 'bgcolor', 'text', '', '', '#FFFFFF', '1'),
(231, 2, 'visit_stats', 'select', 'on,off', '', 'on', '1'),
(232, 2, 'send_mail_on', 'select', 'on,off', '', 'off', '1'),
(233, 2, 'auto_generate_gallery', 'select', '1,0', '', '1', '1'),
(234, 2, 'retain_original_img', 'select', '1,0', '', '1', '1'),
(235, 2, 'member_email_validate', 'select', '1,0', '', '1', '1'),
(236, 2, 'message_board', 'select', '1,0', '', '1', '1'),
(239, 2, 'certificate_id', 'hidden', '', '', '', '1'),
(240, 2, 'token', 'hidden', '', '', '', '1'),
(241, 2, 'certi', 'hidden', '', '', 'http://service.shopex.cn/openapi/api.php', '1'),
(301, 3, 'date_format', 'hidden', '', '', 'Y-m-d', '1'),
(302, 3, 'time_format', 'text', '', '', 'Y-m-d H:i:s', '1'),
(303, 3, 'currency_format', 'text', '', '', 'NT%s元', '1'),
(304, 3, 'thumb_width', 'text', '', '', '100', '1'),
(305, 3, 'thumb_height', 'text', '', '', '100', '1'),
(306, 3, 'image_width', 'text', '', '', '230', '1'),
(307, 3, 'image_height', 'text', '', '', '230', '1'),
(312, 3, 'top_number', 'text', '', '', '10', '1'),
(313, 3, 'history_number', 'text', '', '', '5', '1'),
(314, 3, 'comments_number', 'text', '', '', '5', '1'),
(315, 3, 'bought_goods', 'text', '', '', '3', '1'),
(316, 3, 'article_number', 'text', '', '', '8', '1'),
(317, 3, 'goods_name_length', 'text', '', '', '7', '1'),
(318, 3, 'price_format', 'select', '0,1,2,3,4,5', '', '5', '1'),
(319, 3, 'page_size', 'text', '', '', '10', '1'),
(320, 3, 'sort_order_type', 'select', '0,1,2', '', '0', '1'),
(321, 3, 'sort_order_method', 'select', '0,1', '', '0', '1'),
(322, 3, 'show_order_type', 'select', '0,1,2', '', '1', '1'),
(323, 3, 'attr_related_number', 'text', '', '', '5', '1'),
(324, 3, 'goods_gallery_number', 'text', '', '', '5', '1'),
(325, 3, 'article_title_length', 'text', '', '', '16', '1'),
(326, 3, 'name_of_region_1', 'text', '', '', '國家', '1'),
(327, 3, 'name_of_region_2', 'text', '', '', '區域', '1'),
(328, 3, 'name_of_region_3', 'text', '', '', '縣市', '1'),
(329, 3, 'name_of_region_4', 'text', '', '', '鄉鎮市區', '1'),
(330, 3, 'search_keywords', 'text', '', '', '', '0'),
(332, 3, 'related_goods_number', 'text', '', '', '4', '1'),
(333, 3, 'help_open', 'select', '0,1', '', '1', '1'),
(334, 3, 'article_page_size', 'text', '', '', '10', '1'),
(335, 3, 'page_style', 'select', '0,1', '', '1', '1'),
(336, 3, 'recommend_order', 'select', '0,1', '', '0', '1'),
(337, 3, 'index_ad', 'hidden', '', '', 'sys', '1'),
(401, 4, 'can_invoice', 'select', '1,0', '', '1', '1'),
(402, 4, 'use_integral', 'select', '1,0', '', '1', '1'),
(403, 4, 'use_bonus', 'select', '1,0', '', '1', '1'),
(404, 4, 'use_surplus', 'select', '1,0', '', '1', '1'),
(405, 4, 'use_how_oos', 'select', '1,0', '', '1', '1'),
(406, 4, 'send_confirm_email', 'select', '1,0', '', '0', '1'),
(407, 4, 'send_ship_email', 'select', '1,0', '', '0', '1'),
(408, 4, 'send_cancel_email', 'select', '1,0', '', '0', '1'),
(409, 4, 'send_invalid_email', 'select', '1,0', '', '0', '1'),
(410, 4, 'order_pay_note', 'select', '1,0', '', '1', '1'),
(411, 4, 'order_unpay_note', 'select', '1,0', '', '1', '1'),
(412, 4, 'order_ship_note', 'select', '1,0', '', '1', '1'),
(413, 4, 'order_receive_note', 'select', '1,0', '', '1', '1'),
(414, 4, 'order_unship_note', 'select', '1,0', '', '1', '1'),
(415, 4, 'order_return_note', 'select', '1,0', '', '1', '1'),
(416, 4, 'order_invalid_note', 'select', '1,0', '', '1', '1'),
(417, 4, 'order_cancel_note', 'select', '1,0', '', '1', '1'),
(418, 4, 'invoice_content', 'textarea', '', '', '', '1'),
(419, 4, 'anonymous_buy', 'select', '1,0', '', '1', '1'),
(420, 4, 'min_goods_amount', 'text', '', '', '0', '1'),
(421, 4, 'one_step_buy', 'select', '1,0', '', '0', '1'),
(422, 4, 'invoice_type', 'manual', '', '', 'a:2:{s:4:"type";a:3:{i:0;s:1:"1";i:1;s:1:"2";i:2;s:0:"";}s:4:"rate";a:3:{i:0;d:1;i:1;d:1.5;i:2;d:0;}}', '1'),
(423, 4, 'stock_dec_time', 'select', '1,0', '', '0', '1'),
(424, 4, 'cart_confirm', 'options', '1,2,3,4', '', '3', '0'),
(425, 4, 'send_service_email', 'select', '1,0', '', '0', '1'),
(426, 4, 'show_goods_in_cart', 'select', '1,2,3', '', '3', '1'),
(427, 4, 'show_attr_in_cart', 'select', '1,0', '', '1', '1'),
(501, 5, 'smtp_host', 'text', '', '', 'localhost', '1'),
(502, 5, 'smtp_port', 'text', '', '', '25', '1'),
(503, 5, 'smtp_user', 'text', '', '', '', '1'),
(504, 5, 'smtp_pass', 'password', '', '', '', '1'),
(505, 5, 'smtp_mail', 'text', '', '', '', '1'),
(506, 5, 'mail_charset', 'select', 'UTF8,GB2312,BIG5', '', 'UTF8', '1'),
(507, 5, 'mail_service', 'select', '0,1', '', '0', '0'),
(508, 5, 'smtp_ssl', 'select', '0,1', '', '0', '0'),
(601, 6, 'integrate_code', 'hidden', '', '', 'ecshop', '1'),
(602, 6, 'integrate_config', 'hidden', '', '', '', '1'),
(603, 6, 'hash_code', 'hidden', '', '', '31693422540744c0a6b6da635b7a5a93', '1'),
(604, 6, 'template', 'hidden', '', '', 'default', '1'),
(605, 6, 'install_date', 'hidden', '', '', '1224919217', '1'),
(606, 6, 'ecs_version', 'hidden', '', '', 'v2.7.2', '1'),
(607, 6, 'sms_user_name', 'hidden', '', '', '', '1'),
(608, 6, 'sms_password', 'hidden', '', '', '', '1'),
(609, 6, 'sms_auth_str', 'hidden', '', '', '', '1'),
(610, 6, 'sms_domain', 'hidden', '', '', '', '1'),
(611, 6, 'sms_count', 'hidden', '', '', '', '1'),
(612, 6, 'sms_total_money', 'hidden', '', '', '', '1'),
(613, 6, 'sms_balance', 'hidden', '', '', '', '1'),
(614, 6, 'sms_last_request', 'hidden', '', '', '', '1'),
(616, 6, 'affiliate', 'hidden', '', '', 'a:3:{s:6:"config";a:7:{s:6:"expire";d:24;s:11:"expire_unit";s:4:"hour";s:11:"separate_by";i:0;s:15:"level_point_all";s:2:"5%";s:15:"level_money_all";s:2:"1%";s:18:"level_register_all";i:2;s:17:"level_register_up";i:60;}s:4:"item";a:4:{i:0;a:2:{s:11:"level_point";s:3:"60%";s:11:"level_money";s:3:"60%";}i:1;a:2:{s:11:"level_point";s:3:"30%";s:11:"level_money";s:3:"30%";}i:2;a:2:{s:11:"level_point";s:2:"7%";s:11:"level_money";s:2:"7%";}i:3;a:2:{s:11:"level_point";s:2:"3%";s:11:"level_money";s:2:"3%";}}s:2:"on";i:1;}', '1'),
(617, 6, 'captcha', 'hidden', '', '', '36', '1'),
(618, 6, 'captcha_width', 'hidden', '', '', '100', '1'),
(619, 6, 'captcha_height', 'hidden', '', '', '20', '1'),
(620, 6, 'sitemap', 'hidden', '', '', 'a:6:{s:19:"homepage_changefreq";s:6:"hourly";s:17:"homepage_priority";s:3:"0.9";s:19:"category_changefreq";s:6:"hourly";s:17:"category_priority";s:3:"0.8";s:18:"content_changefreq";s:6:"weekly";s:16:"content_priority";s:3:"0.7";}', '0'),
(621, 6, 'points_rule', 'hidden', '', '', '', '0'),
(622, 6, 'flash_theme', 'hidden', '', '', 'dynfocus', '1'),
(623, 6, 'stylename', 'hidden', '', '', '', 1),
(701, 7, 'show_goodssn', 'select', '1,0', '', '1', '1'),
(702, 7, 'show_brand', 'select', '1,0', '', '1', '1'),
(703, 7, 'show_goodsweight', 'select', '1,0', '', '1', '1'),
(704, 7, 'show_goodsnumber', 'select', '1,0', '', '1', '1'),
(705, 7, 'show_addtime', 'select', '1,0', '', '1', '1'),
(706, 7, 'goodsattr_style', 'select', '1,0', '', '1', '1'),
(707, 7, 'show_marketprice', 'select', '1,0', '', '1', '1'),
(801, 8, 'sms_shop_mobile', 'text', '', '', '', '1'),
(802, 8, 'sms_order_placed', 'select', '1,0', '', '0', '1'),
(803, 8, 'sms_order_payed', 'select', '1,0', '', '0', '1'),
(804, 8, 'sms_order_shipped', 'select', '1,0', '', '0', '1'),
(901, 9, 'wap_config', 'select', '1,0', '', '0', '1'),
(902, 9, 'wap_logo', 'file', '', '../images/', '', '1'),
(903, 2, 'message_check', 'select', '1,0', '', '1', '1');
--
-- user_rank
--
INSERT INTO `ecs_user_rank` (`rank_id`, `rank_name`, `min_points`, `max_points`, `discount`, `show_price`, `special_rank`)
VALUES (NULL, '註冊用戶', '0', '10000', '100', 1, 0);


-- 文章默認分類
INSERT INTO `ecs_article_cat` (`cat_id`, `cat_name`, `cat_type`, `keywords`, `cat_desc`, `sort_order`, `parent_id`) VALUES (1, '系統分類', 2, '', '系統保留分類', 50, 0);
INSERT INTO `ecs_article_cat` (`cat_id`, `cat_name`, `cat_type`, `keywords`, `cat_desc`, `sort_order`, `parent_id`) VALUES (2, '網店信息', 3, '', '網店信息分類', 50, 1);
INSERT INTO `ecs_article_cat` (`cat_id`, `cat_name`, `cat_type`, `keywords`, `cat_desc`, `sort_order`, `parent_id`) VALUES (3, '網店幫助分類', 4, '', '網店幫助分類', 50, 1);

--
-- `ecs_article`
--

INSERT INTO `ecs_article` (`article_id`, `cat_id`, `title`, `content`, `author`, `author_email`, `keywords`, `article_type`, `is_open`, `add_time`, `file_url`, `open_type`) VALUES
(1, 2, '免責條款', '', '', '', '', 0, 1, UNIX_TIMESTAMP(), '', 0),
(2, 2, '隱私保護', '', '', '', '', 0, 1, UNIX_TIMESTAMP(), '', 0),
(3, 2, '咨詢熱點', '', '', '', '', 0, 1, UNIX_TIMESTAMP(), '', 0),
(4, 2, '聯系我們', '', '', '', '', 0, 1, UNIX_TIMESTAMP(), '', 0),
(5, 2, '公司簡介', '', '', '', '', 0, 1, UNIX_TIMESTAMP(), '', 0),
(6, -1, '用戶協議', '', '', '', '', 0, 1, UNIX_TIMESTAMP(), '', 0);

--
-- `ecs_template`
--

INSERT INTO `ecs_template` (`filename`, `region`, `library`, `sort_order`, `id`, `number`, `type`, `theme`, `remarks`) VALUES
('index', '左邊區域', '/library/vote_list.lbi', 8, 0, 0, 0, 'default', ''),
('index', '左邊區域', '/library/email_list.lbi', 9, 0, 0, 0, 'default', ''),
('index', '左邊區域', '/library/order_query.lbi', 6, 0, 0, 0, 'default', ''),
('index', '左邊區域', '/library/cart.lbi', 0, 0, 0, 0, 'default', ''),
('index', '左邊區域', '/library/promotion_info.lbi', 3, 0, 0, 0, 'default', ''),
('index', '左邊區域', '/library/auction.lbi', 4, 0, 3, 0, 'default', ''),
('index', '左邊區域', '/library/group_buy.lbi', 5, 0, 3, 0, 'default', ''),
('index', '', '/library/recommend_promotion.lbi', 0, 0, 4, 0, 'default', ''),
('index', '右邊主區域', '/library/recommend_hot.lbi', 2, 0, 10, 0, 'default', ''),
('index', '右邊主區域', '/library/recommend_new.lbi', 1, 0, 10, 0, 'default', ''),
('index', '右邊主區域', '/library/recommend_best.lbi', 0, 0, 10, 0, 'default', ''),
('index', '左邊區域', '/library/invoice_query.lbi', 7, 0, 0, 0, 'default', ''),
('index', '左邊區域', '/library/top10.lbi', 2, 0, 0, 0, 'default', ''),
('index', '左邊區域', '/library/category_tree.lbi', 1, 0, 0, 0, 'default', ''),
('index', '', '/library/brands.lbi', '0', '0', '11', '0', 'default', ''),
('category', '左邊區域', '/library/category_tree.lbi', 1, 0, 0, 0, 'default', ''),
('category', '右邊區域', '/library/recommend_best.lbi', 0, 0, 5, 0, 'default', ''),
('category', '右邊區域', '/library/goods_list.lbi', 1, 0, 0, 0, 'default', ''),
('category', '右邊區域', '/library/pages.lbi', 2, 0, 0, 0, 'default', ''),
('category', '左邊區域', '/library/cart.lbi', 0, 0, 0, 0, 'default', ''),
('category', '左邊區域', '/library/price_grade.lbi', 3, 0, 0, 0, 'default', ''),
('category', '左邊區域', '/library/filter_attr.lbi', 2, 0, 0, 0, 'default', '');

--
-- 會員註冊項 ecs_reg_fields
--

INSERT INTO `ecs_reg_fields` (`id`, `reg_field_name`, `dis_order`, `display`, `type`, `is_need`) VALUES 
(1, 'MSN', 0, 1, 1, 1), 
(2, 'QQ', 0, 1, 1, 1), 
(3, '辦公電話', 0, 1, 1, 1), 
(4, '家庭電話', 0, 1, 1, 1), 
(5, '手機', 0, 1, 1, 1),
(6, '密碼找回問題', 0, 1, 1, 1);
