<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// modPukiWikiプラグイン呼出用クラス
//
// 修正元ファイル：PukiWiki 1.4のplugin.php
//
class PukiWikiPlugin {
	// プラグイン用に未定義の変数を設定
	function set_plugin_messages($messages)
	{
		foreach ($messages as $name=>$val)
		{
			global $$name;
			
			if (!isset($$name))
			{
				$$name = $val;
			}
		}
	}

	//プラグインが存在するか
	function exist_plugin($name)
	{
		$name = strtolower($name);	//Ryuji_edit(2003-03-18) add 大文字と小文字を区別しないファイルシステム対策
		if (preg_match('/^\w{1,64}$/', $name)
			and file_exists(MOD_PUKI_PLUGIN_DIR . $name . '.inc.php'))
		{
			require_once(MOD_PUKI_PLUGIN_DIR . $name . '.inc.php');
			return TRUE;
		}
		return FALSE;
	}

	//プラグイン関数(action)が存在するか
	function exist_plugin_action($name) {
		return	function_exists('plugin_' . $name . '_action') ? TRUE : (PukiWikiPlugin::exist_plugin($name) ?
			function_exists('plugin_' . $name . '_action') : FALSE);
	}

	//プラグイン関数(convert)が存在するか
	function exist_plugin_convert($name) {
		return	function_exists('plugin_' . $name . '_convert') ? TRUE : (PukiWikiPlugin::exist_plugin($name) ?
			function_exists('plugin_' . $name . '_convert') : FALSE);
	}

	//プラグイン関数(inline)が存在するか
	function exist_plugin_inline($name) {
		return	function_exists('plugin_' . $name . '_inline') ? TRUE : (PukiWikiPlugin::exist_plugin($name) ?
			function_exists('plugin_' . $name . '_inline') : FALSE);
	}

	//プラグインの初期化を実行
	function do_plugin_init($name)
	{
		static $check = array();
		
		if (array_key_exists($name,$check))
		{
			return $check[$name];
		}
		
		$func = 'plugin_'.$name.'_init';
		if ($check[$name] = function_exists($func))
		{
			@call_user_func($func);
			return TRUE;
		}
		return FALSE;
	}

	//プラグイン(action)を実行
	function do_plugin_action($name)
	{
		if (!PukiWikiPlugin::exist_plugin_action($name))
		{
			return array();
		}
		
		PukiWikiPlugin::do_plugin_init($name);
		$retvar = call_user_func('plugin_'.$name.'_action');
		
		// 文字エンコーディング検出用 hidden フィールドを挿入する
		return preg_replace('/(<form[^>]*>)/',"$1\n<div><input type=\"hidden\" name=\"encode_hint\" value=\"ぷ\" /></div>",$retvar);
	}

	//プラグイン(convert)を実行
	function do_plugin_convert($name,$args='')
	{
		global $digest;
		
		// digestを退避
		$_digest = $digest;
		
		$aryargs = ($args !== '') ? PukiWikiFunc::csv_explode(',', $args) : array();

		PukiWikiPlugin::do_plugin_init($name);
		$retvar = call_user_func_array('plugin_'.$name.'_convert',$aryargs);
		
		// digestを復元
		$digest = $_digest;
		
		if ($retvar === FALSE)
		{
			return htmlspecialchars('#'.$name.($args ? "($args)" : ''));
		}
		
		// 文字エンコーディング検出用 hidden フィールドを挿入する
		return preg_replace('/(<form[^>]*>)/',"$1\n<div><input type=\"hidden\" name=\"encode_hint\" value=\"ぷ\" /></div>",$retvar);
	}

	//プラグイン(inline)を実行
	function do_plugin_inline($name,$args,$body)
	{
		global $digest;
		
		// digestを退避
		$_digest = $digest;
		
		$aryargs = ($args !== '') ? PukiWikiFunc::csv_explode(',',$args) : array();
		$aryargs[] =& $body;

		PukiWikiPlugin::do_plugin_init($name);
		$retvar = call_user_func_array('plugin_'.$name.'_inline',$aryargs);
		
		// digestを復元
		$digest = $_digest;
		
		if($retvar === FALSE)
		{
			return htmlspecialchars("&${name}" . ($args ? "($args)" : '') . ';');
		}
		
		return $retvar;
	}
}
?>
