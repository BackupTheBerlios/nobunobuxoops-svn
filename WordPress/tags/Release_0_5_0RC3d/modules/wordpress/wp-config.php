<?php
$_wp_base_dir = 'wordpress';
$_wp_base_prefix = 'wp';

include_once dirname( __FILE__ ).'/../../mainfile.php';
// ** MySQL settings ** //
if (!defined('WP_DB_NAME')) {
	define('WP_DB_NAME', XOOPS_DB_NAME);
	define('WP_DB_USER', XOOPS_DB_USER);
	define('WP_DB_PASSWORD', XOOPS_DB_PASS);
	define('WP_DB_HOST', XOOPS_DB_HOST);
}

global $wpdb, $wp_id;

if (empty($GLOBALS['wp_inblock'])) {
	$_wp_my_dirname = basename( dirname( __FILE__ ) ) ;
	if (!preg_match('/\D+(\d*)/', $_wp_my_dirname, $regs )) {
		echo ('Invalid dirname of WordPress: '. htmlspecialchars($_wp_my_dirname));
	}
//	$GLOBALS['wp_id'] = "$regs[1]";
	$GLOBALS['wp_id'] = "".(($regs[1]!=='') ? $regs[1] : '-');
	$GLOBALS['wp_mod'][$GLOBALS['wp_id']] = $_wp_my_dirname;
}
if (($GLOBALS['wp_id']==="")||($GLOBALS['wp_id']==="-")) {
	$GLOBALS['wp_id'] = "-";
	$GLOBALS['wp_prefix'][$GLOBALS['wp_id']] = $_wp_base_prefix.'_';
} else {
	$GLOBALS['wp_prefix'][$GLOBALS['wp_id']] = $_wp_base_prefix.$GLOBALS['wp_id'].'_';
}

$GLOBALS['wp_base'][$GLOBALS['wp_id']] = XOOPS_ROOT_PATH.'/modules/'.$GLOBALS['wp_mod'][$GLOBALS['wp_id']];
$GLOBALS['wp_siteurl'][$GLOBALS['wp_id']] = XOOPS_URL.'/modules/'.$GLOBALS['wp_mod'][$GLOBALS['wp_id']];
$GLOBALS['table_prefix'][$GLOBALS['wp_id']] = $GLOBALS['xoopsDB']->prefix($GLOBALS['wp_prefix'][$GLOBALS['wp_id']]);
//For compatiblity 
if(!defined('ABSBASE')) define ('ABSBASE' , '/modules/'.$GLOBALS['wp_mod'][$GLOBALS['wp_id']]. '/');
if(!defined('ABSPATH')) define ('ABSPATH' , $GLOBALS['wp_base'][$GLOBALS['wp_id']]. '/');

//Obsolute Variables, XOOPS Module Use XOOPS DB Connection
//$server = WP_DB_HOST;
//$loginsql = WP_DB_USER;
//$passsql = WP_DB_PASSWORD;
//$base = WP_DB_NAME;

$GLOBALS['wp_debug'] = false;
$GLOBALS['use_cache'] = 1; // No reason not to

require($GLOBALS['wp_base'][$GLOBALS['wp_id']].'/wp-settings.php');

// Language File
if (!defined('_LANGCODE')) {
	define('_LANGCODE', 'en');
}
if (file_exists($GLOBALS['wp_base'][$GLOBALS['wp_id']].'/wp-lang/lang_'._LANGCODE.'.php')) {
	require_once($GLOBALS['wp_base'][$GLOBALS['wp_id']].'/wp-lang/lang_'._LANGCODE.'.php');
} else {
	require_once($GLOBALS['wp_base'][$GLOBALS['wp_id']].'/wp-lang/lang_en.php');
}
?>