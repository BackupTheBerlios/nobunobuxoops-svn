<?php
if (file_exists(dirname(__FILE__).'/conf_ml.php')) {
	include_once dirname(__FILE__).'/conf_ml.php';
} else {
	include_once dirname(__FILE__).'/conf_ml.dist.php';
}
/**
* Modify for The Easiest MultiLanguag Hack Enhancement for XOOPS by nobunobu
* Original is XOOPS Multilanguages by marcan
* Set the languages files, language cookie, etc...
**/
// Target check
if(!preg_match( '?'.preg_quote(XOOPS_ROOT_PATH,'?').'(/modules/[^\/]+/admin/|/common/|/modules/system/|/admin\.php)?' ,$_SERVER['SCRIPT_FILENAME'] ) ) {
	// check the current language
	$sysutil_ml_langs = explode( ',' , SYSUTIL_ML_LANGS ) ;
	if( ! empty( $_GET[SYSUTIL_ML_PARAM_NAME] ) && in_array( $_GET[SYSUTIL_ML_PARAM_NAME] , $sysutil_ml_langs ) ) {
		$sysutil_ml_lang = $_GET[SYSUTIL_ML_PARAM_NAME] ;
		setcookie( SYSUTIL_ML_PARAM_NAME , $sysutil_ml_lang , time() + SYSUTIL_ML_COOKIELIFETIME , $xoops_cookie_path, '' , 0 ) ;
	} else if( ! empty( $_COOKIE[SYSUTIL_ML_PARAM_NAME] ) && in_array( $_COOKIE[SYSUTIL_ML_PARAM_NAME] , $sysutil_ml_langs ) ) {
		$sysutil_ml_lang = $_COOKIE[SYSUTIL_ML_PARAM_NAME] ;
	} else {
		$sysutil_ml_lang = sysutil_ml_getlangbyname(sysutil_get_xoops_option('sysutil', 'sysutil_default_lang'));
	}
	ob_start( 'sysutil_ml_filter' ) ;
}
if (sysutil_get_xoops_option('sysutil', 'sysutil_change_lang_conf')) {
	if ((!empty($_GET[SYSUTIL_ML_PARAM_NAME])) && ($sysutil_ml_langname = sysutil_ml_getlangname($_GET[SYSUTIL_ML_PARAM_NAME]))) {
		$xoopsConfig['language'] = $sysutil_ml_langname;
		$_SERVER['QUERY_STRING'] = preg_replace('/(^|&)'.SYSUTIL_ML_PARAM_NAME.'\=.*$/','',$_SERVER['QUERY_STRING']);
		$_SERVER['argv'][0] = preg_replace('/(^|&)'.SYSUTIL_ML_PARAM_NAME.'\=.*$/','',$_SERVER['argv'][0]);;
	} else {
		if (!empty($_COOKIE[SYSUTIL_ML_COOKIE_NAME])) {
			$xoopsConfig['language'] = $_COOKIE[SYSUTIL_ML_COOKIE_NAME];
		} else {
			$xoopsConfig['language'] = sysutil_get_xoops_option('sysutil', 'sysutil_default_lang');
		}
		$sysutil_ml_langname = $xoopsConfig['language'];
	}
	if (empty($_COOKIE[SYSUTIL_ML_COOKIE_NAME]) || ($_COOKIE[SYSUTIL_ML_COOKIE_NAME] != $xoopsConfig['language'])) {
		setcookie(SYSUTIL_ML_COOKIE_NAME, $sysutil_ml_langname, time() + SYSUTIL_ML_COOKIELIFETIME, $xoops_cookie_path, '' , 0);
	}
} else {
	$xoopsConfig['language'] = sysutil_get_xoops_option('sysutil', 'sysutil_default_lang');
}
// ob filter
function sysutil_ml_filter( $s )
{
	global $sysutil_ml_lang , $xoopsUser ;

	$sysutil_ml_langs = explode( ',' , SYSUTIL_ML_LANGS ) ;
	// protection against some injection
	if( ! in_array( $GLOBALS['sysutil_ml_lang'] , $sysutil_ml_langs ) ) {
		$GLOBALS['sysutil_ml_lang'] = $sysutil_ml_langs[0] ;
	}

	// escape brackets inside of <input type="text" value="...">
	$s = preg_replace_callback( '/(\<input)(?=.*type\=[\'\"]?text[\'\"]?)([^>]*)(\>)/isU' , 'sysutil_ml_escape_bracket' , $s ) ;

	// escape brackets inside of <textarea></textarea>
	$s = preg_replace_callback( '/(\<textarea[^>]*\>)(.*)(<\/textarea\>)/isU' , 'sysutil_ml_escape_bracket' , $s ) ;

	// multilanguage image tag
	$langimages = explode( ',' , SYSUTIL_ML_LANGIMAGES ) ;
	$langnames = explode( ',' , SYSUTIL_ML_LANGNAMES ) ;
	if( empty( $_SERVER['QUERY_STRING'] ) ) {
		$link_base = '?'.SYSUTIL_ML_PARAM_NAME.'=' ;
	} else if( ( $pos = strpos($_SERVER['QUERY_STRING'], SYSUTIL_ML_PARAM_NAME.'=') ) === false ) {
		$link_base = '?'.htmlspecialchars($_SERVER['QUERY_STRING'],ENT_QUOTES).'&amp;'.SYSUTIL_ML_PARAM_NAME.'=' ;
	} else if( $pos < 2 ) {
		$link_base = '?'.SYSUTIL_ML_PARAM_NAME.'=' ;
	} else {
		$link_base = '?'.htmlspecialchars(substr($_SERVER['QUERY_STRING'],0,$pos-1),ENT_QUOTES).'&amp;'.SYSUTIL_ML_PARAM_NAME.'=' ;
	}
	$langimage_html = '' ;
	foreach( $sysutil_ml_langs as $l => $lang ) {
		$langimage_html .= '<a href="'.$link_base.$lang.'"><img src="'.XOOPS_URL.'/'.$langimages[$l].'" alt="flag" title="'.$langnames[$l].'" /></a>' ;
	}
	$s = preg_replace( '/\['.SYSUTIL_ML_IMAGETAG.'\]/' , $langimage_html , $s ) ;

	$s = preg_replace( '/\['.SYSUTIL_ML_URLTAG.':([^\]]*?)\]/' , $link_base."$1" , $s ) ;

	// eliminate description between the other language tags.
	foreach( $sysutil_ml_langs as $lang ) {
		if( $GLOBALS['sysutil_ml_lang'] == $lang ) continue ;
		$s = preg_replace_callback( '/\['.preg_quote($lang).'\].*\[\/'.preg_quote($lang).'(?:\]\<br \/\>|\])/isU' , 'sysutil_ml_check_nevercross' , $s ) ;
	}

	// simple pattern to strip selected lang_tags (remove all tags)
	$s = preg_replace( '/\[\/?'.preg_quote($GLOBALS['sysutil_ml_lang']).'\](\<br \/\>)?/i' , '' , $s ) ;

	return $s ;
}

function sysutil_ml_escape_bracket( $matches )
{
	return $matches[1].str_replace('[','&#91;',$matches[2]).$matches[3] ;
}

function sysutil_ml_check_nevercross( $matches )
{
	return preg_match( SYSUTIL_ML_NEVERCROSSREGEX , $matches[0] ) ? $matches[0] : '' ;
}
	
function sysutil_ml_getlangname($lang = '')
{
	include_once XOOPS_ROOT_PATH."/class/xoopslists.php";
	$sysutil_ml_langs = explode(',', SYSUTIL_ML_LANGS);
	$idx = array_search($lang,  $sysutil_ml_langs);
	$sysutil_ml_langnames = explode(',', SYSUTIL_ML_LANGNAMES);
	$langname = $sysutil_ml_langnames[$idx];
	$lang_available = XoopsLists::getLangList();
	If ( ($langname != '') && (in_array($langname, $lang_available)) ) {
		return $langname;
	}
	return false;
}
function sysutil_ml_getlangbyname($langname) {
	include_once XOOPS_ROOT_PATH."/class/xoopslists.php";
	$lang_available = XoopsLists::getLangList();
	If ( ($langname != '') && (in_array($langname, $lang_available)) ) {
		$sysutil_ml_langnames = explode(',', SYSUTIL_ML_LANGNAMES);
		$idx = array_search($langname,  $sysutil_ml_langnames);
		$sysutil_ml_langs = explode(',', SYSUTIL_ML_LANGS);
		$sysutil_ml_lang = $sysutil_ml_langs[$idx];
		return $sysutil_ml_lang;
	}
	return false;
}
?>
