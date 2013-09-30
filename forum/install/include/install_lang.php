<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: install_lang.php 32641 2013-02-27 08:39:58Z monkey $
 */

if(!defined('IN_COMSENZ')) {
	exit('Access Denied');
}

define('UC_VERNAME', '���媩');
$lang = array(
	'SC_GBK' => '²�餤�媩',
	'TC_BIG5' => '�c�餤�媩',
	'SC_UTF8' => '²�餤�� UTF8 ��',
	'TC_UTF8' => '�c�餤�� UTF8 ��',
	'EN_ISO' => 'ENGLISH ISO8859',
	'EN_UTF8' => 'ENGLIST UTF-8',

	'title_install' => SOFT_NAME.' �w���Q��',
	'agreement_yes' => '�ڦP�N',
	'agreement_no' => '�ڤ��P�N',
	'notset' => '������',

	'message_title' => '���ܫH��',
	'error_message' => '���~�H��',
	'message_return' => '��^',
	'return' => '��^',
	'install_wizard' => '�w���Q��',
	'config_nonexistence' => '�t�m��󤣦s�b',
	'nodir' => '�ؿ����s�b',
	'redirect' => '�s�����|�۰ʸ��୶���A�L�ݤH�u�z�w�C<br>���D��z���s�����S���۰ʸ���ɡA���I���o��',
	'auto_redirect' => '�s�����|�۰ʸ��୶���A�L�ݤH�u�z�w',
	'database_errno_2003' => '�L�k�s���ƾڮw�A���ˬd�ƾڮw�O�_�ҰʡA�ƾڮw�A�Ⱦ��a�}�O�_���T',
	'database_errno_1044' => '�L�k�Ыطs���ƾڮw�A���ˬd�ƾڮw�W�ٶ�g�O�_���T',
	'database_errno_1045' => '�L�k�s���ƾڮw�A���ˬd�ƾڮw�Τ�W�Ϊ̱K�X�O�_���T',
	'database_errno_1064' => 'SQL �y�k���~',

	'dbpriv_createtable' => '�S��CREATE TABLE�v���A�L�k�~��w��',
	'dbpriv_insert' => '�S��INSERT�v���A�L�k�~��w��',
	'dbpriv_select' => '�S��SELECT�v���A�L�k�~��w��',
	'dbpriv_update' => '�S��UPDATE�v���A�L�k�~��w��',
	'dbpriv_delete' => '�S��DELETE�v���A�L�k�~��w��',
	'dbpriv_droptable' => '�S��DROP TABLE�v���A�L�k�w��',

	'db_not_null' => '�ƾڮw���w�g�w�˹L UCenter, �~��w�˷|�M�ŭ즳�ƾڡC',
	'db_drop_table_confirm' => '�~��w�˷|�M�ť����즳�ƾڡA�z�T�w�n�~���?',

	'writeable' => '�i�g',
	'unwriteable' => '���i�g',
	'old_step' => '�W�@�B',
	'new_step' => '�U�@�B',

	'database_errno_2003' => '�L�k�s���ƾڮw�A���ˬd�ƾڮw�O�_�ҰʡA�ƾڮw�A�Ⱦ��a�}�O�_���T',
	'database_errno_1044' => '�L�k�Ыطs���ƾڮw�A���ˬd�ƾڮw�W�ٶ�g�O�_���T',
	'database_errno_1045' => '�L�k�s���ƾڮw�A���ˬd�ƾڮw�Τ�W�Ϊ̱K�X�O�_���T',
	'database_connect_error' => '�ƾڮw�s�����~',

	'step_title_1' => '�ˬd�w������',
	'step_title_2' => '�]�m�B������',
	'step_title_3' => '�Ыؼƾڮw',
	'step_title_4' => '�w��',
	'step_env_check_title' => '�}�l�w��',
	'step_env_check_desc' => '���ҥH�Τ��ؿ��v���ˬd',
	'step_db_init_title' => '�w�˼ƾڮw',
	'step_db_init_desc' => '���b����ƾڮw�w��',

	'step1_file' => '�ؿ����',
	'step1_need_status' => '�һݪ��A',
	'step1_status' => '��e���A',
	'not_continue' => '�бN�H�W���e�����ץ��A��',

	'tips_dbinfo' => '��g�ƾڮw�H��',
	'tips_dbinfo_comment' => '',
	'tips_admininfo' => '��g�޲z���H��',
	'step_ext_info_title' => '�w�˦��\\�C',
	'step_ext_info_comment' => '�I���i�J�n��',

	'ext_info_succ' => '�w�˦��\\�C',
	'install_submit' => '����',
	'install_locked' => '�w����w�A�w�g�w�˹L�F�A�p�G�z�T�w�n���s�w�ˡA�Ш�A�Ⱦ��W�R��<br /> '.str_replace(ROOT_PATH, '', $lockfile),
	'error_quit_msg' => '�z�����ѨM�H�W���D�A�w�ˤ~�i�H�~��',

	'step_app_reg_title' => '�]�m�B������',
	'step_app_reg_desc' => '�˴��A�Ⱦ����ҥH�γ]�m UCenter',
	'tips_ucenter' => '�ж�g UCenter �����H��',
	'tips_ucenter_comment' => 'UCenter �O Comsenz ���q���~���֤ߪA�ȵ{�ǡADiscuz! Board ���w�˩M�B��̿হ�{�ǡC�p�G�z�w�g�w�ˤF UCenter�A�ж�g�H�U�H���C�_�h�A�Ш� <a href="http://www.discuz.com/" target="blank">Comsenz ���~����</a> �U���åB�w�ˡA�M��A�~��C',

	'advice_mysql_connect' => '���ˬd mysql �Ҷ��O�_���T�[��',
	'advice_gethostbyname' => '�O�_ PHP �t�m���T��F gethostbyname ��ơC���pô�Ŷ��ӡA�T�w�}�ҤF�����\\��',
	'advice_file_get_contents' => '�Ө�ƻݭn php.ini �� allow_url_fopen �ﶵ�}�ҡC���pô�Ŷ��ӡA�T�w�}�ҤF�����\\��',
	'advice_xml_parser_create' => '�Ө�ƻݭn PHP ��� XML�C���pô�Ŷ��ӡA�T�w�}�ҤF�����\\��',
	'advice_fsockopen' => '�Ө�ƻݭn php.ini �� allow_url_fopen �ﶵ�}�ҡC���pô�Ŷ��ӡA�T�w�}�ҤF�����\\��',
	'advice_pfsockopen' => '�Ө�ƻݭn php.ini �� allow_url_fopen �ﶵ�}�ҡC���pô�Ŷ��ӡA�T�w�}�ҤF�����\\��',
	'advice_stream_socket_client' => '�O�_ PHP �t�m���T��F stream_socket_client ���',
	'advice_curl_init' => '�O�_ PHP �t�m���T��F curl_init ���',

	'ucurl' => 'UCenter �� URL',
	'ucpw' => 'UCenter �Щl�H�K�X',
	'ucip' => 'UCenter ��IP�a�}',
	'ucenter_ucip_invalid' => '�榡���~�A�ж�g���T�� IP �a�}',
	'ucip_comment' => '���j�h�Ʊ��p�U�z�i�H����',

	'tips_siteinfo' => '�ж�g���I�H��',
	'sitename' => '���I�W��',
	'siteurl' => '���I URL',

	'forceinstall' => '�j��w��',
	'dbinfo_forceinstall_invalid' => '��e�ƾڮw���w�g�t���P�˪�e�󪺼ƾڪ�A�z�i�H�ק�u��W�e��v���קK�R���ª��ƾڡA�Ϊ̿�ܱj��w�ˡC�j��w�˷|�R���¼ƾڡA�B�L�k��_',

	'click_to_back' => '�I����^�W�@�B',
	'adminemail' => '�t�ΫH�c Email',
	'adminemail_comment' => '�Ω�o�e�{�ǿ��~���i',
	'dbhost_comment' => '�ƾڮw�A�Ⱦ��a�}, �@�묰 localhost',
	'tablepre_comment' => '�P�@�ƾڮw�B��h�ӽ׾®ɡA�Эק�e��',
	'forceinstall_check_label' => '�ڭn�R���ƾڡA�j��w�� !!!',

	'uc_url_empty' => '�z�S����g UCenter �� URL�A�Ъ�^��g',
	'uc_url_invalid' => 'URL �榡���~',
	'uc_url_unreachable' => 'UCenter �� URL �a�}�i���g���~�A���ˬd',
	'uc_ip_invalid' => '�L�k�ѪR�Ӱ�W�A�ж�g���I�� IP',
	'uc_admin_invalid' => 'UCenter �Щl�H�K�X���~�A�Э��s��g',
	'uc_data_invalid' => '�q�H���ѡA���ˬd UCenter ��URL �a�}�O�_���T ',
	'uc_dbcharset_incorrect' => 'UCenter �ƾڮw�r�Ŷ��P��e���Φr�Ŷ����@�P',
	'uc_api_add_app_error' => '�V UCenter �K�[���ο��~',
	'uc_dns_error' => 'UCenter DNS�ѪR���~�A�Ъ�^��g�@�U UCenter �� IP�a�}',

	'ucenter_ucurl_invalid' => 'UCenter ��URL���šA�Ϊ̮榡���~�A���ˬd',
	'ucenter_ucpw_invalid' => 'UCenter ���Щl�H�K�X���šA�Ϊ̮榡���~�A���ˬd',
	'siteinfo_siteurl_invalid' => '���IURL���šA�Ϊ̮榡���~�A���ˬd',
	'siteinfo_sitename_invalid' => '���I�W�٬��šA�Ϊ̮榡���~�A���ˬd',
	'dbinfo_dbhost_invalid' => '�ƾڮw�A�Ⱦ����šA�Ϊ̮榡���~�A���ˬd',
	'dbinfo_dbname_invalid' => '�ƾڮw�W���šA�Ϊ̮榡���~�A���ˬd',
	'dbinfo_dbuser_invalid' => '�ƾڮw�Τ�W���šA�Ϊ̮榡���~�A���ˬd',
	'dbinfo_dbpw_invalid' => '�ƾڮw�K�X���šA�Ϊ̮榡���~�A���ˬd',
	'dbinfo_adminemail_invalid' => '�t�ζl�c���šA�Ϊ̮榡���~�A���ˬd',
	'dbinfo_tablepre_invalid' => '�ƾڪ�e�󬰪šA�Ϊ̮榡���~�A���ˬd',
	'admininfo_username_invalid' => '�޲z���Τ�W���šA�Ϊ̮榡���~�A���ˬd',
	'admininfo_email_invalid' => '�޲z��Email���šA�Ϊ̮榡���~�A���ˬd',
	'admininfo_password_invalid' => '�޲z���K�X���šA�ж�g',
	'admininfo_password2_invalid' => '�⦸�K�X���@�P�A���ˬd',

	'install_dzfull' => '<br><label><input type="radio"'.(getgpc('install_ucenter') != 'no' ? ' checked="checked"' : '').' name="install_ucenter" value="yes" onclick="if(this.checked)$(\'form_items_2\').style.display=\'none\';" /> ���s�w�� Discuz! X (�t UCenter Server)</label>',
	'install_dzonly' => '<br><label><input type="radio"'.(getgpc('install_ucenter') == 'no' ? ' checked="checked"' : '').' name="install_ucenter" value="no" onclick="if(this.checked)$(\'form_items_2\').style.display=\'\';" /> �Ȧw�� Discuz! X (��u���w�w�g�w�˪� UCenter Server)</label>',

	'username' => '�޲z���㸹',
	'email' => '�޲z�� Email',
	'password' => '�޲z���K�X',
	'password_comment' => '�޲z���K�X���ର��',
	'password2' => '���ƱK�X',

	'admininfo_invalid' => '�޲z���H��������A���ˬd�޲z���㸹�A�K�X�A�l�c',
	'dbname_invalid' => '�ƾڮw�W���šA�ж�g�ƾڮw�W��',
	'tablepre_invalid' => '�ƾڪ�e�󬰪šA�Ϊ̮榡���~�A���ˬd',
	'admin_username_invalid' => '�D�k�Τ�W�A�Τ�W���פ�����W�L 15 �ӭ^��r�šA�B����]�t�S��r�šA�@��O����A�r���Ϊ̼Ʀr',
	'admin_password_invalid' => '�K�X�M�W�����@�P�A�Э��s��J',
	'admin_email_invalid' => 'Email �a�}���~�A���l��a�}�w�g�Q�ϥΩΪ̮榡�L�ġA�Ч󴫬���L�a�}',
	'admin_invalid' => '�z���H���޲z���H���S����g����A�ХJ�Ӷ�g�C�Ӷ���',
	'admin_exist_password_error' => '�ӥΤ�w�g�s�b�A�p�G�z�n�]�m���Τᬰ�׾ª��޲z���A�Х��T��J�ӥΤ᪺�K�X�A�Ϊ̽Ч󴫽׾º޲z�����W�r',

	'tagtemplates_subject' => '���D',
	'tagtemplates_uid' => '�Τ� ID',
	'tagtemplates_username' => '�o����',
	'tagtemplates_dateline' => '���',
	'tagtemplates_url' => '�D�D�a�}',

	'uc_version_incorrect' => '�z�� UCenter �A�Ⱥݪ����L�C�A�Фɯ� UCenter �A�Ⱥݨ�̷s�����A�åB�ɯšA�U���a�}�Ghttp://www.comsenz.com/ �C',
	'config_unwriteable' => '�w���Q�ɵL�k�g�J�t�m���, �г]�m config.inc.php �{���ݩʬ��i�g���A(777)',

	'install_in_processed' => '���b�w��...',
	'install_succeed' => '�w�˦��\\�A�I���i�J',
	'install_cloud' => '�w�˦��\\�A�w��}�qDiscuz!�����x<br>Discuz!�����x�P�O�����U�������������y�q�A�W�j�����B���O�A�W�[�������J�C<br>Discuz!�����x�ثe�K�O���ѤFQQ���p�B�˰T���R�B�a��j���B���C���ΡBSOSO���A�ȡCDiscuz!�����x�N���򴣨ѧ�h�u��A�ȶ��ءC<br>�}�qDiscuz!���x���e�A�нT�O�z�������]Discuz!�BUCHome��SupeSite�^�w�g�ɯŨ�Discuz! X3�C',
	'to_install_cloud' => '���x�}�q',
	'to_index' => '�Ȥ��}�q',

	'init_credits_karma' => '�±�',
	'init_credits_money' => '����',

	'init_postno0' => '�ӥD',
	'init_postno1' => '�F�o',
	'init_postno2' => '�O��',
	'init_postno3' => '�a�O',

	'init_support' => '���',
	'init_opposition' => '�Ϲ�',

	'init_group_0' => '�|��',
	'init_group_1' => '�޲z��',
	'init_group_2' => '�W�Ū��D',
	'init_group_3' => '���D',
	'init_group_4' => '�T��o��',
	'init_group_5' => '�T��X��',
	'init_group_6' => '�T�� IP',
	'init_group_7' => '�C��',
	'init_group_8' => '�������ҷ|��',
	'init_group_9' => '�^��',
	'init_group_10' => '�s��W��',
	'init_group_11' => '���U�|��',
	'init_group_12' => '���ŷ|��',
	'init_group_13' => '���ŷ|��',
	'init_group_14' => '���P�|��',
	'init_group_15' => '�׾¤���',

	'init_rank_1' => '�s�ͤJ��',
	'init_rank_2' => '�p�դ��M',
	'init_rank_3' => '��߰O��',
	'init_rank_4' => '�ۥѼ��Z�H',
	'init_rank_5' => '�S�u�@�a',

	'init_cron_1' => '�M�Ť���o����',
	'init_cron_2' => '�M�ť���b�u�ɶ�',
	'init_cron_3' => '�C��ƾڲM�z',
	'init_cron_4' => '�ͤ�έp�P�l�󯬺�',
	'init_cron_5' => '�D�D�^�_�q��',
	'init_cron_6' => '�C�餽�i�M�z',
	'init_cron_7' => '���ɾާ@�M�z',
	'init_cron_8' => '�׾±��s�M�z',
	'init_cron_9' => '�C��D�D�M�z',
	'init_cron_10' => '�C�� X-Space��s�Τ�',
	'init_cron_11' => '�C�g�D�D��s',

	'init_bbcode_1' => '�Ϥ��e��V�u�ʡA�o�ӮĪG���� HTML �� marquee ���ҡA�`�N�G�o�ӮĪG�u�b Internet Explorer �s�����U���ġC',
	'init_bbcode_2' => '�O�J Flash �ʵe',
	'init_bbcode_3' => '��� QQ �b�u���A�A�I�o�ӹϼХi�H�M�L�]�o�^���',
	'init_bbcode_4' => '�W��',
	'init_bbcode_5' => '�U��',
	'init_bbcode_6' => '�O�J Windows media ���W',
	'init_bbcode_7' => '�O�J Windows media ���W�ε��W',

	'init_qihoo_searchboxtxt' =>'��J�����,�ֳt�j�����׾�',
	'init_threadsticky' =>'�����m��,�����m��,�����m��',

	'init_default_style' => '�q�{����',
	'init_default_forum' => '�q�{����',
	'init_default_template' => '�q�{�ҪO�M�t',
	'init_default_template_copyright' => '�_�ʱd���s�Ь�ަ����d�����q',

	'init_dataformat' => 'Y-n-j',
	'init_modreasons' => '�s�i/SPAM\r\n�c�N���\r\n�H�W���e\r\n�夣���D\r\n���Ƶo��\r\n\r\n�ګ��٦P\r\n��~�峹\r\n��Ф��e',
	'init_userreasons' => '�ܵ��O!\r\n�������O�B��\r\n�٤@��!\r\n�s��\r\n�H�w',
	'init_link' => 'Discuz! �x��׾�',
	'init_link_note' => '���ѳ̷s Discuz! ���~�s�D�B�n��U���P�޳N��y',

	'init_promotion_task' => '�������s����',
	'init_gift_task' => '���]������',
	'init_avatar_task' => '�Y��������',

	'license' => '<div class="license"><h1>���媩���v��ĳ �A�Ω󤤤�Τ�</h1>

<p>���v�Ҧ� (c) 2001-2013�A�_�ʱd���s�Ь�ަ����d�����q�O�d�Ҧ��v�Q�C</p>

<p>�P�±z��ܱd�����~�C�Ʊ�ڭ̪��V�O�ର�z���Ѥ@�Ӱ��ħֳt�B�j�j�����I�ѨM��סA�M�j�j�����Ͻ׾¸ѨM��סC�d�����q���}�� http://www.comsenz.com�A���~�x��Q�װϺ��}�� http://www.discuz.net�C</p>

<p>�Τᶷ���G����ĳ�O�z�P�d�����q��������z�ϥαd�����q���Ѫ��U�سn�󲣫~�ΪA�Ȫ��k�ߨ�ĳ�C�L�ױz�O�ӤH�β�´�B�էQ�P�_�B�γ~�p��]�]�A�H�ǲߩM��s���ت��^�A���ݥJ�Ӿ\\Ū����ĳ�A�]�A�K���Ϊ̭���d���d�����K�d���ڤι�z���v�Q����C�бz�f�\\�ñ����Τ��������A�ȱ��ڡC�p�z���P�N���A�ȱ��ڤ�/�αd���H�ɹ�䪺�ק�A�z�����ϥΩΥD�ʨ����d�����q���Ѫ��d�����~�C�_�h�A�z�������d�����~���������A�Ȫ����U�B�n���B�U���B�d�ݵ��ϥΦ欰�N�Q�����z�糧�A�ȱ��ڥ��������������A�]�A�����d����A�ȱ����H�ɩҰ�������ק�C
<p>���A�ȱ��ڤ@���o���ܧ�, �d���N�b�����W���G�ק鷺�e�C�ק�᪺�A�ȱ��ڤ@���b�����޲z��x�W���G�Y���ĥN����Ӫ��A�ȱ��ڡC�z�i�H�ɵn���d���x��׾¬d�\\�̷s���A�ȱ��ڡC�p�G�z��ܱ��������ڡA�Y��ܱz�P�N������ĳ�U�����󪺬����C�p�G�z���P�N���A�ȱ��ڡA�h������o�ϥΥ��A�Ȫ��v�Q�C�z�Y���H�ϥ����ڳW�w�A�d�����q���v�H�ɤ���βפ�z��d�����~���ϥθ��ëO�d�l�s�����k�߳d�����v�Q�C</p>
<p>�b�z�ѡB�P�N�B�ÿ�u����ĳ���������ګ�A��i�}�l�ϥαd�����~�C�z�i��P�d�����q����ñ�q�t�@�ѭ���ĳ�A�H�ɥR�Ϊ̨��N����ĳ�������Ϊ̥��󳡤��C</p></p>

<p>�d���֦����n�󪺥������Ѳ��v�C���n��u�ѳ\\�i��ĳ�A�ëD�X��C�d���u���\\�z�b��u����ĳ�U�����ڪ����p�U�ƻs�B�U���B�w�ˡB�ϥΩΪ̥H��L�覡���q�󥻳n�󪺥\\��Ϊ̪��Ѳ��v�C</p>

<h3>I. ��ĳ�\\�i���v�Q</h3>
<ol>
   <li>�z�i�H�b������u���\\�i��ĳ����¦�W�A�N���n�����Ω�D�ӷ~�γ~�A�Ӥ�����I�n���v�\\�i�O�ΡC</li>
   <li>�z�i�H�b��ĳ�W�w�������M����d�򤺭ק�d�����~���N�X(�p�G�Q���Ѫ���)�άɭ�����H�A���z�������n�D�C</li>
   <li>�z�֦��ϥΥ��n��c�ت������������|����ơB�峹�ά����H�����Ҧ��v�A�ÿW�ߩӾ�P�ϥΥ��n��c�ت��������e���f�֡B�`�N�q�ȡA�T�O�䤣�I�ǥ���H���X�k�v�q�A�W�ߩӾ�]�ϥαd���n��M�A�ȱa�Ӫ������d���A�Y�y���d�����q�ΥΤ�l�����A�z�����H�������v�C</li>
   <li>�Y�z�ݱN�d���n��ΪA�ȥΤ�ӷ~�γ~�A�����t����o�d�����ѭ��\\�i�A�z�b��o�ӷ~���v����A�z�i�H�N���n�����Ω�ӷ~�γ~�A�P�ɨ̾ک��ʶR�����v�������T�w���޳N��������B�޳N����覡�M�޳N������e�A���ʶR�ɨ�_�A�b�޳N����������֦��q�L���w���覡��o���w�d�򤺪��޳N����A�ȡC�ӷ~���v�Τ�ɦ��ϬM�M���X�N�����v�O�A�����N���N�Q�@�����n�Ҽ{�A���S���@�w�Q�įǪ��ӿթΫO�ҡC</li>
   <li>�z�i�H�q�d�����Ѫ����Τ��ߪA�Ȥ��U���A�X�z���������ε{�ǡA�����V���ε{�Ƕ}�o��/�Ҧ��̤�I�������O�ΡC</li>
</ol>

<h3>II. ��ĳ�W�w�������M����</h3>
<ol>
   <li>����d�����q�ѭ��ӷ~���v���e�A���o�N���n��Ω�ӷ~�γ~�]�]�A����������~�����B�g��ʺ����B�H��Q���ةι�{�էQ�������^�C�ʶR�ӷ~���v�еn��http://www.discuz.com�ѦҬ��������A�]�i�H�P�q8610-51282255�A�ѸԱ��C</li>
   <li>���o�糧�n��λP�����p���ӷ~���v�i��X���B�X��B���εo��l�\\�i�ҡC</li>
   <li>�L�צp��A�Y�L�ץγ~�p��B�O�_�g�L�ק�ά��ơB�ק�{�צp��A�u�n�ϥαd�����~������Υ��󳡤��A���g�ѭ��\\�i�A�������}�B���d�����~�W�٩M�d�����q�U�ݺ����]http://www.comsenz.com�B�� http://www.discuz.net�^ ���챵�������O�d�A�Ӥ���M���έק�C</li>
   <li>�T��b�d�����~������Υ��󳡤���¦�W�H�o�i���󬣥ͪ����B�ק睊���βĤT�誩���Ω󭫷s���o�C</li>
   <li>�z�q���Τ��ߤU�������ε{�ǡA���g���ε{�Ƕ}�o��/�Ҧ��̪��ѭ��\\�i�A���o���i��ϦV�u�{�B�ϦV�J�s�B�ϦV�sĶ���A���o�զ۽ƻs�B�ק�B�챵�B����B�J�s�B�o��B�X���B�o�i�P���������l�Ͳ��~�B�@�~���C</li>
   <li>�p�G�z�����u����ĳ�����ڡA�z�����v�N�Q�פ�A�ҳ\\�i���v�Q�N�Q���^�A�P�ɱz���Ӿ�����k�߳d���C</li>
</ol>

<h3>III. ������O�M�K�d�n��</h3>
<ol>
   <li>���n��ΩҪ��a�����O�@�������ѥ�����T�������t�����v�ξ�O���Φ����Ѫ��C</li>
   <li>�Τ�X����@�ӨϥΥ��n��A�z�����A�ѨϥΥ��n�󪺭��I�A�b�|���ʶR���~�޳N�A�Ȥ��e�A�ڭ̤��ӿմ��ѥ���Φ����޳N����B�ϥξ�O�A�]���Ӿ����]�ϥΥ��n��Ӳ��Ͱ��D�������d���C</li>
   <li>�d�����q����ϥΥ��n��c�ت��������Ϊ̽׾¤����峹�ΫH���Ӿ�d���A�����d���ѱz�ۦ�Ӿ�C</li>
   <li>�d�����q�L�k�����ʱ��ѲĤT��W�Ǧ����Τ��ߪ����ε{�ǡA�]�����O�����ε{�Ǫ��X�k�ʡB�w���ʡB����ʡB�u��ʩΫ~�赥�F�z�q���Τ��ߤU�����ε{�ǮɡA�P�N�ۦ�P�_�éӾ�Ҧ����I�A�Ӥ��̿��d�����q�C���b���󱡪p�U�A�d�����q���v�̪k�������Τ��ߪA�ȨñĨ�������ʡA�]�A����������������ε{�Ƕi������A�Ȱ��A�Ȫ������γ����A�O�s�����O���A�æV�����������i�C�Ѧ���z�βĤT�H�i��y�����l���A�d�����q���Ӿ���󪽱��B�����Ϊ̳s�a���d���C</li>
   <li>�d�����q��d�����Ѫ��n��M�A�Ȥ��ήɩʡB�w���ʡB�ǽT�ʤ��@��O�A�ѩ󤣥i�ܤO�]���B�d�����q�L�k����]���]�]�A�«ȧ����B���_�q���^���y���n��ϥΩM�A�Ȥ���βפ�A�ӵ��z�y���l�����A�z�P�N���l�s�d�����q�d���������v�Q�C   6.�d�����q�S�O���бz�`�N�A�d�����q���F�O�٤��q�~�ȵo�i�M�վ㪺�ۥD�v�A�d�����q�֦��H�ɸg�Υ��g�ƥ��q���ӭק�A�Ȥ��e�B����βפ���Υ����n��ϥΩM�A�Ȫ��v�Q�A�ק�|���G��d�����q�������������W�A�@�g���G�����q���C �d�����q��ϭק�Τ���B�פ���Υ����n��ϥΩM�A�Ȫ��v�Q�ӳy���l�����A�d�����q���ݹ�z�Υ���ĤT��t�d�C</li>
</ol>

<p>�����d�����~�̲ץΤ���v��ĳ�B�ӷ~���v�P�޳N�A�Ȫ��ԲӤ��e�A���ѱd�����q�W�a���ѡC�d�����q�֦��b���ƥ��q�������p�U�A�ק���v��ĳ�M�A�Ȼ��ت��v�Q�A�ק�᪺��ĳ�λ��ت��ۧ��ܤ���_���s���v�Τ�ͮġC</p>

<p>�@���z�}�l�w�˱d�����~�A�Y�Q���������z�Ѩñ�������ĳ���U�����ڡA�b�ɦ��W�z���ڱ¤����v�Q���P�ɡA��������������M����C��ĳ�\\�i�d��H�~���欰�A�N�����H�ϥ����v��ĳ�úc���I�v�A�ڭ̦��v�H�ɲפ���v�A�d�O����l�`�A�ëO�d�l�s�����d�����v�O�C</p>

<p>���\\�i��ĳ���ڪ������A�ĤO�Ϊȯɪ��ѨM�A�A�Ω󤤵ؤH���@�M��j���k�ߡC</p>

<p>�Y�z�M�d�������o�ͥ���ȯɩΪ�ĳ�A�������ͦn��ӸѨM�A��Ӥ������A�z�b�������P�N�N�ȯɩΪ�ĳ����d���Ҧb�a�_�ʥ������ϤH���k�|���ҡC�d�����q�֦���H�W�U�����ڤ��e�������v�έק��v�C</p>

<p>�]���姹�^</p>

<p align="right">�d�����q</p>

</div>',

	'uc_installed' => '�z�w�g�w�˹L UCenter�A�p�G�ݭn���s�w�ˡA�ЧR�� data/install.lock ���',
	'i_agree' => '�ڤw�J�Ӿ\\Ū�A�æP�N�W�z���ڤ����Ҧ����e',
	'supportted' => '���',
	'unsupportted' => '�����',
	'max_size' => '���/�̤j�ؤo',
	'project' => '����',
	'ucenter_required' => 'Discuz! �һݰt�m',
	'ucenter_best' => 'Discuz! �̨�',
	'curr_server' => '��e�A�Ⱦ�',
	'env_check' => '�����ˬd',
	'os' => '�ާ@�t��',
	'php' => 'PHP ����',
	'attachmentupload' => '����W��',
	'unlimit' => '������',
	'version' => '����',
	'gdversion' => 'GD �w',
	'allow' => '���\\ ',
	'unix' => '��Unix',
	'diskspace' => '�ϽL�Ŷ�',
	'priv_check' => '�ؿ��B����v���ˬd',
	'func_depend' => '��ƨ̿���ˬd',
	'func_name' => '��ƦW��',
	'check_result' => '�ˬd���G',
	'suggestion' => '��ĳ',
	'advice_mysql' => '���ˬd mysql �Ҷ��O�_���T�[��',
	'advice_fopen' => '�Ө�ƻݭn php.ini �� allow_url_fopen �ﶵ�}�ҡC���pô�Ŷ��ӡA�T�w�}�ҤF�����\\��',
	'advice_file_get_contents' => '�Ө�ƻݭn php.ini �� allow_url_fopen �ﶵ�}�ҡC���pô�Ŷ��ӡA�T�w�}�ҤF�����\\��',
	'advice_xml' => '�Ө�ƻݭn PHP ��� XML�C���pô�Ŷ��ӡA�T�w�}�ҤF�����\\��',
	'none' => '�L',

	'dbhost' => '�ƾڮw�A�Ⱦ�',
	'dbuser' => '�ƾڮw�Τ�W',
	'dbpw' => '�ƾڮw�K�X',
	'dbname' => '�ƾڮw�W',
	'tablepre' => '�ƾڪ�e��',

	'ucfounderpw' => '�Щl�H�K�X',
	'ucfounderpw2' => '���ƳЩl�H�K�X',

	'init_log' => '��l�ưO��',
	'clear_dir' => '�M�ťؿ�',
	'select_db' => '��ܼƾڮw',
	'create_table' => '�إ߼ƾڪ�',
	'succeed' => '���\\ ',

	'install_data' => '���b�w�˼ƾ�',
	'install_test_data' => '���b�w�˪��[�ƾ�',

	'method_undefined' => '���w�q��k',
	'database_nonexistence' => '�ƾڮw�ާ@�ﹳ���s�b',
	'skip_current' => '���L���B',
	'topic' => '�M�D',

);

$msglang = array(
	'config_nonexistence' => '�z�� config.inc.php ���s�b, �L�k�~��w��, �Х� FTP �N�Ӥ��W�ǫ�A�աC',
);

?>