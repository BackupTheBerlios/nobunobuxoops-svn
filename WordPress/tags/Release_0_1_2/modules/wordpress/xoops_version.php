<?php
$modversion['name'] = _MI_WORDPRESS_NAME;
$modversion['dirname'] = 'wordpress';

$modversion['description'] = _MI_WORDPRESS_DESC;
$modversion['version'] = "0.12";
$modversion['credits'] = "";
$modversion['author'] = _MI_WORDPRESS_AUTHOR;
$modversion['help'] = "help.html";
$modversion['license'] = "GPL see LICENSE";
$modversion['official'] = 0;
$modversion['image'] = "wp-images/module_logo.png";
$modversion['onInstall'] = 'xoops_install.php';

// Sql file (must contain sql generated by phpMyAdmin or phpPgAdmin)
// All tables should not have any prefix!
$modversion['sqlfile']['mysql'] = "sql/mysql.sql";

$modversion['tables'] = array(
	"wp_posts", 
	"wp_users",
	"wp_categories",
	"wp_comments",
	"wp_links",
	"wp_linkcategories",
	"wp_options",
	"wp_optiontypes",
	"wp_optionvalues",
	"wp_optiongroups",
	"wp_optiongroup_options",
	"wp_post2cat"
	);

// Search
$modversion['hasSearch'] = 1;
$modversion['search']['file'] = "xoops_search.php";
$modversion['search']['func'] = "wp_xoops_search";

//Admin things
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = "admin/index.php";
$modversion['adminmenu'] = "admin/menu.php";

$modversion['hasMain'] = 1;
global $xoopsUser;
if($xoopsUser){
	$modversion['sub'][1]['name'] = _MI_WORDPRESS_SMNAME1;
	$modversion['sub'][1]['url'] = "wp-admin/post.php";
}

$modversion['hasconfig'] = 1;
$modversion['config'][1] = array(
	'name'			=> 'wp_use_spaw' ,
	'title'			=> '_MI_WPUSESPAW_CFG_MSG' ,
	'description'	=> '_MI_WPUSESPAW_CFG_DESC' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 0 ,
);

$modversion['config'][2] = array(
	'name'			=> 'wp_edit_authgrp' ,
	'title'			=> '_MI_WPEDITAUTHGRP_CFG_MSG' ,
	'description'	=> '_MI_WPEDITAUTHGRP_CFG_DESC' ,
	'formtype'		=> 'group_multi' ,
	'valuetype'		=> 'array' ,
	'default'		=> array(1) ,
);

$modversion['config'][3] = array(
	'name'			=> 'wp_admin_authgrp' ,
	'title'			=> '_MI_WPADMINAUTHGRP_CFG_MSG' ,
	'description'	=> '_MI_WPADMINAUTHGRP_CFG_DESC' ,
	'formtype'		=> 'group_multi' ,
	'valuetype'		=> 'array' ,
	'default'		=> array(1) ,
);

$modversion['config'][4] = array(
	'name'			=> 'wp_use_xoops_smilies' ,
	'title'			=> '_MI_WP_USE_XOOPS_SMILE' ,
	'description'	=> '_MI_WP_USE_XOOPS_SMILE_DESC' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 0 ,
);

$modversion['blocks'][1]['file'] = "wp_calendar.php";
$modversion['blocks'][1]['name'] =  _MI_WORDPRESS_BNAME1;
$modversion['blocks'][1]['description'] = _MI_WORDPRESS_BDESC1;
$modversion['blocks'][1]['show_func'] = "b_wp_calendar_show";

$modversion['blocks'][2]['file'] = "wp_archives_monthly.php";
$modversion['blocks'][2]['name'] = _MI_WORDPRESS_BNAME2;
$modversion['blocks'][2]['description'] = _MI_WORDPRESS_BDESC2;
$modversion['blocks'][2]['show_func'] = "b_wp_archives_monthly_show";
$modversion['blocks'][2]['edit_func'] = "b_wp_archives_monthly_edit";
$modversion['blocks'][2]['options'] = "0|0";

$modversion['blocks'][3]['file'] = "wp_categories.php";
$modversion['blocks'][3]['name'] =_MI_WORDPRESS_BNAME3;
$modversion['blocks'][3]['description'] = _MI_WORDPRESS_BDESC3;
$modversion['blocks'][3]['show_func'] = "b_wp_categories_show";
$modversion['blocks'][3]['edit_func'] = "b_wp_categories_edit";
$modversion['blocks'][3]['options'] = "0|0|name|asc";

$modversion['blocks'][4]['file'] = "wp_links.php";
$modversion['blocks'][4]['name'] =_MI_WORDPRESS_BNAME4;
$modversion['blocks'][4]['description'] = _MI_WORDPRESS_BDESC4;
$modversion['blocks'][4]['show_func'] = "b_wp_links_show";

$modversion['blocks'][5]['file'] = "wp_search.php";
$modversion['blocks'][5]['name'] = _MI_WORDPRESS_BNAME5;
$modversion['blocks'][5]['description'] = _MI_WORDPRESS_BDESC5;
$modversion['blocks'][5]['show_func'] = "b_wp_search_show";

$modversion['blocks'][6]['file'] = "wp_recent_posts.php";
$modversion['blocks'][6]['name'] = _MI_WORDPRESS_BNAME6;
$modversion['blocks'][6]['description'] = _MI_WORDPRESS_BDESC6;
$modversion['blocks'][6]['show_func'] = "b_wp_recent_posts_show";
$modversion['blocks'][6]['edit_func'] = "b_wp_recent_posts_edit";
$modversion['blocks'][6]['options'] = "10";

$modversion['blocks'][7]['file'] = "wp_recent_comments.php";
$modversion['blocks'][7]['name'] = _MI_WORDPRESS_BNAME7;
$modversion['blocks'][7]['description'] = _MI_WORDPRESS_BDESC7;
$modversion['blocks'][7]['show_func'] = "b_wp_recent_comments_show";
$modversion['blocks'][7]['edit_func'] = "b_wp_recent_comments_edit";
$modversion['blocks'][7]['options'] = "0|10";

$modversion['blocks'][8]['file'] = "wp_contents.php";
$modversion['blocks'][8]['name'] = _MI_WORDPRESS_BNAME8;
$modversion['blocks'][8]['description'] = _MI_WORDPRESS_BNAME8;
$modversion['blocks'][8]['show_func'] = "b_wp_contents_show";
$modversion['blocks'][8]['edit_func'] = "b_wp_contents_edit";
$modversion['blocks'][8]['options'] = "10";
$modversion['blocks'][8]['template'] = "wp_block_contents.html";
?>
