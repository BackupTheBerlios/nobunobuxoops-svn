<?php
// Tables created by sql file (without prefix!)
$modversion['tables'][0] = "mygmap_marker";
$modversion['tables'][1] = "mygmap_catcategory";
$modversion['tables'][2] = "mygmap_area";

// Templates
$modversion['templates'][1]['file'] = 'mygmap_index.html';
$modversion['templates'][1]['description'] = '';

$modversion['templates'][2]['description'] = '';

$modversion['templates'][3]['description'] = '';

$modversion['templates'][4]['description'] = '';

$modversion['adminmenu'] = "admin/menu.php";
$modversion['hasconfig'] = 1;
$modversion['config'][1] = array(
	'name'			=> 'mygmap_api' ,
	'title'			=> '_MI_MYGMAP_API_MSG' ,
	'description'	=> '_MI_MYGMAP_API_DESC' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> '' ,
);

	'name'			=> 'mygmap_cat' ,
	'title'			=> '_MI_MYGMAP_CAT_MSG' ,
	'description'	=> '_MI_MYGMAP_CAT_DESC' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> '0' ,
);

	'name'			=> 'mygmap_lat' ,
	'title'			=> '_MI_MYGMAP_LAT_MSG' ,
	'description'	=> '_MI_MYGMAP_LAT_DESC' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> '133.20066690444946' ,
);

	'name'			=> 'mygmap_lng' ,
	'title'			=> '_MI_MYGMAP_LNG_MSG' ,
	'description'	=> '_MI_MYGMAP_LNG_DESC' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> '34.4038999984083' ,
);

$modversion['config'][5] = array(
	'name'			=> 'mygmap_zoom' ,
	'title'			=> '_MI_MYGMAP_ZOOM_MSG' ,
	'description'	=> '_MI_MYGMAP_ZOOM_DESC' ,
	'formtype'		=> 'select' ,
	'valuetype'		=> 'int' ,
	'default'		=> 4 ,
	'options' => array(
                    '_MI_MYGMAP_Z_OPT0'=>0 ,
                    '_MI_MYGMAP_Z_OPT1'=>1 ,
                    '_MI_MYGMAP_Z_OPT2'=>2 ,
                    '_MI_MYGMAP_Z_OPT3'=>3 ,
                    '_MI_MYGMAP_Z_OPT4'=>4 ,
                    '_MI_MYGMAP_Z_OPT5'=>5 ,
                    '_MI_MYGMAP_Z_OPT6'=>6 ,
                    '_MI_MYGMAP_Z_OPT7'=>7 ,
                    '_MI_MYGMAP_Z_OPT8'=>8 ,
                    '_MI_MYGMAP_Z_OPT9'=>9 ,
                    '_MI_MYGMAP_Z_OPT10'=>10 ,
                    '_MI_MYGMAP_Z_OPT11'=>11 ,
                    '_MI_MYGMAP_Z_OPT12'=>12 ,
                    '_MI_MYGMAP_Z_OPT13'=>13 ,
                    '_MI_MYGMAP_Z_OPT14'=>14 ,
                    '_MI_MYGMAP_Z_OPT15'=>15 ,
                    '_MI_MYGMAP_Z_OPT16'=>16 ,
                    '_MI_MYGMAP_Z_OPT17'=>17 ,
     ),
);

$modversion['config'][6] = array(
	'name'			=> 'mygmap_fuki' ,
	'title'			=> '_MI_MYGMAP_FUKI_MSG' ,
	'description'	=> '_MI_MYGMAP_FUKI_DESC' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 0 ,
);
$modversion['config'][7] = array(
	'name'			=> 'mygmap_search' ,
	'title'			=> '_MI_MYGMAP_SEARCH_MSG' ,
	'description'	=> '_MI_MYGMAP_SEARCH_DESC' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 0 ,
);
$modversion['config'][8] = array(
	'name'			=> 'mygmap_invgeo' ,
	'title'			=> '_MI_MYGMAP_INVGEO_MSG' ,
	'description'	=> '_MI_MYGMAP_INVGEO_DESC' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 0 ,
);
$modversion['config'][9] = array(
	'name'			=> 'mygmap_link' ,
	'title'			=> '_MI_MYGMAP_LINK_MSG' ,
	'description'	=> '_MI_MYGMAP_LINK_DESC' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 1 ,
);
$modversion['config'][10] = array(
	'name'			=> 'mygmap_wiki' ,
	'title'			=> '_MI_MYGMAP_WIKI_MSG' ,
	'description'	=> '_MI_MYGMAP_WIKI_DESC' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 0 ,
);
$modversion['config'][11] = array(
	'name'			=> 'mygmap_blog' ,
	'title'			=> '_MI_MYGMAP_BLOG_MSG' ,
	'description'	=> '_MI_MYGMAP_BLOG_DESC' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 0 ,
);
	'name'			=> 'mygmap_width' ,
	'title'			=> '_MI_MYGMAP_WIDTH_MSG' ,
	'description'	=> '_MI_MYGMAP_WIDTH_DESC' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'int' ,
	'default'		=> 540 ,
);
	'name'			=> 'mygmap_height' ,
	'title'			=> '_MI_MYGMAP_HEIGHT_MSG' ,
	'description'	=> '_MI_MYGMAP_HEIGHT_DESC' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'int' ,
	'default'		=> 460 ,
);
	'name'			=> 'mygmap_text1' ,
	'title'			=> '_MI_MYGMAP_TEXT1_MSG' ,
	'description'	=> '_MI_MYGMAP_TEXT1_DESC' ,
	'formtype'		=> 'textarea' ,
	'valuetype'		=> 'text' ,
	'default'		=> _MI_MYGMAP_TEXT1_DEFAULT ,
);
$modversion['config'][15] = array(
	'name'			=> 'mygmap_text2' ,
	'title'			=> '_MI_MYGMAP_TEXT2_MSG' ,
	'description'	=> '_MI_MYGMAP_TEXT2_DESC' ,
	'formtype'		=> 'textarea' ,
	'valuetype'		=> 'text' ,
	'default'		=> _MI_MYGMAP_TEXT2_DEFAULT ,
);
$modversion['config'][16] = array(
	'name'			=> 'mygmap_setdef_show' ,
	'title'			=> '_MI_MYGMAP_SETDEF_SHOW_MSG' ,
	'description'	=> '_MI_MYGMAP_SETDEF_SHOW_DESC' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 1 ,
);
$modversion['config'][17] = array(
	'name'			=> 'mygmap_debug' ,
	'title'			=> '_MI_MYGMAP_DEBUG_MSG' ,
	'description'	=> '_MI_MYGMAP_DEBUG_DESC' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 0 ,
);
?>