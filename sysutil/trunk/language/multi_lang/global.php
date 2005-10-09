<?php
include_once dirname(__FILE__).'/common.inc.php';

if (sysutil_get_xoops_option('sysutil', 'sysutil_use_ml')) {
	include_once dirname(__FILE__).'/sysutil_ml.inc.php';
} else if (sysutil_get_xoops_option('sysutil', 'sysutil_default_lang')) {
	$xoopsConfig['language'] = sysutil_get_xoops_option('sysutil', 'sysutil_default_lang');
} else {
	$xoopsConfig['language'] = SYSUTIL_ML_DEFAULT_LANGUNAME;
}

if ( file_exists(XOOPS_ROOT_PATH."/language/".$xoopsConfig['language']."/global.php") ) {
    include_once XOOPS_ROOT_PATH."/language/".$xoopsConfig['language']."/global.php";
} else {
    include_once XOOPS_ROOT_PATH."/language/english/global.php";
}

if (sysutil_get_xoops_option('sysutil', 'sysutil_use_autologin')){
	sysutil_session_pre_start();
	include_once dirname(__FILE__).'/sysutil_autologin.inc.php';
	sysutil_session_pre_close();
}
?>
