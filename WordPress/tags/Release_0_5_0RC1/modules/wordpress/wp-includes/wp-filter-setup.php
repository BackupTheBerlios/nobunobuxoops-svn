<?php
// Default filters
add_filter('post_comment_text', 'balanceTags', 50);
add_filter('content_save_pre', 'balanceTags', 50);
add_filter('excerpt_save_pre', 'balanceTags', 50);
add_filter('comment_save_pre', 'balanceTags', 50);

add_filter('comment_author', 'wptexturize');
add_filter('comment_author', 'wp_filter_kses');
add_filter('comment_author', 'convert_chars');

add_filter('comment_email', 'antispambot');

add_filter('comment_url', 'clean_url');

add_filter('comment_text', 'wptexturize');
add_filter('comment_text', 'wp_filter_kses');
add_filter('comment_text', 'convert_bbcode');
add_filter('comment_text', 'convert_gmcode');
add_filter('comment_text', 'convert_chars');
add_filter('comment_text', 'make_clickable');
add_filter('comment_text', 'wpautop', 30);
add_filter('comment_text', 'balanceTags', 50);
add_filter('comment_text', 'convert_smilies', 20);

add_filter('comment_excerpt', 'convert_chars');

// Default filters
add_filter('the_title', 'wptexturize');
add_filter('the_title', 'convert_smilies');
add_filter('the_title', 'convert_chars');
add_filter('the_title', 'trim');

add_filter('the_title_rss', 'strip_tags');

add_filter('the_content', 'wptexturize');
add_filter('the_content', 'convert_bbcode');
add_filter('the_content', 'convert_gmcode');
add_filter('the_content', 'convert_smilies');
add_filter('the_content', 'convert_chars');
add_filter('the_content', 'wpautop');

add_filter('the_excerpt', 'wptexturize');
add_filter('the_excerpt', 'convert_bbcode');
add_filter('the_excerpt', 'convert_gmcode');
add_filter('the_excerpt', 'convert_smilies');
add_filter('the_excerpt', 'convert_chars');
add_filter('the_excerpt', 'wpautop');

add_filter('category_description', 'wptexturize');
add_filter('list_cats', 'wptexturize');
add_filter('single_post_title', 'wptexturize');

add_action('sanitize_title', 'sanitize_title_with_dashes');
?>
