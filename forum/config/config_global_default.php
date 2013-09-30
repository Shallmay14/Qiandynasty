<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: config_global_default.php 33337 2013-05-29 02:23:47Z andyzheng $
 */

$_config = array();

// ----------------------------  CONFIG DB  ----------------------------- //
// ----------------------------  �ƾڮw�����]�m---------------------------- //

/**
 * �ƾڮw�D�A�Ⱦ��]�m, ����h�ժA�Ⱦ��]�m, ��]�m�h�ժA�Ⱦ���, �h�|�ھڤ��G�������ϥάY�ӪA�Ⱦ�
 * @example
 * $_config['db']['1']['dbhost'] = 'localhost'; // �A�Ⱦ��a�}
 * $_config['db']['1']['dbuser'] = 'root'; // �Τ�
 * $_config['db']['1']['dbpw'] = 'root';// �K�X
 * $_config['db']['1']['dbcharset'] = 'gbk';// �r�Ŷ�
 * $_config['db']['1']['pconnect'] = '0';// �O�_����s��
 * $_config['db']['1']['dbname'] = 'x1';// �ƾڮw
 * $_config['db']['1']['tablepre'] = 'pre_';// ��W�e��
 *
 * $_config['db']['2']['dbhost'] = 'localhost';
 * ...
 *
 */
$_config['db'][1]['dbhost']  		= 'localhost';
$_config['db'][1]['dbuser']  		= 'root';
$_config['db'][1]['dbpw'] 	 	= 'root';
$_config['db'][1]['dbcharset'] 		= 'big5';
$_config['db'][1]['pconnect'] 		= 0;
$_config['db'][1]['dbname']  		= 'ultrax';
$_config['db'][1]['tablepre'] 		= 'pre_';

/**
 * �ƾڮw�q�A�Ⱦ��]�m( slave, �uŪ ), ����h�ժA�Ⱦ��]�m, ��]�m�h�ժA�Ⱦ���, �t�ήھڨC���H���ϥ�
 * @example
 * $_config['db']['1']['slave']['1']['dbhost'] = 'localhost';
 * $_config['db']['1']['slave']['1']['dbuser'] = 'root';
 * $_config['db']['1']['slave']['1']['dbpw'] = 'root';
 * $_config['db']['1']['slave']['1']['dbcharset'] = 'gbk';
 * $_config['db']['1']['slave']['1']['pconnect'] = '0';
 * $_config['db']['1']['slave']['1']['dbname'] = 'x1';
 * $_config['db']['1']['slave']['1']['tablepre'] = 'pre_';
 * $_config['db']['1']['slave']['1']['weight'] = '0'; //�v���G�ƾڶV�j�v���V��
 *
 * $_config['db']['1']['slave']['2']['dbhost'] = 'localhost';
 * ...
 *
 */
$_config['db']['1']['slave'] = array();

//�ҥαq�A�Ⱦ����}��
$_config['db']['slave'] = false;
/**
 * �ƾڮw ���G���p�����]�m
 *
 * @example �N common_member ���p��ĤG�A�Ⱦ�, common_session ���p�b�ĤT�A�Ⱦ�, �h�]�m��
 * $_config['db']['map']['common_member'] = 2;
 * $_config['db']['map']['common_session'] = 3;
 *
 * ���S�����T�n���A�Ⱦ�����, �h�@���q�{���p�b�Ĥ@�A�Ⱦ��W
 *
 */
$_config['db']['map'] = array();

/**
 * �ƾڮw ���@�]�m, �����]�m�q�`��w��C�ӳ��p���A�Ⱦ�
 */
$_config['db']['common'] = array();

/**
 *  �T�αq�ƾڮw���ƾڪ�, ��W�r�����ϥγr������
 *
 * @example common_session, common_member �o��Ӫ�ȱq�D�A�Ⱦ�Ū�g, ���ϥαq�A�Ⱦ�
 * $_config['db']['common']['slave_except_table'] = 'common_session, common_member';
 *
 */
$_config['db']['common']['slave_except_table'] = '';

/**
 * ���s�A�Ⱦ��u�Ƴ]�m
 * �H�U�]�m�ݭnPHP�X�i�ե����A�䤤 memcache �u�����L�]�m�A
 * �� memcache �L�k�ҥήɡA�|�۰ʶ}�ҥt�~������u�ƼҦ�
 */

//���s�ܶq�e��, �i���,�קK�P�A�Ⱦ������{�Ǥޥο���
$_config['memory']['prefix'] = 'discuz_';

/* reids�]�m, �ݭnPHP�X�i�ե���, timeout�Ѽƪ��@�ΨS���d�� */
$_config['memory']['redis']['server'] = '';
$_config['memory']['redis']['port'] = 6379;
$_config['memory']['redis']['pconnect'] = 1;
$_config['memory']['redis']['timeout'] = 0;
$_config['memory']['redis']['requirepass'] = '';
/**
 * �O�_�ϥ� Redis::SERIALIZER_IGBINARY�ﶵ,�ݭnigbinary���,windows�U���ծɽ������A�_�h�|�X>�{���~Reading from client: Connection reset by peer
 * ����H�U�ﶵ�A�q�{�ϥ�PHP��serializer
 * [���n] �ӿﶵ�w�g���N��Ӫ� $_config['memory']['redis']['igbinary'] �ﶵ
 * Redis::SERIALIZER_IGBINARY =2
 * Redis::SERIALIZER_PHP =1
 * Redis::SERIALIZER_NONE =0 //�h���ϥ�serialize,�Y�L�k�O�sarray
 */
$_config['memory']['redis']['serializer'] = 1;

$_config['memory']['memcache']['server'] = '';			// memcache �A�Ⱦ��a�}
$_config['memory']['memcache']['port'] = 11211;			// memcache �A�Ⱦ��ݤf
$_config['memory']['memcache']['pconnect'] = 1;			// memcache �O�_���[�s��
$_config['memory']['memcache']['timeout'] = 1;			// memcache �A�Ⱦ��s���W��

$_config['memory']['apc'] = 1;							// �Ұʹ� apc �����
$_config['memory']['xcache'] = 1;						// �Ұʹ� xcache �����
$_config['memory']['eaccelerator'] = 1;					// �Ұʹ� eaccelerator �����
$_config['memory']['wincache'] = 1;						// �Ұʹ� wincache �����
// �A�Ⱦ������]�m
$_config['server']['id']		= 1;			// �A�Ⱦ��s���A�hwebserver���ɭԡA�Ω���ѷ�e�A�Ⱦ���ID

// ����U������
//
// ���a���Ū���Ҧ�; �Ҧ�2���̸`�٤��s�覡�A��������h�u�{�U��
// 1=fread 2=readfile 3=fpassthru 4=fpassthru+multiple
$_config['download']['readmod'] = 2;

// �O�_�ҥ� X-Sendfile �\\��]�ݭn�A�Ⱦ�����^0=close 1=nginx 2=lighttpd 3=apache
$_config['download']['xsendfile']['type'] = 0;

// �ҥ� nginx X-sendfile �ɡA�׾ª���ؿ��������M�g���|�A�Шϥ� / ����
$_config['download']['xsendfile']['dir'] = '/down/';

//  CONFIG CACHE
$_config['cache']['type'] 			= 'sql';	// �w�s���� file=���w�s, sql=�ƾڮw�w�s

// ������X�]�m
$_config['output']['charset'] 			= 'big5';	// �����r�Ŷ�
$_config['output']['forceheader']		= 1;		// �j���X�����r�Ŷ��A�Ω��קK�Y�����ҶýX
$_config['output']['gzip'] 			= 0;		// �O�_�ĥ� Gzip ���Y��X
$_config['output']['tplrefresh'] 		= 1;		// �ҪO�۰ʨ�s�}�� 0=����, 1=���}
$_config['output']['language'] 			= 'zh_tw';	// �����y�� zh_cn/zh_tw
$_config['output']['staticurl'] 		= 'static/';	// ���I�R�A�����|�A�u/�v����
$_config['output']['ajaxvalidate']		= 0;		// �O�_�Y������ Ajax �������u��� 0=�����A1=���}
$_config['output']['iecompatible']		= 0;		// ���� IE �ݮe�Ҧ�

// COOKIE �]�m
$_config['cookie']['cookiepre'] 		= 'discuz_'; 	// COOKIE�e��
$_config['cookie']['cookiedomain'] 		= ''; 		// COOKIE�@�ΰ�
$_config['cookie']['cookiepath'] 		= '/'; 		// COOKIE�@�θ��|

// ���I�w���]�m
$_config['security']['authkey']			= 'asdfasfas';	// ���I�[�K�K�_
$_config['security']['urlxssdefend']		= true;		// �ۨ� URL XSS ���m
$_config['security']['attackevasive']		= 0;		// CC �������m 1|2|4|8

$_config['security']['querysafe']['status']	= 1;		// �O�_�}��SQL�w���˴��A�i�۰ʹw��SQL�`�J����
$_config['security']['querysafe']['dfunction']	= array('load_file','hex','substring','if','ord','char');
$_config['security']['querysafe']['daction']	= array('@','intooutfile','intodumpfile','unionselect','(select', 'unionall', 'uniondistinct');
$_config['security']['querysafe']['dnote']	= array('/*','*/','#','--','"');
$_config['security']['querysafe']['dlikehex']	= 1;
$_config['security']['querysafe']['afullnote']	= 0;

$_config['admincp']['founder']			= '1';		// ���I�Щl�H�G�֦����I�޲z��x���̰��v���A�C�ӯ��I�i�H�]�m 1�W�Φh�W�Щl�H
								// �i�H�ϥ�uid�A�]�i�H�ϥΥΤ�W�F�h�ӳЩl�H�����Шϥγr���u,�v���};
$_config['admincp']['forcesecques']		= 0;		// �޲z�H�������]�m�w�����ݤ~��i�J�t�γ]�m 0=�_, 1=�O[�w��]
$_config['admincp']['checkip']			= 1;		// ��x�޲z�ާ@�O�_���Һ޲z���� IP, 1=�O[�w��], 0=�_�C�Ȧb�޲z���L�k�n����x�ɳ]�m 0�C
$_config['admincp']['runquery']			= 0;		// �O�_���\\��x�B�� SQL �y�y 1=�O 0=�_[�w��]
$_config['admincp']['dbimport']			= 1;		// �O�_���\\��x��_�׾¼ƾ�  1=�O 0=�_[�w��]

/**
 * �t�λ��{�եΥ\\��Ҷ�
 */

// ���{�ե�: �`�}�� 0=��  1=�}
$_config['remote']['on'] = 0;

// ���{�ե�: �{�ǥؿ��W. �X��w���Ҽ{,�z�i�H���o�ӥؿ��W, �ק粒��, �Ф�u�ק�{�Ǫ���ڥؿ�
$_config['remote']['dir'] = 'remote';

// ���{�ե�: �q�H�K�_. �Ω�Ȥ�ݩM���A�Ⱥݪ��q�H�[�K. ���פ��֩� 32 ��
//          �q�{�ȬO $_config['security']['authkey']	�� md5, �z�]�i�H��u���w
$_config['remote']['appkey'] = md5($_config['security']['authkey']);

// ���{�ե�: �}�ҥ~�� cron ����. �t�Τ������A����cron, cron���ȥѥ~���{�ǿE��
$_config['remote']['cron'] = 0;

// $_GET|$_POST���ݮe�B�z�A0�������A1���}�ҡF�}�ҫ�Y�i�ϥ�$_G['gp_xx'](xx���ܶq�W�A$_GET�M$_POST���X���Ҧ��ܶq�W)�A�Ȭ��w�gaddslashes()�B�z�L
$_config['input']['compatible'] = 1;

?>