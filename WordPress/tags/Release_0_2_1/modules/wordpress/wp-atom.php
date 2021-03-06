<?php 
$blog = 1;
$doing_rss = 1;
header("Content-type: application/xml");
include_once (dirname(__FILE__)."/../../mainfile.php");
error_reporting(E_ERROR);
if ($HTTP_GET_VARS['num']) $showposts = $HTTP_GET_VARS['num'];
require('wp-blog-header.php');
if (isset($showposts) && $showposts) {
    $showposts = (int)$showposts;
	$posts_per_page = $showposts;
} else {
	$posts_per_page = get_settings('posts_per_rss');
}
if (function_exists('mb_convert_encoding')) {
	$rss_charset = 'utf-8';
}else{
	$rss_charset = $blog_charset;
}
?>
<?php echo '<?xml version="1.0" encoding="'.$rss_charset.'"?'.'>'; ?>
<feed version="0.3"
  xmlns="http://purl.org/atom/ns#"
  xmlns:dc="http://purl.org/dc/elements/1.1/"
  xml:lang="<?php echo (get_settings('rss_language')?get_settings('rss_language'):'en') ?>">
	<title><?php bloginfo_rss('name') ?></title>
	<link rel="alternate" type="text/html" href="<?php bloginfo_rss('url') ?>" />
	<tagline><?php bloginfo_rss("description") ?></tagline>
	<modified><?php echo gmdate('Y-m-d\TH:i:s\Z'); ?></modified>
	<copyright>Copyright <?php echo mysql2date('Y', get_lastpostdate()); ?></copyright>
	<generator url="http://wordpress.xwd.jp/" version="<?php echo $wp_version ?>">WordPress</generator>
	
	<?php $items_count = 0; if ($posts) { foreach ($posts as $post) { start_wp(); ?>
	<entry>
	  	<author>
			<name><?php the_author_rss() ?></name>
		</author>
		<title><?php the_title_rss() ?></title>
		<link rel="alternate" type="text/html" href="<?php permalink_single_rss() ?>" />
		<id><?php bloginfo_rss("url") ?>?p=<?php echo $id; ?></id>
		<modified><?php the_time('Y-m-d\TH:i:s\Z'); ?></modified>
		<issued><?php the_time('Y-m-d\TH:i:s\Z'); ?></issued>
		<?php the_category_rss('rdf') ?>
<?php $more = 1; if (get_settings('rss_use_excerpt')) {
?>
		<summary type="text/html"><?php the_excerpt_rss(get_settings('rss_excerpt_length'), 2) ?></summary>
<?php
} else { // use content
?>
		<summary type="text/html"><?php the_content_rss('', 0, '', get_settings('rss_excerpt_length'), 2) ?></summary>
<?php
} // end else use content
?>
		<content type="text/html" mode="escaped" xml:base="<?php permalink_single_rss() ?>"><![CDATA[<?php the_content_rss('', 0, '', 0, 1) ?>]]></content>
	</entry>
	<?php $items_count++; if (($items_count == get_settings('posts_per_rss')) && empty($m)) { break; } } } ?>
</feed>
