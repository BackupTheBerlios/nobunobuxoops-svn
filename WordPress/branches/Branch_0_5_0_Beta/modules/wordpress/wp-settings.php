<?php
$GLOBALS['HTTP_HOST'] = getenv('HTTP_HOST');  /* domain name */
$GLOBALS['REMOTE_ADDR'] = getenv('REMOTE_ADDR'); /* visitor's IP */
$GLOBALS['HTTP_USER_AGENT'] = getenv('HTTP_USER_AGENT'); /* visitor's browser */

global $siteurl;

if(!defined('WPINC')) define('WPINC', 'wp-includes');

require_once (ABSPATH . WPINC . '/wp-db.php');
// Table names
$GLOBALS['wpdb']->posts[$GLOBALS['wp_id']] = $GLOBALS['table_prefix'][$GLOBALS['wp_id']].'posts';
$GLOBALS['wpdb']->users[$GLOBALS['wp_id']] = $GLOBALS['table_prefix'][$GLOBALS['wp_id']].'users';
$GLOBALS['wpdb']->settings[$GLOBALS['wp_id']] = $GLOBALS['table_prefix'][$GLOBALS['wp_id']].'settings'; // only used during upgrade
$GLOBALS['wpdb']->categories[$GLOBALS['wp_id']] = $GLOBALS['table_prefix'][$GLOBALS['wp_id']].'categories';
$GLOBALS['wpdb']->post2cat[$GLOBALS['wp_id']] = $GLOBALS['table_prefix'][$GLOBALS['wp_id']].'post2cat';
$GLOBALS['wpdb']->comments[$GLOBALS['wp_id']] = $GLOBALS['table_prefix'][$GLOBALS['wp_id']].'comments';
$GLOBALS['wpdb']->links[$GLOBALS['wp_id']] = $GLOBALS['table_prefix'][$GLOBALS['wp_id']].'links';
$GLOBALS['wpdb']->linkcategories[$GLOBALS['wp_id']] = $GLOBALS['table_prefix'][$GLOBALS['wp_id']].'linkcategories';
$GLOBALS['wpdb']->options[$GLOBALS['wp_id']] = $GLOBALS['table_prefix'][$GLOBALS['wp_id']].'options';
$GLOBALS['wpdb']->optiontypes[$GLOBALS['wp_id']] = $GLOBALS['table_prefix'][$GLOBALS['wp_id']].'optiontypes';
$GLOBALS['wpdb']->optionvalues[$GLOBALS['wp_id']] = $GLOBALS['table_prefix'][$GLOBALS['wp_id']].'optionvalues';
$GLOBALS['wpdb']->optiongroups[$GLOBALS['wp_id']] = $GLOBALS['table_prefix'][$GLOBALS['wp_id']].'optiongroups';
$GLOBALS['wpdb']->optiongroup_options[$GLOBALS['wp_id']] = $GLOBALS['table_prefix'][$GLOBALS['wp_id']].'optiongroup_options';
$GLOBALS['wpdb']->postmeta[$GLOBALS['wp_id']] = $GLOBALS['table_prefix'][$GLOBALS['wp_id']].'postmeta';

// This is the name of the include directory. No "/" allowed.

require ('wp-ver.php');
require_once (ABSPATH.WPINC.'/functions.php');
require_once (ABSPATH . 'class/wp_classes.php');
if (empty($GLOBALS['wpPostHandler'][wp_prefix()])) {
	$GLOBALS['wpPostHandler'][wp_prefix()] =& new WordPressPostHandler($GLOBALS['xoopsDB'], wp_prefix());
	$GLOBALS['wpPost2CatHandler'][wp_prefix()] =& new WordPressPost2CatHandler($GLOBALS['xoopsDB'], wp_prefix());
	$GLOBALS['wpPostMetaHandler'][wp_prefix()] =& new WordPressPostMetaHandler($GLOBALS['xoopsDB'], wp_prefix());
	$GLOBALS['wpCommentHandler'][wp_prefix()] =& new WordPressCommentHandler($GLOBALS['xoopsDB'], wp_prefix());
	$GLOBALS['wpUserHandler'][wp_prefix()] =& new WordPressUserHandler($GLOBALS['xoopsDB'], wp_prefix());
	$GLOBALS['wpCategoryHandler'][wp_prefix()] =& new WordPressCategoryHandler($GLOBALS['xoopsDB'], wp_prefix());
	$GLOBALS['wpLinkHandler'][wp_prefix()] =& new WordPressLinkHandler($GLOBALS['xoopsDB'], wp_prefix());
	$GLOBALS['wpLinkCategoryHandler'][wp_prefix()] =& new WordPressLinkCategoryHandler($GLOBALS['xoopsDB'], wp_prefix());
	$GLOBALS['wpOptionGroup2OptionHandler'][wp_prefix()] =& new WordPressOptionGroup2OptionHandler($GLOBALS['xoopsDB'], wp_prefix());
	$GLOBALS['wpOptionHandler'][wp_prefix()] =& new WordPressOptionHandler($GLOBALS['xoopsDB'], wp_prefix());
}
require_once (ABSPATH.WPINC.'/wp-tickets.php');
require_once (ABSPATH.WPINC.'/functions-formatting.php');
require_once (ABSPATH.WPINC.'/functions-filter.php');
if (get_settings('hack_file')) {
	include_once($wp_base[$wp_id].'/my-hacks.php');
}
require ('wp-config-extra.php');
require_once (ABSPATH.WPINC.'/template-functions.php');
require_once (ABSPATH.WPINC.'/class-xmlrpc.php');
require_once (ABSPATH.WPINC.'/class-xmlrpcs.php');
require_once (ABSPATH.WPINC.'/links.php');
require_once (ABSPATH.WPINC.'/kses.php');

if (empty($GLOBALS['cache_categories'][wp_id()])||(count($GLOBALS['cache_categories'][wp_id()])==0)) {
	$GLOBALS['cache_categories'][wp_id()] = array();
	$categoryHandler =& wp_handler('Category');
	$categoriyObjects =& $categoryHandler->getObjects();
	foreach ($categoriyObjects as $categoriyObject) {
		$catt = $categoriyObject->exportWpObject();
		$GLOBALS['cache_categories'][wp_id()][$catt->cat_ID] = $catt;
	}
}
// We should eventually migrate to either calling
// get_settings() wherever these are needed OR
// accessing a single global $all_settings var

if (!strstr($_SERVER['REQUEST_URI'], 'install.php')) {
	$GLOBALS['siteurl'] = wp_siteurl();
	if (get_xoops_option(wp_mod(),'wp_use_xoops_smilies')) {
		$GLOBALS['smilies_directory'] = XOOPS_URL."/uploads";
	} else {
		$GLOBALS['smilies_directory'] = get_settings('smilies_directory');
	}
	//WordPressプラグイン互換性確保用
    $GLOBALS['querystring_start'] = '?';
    $GLOBALS['querystring_equal'] = '=';
    $GLOBALS['querystring_separator'] = '&amp;';
}
$GLOBALS['dateformat'] = stripslashes(get_settings('date_format'));
$GLOBALS['timeformat'] = stripslashes(get_settings('time_format'));

// Used to guarantee unique cookies
$GLOBALS['cookiehash'] = md5($GLOBALS['siteurl']);

require(ABSPATH.WPINC.'/vars.php');
require(ABSPATH.WPINC.'/wp-filter-setup.php');
if ($wp_inblock!=1) {
	if (!defined('XOOPS_PULUGIN'.wp_id())) {
		define('XOOPS_PULUGIN'.wp_id(), 1);
		if (get_settings('active_plugins')) {
			$check_plugins = explode("\n", (get_settings('active_plugins')));
			foreach ($check_plugins as $check_plugin) {
				if (file_exists(ABSPATH . 'wp-content/plugins/'. $check_plugin)) {
					require_once(ABSPATH . 'wp-content/plugins/'. $check_plugin);
				}
			}
		}
		if (!defined('SHUTDOWN_ACTION_HOOK')) {
			define('SHUTDOWN_ACTION_HOOK','1');
			function shutdown_action_hook() {
				do_action('shutdown', '');
			}
			register_shutdown_function('shutdown_action_hook');
		}
	}
}
?>