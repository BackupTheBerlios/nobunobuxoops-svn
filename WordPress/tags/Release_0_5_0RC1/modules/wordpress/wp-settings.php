<?php
$HTTP_HOST = getenv('HTTP_HOST');  /* domain name */
$REMOTE_ADDR = getenv('REMOTE_ADDR'); /* visitor's IP */
$HTTP_USER_AGENT = getenv('HTTP_USER_AGENT'); /* visitor's browser */

global $siteurl;

if(!defined('WPINC')) define('WPINC', 'wp-includes');

require_once (ABSPATH . WPINC . '/wp-db.php');
// Table names
$wpdb->posts[$wp_id]               = $table_prefix[$wp_id] . 'posts';
$wpdb->users[$wp_id]               = $table_prefix[$wp_id] . 'users';
$wpdb->settings[$wp_id]            = $table_prefix[$wp_id] . 'settings'; // only used during upgrade
$wpdb->categories[$wp_id]          = $table_prefix[$wp_id] . 'categories';
$wpdb->post2cat[$wp_id]            = $table_prefix[$wp_id] . 'post2cat';
$wpdb->comments[$wp_id]            = $table_prefix[$wp_id] . 'comments';
$wpdb->links[$wp_id]               = $table_prefix[$wp_id] . 'links';
$wpdb->linkcategories[$wp_id]      = $table_prefix[$wp_id] . 'linkcategories';
$wpdb->options[$wp_id]             = $table_prefix[$wp_id] . 'options';
$wpdb->optiontypes[$wp_id]         = $table_prefix[$wp_id] . 'optiontypes';
$wpdb->optionvalues[$wp_id]        = $table_prefix[$wp_id] . 'optionvalues';
$wpdb->optiongroups[$wp_id]        = $table_prefix[$wp_id] . 'optiongroups';
$wpdb->optiongroup_options[$wp_id] = $table_prefix[$wp_id] . 'optiongroup_options';
$wpdb->postmeta[$wp_id]            = $table_prefix[$wp_id] . 'postmeta';

$dogs = $wpdb->get_results("SELECT * FROM {$wpdb->categories[$wp_id]} WHERE 1=1");
foreach ($dogs as $catt) {
    $cache_categories[$wp_id][$catt->cat_ID] = $catt;
}
// This is the name of the include directory. No "/" allowed.

require ('wp-ver.php');
require_once (ABSPATH . WPINC . '/functions.php');
require_once (ABSPATH . WPINC . '/wp-tickets.php');
require_once (ABSPATH . WPINC . '/functions-formatting.php');
require_once (ABSPATH . WPINC . '/functions-filter.php');
if (get_settings('hack_file')) {
	include_once ($wp_base[$wp_id] . '/my-hacks.php');
}
require ('wp-config-extra.php');
require_once (ABSPATH . WPINC . '/template-functions.php');
require_once (ABSPATH . WPINC . '/class-xmlrpc.php');
require_once (ABSPATH . WPINC . '/class-xmlrpcs.php');
require_once (ABSPATH . WPINC . '/links.php');
require_once (ABSPATH . WPINC . '/kses.php');
auto_upgrade();

// We should eventually migrate to either calling
// get_settings() wherever these are needed OR
// accessing a single global $all_settings var

if (!strstr($_SERVER['REQUEST_URI'], 'install.php')) {
	$siteurl = $wp_siteurl[$wp_id];
	if (get_xoops_option($wp_mod[$wp_id],'wp_use_xoops_smilies')) {
		$smilies_directory = XOOPS_URL."/uploads";
	} else {
		$smilies_directory = get_settings('smilies_directory');
	}
	//WordPressプラグイン互換性確保用
    $querystring_start = '?';
    $querystring_equal = '=';
    $querystring_separator = '&amp;';
}

// Used to guarantee unique cookies
$cookiehash = md5($siteurl);

require (ABSPATH . WPINC . '/vars.php');
require (ABSPATH . WPINC . '/wp-filter-setup.php');
if ($wp_inblock!=1) {
	if (!defined('XOOPS_PULUGIN'.$wp_id)) {
		define('XOOPS_PULUGIN'.$wp_id,1);
		if (get_settings('active_plugins')) {
			$check_plugins = explode("\n", (get_settings('active_plugins')));
			foreach ($check_plugins as $check_plugin) {
				if (file_exists(ABSPATH . 'wp-content/plugins/' . $check_plugin)) {
					include_once(ABSPATH . 'wp-content/plugins/' . $check_plugin);
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