<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id$
/*
Last-Update:2002-10-29 rev.8

*�ץ饰���� weblog_viewer
�۲��Υڡ����θ��Ф�(*,**,***)�ΰ�����ɽ������

*Usage
 #weblog_viewer(config,mode,[�ѥ�᡼��])
 
*�ѥ�᡼��
-config(ɬ�ܡ��ǽ�˻���)
 :config/plugin/weblog����CONFIG̾
-mode(ɬ�ܡ������ܤ˻���)
 Weblog�����μ����⡼��
 	Recent        ���ǿ���ʪ������ɽ��
 	MonthlyList   ��������Weblog����ɽ��
 	DailyList     ����������Weblog����ɽ��
 	MonthlyIndex  �����̥���ǥå�����ɽ��
 	DailyIndex    �����̥���ǥå�����ɽ���ʷ�����ǽ��
 	DailyCalendar ������������ɽ���ʷ�����ǽ�����ꤷ�ʤ��Ȥ��������:
 	Category      �����ƥ��꡼����ǥå�����ɽ��
�ʲ��Υѥ�᡼���Ͻ���Ʊ
-month:YYYY-MM
 �оݷ�λ���(mode��MonthlyList�ޤ��ϡ�DailyIndex�ξ��˻���)
-day:YYYY-MM-DD
 �о����λ���(mode��Daily�ξ��˻���)
-count
 �ƥ���ǥå�����η����ɽ��(mode��MonthlyIndex,DailyIndex,Category�ξ��˻���)
-limit:n
 ����ɽ�����(Recent�ڤ�MonthlyIndex�ξ��˻���)
-reverse
 �ڡ������¤ӽ��ȿž�����߽�ˤ���
-noney
 New�ޡ������դ��ʤ���
*/

require_once "weblog_common.inc.php";

define('WEBLOG_LIST_CONTENT_HEAD','#content_1_'); // html.php 1.36�ʹ�
define('WEBLOG_LIST_ANCHOR_ORIGIN',0); // html.php 1.36�ʹ�

function plugin_weblog_list_init() {
	global $_weblog_list_anchor;
	global $options;
	
	if (!isset($_weblog_list_anchor)) {
		$_weblog_list_anchor = 0;
	}
	//��å�����������
	if (count($_weblog_msgs) == 0) {
		weblog_msg_init();
	}

	//����ե����μ���(default)
	$options = array();
	$options = weblog_get_options("default",$options);

}

function plugin_weblog_list_convert() {
	global $script,$vars;
	global $options, $_weblog_msgs;

	if (func_num_args()>=2) {
		$args = func_get_args();
		$conf_name = array_shift($args);
		$mode = array_shift($args);
	} else {
		$args = array();
	}
	$conf_name = $conf_name;
	$options = weblog_get_options($conf_name,$options);

	if (count($options) == 0) {
		return "[weblog_list]:".sprintf($_weblog_msgs['err_msg_noconf'],$conf_name);
	}

	// ¾�Υѥ�᡼�������å�
	$params = array(
		'count' => FALSE,
		'limit' => FALSE,
		'reverse' => FALSE,
		'month' => FALSE,
		'day' => FALSE,
		'nonew'=>FALSE,
		'_args' => array(),
		'_done'=>FALSE,
		'depth' => FALSE,
		'weblog' => FALSE,
		'c_prefix' => FALSE,
		'relatedcount' => FALSE,
	);
	
	array_walk($args, 'weblog_viewer_check_arg', &$params);
	
	if ($params['month']) {
		if (preg_match("/([0-9]{4})-([0-9]{2})/",$params['month'],$m)) {
			if (!checkdate($m[2], 1, $m[1])) {
				return $_weblog_msgs['msg_invalid_param'];
			}
		} else {
			return $_weblog_msgs['msg_invalid_param'];
		}
	}
	
	if ($params['day']) {
		if (preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})/",$params['day'],$m)) {
			if (!checkdate($m[2], $m[3], $m[1])) {
				return $_weblog_msgs['msg_invalid_param'];
			}
		} else {
			return $_weblog_msgs['msg_invalid_param'];
		}
	}
	$prefix = strip_bracket($options['PREFIX']);
	switch (strtolower($mode)) {
		case 'recent' :
			$pattern = "$prefix/";
			$params['depth'] = 1;
			$params['weblog'] = TRUE;
			$params['reverse'] = TRUE;
			if (!$params['limit']) {
				$params['limit'] = 15;
			}
			break;
		case 'monthlylist' :
			$pattern = "$prefix/{$params['month']}-";
			$params['depth'] = 1;
			$params['weblog'] = TRUE;
			break;
		case 'dailylist' :
			$pattern = "$prefix/{$params['day']}-";
			$params['depth'] = 1;
			$params['weblog'] = 'time';
			break;
		case 'monthlyindex' :
			$pattern = sprintf(strip_bracket($options['MONTHLY_PREFIX']),$prefix)."/";
			if ($params['count']) {
				$params['c_prefix'] = "$prefix/";
			}
			$params['depth'] = 1;
			break;
		case 'dailyindex' :
			if (!$params['month']) {
				$pattern = sprintf(strip_bracket($options['DAILY_PREFIX']),$prefix)."/";
			} else {
				$pattern = sprintf(strip_bracket($options['DAILY_PREFIX']),$prefix)."/{$params['month']}";
			}
			if ($params['count']) {
				$params['c_prefix'] = "$prefix/";
			}
			$params['depth'] = 1;
			break;
		case 'category' :
			$pattern = sprintf(strip_bracket($options['CATEGORY_PREFIX']),$prefix)."/";
			if ($params['count']) {
				$params['relatedcount'] = TRUE;
			}
			break;
		case 'dailycalendar' :
			$pattern = sprintf(strip_bracket($options['DAILY_PREFIX']),$prefix)."/";
			if (!$params['month']) {
				$params['month'] = date("Y-m");
			}
			if ($params['count']) {
				$params['c_prefix'] = "$prefix/";
			}
			return weblog_viewer_show_calendar($pattern,$params);
			break;
		default:
			return $_weblog_msgs['msg_invalid_param'];
	}
	return weblog_viewer_show_lists($pattern,$params);
}

function weblog_viewer_show_lists($pattern,&$params) {
	global $options, $_weblog_msgs;
	$pages = weblog_viewer_get_child_pages($pattern,$params['depth']);

	if ($params['reverse']) $pages = array_reverse($pages);

	foreach ($pages as $page) {
		$params[$page] = 0;
	}

	if (count($pages) == 0) {
		return str_replace('$1',htmlspecialchars($pattern),$_weblog_msgs['err_nopages']);
	}

	$ret = '<ul>';
	$i = 1;
	foreach ($pages as $page)
	{
		if ($params['c_prefix']) {
			$child_count = weblog_viewer_count_contents($page,$pattern,$params);
		}
		$ret .= weblog_viewer_show_headings($page,$params,$pattern,$child_count);
		if ($params['limit'] && $params['limit'] < ++$i) break;
	}
	$ret .= '</ul>'."\n";
	return $ret;
}

function weblog_viewer_count_contents($page,$pattern,&$params) {
	$_pattern = preg_replace("/\/[0-9\-]*$/","",$pattern);
	$suffix = preg_replace("/^".preg_quote($_pattern,'/')."\//","",$page);
	$c_pattern = $params['c_prefix'].$suffix;
	return count(weblog_viewer_get_child_pages($c_pattern,1));
}

function weblog_viewer_show_headings($page,&$params,$prefix="",$child_count="") {
	global $script,$auto_template_name;
	global $_weblog_list_anchor, $_weblog_msgs;
	static $_auto_template_name = "";
	
	if (!$_auto_template_name) $_auto_template_name = preg_quote($auto_template_name,'/');
	
	// �ƥ�ץ졼�ȥڡ�����ɽ�����ʤ����
	if (preg_match("/\/".$_auto_template_name."(_m)?$/",$page)) return;
	
	$ret = '';
	$rules = '/\(\(((?:(?!\)\)).)*)\)\)/';
	$is_done = (isset($params[$page]) and $params[$page] > 0); //�ڡ�����ɽ���ѤߤΤȤ�True
	if (!$is_done) { $params[$page] = ++$_weblog_list_anchor; }
	
	$name = strip_bracket($page);
	$title = $name.' '.get_pg_passage($page,FALSE);

	if ($params['weblog']) {
		if (!ereg("(.*/)?([0-9]{4})-([0-9]{2})-([0-9]{2})-([0-9]{6}).*$", $name, $m) ) return;
		if (!checkdate($m[3], $m[4], $m[2])) return;
	}

	if ($use_static_url = 1)
	{
		$pgid = get_pgid_by_name($page);
		$href = XOOPS_WIKI_URL."/{$pgid}.html";
	}
	else
		$href = $script.'?'.rawurlencode($name);

	//�ڡ���̾���ֿ�����-�פ����ξ��ϡ�*(**)�Ԥ�������Ƥߤ�
	$_name = "";
	if (preg_match("/^(.*\/)?[0-9\-]+$/",$name))
	{
		$_name = get_heading($page);
	}

	//���ڡ���̾�Ͼʤ� nao-pon
	if ($name != $prefix) {
		$name = str_replace($prefix,"",$name);
		$_is_base = false;
	} else {
		$_is_base = true;
	}
	
	//���ؤǥޡ���������
	$name = str_replace("/","\t",$name);//�ޥ���Х��Ȥ��θ����TAB���Ѵ�
	$c_count =count_chars($name);
	if ($_is_base) {
		$c_margin = 0; //���ڡ���
	} else {
		$c_margin = $c_count[9]*15;//TAB�Υ����ɡ᣹
	}
	//[/(\t���Ѵ���)]�����򥫥å�
	$name = preg_replace("/.*\t/","",$name);

	$ret .= '<li style="margin-left:'.$c_margin.'px;">';
	
	if ($params['weblog']) {
		$_page = preg_replace("/(.*\/)?([0-9\-]+)$/","\\2",$page);
		$t_year = substr($_page,0,4);
		$t_month = substr($_page,5,2);
		$t_day = substr($_page,8,2);
		$t_hour = substr($_page,11,2);
		$t_min = substr($_page,13,2);
		$t_sec = substr($_page,15,2);
		$timestamp=mktime($t_hour,$t_min,$t_sec,$t_month,$t_day,$t_year);

//		$info =  get_pg_info_db($page);
//		$timestamp = $info['buildtime'];
		
		$make_date[1] = date("Y",$timestamp);
		$make_date[2] = date("m",$timestamp);
		$make_date[3] = date("d",$timestamp);
		$make_date[4] = date("H:i",$timestamp);
		if ($params['weblog']==="time") {
			$page_attr = $make_date[4];
		} else {
			$page_attr = $make_date[2]."/".$make_date[3]." ".$make_date[4];
		}
		$ret .= $page_attr." - ";
	}	
	if ($_name) $name = $_name;
	
	if ($params['relatedcount'])
		$name .= " (".links_get_related_count($page).")";
	
	if ($child_count != "")
		$name .= " ($child_count)";
	
	//New�ޡ����ղ�
	if (!$params['nonew'] && exist_plugin_inline("new"))
		$new_mark = do_plugin_inline("new","{$page}/,nolink","");
	
	$ret .= '<a id="list_'.$params[$page].'" href="'.$href.'" title="'.$title.'">'.$name.'</a>'.$new_mark;
	$anchor = WEBLOG_LIST_ANCHOR_ORIGIN;
	$_ret = '';
	if ($_ret != '') { $ret .= "<ul>$_ret</ul>\n"; }
	$ret .= '</li>'."\n";
	return $ret;
}
function weblog_viewer_get_child_pages($pattern,$depth=FALSE) {
	global $vars;
	
	$pages = array();
	foreach (get_existpages_db(false,$pattern."%") as $_page) {
		$_page = strip_bracket($_page);
		if ((int)$depth)
		{
			$pattern1 = preg_replace("/\/[0-9\-]*$/","",$pattern);

			$c_count =count_chars(preg_replace("/^".preg_quote($pattern1,'/')."\//","",$_page));
			if ($c_count[47] < $depth)
				$pages[$_page] = str_replace("/","\x00",$_page);
		}
		else
			$pages[$_page] = str_replace("/","\x00",$_page);
	}
	natcasesort($pages);

	return array_keys($pages);
}
//����������ɽ������
function weblog_viewer_show_calendar($prefix,&$params) {
	global $script,$weeklabels,$vars,$command,$WikiName,$BracketName;
	global $options, $_weblog_msgs;

	require_once("calendar2.inc.php");
	
	$date_str = $params['month'];
	$yr = substr($date_str,0,4);
	$mon = substr($date_str,5,2);
	if($yr != date("Y") || $mon != date("m"))
	{
		$now_day = 1;
		$other_month = 1;
	}
	else
	{
		$now_day = date("d");
		$other_month = 0;
	}
	$today = getdate(mktime(0,0,0,$mon,$now_day,$yr));
	
	$m_num = $today[mon];
	$d_num = $today[mday];
	$year = $today[year];
	$f_today = getdate(mktime(0,0,0,$m_num,1,$year));
	$wday = $f_today[wday];
	$day = 1;
	$fweek = true;

	$ret .= '
<table class="style_calendar" cellspacing="1" border="0">
  <tr>
    <td align="middle" class="style_td_caltop" colspan="7">
      <div class="small" style="text-align:center"><strong>'.$date_str.'</strong></div>
    </td>
  </tr>
  <tr>
';

	foreach($weeklabels as $label)
	{
		$ret .= '
    <td align="middle" class="style_td_week">
      <div class="small" style="text-align:center"><strong>'.$label.'</strong></div>
    </td>';
	}

	$ret .= "</tr>\n<tr>\n";

	while(checkdate($m_num,$day,$year))
	{
		$dt = sprintf("%4d-%02d-%02d", $year, $m_num, $day);
		$holiday = check_holiday($year,$m_num,$day);
		if ($holiday) {
			$title_tag = "[".get_holiday($holiday)."]";
		} else {
			$title_tag = "";
		}
		$name = "$prefix$dt";
		$page = "[[$prefix$dt]]";
		$page_url = rawurlencode("[[$prefix$dt]]");
		if($cmd == "edit") $refer = "&amp;refer=$page_url";
		else               $refer = "";
		if(!is_page($page)) {
			$link = "<strong>$day</strong>";
			$bg = "";
		} else {
			if ($params['c_prefix']) {
				$child_count = weblog_viewer_count_contents($name,$prefix,$params);
				$day_title = sprintf($_weblog_msgs['msg_daily'],$dt,$title_tag,$child_count);
			} else {
				$day_title = "$name $title_tag";
			}
			if ($use_static_url = 1) {
				$pgid = get_pgid_by_name($page);
				$href = XOOPS_WIKI_URL."/{$pgid}.html";
			} else {
				$href = $script.'?'.rawurlencode($name);
			}

			$link = "<a href=\"$href\" title=\"$day_title\"><strong>$day</strong></a>";
			$bg = "style=\"background-image:url(image/pencil.gif);background-repeat:no-repeat;\"";
		}
		if($fweek)
		{
			for($i=0;$i<$wday;$i++)
			{ // Blank 
				$ret .= "    <td align=\"center\" class=\"style_td_blank\">&nbsp;</td>\n"; 
			} 
		$fweek=false;
		}

		if($wday == 0) $ret .= "  </tr><tr>\n";
		if(!$other_month && ($day == $today[mday]) && ($m_num == $today[mon]) && ($year == $today[year]))
		{
			//  Today 
			$ret .= "    <td align=\"center\" class=\"style_td_today\" $bg nowrap><span class=\"small\">$link</span></td>\n"; 
		}
		else if($wday == 0 || ($holiday))
		{
			//  Sunday 
			$ret .= "    <td align=\"center\" class=\"style_td_sun\" $bg title=\"$title_tag\" nowrap><span class=\"small\">$link</span></td>\n";
		}
		else if($wday == 6)
		{
			//  Saturday 
			$ret .= "    <td align=\"center\" class=\"style_td_sat\" $bg nowrap><span class=\"small\">$link</span></td>\n";
		}
		else
		{
			// Weekday 
			$ret .= "    <td align=\"center\" class=\"style_td_day\" $bg nowrap><span class=\"small\">$link</span></td>\n";
		}
		$day++;
		$wday++;
		$wday = $wday % 7;
	}
	if($wday > 0)
	{
		while($wday < 7)
		{ // Blank 
			$ret .= "    <td align=\"center\" class=\"style_td_blank\">&nbsp;</td>\n";
		$wday++;
		} 
	}

	$ret .= "  </tr>\n</table>\n";
	return $ret;
}

//���ץ�������Ϥ���
function weblog_viewer_check_arg($val, $key, &$params)
{
	if ($val == '') { $params['_done'] = TRUE; return; }

	if (!$params['_done']) {
		foreach (array_keys($params) as $key)
		{
			if (strpos($val,':')) // PHP4.3.4��Apache2 �Ķ��ǲ��Τ�Apache�������Ȥ���𤬤��ä��Τ�
				list($_val,$thisval) = explode(":",$val);
			else
			{
				$_val = $val;
				$thisval = null;
			}
			if (strtolower($_val) == $key)
			{
				if (!empty($thisval))
					$params[$key] = $thisval;
				else
					$params[$key] = TRUE;
				return;
			}
		}
		$params['_done'] = TRUE;
	}
	$params['_args'][] = $val;
}
?>
