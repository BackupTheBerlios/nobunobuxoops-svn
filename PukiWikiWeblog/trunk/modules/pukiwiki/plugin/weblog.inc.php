<?php
/*
 * PukiWiki weblog¥×¥é¥°¥¤¥ó
 *
 * $Id$
 *
 * CopyRight 2004 nobunobu (nobunobu at www.kowa.org)
 * http://www.kowa.org/
 *
 *Original
 * CopyRight 2003 Chung-Yen Chang (candyz at kandix.idv.tw)
 * http://pukiwiki.kandix.idv.tw/
 *
 */
/**
 *Usage:
 	#weblog
	#weblog(param1)
		param1 = config
*/
///////////////////////////////////////////////////////////

require_once "weblog_common.inc.php";

function plugin_weblog_init() {
	global $_weblog_msgs, $options;

	//¥á¥Ã¥»¡¼¥¸¤ÎÀßÄê
	if (count($_weblog_msgs) == 0) {
		weblog_msg_init();
	}
	//¥³¥ó¥Õ¥£¥°¤Î¼èÆÀ(default)
	$options = array();
	$options = weblog_get_options("default",$options);
}
function plugin_weblog_action()
{
	global $vars;
	
	if ($vars["mode"] == "edit") {
		//µ­»ö¤ÎÊÔ½¸¥Õ¥©¡¼¥àÉ½¼¨
		return plugin_weblog_action_edit();
	} else if ($vars["mode"] == "new" || $vars["mode"] == "save") {
		//¥Õ¥©¡¼¥à¤è¤êÅê¹Æ¤µ¤ì¤¿µ­»ö¤ÎÊÝÂ¸
		return plugin_weblog_action_save();
	} else {
		//¿·µ¬Åê¹ÆÍÑ¥Õ¥©¡¼¥à¤ÎÉ½¼¨
		return plugin_weblog_action_new();
	}
}
function plugin_weblog_action_save()
{
	global $script,$post,$vars;
    global $X_uid,$wiki_user_dir,$no_name,$_msg_week;
	global $options, $_weblog_msgs;

	//$vars['page']¤ÎÂàÈò
	//  ÅÓÃæ¤ÇÊÌ¥Õ¥¡¥¤¥ë½ñ¤­¹þ¤ß»þ¤ËÊÑ¹¹¤¹¤ë¤¬¡¢¸å¤ÇÌá¤¹É¬Í×¤¬¤¢¤ë¡£
	$page_orig = $vars['page'];
	$mode = $vars['mode'];
	$old_page = "";
	if ($mode == "new") {
		//¿·µ¬ºîÀ®»þ¡¢¸½ºßÆü»þ¤Î¼èÆÀ
		$timestamp=time();
	} else {
		//ÊÔ½¸»þ¡¢ÂÐ¾Ý¥Ú¡¼¥¸Ì¾¤è¤ê¼èÆÀ
		$_page = $vars['page_name'];
		if ($post['update_stamp']==1) {
			$old_page = $_page;
			$timestamp=time();
			$mode="renew";
		} else {
			$t_year = substr($_page,0,4);
			$t_month = substr($_page,5,2);
			$t_day = substr($_page,8,2);
			$t_hour = substr($_page,11,2);
			$t_min = substr($_page,13,2);
			$t_sec = substr($_page,15,2);
			$timestamp=mktime($t_hour,$t_min,$t_sec,$t_month,$t_day,$t_year);
		}
	}
	
	//¥³¥ó¥Õ¥£¥°¤ÎÆÉ¤ß¹þ¤ß(»ØÄêweblog¸ÇÍ­)
	$conf_name = $vars['config'];
	$options = weblog_get_options($conf_name,$options);
	
	$prefix = strip_bracket($options['PREFIX']);
	
	//¥Ú¡¼¥¸Ì¾¤ÎÀßÄê
	if (($mode == "new")||($mode == "renew")) {
		//¿·µ¬ºîÀ®»þ¤Ë¤Ï¡¢prefix/YYYYMMDD-HHMMSS ¤Î·Á¼°¤ÇºîÀ®¡Ê½ÅÊ£»þ¤Ï -nn ¤Î·Á¼°¤ÇSuffix)
		$i = 0;
		$postdaytime_str = date("Y-m-d-His",$timestamp);
		$_page = $postdaytime_str;
		while(is_page("$prefix/$_page")) {
			$i++;
			$_page = $postdaytime_str."-".sprintf("%02d",$i);
		}
	}
	$page = "$prefix/$_page";
	//ËÜÊ¸¤ÎÍ­Ìµ¤Î¥Á¥§¥Ã¥¯
	if ($post['body'] == '') {
		list($vars['page'],$vars['refer']) = weblog_set_return($page_orig,$prefix);
		return array('msg'=>"<p><strong>{$_weblog_msgs['err_msg_nomsg']}</strong></p>\n",'body'=>'');
	}
	
	//¸¢¸Â¤Î¥Á¥§¥Ã¥¯
	if ($mode == "new") {
		//¿·µ¬ºîÀ®»þ¤Ë¤Ï¡¢¿Æ¥Ú¡¼¥¸¤Î¸¢¸Â¤ò¸µ¤ËÈ½ÃÇ
		if (!edit_auth($prefix,FALSE,FALSE)) {
			list($vars['page'],$vars['refer']) = weblog_set_return($page_orig,$prefix);
			return array('msg'=>"<p><strong>{$_weblog_msgs['err_msg_noauth']}</strong></p>\n",'body'=>'');
		}
	} else if($mode == "renew") {
		//½ñ´¹»þ¤Ë¤Ï¡¢¸µ¥Ú¡¼¥¸¤Î¸¢¸Â¤ò¸µ¤ËÈ½ÃÇ
		if (!edit_auth($old_page,FALSE,FALSE)) {
			list($vars['page'],$vars['refer']) = weblog_set_return($page_orig,$prefix);
			return array('msg'=>"<p><strong>{$_weblog_msgs['err_msg_noauth']}</strong></p>\n",'body'=>'');
		}
	} else {
		//ÊÔ½¸»þ¤Ë¤Ï¡¢ÂÐ¾Ý¥Ú¡¼¥¸¤Î¸¢¸Â¤ò¸µ¤ËÈ½ÃÇ
		if (!edit_auth($page,FALSE,FALSE)) {
			list($vars['page'],$vars['refer']) = weblog_set_return($page_orig,$prefix);
			return array('msg'=>"<p><strong>{$_weblog_msgs['err_msg_noauth']}</strong></p>\n",'body'=>'');
		}
	}

	//Åê¹Æ¼ÔµÚ¤Ó¸¢¸ÂÀßÄê
	$author = ($post['author'] == '') ? $_weblog_msgs['no_name'] : $post['author'];
	if ($X_uid == 0) {
		$contents_auth = "// author:0\n";
		$tmpl_val['__AUTHOR'] = "&weblog_field(__AUTHOR){".$no_name."(".$author.")};";
	} else {
        $contents_auth = "#freeze	uid:$X_uid	aid:0	gid:0\n// author:".$X_uid."\n";
		$tmpl_val['__AUTHOR'] = "&weblog_field(__AUTHOR){".$author."};";
	}

	//Åê¹ÆÆü»þ
	$tmpl_val['__TIMESTAMP'] = "&weblog_field(__TIMESTAMP){{$timestamp}};";
	
	//·ïÌ¾
	$subject = ($post['subject'] == '') ? $_weblog_msgs['no_subject'] : $post['subject'];
	$tmpl_val['__SUBJECT'] = "&weblog_field(__SUBJECT){{$subject}};";
	
	//¥«¥Æ¥´¥ê¡¼
	if ($post['category'] == ''){
		$category = $options['DEFAULT_CATEGORY'];
	} else {
		$category = $post['category'];
	}
	$catprefix = sprintf(strip_bracket($options['CATEGORY_PREFIX']),$prefix);
	$catpage = "$catprefix/$category";
	
	$tmpl_val['__CATEGORY'] = plugin_weblog_category_maketag($catprefix,$category);
	
	//ËÜÊ¸
	$body = rtrim($post['body']);
	$body = preg_replace("/\s*((\x0D\x0A)|(\x0D)|(\x0A))/", "\n", $body);
	$body = rep_for_pre($body);
	//¼«Æ°²þ¹Ô½èÍý
	if ($post['auto_br']) {
		$body = auto_br($body);
	}
	$tmpl_val['__BODY'] = "#weblog_field(__BODY,Start)\n$body\n\n#weblog_field(__BODY,End)\n\n";
	
	//¥³¥á¥ó¥È
	if ($post['allow_comment']) {
		$comment_prefix = sprintf(strip_bracket($options['COMMENT_PREFIX']),$prefix);
		$tmpl_val['__COMMENT'] = "#pcomment(".add_bracket($comment_prefix."/$_page").",10,above)\n";
	} else {
		$tmpl_val['__COMMENT'] =  $_weblog_msgs['message_disable_comment'];
	}
	//EDIT¥Ü¥¿¥ó
	$tmpl_val['__EDIT'] = "&weblog_field(__EDIT,$conf_name);";
	
	//PING
	$tmpl_val['__PING'] = array("([^\]]*)","#ping(\\1)");

	//  (Write WebLog Article to an individual file)
	//¥Æ¥ó¥×¥ì¡¼¥È(page)¤«¤é¤ÎÆÉ¹þ
	$contents = weblog_load_template($conf_name,"page");
	//¥Õ¥£¡¼¥ë¥É¤ÎÃÖ´¹
	$contents = weblog_assign_value($contents,$tmpl_val);
	//¥Ú¡¼¥¸¤Î½ñ¹þ
	$vars['page'] = $page;
	if ($mode == "new") {
		page_write($page,$contents_auth.$contents);
	} else if ($mode == "renew"){
		page_write($page,$contents_auth.$contents);
		//¸Å¤¤¥Õ¥¡¥¤¥ë¤òºï½ü¤·£Ä£Â´Ø·¸¤ò¹¹¿·
		$old_page = add_bracket("$prefix/$old_page");
		@unlink(DATA_DIR.$dir.encode($old_page).".txt");
		is_page($old_page,true);
		links_update($old_page);
		pginfo_db_write($old_page,"delete");
		delete_page_html($old_page);
	} else {
		page_write($page,$contents_auth.$contents,true);
	}
	$postmonth = date("Y-m",$timestamp);
	$postmonth_str = date($_weblog_msgs['fmt_month'],$timestamp);
	$postday = date("Y-m-d",$timestamp);
	$postday_str=date($_weblog_msgs['fmt_day'],$timestamp);
	//Åö·î¤Î¥¤¥ó¥Ç¥Ã¥¯¥¹¥Ú¡¼¥¸Ì¾¼èÆÀ
	$monthpage=sprintf(strip_bracket($options['MONTHLY_PREFIX']),$prefix)."/$postmonth";
	//ÅöÆü¤Î¥¤¥ó¥Ç¥Ã¥¯¥¹¥Ú¡¼¥¸Ì¾¼èÆÀ
	$daypage=sprintf(strip_bracket($options['DAILY_PREFIX']),$prefix)."/$postday";
	
	$tmpl_val['__CONF'] = $conf_name;
	$tmpl_val['__BASELINK'] = "[[$prefix]]";
	$tmpl_val['__MONTH'] = $postmonth;
	$tmpl_val['__MONTHNAME'] = $postmonth_str;
	$tmpl_val['__MONTHLINK'] = "[[$postmonth_str>$monthpage]]";
	$tmpl_val['__DAY'] = $postday;
	$tmpl_val['__DAYNAME'] = $postday_str."(".$_msg_week[date("w",$timestamp)].")";

	//·îÊÌ¥¤¥ó¥Ç¥Ã¥¯¥¹¤ÎºîÀ®
	//·îÊÌ¥¤¥ó¥Ç¥Ã¥¯¥¹¤¬Â¸ºß¤·¤Ê¤¤¤È¤­¤Î¤ßºîÀ®¤¹¤ë¡£
	if (!is_page($monthpage)) {
		//¸¢¸ÂÀßÄê
		$monthly_auth = "#freeze\tuid:1\taid:0\tgid:0\n// author:1\n";	
		//¥Æ¥ó¥×¥ì¡¼¥È(pageMonthly)¤«¤é¤ÎÆÉ¹þ
		$monthly_body = weblog_load_template($conf_name,"pageMonthly");
		//¥Õ¥£¡¼¥ë¥É¤ÎÃÖ´¹
		$monthly_body = weblog_assign_value($monthly_body,$tmpl_val);
		//¥Ú¡¼¥¸¤Î½ñ¹þ
		$vars['page'] = $monthpage;
		page_write($monthpage,$monthly_auth.$monthly_body,$notimestamp=FALSE);
	}
	//ÆüÊÌ¥¤¥ó¥Ç¥Ã¥¯¥¹¤ÎºîÀ®
	//ÆüÊÌ¥¤¥ó¥Ç¥Ã¥¯¥¹¤¬Â¸ºß¤·¤Ê¤¤¤È¤­¤Î¤ßºîÀ®¤¹¤ë¡£
	if (!is_page($daypage)) {
		//¸¢¸ÂÀßÄê
		$daily_auth = "#freeze\tuid:1\taid:0\tgid:0\n// author:1\n";
		//¥Æ¥ó¥×¥ì¡¼¥È(pageDaily)¤«¤é¤ÎÆÉ¹þ
		$daily_body = weblog_load_template($conf_name,"pageDaily");
		//¥Õ¥£¡¼¥ë¥É¤ÎÃÖ´¹
		$daily_body = weblog_assign_value($daily_body,$tmpl_val);
		//¥Ú¡¼¥¸¤Î½ñ¹þ
		$vars['page'] = $daypage;
		page_write($daypage,$daily_auth.$daily_body,$notimestamp=FALSE);
	}
	//¥È¥é¥Ã¥¯¥Ð¥Ã¥¯ÍÑPING¤ÎÁ÷¿®
	$retmsg = "";
	if (file_exists(CACHE_DIR.encode(strip_bracket($page)).".tbf")) {
		$r_page = rawurlencode(strip_bracket($page));
		$retmsg = $_weblog_msgs['message_ping']."<img style=\"float:left\" src=\"".XOOPS_URL."/modules/pukiwiki/ping.php?$r_page\" width=1 height=1/> </br>";
	}
	if ($vars['popup'] != "true") {
		//Àµ¾ï½ªÎ»¤Î¥á¥Ã¥»¡¼¥¸½ÐÎÏ
		list($vars['page'],$vars['refer']) = weblog_set_return($page_orig,$prefix);
		return array('msg'=>"$retmsg{$_weblog_msgs['message_sent']}\n",'body'=>'');
	} else {
		echo <<< EOD
<link rel="stylesheet" href="skin/default.ja.css" type="text/css" media="screen" charset="shift_jis">
<link rel="stylesheet" href="cache/css.css" type="text/css" media="screen" charset="shift_jis">
</head>
<body>
$retmsg{$_weblog_msgs['message_sent_complete']}
</body>
</html>
EOD;
		exit;
	}
}

function plugin_weblog_action_new()
{
	global $vars;
	global $options, $_weblog_msgs;

	$conf_name = $vars['conf'];
	$subject = weblog_conv_knj_escape($vars['subject']);
	$body = weblog_conv_knj_escape($vars['body']);
	$category = $vars['category'];
	$options = weblog_get_options($conf_name,$options);
	$weblog_name = $options['NAME'];
	$prefix = strip_bracket($options['PREFIX']);
	if (!edit_auth($prefix,FALSE,FALSE)) {
		list($vars['page'],$vars['refer']) = weblog_set_return($vars['page'],$prefix);
		return array('msg'=>"<p><strong>{$_weblog_msgs['err_msg_noauth']}</strong></p>\n",'body'=>'');
	}
	$body = plugin_weblog_make_form($conf_name,"new","",$subject,$body,$category,'',$vars['popup']);
	$title = sprintf($_weblog_msgs['lbl_new_title'],$weblog_name);
	if ($vars['popup']=='true') {
		echo <<<EOD
<html>
<head>
<title>$title</title>
<link rel="stylesheet" href="skin/default.ja.css" type="text/css" media="screen" charset="shift_jis">
<link rel="stylesheet" href="cache/css.css" type="text/css" media="screen" charset="shift_jis">
</head>
<body>
<h3>$title</h3>
$body
</body>
</html>
EOD;
		exit;
	}
	return array(
		'msg' => $title,
		'body' => $body,
	);

}
function plugin_weblog_action_edit()
{
	global $script,$post,$vars;
    global $X_uid,$wiki_user_dir,$no_name,$_msg_week;
	global $options, $_weblog_msgs, $no_name;
	global $xoopsUser;
	
	$conf_name = $vars['conf'];
	$_page = $vars['page_name'];
	$options = weblog_get_options($conf_name,$options);
	$weblog_name = $options['NAME'];
	$prefix = strip_bracket($options['PREFIX']);
	if (!edit_auth($prefix,FALSE,FALSE)) {
		list($vars['page'],$vars['refer']) = weblog_set_return($vars['page'],$prefix);
		return array('msg'=>"<p><strong>{$_weblog_msgs['err_msg_noauth']}</strong></p>\n",'body'=>'');
	}
	$page_name = "$prefix/$_page";
	$src = @join("",get_source($page_name));
	$sources = $src;
	if (preg_match("/\&weblog_field\(__SUBJECT\)\{([^}]+)\}\;/m",$sources,$match)) {
		$subject = $match[1];
	}
	if (preg_match("/\&weblog_field\(__AUTHOR\)\{([^}]+)\}\;/m",$sources,$match)) {
		$author = $match[1];
		if (!$xoopsUser) {
			$author = preg_replace("/".preg_quote($no_name,"/")."\((.*)\)/","\\1",$author);
		}
	}
	if (preg_match("/\&weblog_field\(__CATEGORY,[^\)]+\)\{([^}]+)\}\;((\[ )?\[\[.*\]\]( \])?)+/m",$sources,$match)) {
		$category = $match[1];
	}
	if (preg_match("/#weblog_field\(__BODY\,Start\)\s*\n(.*\n)#weblog_field\(__BODY\,End\)\n/ms",$sources,$match)) {
		$body = $match[1];
		$body = preg_replace("/\s*((\x0D\x0A)|(\x0D)|(\x0A))/", "\n", $body);
	}
	return array(
		'msg' => $weblog_name,
		'body' => plugin_weblog_make_form($conf_name,"save",$_page,$subject,$body,$category,$author),
	);
}

function plugin_weblog_convert()
{
	global $options;

	if (func_num_args() > 0){
		$args = func_get_args();
	}
	if ($args[0]!="") {
		//¥³¥ó¥Õ¥£¥°¤ÎÆÉ¤ß¹þ¤ß(»ØÄêweblog¸ÇÍ­)
		$conf_name = $args[0];
		$options = weblog_get_options($conf_name,$options);
	}
	$prefix = strip_bracket($options['PREFIX']);
	if (!edit_auth($prefix,FALSE,FALSE)) {
		return "";
	}
	return plugin_weblog_make_form($conf_name,"new","","","","");
}

function plugin_weblog_inline()
{
	global $script,$vars,$wiki_user_dir;
	$prmcnt = func_num_args();
	if ($prmcnt < 2 ) {
		return "";
	}
	$prms = func_get_args();
	$body = "";
	if ($prms[0] == "blogthis") {
		$link = "javascript:d=document;w=window;t='';";
		$link .= "if(d.selection){t=d.selection.createRange().text}";
		$link .= "else%20if(d.getSelection){t=d.getSelection()}";
		$link .= "else%20if(w.getSelection){t=w.getSelection()}";
		$link .= "void(w.open('".$script."?plugin=weblog&conf=".$prms[1];
		$link .= "&subject='+escape(d.title)+'&body='+escape(t+'\\n****»²¹Í¥Ú¡¼¥¸\\n-[['+d.title+':'+d.location.href+']]\\n')";
		if ($prms[2] == "popup") {
			$link .= "+'&popup=true','_blank','scrollbars=yes,width=640,height=480,status=yes,resizable=yes'))";
		} else {
			$link .= "))";
		}
		$body .= "<a href=\"$link\" >[BlogThis!]</a>";
	}
	return $body;
}

function plugin_weblog_make_form($conf_name,$mode="new",$page_name="",$subject="",$body="",$category="",$author="",$popup="")
{
	global $script,$vars,$digest;
	global $xoopsOption;
	global $options, $_weblog_msgs;
	// xoops //
	global $xoopsUser;
	if ($mode == "new") {
		if ($xoopsUser){
			$author = $xoopsUser->uname();
		} else {
			$author = "";
		}
	}
	$s_digest = htmlspecialchars($digest);
	$author_cols = $options['AUTHOR_COLS'];
	$cat_cols = $options['CATEGORY_COLS'];
	$subject_cols = $options['SUBJECT_COLS'];
	$body_rows = $options['BODY_ROWS'];
	$body_cols = $options['BODY_COLS'];
	if ($category == "") {
		$category = $options['DEFAULT_CATEGORY'];
	}
	$auto_br = ($options['ARTICL_AUTO_BR']==1) ? 'checked' : '';
	$allow_comment = ($options['ALLOW_COMMENT']==1) ? 'checked' : '';
	$update_stamp = ($options['UPDATE_STAMP']==1) ? 'checked' : '';
	$string = <<<EOD
<form name="weblog_form" action="$script" method="post">
 <div>
  <input type="hidden" name="plugin" value="weblog" />
  <input type="hidden" name="digest" value="$s_digest" />
  <input type="hidden" name="config" value="$conf_name" />
  <input type="hidden" name="mode" value="$mode" />
  <input type="hidden" name="popup" value="$popup" />
  <input type="hidden" name="page_name" value="$page_name" />
EOD;
	if ($author=="" || (($mode=="save") && (!$xoopsUser))) {
		$string .= "{$_weblog_msgs['lbl_author']} <input type=\"text\" name=\"author\" size=\"$author_cols\" value=\"$author\" /><br />";
	} else {
		$string .= "{$_weblog_msgs['lbl_author']} $author <input type=\"hidden\" name=\"author\" value=\"$author\" /><br />";
	}
	$string .= <<<EOD
  {$_weblog_msgs['lbl_category']} <input type="text" name="category" size="$cat_cols" value="$category" /><br />
  {$_weblog_msgs['lbl_subject']} <input type="text" name="subject" size="$subject_cols"value="$subject" /><br />
  <textarea name="body" rows="$body_rows" cols="$body_cols" wrap="virtual" >$body</textarea><br />
EOD;
	if($options['SHOW_PING_FIELD']){
		$string .= "{$_weblog_msgs['send_trackback_ping']}<textarea name=\"pingurls\" rows=\"1\" cols=\"60\">\n</textarea><br />";
	}
	$string .= <<<EOD
  {$_weblog_msgs['allow_comment']}
  <input type="checkbox" name="allow_comment" value="1" $allow_comment />
EOD;
	if (TRUE) {
		$string .= "¡¡{$_weblog_msgs['auto_br']}<input type=\"checkbox\" name=\"auto_br\" value=\"1\" $auto_br />";
	}
	if ($mode == "save") {
		$string .= "¡¡{$_weblog_msgs['update_stamp']}<input type=\"checkbox\" name=\"update_stamp\" value=\"1\" $update_stamp /><br />";
	}
	$string .= <<<EOD
  <br /><input type="submit" name="weblog" value="{$_weblog_msgs['btn_submit']}" />
 </div>
</form>
EOD;

	return $string;
}

function plugin_weblog_category_maketag($prefix,$category)
{
	$base = $prefix."/";
	$base_name = add_bracket($prefix);
	$cats = explode(",",$category);
	foreach ($cats as $cat)
	{
		if ($cat) 
		{
			if ($base_name && !is_page($base_name))
			{
				page_write($base_name,"#norelated\n***Category lists of ''".substr(strip_bracket($base_name),1)."''\n#ls2(,pagename,notemplate,relatedcount)\n");
					page_write(add_bracket($base."template"),"***Category: [[$1]]\n|T:100% TC:0 SC:0 :TOP|SC:0 :TOP|c\n|#related|****Sub Categorys->\n#ls2(,pagename,notemplate,relatedcount)|\n");
				//}
			}
			$page_names = explode("/",$cat);
			if (count($page_names) > 1)
			{
				$_cat = "";
				$cats = array();
				foreach ($page_names as $page_name)
				{
					$_cat .= $page_name;
					$cats[] = "[[$page_name>$base$_cat]]";
					$_cat .= "/";
				}
				$ret .= "[ ".join('/',$cats)." ]";
			}
			else
				$ret .= "[ [[$cat>$base$cat]] ]";
		}
	}
	return "&weblog_field(__CATEGORY,$prefix){{$category}};".$ret;
}
/*
 * $Log$
 * Revision 1.6  2004/02/25 08:56:46  nobu
 * CVS—p$Log‚Ì‰ß‹Ž•ª—š—ð‚Ì•âŠ®
 *
 * Revision 1.5  2004/02/25 08:37:14  nobu
 * CVSÍÑ¥­¡¼¥ï¡¼¥ÉÄÉ²Ã¡Ê$IdµÚ¤Ó$Log)
 *
 * Revision 1.4  2004/02/25 05:46:32  nobu
 * -¥Æ¥ó¥×¥ì¡¼¥ÈÊ¸»úÎóÃÖ´¹¤Î¶¦ÄÌ´Ø¿ô»ÈÍÑ
 *
 * Revision 1.3  2004/02/25 05:35:53  nobu
 * -À¸À®¥Ú¡¼¥¸¤Î¥Æ¥ó¥×¥ì¡¼¥È¤Ç[__PING:http://¡¦¡¦¡¦¡¦]·Á¼°¤ÇPING¥µ¡¼¥Ð¤Î»ØÄê¤ò²ÄÇ½\¤Ë¡£
 * -µ­»öÊÔ½¸»þ¤Ë¥¿¥¤¥à¥¹¥¿¥ó¥×¤òÊÑ¹¹¤¹¤ë¾ì¹ç¤Ï¡¢¥Õ¥¡¥¤¥ëÌ¾¤âÊÑ¹¹¡£(¸½¾õ¤Ç¤Ï¡¢ÀÅÅªURL¤âÊÑ¤ï¤ë)
 * -BlogThisÍÑ¥Ý¥Ã¥×¥¢¥Ã¥×¥â¡¼¥É¤Ç¤ÎÅê¹Æ¸å¤Î²èÌÌÁ«°Ü¤ò²þÁ±
 *
 * Revision 1.2  2004/02/25 05:33:30  nobu
 * -weblog_rss¥×¥é¥°¥¤¥ó¤òÄÉ²Ã¡£
 * -¿·µ¬Åê¹Æ¥Õ¥©¡¼¥à¤ËBlogThisÍÑ¤Î¥Ý¥Ã¥×¥¢¥Ã¥×¥â¡¼¥É¤òÄÉ²Ã
 *
 * Revision 1.1  2004/02/25 05:27:40  nobu
 * PukiWikiModÍÑweblog¥×¥é¥°¥¤¥ó½é´ü¥ê¥ê¡¼¥¹
 *
 */
?>
