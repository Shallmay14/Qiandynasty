<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: lang_email.php 27449 2012-02-01 05:32:35Z zhangguosheng $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}


$lang = array
(
	'hello' => '�z�n',
	'moderate_member_invalidate' => '�_�M',
	'moderate_member_delete' => '�R��',
	'moderate_member_validate' => '�q�L',


	'get_passwd_subject' =>		'���^�K�X����',
	'get_passwd_message' =>		'
<p>{username}�A
�o�ʫH�O�� {bbname} �o�e���C</p>

<p>�z����o�ʶl��A�O�ѩ�o�Ӷl�c�a�}�b {bbname} �Q�n�O���Τ�l�c�A
�B�ӥΤ�ШD�ϥ� Email �K�X���m�\\��ҭP�C</p>
<p>
----------------------------------------------------------------------<br />
<strong>���n�I</strong><br />
----------------------------------------------------------------------</p>

<p>�p�G�z�S������K�X���m���ШD�Τ��O {bbname} �����U�Τ�A�ХߧY����
�çR���o�ʶl��C�u���b�z�T�{�ݭn���m�K�X�����p�U�A�~�ݭn�~��\\Ū�U����
���e�C</p>
<p>
----------------------------------------------------------------------<br />
<strong>�K�X���m����</strong><br />
----------------------------------------------------------------------</p>
</p>
�z�u�ݦb����ШD�᪺�T�Ѥ��A�q�L�I���U�����챵���m�z���K�X�G<br />

<a href="{siteurl}member.php?mod=getpasswd&amp;uid={uid}&amp;id={idstring}" target="_blank">{siteurl}member.php?mod=getpasswd&amp;uid={uid}&amp;id={idstring}</a>
<br />
(�p�G�W�����O�챵�Φ��A�бN�Ӧa�}��u�߶K���s�����a�}��A�X��)</p>

<p>�b�W�����챵�ҥ��}����������J�s���K�X�ᴣ��A�z�Y�i�ϥηs���K�X�n�������F�C�z�i�H�b�Τᱱ��O���H�ɭק�z���K�X�C</p>

<p>���ШD����̪� IP �� {clientip}</p>


<p>
���P<br />
</p>
<p>{bbname} �޲z�ζ�.
{siteurl}</p>',


	'email_verify_subject' =>	'Email �a�}����',
	'email_verify_message' =>	'<br />
<p>{username}�A<br />
�o�ʫH�O�� {bbname} �o�e���C</p>

<p>�z����o�ʶl��A�O�ѩ�b {bbname} �i��F�s�Τ���U�A�ΥΤ�ק� Email �ϥ�
�F�o�Ӷl�c�a�}�C�p�G�z�èS���X�ݹL {bbname}�A�ΨS���i��W�z�ާ@�A�Щ�
���o�ʶl��C�z���ݭn�h�q�ζi���L�i�@�B���ާ@�C</p>
<br />
----------------------------------------------------------------------<br />
<strong>�b���E������</strong><br />
----------------------------------------------------------------------<br />
<br />
<p>�p�G�z�O {bbname} ���s�Τ�A�Φb�ק�z�����U Email �ɨϥΤF���a�}�A�ڭ̻�
�n��z���a�}���ĩʶi�����ҥH�קK�U���l��Φa�}�Q�ݥΡC</p>

<p>�z�u���I���U�����챵�Y�i�E���z���b���G<br />

<a href="{url}" target="_blank">{url}</a>
<br />
(�p�G�W�����O�챵�Φ��A�бN�Ӧa�}��u�߶K���s�����a�}��A�X��)</p>

<p>�P�±z���X�ݡA���z�ϥδr�֡I</p>


<p>
���P<br />

{bbname} �޲z�ζ�.<br />
{siteurl}</p>',

	'email_register_subject' =>	'�׾µ��U�a�}',
	'email_register_message' =>	'<br />
<p>�o�ʫH�O�� {bbname} �o�e���C</p>

<p>�z����o�ʶl��A�O�ѩ�b {bbname} ����F�s�Τ���U�a�}�ϥ�
�F�o�Ӷl�c�a�}�C�p�G�z�èS���X�ݹL {bbname}�A�ΨS���i��W�z�ާ@�A�Щ�
���o�ʶl��C�z���ݭn�h�q�ζi���L�i�@�B���ާ@�C</p>
<br />
----------------------------------------------------------------------<br />
<strong>�s�Τ���U����</strong><br />
----------------------------------------------------------------------<br />
<br />
<p>�p�G�z�O {bbname} ���s�Τ�A�Φb�ק�z�����U Email �ɨϥΤF���a�}�A�ڭ̻�
�n��z���a�}���ĩʶi�����ҥH�קK�U���l��Φa�}�Q�ݥΡC</p>

<p>�z�u���I���U�����챵�Y�i�i��Τ���U�A�H�U�챵���Ĵ���3�ѡC�L���i�H���s�ШD�o�e�@�ʷs���l�����ҡG<br />

<a href="{url}" target="_blank">{url}</a>
<br />
(�p�G�W�����O�챵�Φ��A�бN�Ӧa�}��u�߶K���s�����a�}��A�X��)</p>

<p>�P�±z���X�ݡA���z�ϥδr�֡I</p>


<p>
���P<br />

{bbname} �޲z�ζ�.<br />
{siteurl}</p>',


	'add_member_subject' =>		'�z�Q�K�[�����|��',
	'add_member_message' => 	'
{newusername} �A
�o�ʫH�O�� {bbname} �o�e���C<br />
<br />
�ڬO {adminusername} �A{bbname} ���޲z�̤��@�C�z����o�ʶl��A�O�ѩ�z<br />
���Q�K�[���� {bbname} ���|���A��e Email �Y�O�ڭ̬��z���U���l�c�a�}�C<br />
<br />
----------------------------------------------------------------------<br />
���n�I<br />
----------------------------------------------------------------------<br />
<br />
�p�G�z�� {bbname} ���P����εL�N�����|���A�Щ����o�ʶl��C<br />
<br />
----------------------------------------------------------------------<br />
�b���H��<br />
----------------------------------------------------------------------<br />
<br />
�����W�١G{bbname}<br />
�����a�}�G{siteurl}<br />
<br />
�Τ�W�G{newusername}<br />
�K�X�G{newpassword}<br />
<br />
�q�{�b�_�z�i�H�ϥαz���b���n�� {bbname}�A���z�ϥδr�֡I<br />
<br />
<br />
<br />
���P<br />
<br />
{bbname} �޲z�ζ�.<br />
{siteurl}',


	'birthday_subject' =>		'���z�ͤ�ּ�',
	'birthday_message' => 		'<br />
{username}�A<br />
�o�ʫH�O�� {bbname} �o�e���C<br />
<br />
�z����o�ʶl��A�O�ѩ�o�Ӷl�c�a�}�b {bbname} �Q�n�O���Τ�l�c�A<br />
�åB���ӱz��g���H���A���ѬO�z���ͤ�C�ܰ�����b���ɬ��z�m�W�@��<br />
�ͤ鯬�֡A���ԥN��{bbname}�޲z�ζ��A�J�߯��ֱz�ͤ�ּ֡C<br />
<br />
�p�G�z�ëD {bbname} ���|���A�Τ��ѨëD�z���ͤ�A�i��O���H�~�ΤF�z���l<br />
��a�}�A�ο��~����g�F�ͤ�H���C���l�󤣷|�h�����Ƶo�e�A�Щ����o�ʶl��C<br />
<br />
<br />
���P<br />
<br />
{bbname} �޲z�ζ�.<br />
{siteurl}',

	'email_to_friend_subject' =>	'{$_G[member][username]} ���˵��z: $thread[subject]',
	'email_to_friend_message' =>	'<br />
�o�ʫH�O�� {$_G[setting][bbname]} �� {$_G[member][username]} �o�e���C<br />
<br />
�z����o�ʶl��A�O�ѩ�b {$_G[member][username]} �q�L {$_G[setting][bbname]} ���u���˵��B�͡v<br />
�\\����ˤF�p�U�����e���z�C�p�G�z�惡���P����A�Щ����o�ʶl��C�z���ݭn�h�q�ζi���L�i�@�B���ާ@�C<br />
<br />
----------------------------------------------------------------------<br />
�H����}�l<br />
----------------------------------------------------------------------<br />
<br />
$message<br />
<br />
----------------------------------------------------------------------<br />
�H���嵲��<br />
----------------------------------------------------------------------<br />
<br />
�Ъ`�N�o�ʫH�ȶȬO�ѥΤ�ϥ� �u���˵��B�͡v�o�e���A���O�����x��l��A<br />
�����޲z�ζ����|��o���l��t�d�C<br />
<br />
�w��z�X�� {$_G[setting][bbname]}<br />
$_G[siteurl]',

	'email_to_invite_subject' =>	'�z���B�� {$_G[member][username]} �o�e {$_G[setting][bbname]} �������U�ܽнX���z',
	'email_to_invite_message' =>	'<br />
$sendtoname,<br />
�o�ʫH�O�� {$_G[setting][bbname]} �� {$_G[member][username]} �o�e���C<br />
<br />
�z����o�ʶl��A�O�ѩ� {$_G[member][username]} �q�L {bbname} ���u�o�e�ܽнX���B�͡v<br />
�\\����ˤF�p�U�����e���z�C�p�G�z�惡���P����A�Щ����o�ʶl��C�z���ݭn�h�q�ζi���L�i<br />
�@�B���ާ@�C<br />
<br />
----------------------------------------------------------------------<br />
�H����}�l<br />
----------------------------------------------------------------------<br />
<br />
$message<br />
<br />
----------------------------------------------------------------------<br />
�H���嵲��<br />
----------------------------------------------------------------------<br />
<br />
�Ъ`�N�o�ʫH�ȶȬO�ѥΤ�ϥ� �u�o�e�ܽнX���B�͡v�o�e���A���O�����x��l��A<br />
�����޲z�ζ����|��o���l��t�d�C<br />
<br />
�w��z�X�� {$_G[setting][bbname]}<br />
$_G[siteurl]',


	'moderate_member_subject' =>	'�Τ�f�ֵ��G�q��',
	'moderate_member_message' =>	'<br />
<p>{username},
�o�ʫH�O�� {bbname} �o�e���C</p>

<p>�z����o�ʶl��A�O�ѩ�o�Ӷl�c�a�}�b {bbname} �Q�s�Τ���U�ɩ�
�ϥΡA�B�޲z���]�m�F��s�Τ�ݭn�i��H�u�f�֡A���l��N�q���z����
�ӽЪ��f�ֵ��G�C</p>
<br />
----------------------------------------------------------------------<br />
<strong>���U�H���P�f�ֵ��G</strong><br />
----------------------------------------------------------------------<br />
<br />
�Τ�W: {username}<br />
���U�ɶ�: {regdate}<br />
����ɶ�: {submitdate}<br />
���榸��: {submittimes}<br />
���U��]: {message}<br />
<br />
�f�ֵ��G: {modresult}<br />
�f�֮ɶ�: {moddate}<br />
�f�ֺ޲z��: {adminusername}<br />
�޲z���d��: {remark}<br />
<br />
----------------------------------------------------------------------<br />
<strong>�f�ֵ��G����</strong><br />
----------------------------------------------------------------------<br />

<p>�q�L: �z�����U�w�q�L�f�֡A�z�w���� {bbname} �������Τ�C</p>

<p>�_�M: �z�����U�H��������A�Υ������ڭ̹�s�Τ᪺�Y�ǭn�D�A�z�i�H
	  �ھں޲z���d���A<a href="home.php?mod=spacecp&ac=profile" target="_blank">�����z�����U�H��</a>�A�M��A������C</p>

<p>�R���G�z�����U�ѩ�P�ڭ̪��n�D���t���j�A�Υ������s���U�H�Ƥw
	  �W�L�w���A�ӽФw�Q�_�M�C�z���b���w�q�ƾڮw���R���A�N�L�k
	  �A�ϥΨ�n���δ���A���f�֡A�бz�̸ѡC</p>

<br />
<br />
���P<br />
<br />
{bbname} �޲z�ζ�.<br />
{siteurl}',

	'adv_expiration_subject' =>	'�z���I���s�i�N�� {day} �ѫ����A�ФήɳB�z',
	'adv_expiration_message' =>	'�z���I���H�U�s�i�N�� {day} �ѫ����A�ФήɳB�z�G<br /><br />{advs}',
	'invite_payment_email_message' => '
�w��z���{{bbname}�]{siteurl}�^�A�z���q��{orderid}�w�g��I�����A�q��w�T�{���ġC<br />
<br />----------------------------------------------------------------------<br />
�H�U�O�z��o���ܽнX
<br />----------------------------------------------------------------------<br />

{codetext}

<br />----------------------------------------------------------------------<br />
���n�I
<br />----------------------------------------------------------------------<br />',
);

?>