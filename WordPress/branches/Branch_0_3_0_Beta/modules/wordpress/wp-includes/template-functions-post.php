<?php

// Default filters
add_filter('the_title', 'convert_chars');
add_filter('the_title', 'trim');

add_filter('the_title_rss', 'strip_tags');

add_filter('the_content', 'convert_bbcode');
add_filter('the_content', 'convert_gmcode');
add_filter('the_content', 'convert_smilies');
add_filter('the_content', 'convert_chars');
add_filter('the_content', 'wpautop');

add_filter('the_content', 'convert_bbcode');
add_filter('the_content', 'convert_gmcode');
add_filter('the_excerpt', 'convert_smilies');
add_filter('the_excerpt', 'convert_chars');
add_filter('the_excerpt', 'wpautop');

function get_the_password_form() {
	$output = "<form action='" . get_settings('siteurl') . "/wp-pass.php' method='post'>
	<p>This post is password protected. To view it please enter your password below:</p>
	<p><label>Password: <input name='post_password' type='text' size='20' /></label> <input type='submit' name='Submit' value='Submit' /></p>
	</form>
	";
	return $output;
}

function the_ID() {
	global $id;
	echo $id;
}

function the_title($before = '', $after = '', $echo = true) {
	$title = get_the_title();
	$title = convert_smilies($title);
	if (!empty($title)) {
		$title = convert_chars($before.$title.$after);
		$title = apply_filters('the_title', $title);
        if ($echo)
            echo $title;
        else
            return $title;
	}
}

function the_title_rss() {
	$title = get_the_title();
	$title = apply_filters('the_title', $title);
	$title = apply_filters('the_title_rss', $title);
	echo wp_convert_rss_charset($title);
}

function get_the_title() {
	global $post;
	$output = stripslashes($post->post_title);
	if (!empty($post->post_password)) { // if there's a password
		$output = 'Protected: ' . $output;
	}
	return $output;
}

function the_content($more_link_text=_WP_TPL_MORE, $stripteaser=0, $more_file='') {
    $content = get_the_content($more_link_text, $stripteaser, $more_file);
    $content = apply_filters('the_content', $content);
    $content = str_replace(']]>', ']]&gt;', $content);
    echo $content;
}

function the_content_rss($more_link_text='(more...)', $stripteaser=0, $more_file='', $cut = 0, $encode_html = 0) {
	$content = get_the_content($more_link_text, $stripteaser, $more_file);
	$content = apply_filters('the_content', $content);
	if ($cut && !$encode_html) {
		$encode_html = 2;
	}
	if ($encode_html == 1) {
		$content = htmlspecialchars($content);
		$cut = 0;
	} elseif ($encode_html == 0) {
		$content = make_url_footnote($content);
	} elseif ($encode_html == 2) {
		$content = htmlspecialchars(strip_tags($content));
	} elseif ($encode_html == 3) {
		$content = convert_smilies($content);
		$cut = 0;
	}
	if ($cut) {
		$blah = explode(' ', $content);
		if (count($blah) > $cut) {
			$k = $cut;
			$use_dotdotdot = 1;
		} else {
			$k = count($blah);
			$use_dotdotdot = 0;
		}
		for ($i=0; $i<$k; $i++) {
			$excerpt .= $blah[$i].' ';
		}
		$excerpt .= ($use_dotdotdot) ? '...' : '';
		$content = $excerpt;
	}
	$content = str_replace(']]>', ']]&gt;', $content);
		echo wp_convert_rss_charset($content);
}

function get_the_content($more_link_text='(more...)', $stripteaser=0, $more_file='') {
	global $id, $post, $more, $c, $withcomments, $page, $pages, $multipage, $numpages;
	global $preview, $cookiehash;
    global $pagenow;
	$output = '';

	if (!empty($post->post_password)) { // if there's a password
		if ($_COOKIE['wp-postpass_'.$cookiehash] != $post->post_password) {  // and it doesn't match the cookie
			$output = get_the_password_form();
			return $output;
		}
	}

	if ($more_file != '') {
		$file = $more_file;
	} else {
		$file = $pagenow; //$_SERVER['PHP_SELF'];
	}
	$content = $pages[$page-1];
	$content = explode('<!--more-->', $content);
	if ((preg_match('/<!--noteaser-->/', $post->post_content) && ((!$multipage) || ($page==1))))
		$stripteaser = 1;
	$teaser = $content[0];
	if (($more) && ($stripteaser))
		$teaser = '';
	$output .= $teaser;
	if (count($content)>1) {
		if ($more) {
			$output .= '<a id="more-'.$id.'"></a>'.$content[1];
		} else {
			$output .= " <a href='". get_permalink() . "#more-$id'>$more_link_text</a>";
		}
	}
	if ($preview) { // preview fix for javascript bug with foreign languages
		$output =  preg_replace('/\%u([0-9A-F]{4,4})/e',  "'&#'.base_convert('\\1',16,10).';'", $output);
	}
	return $output;
}

function the_excerpt() {
    echo apply_filters('the_excerpt', get_the_excerpt());
}

function the_excerpt_rss($cut = 0, $encode_html = 0) {
    $output = get_the_excerpt(true);

    $output = convert_chars($output);
    if ($cut && !$encode_html) {
        $encode_html = 2;
    }
    if ($encode_html == 1) {
        $output = htmlspecialchars($output);
        $cut = 0;
    } elseif ($encode_html == 0) {
        $output = make_url_footnote($output);
    } elseif ($encode_html == 2) {
        $output = strip_tags($output);
        $output = str_replace('&', '&amp;', $output);
    }
    if ($cut) {
        $excerpt = '';
        $blah = explode(' ', $output);
        if (count($blah) > $cut) {
            $k = $cut;
            $use_dotdotdot = 1;
        } else {
            $k = count($blah);
            $use_dotdotdot = 0;
        }
        for ($i=0; $i<$k; $i++) {
            $excerpt .= $blah[$i].' ';
        }
        $excerpt .= ($use_dotdotdot) ? '...' : '';
        $output = $excerpt;
    }
    $output = str_replace(']]>', ']]&gt;', $output);
	echo wp_convert_rss_charset(apply_filters('the_excerpt_rss', $output));
}

function get_the_excerpt($fakeit = false) {
	global $id, $post;
	global $preview, $cookiehash;
	$output = '';
	$output = stripslashes($post->post_excerpt);
	if (!empty($post->post_password)) { // if there's a password
		if ($_COOKIE['wp-postpass_'.$cookiehash] != $post->post_password) {  // and it doesn't match the cookie
			$output = "There is no excerpt because this is a protected post.";
			return $output;
		}
	}
    //if we haven't got an excerpt, make one in the style of the rss ones
    if (($output == '') && $fakeit) {
        $output = get_the_content();
        $output = strip_tags($output);
        $blah = explode(' ', $output);
        $excerpt_length = 120;
        if (count($blah) > $excerpt_length) {
			$k = $excerpt_length;
			$use_dotdotdot = 1;
		} else {
			$k = count($blah);
			$use_dotdotdot = 0;
		}
        $excerpt = '';
		for ($i=0; $i<$k; $i++) {
			$excerpt .= $blah[$i].' ';
		}
		$excerpt .= ($use_dotdotdot) ? '...' : '';
		$output = $excerpt;
    } // end if no excerpt
	if ($preview) { // preview fix for javascript bug with foreign languages
		$output =  preg_replace('/\%u([0-9A-F]{4,4})/e',  "'&#'.base_convert('\\1',16,10).';'", $output);
	}
	return $output;
}

function wp_link_pages($args = '') {
	parse_str($args, $r);
	if (!isset($r['before'])) $r['before'] = '<p>' . 'Pages:';
	if (!isset($r['after'])) $r['after'] = '</p>';
	if (!isset($r['next_or_number'])) $r['next_or_number'] = 'number';
	if (!isset($r['nextpagelink'])) $r['nextpagelink'] = 'Next page';
	if (!isset($r['previouspagelink'])) $r['previouspagelink'] = 'Previous page';
	if (!isset($r['pagelink'])) $r['pagelink'] = '%';
	if (!isset($r['more_file'])) $r['more_file'] = '';
	link_pages($r['before'], $r['after'], $r['next_or_number'], $r['nextpagelink'], $r['previouspagelink'], $r['pagelink'], $r['more_file']);
}

function link_pages($before='<br />', $after='<br />', $next_or_number='number', $nextpagelink='next page', $previouspagelink='previous page', $pagelink='%', $more_file='') {
	global $id, $page, $numpages, $multipage, $more;
	global $pagenow;
	if ($more_file != '') {
		$file = $more_file;
	} else {
		$file = $pagenow;
	}
	if (($multipage)) {
		if ($next_or_number=='number') {
			echo $before;
			for ($i = 1; $i < ($numpages+1); $i = $i + 1) {
				$j=str_replace('%',"$i",$pagelink);
				echo " ";
				if (($i != $page) || ((!$more) && ($page==1))) {
				if ('' == get_settings('permalink_structure')) {
					echo '<a href="'.get_permalink().'&amp;page='.$i.'">';
				} else {
					echo '<a href="'.get_permalink().$i.'/">';
				}
				}
				echo $j;
				if (($i != $page) || ((!$more) && ($page==1)))
					echo '</a>';
			}
			echo $after;
		} else {
			if ($more) {
				echo $before;
				$i=$page-1;
				if ($i && $more) {
				if ('' == get_settings('permalink_structure')) {
					echo '<a href="'.get_permalink().'&amp;page='.$i.'">';
				} else {
					echo '<a href="'.get_permalink().$i.'/">';
				}
				}
				$i=$page+1;
				if ($i<=$numpages && $more) {
				if ('' == get_settings('permalink_structure')) {
					echo '<a href="'.get_permalink().'&amp;page='.$i.'">';
				} else {
					echo '<a href="'.get_permalink().$i.'/">';
				}
				}
				echo $after;
			}
		}
	}
}


function previous_post($format='%', $previous='previous post: ', $title='yes', $in_same_cat='no', $limitprev=1, $excluded_categories='') {
	global  $id, $post, $siteurl, $wpdb ,$wp_id;
	global $p, $posts, $posts_per_page, $s, $single;

	if(($p) || ($posts_per_page == 1) || 1 == $single) {

		$current_post_date = $post->post_date;
		$current_category = $post->post_category;

		$sqlcat = '';
		if ($in_same_cat != 'no') {
			$sqlcat = " AND post_category = '$current_category' ";
		}

		$sql_exclude_cats = '';
		if (!empty($excluded_categories)) {
			$blah = explode('and', $excluded_categories);
			foreach($blah as $category) {
				$category = intval($category);
				$sql_exclude_cats .= " AND post_category != $category";
			}
		}

		$limitprev--;
		$lastpost = @$wpdb->get_row("SELECT ID, post_title FROM {$wpdb->posts[$wp_id]} WHERE post_date < '$current_post_date' AND post_status = 'publish' $sqlcat $sql_exclude_cats ORDER BY post_date DESC LIMIT $limitprev, 1");
		if ($lastpost) {
			$string = '<a href="'.get_permalink($lastpost->ID).'">'.$previous;
			if ($title == 'yes') {
                $string .= wptexturize(stripslashes($lastpost->post_title));
            }
			$string .= '</a>';
			$format = str_replace('%', $string, $format);
			echo $format;
		}
	}
}

function next_post($format='%', $next='next post: ', $title='yes', $in_same_cat='no', $limitnext=1, $excluded_categories='') {
	global  $p, $posts, $id, $post, $siteurl, $wpdb ,$wp_id;
	global $time_difference, $single;
	if(($p) || ($posts==1) || 1 == $single) {

		$current_post_date = $post->post_date;
		$current_category = $post->post_category;

		$sqlcat = '';
		if ($in_same_cat != 'no') {
			$sqlcat = " AND post_category='$current_category' ";
		}

		$sql_exclude_cats = '';
		if (!empty($excluded_categories)) {
			$blah = explode('and', $excluded_categories);
			foreach($blah as $category) {
				$category = intval($category);
				$sql_exclude_cats .= " AND post_category != $category";
			}
		}

		$now = date('Y-m-d H:i:s',(time() + ($time_difference * 3600)));

		$limitnext--;

		$nextpost = @$wpdb->get_row("SELECT ID,post_title FROM {$wpdb->posts[$wp_id]} WHERE post_date > '$current_post_date' AND post_date < '$now' AND post_status = 'publish' $sqlcat $sql_exclude_cats ORDER BY post_date ASC LIMIT $limitnext,1");
		if ($nextpost) {
			$string = '<a href="'.get_permalink($nextpost->ID).'">'.$next;
			if ($title=='yes') {
				$string .= wptexturize(stripslashes($nextpost->post_title));
			}
			$string .= '</a>';
			$format = str_replace('%', $string, $format);
			echo $format;
		}
	}
}

function next_posts($max_page = 0) { // original by cfactor at cooltux.org
	global $siteurl, $p, $paged, $what_to_show, $pagenow;
	if (empty($p) && ($what_to_show == 'paged')) {
		$qstr = $_SERVER['QUERY_STRING'];
		if (!empty($qstr)) {
			$qstr = preg_replace("/&paged=\d{0,}/","",$qstr);
			$qstr = preg_replace("/paged=\d{0,}/","",$qstr);
		} elseif (stristr($_SERVER['REQUEST_URI'], $_SERVER['SCRIPT_NAME'] )) {
			if ('' != $qstr = str_replace($_SERVER['SCRIPT_NAME'], '',
											$_SERVER['REQUEST_URI']) ) {
				$qstr = preg_replace("/^\//", "", $qstr);
				$qstr = preg_replace("/paged\/\d{0,}\//", "", $qstr);
				$qstr = preg_replace("/paged\/\d{0,}/", "", $qstr);
				$qstr = preg_replace("/\/$/", "", $qstr);
			}
		}
		if (!$paged) $paged = 1;
		$nextpage = intval($paged) + 1;
		if (!$max_page || $max_page >= $nextpage) {
			echo  $siteurl.'/'.$pagenow.'?'.
				($qstr == '' ? '' : $qstr.'&amp;') .
				'paged='.$nextpage;
		}
	}
}

function next_posts_link($label='Next Page &raquo;', $max_page=0) {
	global $p, $paged, $result, $request, $posts_per_page, $what_to_show, $wpdb, $wp_id;
	if ($what_to_show == 'paged') {
		if (!$max_page) {
			$nxt_request = $request;
            //if the query includes a limit clause, call it again without that
            //limit clause!
			if ($pos = strpos(strtoupper($request), 'LIMIT')) {
				$nxt_request = substr($request, 0, $pos);
			}
			$nxt_result = $wpdb->query($nxt_request);
			$numposts = $wpdb->num_rows;
			$max_page = ceil($numposts / $posts_per_page);
		}
		if (!$paged)
            $paged = 1;
		$nextpage = intval($paged) + 1;
		if (empty($p) && (empty($paged) || $nextpage <= $max_page)) {
			echo '<a href="';
			next_posts($max_page);
			echo '">'. preg_replace('/&([^#])(?![a-z]{1,8};)/', '&#038;$1', $label) .'</a>';
		}
	}
}

function previous_posts() { // original by cfactor at cooltux.org
	global $siteurl, $p, $paged, $what_to_show, $pagenow;
	if (empty($p) && ($what_to_show == 'paged')) {
		$qstr = $_SERVER['QUERY_STRING'];
		if (!empty($qstr)) {
			$qstr = preg_replace("/&paged=\d{0,}/","",$qstr);
			$qstr = preg_replace("/paged=\d{0,}/","",$qstr);
		} elseif (stristr($_SERVER['REQUEST_URI'], $_SERVER['SCRIPT_NAME'] )) {
			if ('' != $qstr = str_replace($_SERVER['SCRIPT_NAME'], '',
											$_SERVER['REQUEST_URI']) ) {
				$qstr = preg_replace("/^\//", "", $qstr);
				$qstr = preg_replace("/paged\/\d{0,}\//", "", $qstr);
				$qstr = preg_replace("/paged\/\d{0,}/", "", $qstr);
				$qstr = preg_replace("/\/$/", "", $qstr);
			}
		}
		$nextpage = intval($paged) - 1;
		if ($nextpage < 1) $nextpage = 1;
		echo  $siteurl.'/'.$pagenow.'?'.
			($qstr == '' ? '' : $qstr.'&amp;') .
			'paged='.$nextpage;
	}
}

function previous_posts_link($label='&laquo; Previous Page') {
	global $p, $paged, $what_to_show;
	if (empty($p)  && ($paged > 1) && ($what_to_show == 'paged')) {
		echo '<a href="';
		previous_posts();
		echo '">'. preg_replace('/&([^#])(?![a-z]{1,8};)/', '&#038;$1', $label) .'</a>';
	}
}

function posts_nav_link($sep=' :: ', $prelabel='<< Previous Page', $nxtlabel='Next Page >>') {
	global $p, $what_to_show, $request, $posts_per_page, $wpdb, $wp_id;
	if (empty($p) && ($what_to_show == 'paged')) {
		$nxt_request = $request;
		if ($pos = strpos(strtoupper($request), 'LIMIT')) {
			$nxt_request = substr($request, 0, $pos);
		}
        $nxt_result = $wpdb->query($nxt_request);
        $numposts = $wpdb->num_rows;
		$max_page = ceil($numposts / $posts_per_page);
		if ($max_page > 1) {
			previous_posts_link($prelabel);
			echo preg_replace('/&([^#])(?![a-z]{1,8};)/', '&#038;$1', $sep);
			next_posts_link($nxtlabel, $max_page);
		}
	}
}
if (0) {
	/*
	 * Post-meta: Custom per-post fields.
	 */
	 
	function get_post_custom() {
		global $id, $post_meta_cache;

		return $post_meta_cache[$id];
	}

	function get_post_custom_keys() {
		global $id, $post_meta_cache;
		
		if (!is_array($post_meta_cache[$id]))
			return;
		if ($keys = array_keys($post_meta_cache[$id]))
			return $keys;
	}

	function get_post_custom_values($key='') {
		global $id, $post_meta_cache;

		return $post_meta_cache[$id][$key];
	}

	// this will probably change at some point...
	function the_meta() {
		global $id, $post_meta_cache;
		
		if ($keys = get_post_custom_keys()) {
			echo "<ul class='post-meta'>\n";
			foreach ($keys as $key) {
				$values = array_map('trim',$post_meta_cache[$id][$key]);
				$value = implode($values,', ');
				
				echo "<li><span class='post-meta-key'>$key:</span> $value</li>\n";
			}
			echo "</ul>\n";
		}
	}
}
?>
