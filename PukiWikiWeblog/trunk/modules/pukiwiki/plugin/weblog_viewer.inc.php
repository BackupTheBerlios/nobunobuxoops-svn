<?php
/*
 * PukiWiki weblog_viewer¥×¥é¥°¥¤¥ó
 *
 *$Id$
 *
 */
/**
 *³µÍ×
  weblog¥×¥é¥°¥¤¥ó¤ÇºîÀ®¤·¤¿¥Ú¡¼¥¸¤ò°ìÍ÷É½¼¨¤¹¤ë¤¿¤á¤Î¥×¥é¥°¥¤¥ó¤Ç¤¹¡£
  -  calendar_viewer¥×¥é¥°¥¤¥ó¤ò¸µ¤ËºîÀ®
 *¹¹¿·ÍúÎò
 *»È¤¤Êý
  /// #weblog_viewer(pagename,(yyyy-mm|n|this),[mode])
 **pagename
  weblog¥×¥é¥°¥¤¥ó¤òµ­½Ò¤·¤Æ¤ë¥Ú¡¼¥¸Ì¾
 **(yyyy-mm|n|this)
  -yyyy-mm
  --yyyy-mm¤Ç»ØÄê¤·¤¿Ç¯·î¤Î¥Ú¡¼¥¸¤ò°ìÍ÷É½¼¨
  -n
  --n·ï¤Î°ìÍ÷É½¼¨
  -this
  --º£·î¤Î¥Ú¡¼¥¸¤ò°ìÍ÷É½¼¨
  **[mode]
  ¾ÊÎ¬²ÄÇ½¤Ç¤¹¡£¾ÊÎ¬»þ¤Î¥Ç¥Õ¥©¥ë¥È¤Ïpast
  -past
  --º£Æü°ÊÁ°¤Î¥Ú¡¼¥¸¤Î°ìÍ÷É½¼¨¥â¡¼¥É¡£¹¹¿·ÍúÎò¤äÆüµ­¸þ¤­
  -future
  --º£Æü°Ê¹ß¤Î¥Ú¡¼¥¸¤Î°ìÍ÷É½¼¨¥â¡¼¥É¡£¥¤¥Ù¥ó¥ÈÍ½Äê¤ä¥¹¥±¥¸¥å¡¼¥ë¸þ¤­
  -view
  --²áµî¤«¤éÌ¤Íè¤Ø¤Î°ìÍ÷É½¼¨¥â¡¼¥É¡£É½¼¨ÍÞ»ß¤¹¤ë¥Ú¡¼¥¸¤Ï¤¢¤ê¤Þ¤»¤ó¡£

 */
///////////////////////////////////////////////////////////

require_once "weblog_common.inc.php";

// initialize variables
function plugin_weblog_viewer_init() {
	global $_weblog_msgs, $options;

	//¥á¥Ã¥»¡¼¥¸¤ÎÀßÄê
	if (count($_weblog_msgs) == 0) {
		weblog_msg_init();
	}
	//¥³¥ó¥Õ¥£¥°¤Î¼èÆÀ(default)
	$options = array();
	$options = weblog_get_options("default",$options);
}


function plugin_weblog_viewer_convert()
{
	global $WikiName,$BracketName,$vars,$get,$post,$hr,$script,$trackback;
	global $anon_writable,$wiki_user_dir;
	global $comment_no,$h_excerpt,$digest;
	global $options, $_weblog_msgs,$_msg_week;
	
	//*°ú¿ô¤Î³ÎÇ§
	if(func_num_args()>=2){
		$func_vars_array = func_get_args();
		$params = call_user_func_array("plugin_weblog_viewer_check_args",$func_vars_array);
		if (count($params) <=1) {
			return "[weblog_viewer]:{$_weblog_msgs['err_msg_arg2']}";
		}
	} else {
		return "[weblog_viewer]:{$_weblog_msgs['err_msg_noargs']}";
	}
	
	foreach($params as $param_key=>$param_val) {
		$$param_key = $param_val;
	}
	$conf_name = $params['conf_name'];
	$options = weblog_get_options($conf_name,$options);
	
	if (count($options) == 0) {
		return "[weblog_viewer]:".sprintf($_weblog_msgs['err_msg_noconf'],$conf_name);
	}

	$pagename = strip_bracket($options['PREFIX']);
	
	//*°ìÍ÷É½¼¨¤¹¤ë¥Ú¡¼¥¸Ì¾¤È¥Õ¥¡¥¤¥ëÌ¾¤Î¥Ñ¥¿¡¼¥ó¡¡¥Õ¥¡¥¤¥ëÌ¾¤Ë¤ÏÇ¯·î¤ò´Þ¤à
	if ($pagename == ""){
		//pagenameÌµ¤·¤Îyyyy-mm-dd¤ËÂÐ±þ¤¹¤ë¤¿¤á¤Î½èÍý
		$pagepattern = "";
		$pagepattern_len = 0;
		$filepattern = $page_YM;
		$filepattern_len = strlen($filepattern);
	}else{
		$pagepattern = strip_bracket($pagename) .'/';
		$pagepattern_len = strlen($pagepattern);
		$filepattern = $pagepattern.$page_YM;
		$filepattern_len = strlen($filepattern);
	}

	//*¥Ú¡¼¥¸¥ê¥¹¥È¤Î¼èÆÀ
	$pagelist = array();
	$datelength = 10;
	foreach(get_existpages_db(false,$filepattern) as $page) {
		//$page¤¬¥«¥ì¥ó¥À¡¼·Á¼°¤Ê¤Î¤«¥Á¥§¥Ã¥¯ ¥Ç¥Õ¥©¥ë¥È¤Ç¤Ï¡¢ yyyy-mm-dd-HHMMSS-([1-9])?
		$page = strip_bracket($page);
		if (plugin_weblog_viewer_isValidDate(substr($page,$pagepattern_len)) == false) continue;
		//*modeËè¤ËÊÌ¾ò·ï¤Ç¤Ï¤¸¤¯
		//past mode¤Ç¤ÏÌ¤Íè¤Î¥Ú¡¼¥¸¤ÏNG
		if (((substr($page,$pagepattern_len,$datelength)) > date("Y-m-d"))&&($mode=="past") )continue;
		//future mode¤Ç¤Ï²áµî¤Î¥Ú¡¼¥¸¤ÏNG
		if (((substr($page,$pagepattern_len,$datelength)) < date("Y-m-d"))&&($mode=="future") )continue;
		//view mode¤Ê¤éall OK
		if (strlen(substr($page,$pagepattern_len)) == $datelength) {
			$pagelist[] = $page."--";
		} else {
			$pagelist[] = $page;
		}
	}

	//¥Ê¥Ó¥Ð¡¼ºîÀ®¤³¤³¤«¤é
	$enc_pagename = rawurlencode(substr($pagepattern,0,$pagepattern_len -1));

	if ($page_YM != ""){
		//Ç¯·îÉ½¼¨»þ
		$this_year = substr($page_YM,0,4);
		$this_month = substr($page_YM,5,2);
		//¼¡·î
		$next_year = $this_year;
		$next_month = $this_month + 1;
		if ($next_month >12){
			$next_year ++;
			$next_month = 1;
		}
		$next_YM_T = $next_YM = sprintf("%04d-%02d",$next_year,$next_month);

		//Á°·î
		$prev_year = $this_year;
		$prev_month = $this_month -1;
		if ($prev_month < 1){
			$prev_year --;
			$prev_month = 12;
		}
		$prev_YM_T = $prev_YM = sprintf("%04d-%02d",$prev_year,$prev_month);
		if ($cal2 == 1 ) {
			$prev_YM = sprintf("%04d%02d",$prev_year,$prev_month);
		}

		if ($mode == "past"){
			$right_YM = $prev_YM;
			$right_text = $prev_YM_T."&gt;&gt;";
			$left_YM = $next_YM;
			$left_text = "&lt;&lt;".$next_YM_T;
		}else{
			$left_YM = $prev_YM;
			$left_text = "&lt;&lt;".$prev_YM_T;
			$right_YM = $next_YM;
			$right_text = $next_YM_T."&gt;&gt;";
		}
	}else{
		//n·ïÉ½¼¨»þ
		if ($limit_base+$limit_pitch >= count($pagelist)){
			$right_YM = "";
		}else{
			$right_base = $limit_base + $limit_pitch;
			$right_YM = $right_base ."*".$limit_pitch;
			$right_text = "¼¡¤Î".$limit_pitch."·ï&gt;&gt;";
		}
		$left_base  = $limit_base - $limit_pitch;
		if ($left_base >= 0) {
			$left_YM = $left_base . "*" . $limit_pitch;
			$left_text = "&lt;&lt;Á°¤Î".$limit_pitch."·ï";
		}else{
			$left_YM = "";
		}
	}
	//¥ê¥ó¥¯ºîÀ®
	$_conf = rawurlencode($conf_name);
	if ($left_YM != ""){
		$left_link = "<a href=\"". $script."?plugin=weblog_viewer&amp;conf=".$_conf."&amp;date=".$left_YM ."&amp;mode=".$mode."\">".$left_text."</a>";
	}else{
		$left_link = "";
	}
	if ($right_YM != ""){
		$right_link = "<a href=\"". $script."?plugin=weblog_viewer&amp;conf=".$_conf."&amp;date=".$right_YM ."&amp;mode=".$mode."\">".$right_text."</a>";
	}else {
		$right_link = "";
	}

	//past mode¤Ï<<¿· µì>> Â¾¤Ï<<µì ¿·>>
	$pageurl = $script."?".rawurlencode("[[".strip_bracket($pagename)."]]");
	$navi_bar .= "<table width =\"100%\" class=\"style_calendar_navi\"><tr><td align=\"left\" width=\"33%\">";
	$navi_bar .= $left_link;
	$navi_bar .= "</td><td align=\"center\" width=\"34%\">";
	$navi_bar .= make_pagelink($pagename,$options['NAME']);
	$navi_bar .= "</td><td align=\"right\" width=\"33%\">";
	$navi_bar .= $right_link;
	$navi_bar .= "</td></tr></table>";
  
	//¥Ê¥Ó¥Ð¡¼ºîÀ®¤³¤³¤Þ¤Ç

	//*¤³¤³¤«¤é¥¤¥ó¥¯¥ë¡¼¥É³«»Ï

	//ÊÑ¿ôÃÍÂàÈò
	$tmppage = $vars["page"];
	$_comment_no = $comment_no;
	$_h_excerpt = $h_excerpt;
	$_digest = $digest;

	//$tmp_related = $related;
	$return_body = "";

	//¥Ê¥Ó¥Ð¡¼
	$return_body .= $navi_bar;

	//¤Þ¤º¥½¡¼¥È
	if ($mode == "past"){
		//past mode¤Ç¤Ï¿·¢ªµì
		rsort ($pagelist);
	}else {
		//view mode ¤È future mode ¤Ç¤Ï¡¢µì¢ª¿·
		sort ($pagelist);
	}

	//$limit_page¤Î·ï¿ô¤Þ¤Ç¥¤¥ó¥¯¥ë¡¼¥É
	
	$template_sources = weblog_load_template($conf_name,"list");
	if ($template_sources==FALSE) {
		return "[weblog_viewer]:".sprintf($_weblog_msgs['err_msg_notemplate'],"$conf_name/list");
	}
	$tmp = $limit_base;
	$kensu = 0;
	$date_str = "";
	while ($tmp < $limit_page){
		if (!isset($pagelist[$tmp])) break;
		$pagelist[$tmp] = preg_replace("/{-}-$/","",$pagelist[$tmp]);
		$page = "[[" . $pagelist[$tmp] .  "]]";

		$vars["page"] = $post["page"] = $get["page"] = $page;
		//comment_no ½é´ü²½
		$comment_no = 0;
		$weblog_content='';
		$content_flg = FALSE;
		$sources = get_source($page);
		$src = "";
		foreach ($sources as $source) {
			$src .= "$source";
		}
		$sources = $src;
		$w_author = "";
		if (preg_match("/(\&weblog_field\(__AUTHOR\)\{[^}]*\}\;)/mS",$sources,$match)) {
			$w_author = $match[1];
		}
		$w_timestamp = "";
		if (preg_match("/(\&weblog_field\(__TIMESTAMP\)\{[0-9]+\}\;)/m",$sources,$match)) {
			$w_timestamp = $match[1];
		}
		$w_subject = "";
		if (preg_match("/(\&weblog_field\(__SUBJECT\)\{[^}]+\}\;)/m",$sources,$match)) {
			$w_subject = $match[1];
		}
		$w_category = "";
		if (preg_match("/(\&weblog_field\(__CATEGORY,[^\)]+\)\{[^}]+\}\;((\[ )?\[\[.*\]\]( \])?)+)/m",$sources,$match)) {
			$w_category = $match[1];
		}
		$w_edit = "";
		if (preg_match("/(\&weblog_field\(__EDIT\)\{[^}]+\}\;)/m",$sources,$match)) {
			$w_edit = $match[1];
		}
		$w_body = "";
		if (preg_match("/(#weblog_field\(__BODY\,Start\)\s*\n.*\n#weblog_field\(__BODY\,End\)\n)/ms",$sources,$match)) {
			$w_body = $match[1];
		}
		$cmt_page = sprintf(strip_bracket($options['COMMENT_PREFIX']),strip_bracket($page));
		$count_of_page = plugin_weblog_viewer_comment_count($cmt_page);
		if(!$count_of_page) {
			$w_comment = "[ [[". $_weblog_msgs['lbl_comment']."(0)>".$page."]] ]";
		} else {
			$w_comment = "[ [[". $_weblog_msgs['lbl_comment']."($count_of_page)>".$cmt_page."]] ]";
		}
		$w_trackback = "";
		if  ($trackback) {
			//[[name>URL]]¤Ã¤Æ»È¤¤Êý¤ÏÀµ¤·¤¤¤Î¤«¤Ê¤¡¡©ËÜÍè¤Ï[[name:URL]]¤À¤±¤É¤³¤Î¾ì¹ç¤ÏÊÌWindowÉ½¼¨¤Ë¤Ê¤ë¤·¡¦¡¦¡¦
			//¤È¤ê¤¢¤¨¤º¡¢·ë²Ì¥ª¡¼¥é¥¤¤Ç»È¤Ã¤Æ¤ß¤è¤¦¡£
			$w_trackback = "[ [[TracBack(".tb_count($page).")>$script?plugin=tb&__mode=view&tb_id=".tb_get_id($page)."]] ]";
		}

		$w_sources = $template_sources;
		$w_sources = preg_replace("/\[__SUBJECT\]/",$w_subject,$w_sources);
		$w_sources = preg_replace("/\[__AUTHOR\]/",$w_author,$w_sources);
		$w_sources = preg_replace("/\[__TIMESTAMP\]/",$w_timestamp,$w_sources);
		$w_sources = preg_replace("/\[__BODY\]/",$w_body,$w_sources);
		$w_sources = preg_replace("/\[__CATEGORY\]/",$w_category,$w_sources);
		$w_sources = preg_replace("/\[__COMMENTCOUNT\]/",$w_comment,$w_sources);
		$w_sources = preg_replace("/\[__TRACKBACKCOUNT\]/",$w_trackback,$w_sources);
		$weblog_content = convert_html($w_sources,false,false);

		if (!is_freeze($page,FALSE)) {
			$_page = preg_replace("/(.*\/)?([0-9\-]+)$/","\\2",strip_bracket($vars['page']));
			$edit_tag = "<a href=\"$script?plugin=weblog&mode=edit&conf=$conf_name&page_name=$_page\">";
			$edit_tag .= "<img src=\"image/edit.png\" alt=\"Edit\" title=\"Edit\" /></a>";
			$weblog_content = preg_replace("/\[__EDIT\]/",$edit_tag,$weblog_content);
		} else {
			$weblog_content = preg_replace("/\[__EDIT\]/","",$weblog_content);
		}
		$body = "<div class=\"style_calendar_body\">".$weblog_content."</div>";

		if (ereg("([0-9]{4})-([0-9]{2})-([0-9]{2})-([0-9]{3})",$page,$match)) {
			$date0 = mktime(0,0,0,$match[2],$match[3],$match[1]);
			$date_str0 = date($_weblog_msgs['fmt_fullday'],$date0);
		}	$date_str0.="(".$_msg_week[date("w",$date0)].")";

		if ($date_str != $date_str0) {
			$date_str = $date_str0;
			if (ereg("(.*[0-9]{4}-[0-9]{2}-[0-9]{2})",strip_bracket($page),$match)) {
				$day_page= ":Weblog/Daily/".$match[1];
			}
		    $head = "<div class = \"style_calendar_date\">".make_pagelink($day_page,$date_str)."</div>\n";
		} else {
			$head = "";
		}
		$return_body .= $head . $body;
		$tmp++;
		$kensu++;
	}

	//É½¼¨¥Ç¡¼¥¿¤¬¤¢¤Ã¤¿¤é¥Ê¥Ó¥Ð¡¼É½¼¨
	if ($kensu) $return_body .= $navi_bar;

	$vars["page"] = $post["page"] = $get["page"] = $tmppage;
	$comment_no = $_comment_no;
	$h_excerpt = $_h_excerpt;
	$digest = $_digest;

	return $return_body;
}

function plugin_weblog_viewer_action(){
	global $WikiName,$BracketName,$vars,$get,$post,$hr,$script;

	$return_vars_array = array();

	$conf_name = $vars["conf"];

	$page_YM = $vars["date"];
	if ($page_YM == ""){
	    $page_YM = date("Y-m");
	}
	$mode = $vars["mode"];

	$args_array = array($conf_name, $page_YM,$mode);
	$return_vars_array["body"] = call_user_func_array("plugin_weblog_viewer_convert",$args_array);

	$return_vars_array["msg"] = "weblog_viewer ".htmlspecialchars($vars["page"]);
	if ($vars["page"] != ""){
		$return_vars_array["msg"] .= "/";
	}
	if(preg_match("/\*/",$page_YM)){
	    //¤¦¡¼¤ó¡¢n·ïÉ½¼¨¤Î»þ¤Ï¤Ê¤ó¤Æ¥Ú¡¼¥¸Ì¾¤Ë¤·¤¿¤é¤¤¤¤¡©
	}else{
		$return_vars_array["msg"] .= htmlspecialchars($page_YM);
	}

	$vars['page'] = $page;
	return $return_vars_array;
}

function plugin_weblog_viewer_isValidDate($aStr) {
	if( ereg("^([0-9]{4})-([0-9]{2})-([0-9]{2})-([0-9]{6}).*$", $aStr, $m) ) {
		return checkdate($m[2], $m[3], $m[1]);
	}
	return false;
}

function plugin_weblog_viewer_comment_count($page_count)
{
	$count_line = 0;
	$page_source = get_source($page_count);
	foreach($page_source as $line) {
		if(substr($line,0,10) == "-&areaedit"){
			$count_line++;
		}
	}
	return $count_line;
}

function plugin_weblog_viewer_check_args()
{
	$func_vars_array = func_get_args();
	$params = array();
	$_options = array();
	foreach($func_vars_array as $option) {
		$_options[] = $option;
	}
	$func_vars_array = $_options;
	unset($_options,$option);

	$params['conf_name'] = $func_vars_array[0];;

	if (preg_match("/[0-9]{4}-[0-9]{2}/",$func_vars_array[1])) {
		//»ØÄêÇ¯·î¤Î°ìÍ÷É½¼¨
		$params['page_YM'] = $func_vars_array[1];
		$params['limit_base'] = 0;
		$params['limit_page'] = 310;	//¼êÈ´¤­¡£31ÆüÊ¬¡ß10¥Ú¡¼¥¸¤ò¥ê¥ß¥Ã¥È¤È¤¹¤ë¡£
	} else if (preg_match("/this/si",$func_vars_array[1])) {
		//º£·î¤Î°ìÍ÷É½¼¨
		$params['page_YM'] = date("Y-m");
		$params['limit_base'] = 0;
		$params['limit_page'] = 310;
	} else if (preg_match("/^[0-9]+$/",$func_vars_array[1])) {
		//nÆüÊ¬É½¼¨
		$params['page_YM'] = "";
		$params['limit_base'] = 0;
		$params['limit_pitch'] = $func_vars_array[1];
		$params['limit_page'] = $params['limit_pitch'];
	} else if (preg_match("/([0-9]+)\*([0-9]+)/",$func_vars_array[1],$reg_array)) {
		$params['page_YM'] = "";
		$params['limit_base'] = $reg_array[1];
		$params['limit_pitch'] = $reg_array[2];
		$params['limit_page'] = $reg_array[1] + $params['limit_pitch'];
	} else {
		return Array();
	}
	if (isset($func_vars_array[2])&&preg_match("/past|view|future/si",$func_vars_array[2])) {
		$params['mode'] = $func_vars_array[2];
	}
	return $params;
}
/*
 * $Log$
 * Revision 1.5  2004/02/25 08:56:46  nobu
 * CVS—p$Log‚Ì‰ß‹Ž•ª—š—ð‚Ì•âŠ®
 *
 * Revision 1.4  2004/02/25 08:37:14  nobu
 * CVSÍÑ¥­¡¼¥ï¡¼¥ÉÄÉ²Ã¡Ê$IdµÚ¤Ó$Log)
 *
 * Revision 1.3  2004/02/25 05:44:11  nobu
 * -ÉÔÍ×¤Ê¥³¥á¥ó¥È¹Ô¤òºï½ü
 *
 * Revision 1.2  2004/02/25 05:28:45  nobu
 * weblog_viewer¥×¥é¥°¥¤¥ó¤Ç»ÈÍÑ¤·¤Æ¤¤¤¿ÆÈ¼«¤Î¥¹¥¿¥¤¥ë¤ÎÄêµÁ¤òcalendar¤Èk¶¦ÍÑ¤¹¤ë¤è¤¦¤ËÊÑ¹¹¡£
 *
 * Revision 1.1  2004/02/25 05:27:40   nobu
 * PukiWikiModÍÑweblog¥×¥é¥°¥¤¥ó½é´ü¥ê¥ê¡¼¥¹
 *
 */
?>
