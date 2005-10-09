<?php
function b_sysutil_login_show($options) {
	global $xoopsUser, $xoopsConfig;
	$showform = empty( $options[0] ) ? 0 : 1 ;
	if(empty($_SESSION['xoopsUserId'])) {
		if(!isset($_COOKIE['autologin_uname']) || !isset($_COOKIE['autologin_pass'])) {
			if (!$xoopsUser) {
	            $config_handler =& xoops_gethandler('config');
	            $xoopsConfigUser =& $config_handler->getConfigsByCat(XOOPS_CONF_USER);
				$block = array();
				$block['lang_username'] = _USERNAME;
				$block['unamevalue'] = "";
				if (isset($_COOKIE[$xoopsConfig['usercookie']])) {
					$block['unamevalue'] = $_COOKIE[$xoopsConfig['usercookie']];
				}
				$block['allow_register'] = $xoopsConfigUser['allow_register'];
				$block['lang_password'] = _PASSWORD;
				$block['lang_login'] = _LOGIN;
				$block['lang_lostpass'] = _MB_SYSUTIL_LPASS;
				$block['lang_registernow'] = _MB_SYSUTIL_RNOW;
				$block['lang_rememberme'] = _MB_SYSUTIL_REMEMBERME;
		    	return $block;
		    }
		}
	}
	return false;
}
?>
