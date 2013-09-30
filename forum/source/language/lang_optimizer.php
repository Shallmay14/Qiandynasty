<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: lang_optimizer.php 32960 2013-03-28 04:36:15Z chenmengshu $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$lang = array
(
	'optimizer_dbbackup_advice' => '�T�Ӥ�S���i��ƾڳƥ��F,��ĳ�ߧY�ƥ��ƾ�',
	'optimizer_dbbackup_lastback' => '�W���ƾڳƥ���',
	'optimizer_dbbackup_clean_safe' => '�S���˴���ƾڮw�ƥ����A�L�w�����D',
	'optimizer_dbbackup_clean_delete' => '�˴��� {filecount} �Ӽƾڳƥ����(�ؿ�: ./data/backup_xxx)�A<br>�кɧ֤�u�ƻs��w����m�ƥ��A�M��R���o�Ǥ��',
	'optimizer_filecheck_advice' => '�T�Ӥ�S���i�������F,��ĳ�ߧY�i�����',
	'optimizer_filecheck_lastcheck' => '�W���������',
	'optimizer_log_clean' => '�� {count} �Ӥ�x��i�H�M�z�u��',
	'optimizer_log_not_found' => '���o�{�i�M�z����x��',
	'optimizer_patch_have' => '�z�� {patchnum} �ӡA�кɧ֧�s',
	'optimizer_patch_check_safe' => '�˴��w��',
	'optimizer_plugin_new_plugin' => '�z�� {newversion} �����Φ��i�Χ�s',
	'optimizer_plugin_no_upgrade' => '���ݭn���Χ�s',
	'optimizer_post_need_split' => '���l��Τ��� {count} �ӻݭn�u��',
	'optimizer_post_not_need' => '���ݭn�u��',
	'optimizer_seo_advice' => '��ĳ�z����SEO�]�m',
	'optimizer_seo_no_need' => '�o�{�w�g�����Fseo�]�m',
	'optimizer_setting_cache_index' => '�w�s�׾­���',
	'optimizer_setting_cache_index_desc' => '�}�Ҧ��\\��i��A�Ⱦ��t��',
	'optimizer_setting_cache_optimize_desc' => '�]�m�w�s�ɶ���900��',
	'optimizer_setting_cache_post' => '�w�s���l',
	'optimizer_setting_cache_post_desc' => '�}�Ҧ��\\��i��A�Ⱦ��t��',
	'optimizer_setting_cache_post_optimize_desc' => '�]�m�w�s�ɶ���900��',
	'optimizer_setting_optimizeviews' => '�u�Ƨ�s�D�D�s���q',
	'optimizer_setting_optimizeviews_desc' => '�}�Ҧ��\\��i���s�D�D�s���q�ɹ�A�Ⱦ����ͪ��t��',
	'optimizer_setting_optimizeviews_optimize_desc' => '�}�Ҧ��\\��',
	'optimizer_setting_delayviewcount' => '����U���q�����s',
	'optimizer_setting_delayviewcount_desc' => '�����s�����s���q�A�i���㭰�C�X�ݶq�ܤj�����I���A�Ⱦ��t��',
	'optimizer_setting_delayviewcount_optimize_desc' => '�}�Ҧ��\\��',
	'optimizer_setting_preventrefresh' => '�d�ݼƶ}�Ҩ���s',
	'optimizer_setting_preventrefresh_desc' => '�}�Ҩ���s�A�i���㭰�C�A�Ⱦ����O',
	'optimizer_setting_preventrefresh_optimize_desc' => '�}�Ҧ��\\��',
	'optimizer_setting_nocacheheaders' => '�T���s�����w��',
	'optimizer_setting_nocacheheaders_desc' => '�i�Ω�ѨM���ӧO�s�������e��s�����`�����D�A���\\��|�[���A�Ⱦ��t��',
	'optimizer_setting_nocacheheaders_optimize_desc' => '�������\\��',
	'optimizer_setting_jspath' => 'JS ���w�s',
	'optimizer_setting_jspath_desc' => '��}�����w�s�ؿ��ɡA�t�η|�N�q�{�ؿ����� *.js ���i�����Y�M��O�s��w�s�ؿ��H����Ū���t��',
	'optimizer_setting_jspath_optimize_desc' => '�ק�js���|��w�s�ؿ�',
	'optimizer_setting_lazyload' => '�Ϥ����ɥ[��',
	'optimizer_setting_lazyload_desc' => '���������Ϥ��b�s��������e���f�ɦA�[���A�i���㭰�C�X�ݶq�ܤj�����I���A�Ⱦ��t��',
	'optimizer_setting_lazyload_optimize_desc' => '�}�Ҧ��\\��',
	'optimizer_setting_sessionclose' => '����session����',
	'optimizer_setting_sessionclose_desc' => '����session����H��A�i���㭰�C���I���A�Ⱦ��t��A��ĳ�b�u�Τ�ƶW�L2�U�ɶ}�ҥ��\\��<br>�`�N�G�C�ȼƩM�Τ᪺�b�u�ɪ��N���A�i��έp�A�׾­����M�����C�������b�u�Τ�C��\\��N���i��',
	'optimizer_setting_sessionclose_optimize_desc' => '�}�Ҧ��\\��',
	'optimizer_setting_need_optimizer' => '�� {count} �ӳ]�m���i�H�u��',
	'optimizer_setting_no_need' => '�]�m���L���u��',
	'optimizer_thread_need_optimizer' => '�ݭn�u�Ʊz���D�D��F',
	'optimizer_thread_no_need' => '���ݭn�u��',
	'optimizer_upgrade_need_optimizer' => '���s�����A�ήɧ�s��̷s����',
	'optimizer_upgrade_no_need' => '�w�g�O�̷s��',
	'optimizer_setting_rewriteguest' => 'Rewrite�Ȱw��C��',
	'optimizer_setting_rewriteguest_desc' => '�}�Ҧ����A�h Rewrite�\\��u��C�ȩM�j���������ġA�i��A�Ⱦ��t��',
	'optimizer_setting_rewriteguest_optimize_desc' => '�}�Ҧ��\\��',
);
?>