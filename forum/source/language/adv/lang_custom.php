<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: lang_custom.php 27449 2012-02-01 05:32:35Z zhangguosheng $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$lang = array
(
	'custom_name' => '�۩w�q�s�i',
	'custom_desc' => '�q�L�b�Ҫ��BHTML ��󤤲K�[�s�i�N�X�A�i�H�b���I�����N�����K�[�s�i�C�A�Ω����o²�� HTML ���Ѫ������C<br /><br />
		<a href="javascript:;" onclick="prompt(\'�нƻs(CTRL+C)�H�U���e�òK�[��ҪO���A�K�[���s�i��\', \'<!--{ad/custom_'.$_GET['customid'].'}-->\')" />�����ե�</a>&nbsp;
		<a href="javascript:;" onclick="prompt(\'�нƻs(CTRL+C)�H�U���e�òK�[�� HTML ��󤤡A�K�[���s�i��\', \'&lt;script type=\\\'text/javascript\\\' src=\\\''.$_G['siteurl'].'api.php?mod=ad&adid=custom_'.$_GET['customid'].'\\\'&gt;&lt;/script&gt;\')" />�~���ե�</a>',
	'custom_id_notfound' => '�۩w�q�s�i���s�b',
	'custom_codelink' => '�����ե�',
	'custom_text' => '�۩w�q�s�i',
);

?>