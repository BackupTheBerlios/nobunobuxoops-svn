<?php
/*
 * PukiWiki weblog_rssプラグイン
 *
 *$Id$
 *
 */

///////////////////////////////////////////////////////////
require_once 'weblog_common.inc.php';

function plugin_weblog_rss_inline()
{
	global $script,$rss_max;
	$list_count = $rss_max;
	if (func_num_args() == 4) {
		list($conf,$with_content,$list_count,$body) = func_get_args();
	} elseif (func_num_args() == 3) {
		list($conf,$with_content,$body) = func_get_args();
	} elseif (func_num_args() == 2) {
		list($conf,$body) = func_get_args();
		$with_content="false";
	} elseif (func_num_args() == 1) {
		$conf = $type = $with_content = "";
	} else {
		return FALSE;
	}
	$s_conf = "&amp;config=".rawurlencode($conf);
	$alt_conf = " of ".htmlspecialchars($conf);

	if ($with_content=="true") {
		$s_content = "&amp;content=true";
	} else {
		$s_content = "&amp;content=false";
	}

	$s_list_count = "&amp;count=$list_count";
	return "<a href=\"$script?plugin=weblog_rss&$s_conf$s_content$s_list_count	\"><img src=\"".XOOPS_WIKI_URL."/image/rdf.png\" alt=\"RSS$alt_conf\" /></a>";
}
function plugin_weblog_rss_action()
{
	global $rss_max,$page_title,$WikiName,$BracketName,$script,$whatsnew,$trackback,$use_static_url,$anon_writable;
	global $vars;
	global $options, $_weblog_msgs;

	//コンフィグの読み込み(指定weblog固有)
	$conf_name = $vars['config'];
	$options = weblog_get_options($conf_name,$options);

	$page = strip_bracket($options['PREFIX']);

	$with_content = $vars['content'];
	$list_count=$vars['count'];
	if ($list_count == 0) {
		$list_count = $rss_max;
	}
	$lines = get_existpages(false,$page,$list_count," ORDER BY editedtime DESC",true);
	header("Content-type: application/xml");
	if (is_page($page)) {
		$linkpage = $page;
	} else {
		if (strrchr($page,"/")) {
			$_p = substr($page,0,-strlen(strrchr($page,"/")));
			if (is_page($_p)) {
				$linkpage = $_p;
			}
		} else {
			$linkpage = $whatsnew;
		}
	}
	if ($use_static_url) {
		$linkpage_url = XOOPS_WIKI_URL."/".get_pgid_by_name($linkpage).".html";
	} else {
		$linkpage_url = $script."?".rawurlencode($linkpage);
	}
	$page_title_utf8 = $page_title;
	if(function_exists("mb_convert_encoding")) {
		$page_title_utf8 = mb_convert_encoding($page_title_utf8,"UTF-8","auto");
//		$page_utf8 = mb_convert_encoding($linkpage,"UTF-8","auto");
		$page_utf8 = mb_convert_encoding($options['NAME'],"UTF-8","auto");
		$page_add_utf8 = ($linkpage)? "-".$page_utf8 : "";
	}
	$item = "";
	$rdf_li = "";
	foreach($lines as $line) {
		$vars['page'] = $line;
		$page_name = strip_bracket($line);
		if (!preg_match("/^(.*\/)?[0-9\-]+$/",$page_name)) continue;
		$src = @join("",get_source($page_name));
		$sources = $src;
		if (preg_match("/\&weblog_field\(__SUBJECT\)\{([^}]+)\}\;/m",$sources,$match)) {
			$subject = $match[1];
		}
		if (preg_match("/\&weblog_field\(__AUTHOR\)\{([^}]+)\}\;/m",$sources,$match)) {
			$author = $match[1];
		}
		if (preg_match("/\&weblog_field\(__CATEGORY,:([^\)]+)\)\{([^}]+)\}\;((\[ )?\[\[.*\]\]( \])?)+/m",$sources,$match)) {
			$catpath = $match[1];
			$category = $match[2];
		}
		if (preg_match("/#weblog_field\(__BODY\,Start\)\s*\n(.*\n)#weblog_field\(__BODY\,End\)\n/ms",$sources,$match)) {
			$body = $match[1];
			$body = preg_replace("/\s*((\x0D\x0A)|(\x0D)|(\x0A))/", "\n", $body);
		}

		$title = mb_convert_encoding($subject,"UTF-8","auto");

		$url = strip_bracket($line);
//		if ($page) $title = preg_replace("/^".preg_quote($page_utf8,"/")."\//","",$title);
		$title = htmlspecialchars($title);

		$desc = date("D, d M Y H:i:s T",filemtime(get_filename(encode($line))));
		$dcdate =  substr_replace(date("Y-m-d\TH:i:sO",filemtime(get_filename(encode($line)))),':',-2,0);
		
		if ($use_static_url)
			$link_url = XOOPS_WIKI_URL."/".get_pgid_by_name($line).".html";
		else
			$link_url = $script."?".rawurlencode($url);
		
		$items.= "<item rdf:about=\"".$link_url."\">\n";
		$items.= " <title>$title</title>\n";
		$items.= " <link>".$link_url."</link>\n";
		$items.= " <dc:date>$dcdate</dc:date>\n";
		
		$_anon_writable = $anon_writable;
		$anon_writable = 0;
		$desc = convert_html($body,false,false);
		$desc=mb_convert_encoding(mb_substr(strip_htmltag($desc),0,250,"EUC-JP"),"UTF-8","auto");
		$desc=htmlspecialchars($desc);
		$desc=mb_ereg_replace("\n","",$desc);
		$items.= " <description>$desc</description>\n";
		if($with_content=="true") {
			$src = preg_replace("/\&weblog_field\(__EDIT\,[^\)]+\);/m","",$src);
			$content = convert_html($src,false,false);
			$content = mb_convert_encoding($content,"UTF-8","auto");
			$content = preg_replace("/\<input [^\>]+\/>(\n)?/ms","",$content);
			$content = preg_replace("/^(\s*\n)+/mS","\n",$content);
			$content = preg_replace("/\s*\<br \/\>\s*/mS","<br />",$content);
			$content = preg_replace("/class\=\"p\_right\"/",'style="text-align:right;"',$content);
			$items.= "<content:encoded>\n<![CDATA[\n";
			$items.= "$content\n";
			$items.= "]]>\n</content:encoded>\n";
		}
		$anon_writable = $anon_writable = 0;
		//trackback
		if ($trackback) {
			$dc_identifier = $trackback_ping = '';
			$r_page=rawurlencode($url);
			$tb_id = tb_get_id($url);
			$dc_identifier = " <dc:identifer>$link_url</dc:identifer>\n";
			$trackback_ping = " <trackback:ping>$script?pwm_ping=$tb_id</trackback:ping>\n";
			$items.=$dc_identifier . $trackback_ping;
		}
		if ($category != "") {
			$cats = explode(",",$category);
			foreach($cats as $cat_item) {
//				$subject = $catpath."/".$cat_item;
				$subject = $cat_item;
				$subject = mb_convert_encoding($subject,"UTF-8","auto");
				$items .= "<dc:subject>$subject</dc:subject>\n";
			}
			
		}

		$items.= "</item>\n\n";
		$rdf_li.= "<rdf:li rdf:resource=\"".$link_url."\" />\n";
	}
//	header('Content-type: text');

	$r_page = rawurlencode($page);
	echo <<<EOD
<?xml version="1.0" encoding="utf-8"?>

<rdf:RDF 
  xmlns:dc="http://purl.org/dc/elements/1.1/"
  xmlns="http://purl.org/rss/1.0/"
  xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
EOD;
	if($with_content=="true") {
		echo "  xmlns:content=\"http://purl.org/rss/1.0/modules/content/\"\n";
	}
	if($trackback) {
  		echo "  xmlns:trackback=\"http://madskills.com/public/xml/rss/module/trackback/\"\n";
	}
	echo <<<EOD
  xml:lang="ja">
  <channel rdf:about="$script?plugin=rss10&amp;page=$r_page&amp;content=$with_content&amp;count=$list_count">
  <title>$page_title_utf8.$page_add_utf8</title>
  <link>$linkpage_url</link>
  <description>PukiWiki Weblog RecentChanges</description>
  <items>
   <rdf:Seq>
      $rdf_li
   </rdf:Seq>
  </items>
 </channel>
 $items
</rdf:RDF>
EOD;
	exit;
}
?>
