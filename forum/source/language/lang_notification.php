<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: lang_notification.php 32028 2012-10-31 10:12:22Z zhangjie $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$lang = array
(

	'type_wall' => '�d��',
	'type_piccomment' => '�Ϥ�����',
	'type_blogcomment' => '��x����',
	'type_clickblog' => '��x��A',
	'type_clickarticle' => '�峹��A',
	'type_clickpic' => '�Ϥ���A',
	'type_sharecomment' => '���ɵ���',
	'type_doing' => '�O��',
	'type_friend' => '�n��',
	'type_credit' => '�n��',
	'type_bbs' => '�׾�',
	'type_system' => '�t��',
	'type_thread' => '�D�D',
	'type_task' => '����',
	'type_group' => '�s��',

	'mail_to_user' => '���s���q��',
	'showcredit' => '{actor} �ذe���z {credit} ���v���n���A���U���ɱz�b <a href="misc.php?mod=ranklist&type=member" target="_blank">�v���Ʀ�]</a> �����W��',
	'share_space' => '{actor} ���ɤF�z���Ŷ�',
	'share_blog' => '{actor} ���ɤF�z����x <a href="{url}" target="_blank">{subject}</a>',
	'share_album' => '{actor} ���ɤF�z���ۥU <a href="{url}" target="_blank">{albumname}</a>',
	'share_pic' => '{actor} ���ɤF�z���ۥU {albumname} ���� <a href="{url}" target="_blank"> �Ϥ�</a>',
	'share_thread' => '{actor} ���ɤF�z�����l <a href="{url}" target="_blank">{subject}</a>',
	'share_article' => '{actor} ���ɤF�z���峹 <a href="{url}" target="_blank">{subject}</a>',
	'magic_present_note' => '�e���z�@�ӹD�� <a href="{url}" target="_blank">{name}</a>',
	'friend_add' => '{actor} �M�z�����F�n��',
	'friend_request' => '{actor} �ШD�[�z���n��{note}&nbsp;&nbsp;<a onclick="showWindow(this.id, this.href, \'get\', 0);" class="xw1" id="afr_{uid}" href="{url}">���ӽ�</a>',
	'doing_reply' => '{actor} �^�_�F�z�� <a href="{url}" target="_blank">�O��</a>',
	'wall_reply' => '{actor} �^�_�F�z�� <a href="{url}" target="_blank">�d��</a>',
	'pic_comment_reply' => '{actor} �^�_�F�z�� <a href="{url}" target="_blank">�Ϥ�����</a>',
	'blog_comment_reply' => '{actor} �^�_�F�z�� <a href="{url}" target="_blank">��x����</a>',
	'share_comment_reply' => '{actor} �^�_�F�z�� <a href="{url}" target="_blank">���ɵ���</a>',
	'wall' => '{actor} �b�d���O�W���z <a href="{url}" target="_blank">�d��</a>',
	'pic_comment' => '{actor} ���פF�z�� <a href="{url}" target="_blank">�Ϥ�</a>',
	'blog_comment' => '{actor} ���פF�z����x <a href="{url}" target="_blank">{subject}</a>',
	'share_comment' => '{actor} ���פF�z�� <a href="{url}" target="_blank">����</a>',
	'click_blog' => '{actor} ��z����x <a href="{url}" target="_blank">{subject}</a> ���F��A',
	'click_pic' => '{actor} ��z�� <a href="{url}" target="_blank">�Ϥ�</a> ���F��A',
	'click_article' => '{actor} ��z���峹 <a href="{url}" target="_blank">{subject}</a> ���F��A',
	'show_out' => '{actor} �X�ݤF�z���D����A�z�b�v���ƦW�]�����̫�@�ӿn���]�Q���O���F',
	'puse_article' => '���߱z�A�z��<a href="{url}" target="_blank">{subject}</a>�w�Q�K�[��峹�C��A <a href="{newurl}" target="_blank">�I���d��</a>',

	'myinvite_request' => '���s�����ή���<a href="home.php?mod=space&do=notice&view=userapp">�I���i�J���ή��������i������ާ@</a>',


	'group_member_join' => '{actor} �[�J�z�� <a href="forum.php?mod=group&fid={fid}" target="_blank">{groupname}</a> �s�ջݭn�f�֡A�Ш�s��<a href="{url}" target="_blank">�޲z��x</a> �i��f��',
	'group_member_invite' => '{actor} �ܽбz�[�J <a href="forum.php?mod=group&fid={fid}" target="_blank">{groupname}</a> �s�աA<a href="{url}" target="_blank">�I�����W�[�J</a>',
	'group_member_check' => '�z�w�q�L�F <a href="{url}" target="_blank">{groupname}</a> �s�ժ��f�֡A�� <a href="{url}" target="_blank">�I���o�̳X��</a>',
	'group_member_check_failed' => '�z�S���q�L <a href="{url}" target="_blank">{groupname}</a> �s�ժ��f�֡C',
	'group_mod_check' => '�z���Ыت��s�� <a href="{url}" target="_blank">{groupname}</a> �f�ֳq�L�F�A�� <a href="{url}" target="_blank">�I���o�̳X��</a>',

	'reason_moderate' => '�z���D�D <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> �Q {actor} {modaction} <div class="quote"><blockquote>{reason}</blockquote></div>',

	'reason_merge' => '�z���D�D <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> �Q {actor} {modaction} <div class="quote"><blockquote>{reason}</blockquote></div>',

	'reason_delete_post' => '�z�b <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> �����l�Q {actor} �R�� <div class="quote"><blockquote>{reason}</blockquote></div>',

	'reason_delete_comment' => '�z�b <a href="forum.php?mod=redirect&goto=findpost&pid={pid}&ptid={tid}" target="_blank">{subject}</a> ���I���Q {actor} �R�� <div class="quote"><blockquote>{reason}</blockquote></div>',

	'reason_ban_post' => '�z���D�D <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> �Q {actor} {modaction} <div class="quote"><blockquote>{reason}</blockquote></div>',

	'reason_warn_post' => '�z���D�D <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> �Q {actor} {modaction}<br />
�s�� {warningexpiration} �Ѥ��֭p {warninglimit} ��ĵ�i�A�z�N�Q�۰ʸT��o�� {warningexpiration} �ѡC<br />
�I��ܥثe�A�z�w�Qĵ�i {authorwarnings} ���A�Ъ`�N�I<div class="quote"><blockquote>{reason}</blockquote></div>',

	'reason_move' => '�z���D�D <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> �Q {actor} ���ʨ� <a href="forum.php?mod=forumdisplay&fid={tofid}" target="_blank">{toname}</a> <div class="quote"><blockquote>{reason}</blockquote></div>',

	'reason_copy' => '�z���D�D <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> �Q {actor} �ƻs�� <a href="forum.php?mod=viewthread&tid={threadid}" target="_blank">{subject}</a> <div class="quote"><blockquote>{reason}</blockquote></div>',

	'reason_remove_reward' => '�z���a��D�D <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> �Q {actor} �M�P <div class="quote"><blockquote>{reason}</blockquote></div>',

	'reason_stamp_update' => '�z���D�D <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> �Q {actor} �K�[�F�ϳ� {stamp} <div class="quote"><blockquote>{reason}</blockquote></div>',

	'reason_stamp_delete' => '�z���D�D <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> �Q {actor} �M�P�F�ϳ� <div class="quote"><blockquote>{reason}</blockquote></div>',

	'reason_stamplist_update' => '�z���D�D <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> �Q {actor} �K�[�F�ϼ� {stamp} <div class="quote"><blockquote>{reason}</blockquote></div>',

	'reason_stamplist_delete' => '�z���D�D <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> �Q {actor} �M�P�F�ϼ� <div class="quote"><blockquote>{reason}</blockquote></div>',

	'reason_stickreply' => '�z�b�D�D <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> ���^���Q {actor} �m�� <div class="quote"><blockquote>{reason}</blockquote></div>',

	'reason_stickdeletereply' => '�z�b�D�D <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> ���^���Q {actor} �M�P�m�� <div class="quote"><blockquote>{reason}</blockquote></div>',

	'reason_quickclear' => '�z��{cleartype} �Q {actor} �M�� <div class="quote"><blockquote>{reason}</blockquote></div>',

	'reason_live_update' => '�z���D�D <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> �Q {actor} �]�m�������K <div class="quote"><blockquote>{reason}</blockquote></div>',
	'reason_live_cancle' => '�z���D�D <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> �Q {actor} �������� <div class="quote"><blockquote>{reason}</blockquote></div>',

	'modthreads_delete' => '�z�o���D�D {threadsubject} ���q�L�f�֡A�{�w�Q�R���I',

	'modthreads_delete_reason' => '�z�o���D�D {threadsubject} ���q�L�f�֡A�{�w�Q�R���I<div class="quote"><blockquote>{reason}</blockquote></div>',
	'modthreads_validate' => '�z�o���D�D <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{threadsubject}</a> �w�f�ֳq�L�I &nbsp; <a href="forum.php?mod=viewthread&tid={tid}" target="_blank" class="lit">�d�� &rsaquo;</a>',

	'modreplies_delete' => '�z�o��^�_���q�L�f�֡A�{�w�Q�R���I <p class="summary">�^�_���e�G<span>{post}</span></p>',

	'modreplies_validate' => '�z�o���^�_�w�f�ֳq�L�I &nbsp; <a href="forum.php?mod=redirect&goto=findpost&pid={pid}&ptid={tid}" target="_blank" class="lit">�d�� &rsaquo;</a> <p class="summary">�^�_���e�G<span>{post}</span></p>',

	'transfer' => '�z����@���Ӧ� {actor} ���n����� {credit} &nbsp; <a href="home.php?mod=spacecp&ac=credit&op=log&suboperation=creditslog" target="_blank" class="lit">�d�� &rsaquo;</a>
<p class="summary">{actor} ���G<span>{transfermessage}</span></p>',

	'addfunds' => '�z���檺�n���R�ȽШD�w�����A�������B���n���w�s�J�z���n����� &nbsp; <a href="home.php?mod=spacecp&ac=credit&op=base" target="_blank" class="lit">�d�� &rsaquo;</a>
<p class="summary">�q�渹�G<span>{orderid}</span></p><p class="summary">��X�G<span>�H���� {price} ��</span></p><p class="summary">���J�G<span>{value}</span></p>',

	'rate_reason' => '�z�b�D�D <a href="forum.php?mod=redirect&goto=findpost&pid={pid}&ptid={tid}" target="_blank">{subject}</a> �����l�Q {actor} ���� {ratescore} <div class="quote"><blockquote>{reason}</blockquote></div>',

	'recommend_note_post' => '���ߡA�z�����l <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> �Q�s��ĥ�',

	'rate_removereason' => '�z�b�D�D <a href="forum.php?mod=redirect&goto=findpost&pid={pid}&ptid={tid}" target="_blank">{subject}</a> �����l������ {ratescore} <div class="quote"><blockquote>{reason}</blockquote></div> �Q {actor} �M�P',

	'trade_seller_send' => '<a href="home.php?mod=space&uid={buyerid}" target="_blank">{buyer}</a> �ʶR�z���ӫ~ <a href="forum.php?mod=trade&orderid={orderid}" target="_blank">{subject}</a>�A���w�I�ڡA���ݱz�o�f &nbsp; <a href="forum.php?mod=trade&orderid={orderid}" target="_blank" class="lit">�d�� &rsaquo;</a>',

	'trade_buyer_confirm' => '�z�ʶR���ӫ~ <a href="forum.php?mod=trade&orderid={orderid}" target="_blank">{subject}</a>�A<a href="home.php?mod=space&uid={sellerid}" target="_blank">{seller}</a> �w�o�f�A���ݱz�T�{ &nbsp; <a href="forum.php?mod=trade&orderid={orderid}" target="_blank" class="lit">�d�� &rsaquo;</a>',

	'trade_fefund_success' => '�ӫ~ <a href="forum.php?mod=trade&orderid={orderid}" target="_blank">{subject}</a> �w�h�ڦ��\\ &nbsp; <a href="forum.php?mod=trade&orderid={orderid}" target="_blank" class="lit">���� &rsaquo;</a>',

	'trade_success' => '�ӫ~ <a href="forum.php?mod=trade&orderid={orderid}" target="_blank">{subject}</a> �w������\\ &nbsp; <a href="forum.php?mod=trade&orderid={orderid}" target="_blank" class="lit">���� &rsaquo;</a>',

	'trade_order_update_sellerid' => '��a <a href="home.php?mod=space&uid={sellerid}" target="_blank">{seller}</a> �ק�F�ӫ~ <a href="forum.php?mod=trade&orderid={orderid}" target="_blank">{subject}</a> �������A�нT�{ &nbsp; <a href="forum.php?mod=trade&orderid={orderid}" target="_blank" class="lit">�d�� &rsaquo;</a>',

	'trade_order_update_buyerid' => '�R�a <a href="home.php?mod=space&uid={buyerid}" target="_blank">{buyer}</a> �ק�F�ӫ~ <a href="forum.php?mod=trade&orderid={orderid}" target="_blank">{subject}</a> �������A�нT�{ &nbsp; <a href="forum.php?mod=trade&orderid={orderid}" target="_blank" class="lit">�d�� &rsaquo;</a>',

	'eccredit' => '�P�z����� {actor} �w��z�@�F���� &nbsp; <a href="forum.php?mod=trade&orderid={orderid}" target="_blank" class="lit">�^�� &rsaquo;</a>',

	'activity_notice' => '{actor} �ӽХ[�J�z�|�쪺���� <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a>�A�мf�� &nbsp; <a href="forum.php?mod=viewthread&tid={tid}" target="_blank" class="lit">�d�� &rsaquo;</a>',

	'activity_apply' => '���� <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> ���o�_�H {actor} �w���z�ѥ[������ &nbsp; <a href="forum.php?mod=viewthread&tid={tid}" target="_blank" class="lit">�d�� &rsaquo;</a> <div class="quote"><blockquote>{reason}</blockquote></div>',

	'activity_replenish' => '���� <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> ���o�_�H {actor} �q���z�ݭn�������ʳ��W�H�� &nbsp; <a href="forum.php?mod=viewthread&tid={tid}" target="_blank" class="lit">�d�� &rsaquo;</a> <div class="quote"><blockquote>{reason}</blockquote></div>',

	'activity_delete' => '���� <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> ���o�_�H {actor} �ڵ��z�ѥ[������ &nbsp; <a href="forum.php?mod=viewthread&tid={tid}"  target="_blank" class="lit">�d�� &rsaquo;</a> <div class="quote"><blockquote>{reason}</blockquote></div>',

	'activity_cancel' => '{actor} �����F�ѥ[ <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> ���� &nbsp; <a href="forum.php?mod=viewthread&tid={tid}"  target="_blank" class="lit">�d�� &rsaquo;</a> <div class="quote"><blockquote>{reason}</blockquote></div>',

	'activity_notification' => '���� <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> ���o�_�H {actor} �o�ӳq��&nbsp; <a href="forum.php?mod=viewthread&tid={tid}" target="_blank" class="lit">�d�ݬ��� &rsaquo;</a> <div class="quote"><blockquote>{msg}</blockquote></div>',

	'reward_question' => '�z���a��D�D <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> �Q {actor} �]�m�F�̨ε��� &nbsp; <a href="forum.php?mod=viewthread&tid={tid}" target="_blank" class="lit">�d�� &rsaquo;</a>',

	'reward_bestanswer' => '�z���^�_�Q�a��D�D <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> ���@�� {actor} �אּ�̨ε��� &nbsp; <a href="forum.php?mod=viewthread&tid={tid}" target="_blank" class="lit">�d�� &rsaquo;</a>',

	'reward_bestanswer_moderator' => '�z�b�a��D�D <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> ���^�_�Q�אּ�̨ε��� &nbsp; <a href="forum.php?mod=viewthread&tid={tid}" target="_blank" class="lit">�d�� &rsaquo;</a>',

	'comment_add' => '{actor} �I���F�z���g�b�D�D <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> �o�����l &nbsp; <a href="forum.php?mod=redirect&goto=findpost&pid={pid}&ptid={tid}" target="_blank" class="lit">�d�� &rsaquo;</a>',

	'reppost_noticeauthor' => '{actor} �^�_�F�z�����l <a href="forum.php?mod=redirect&goto=findpost&ptid={tid}&pid={pid}" target="_blank">{subject}</a> &nbsp; <a href="forum.php?mod=redirect&goto=findpost&pid={pid}&ptid={tid}" target="_blank" class="lit">�d��</a>',

	'task_reward_credit' => '���߱z�������ȡG<a href="home.php?mod=task&do=view&id={taskid}" target="_blank">{name}</a>�A��o�n�� {creditbonus} &nbsp; <a href="home.php?mod=spacecp&ac=credit&op=base" target="_blank" class="lit">�d�ݧڪ��n�� &rsaquo;</a></p>',

	'task_reward_magic' => '���߱z�������ȡG<a href="home.php?mod=task&do=view&id={taskid}" target="_blank">{name}</a>�A��o�D�� <a href="home.php?mod=magic&action=mybox" target="_blank">{rewardtext}</a> {bonus} �i',

	'task_reward_medal' => '���߱z�������ȡG<a href="home.php?mod=task&do=view&id={taskid}" target="_blank">{name}</a>�A��o���� <a href="home.php?mod=medal" target="_blank">{rewardtext}</a> ���Ĵ� {bonus} ��',

	'task_reward_medal_forever' => '���߱z�������ȡG<a href="home.php?mod=task&do=view&id={taskid}" target="_blank">{name}</a>�A��o���� <a href="home.php?mod=medal" target="_blank">{rewardtext}</a> �ä[����',

	'task_reward_invite' => '���߱z�������ȡG<a href="home.php?mod=task&do=view&id={taskid}" target="_blank">{name}</a>�A��o<a href="home.php?mod=spacecp&ac=invite" target="_blank">�ܽнX {rewardtext}��</a> ���Ĵ� {bonus} ��',

	'task_reward_group' => '���߱z�������ȡG<a href="home.php?mod=task&do=view&id={taskid}" target="_blank">{name}</a>�A��o�Τ�� {rewardtext} ���Ĵ� {bonus} �� &nbsp; <a href="home.php?mod=spacecp&ac=usergroup" target="_blank" class="lit">�ݬݧگవ���� &rsaquo;</a>',

	'user_usergroup' => '�z���Τ�դɯŬ� {usergroup} &nbsp; <a href="home.php?mod=spacecp&ac=usergroup" target="_blank" class="lit">�ݬݧگవ���� &rsaquo;</a>',

	'grouplevel_update' => '���߱z�A�z���s�� {groupname} �w�ɯŨ� {newlevel}�C',

	'thread_invite' => '{actor} �ܽбz{invitename} <a href="forum.php?mod=viewthread&tid={tid}" target="_blank">{subject}</a> &nbsp; <a href="forum.php?mod=viewthread&tid={tid}" target="_blank" class="lit">�d�� &rsaquo;</a>',
	'blog_invite' => '{actor} �ܽбz�d�ݤ�x <a href="home.php?mod=space&uid={uid}&do=blog&id={blogid}" target="_blank">{subject}</a> &nbsp; <a href="home.php?mod=space&uid={uid}&do=blog&id={blogid}" target="_blank" class="lit">�d�� &rsaquo;</a>',
	'article_invite' => '{actor} �ܽбz�d�ݤ峹 <a href="{url}" target="_blank">{subject}</a> &nbsp; <a href="{url}" target="_blank" class="lit">�d�� &rsaquo;</a>',
	'invite_friend' => '���߱z���\\�ܽШ� {actor} �æ����z���n��',

	'poke_request' => '<a href="{fromurl}" class="xi2">{fromusername}</a>: <span class="xw0">{pokemsg}&nbsp;</span><a href="home.php?mod=spacecp&ac=poke&op=reply&uid={fromuid}&from=notice" id="a_p_r_{fromuid}" class="xw1" onclick="showWindow(this.id, this.href, \'get\', 0);">�^���۩I</a><span class="pipe">|</span><a href="home.php?mod=spacecp&ac=poke&op=ignore&uid={fromuid}&from=notice" id="a_p_i_{fromuid}" onclick="showWindow(\'pokeignore\', this.href, \'get\', 0);">����</a>',

	'profile_verify_error' => '{verify}��Ƽf�ֳQ�ڵ�,�H�U�r�q�ݭn���s��g:<br/>{profile}<br/>�ڵ��z��:{reason}',
	'profile_verify_pass' => '���߱z�A�z��g��{verify}��Ƽf�֤w�q�L',
	'profile_verify_pass_refusal' => '�ܿ�ѡA�z��g��{verify}��Ƽf�֤w�Q�ڵ�',
	'member_ban_speak' => '�z�w�Q {user} �T��o���A�����G{day}��(0�G�N��ä[�T��)�A�T���z�ѡG{reason}',
	'member_ban_visit' => '�z�w�Q {user} �T��X�ݡA�����G{day}��(0�G�N��ä[�T��X��)�A�T��X�ݲz�ѡG{reason}',
	'member_ban_status' => '�z�w�Q {user} ��w�A�T��X�ݲz�ѡG{reason}',
	'member_follow' => '�z���`���H�w��{count}���s�ʺA�C<a href="home.php?mod=follow">�I���d��</a>',
	'member_follow_add' => '{actor} ��ť�F�z�C<a href="home.php?mod=follow&do=follower">�I���d��</a>',

	'member_moderate_invalidate' => '�z���㸹����q�L�޲z�����f�֡A��<a href="home.php?mod=spacecp&ac=profile">���s������U�H��</a>�C<br />�޲z���d��: <b>{remark}</b>',
	'member_moderate_validate' => '�z���㸹�w�q�L�f�֡C<br />�޲z���d��: <b>{remark}</b>',
	'member_moderate_invalidate_no_remark' => '�z���㸹����q�L�޲z�����f�֡A��<a href="home.php?mod=spacecp&ac=profile">���s������U�H��</a>�C',
	'member_moderate_validate_no_remark' => '�z���㸹�w�q�L�f�֡C',
	'manage_verifythread' => '���s���ݼf�֥D�D�C<a href="admin.php?action=moderate&operation=threads&dateline=all">�{�b�i��f��</a>',
	'manage_verifypost' => '���s���ݼf�֦^���C<a href="admin.php?action=moderate&operation=replies&dateline=all">�{�b�i��f��</a>',
	'manage_verifyuser' => '���s���ݼf�ַ|���C<a href="admin.php?action=moderate&operation=members">�{�b�i��f��</a>',
	'manage_verifyblog' => '���s���ݼf�֤�x�C<a href="admin.php?action=moderate&operation=blogs">�{�b�i��f��</a>',
	'manage_verifydoing' => '���s���ݼf�ְO���C<a href="admin.php?action=moderate&operation=doings">�{�b�i��f��</a>',
	'manage_verifypic' => '���s���ݼf�ֹϤ��C<a href="admin.php?action=moderate&operation=pictures">�{�b�i��f��</a>',
	'manage_verifyshare' => '���s���ݼf�֤��ɡC<a href="admin.php?action=moderate&operation=shares">�{�b�i��f��</a>',
	'manage_verifycommontes' => '���s���ݼf�֯d��/���סC<a href="admin.php?action=moderate&operation=comments">�{�b�i��f��</a>',
	'manage_verifyrecycle' => '�^�������s���ݳB�z�D�D�C<a href="admin.php?action=recyclebin">�{�b�B�z</a>',
	'manage_verifyrecyclepost' => '�^���^�������s���ݳB�z�^���C<a href="admin.php?action=recyclebinpost">�{�b�B�z</a>',
	'manage_verifyarticle' => '���s���ݼf�֤峹�C<a href="admin.php?action=moderate&operation=articles">�{�b�i��f��</a>',
	'manage_verifymedal' => '���s���ݼf�־����ӽСC<a href="admin.php?action=medals&operation=mod">�{�b�i��f��</a>',
	'manage_verifyacommont' => '���s���ݼf�֤峹���סC<a href="admin.php?action=moderate&operation=articlecomments">�{�b�i��f��</a>',
	'manage_verifytopiccommont' => '���s���ݼf�ֱM�D���סC<a href="admin.php?action=moderate&operation=topiccomments">�{�b�i��f��</a>',
	'manage_verify_field' => '���s���ݳB�z{verifyname}�C<a href="admin.php?action=verify&operation=verify&do={doid}">�{�b�B�z</a>',
	'system_notice' => '{subject}<p class="summary">{message}</p>',
	'system_adv_expiration' => '�z���I���H�U�s�i�N�� {day} �ѫ����A�ФήɳB�z�G<br />{advs}',
	'report_change_credits' => '{actor} �B�z�F�z���|�� {creditchange} {msg}',
	'at_message' => '<a href="home.php?mod=space&uid={buyerid}" target="_blank">{buyer}</a> �b�D�D <a href="forum.php?mod=redirect&goto=findpost&ptid={tid}&pid={pid}" target="_blank">{subject}</a> ������F�z<div class="quote"><blockquote>{message}</blockquote></div><a href="forum.php?mod=redirect&goto=findpost&ptid={tid}&pid={pid}" target="_blank">�{�b�h�ݬ�</a>�C',
	'new_report' => '���s���|�����ݳB�z�A<a href="admin.php?action=report" target="_blank">�I���i�J��x�B�z</a>�C',
	'new_post_report' => '���s���|�����ݳB�z�A<a href="forum.php?mod=modcp&action=report&fid={fid}" target="_blank">�I���i�J�޲z����</a>�C',
	'magics_receive' => '�z���� {actor} �e���z���D�� {magicname}
<p class="summary">{actor} ���G<span>{msg}</span></p>
<p class="mbn"><a href="home.php?mod=magic" target="_blank">�^�عD��</a><span class="pipe">|</span><a href="home.php?mod=magic&action=mybox" target="_blank">�d�ݧڪ��D��c</a></p>',
	'invite_collection' => '{actor} �ܽбz�ѻP���@�^�M��  <a href="forum.php?mod=collection&action=view&ctid={ctid}">{collectionname}</a>�C<br /> <a href="forum.php?mod=collection&action=edit&op=acceptinvite&ctid={ctid}&dateline={dateline}">�����ܽ�</a>',
	'collection_removed' => '�z�ѻP���@���^�M��  <a href="forum.php?mod=collection&action=view&ctid={ctid}">{collectionname}</a> �w�Q {actor} �����C',
	'exit_collection' => '�z�w�g�h�X���@�^�M��  <a href="forum.php?mod=collection&action=view&ctid={ctid}">{collectionname}</a>�C',
	'collection_becommented' => '�z���^�M��  <a href="forum.php?mod=collection&action=view&ctid={ctid}">{collectionname}</a> ����F�s���סC',
	'collection_befollowed' => '�z���^�M��  <a href="forum.php?mod=collection&action=view&ctid={ctid}">{collectionname}</a> ���s�Τ�q�\\�F�I',
	'collection_becollected' => '���߱z���D�D <a href="forum.php?mod=viewthread&tid={tid}">{threadname}</a> �Q�^�M��  <a href="forum.php?mod=collection&action=view&ctid={ctid}">{collectionname}</a> �����F�I',

	'pmreportcontent' => '{pmreportcontent}',

);

?>