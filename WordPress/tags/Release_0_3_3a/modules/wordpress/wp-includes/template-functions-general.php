<?php

/* Note: these tags go anywhere in the template */

function bloginfo($show='') {
    $info = get_bloginfo($show);
    $info = apply_filters('bloginfo', $info);
    echo convert_chars($info);
}

function bloginfo_rss($show='') {
	global $blog_charset;
	$info = strip_tags(get_bloginfo($show));
	echo wp_convert_rss_charset($info);
}

function bloginfo_unicode($show='') {
    $info = get_bloginfo($show);
    echo convert_chars($info);
}

function get_bloginfo($show='') {
	global $siteurl;

    $do_perma = 0;
    $feed_url = $siteurl;
    $comment_feed_url = $siteurl;;

    if ('' != get_settings('permalink_structure')) {
        $do_perma = 1;
        $feed_url = $siteurl.'/feed';
        $comment_feed_url = $siteurl.'/comments/feed';
    }

    switch($show) {
        case 'url':
		case 'siteurl':
			$output = $siteurl.'/index.php';
            break;
        case 'description':
            $output = get_settings('blogdescription');
            break;
        case 'rdf_url':
            $output = $siteurl.'/wp-rdf.php';
            if ($do_perma) {
                $output = $feed_url . '/rdf/';
            }
            break;
        case 'rss_url':
            $output = $siteurl.'/wp-rss.php';
            if ($do_perma) {
                $output = $feed_url . '/rss/';
            }
            break;
        case 'rss2_url':
            $output = $siteurl.'/wp-rss2.php';
            if ($do_perma) {
                $output = $feed_url . '/rss2/';
            }
            break;
        case 'atom_url':
            $output = $siteurl.'/wp-atom.php';
            if ($do_perma) {
                $output = $feed_url . '/atom/';
            }
            break;        
        case 'comments_rss2_url':
            $output = $siteurl.'/wp-commentsrss2.php';
            if ($do_perma) {
                $output = $comment_feed_url . '/rss2/';
            }
            break;
        case 'pingback_url':
            $output = $siteurl.'/xmlrpc.php';
            break;
        case 'admin_email':
            $output = get_settings('admin_email');
            break;
		case 'charset':
			$output = get_settings('blog_charset');
			if ('' == $output) $output = 'UTF-8';
			break;
		case 'version':
			global $wp_version;
			$output = $wp_version;
			break;
        case 'name':
        default:
            $output = get_settings('blogname');
            break;
    }
    return $output;
}

function wp_title($sep = '&raquo;', $display = true) {
	global $wpdb,  $wp_id;
	global $year, $monthnum, $day, $cat, $p, $name, $month;

	// If there's a category
   if(!empty($cat)) {
        if (!stristr($cat,'-')) { // category excluded
            $title = stripslashes(get_the_category_by_ID($cat));
        }
    }
	if (!empty($category_name)) {
		$title = stripslashes($wpdb->get_var("SELECT cat_name FROM {$wpdb->categories[$wp_id]} WHERE category_nicename = '$category_name'"));
	}

	// If there's a month
	if(!empty($m)) {
		$my_year = substr($m, 0, 4);
		$my_month = $month[substr($m, 4, 2)];
		$title = "$my_year $sep $my_month";

	}
	if (!empty($year)) {
		$title = $year;
		if (!empty($monthnum)) {
			$title .= " $sep ".$month[zeroise($monthnum, 2)];
		}
		if (!empty($day)) {
			$title .= " $sep ".zeroise($day, 2);
		}
	}

	// If there's a post
	if (intval($p) || '' != $name) {
		if (!$p) {
		if ($year != '') {
			$year = '' . intval($year);
			$where .= ' AND YEAR(post_date)=' . $year;
		}

		if ($monthnum != '') {
			$monthnum = '' . intval($monthnum);
			$where .= ' AND MONTH(post_date)=' . $monthnum;
		}

		if ($day != '') {
			$day = '' . intval($day);
			$where .= ' AND DAYOFMONTH(post_date)=' . $day;
		}
			$p = $wpdb->get_var("SELECT ID FROM {$wpdb->posts[$wp_id]} WHERE post_name = '$name' $where");
		}
		$post_data = get_postdata($p);
		$title = strip_tags(stripslashes($post_data['Title']));
		$title = apply_filters('single_post_title', $title);
	}

	// Send it out
	if ($display && isset($title)) {
		echo " $sep $title";
	} elseif (!$display && isset($title)) {
		return " $sep $title";
	}
}

function single_post_title($prefix = '', $display = true) {
	global $p, $name, $wpdb, $wp_id;
	if (intval($p) || '' != $name) {
		if (!$p) {
			$p = $wpdb->get_var("SELECT ID FROM {$wpdb->posts[$wp_id]} WHERE post_name = '$name'");
		}
		$post_data = get_postdata($p);
		$title = $post_data['Title'];
		$title = apply_filters('single_post_title', $title);
		if ($display) {
			echo $prefix.strip_tags(stripslashes($title));
		} else {
			return strip_tags(stripslashes($title));
		}
	}
}

function single_cat_title($prefix = '', $display = true ) {
	global $cat;
	if(!empty($cat) && !(strtoupper($cat) == 'ALL')) {
		$my_cat_name = get_the_category_by_ID($cat);
		if(!empty($my_cat_name)) {
			if ($display)
				echo $prefix.strip_tags(stripslashes($my_cat_name));
			else
				return strip_tags(stripslashes($my_cat_name));
		}
	}
}

function single_month_title($prefix = '', $display = true ) {
	global $m, $month;
	if(!empty($m)) {
		$my_year = substr($m,0,4);
		$my_month = $month[substr($m,4,2)];
		if ($display)
			echo $prefix.$my_month.$prefix.$my_year;
		else
			return $m;
	}
}

/* link navigation hack by Orien http://icecode.com/ */
function get_archives_link($url, $text, $format = "html", $before = "", $after = "") {
	if ('link' == $format) {
		return "\t".'<link rel="archives" title="'.$text.'" href="'.$url.'" />'."\n";
	} else if ('option' == $format) {
		return '<option value="'.$url.'">'.$text.$after.'</option>'."\n";
	} else if ('html' == $format) {
		return "\t".'<li><a href="'.$url.'" title="'.$text.'">'.$text.'</a>'.$after.'</li>'."\n";
	} else { // custom
		return "\t".$before.'<a href="'.$url.'" title="'.$text.'">'.$text.'</a>'.$after."\n";
	}
}


function wp_get_archives($args = '') {
	parse_str($args, $r);
	if (!isset($r['type'])) $r['type'] = '';
	if (!isset($r['limit'])) $r['limit'] = '';
	if (!isset($r['format'])) $r['format'] = 'html';
	if (!isset($r['before'])) $r['before'] = '';
	if (!isset($r['after'])) $r['after'] = '';
	if (!isset($r['show_post_count'])) $r['show_post_count'] = false;
	get_archives($r['type'], $r['limit'], $r['format'], $r['before'], $r['after'], $r['show_post_count']);
}

function get_archives($type='', $limit='', $format='html', $before = "", $after = "", $show_post_count = false) {
	global  $dateformat, $time_difference, $siteurl,$wp_id;
    global $month, $wpdb,  $wp_month_format;

    if ('' == $type) {
        $type = get_settings('archive_mode');
    }

	if ('' != $limit) {
        $limit = (int) $limit;
		$limit = " LIMIT $limit";
	}
	// this is what will separate dates on weekly archive links
	$archive_week_separator = '&#8211;';

	// archive link url
	$archive_link_m = $siteurl.'/index.php?m=';	# monthly archive;
	$archive_link_w = $siteurl.'/index.php?w=';	# weekly archive;
	$archive_link_p = $siteurl.'/index.php?p=';	# post-by-post archive;

    // over-ride general date format ? 0 = no: use the date format set in Options, 1 = yes: over-ride
    $archive_date_format_over_ride = 0;

    // options for daily archive (only if you over-ride the general date format)
    $archive_day_date_format = 'Y/m/d';

    // options for weekly archive (only if you over-ride the general date format)
    $archive_week_start_date_format = 'Y/m/d';
    $archive_week_end_date_format   = 'Y/m/d';

    if (!$archive_date_format_over_ride) {
        $archive_day_date_format = $dateformat;
        $archive_week_start_date_format = $dateformat;
        $archive_week_end_date_format   = $dateformat;
    }

	$now = date('Y-m-d H:i:s',(time() + ($time_difference * 3600)));

	if ('monthly' == $type) {
		$arcresults = $wpdb->get_results("SELECT DISTINCT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, count(ID) as posts FROM {$wpdb->posts[$wp_id]} WHERE post_date < '$now' AND post_status = 'publish' GROUP BY YEAR(post_date), MONTH(post_date) ORDER BY post_date DESC" . $limit);
        if ($arcresults) {
            foreach ($arcresults as $arcresult) {
                $url  = get_month_link($arcresult->year,   $arcresult->month);
                if ($show_post_count) {
               		$text = ereg_replace('%MONTH',$month[zeroise($arcresult->month,2)],$wp_month_format);
					$text = ereg_replace('%YEAR',sprintf("%d",$arcresult->year),$text);
                    $after = "&nbsp;($arcresult->posts)";
                } else {
                	$text = ereg_replace('%MONTH',$month[zeroise($arcresult->month,2)],$wp_month_format);
					$text = ereg_replace('%YEAR',sprintf("%d",$arcresult->year),$text);
                }
                echo get_archives_link($url, $text, $format, $before, $after);
            }
        }
	} elseif ('daily' == $type) {
		$arcresults = $wpdb->get_results("SELECT DISTINCT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, DAYOFMONTH(post_date) AS `dayofmonth` FROM {$wpdb->posts[$wp_id]} WHERE post_date < '$now' AND post_status = 'publish' ORDER BY post_date DESC" . $limit);
        if ($arcresults) {
            foreach ($arcresults as $arcresult) {
                $url  = get_day_link($arcresult->year, $arcresult->month, $arcresult->dayofmonth);
                $date = sprintf("%d-%02d-%02d 00:00:00", $arcresult->year, $arcresult->month, $arcresult->dayofmonth);
                $text = mysql2date($archive_day_date_format, $date);
                echo get_archives_link($url, $text, $format, $before, $after);
            }
        }
	} elseif ('weekly' == $type) {
		$arcresults = $wpdb->get_results("SELECT DISTINCT WEEK(post_date, ".get_settings('start_of_week').") AS `week`, YEAR(post_date) AS yr, DATE_FORMAT(post_date, '%Y-%m-%d') AS yyyymmdd FROM {$wpdb->posts[$wp_id]} WHERE post_date < '$now' AND post_status = 'publish' ORDER BY post_date DESC" . $limit);
		$arc_w_last = '';
        if ($arcresults) {
            foreach ($arcresults as $arcresult) {
                if ($arcresult->week != $arc_w_last) {
                    $arc_year = $arcresult->yr;
                    $arc_w_last = $arcresult->week;
                    $arc_week = get_weekstartend($arcresult->yyyymmdd, get_settings('start_of_week'));
                    $arc_week_start = date_i18n($archive_week_start_date_format, $arc_week['start']);
                    $arc_week_end = date_i18n($archive_week_end_date_format, $arc_week['end']);
                    $url  = sprintf("%s/index.php?m=%s&amp;w=%d", $siteurl, $arc_year, $arcresult->week);
                    $text = $arc_week_start . $archive_week_separator . $arc_week_end;
                    echo get_archives_link($url, $text, $format, $before, $after);
                }
            }
        }
	} elseif ('postbypost' == $type) {
		$arcresults = $wpdb->get_results("SELECT ID, post_date, post_title FROM {$wpdb->posts[$wp_id]} WHERE post_date < '$now' AND post_status = 'publish' ORDER BY post_date DESC" . $limit);
        if ($arcresults) {
            foreach ($arcresults as $arcresult) {
                if ($arcresult->post_date != '0000-00-00 00:00:00') {
                    $url  = get_permalink($arcresult->ID);
                    $arc_title = stripslashes($arcresult->post_title);
                    if ($arc_title) {
                        $text = strip_tags($arc_title);
                    } else {
                        $text = $arcresult->ID;
                    }
                    echo get_archives_link($url, $text, $format, $before, $after);
                }
            }
        }
	}
}
function get_calendar($daylength = 1) {
	global $wpdb ,$wp_id, $m, $monthnum, $year, $timedifference, $month, $weekday,  $posts, $wp_month_format,$blog_charset;

    // Quick check. If we have no posts at all, abort!
    if (!$posts) {
        $gotsome = $wpdb->get_var("SELECT ID from {$wpdb->posts[$wp_id]} WHERE post_status = 'publish' ORDER BY post_date DESC LIMIT 1");
        if (!$gotsome)
            return;
    }

	if (isset($_GET['w'])) {
		$w = ''.intval($_GET['w']);
	}
	$time_difference = get_settings('time_difference');

	// Let's figure out when we are
	if (!empty($monthnum) && !empty($year)) {
		$thismonth = ''.intval($monthnum);
		$thisyear = ''.intval($year);
	} elseif (!empty($w)) {
		// We need to get the month from MySQL
		$thisyear = ''.intval(substr($m, 0, 4));
		$d = (($w - 1) * 7) + 6; //it seems MySQL's weeks disagree with PHP's
		$thismonth = $wpdb->get_var("SELECT DATE_FORMAT((DATE_ADD('${thisyear}0101', INTERVAL $d DAY) ), '%m')");
	} elseif (!empty($m)) {
		$calendar = substr($m, 0, 6);
		$thisyear = ''.intval(substr($m, 0, 4));
		if (strlen($m) < 6) {
			$thismonth = '01';
		} else {
			$thismonth = ''.intval(substr($m, 4, 2));
		}
	} else {
		$thisyear = intval(date('Y', time()+($time_difference * 3600)));
		$thismonth = intval(date('m', time()+($time_difference * 3600)));
	}

	$unixmonth = mktime(0, 0 , 0, $thismonth, 1, $thisyear);

	// Get the next and previous month and year with at least one post
	$previous = $wpdb->get_row("SELECT DISTINCT MONTH( post_date ) AS month, YEAR( post_date ) AS year
			FROM {$wpdb->posts[$wp_id]}
			WHERE post_date < '$thisyear-$thismonth-01'
			AND post_status = 'publish'
							  ORDER BY post_date DESC
							  LIMIT 1");
	$next = $wpdb->get_row("SELECT  DISTINCT MONTH( post_date ) AS month, YEAR( post_date ) AS year
			FROM {$wpdb->posts[$wp_id]}
			WHERE post_date >  '$thisyear-$thismonth-01'
			AND MONTH( post_date ) != MONTH( '$thisyear-$thismonth-01' )
			AND post_status = 'publish'
							  ORDER  BY post_date ASC
							  LIMIT 1");
	$month_str = ereg_replace('%MONTH',$month[zeroise($thismonth, 2)],$wp_month_format);
	$month_str = ereg_replace('%YEAR',date('Y', $unixmonth),$month_str);
	echo '<table id="wp-calendar">
	<caption>' . $month_str . '</caption>
	<thead>
	<tr>';
	foreach ($weekday as $wd) {
		if (function_exists('mb_substr')) {
			echo "\n\t\t<th abbr='$wd' scope='col' title='$wd'>" . mb_substr($wd, 0, $daylength,$blog_charset) . '</th>';
		} else {
			echo "\n\t\t<th abbr='$wd' scope='col' title='$wd'>" . substr($wd, 0, $daylength) . '</th>';
		}
	}

	echo '
	</tr>
	</thead>

	<tfoot>
	<tr>';

	if ($previous) {
		if (function_exists('mb_convert_encoding')) {
			$smonth_name = mb_substr($month[zeroise($previous->month, 2)], 0, 3,$blog_charset);
		} else {
			$smonth_name = substr($month[zeroise($previous->month, 2)], 0, 3);
		}
		$month_str = ereg_replace('%MONTH',$month[zeroise($previous->month, 2)],$wp_month_format);
		$month_str = ereg_replace('%YEAR',date('Y', mktime(0, 0 , 0, $previous->month, 1, $previous->year)),$month_str);
		echo "\n\t\t".'<td abbr="' . $month[zeroise($previous->month, 2)] . '" colspan="3" id="prev"><a href="' .
				get_month_link($previous->year, $previous->month) . '" title="View posts for ' . $month_str . '">&laquo; ' . $smonth_name . '</a></td>';
	} else {
		echo "\n\t\t".'<td colspan="3" id="prev" class="pad">&nbsp;</td>';
	}

	echo "\n\t\t".'<td class="pad">&nbsp;</td>';

	if ($next) {
		if (function_exists('mb_substr')) {
			$smonth_name = mb_substr($month[zeroise($next->month, 2)], 0, 3,$blog_charset);
		} else {
			$smonth_name = substr($month[zeroise($next->month, 2)], 0, 3);
		}
		$month_str = ereg_replace('%MONTH',$month[zeroise($next->month, 2)],$wp_month_format);
		$month_str = ereg_replace('%YEAR',date('Y', mktime(0, 0 , 0, $next->month, 1, $next->year)),$month_str);
		echo "\n\t\t".'<td abbr="' . $month[zeroise($next->month, 2)] . '" colspan="3" id="next"><a href="' .
				get_month_link($next->year, $next->month) . '" title="View posts for ' . $month_str . '">' . $smonth_name . ' &raquo;</a></td>';
	} else {
		echo "\n\t\t".'<td colspan="3" id="next" class="pad">&nbsp;</td>';
	}

	echo '
	</tr>
	</tfoot>

	<tbody>
	<tr>';

	// Get days with posts
	$dayswithposts = $wpdb->get_results("SELECT DISTINCT DAYOFMONTH(post_date)
			FROM {$wpdb->posts[$wp_id]} WHERE MONTH(post_date) = $thismonth
			AND YEAR(post_date) = $thisyear
			AND post_status = 'publish'
			AND post_date < '" . date("Y-m-d H:i:s", (time() + ($time_difference * 3600)))."'", ARRAY_N);
	if ($dayswithposts) {
		foreach ($dayswithposts as $daywith) {
			$daywithpost[] = $daywith[0];
		}
	} else {
		$daywithpost = array();
	}



	if (strstr($_SERVER["HTTP_USER_AGENT"], "MSIE") ||
		  strstr(strtolower($_SERVER["HTTP_USER_AGENT"]), "camino") ||
		  strstr(strtolower($_SERVER["HTTP_USER_AGENT"]), "safari")) {
		$ak_title_separator = "\n";
	} else {
		$ak_title_separator = ", ";
	}

	$ak_titles_for_day = array();
	$ak_post_titles = $wpdb->get_results("SELECT post_title, DAYOFMONTH(post_date) as dom "
										 ."FROM {$wpdb->posts[$wp_id]} "
										 ."WHERE YEAR(post_date) = '$thisyear' "
										 ."AND MONTH(post_date) = '$thismonth' "
										 ."AND post_date < '".date("Y-m-d H:i:s", (time() + ($time_difference * 3600)))."' "
										 ."AND post_status = 'publish'"
										);
	if ($ak_post_titles) {
		foreach ($ak_post_titles as $ak_post_title) {
			if (empty($ak_titles_for_day["day_".$ak_post_title->dom])) {
				$ak_titles_for_day["day_".$ak_post_title->dom] = '';
			}
			if (empty($ak_titles_for_day["$ak_post_title->dom"])) { // first one
				$ak_titles_for_day["$ak_post_title->dom"] = htmlspecialchars(stripslashes($ak_post_title->post_title));
			} else {
				$ak_titles_for_day["$ak_post_title->dom"] .= $ak_title_separator . htmlspecialchars(stripslashes($ak_post_title->post_title));
			}
		}
	}


	// See how much we should pad in the beginning
	$pad = intval(date('w', $unixmonth));
	if (0 != $pad) echo "\n\t\t<td colspan='$pad' class='pad'>&nbsp;</td>";

	$daysinmonth = intval(date('t', $unixmonth));
	for ($day = 1; $day <= $daysinmonth; ++$day) {
		if (isset($newrow) && $newrow)
			echo "\n\t</tr>\n\t<tr>\n\t\t";
		$newrow = false;

		if ($day == date('j', (time() + ($time_difference * 3600))) && $thismonth == date('m', time()+($time_difference * 3600)))
			echo '<td id="today">';
		else
			echo "<td>";

		if (in_array($day, $daywithpost)) { // any posts today?
			echo '<a href="' . get_day_link($thisyear, $thismonth, $day) . "\" title=\"$ak_titles_for_day[$day]\">$day</a>";
		} else {
			echo $day;
		}
		echo '</td>';

		if (6 == date('w', mktime(0, 0 , 0, $thismonth, $day, $thisyear)))
			$newrow = true;
	}

	$pad = 7 - date('w', mktime(0, 0 , 0, $thismonth, $day, $thisyear));
	if ($pad != 0 && $pad != 7)
		echo "\n\t\t<td class='pad' colspan='$pad'>&nbsp;</td>";

	echo "\n\t</tr>\n\t</tbody>\n\t</table>";
}

function allowed_tags() {
	global $allowedtags;
	$allowed = "";
	foreach($allowedtags as $tag => $attributes) {
		$allowed .= "<$tag";
		if (0 < count($attributes)) {
			foreach ($attributes as $attribute => $limits) {
				$allowed .= " $attribute=\"\"";
			}
		}
		$allowed .= "> ";
	}
	return htmlentities($allowed);
}

/***** Date/Time tags *****/
function the_date_xml() {
    global $post;
    echo mysql2date("Y-m-d",$post->post_date);
    //echo ""+$post->post_date;
}

function the_date($d='', $before='', $after='', $echo = true) {
	global $id, $post, $day, $previousday, $dateformat, $newday;
	$the_date = '';
	if ($day != $previousday) {
		$the_date .= $before;
		if ($d=='') {
			$the_date .= mysql2date($dateformat, $post->post_date);
		} else {
			$the_date .= mysql2date($d, $post->post_date);
		}
		$the_date .= $after;
		$previousday = $day;
	}
	$the_date = apply_filters('the_date', $the_date);
	if ($echo) {
		echo $the_date;
	} else {
		return $the_date;
	}
}

function the_time($d='', $echo = true) {
	global $id, $post, $timeformat;
	if ($d=='') {
		$the_time = mysql2date($timeformat, $post->post_date);
	} else {
		$the_time = mysql2date($d, $post->post_date);
	}
	$the_time = apply_filters('the_time', $the_time);
	if ($echo) {
		echo $the_time;
	} else {
		return $the_time;
	}
}

function the_weekday() {
	global $weekday, $id, $post;
	$the_weekday = $weekday[mysql2date('w', $post->post_date)];
	$the_weekday = apply_filters('the_weekday', $the_weekday);
	echo $the_weekday;
}

function the_weekday_date($before='',$after='') {
	global $weekday, $id, $post, $day, $previousweekday;
	$the_weekday_date = '';
	if ($day != $previousweekday) {
		$the_weekday_date .= $before;
		$the_weekday_date .= $weekday[mysql2date('w', $post->post_date)];
		$the_weekday_date .= $after;
		$previousweekday = $day;
	}
	$the_weekday_date = apply_filters('the_weekday_date', $the_weekday_date);
	echo $the_weekday_date;
}
?>