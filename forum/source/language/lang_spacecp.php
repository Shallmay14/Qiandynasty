<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: lang_spacecp.php 32426 2013-01-15 10:00:21Z liulanbo $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$lang = array(

	'by' => '�q�L',
	'tab_space' => ' ',

	'share' => '����',
	'share_action' => '���ɤF',

	'pm_comment' => '�����I��',
	'pm_thread_about' => '����z�b�u{subject}�v�����l',

	'wall_pm_subject' => '�z�n�A�ڵ��z�d���F',
	'wall_pm_message' => '�ڦb�z���d���O���z�d���F�A[url=\\1]�I���o�̥h�d���O�ݬݧa[/url]',
	'reward' => '�a��',
	'reward_info' => '�ѻP�벼�i��o  \\1 �n��',
	'poll_separator' => '"�B"',

	'pm_report_content' => '<a href="home.php?mod=space&uid={reporterid}" target="_blank">{reportername}</a>�|���u�����G<br>�Ӧ�<a href="home.php?mod=space&uid={uid}" target="_blank">{username}</a>���u����<br>���e�G{message}',
	'message_can_not_send_1' => '�o�e���ѡA�z��e�W�X�F24�p�ɤ���H�|�ܪ��W��',
	'message_can_not_send_2' => '�⦸�o�e�u�����ӧ֡A�еy�ԦA�o�e',
	'message_can_not_send_3' => '��p�A�z���൹�D�n�ͧ�q�o�e�u����',
	'message_can_not_send_4' => '��p�A�z�ثe�٤���ϥεo�e�u�����\\��',
	'message_can_not_send_5' => '�z�W�X�F24�p�ɤ��s��|�ܪ��W��',
	'message_can_not_send_6' => '���̽��F�z���u����',
	'message_can_not_send_7' => '�W�L�F�s��H�ƤW��',
	'message_can_not_send_8' => '��p�A�z���൹�ۤv�o�u����',
	'message_can_not_send_9' => '����H���ũι��̽��F�z���u����',
	'message_can_not_send_10' => '�o�_�s��H�Ƥ���p���H',
	'message_can_not_send_11' => '�ӷ|�ܤ��s�b',
	'message_can_not_send_12' => '��p�A�z�S���v���ާ@',
	'message_can_not_send_13' => '�o���O�s�����',
	'message_can_not_send_14' => '�o���O�p�H����',
	'message_can_not_send_15' => '�ƾڦ��~',
	'message_can_not_send_16' => '�z�W�X�F24�p�ɤ��o�u�����ƶq���W��',
	'message_can_not_send_onlyfriend' => '�ӥΤ�u�����n�͵o�e���u����',


	'friend_subject' => '<a href="{url}" target="_blank">{username} �ШD�[�z���n��</a>',
	'friend_request_note' => '�A�����G{note}',
	'comment_friend' =>'<a href="\\2" target="_blank">\\1 ���z�d���F</a>',
	'photo_comment' => '<a href="\\2" target="_blank">\\1 ���פF�z���Ӥ�</a>',
	'blog_comment' => '<a href="\\2" target="_blank">\\1 ���פF�z����x</a>',
	'poll_comment' => '<a href="\\2" target="_blank">\\1 ���פF�z���벼</a>',
	'share_comment' => '<a href="\\2" target="_blank">\\1 ���פF�z������</a>',
	'friend_pm' => '<a href="\\2" target="_blank">\\1 ���z�o�p�H�F</a>',
	'poke_subject' => '<a href="\\2" target="_blank">\\1 �V�z���۩I</a>',
	'mtag_reply' => '<a href="\\2" target="_blank">\\1 �^�_�F�z�����D</a>',
	'event_comment' => '<a href="\\2" target="_blank">\\1 ���פF�z������</a>',

	'friend_pm_reply' => '\\1 �^�_�F�z���p�H',
	'comment_friend_reply' => '\\1 �^�_�F�z���d��',
	'blog_comment_reply' => '\\1 �^�_�F�z����x����',
	'photo_comment_reply' => '\\1 �^�_�F�z���Ӥ�����',
	'poll_comment_reply' => '\\1 �^�_�F�z���벼����',
	'share_comment_reply' => '\\1 �^�_�F�z�����ɵ���',
	'event_comment_reply' => '\\1 �^�_�F�z�����ʵ���',

	'mail_my' => '�n�ͻP�ڪ����ʴ���',
  	'mail_system' => '�t�δ���',

	'invite_subject' => '{username}�ܽбz�[�J{sitename}�A�æ����n��',
	'invite_massage' => '<table border="0">
		<tr>
		<td valign="top">{avatar}</td>
		<td valign="top">
		<h3>Hi�A�ڬO{username}�A�ܽбz�]�[�J{sitename}�æ����ڪ��n��</h3><br>
		�Х[�J��ڪ��n�ͤ��A�z�N�i�H�A�ѧڪ���p�A�P�ڤ@�_��y�A�H�ɻP�ګO���pô�C<br>
		<br>
		�ܽЪ����G<br>{saymsg}
		<br><br>
		<strong>�бz�I���H�U�챵�A�����n���ܽСG</strong><br>
		<a href="{inviteurl}">{inviteurl}</a><br>
		<br>
		<strong>�p�G�z�֦�{sitename}�W�����㸹�A���I���H�U�챵�d�ݧڪ��ӤH�D���G</strong><br>
		<a href="{siteurl}home.php?mod=space&uid={uid}">{siteurl}home.php?mod=space&uid={uid}</a><br>
		</td></tr></table>',

	'app_invite_subject' => '{username}�ܽбz�[�J{sitename}�A�@�_�Ӫ�{appname}',
	'app_invite_massage' => '<table border="0">
		<tr>
		<td valign="top">{avatar}</td>
		<td valign="top">
		<h3>Hi�A�ڬO{username}�A�b{sitename}�W�� {appname}�A�ܽбz�]�[�J�@�_��</h3><br>
		<br>
		�ܽЪ����G<br>
		{saymsg}
		<br><br>
		<strong>�бz�I���H�U�챵�A�����n���ܽФ@�_��{appname}�G</strong><br>
		<a href="{inviteurl}">{inviteurl}</a><br>
		<br>
		<strong>�p�G�z�֦�{sitename}�W�����㸹�A���I���H�U�챵�d�ݧڪ��ӤH�D���G</strong><br>
		<a href="{siteurl}home.php?mod=space&uid={uid}">{siteurl}home.php?mod=space&uid={uid}</a><br>
		</td></tr></table>',

	'person' => '�H',
	'delete' => '�R��',

	'space_update' => '{actor} �QSHOW�F�@�U',

	'active_email_subject' => '�z���l�c�E���l��',
	'active_email_msg' => '�нƻs�U�����E���챵���s�����i��X�ݡA�H�K�E���z���l�c�C<br>�l�c�E���챵:<br><a href="{url}" target="_blank">{url}</a>',
	'share_space' => '���ɤF�@�ӥΤ�',
	'share_blog' => '���ɤF�@�g��x',
	'share_album' => '���ɤF�@�ӬۥU',
	'default_albumname' => '�q�{�ۥU',
	'share_image' => '���ɤF�@�i�Ϥ�',
	'share_article' => '���ɤF�@�g�峹',
	'album' => '�ۥU',
	'share_thread' => '���ɤF�@�ө��l',
	'mtag' => '{$_G[setting][navs][3][navname]}',
	'share_mtag' => '���ɤF�@��{$_G[setting][navs][3][navname]}',
	'share_mtag_membernum' => '�{�� {membernum} �W����',
	'share_tag' => '���ɤF�@�Ӽ���',
	'share_tag_blognum' => '�{�� {blognum} �g��x',
	'share_link' => '���ɤF�@�Ӻ��}',
	'share_video' => '���ɤF�@�ӵ��W',
	'share_music' => '���ɤF�@�ӭ���',
	'share_flash' => '���ɤF�@�� Flash',
	'share_event' => '���ɤF�@�Ӭ���',
	'share_poll' => '���ɤF�@��\\1�벼',
	'event_time' => '���ʮɶ�',
	'event_location' => '���ʦa�I',
	'event_creator' => '�o�_�H',
	'the_default_style' => '�q�{����',
	'the_diy_style' => '�۩w�q����',

	'thread_edit_trail' => '<ins class="modify">[�����D�� \\1 �� \\2 �s��]</ins>',
	'create_a_new_album' => '�ЫؤF�s�ۥU',
	'not_allow_upload' => '�z�{�b�S���v���W�ǹϤ�',
	'not_allow_upload_extend' => '�����\\�W��{extend}�������Ϥ�',
	'files_can_not_exceed_size' => '{extend}����󤣯�W�L{size}',
	'get_passwd_subject' => '���^�K�X�l��',
	'get_passwd_message' => '�z�u�ݦb����ШD�᪺�T�Ѥ����A�q�L�I���U�����챵���m�z���K�X�G<br />\\1<br />(�p�G�W�����O�챵�Φ��A�бN�a�}��u�ߩ����s�����a�}��A�X��)<br />�W�����������}��A��J�s���K�X�ᴣ��A����z�Y�i�ϥηs���K�X�n���F�C',
	'file_is_too_big' => '���L�j',

	'take_part_in_the_voting' => '{actor} �ѻP�F {touser} ��{reward}�벼 <a href="{url}" target="_blank">{subject}</a>',
	'lack_of_access_to_upload_file_size' => '�L�k����W�Ǥ��j�p',
	'only_allows_upload_file_types' => '�u���\\�W��jpg�Bjpeg�Bgif�Bpng�зǮ榡���Ϥ�',
	'unable_to_create_upload_directory_server' => '�A�Ⱦ��L�k�ЫؤW�ǥؿ�',
	'inadequate_capacity_space' => '�Ŷ��e�q�����A����W�Ƿs����',
	'mobile_picture_temporary_failure' => '�L�k�ಾ�{�ɤ���A�Ⱦ����w�ؿ�',
	'ftp_upload_file_size' => '���{�W�ǹϤ�����',
	'comment' => '����',
	'upload_a_new_picture' => '�W�ǤF�s�Ϥ�',
	'upload_album' => '��s�F�ۥU',
	'the_total_picture' => '�@ \\1 �i�Ϥ�',

	'space_open_subject' => '�֨ӥ��z�@�U�z���ӤH�D���a',
	'space_open_message' => 'hi�A�ڤ��ѥh���X�F�@�U�z���ӤH�D���A�o�{�z�ۤv�٨S�����z�L�O�C���֨Ӭݬݧa�C�a�}�O�G\\1space.php',



	'apply_mtag_manager' => '�Q�ӽЦ��� <a href="\\1" target="_blank">\\2</a> ���s�D�A�z�Ѧp�U:\\3�C<a href="\\1" target="_blank">(�I���o�̶i�J�޲z)</a>',


	'magicunit' => '��',
	'magic_note_wall' => '{actor}�b�d���O�W���z<a href="{url}" target="_blank">�d��</a>',
	'magic_call' => '�b��x���I�F�z���W�A<a href="{url}" target="_blank">�֥h�ݬݧa</a>',


	'present_user_magics' => '�z����F�޲z���ذe���D��G\\1',
	'has_not_more_doodle' => '�z�S����~�O�F',

	'do_stat_login' => '�ӳX�Τ�',
	'do_stat_mobilelogin' => '����X��',
	'do_stat_connectlogin' => 'QQ�n���X��',
	'do_stat_register' => '�s���U�Τ�',
	'do_stat_invite' => '�n���ܽ�',
	'do_stat_appinvite' => '�����ܽ�',
	'do_stat_add' => '�H���o�G',
	'do_stat_comment' => '�H������',
	'do_stat_space' => '����',
	'do_stat_doing' => '�O��',
	'do_stat_blog' => '��x',
	'do_stat_activity' => '����',
	'do_stat_reward' => '�a��',
	'do_stat_debate' => '�G��',
	'do_stat_trade' => '�ӫ~',
	'do_stat_group' => "�Ы�{$_G[setting][navs][3][navname]}",
	'do_stat_tgroup' => "{$_G[setting][navs][3][navname]}",
	'do_stat_home' => "{$_G[setting][navs][4][navname]}",
	'do_stat_forum' => "{$_G[setting][navs][2][navname]}",
	'do_stat_groupthread' => '�s�եD�D',
	'do_stat_post' => '�D�D�^�_',
	'do_stat_grouppost' => '�s�զ^�_',
	'do_stat_pic' => '�Ϥ�',
	'do_stat_poll' => '�벼',
	'do_stat_event' => '����',
	'do_stat_share' => '����',
	'do_stat_thread' => '�D�D',
	'do_stat_docomment' => '�O���^�_',
	'do_stat_blogcomment' => '��x����',
	'do_stat_piccomment' => '�Ϥ�����',
	'do_stat_pollcomment' => '�벼����',
	'do_stat_pollvote' => '�ѻP�벼',
	'do_stat_eventcomment' => '���ʵ���',
	'do_stat_eventjoin' => '�ѥ[����',
	'do_stat_sharecomment' => '���ɵ���',
	'do_stat_post' => '�D�D�^��',
	'do_stat_click' => '��A',
	'do_stat_wall' => '�d��',
	'do_stat_poke' => '���۩I',
	'do_stat_sendpm' => '�o�u����',
	'do_stat_addfriend' => '�n�ͽШD',
	'do_stat_friend' => '�����n��',
	'do_stat_post_number' => '�o���q',
	'do_stat_statistic' => '�X�ֲέp',
	'logs_credit_update_INDEX' => array('TRC','RTC','RAC','MRC','BMC','TFR','RCV','CEC','ECU','SAC','BAC','PRC','RSC','STC','BTC','AFD','UGP','RPC','ACC','RCT','RCA','RCB','CDC','RGC','BGC','AGC','RKC','BME','RPR','RPZ','FCP','BGC'),
	'logs_credit_update_TRC' => '���ȼ��y',
	'logs_credit_update_RTC' => '�a��D�D',
	'logs_credit_update_RAC' => '�̨ε���',
	'logs_credit_update_MRC' => '�D���H�����',
	'logs_credit_update_BMC' => '�ʶR�D��',
	'logs_credit_update_TFR' => '�����X',
	'logs_credit_update_RCV' => '��㱵��',
	'logs_credit_update_CEC' => '�n���I��',
	'logs_credit_update_ECU' => 'UCenter�n���I����X',
	'logs_credit_update_SAC' => '�X�����',
	'logs_credit_update_BAC' => '�ʶR����',
	'logs_credit_update_PRC' => '���l�Q����',
	'logs_credit_update_RSC' => '���l����',
	'logs_credit_update_STC' => '�X��D�D',
	'logs_credit_update_BTC' => '�ʶR�D�D',
	'logs_credit_update_AFD' => '�ʶR�n��',
	'logs_credit_update_UGP' => '�ʶR�X�i�Τ��',
	'logs_credit_update_RPC' => '�|�����g',
	'logs_credit_update_ACC' => '�ѻP����',
	'logs_credit_update_RCT' => '�^�����y',
	'logs_credit_update_RCA' => '�^������',
	'logs_credit_update_RCB' => '���٦^�����y�n��',
	'logs_credit_update_CDC' => '�d�K�R��',
	'logs_credit_update_RGC' => '�^�����]',
	'logs_credit_update_BGC' => '�I�U���]',
	'logs_credit_update_AGC' => '��o���]',
	'logs_credit_update_RKC' => '�v���ƦW',
	'logs_credit_update_BME' => '�ʶR����',
	'logs_credit_update_RPR' => '��x�n�����g',
	'logs_credit_update_RPZ' => '��x�n�����g�M�s',
	'logs_credit_update_FCP' => '�I�O����',
	'logs_credit_update_BGR' => '�Ыظs��',
	'buildgroup' => '�d�ݤw�Ыت��s��',
	'logs_credit_update_reward_clean' => '�M�s',
	'logs_select_operation' => '�п�ܾާ@����',
	'task_credit' => '���ȼ��y�n��',
	'special_3_credit' => '�a��D�D�����n��',
	'special_3_best_answer' => '�̨ε�������a��n��',
	'magic_credit' => '�D���H������n��',
	'magic_space_gift' => '�b�ۤw�Ŷ������I�U���]',
	'magic_space_re_gift' => '�^���٨S���Χ������]',
	'magic_space_get_gift' => '�X�ݪŶ���������]',
	'credit_transfer' => '�i��n����b',
	'credit_transfer_tips' => '����㦬�J',
	'credit_exchange_tips_1' => '����n����I���ާ@,�N ',
	'credit_exchange_to' => '�I����',
	'credit_exchange_center' => '�q�LUCenter�I���n��',
	'attach_sell' => '�X��',
	'attach_sell_tips' => '��������o�n��',
	'attach_buy' => '�ʶR',
	'attach_buy_tips' => '�������X�n��',
	'grade_credit' => '�Q������o���n��',
	'grade_credit2' => '���l�����������n��',
	'thread_credit' => '�D�D��o�n��',
	'thread_credit2' => '�D�D��X�n��',
	'buy_credit' => '��n���R��',
	'buy_usergroup' => '�ʶR�X�i�Τ�դ�X�n��',
	'buy_medal' => '�ʶR����',
	'buy_forum' => '�ʶR�I�O�������X���v��',
	'report_credit' => '�|���\\�त�����g',
	'join' => '�ѻP',
	'activity_credit' => '���ʦ����n��',
	'thread_send' => '�����o��',
	'replycredit' => '���o���n��',
	'add_credit' => '���y�n��',
	'recovery' => '�^��',
	'replycredit_post' => '�^�����y',
	'replycredit_thread' => '���o�����l',
	'card_credit' => '�d�K�R����o���n��',
	'ranklist_top' => '�ѥ[�v���ƦW���O�n��',
	'admincp_op_credit' => '��x�n�����g�ާ@',
	'credit_update_reward_clean' => '�M�s',

	'profile_unchangeable' => '������ƴ���ᤣ�i�ק�',
	'profile_is_verifying' => '������ƥ��b�f�֤�',
	'profile_mypost' => '�ڴ��檺���e',
	'profile_need_verifying' => '������ƴ����ݭn�f��',
	'profile_edit' => '�ק�',
	'profile_censor' => '(�t���ӷP���J)',
	'profile_verify_modify_error' => '{verify}�w�g�{�ҳq�L�����\\�ק�',
	'profile_verify_verifying' => '�z��{verify}�H���w����A�Э@�ߵ��ݮ֬d�C',

	'district_level_1' => '-�٥�-',
	'district_level_2' => '-����-',
	'district_level_3' => '-�{��-',
	'district_level_4' => '-�m��-',
	'invite_you_to_visit' => '{user}�ܽбz�X��{bbname}',

	'portal' => '����',
	'group' => '�s��',
	'follow' => '�s��',
	'collection' => '�^��',
	'guide' => '��Ū',
	'feed' => '�ʺA',
	'blog' => '��x',
	'doing' => '�O��',
	'wall' => '�d���O',
	'homepage' => '�ӤH�D��',
	'ranklist' => '�Ʀ�]',
	'select_the_navigation_position' => '���{type}�ɯ��m',
	'close_module' => '����{type}�\\��',

	'follow_add_remark' => '�K�[�Ƶ�',
	'follow_modify_remark' => '�ק�Ƶ�',
	'follow_specified_group' => '�s���M��',
	'follow_specified_forum' => '�s���M��',

	'filesize_lessthan' => '���j�p���Ӥp��',

	'spacecp_message_prompt' => '(��� {msg} �N�X,�̤j 1000 �r)',
	'card_update_doing' => ' <a class="xi2" href="###">[��s�O��]</a>',
	'email_acitve_message' => '<img src="{imgdir}/mail_inactive.png" alt="������" class="vm" /> <span class="xi1">�s�l�c({newemail})�������Ҥ�...</span><br />
								�t�Τw�g�V�Ӷl�c�o�e�F�@�����ҿE���l��A�Ьd���l��A�i�����ҿE���C<br>
								�p�G�S���������Ҷl��A�z�i�H�󴫤@�Ӷl�c�A�Ϊ�<a href="home.php?mod=spacecp&ac=profile&op=password&resend=1" class="xi2">���s�������Ҷl��</a>',
	'qq_set_status' => '�]�m�ڪ�QQ�b�u���A',
	'qq_dialog' => '�o�_QQ���',

);

?>