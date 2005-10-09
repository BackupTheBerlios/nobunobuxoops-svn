<?php
if(!defined('XOOPS_ROOT_PATH')) exit ;
$mydirname = basename(dirname( __FILE__ )) ;

$modversion['name'] = $mydirname;
$modversion['version'] = '0.1';
$modversion['description'] = 'XOOPS System Hack Utilities, AutoLogin & Multi Language Module';
$modversion['credits'] = 'NobuNobu';
$modversion['author'] = 'http://www.kowa.org/';
$modversion['help'] = '';
$modversion['license'] = 'GPL';
$modversion['official'] = 0;
$modversion['image'] = 'images/blank_module.png';
$modversion['dirname'] = $mydirname;
$modversion['onInstall'] = 'include/on_install.php';

$modversion['hasMain'] = 1;

// Templates
$modversion['templates'][1]['file'] = 'sysutil_userform.html';
$modversion['templates'][1]['description'] = 'Auto Logon Form';

//Admin things
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = 'admin/index.php';
$modversion['adminmenu'] = 'admin/menu.php';
$modversion['hasconfig'] = 1;
$modversion['config'][1] = array(
	'name'			=> 'sysutil_use_autologin' ,
	'title'			=> '_MI_SYSUTIL_CFG1_MSG' ,
	'description'	=> '_MI_SYSUTIL_CFG1_DESC' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 0 ,
);
$modversion['config'][2] = array(
	'name'			=> 'sysutil_login_lifetime' ,
	'title'			=> '_MI_SYSUTIL_CFG2_MSG' ,
	'description'	=> '_MI_SYSUTIL_CFG2_DESC' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'int' ,
	'default'		=> 240 ,
);
$modversion['config'][3] = array(
	'name'			=> 'sysutil_use_ml' ,
	'title'			=> '_MI_SYSUTIL_CFG3_MSG' ,
	'description'	=> '_MI_SYSUTIL_CFG3_DESC' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 0 ,
);
$modversion['config'][4] = array(
	'name'			=> 'sysutil_default_lang' ,
	'title'			=> '_MI_SYSUTIL_CFG4_MSG' ,
	'description'	=> '_MI_SYSUTIL_CFG4_DESC' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'string' ,
	'default'		=> 'english' ,
);
$modversion['config'][5] = array(
	'name'			=> 'sysutil_change_lang_conf' ,
	'title'			=> '_MI_SYSUTIL_CFG5_MSG' ,
	'description'	=> '_MI_SYSUTIL_CFG5_DESC' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 1 ,
);

// Blocks
$modversion['blocks'][1]['file'] = 'sysutil_login.php';
$modversion['blocks'][1]['name'] = _MI_SYSUTIL_BNAME1;
$modversion['blocks'][1]['description'] = 'Shows login block';
$modversion['blocks'][1]['show_func'] = 'b_sysutil_login_show';
$modversion['blocks'][1]['template'] = 'sysutil_block_login.html';
// Blocks
$modversion['blocks'][2]['file'] = 'sysutil_langsel.php';
$modversion['blocks'][2]['name'] = _MI_SYSUTIL_BNAME2;
$modversion['blocks'][2]['description'] = 'Shows Select Language';
$modversion['blocks'][2]['show_func'] = 'b_sysutil_langsel_show';
?>
