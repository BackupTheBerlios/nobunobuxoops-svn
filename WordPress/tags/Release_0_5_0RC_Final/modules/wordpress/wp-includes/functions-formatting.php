<?php
if( ! defined( 'WP_FUNCTION_FORMATTING_INCLUDED' ) ) {
	define( 'WP_FUNCTION_FORMATTING_INCLUDED' , 1 ) ;
function wptexturize($text) {
	$output = '';
	// Capture tags and everything inside them
	$textarr = preg_split("/(<.*>)/Us", $text, -1, PREG_SPLIT_DELIM_CAPTURE); // capture the tags as well as in between
	$stop = count($textarr); $next = true; // loop stuff
	for ($i = 0; $i < $stop; $i++) {
		$curl = $textarr[$i];

		if (isset($curl{0}) && '<' != $curl{0} && $next) { // If it's not a tag
			$curl = str_replace('---', '&#8212;', $curl);
			$curl = str_replace('--', '&#8211;', $curl);
			$curl = str_replace('...', '&#8230;', $curl);
			$curl = str_replace('``', '&#8220;', $curl);

			// This is a hack, look at this more later. It works pretty well though.
			$cockney = array("'tain't","'twere","'twas","'tis","'twill","'til","'bout","'nuff","'round");
			$cockneyreplace = array("&#8217;tain&#8217;t","&#8217;twere","&#8217;twas","&#8217;tis","&#8217;twill","&#8217;til","&#8217;bout","&#8217;nuff","&#8217;round");
			$curl = str_replace($cockney, $cockneyreplace, $curl);

			$curl = preg_replace("/'s/", '&#8217;s', $curl);
			$curl = preg_replace("/'(\d\d(?:&#8217;|')?s)/", "&#8217;$1", $curl);
			$curl = preg_replace('/(\s|\A|")\'/', '$1&#8216;', $curl);
			$curl = preg_replace('/(\d+)"/', "$1&Prime;", $curl);
			$curl = preg_replace("/(\d+)'/", "$1&prime;", $curl);
			$curl = preg_replace("/(\S)'([^'\s])/", "$1&#8217;$2", $curl);
			$curl = preg_replace('/"([\s.,!?;:&\']|\Z)/', '&#8221;$1', $curl);
            $curl = preg_replace('/(\s|\A)"/', '$1&#8220;', $curl);
			$curl = preg_replace("/'([\s.]|\Z)/", '&#8217;$1', $curl);
			$curl = preg_replace("/\(tm\)/i", '&#8482;', $curl);
			$curl = preg_replace("/\(c\)/i", '&#169;', $curl);
			$curl = preg_replace("/\(r\)/i", '&#174;', $curl);
			$curl = preg_replace('/&([^#])(?![a-z]{1,8};)/', '&#038;$1', $curl);
			$curl = str_replace("''", '&#8221;', $curl);
			
			$curl = preg_replace('/(d+)x(\d+)/', "$1&#215;$2", $curl);

		} elseif (strstr($curl, '<code') || strstr($curl, '<pre') || strstr($curl, '<kbd' || strstr($curl, '<style') || strstr($curl, '<script'))) {
			// strstr is fast
			$next = false;
		} else {
			$next = true;
		}
		$output .= $curl;
	}
	return $output;
}

function clean_pre($text) {
	$text = stripslashes($text);
	$text = str_replace('<br />', '', $text);
	return $text;
}

function wpautop($pee, $br = 1) {
	$pee = $pee . "\n"; // just to make things a little easier, pad the end
	$pee = preg_replace('|<br />\s*<br />|i', "\n\n", $pee);
	// Space things out a little
	$pee = preg_replace('!(<(?:table|thead|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|form|blockquote|p|h[1-6])[^>]*>)!i', "\n$1", $pee);
	$pee = preg_replace('!(</(?:table|thead|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|form|blockquote|p|h[1-6])>)!i', "$1\n", $pee);
	$pee = preg_replace("/(\r\n|\r)/", "\n", $pee); // cross-platform newlines 
	$pee = preg_replace("/\n\n+/", "\n\n", $pee); // take care of duplicates
	$pee = preg_replace('/\n?(.+?)(?:\n\s*\n|\z)/s', "\t<p>$1</p>\n", $pee); // make paragraphs, including one at the end 
	$pee = preg_replace('|<p>\s*?</p>|i', '', $pee); // under certain strange conditions it could create a P of entirely whitespace 
    $pee = preg_replace('!<p>\s*(</?(?:table|thead|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|form|blockquote|p|h[1-6])[^>]*>)\s*</p>!i', "$1", $pee); // don't pee all over a tag
	$pee = preg_replace("|<p>(<li.+?)</p>|i", "$1", $pee); // problem with nested lists
	$pee = preg_replace('|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $pee);
	$pee = preg_replace('|</blockquote></p>|i', '</p></blockquote>', $pee);
	$pee = preg_replace('!<p>\s*(</?(?:table|thead|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|form|blockquote|p|h[1-6])[^>]*>)!i', "$1", $pee);
	$pee = preg_replace('!(</?(?:table|thead|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|form|blockquote|p|h[1-6])[^>]*>)\s*</p>!i', "$1", $pee); 
	if ($br) {
       $pee = preg_replace('/(\<[a-z][a-z0-9]+\s.*?\>)/ies' ,'str_replace(array("\n","\r"), array(" ", " "), "\\1")', $pee);
	   $pee = preg_replace('|(?<!<br />)\s*\n|i', "<br />\n", $pee); // optionally make line breaks
    }
	$pee = preg_replace('!(</?(?:table|thead|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|form|blockquote|p|h[1-6])[^>]*>)\s*<br />!i', "$1", $pee);
	$pee = preg_replace('!<br />(\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)>)!i', '$1', $pee);
	$pee = preg_replace('/&([^#])(?![a-z]{1,8};)/', '&#038;$1', $pee);
	$pee = preg_replace('!(<pre.*?>)(.*?)</pre>!ise', " stripslashes('$1') .  clean_pre('$2')  . '</pre>' ", $pee);
	
	return $pee; 
}


function wp_filter_kses($string) {
	return wp_kses($string, $GLOBALS['wp_allowed_tags'],$GLOBALS['wp_allowed_protocols'], false);
}

function clean_html($string) {
	return wp_kses($string, $GLOBALS['wp_fullclean_tags'],$GLOBALS['wp_allowed_protocols'], false);
}

function sanitize_title($title) {
    $title = do_action('sanitize_title', $title);
    return $title;
}

function sanitize_title_with_dashes($title) {
    $title = strtolower($title);
    $title = preg_replace('/&.+?;/', '', $title); // kill entities
    $title = preg_replace('/[^a-z0-9 _-]/', '', $title);
    $title = preg_replace('/\s+/', ' ', $title);
    $title = str_replace(' ', '-', $title);
    $title = preg_replace('|-+|', '-', $title);
    $title = trim($title, '-');

    return $title;
}

function sanitize_text($str, $isArea=false, $isURL=false) {
	if (get_magic_quotes_gpc()) {
		$str = stripslashes($str);
	}
	$patterns = array();
	$replacements = array();

	$patterns[] = "/&amp;/i";
	$replacements[] = '&';
	$patterns[] = "/&nbsp;/";
	$replacements[] = '&amp;nbsp;';

	if ($isArea) {
		$patterns[] = "/&lt;(\/)?\s*script.*?&gt;/si";
		$replacements[] = '[$1script]';
		$patterns[] = "/&lt;(\/)?\s*style.*?&gt;/si";
		$replacements[] = '[$style]';
		$patterns[] = "/&lt;(\/)?\s*body.*?&gt;/si";
		$replacements[] = '[$body]';
		$patterns[] = "/&lt;(\/)?\s*link.*?&gt;/si";
		$replacements[] = '[$link]';
		$patterns[] = "/(&lt;.*)(?:onError|onUnload|onBlur|onFocus|onClick|onMouseOver|onSubmit|onReset|onChange|onSelect|onAbort)\s*=\s*(&quot;|&#039;).*\\2(.*?&gt;)/si";
		$replacements[] = '$1$3';
		if ($isURL) {
			$patterns[] = "/(&quot;|&#039;).*/";
			$replacements[] = "";
			$patterns[] = "/(?:onError|onUnload|onBlur|onFocus|onClick|onMouseOver|onSubmit|onReset|onChange|onSelect|onAbort)\s*=\s*('|\"|&quot;|&#039;).*(\\1)?/si";
			$replacements[] = "";
		}
	} else {
		$patterns[] = "/(&#13|&#10).*/";
		$replacements[] = "";
	}
	if ($isURL) {
		$patterns[] = "/javascript:/si";
		$replacements[] = "javascript|";
		$patterns[] = "/vbscript:/si";
		$replacements[] = "vbscript|";
		$patterns[] = "/about:/si";
		$replacements[] = "about|";
	}
	$str = htmlspecialchars($str, ENT_QUOTES);
	$str = preg_replace($patterns,$replacements, $str);
	
	return $str;
}

function convert_chars($content,$flag='obsolete attribute left there for backwards compatibility') { // html/unicode entities output
	// removes metadata tags
	$content = preg_replace('/<title>(.+?)<\/title>/','',$content);
	$content = preg_replace('/<category>(.+?)<\/category>/','',$content);

	if (get_settings('use_htmltrans')) {
		// converts lone & characters into &#38; (a.k.a. &amp;)
		$content = preg_replace('/&[^#](?![a-z]*;)/ie', '"&#38;".substr("\0",1)', $content);

		// converts HTML-entities to their display values in order to convert them again later
		$content = preg_replace('/['.chr(127).'-'.chr(255).']/e', '"&#".ord(\'\0\').";"', $content );
		$content = strtr($content, $GLOBALS['wp_htmltrans']);

		// now converting: Windows CP1252 => Unicode (valid HTML)
		// (if you've ever pasted text from MSWord, you'll understand)

		$content = strtr($content, $GLOBALS['wp_htmltranswinuni']);
	}

	// you can delete these 2 lines if you don't like <br /> and <hr />
	$content = str_replace('<br>','<br />',$content);
	$content = str_replace('<hr>','<hr />',$content);

	return $content;
}

/*
 balanceTags

 Balances Tags of string using a modified stack.

 @param text      Text to be balanced
 @return          Returns balanced text
 @author          Leonard Lin (leonard@acm.org)
 @version         v1.1
 @date            November 4, 2001
 @license         GPL v2.0
 @notes
 @changelog
             1.2  ***TODO*** Make better - change loop condition to $text
             1.1  Fixed handling of append/stack pop order of end text
                  Added Cleaning Hooks
             1.0  First Version
*/
function balanceTags($text, $is_comment = 0) {
	if (get_settings('use_balanceTags') == 0) {
		return $text;
	}

	$tagstack = array();
	$stacksize = 0;
	$tagqueue = '';
	$newtext = '';

	# WP bug fix for comments - in case you REALLY meant to type '< !--'
	$text = str_replace('< !--', '<    !--', $text);
	# WP bug fix for LOVE <3 (and other situations with '<' before a number)
	$text = preg_replace('#<([0-9]{1})#', '&lt;$1', $text);

	while (preg_match("/<(\/?\w*)\s*([^>]*)>/",$text,$regex)) {
		$newtext = $newtext . $tagqueue;

		$i = strpos($text,$regex[0]);
		$l = strlen($tagqueue) + strlen($regex[0]);

		// clear the shifter
		$tagqueue = '';
		// Pop or Push
		if ($regex[1][0] == "/") { // End Tag
			$tag = strtolower(substr($regex[1],1));
			// if too many closing tags
			if($stacksize <= 0) {
				$tag = '';
				//or close to be safe $tag = '/' . $tag;
			}
			// if stacktop value = tag close value then pop
			else if ($tagstack[$stacksize - 1] == $tag) { // found closing tag
				$tag = '</' . $tag . '>'; // Close Tag
				// Pop
				array_pop ($tagstack);
				$stacksize--;
			} else { // closing tag not at top, search for it
				for ($j=$stacksize-1;$j>=0;$j--) {
					if ($tagstack[$j] == $tag) {
					// add tag to tagqueue
						for ($k=$stacksize-1;$k>=$j;$k--){
							$tagqueue .= '</' . array_pop ($tagstack) . '>';
							$stacksize--;
						}
						break;
					}
				}
				$tag = '';
			}
		} else { // Begin Tag
			$tag = strtolower($regex[1]);

			// Tag Cleaning

			// Push if not img or br or hr
			if($tag != 'br' && $tag != 'img' && $tag != 'hr') {
				$stacksize = array_push ($tagstack, $tag);
			}

			// Attributes
			// $attributes = $regex[2];
			$attributes = $regex[2];
			if($attributes) {
				$attributes = ' '.$attributes;
			}
			$tag = '<'.$tag.$attributes.'>';
		}
		$newtext .= substr($text,0,$i) . $tag;
		$text = substr($text,$i+$l);
	}

	// Clear Tag Queue
	$newtext = $newtext . $tagqueue;

	// Add Remaining text
	$newtext .= $text;

	// Empty Stack
	while($x = array_pop($tagstack)) {
		$newtext = $newtext . '</' . $x . '>'; // Add remaining tags to close
	}

	// WP fix for the bug with HTML comments
	$newtext = str_replace("< !--","<!--",$newtext);
	$newtext = str_replace("<    !--","< !--",$newtext);

	return $newtext;
}

function format_to_edit($content) {
	$content = stripslashes($content);
	if ($GLOBALS['autobr']) { $content = unautobrize($content); }
	$content = apply_filters('format_to_edit', $content);
	$content = htmlspecialchars($content);
	return $content;
	}

function format_to_post($content) {
	$content = addslashes($content);
	if ($GLOBALS['post_autobr'] || $GLOBALS['comment_autobr']) { $content = autobrize($content); }
	$content = apply_filters('format_to_post', $content);
	return $content;
}

function zeroise($number,$threshold) { // function to add leading zeros when necessary
	$number = substr(str_repeat('0',$threshold).$number, -$threshold);
	return $number;
}

function backslashit($string) {
	$string = preg_replace('/([a-z])/i', '\\\\\1', $string);
	return $string;
}

function autobrize($content) {
	$content = preg_replace("/<br>\n/", "\n", $content);
	$content = preg_replace("/<br \/>\n/", "\n", $content);
	$content = preg_replace("/(\015\012)|(\015)|(\012)/", "<br />\n", $content);
	return $content;
	}
function unautobrize($content) {
	$content = preg_replace("/<br>\n/", "\n", $content);   //for PHP versions before 4.0.5
	$content = preg_replace("/<br \/>\n/", "\n", $content);
	return $content;
	}



function mysql2date($dateformatstring, $mysqlstring, $use_b2configmonthsdays = 1, $charset="") {
	if (empty($mysqlstring)) {
		return false;
	}
	$i = mktime(substr($mysqlstring,11,2),substr($mysqlstring,14,2),substr($mysqlstring,17,2),substr($mysqlstring,5,2),substr($mysqlstring,8,2),substr($mysqlstring,0,4));
	if (!empty($GLOBALS['month']) && !empty($GLOBALS['weekday']) && $use_b2configmonthsdays) {
		$datemonth = $GLOBALS['month'][date('m', $i)];
		$dateweekday = $GLOBALS['weekday'][date('w', $i)];
		$dateformatstring = ' '.$dateformatstring;
		$dateformatstring = preg_replace("/([^\\\])D/", "\\1".backslashit(mb_substring($dateweekday, 0, $GLOBALS['s_weekday_length'], $charset)),$dateformatstring);
		$dateformatstring = preg_replace("/([^\\\])F/", "\\1".backslashit($datemonth), $dateformatstring);
		$dateformatstring = preg_replace("/([^\\\])l/", "\\1".backslashit($dateweekday), $dateformatstring);
		$dateformatstring = preg_replace("/([^\\\])M/", "\\1".backslashit(mb_substring($datemonth, 0, $GLOBALS['s_month_length'], $charset)), $dateformatstring);
		$dateformatstring = substr($dateformatstring, 1, strlen($dateformatstring)-1);
	}
	$timezone=date('O',$i);
	$rdf_timezone = substr($timezone,0,3).':'.substr($timezone,3,2);
	$dateformatstring = preg_replace("/([^\\\])o/", "\\1".backslashit($rdf_timezone), $dateformatstring);
	$j = @date($dateformatstring, $i);
	return $j;
}

function current_time($type) {
	$time_difference = get_settings('time_difference');
	switch ($type) {
		case 'mysql':
			return date('Y-m-d H:i:s', (time() + ($time_difference * 3600) ) );
			break;
		case 'timestamp':
			return (time() + ($time_difference * 3600) );
			break;
	}
}

function addslashes_gpc($gpc) {
	if (!get_magic_quotes_gpc()) {
		$gpc = addslashes($gpc);
	}
	return $gpc;
}

function antispambot($emailaddy, $mailto=0) {
	$emailNOSPAMaddy = '';
	srand ((float) microtime() * 1000000);
	for ($i = 0; $i < strlen($emailaddy); $i = $i + 1) {
		$j = floor(rand(0, 1+$mailto));
		if ($j==0) {
			$emailNOSPAMaddy .= '&#'.ord(substr($emailaddy,$i,1)).';';
		} elseif ($j==1) {
			$emailNOSPAMaddy .= substr($emailaddy,$i,1);
		} elseif ($j==2) {
			$emailNOSPAMaddy .= '%'.zeroise(dechex(ord(substr($emailaddy, $i, 1))), 2);
		}
	}
	$emailNOSPAMaddy = str_replace('@','&#64;',$emailNOSPAMaddy);
	return $emailNOSPAMaddy;
}

function make_clickable($text) { // original function: phpBB, extended here for AIM & ICQ
    $ret = " " . $text;
    $ret = preg_replace("#([\n ])([a-z]+?)://([^, <>{}\n\r]+)#i", "\\1<a href=\"\\2://\\3\" target=\"_blank\">\\2://\\3</a>", $ret);
    $ret = preg_replace("#([\n ])aim:([^,< \n\r]+)#i", "\\1<a href=\"aim:goim?screenname=\\2\\3&message=Hello\">\\2\\3</a>", $ret);
    $ret = preg_replace("#([\n ])icq:([^,< \n\r]+)#i", "\\1<a href=\"http://wwp.icq.com/scripts/search.dll?to=\\2\\3\">\\2\\3</a>", $ret);
    $ret = preg_replace("#([\n ])www\.([a-z0-9\-]+)\.([a-z0-9\-.\~]+)((?:/[^,< \n\r]*)?)#i", "\\1<a href=\"http://www.\\2.\\3\\4\" target=\"_blank\">www.\\2.\\3\\4</a>", $ret);
    $ret = preg_replace("#([\n ])([a-z0-9\-_.]+?)@([^,< \n\r]+)#i", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>", $ret);
    $ret = substr($ret, 1);
    return $ret;
}

function convert_smilies($text) {
	if (get_settings('use_smilies')) {
		// HTML loop taken from texturize function, could possible be consolidated
		$textarr = preg_split("/(<.*>)/U", $text, -1, PREG_SPLIT_DELIM_CAPTURE); // capture the tags as well as in between
		$stop = count($textarr);// loop stuff
		$output = '';
		for ($i = 0; $i < $stop; $i++) {
			$content = $textarr[$i];
			if ((strlen($content) > 0) && ('<' != $content{0})) { // If it's not a tag
				$content = str_replace($GLOBALS['wp_smiliessearch'][wp_id()], $GLOBALS['wp_smiliesreplace'][wp_id()], $content);
			}
			$output .= $content;
		}
	} else {
		// return default text.
		$output = $text;
	}
	return $output;
}

function is_email($user_email) {
	$chars = "/^([a-z0-9_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,4}\$/i";
	if(strstr($user_email, '@') && strstr($user_email, '.')) {
		if (preg_match($chars, $user_email)) {
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

function strip_all_but_one_link($text, $mylink) {
	$match_link = '#(<a.+?href.+?'.'>)(.+?)(</a>)#';
	preg_match_all($match_link, $text, $matches);
	$count = count($matches[0]);
	for ($i=0; $i<$count; $i++) {
		if (!strstr($matches[0][$i], $mylink)) {
			$text = str_replace($matches[0][$i], $matches[2][$i], $text);
		}
	}
	return $text;
}

function date_i18n($dateformatstring, $unixtimestamp, $charset="") {
	if ((!empty($GLOBALS['month'])) && (!empty($GLOBALS['weekday']))) {
		$datemonth = $GLOBALS['month'][date('m', $unixtimestamp)];
		$dateweekday = $GLOBALS['weekday'][date('w', $unixtimestamp)];
		$dateformatstring = ' '.$dateformatstring;
		$dateformatstring = preg_replace("/([^\\\])D/", "\\1".backslashit(mb_substring($dateweekday, 0, $GLOBALS['s_weekday_length'], $charset)), $dateformatstring);
		$dateformatstring = preg_replace("/([^\\\])F/", "\\1".backslashit($datemonth), $dateformatstring);
		$dateformatstring = preg_replace("/([^\\\])l/", "\\1".backslashit($dateweekday), $dateformatstring);
		$dateformatstring = preg_replace("/([^\\\])M/", "\\1".backslashit(mb_substring($datemonth, 0, $GLOBALS['s_month_length'], $charset)), $dateformatstring);
		$dateformatstring = substr($dateformatstring, 1, strlen($dateformatstring)-1);
	}
	$j = @date($dateformatstring, $unixtimestamp);
	return $j;
	}

function get_weekstartend($mysqlstring, $start_of_week) {
	$my = substr($mysqlstring,0,4);
	$mm = substr($mysqlstring,8,2);
	$md = substr($mysqlstring,5,2);
	$day = mktime(0,0,0, $md, $mm, $my);
	$weekday = date('w',$day);
	$i = 86400;
	while ($weekday > $start_of_week) {
		$weekday = date('w',$day);
		$day = $day - 86400;
		$i = 0;
	}
	$week['start'] = $day + 86400 - $i;
	$week['end']   = $day + 691199;
	return $week;
}

/* big funky fixes for browsers' javascript bugs */
function fix_js_param($str) {
    if (($GLOBALS['is_macIE']) && (!isset($GLOBALS['IEMac_bookmarklet_fix']))) {
        $str = preg_replace($GLOBALS['wp_macIE_correction']['in'],$GLOBALS['wp_macIE_correction']['out'], $str);
    }
    if (($GLOBALS['is_winIE']) && (!isset($GLOBALS['IEWin_bookmarklet_fix']))) {
        $str =  preg_replace("/\%u([0-9A-F]{4,4})/e",  "'&#'.base_convert('\\1',16,10).';'", $str);
    }
    if (($GLOBALS['is_gecko']) && (!isset($GLOBALS['Gecko_bookmarklet_fix']))) {
        $str = preg_replace($GLOBALS['wp_gecko_correction']['in'], $GLOBALS['wp_gecko_correction']['out'], $str);
		$str = preg_replace("/\%u([0-9A-F]{4,4})/e", "'&#'.base_convert('\\1',16,10).';'", $str);
    }
    return $str;
}

function convert_bbcode($content) {
    if (get_settings('use_bbcode')) {
        $myts = new MyTextSanitizer;
        $content =& $myts->codePreConv($content, 1); // Ryuji_edit(2003-11-18)
        if (method_exists($myts, 'wikiPreConv')) {
			$content =& $myts->wikiPreConv($content, 1); // modPukiWiki Conv by nobunobu
		}
        $content =& $myts->xoopsCodeDecode($content);
        if (method_exists($myts, 'wikiConv')) {
			$content =& $myts->wikiConv($content, 1, 0, 1); // modPukiWiki Conv by nobunobu
		}
        $content =& $myts->codeConv($content, 1, 0);    // Ryuji_edit(2003-11-18)
    }
    return $content;
}

function convert_gmcode($content) {
	if (get_settings('use_gmcode')) {
		$content = preg_replace($GLOBALS['wp_gmcode']['in'], $GLOBALS['wp_gmcode']['out'], $content);
	}
	return $content;
}

function popuplinks($text) {
	// Comment text in popup windows should be filtered through this.
	// Right now it's a moderately dumb function, ideally it would detect whether
	// a target or rel attribute was already there and adjust its actions accordingly.
	$text = preg_replace('/<a (.+?)>/i', "<a $1 target='_blank' rel='external'>", $text);
	return $text;
}
}
?>