<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: lang_feed.php 27449 2012-02-01 05:32:35Z zhangguosheng $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$lang = array
(

	'feed_blog_password' => '{actor} �o��F�s�[�K��x {subject}',
	'feed_blog_title' => '{actor} �o��F�s��x',
	'feed_blog_body' => '<b>{subject}</b><br />{summary}',
	'feed_album_title' => '{actor} ��s�F�ۥU',
	'feed_album_body' => '<b>{album}</b><br />�@ {picnum} �i�Ϥ�',
	'feed_pic_title' => '{actor} �W�ǤF�s�Ϥ�',
	'feed_pic_body' => '{title}',



	'feed_poll' => '{actor} �o�_�F�s�벼',

	'feed_comment_space' => '{actor} �b {touser} ���d���O�d�F��',
	'feed_comment_image' => '{actor} ���פF {touser} ���Ϥ�',
	'feed_comment_blog' => '{actor} ���פF {touser} ����x {blog}',
	'feed_comment_poll' => '{actor} ���פF {touser} ���벼 {poll}',
	'feed_comment_event' => '{actor} �b {touser} ��´������ {event} ���d���F',
	'feed_comment_share' => '{actor} �� {touser} ���ɪ� {share} �o��F����',

	'feed_showcredit' => '{actor} �ذe�� {fusername} �v���n�� {credit} �ӡA���U�n�ʹ��ɦb<a href="misc.php?mod=ranklist&type=member" target="_blank">�v���Ʀ�]</a>�����W��',
	'feed_showcredit_self' => '{actor} �W�[�v���n�� {credit} �ӡA���ɦۤv�b<a href="misc.php?mod=ranklist&type=member" target="_blank">�v���Ʀ�]</a>�����W��',
	'feed_doing_title' => '{actor}�G{message}',
	'feed_friend_title' => '{actor} �M {touser} �����F�n��',



	'feed_click_blog' => '{actor} �e�F�@�ӡu{click}�v�� {touser} ����x {subject}',
	'feed_click_thread' => '{actor} �e�F�@�ӡu{click}�v�� {touser} �����D {subject}',
	'feed_click_pic' => '{actor} �e�F�@�ӡu{click}�v�� {touser} ���Ϥ�',
	'feed_click_article' => '{actor} �e�F�@�ӡu{click}�v�� {touser} ���峹 {subject}',


	'feed_task' => '{actor} �����F�������� {task}',
	'feed_task_credit' => '{actor} �����F�������� {task}�A����F {credit} �Ӽ��y�n��',

	'feed_profile_update_base' => '{actor} ��s�F�ۤv���򥻸��',
	'feed_profile_update_contact' => '{actor} ��s�F�ۤv���pô�覡',
	'feed_profile_update_edu' => '{actor} ��s�F�ۤv���Ш|���p',
	'feed_profile_update_work' => '{actor} ��s�F�ۤv���u�@�H��',
	'feed_profile_update_info' => '{actor} ��s�F�ۤv���ӤH�H��',
	'feed_profile_update_bbs' => '{actor} ��s�F�ۤv���׾«H��',
	'feed_profile_update_verify' => '{actor} ��s�F�ۤv���{�ҫH��',

	'feed_add_attachsize' => '{actor} �� {credit} �ӿn���I���F {size} ����Ŷ��A�i�H�W�ǧ�h���Ϥ���(<a href="home.php?mod=spacecp&ac=credit&op=addsize">�ڤ]�ӧI��</a>)',

	'feed_invite' => '{actor} �o�_�ܽСA�M {username} �����F�n��',

	'magicuse_thunder_announce_title' => '<strong>{username} �o�X�F�u�p�蠟�n�v</strong>',
	'magicuse_thunder_announce_body' => '�j�a�n�A�ڤW�u��<br /><a href="home.php?mod=space&uid={uid}" target="_blank">�w��ӧڮa��Ӫ�</a>',


	'feed_thread_title' =>			'{actor} �o��F�s���D',
	'feed_thread_message' =>		'<b>{subject}</b><br />{message}',

	'feed_reply_title' =>			'{actor} �^�_�F {author} �����D {subject}',
	'feed_reply_title_anonymous' =>		'{actor} �^�_�F���D {subject}',
	'feed_reply_message' =>			'',

	'feed_thread_poll_title' =>		'{actor} �o�_�F�s�벼',
	'feed_thread_poll_message' =>		'<b>{subject}</b><br />{message}',

	'feed_thread_votepoll_title' =>		'{actor} �ѻP�F���� {subject} ���벼',
	'feed_thread_votepoll_message' =>	'',

	'feed_thread_goods_title' =>		'{actor} �X��F�@�ӷs�ӫ~',
	'feed_thread_goods_message_1' =>	'<b>{itemname}</b><br />��� {itemprice} �� ���[ {itemcredit}{creditunit}',
	'feed_thread_goods_message_2' =>	'<b>{itemname}</b><br />��� {itemprice} ��',
	'feed_thread_goods_message_3' =>	'<b>{itemname}</b><br />��� {itemcredit}{creditunit}',

	'feed_thread_reward_title' =>		'{actor} �o�_�F�s�a��',
	'feed_thread_reward_message' =>		'<b>{subject}</b><br />�a�� {rewardprice}{extcredits}',

	'feed_reply_reward_title' =>		'{actor} �^�_�F���� {subject} ���a��',
	'feed_reply_reward_message' =>		'',

	'feed_thread_activity_title' =>		'{actor} �o�_�F�s����',
	'feed_thread_activity_message' =>	'<b>{subject}</b><br />�}�l�ɶ��G{starttimefrom}<br />���ʦa�I�G{activityplace}<br />{message}',

	'feed_reply_activity_title' =>		'{actor} ���W�ѥ[�F {subject} ������',
	'feed_reply_activity_message' =>	'',

	'feed_thread_debate_title' =>		'{actor} �o�_�F�s�G��',
	'feed_thread_debate_message' =>		'<b>{subject}</b><br />����G{affirmpoint}<br />�Ϥ�G{negapoint}<br />{message}',

	'feed_thread_debatevote_title_1' =>	'{actor} �H���設���ѻP�F���� {subject} ���G��',
	'feed_thread_debatevote_title_2' =>	'{actor} �H�Ϥ設���ѻP�F���� {subject} ���G��',
	'feed_thread_debatevote_title_3' =>	'{actor} �H���ߨ����ѻP�F���� {subject} ���G��',
	'feed_thread_debatevote_message_1' =>	'',
	'feed_thread_debatevote_message_2' =>	'',
	'feed_thread_debatevote_message_3' =>	'',

);

?>