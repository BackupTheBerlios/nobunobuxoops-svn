<?php
function b_wp_contents_show($options) {
	global $xoopsDB,$xoopsUser,$wpdb,$siteurl;
	global $tableoptions,$tableposts,$tableusers,$tablecategories,$tablecomments,$tablepost2cat;
    global $querystring_start, $querystring_equal, $querystring_separator;
	global $id, $posts, $post, $day, $previousday, $dateformat, $newday;
	require_once (dirname(__FILE__).'/../wp-blog-header.php');
	$blog = 1;
	$block = array();
	$block['siteurl'] = $siteurl;
	foreach ($posts as $post) {
		$content = array();
		start_wp();
		$content['date'] = the_date('','<h2>','</h2>', false);
		$content['time'] = the_time('', false);
		$content['title'] = the_title('','', false);
		$content['permlink'] = get_permalink();
//
		ob_start();
		the_author();
		$content['author'] = ob_get_contents();
		ob_end_clean();
//
		ob_start();
		the_category();
		$content['category'] = ob_get_contents();
		ob_end_clean();
//
		ob_start();
		the_content();
		$content['body'] = ob_get_contents();
		ob_end_clean();
//
		ob_start();
		link_pages('<br />Pages: ', '<br />', 'number');
		$content['linkpage'] = ob_get_contents();
		ob_end_clean();
//
		ob_start();
		comments_popup_link('Comments (0)', 'Comments (1)', 'Comments (%)');
		$content['comments'] = ob_get_contents();
		ob_end_clean();
//
		ob_start();
		trackback_rdf();
		$content['trackback'] = ob_get_contents();
		ob_end_clean();
//
		$block['contents'][] = $content;
	}
	return $block;
}
?>
