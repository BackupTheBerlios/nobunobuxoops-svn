<?php
/*
 * PukiWiki weblog_fieldƒvƒ‰ƒOƒCƒ“
 *
 * $Id$
 *
 * CopyRight 2004 nobunobu (nobunobu at www.kowa.org)
 * http://www.kowa.org/
 *
 */
///////////////////////////////////////////////////////////

function plugin_weblog_field_inline() {
	global $script,$vars,$wiki_user_dir;
	$prmcnt = func_num_args();
	if ($prmcnt < 2)
	{
		return "";
	}
	$prms = func_get_args();
	$body = array_pop($prms);
	switch ($prms[0]) {
		case "__AUTHOR" :
			$body = convert_html(sprintf($wiki_user_dir,$body),false,false);
			$body = preg_replace("/^<p>(.*)<\/p>$/ms","\\1",$body);
			break;
		case "__TIMESTAMP" :
			$body = date("YÇ¯m·îdÆü H»þiÊ¬sÉÃ",$body);
			break;
		case "__CATEGORY" :
			$body = convert_html("[[Category {$prms[1]}>{$prms[1]}]]:",false,false);
			$body = preg_replace("/^<p>(.*)<\/p>$/ms","\\1",$body);
			break;
		case "__SUBJECT" :
			$page = strip_bracket($vars['page']);
			$body = convert_html("[[$body>$page]]",false,false);
			$body = preg_replace("/^<p>(.*)<\/p>$/ms","\\1",$body);
			break;
		case "__EDIT":
			if (!is_freeze($page,FALSE)) {
				$_page = preg_replace("/(.*\/)?([0-9\-]+)$/","\\2",strip_bracket($vars['page']));
				$body = "<a href=\"$script?plugin=weblog&mode=edit&conf={$prms[1]}&page_name=$_page\">";
				$body .= "<img src=\"image/edit.png\" alt=\"Edit\" title=\"Edit\" /></a>";
			}
			break;
		default :
	}
	return $body;
}

function plugin_weblog_field_convert() {
	$prmcnt = func_num_args();
	if ($prmcnt < 2)
	{
		return "";
	}
	$prms = func_get_args();
	return "";
}
?>
