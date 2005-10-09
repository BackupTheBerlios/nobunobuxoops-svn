<?php
// get cookie path
$xoops_cookie_path = defined('XOOPS_COOKIE_PATH') ? XOOPS_COOKIE_PATH : preg_replace( '?http://[^/]+(/.*)$?' , "$1" , XOOPS_URL ) ;
if( $xoops_cookie_path == XOOPS_URL ) $xoops_cookie_path = '/' ;

function sysutil_get_xoops_option($dirname,$conf_name) {
	if (empty($GLOBALS['sysutil_config_cache'][$dirname])) {
		$module_handler =& xoops_gethandler('module');
		$module=$module_handler->getByDirname($dirname);
		if ($module) {
			$mid=$module->getVar('mid');
			$config_handler =& xoops_gethandler('config');
		    $records =& $config_handler->getConfigList($mid);
		    $GLOBALS['sysutil_config_cache'][$dirname] = $records;
		} else {
			return false;
		}
	}
	if (!empty($GLOBALS['sysutil_config_cache'][$dirname][$conf_name])) {
		return $GLOBALS['sysutil_config_cache'][$dirname][$conf_name];
	} else {
		return false;
	}
}

function sysutil_session_pre_start() {
	$sess_handler =& xoops_gethandler('session');
	if ($GLOBALS['xoopsConfig']['use_ssl'] && isset($_POST[$GLOBALS['xoopsConfig']['sslpost_name']]) && $_POST[$GLOBALS['xoopsConfig']['sslpost_name']] != '') {
	    session_id($_POST[$GLOBALS['xoopsConfig']['sslpost_name']]);
	} elseif ($GLOBALS['xoopsConfig']['use_mysession'] && $GLOBALS['xoopsConfig']['session_name'] != '') {
	    if (isset($_COOKIE[$GLOBALS['xoopsConfig']['session_name']])) {
	        session_id($_COOKIE[$GLOBALS['xoopsConfig']['session_name']]);
	    } else {
	        // no custom session cookie set, destroy session if any
	        $_SESSION = array();
	        //session_destroy();
	    }
	    @ini_set('session.gc_maxlifetime', $GLOBALS['xoopsConfig']['session_expire'] * 60);
	}
	session_set_save_handler(
		array(&$sess_handler, 'open'),
		array(&$sess_handler, 'close'),
		array(&$sess_handler, 'read'),
		array(&$sess_handler, 'write'),
		array(&$sess_handler, 'destroy'),
		array(&$sess_handler, 'gc')
	);
	session_start();
}

function sysutil_session_pre_close() {
	session_write_close();
}
?>