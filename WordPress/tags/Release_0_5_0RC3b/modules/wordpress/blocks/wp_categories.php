<?php
$_wp_base_prefix = 'wp';
$_wp_my_dirname = basename( dirname(dirname( __FILE__ ) ) );
if (!preg_match('/\D+(\d*)/', $_wp_my_dirname, $_wp_regs )) {
	echo ('Invalid dirname for WordPress Module: '. htmlspecialchars($_wp_my_dirname));
}
$_wp_my_dirnumber = $_wp_regs[1] ;
$_wp_my_prefix = $_wp_base_prefix.$_wp_my_dirnumber.'_';

if( ! defined( 'WP_CATEGORIES_BLOCK_INCLUDED' ) ) {
	define( 'WP_CATEGORIES_BLOCK_INCLUDED' , 1 ) ;

	function _b_wp_categories_edit($options)
	{
		$form = "";
		$form .= "Category List Style:&nbsp;";
		if ( $options[0] == 0 ) {
			$chk = " checked='checked'";
		}
		$form .= "<input type='radio' name='options[]' value='0'".$chk." />&nbsp;Simple List";
		$chk = "";
		if ( $options[0] == 1 ) {
			$chk = " checked=\"checked\"";
		}
		$form .= "&nbsp;<input type='radio' name='options[]' value='1'".$chk." />&nbsp;Dropdown List";

		$form .= "<br />Listing with count:&nbsp;";
		if ( $options[1] == 1 ) {
			$chk = " checked='checked'";
		}
		$form .= "<input type='radio' name='options[1]' value='1'".$chk." />&nbsp;"._YES."";
		$chk = "";
		if ( $options[1] == 0 ) {
			$chk = " checked=\"checked\"";
		}
		$form .= "&nbsp;<input type='radio' name='options[1]' value='0'".$chk." />"._NO."";
		
		$form .= "<br />Sorting Key:&nbsp;";
		if ( $options[2] == 'ID' ) {
			$chk = " checked='checked'";
		}
		$form .= "<input type='radio' name='options[2]' value='ID'".$chk." />&nbsp;".ID."";
		$chk = "";
		if ( $options[2] == 'name' ) {
			$chk = " checked=\"checked\"";
		}
		$form .= "&nbsp;<input type='radio' name='options[2]' value='name'".$chk." />".Name."";
		$chk = "";
		if ( $options[2] == 'description' ) {
			$chk = " checked=\"checked\"";
		}
		$form .= "&nbsp;<input type='radio' name='options[2]' value='description'".$chk." />".Description."";

		$form .= "<br />Sorting Order:&nbsp;";
		if ( $options[3] == 'asc' ) {
			$chk = " checked='checked'";
		}
		$form .= "<input type='radio' name='options[3]' value='asc'".$chk." />&nbsp;".Ascending."";
		$chk = "";
		if ( $options[3] == 'desc' ) {
			$chk = " checked=\"checked\"";
		}
		$form .= "&nbsp;<input type='radio' name='options[3]' value='desc'".$chk." />".Descending."";

		return $form;
	}

	function _b_wp_categories_show($options, $wp_num="")
	{
		$block_style =  ($options[0])?$options[0]:0;
		$with_count =  ($options[1])?$options[1]:0;
		$sorting_key = ($options[2])?$options[2]:'name';
		$sorting_order = ($options[3])?$options[3]:'asc';
		
		if (current_wp()) {
			if ( !empty( $_SERVER['PATH_INFO'] ) ) {
				permlink_to_param();
			}
			init_param('GET', 'cat','string','');
			init_param('GET', 'category_name','string','');
			if (!empty($GLOBALS['category_name']) && empty($GLOBALS['cat'])) {
			    if (stristr($GLOBALS['category_name'],'/')) {
			        $GLOBALS['category_name'] = explode('/',$GLOBALS['category_name']);
			        if ($GLOBALS['category_name'][count($GLOBALS['category_name'])-1]) {
				        $GLOBALS['category_name'] = $GLOBALS['category_name'][count($GLOBALS['category_name'])-1]; // no trailing slash
			        } else {
				        $GLOBALS['category_name'] = $GLOBALS['category_name'][count($GLOBALS['category_name'])-2]; // there was a trailling slash
			        }
			    }
			    $categoryHandler =& wp_handler('Category');
			    $categoryObject =& $categoryHandler->getByNiceName($GLOBALS['category_name']);
			    $GLOBALS['cat'] = $categoryObject->getVar('cat_ID');
			}
		}

		if ($block_style == 0) {
			// Simple Listing
			ob_start();
			block_style_get($wp_num);
			echo '<ul class="wpBlockList">'."\n";
			wp_list_cats("hide_empty=0&sort_column=$sorting_key&sorting_order=$sorting_order&optioncount=$with_count");
			echo '</ul>'."\n";
			$block['content'] = ob_get_contents();
			ob_end_clean();
		} else {
			// Dropdown Listing
			$file = wp_siteurl().'/index.php';
			$link = $file.'?cat=';
			ob_start();
			block_style_get($wp_num);
			echo '<form name="listcatform'.$wp_num.'" id="listcatform'.$wp_num.'" action="#">';
			$select_str = '<select name="cat" onchange="window.location = (document.forms.listcatform'.$wp_num.'.cat[document.forms.listcatform'.$wp_num.'.cat.selectedIndex].value);"> ';
			dropdown_cats(1,_WP_LIST_CAT_ALL,$sorting_key,$sorting_order,0,$with_count,0,false,0,0,true,0,true,0);
			echo '</form>';
			$block_str = ob_get_contents();
			ob_end_clean();
			$block['content'] = ereg_replace('\<select name\=[^\>]*\>',$select_str,$block_str);
		}
		return $block;
	}
}

eval ('
	function b_'.$_wp_my_prefix.'categories_edit($options) {
		return (_b_wp_categories_edit($options));
	}
	function b_'.$_wp_my_prefix.'categories_show($options) {
		$GLOBALS["use_cache"] = 1;
		$GLOBALS["wp_id"] = "'.(($_wp_my_dirnumber!=='') ? $_wp_my_dirnumber : '-').'";
		$GLOBALS["wp_inblock"] = 1;
		$GLOBALS["wp_mod"][$GLOBALS["wp_id"]] ="'.$_wp_my_dirname.'";
		require(XOOPS_ROOT_PATH."/modules/'.$_wp_my_dirname.'/wp-config.php");
		$GLOBALS["wp_inblock"] = 0;
		return (_b_wp_categories_show($options,"'.$_wp_my_dirnumber.'"));
	}
');

?>
