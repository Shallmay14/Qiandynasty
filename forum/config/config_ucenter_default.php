<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: config_ucenter_default.php 11023 2010-05-20 02:23:09Z monkey $
 */

// ============================================================================
define('UC_CONNECT', 'mysql');				// �s�� UCenter ���覡: mysql/NULL, �q�{���Ůɬ� fscoketopen(), mysql �O�����s�����ƾڮw, ���F�Ĳv, ��ĳ�ĥ� mysql
// �ƾڮw���� (mysql �s����)
define('UC_DBHOST', 'localhost');			// UCenter �ƾڮw�D��
define('UC_DBUSER', 'root');				// UCenter �ƾڮw�Τ�W
define('UC_DBPW', 'root');				// UCenter �ƾڮw�K�X
define('UC_DBNAME', 'ucenter');				// UCenter �ƾڮw�W��
define('UC_DBCHARSET', 'big5');				// UCenter �ƾڮw�r�Ŷ�
define('UC_DBTABLEPRE', '`ucenter`.uc_');		// UCenter �ƾڮw��e��
define('UC_DBCONNECT', '0');				// UCenter �ƾڮw���[�s�� 0=����, 1=���}

// �q�H����
define('UC_KEY', 'yeN3g9EbNfiaYfodV63dI1j8Fbk5HaL7W4yaW4y7u2j4Mf45mfg2v899g451k576');	// �P UCenter ���q�H�K�_, �n�P UCenter �O���@�P
define('UC_API', 'http://localhost/ucenter/branches/1.5.0/server'); // UCenter �� URL �a�}, �b�ե��Y���ɨ̿হ�`�q
define('UC_CHARSET', 'big5');				// UCenter ���r�Ŷ�
define('UC_IP', '127.0.0.1');				// UCenter �� IP, �� UC_CONNECT ���D mysql �覡��, �åB��e���ΪA�Ⱦ��ѪR��W�����D��, �г]�m����
define('UC_APPID', '1');				// ��e���Ϊ� ID

// ============================================================================

define('UC_PPP', '20');

?>