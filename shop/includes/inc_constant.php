<?php

/**
 * ECSHOP 常量
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: inc_constant.php 17063 2010-03-25 06:35:46Z liuhui $
*/

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

/* 圖片處理相關常數 */
define('ERR_INVALID_IMAGE',         1);
define('ERR_NO_GD',                 2);
define('ERR_IMAGE_NOT_EXISTS',      3);
define('ERR_DIRECTORY_READONLY',    4);
define('ERR_UPLOAD_FAILURE',        5);
define('ERR_INVALID_PARAM',         6);
define('ERR_INVALID_IMAGE_TYPE',    7);

/* 插件相關常數 */
define('ERR_COPYFILE_FAILED',       1);
define('ERR_CREATETABLE_FAILED',    2);
define('ERR_DELETEFILE_FAILED',     3);

/* 商品屬性類型常數 */
define('ATTR_TEXT',                 0);
define('ATTR_OPTIONAL',             1);
define('ATTR_TEXTAREA',             2);
define('ATTR_URL',                  3);

/* 會員整合相關常數 */
define('ERR_USERNAME_EXISTS',       1); // 用戶名已經存在
define('ERR_EMAIL_EXISTS',          2); // Email已經存在
define('ERR_INVALID_USERID',        3); // 無效的user_id
define('ERR_INVALID_USERNAME',      4); // 無效的用戶名
define('ERR_INVALID_PASSWORD',      5); // 密碼錯誤
define('ERR_INVALID_EMAIL',         6); // email錯誤
define('ERR_USERNAME_NOT_ALLOW',    7); // 用戶名不允許註冊
define('ERR_EMAIL_NOT_ALLOW',       8); // EMAIL不允許註冊

/* 加入購物車失敗的錯誤代碼 */
define('ERR_NOT_EXISTS',            1); // 商品不存在
define('ERR_OUT_OF_STOCK',          2); // 商品缺貨
define('ERR_NOT_ON_SALE',           3); // 商品已下架
define('ERR_CANNT_ALONE_SALE',      4); // 商品不能單獨銷售
define('ERR_NO_BASIC_GOODS',        5); // 沒有基本件
define('ERR_NEED_SELECT_ATTR',      6); // 需要用戶選擇屬性

/* 購物車商品類型 */
define('CART_GENERAL_GOODS',        0); // 普通商品
define('CART_GROUP_BUY_GOODS',      1); // 團購商品
define('CART_AUCTION_GOODS',        2); // 拍賣商品
define('CART_SNATCH_GOODS',         3); // 奪寶奇兵
define('CART_EXCHANGE_GOODS',       4); // 積分商城

/* 訂單狀態 */
define('OS_UNCONFIRMED',            0); // 未確認
define('OS_CONFIRMED',              1); // 已確認
define('OS_CANCELED',               2); // 已取消
define('OS_INVALID',                3); // 無效
define('OS_RETURNED',               4); // 退貨
define('OS_SPLITED',                5); // 已分單
define('OS_SPLITING_PART',          6); // 部分分單

/* 支付類型 */
define('PAY_ORDER',                 0); // 訂單支付
define('PAY_SURPLUS',               1); // 會員預付款

/* 配送狀態 */
define('SS_UNSHIPPED',              0); // 未發貨
define('SS_SHIPPED',                1); // 已發貨
define('SS_RECEIVED',               2); // 已收貨
define('SS_PREPARING',              3); // 備貨中
define('SS_SHIPPED_PART',           4); // 已發貨(部分商品)
define('SS_SHIPPED_ING',            5); // 發貨中(處理分單)

/* 支付狀態 */
define('PS_UNPAYED',                0); // 未付款
define('PS_PAYING',                 1); // 付款中
define('PS_PAYED',                  2); // 已付款

/* 綜合狀態 */
define('CS_AWAIT_PAY',              100); // 待付款：貨到付款且已發貨且未付款，非貨到付款且未付款
define('CS_AWAIT_SHIP',             101); // 待發貨：貨到付款且未發貨，非貨到付款且已付款且未發貨
define('CS_FINISHED',               102); // 已完成：已確認、已付款、已發貨

/* 缺貨處理 */
define('OOS_WAIT',                  0); // 等待貨物備齊後再發
define('OOS_CANCEL',                1); // 取消訂單
define('OOS_CONSULT',               2); // 與店主協商

/* 帳戶明細類型 */
define('SURPLUS_SAVE',              0); // 為帳戶衝值
define('SURPLUS_RETURN',            1); // 從帳戶提款

/* 評論狀態 */
define('COMMENT_UNCHECKED',         0); // 未審核
define('COMMENT_CHECKED',           1); // 已審核或已回覆(允許顯示)
define('COMMENT_REPLYED',           2); // 該評論的內容屬於回覆

/* 紅包發放的方式 */
define('SEND_BY_USER',              0); // 按用戶發放
define('SEND_BY_GOODS',             1); // 按商品發放
define('SEND_BY_ORDER',             2); // 按訂單發放
define('SEND_BY_PRINT',             3); // 線下發放

/* 廣告的類型 */
define('IMG_AD',                    0); // 圖片廣告
define('FALSH_AD',                  1); // flash廣告
define('CODE_AD',                   2); // 代碼廣告
define('TEXT_AD',                   3); // 文字廣告

/* 是否需要用戶選擇屬性 */
define('ATTR_NOT_NEED_SELECT',      0); // 不需要選擇
define('ATTR_NEED_SELECT',          1); // 需要選擇

/* 用戶中心留言類型 */
define('M_MESSAGE',                 0); // 留言
define('M_COMPLAINT',               1); // 投訴
define('M_ENQUIRY',                 2); // 詢問
define('M_CUSTOME',                 3); // 售後
define('M_BUY',                     4); // 求購
define('M_BUSINESS',                5); // 商家
define('M_COMMENT',                 6); // 評論

/* 團購活動狀態 */
define('GBS_PRE_START',             0); // 未開始
define('GBS_UNDER_WAY',             1); // 進行中
define('GBS_FINISHED',              2); // 已結束
define('GBS_SUCCEED',               3); // 團購成功（可以發貨了）
define('GBS_FAIL',                  4); // 團購失敗

/* 紅包是否發送郵件 */
define('BONUS_NOT_MAIL',            0);
define('BONUS_MAIL_SUCCEED',        1);
define('BONUS_MAIL_FAIL',           2);

/* 商品活動類型 */
define('GAT_SNATCH',                0);
define('GAT_GROUP_BUY',             1);
define('GAT_AUCTION',               2);
define('GAT_POINT_BUY',             3);
define('GAT_PACKAGE',               4); // 超值禮包

/* 帳號變動類型 */
define('ACT_SAVING',                0);     // 帳戶衝值
define('ACT_DRAWING',               1);     // 帳戶提款
define('ACT_ADJUSTING',             2);     // 調節帳戶
define('ACT_OTHER',                99);     // 其他類型

/* 密碼加密方法 */
define('PWD_MD5',                   1);  //md5加密方式
define('PWD_PRE_SALT',              2);  //前置驗證串的加密方式
define('PWD_SUF_SALT',              3);  //後置驗證串的加密方式

/* 文章分類類型 */
define('COMMON_CAT',                1); //普通分類
define('SYSTEM_CAT',                2); //系統默認分類
define('INFO_CAT',                  3); //網店信息分類
define('UPHELP_CAT',                4); //網店幫助分類分類
define('HELP_CAT',                  5); //網店幫助分類

/* 活動狀態 */
define('PRE_START',                 0); // 未開始
define('UNDER_WAY',                 1); // 進行中
define('FINISHED',                  2); // 已結束
define('SETTLED',                   3); // 已處理

/* 驗證碼 */
define('CAPTCHA_REGISTER',          1); //註冊時使用驗證碼
define('CAPTCHA_LOGIN',             2); //登錄時使用驗證碼
define('CAPTCHA_COMMENT',           4); //評論時使用驗證碼
define('CAPTCHA_ADMIN',             8); //後台登錄時使用驗證碼
define('CAPTCHA_LOGIN_FAIL',       16); //登錄失敗後顯示驗證碼
define('CAPTCHA_MESSAGE',          32); //留言時使用驗證碼

/* 優惠活動的優惠範圍 */
define('FAR_ALL',                   0); // 全部商品
define('FAR_CATEGORY',              1); // 按分類選擇
define('FAR_BRAND',                 2); // 按品牌選擇
define('FAR_GOODS',                 3); // 按商品選擇

/* 優惠活動的優惠方式 */
define('FAT_GOODS',                 0); // 送贈品或優惠購買
define('FAT_PRICE',                 1); // 現金減免
define('FAT_DISCOUNT',              2); // 價格打折優惠

/* 評論條件 */
define('COMMENT_LOGIN',             1); //只有登錄用戶可以評論
define('COMMENT_CUSTOM',            2); //只有有過一次以上購買行為的用戶可以評論
define('COMMENT_BOUGHT',            3); //只有購買夠該商品的人可以評論

/* 減庫存時機 */
define('SDT_SHIP',                  0); // 發貨時
define('SDT_PLACE',                 1); // 下訂單時

/* 加密方式 */
define('ENCRYPT_ZC',                1); //zc加密方式
define('ENCRYPT_UC',                2); //uc加密方式

/* 商品類別 */
define('G_REAL',                    1); //實體商品
define('G_CARD',                    0); //虛擬卡

/* 積分兌換 */
define('TO_P',                      0); //兌換到商城消費積分
define('FROM_P',                    1); //用商城消費積分兌換
define('TO_R',                      2); //兌換到商城等級積分
define('FROM_R',                    3); //用商城等級積分兌換

/* 支付寶商家賬戶 */
define('ALIPAY_AUTH', 'gh0bis45h89m5mwcoe85us4qrwispes0');
define('ALIPAY_ID', '2088002052150939');

/* 添加feed事件到UC的TYPE*/
define('BUY_GOODS',                 1); //購買商品
define('COMMENT_GOODS',             2); //添加商品評論

/* 郵件發送用戶 */
define('SEND_LIST', 0);
define('SEND_USER', 1);
define('SEND_RANK', 2);

/* license接口 */
define('LICENSE_VERSION', '1.0');

/* 配送方式 */
define('SHIP_LIST', 'cac|city_express|ems|flat|fpd|post_express|post_mail|presswork|sf_express|sto_express|yto|zto');

?>