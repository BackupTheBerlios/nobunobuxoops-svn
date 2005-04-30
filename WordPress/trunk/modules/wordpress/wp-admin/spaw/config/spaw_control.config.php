<?php 
// ================================================
// SPAW PHP WYSIWYG editor control
// ================================================
// Configuration file
// ================================================
// Developed: Alan Mendelevich, alan@solmetra.lt
// Copyright: Solmetra (c)2003 All rights reserved.
// ------------------------------------------------
//                                www.solmetra.com
// ================================================
// v.1.0, 2003-03-27
// ================================================

$spaw_conf_path = dirname(__FILE__);
$spaw_root = dirname($spaw_conf_path) . '/';

if( ! defined( 'XOOPS_ROOT_PATH' ) ) {
	$cur_path = '/modules/wordpress\d?/wp-admin/spaw/config';
	$root_dir = preg_replace('|'.$cur_path.'$|', '', $spaw_conf_path);
	include_once( $root_dir. '/mainfile.php' ) ;
}

// directory where spaw files are located
// $spaw_dir = 'spaw/';
//$spaw_dir = XOOPS_URL . '/common/spaw/' ;
$spaw_dir = preg_replace('|^'.XOOPS_ROOT_PATH.'|', XOOPS_URL , $spaw_root);

// base url for images
$spaw_base_url = XOOPS_URL.'/';

// $spaw_root = dirname( dirname( __FILE__ ) ) . '/' ;
// $spaw_root = XOOPS_ROOT_PATH . '/common/spaw/' ;

$spaw_default_toolbars = 'full';
$spaw_default_theme = 'default';
$spaw_default_lang = _LANGCODE ;
$spaw_default_css_stylesheet = $spaw_dir.'wysiwyg.css';

// add javascript inline or via separate file
$spaw_inline_js = false;

// use active toolbar (reflecting current style) or static
$spaw_active_toolbar = true;

// default dropdown content
$spaw_dropdown_data['style']['default'] = 'Normal';
$spaw_dropdown_data['table_style']['default'] = 'Normal';
$spaw_dropdown_data['td_style']['default'] = 'Normal';

$spaw_dropdown_data['font']['Arial'] = 'Arial';
$spaw_dropdown_data['font']['Courier'] = 'Courier';
$spaw_dropdown_data['font']['Tahoma'] = 'Tahoma';
$spaw_dropdown_data['font']['Times New Roman'] = 'Times';
$spaw_dropdown_data['font']['Verdana'] = 'Verdana';

$spaw_dropdown_data['fontsize']['1'] = '1';
$spaw_dropdown_data['fontsize']['2'] = '2';
$spaw_dropdown_data['fontsize']['3'] = '3';
$spaw_dropdown_data['fontsize']['4'] = '4';
$spaw_dropdown_data['fontsize']['5'] = '5';
$spaw_dropdown_data['fontsize']['6'] = '6';

$spaw_dropdown_data['paragraph']['Normal'] = 'Normal';
$spaw_dropdown_data['paragraph']['Heading 1'] = 'Heading 1';
$spaw_dropdown_data['paragraph']['Heading 2'] = 'Heading 2';
$spaw_dropdown_data['paragraph']['Heading 3'] = 'Heading 3';
$spaw_dropdown_data['paragraph']['Heading 4'] = 'Heading 4';
$spaw_dropdown_data['paragraph']['Heading 5'] = 'Heading 5';
$spaw_dropdown_data['paragraph']['Heading 6'] = 'Heading 6';

// image library related config

// allowed extentions for uploaded image files
$spaw_valid_imgs = array('gif', 'jpg', 'jpeg', 'png');

// allow upload in image library
$spaw_upload_allowed = false;
// allow delete from image library
$spaw_img_delete_allowed = false;
// file to include in img_library.php (useful for setting $spaw_imglibs dynamically
// $spaw_imglib_include = '';

// allowed hyperlink targets
$spaw_a_targets['_self'] = 'Self';
$spaw_a_targets['_blank'] = 'Blank';
$spaw_a_targets['_top'] = 'Top';
$spaw_a_targets['_parent'] = 'Parent';

// image popup script url
$spaw_img_popup_url = $spaw_dir.'img_popup.php';

// internal link script url
$spaw_internal_link_script = 'url to your internal link selection script';

// disables style related controls in dialogs when css class is selected
$spaw_disable_style_controls = true;

$i=0;
$spaw_imglibs = array();
//Get Readable Module list
$groups = ( $xoopsUser ) ? $xoopsUser -> getGroups() : XOOPS_GROUP_ANONYMOUS;
$gperm_handler = & xoops_gethandler( 'groupperm' );
$available_modules = $gperm_handler->getItemIds('module_read', $groups);
$module_handler =& xoops_gethandler('module');
$criteria = new CriteriaCompo(new Criteria('hassearch', 1));
$criteria->add(new Criteria('isactive', 1));
$criteria->add(new Criteria('mid', "(".implode(',', $available_modules).")", 'IN'));
$modules =& $module_handler->getObjects($criteria, true);

//Get WordPress Uploaded Images.
foreach ($modules as $module){
	$mod = $module->getVar('dirname');
	$wpconf_fname =  XOOPS_ROOT_PATH."/modules/".$mod."/wp-config.php";
	if (file_exists($wpconf_fname)) {
		include_once($wpconf_fname);
		get_currentuserinfo();
		if ($GLOBALS['user_level'] > 1) {
			$spaw_imglibs[$i] = array(
				'type'    =>  'Dir',
				'value'   => ereg_replace(XOOPS_URL.'\/(.*)',"\\1",get_settings('fileupload_url'))."/" ,
				'text'    => 'Uploads[WordPress]',
				'catID'   => $i,
				'storetype' => 'file',
				'autoID' => 0
			);
			$i++;
		}
	}
}

//Check using XOOPS Original Image manager
$fp = fopen( XOOPS_ROOT_PATH . '/imagemanager.php' , 'r' ) ;
$top_of_imagemanager = fread( $fp , 4096 ) ;
fclose( $fp ) ;

if( !strstr( $top_of_imagemanager , '$mydirname' ) && 
    !preg_match( '?modules/(\D+)(\d*)/imagemanager.php?' , $top_of_imagemanager , $regs ) ) {
	global $xoopsDB;

	$result = $xoopsDB->query("SELECT imgcat_name, imgcat_id, imgcat_storetype FROM " . $xoopsDB->prefix('imagecategory') . " ORDER BY imgcat_name ASC");

	while($imgcat = $xoopsDB->fetcharray($result)){
		$spaw_imglibs[$i]["type"]  = "XoopsImage";
		$spaw_imglibs[$i]["value"] = 'uploads/';
		$spaw_imglibs[$i]["text"] = $imgcat["imgcat_name"]."[XOOPS IM]";
		$spaw_imglibs[$i]["catID"] = $i;
		$spaw_imglibs[$i]["storetype"] = $imgcat["imgcat_storetype"];
		$spaw_imglibs[$i]["autoID"] = $imgcat["imgcat_id"];

		$i++;
	}
}

//myAlbum-P Modules
foreach ($modules as $module){
	$mod = $module->getVar('dirname');
	$offset=$i;
	$icon_fname =  XOOPS_ROOT_PATH."/modules/".$mod."/images/myalbum_slogo.gif";
	if (file_exists($icon_fname)) {
		if( ! preg_match( '/^(\D+)(\d*)$/' , $mod , $regs ) ) echo ( "invalid dirname: " . htmlspecialchars( $mod ) ) ;
		$imnumber = $regs[2] === '' ? '' : intval( $regs[2] ) ;

		$result = $xoopsDB->query("SELECT title, cid FROM ".$xoopsDB->prefix('myalbum'.$imnumber.'_cat')." ORDER BY title ASC");

		while($imgcat = $xoopsDB->fetcharray($result)){
			$spaw_imglibs[$i]["type"]  = "myAlbum-P";
			$spaw_imglibs[$i]["value"] = 'uploads/';
			$spaw_imglibs[$i]["text"] = $imgcat["title"]."[$mod]";
			$spaw_imglibs[$i]["catID"] = $i;
			$spaw_imglibs[$i]["autoID"] = $imgcat["cid"];
			$spaw_imglibs[$i]["storetype"] = $imnumber ;

			$i++;
		}
	}
}?>
