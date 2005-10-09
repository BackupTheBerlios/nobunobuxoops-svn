<?php
include_once(XOOPS_ROOT_PATH."/class/xoopslists.php");
function b_sysutil_langsel_show($options) {
	if (empty($_SERVER['QUERY_STRING'])) {
		$pagenquery = $_SERVER['PHP_SELF'].'?'.SYSUTIL_ML_PARAM_NAME.'=';
	} elseif (isset($_SERVER['QUERY_STRING'])) {
		
		$query = explode("&",$_SERVER['QUERY_STRING']);
		$langquery = $_SERVER['QUERY_STRING'];
		
		// If the last parameter of the QUERY_STRING is sel_lang, delete it so we don't have repeating sel_lang=...
		If (strpos($query[count($query) - 1], SYSUTIL_ML_PARAM_NAME.'=')  === 0 ) {
			$langquery = str_replace('&' . $query[count($query) - 1], '', $langquery);
		}
		
		$pagenquery = $_SERVER['PHP_SELF'].'?'.$langquery.'&'.SYSUTIL_ML_PARAM_NAME.'=';
		$pagenquery = str_replace('?&','?',$pagenquery);
	}

	//show a drop down list to select language
	
	$block['content'] = "<script type='text/javascript'>
<!--
function SelLang_jumpMenu(targ,selObj,restore){
eval(targ+\".location='".$pagenquery."\"+selObj.options[selObj.selectedIndex].value+\"'\");
if (restore) selObj.selectedIndex=0;
}
-->
</script>";
	$block['content'] .= '<div align="center"><select name="'.SYSUTIL_ML_PARAM_NAME.'" onChange="SelLang_jumpMenu(\'parent\',this,0)">';
	$languages = XoopsLists::getLangList();
	$langnames = explode(',',SYSUTIL_ML_LANGNAMES);
	$langs = explode(',',SYSUTIL_ML_LANGS);
	for ($i=0; $i < count($langs); $i++) {
		$block['content'] .= '<option value="'.$langs[$i].'"';
		if ($GLOBALS['sysutil_ml_lang'] == $langs[$i]) $block['content'] .= " selected";
		$block['content'] .= '>'.$langnames[$i].'</option>';
	}
	
	$block['content'] .= '</select></div>';
	
	return $block;
}
?>
