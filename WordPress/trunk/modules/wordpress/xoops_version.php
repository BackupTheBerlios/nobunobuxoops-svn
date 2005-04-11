<?php
if( ! defined( 'XOOPS_ROOT_PATH' ) ) exit ;
$my_wp_dirname = basename( dirname( __FILE__ ) ) ;
if( ! preg_match( '/wordpress(\d*)/' , $my_wp_dirname , $regs ) ) echo ( "invalid dirname of WordPress: " . htmlspecialchars( $my_wp_dirname ) ) ;
include XOOPS_ROOT_PATH.'/modules/'.$my_wp_dirname.'/wp-ver.php';
$my_wp_dirnumber = $regs[1] ;

$modversion['name'] = sprintf(_MI_WORDPRESS_NAME, $my_wp_dirnumber);
$modversion['dirname'] = $my_wp_dirname;

$modversion['description'] = _MI_WORDPRESS_DESC;
$modversion['version'] = $GLOBALS['wp_version_xoops'];
$modversion['credits'] = "";
$modversion['author'] = _MI_WORDPRESS_AUTHOR;
$modversion['help'] = "help.html";
$modversion['license'] = "GPL see LICENSE";
$modversion['official'] = 0;
$modversion['image'] = "wp-images/wordpress{$my_wp_dirnumber}.png";
$modversion['onInstall'] = 'xoops_install.php';

// Sql file (must contain sql generated by phpMyAdmin or phpPgAdmin)
// All tables should not have any prefix!
$modversion['sqlfile']['mysql'] = "sql/mysql{$my_wp_dirnumber}.sql";

$modversion['tables'] = array(
	"wp{$my_wp_dirnumber}_posts", 
	"wp{$my_wp_dirnumber}_users",
	"wp{$my_wp_dirnumber}_categories",
	"wp{$my_wp_dirnumber}_comments",
	"wp{$my_wp_dirnumber}_links",
	"wp{$my_wp_dirnumber}_linkcategories",
	"wp{$my_wp_dirnumber}_options",
	"wp{$my_wp_dirnumber}_optiontypes",
	"wp{$my_wp_dirnumber}_optionvalues",
	"wp{$my_wp_dirnumber}_optiongroups",
	"wp{$my_wp_dirnumber}_optiongroup_options",
	"wp{$my_wp_dirnumber}_post2cat",
	"wp{$my_wp_dirnumber}_postmeta"
	);

// Search
$modversion['hasSearch'] = 1;
$modversion['search']['file'] = "xoops_search.php";
$modversion['search']['func'] = "wp{$my_wp_dirnumber}_xoops_search";

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
	'formtype'		=> 'select' ,
	'valuetype'		=> 'int' ,
	'default'		=> 0 ,
	'options' => array(
                    '_MI_OPT_WYSIWYG_NONE'=>0 ,
                    '_MI_OPT_WYSIWYG_SPAW'=>1 ,
                    '_MI_OPT_WYSIWYG_KOIVI'=>2 ,
                ),
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

$modversion['config'][5] = array(
	'name'			=> 'use_theme_template' ,
	'title'			=> '_MI_WP_USE_THEME_TEMPLATE' ,
	'description'	=> '_MI_WP_USE_THEME_TEMPLATE_DESC' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 0 ,
);

$modversion['config'][6] = array(
	'name'			=> 'wp_use_blockcssheader' ,
	'title'			=> '_MI_WP_USE_BLOCKCSSHEADER' ,
	'description'	=> '_MI_WP_USE_BLOCKCSSHEADER_DESC' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 0 ,
);

$modversion['config'][7] = array(
	'name'			=> 'wp_use_xoops_comments' ,
	'title'			=> '_MI_WP_USE_XOOPS_COMM' ,
	'description'	=> '_MI_WP_USE_XOOPS_COMM_DESC' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 0 ,
);

$modversion['blocks']= array();

$modversion['blocks'][1]['file'] = "wp_calendar.php";
$modversion['blocks'][1]['name'] = sprintf( _MI_WORDPRESS_BNAME1 , $my_wp_dirnumber );
$modversion['blocks'][1]['description'] = _MI_WORDPRESS_BDESC1;
$modversion['blocks'][1]['show_func'] = "b_wp{$my_wp_dirnumber}_calendar_show";
$modversion['blocks'][1]['can_clone'] = true ;

$modversion['blocks'][2]['file'] = "wp_archives_monthly.php";
$modversion['blocks'][2]['name'] = sprintf( _MI_WORDPRESS_BNAME2 , $my_wp_dirnumber );
$modversion['blocks'][2]['description'] = _MI_WORDPRESS_BDESC2;
$modversion['blocks'][2]['show_func'] = "b_wp{$my_wp_dirnumber}_archives_monthly_show";
$modversion['blocks'][2]['edit_func'] = "b_wp{$my_wp_dirnumber}_archives_monthly_edit";
$modversion['blocks'][2]['options'] = "0|0";
$modversion['blocks'][2]['can_clone'] = true ;

$modversion['blocks'][3]['file'] = "wp_categories.php";
$modversion['blocks'][3]['name'] =sprintf( _MI_WORDPRESS_BNAME3 , $my_wp_dirnumber );
$modversion['blocks'][3]['description'] = _MI_WORDPRESS_BDESC3;
$modversion['blocks'][3]['show_func'] = "b_wp{$my_wp_dirnumber}_categories_show";
$modversion['blocks'][3]['edit_func'] = "b_wp{$my_wp_dirnumber}_categories_edit";
$modversion['blocks'][3]['options'] = "0|0|name|asc";
$modversion['blocks'][3]['can_clone'] = true ;

$modversion['blocks'][4]['file'] = "wp_links.php";
$modversion['blocks'][4]['name'] =sprintf( _MI_WORDPRESS_BNAME4 , $my_wp_dirnumber );
$modversion['blocks'][4]['description'] = _MI_WORDPRESS_BDESC4;
$modversion['blocks'][4]['show_func'] = "b_wp{$my_wp_dirnumber}_links_show";
$modversion['blocks'][4]['can_clone'] = true ;

$modversion['blocks'][5]['file'] = "wp_search.php";
$modversion['blocks'][5]['name'] = sprintf( _MI_WORDPRESS_BNAME5 , $my_wp_dirnumber );
$modversion['blocks'][5]['description'] = _MI_WORDPRESS_BDESC5;
$modversion['blocks'][5]['show_func'] = "b_wp{$my_wp_dirnumber}_search_show";
$modversion['blocks'][5]['can_clone'] = true ;

$modversion['blocks'][6]['file'] = "wp_recent_posts.php";
$modversion['blocks'][6]['name'] = sprintf( _MI_WORDPRESS_BNAME6 , $my_wp_dirnumber );
$modversion['blocks'][6]['description'] = _MI_WORDPRESS_BDESC6;
$modversion['blocks'][6]['show_func'] = "b_wp{$my_wp_dirnumber}_recent_posts_show";
$modversion['blocks'][6]['edit_func'] = "b_wp{$my_wp_dirnumber}_recent_posts_edit";
$modversion['blocks'][6]['options'] = "10|1|0|0|0|0||all|0";
$modversion['blocks'][6]['can_clone'] = true ;

$modversion['blocks'][7]['file'] = "wp_recent_comments.php";
$modversion['blocks'][7]['name'] = sprintf( _MI_WORDPRESS_BNAME7 , $my_wp_dirnumber );
$modversion['blocks'][7]['description'] = _MI_WORDPRESS_BDESC7;
$modversion['blocks'][7]['show_func'] = "b_wp{$my_wp_dirnumber}_recent_comments_show";
$modversion['blocks'][7]['edit_func'] = "b_wp{$my_wp_dirnumber}_recent_comments_edit";
$modversion['blocks'][7]['options'] = "0|10|0|0|1";
$modversion['blocks'][7]['can_clone'] = true ;

$modversion['blocks'][8]['file'] = "wp_contents.php";
$modversion['blocks'][8]['name'] = sprintf( _MI_WORDPRESS_BNAME8 , $my_wp_dirnumber );
$modversion['blocks'][8]['description'] = _MI_WORDPRESS_BDESC8;
$modversion['blocks'][8]['show_func'] = "b_wp{$my_wp_dirnumber}_contents_show";
$modversion['blocks'][8]['edit_func'] = "b_wp{$my_wp_dirnumber}_contents_edit";
$modversion['blocks'][8]['options'] = "5";
$modversion['blocks'][8]['template'] = "wp_block_contents.html";
$modversion['blocks'][8]['can_clone'] = true ;

$modversion['blocks'][9]['file'] = "wp_authors.php";
$modversion['blocks'][9]['name'] =sprintf( _MI_WORDPRESS_BNAME9 , $my_wp_dirnumber );
$modversion['blocks'][9]['description'] = _MI_WORDPRESS_BDESC9;
$modversion['blocks'][9]['show_func'] = "b_wp{$my_wp_dirnumber}_authors_show";
$modversion['blocks'][9]['edit_func'] = "b_wp{$my_wp_dirnumber}_authors_edit";
$modversion['blocks'][9]['options'] = "0|nickname|0";
$modversion['blocks'][9]['can_clone'] = true ;

$modversion['hasComments'] = 1;
$modversion['comments']['itemName'] = 'p';
$modversion['comments']['pageName'] = 'index.php';

// On Update
if( ! empty( $_POST['fct'] ) && ! empty( $_POST['op'] ) && $_POST['fct'] == 'modulesadmin' && $_POST['op'] == 'update_ok' && $_POST['dirname'] == $modversion['dirname'] ) {
	include dirname( __FILE__ ) . "/include/onupdate.inc.php" ;
}
?>
