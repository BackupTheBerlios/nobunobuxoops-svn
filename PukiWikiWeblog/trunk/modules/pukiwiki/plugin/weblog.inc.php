<?php
 /*
 
 PukiWiki WebLog Plugin

 CopyRight 2004 nobunobu (nobunobu at www.kowa.org)
 http://www.kowa.org/

Original
 CopyRight 2003 Chung-Yen Chang (candyz at kandix.idv.tw)
 http://pukiwiki.kandix.idv.tw/
 
 $Id$
 
Usage:
	#weblog
	#weblog(param1)
		param1 = config

*/
/////////////////////////////////////////////////

require_once "weblog_common.inc.php";

function plugin_weblog_init() {
	global $_weblog_msgs, $options;

	//メッセージの設定
	if (count($_weblog_msgs) == 0) {
		weblog_msg_init();
	}
	//コンフィグの取得(default)
	$options = array();
	$options = weblog_get_options("default",$options);
}
function plugin_weblog_action()
{
	global $vars;
	global $options, $_weblog_msgs;
	
	if ($vars["mode"] == "edit") {
		return plugin_weblog_action_edit();
	} else if ($vars["mode"] == "new" || $vars["mode"] == "save") {
		return plugin_weblog_action_save();
	} else {
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
		return array(
			'msg' => $weblog_name,
			'body' => plugin_weblog_make_form($conf_name,"new","",$subject,$body,$category),
		);
	}
}
function plugin_weblog_action_save()
{
	global $script,$post,$vars;
    global $X_uid,$wiki_user_dir,$no_name,$_msg_week;
	global $options, $_weblog_msgs;

	//$vars['page']の退避
	//  途中で別ファイル書き込み時に変更するが、後で戻す必要がある。
	$page_orig = $vars['page'];
	$mode = $vars['mode'];
	if ($mode == "new") {
		//新規作成時、現在日時の取得
		$timestamp=time();
	} else {
		//編集時、対象ページ名より取得
		$_page = $vars['page_name'];
		$t_year = substr($_page,0,4);
		$t_month = substr($_page,5,2);
		$t_day = substr($_page,8,2);
		$t_hour = substr($_page,11,2);
		$t_min = substr($_page,13,2);
		$t_sec = substr($_page,15,2);
		$timestamp=mktime($t_hour,$t_min,$t_sec,$t_month,$t_day,$t_year);
	}
	
	//コンフィグの読み込み(指定weblog固有)
	$conf_name = $vars['config'];
	$options = weblog_get_options($conf_name,$options);
	
	$prefix = strip_bracket($options['PREFIX']);
	
	//ページ名の設定
	if ($mode == "new") {
		//新規作成時には、prefix/YYYYMMDD-HHMMSS の形式で作成（重複時は -nn の形式でSuffix)
		$i = 0;
		$postdaytime_str = date("Y-m-d-His",$timestamp);
		$_page = $postdaytime_str;
		while(is_page("$prefix/$_page")) {
			$i++;
			$_page = $postdaytime_str."-".sprintf("%02d",$i);
		}
	}
	$page = "$prefix/$_page";
	//本文の有無のチェック
	if ($post['body'] == '') {
		list($vars['page'],$vars['refer']) = weblog_set_return($page_orig,$prefix);
		return array('msg'=>"<p><strong>{$_weblog_msgs['err_msg_nomsg']}</strong></p>\n",'body'=>'');
	}
	
	//権限のチェック
	if ($mode == "new") {
		//新規作成時には、親ページの権限を元に判断
		if (!edit_auth($prefix,FALSE,FALSE)) {
			list($vars['page'],$vars['refer']) = weblog_set_return($page_orig,$prefix);
			return array('msg'=>"<p><strong>{$_weblog_msgs['err_msg_noauth']}</strong></p>\n",'body'=>'');
		}
	} else {
		//編集時には、対象ページの権限を元に判断
		if (!edit_auth($page,FALSE,FALSE)) {
			list($vars['page'],$vars['refer']) = weblog_set_return($page_orig,$prefix);
			return array('msg'=>"<p><strong>{$_weblog_msgs['err_msg_noauth']}</strong></p>\n",'body'=>'');
		}
	}

	//投稿者及び権限設定
	$author = ($post['author'] == '') ? $_weblog_msgs['no_name'] : $post['author'];
	if ($X_uid == 0) {
		$contents_auth = "// author:0\n";
		$author = "&weblog_field(__AUTHOR){".$no_name."(".$author.")};";
	} else {
        $contents_auth = "#freeze	uid:$X_uid	aid:0	gid:0\n// author:".$X_uid."\n";
		$author = "&weblog_field(__AUTHOR){".$author."};";
	}

	//投稿日時
	$timestamp_str = "&weblog_field(__TIMESTAMP){{$timestamp}};";
	
	//件名
	$subject = ($post['subject'] == '') ? $_weblog_msgs['no_subject'] : $post['subject'];
	$subject = "&weblog_field(__SUBJECT){{$subject}};";
	
	//カテゴリー
	if ($post['category'] == ''){
		$category = $options['DEFAULT_CATEGORY'];
	} else {
		$category = $post['category'];
	}
	$catprefix = sprintf(strip_bracket($options['CATEGORY_PREFIX']),$prefix);
	$catpage = "$catprefix/$category";
	
	$category = plugin_weblog_category_maketag($catprefix,$category);
	//本文
	$body = rtrim($post['body']);
	$body = preg_replace("/\s*((\x0D\x0A)|(\x0D)|(\x0A))/", "\n", $body);

	$body = rep_for_pre($body);
	if ($post['auto_br']) {
		$body = auto_br($body);
	}
	$body = "#weblog_field(__BODY,Start)\n$body\n\n#weblog_field(__BODY,End)\n\n";
	//コメント
	if ($post['allow_comment']) {
		$comment_prefix = sprintf(strip_bracket($options['COMMENT_PREFIX']),$prefix);
		$comment = "#pcomment(".add_bracket($comment_prefix."/$_page").",10,above)\n";
	}
	$edit_link = "&weblog_field(__EDIT,$conf_name);";

	//  (Write WebLog Article to an individual file)
	//テンプレート(page)からの読込
	$contents = plugin_weblog_load_template($conf_name,"page");
	//フィールドの置換
	$contents = preg_replace("/\[__SUBJECT\]/",$subject,$contents);
	$contents = preg_replace("/\[__TITLE\]/",$title,$contents);
	$contents = preg_replace("/\[__AUTHOR\]/",$author,$contents);
	$contents = preg_replace("/\[__TIMESTAMP\]/",$timestamp_str,$contents);
	$contents = preg_replace("/\[__CATEGORY\]/",$category,$contents);
	$contents = preg_replace("/\[__BODY\]/",$body,$contents);
	$contents = preg_replace("/\[__COMMENT\]/",$comment,$contents);
	$contents = preg_replace("/\[__EDIT\]/",$edit_link,$contents);
	//ページの書込
	$vars['page'] = $page;
	if (($mode == "new") || ($post['update_stamp']==1)) {
		page_write($page,$contents_auth.$contents);
	} else {
		page_write($page,$contents_auth.$contents,true);
	}
	//月別インデックスの作成
	$postmonth = date("Y-m",$timestamp);
	$postmonth_str = date($_weblog_msgs['fmt_month'],$timestamp);
	//当月のインデックスページ名取得
	$monthpage=sprintf(strip_bracket($options['MONTHLY_PREFIX']),$prefix)."/$postmonth";
	//月別インデックスが存在しないときのみ作成する。
	if (!is_page($monthpage)) {
		//権限設定
		$monthly_auth = "#freeze\tuid:1\taid:0\tgid:0\n// author:1\n";	
		//テンプレート(pageMonthly)からの読込
		$monthly_body = plugin_weblog_load_template($conf_name,"pageMonthly");
		//フィールドの置換
		$monthly_body = preg_replace("/\[__CONF\]/",$conf_name,$monthly_body);
		$monthly_body = preg_replace("/\[__MONTHNAME\]/",$postmonth_str,$monthly_body);
		$monthly_body = preg_replace("/\[__MONTH\]/",$postmonth,$monthly_body);
		$monthly_body = preg_replace("/\[__BASELINK\]/","[[$prefix]]",$monthly_body);
		$monthly_body .= $source;
		//ページの書込
		$vars['page'] = $monthpage;
		page_write($monthpage,$monthly_auth.$monthly_body,$notimestamp=FALSE);
	}
	//日別インデックスの作成
	$postday = date("Y-m-d",$timestamp);
	$postday_str=date($_weblog_msgs['fmt_day'],$timestamp);
	$postday_str.="(".$_msg_week[date("w",$timestamp)].")";
	$postday_fullstr=date($_weblog_msgs['fmt_fullday'],$timestamp);
	$postday_fullstr.="(".$_msg_week[date("w",$timestamp)].")";
	//当日のインデックスページ名取得
	$daypage=sprintf(strip_bracket($options['DAILY_PREFIX']),$prefix)."/$postday";
	//日別インデックスが存在しないときのみ作成する。
	if (!is_page($daypage)) {
		//権限設定
		$daily_auth = "#freeze\tuid:1\taid:0\tgid:0\n// author:1\n";
		//テンプレート(pageDaily)からの読込
		$daily_body = plugin_weblog_load_template($conf_name,"pageDaily");
		//フィールドの置換
		$daily_body = preg_replace("/\[__CONF\]/",$conf_name,$daily_body);
		$daily_body = preg_replace("/\[__DAY\]/",$postday,$daily_body);
		$daily_body = preg_replace("/\[__DAYNAME\]/",$postday_str,$daily_body);
		$daily_body = preg_replace("/\[__MONTH\]/",$postmonth,$daily_body);
		$daily_body = preg_replace("/\[__BASELINK\]/","[[$prefix]]",$daily_body);
		$daily_body = preg_replace("/\[__MONTHLINK\]/","[[$postmonth_str>$monthpage]]",$daily_body);
		//ページの書込
		$vars['page'] = $daypage;
		page_write($daypage,$daily_auth.$daily_body,$notimestamp=FALSE);
	}
	//トラックバック用PINGの送信
	$retmsg = "";
	if (file_exists(CACHE_DIR.encode(strip_bracket($page)).".tbf")) {
		$r_page = rawurlencode(strip_bracket($page));
		$retmsg = $_weblog_msgs['message_ping']."<img style=\"float:left\" src=\"".XOOPS_URL."/modules/pukiwiki/ping.php?$r_page\" width=1 height=1/> </br>";
	}
	//正常終了のメッセージ出力
	list($vars['page'],$vars['refer']) = weblog_set_return($page_orig,$prefix);
	return array('msg'=>"$retmsg{$_weblog_msgs['message_sent']}\n",'body'=>'');
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
		//コンフィグの読み込み(指定weblog固有)
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
		$link .= "&subject='+escape(d.title)+'&body='+escape(t+'\n****参考ページ\n-[['+d.title+':'+d.location.href+']]\n')))";
		$body .= "<a href=\"$link\" >[BlogThis!]</a>";
	}
	return $body;
}

function plugin_weblog_make_form($conf_name,$mode="new",$page_name="",$subject="",$body="",$category="",$author="")
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
<form action="$script" method="post">
 <div>
  <input type="hidden" name="plugin" value="weblog" />
  <input type="hidden" name="digest" value="$s_digest" />
  <input type="hidden" name="config" value="$conf_name" />
  <input type="hidden" name="mode" value="$mode" />
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
		$string .= "　{$_weblog_msgs['auto_br']}<input type=\"checkbox\" name=\"auto_br\" value=\"1\" $auto_br />";
	}
	if ($mode == "save") {
		$string .= "　{$_weblog_msgs['update_stamp']}<input type=\"checkbox\" name=\"update_stamp\" value=\"1\" $update_stamp /><br />";
	}
	$string .= <<<EOD
  <br /><input type="submit" name="weblog" value="{$_weblog_msgs['btn_submit']}" />
 </div>
</form>
EOD;

	return $string;
}

function plugin_weblog_load_template($conf_name,$template) {
	$retstr = "";
	$_page = ":config/plugin/weblog/$conf_name/$template";
	if (!is_page($_page)) {
		$_page = ":config/plugin/weblog/default/$template";
		if (!is_page($_page)) {
			return FALSE;
		}
	}
	$sources = get_source($_page);
	foreach ($sources as $source) {
		$source = preg_replace('/^(\*{1,6}.*)\[#[A-Za-z][\w-]+\](.*)$/m','$1$2',$source);
		$source = preg_replace("/^#freeze(?:\tuid:([0-9]+))?(?:\taid:([0-9,]+))?(?:\tgid:([0-9,]+))?\n/",'',$source);
		$source = preg_replace("/^#unvisible(?:\tuid:([0-9]+))?(?:\taid:([0-9,]+|all))?(?:\tgid:([0-9,]+))?\n/",'',$source);
		$source = preg_replace("/^\/\/ author:([0-9]+)\n/","",$source);
		$retstr .= $source;
	}
	return $retstr;
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

?>
