<?php
// $id$
function weblog_msg_init() {
	if (LANG=='ja') {
		$messages = array('_weblog_msgs' => array(
			'lbl_author' => 'お名前:',
			'lbl_category' => 'カテゴリ:',
			'lbl_subject' => 'タイトル:',
			'lbl_comment' => 'コメント',
			'send_trackback_ping' => 'トラックバック先:',
			'allow_comment' => 'コメントを許可する:',
			'auto_br' => '自動改行を行う:',
			'update_stamp' => 'タイムスタンプを更新する:',
			'lbl_yes' => 'はい',
			'lbl_no' => 'いいえ',
			'lbl_by' => '投稿者:',
			'lbl_at' => '投稿日時:',
			'lbl_daily_header' => '日付別',
			'btn_post' => 'write:',
			'btn_submit' => '投稿する',
			'btn_edit' => '記事編集',
			'no_name' => '匿名希望',
			'no_subject' => '(無題)',
			'fmt_day' => 'm月d日',
			'fmt_month' => 'Y年m月',
			'fmt_fullday' => 'Y年m月d日',
			'fmt_time' => 'H時i分',
			'fmt_fulltime' => 'H時i分s秒',
				'message_sent' => '投稿記事の保存中です。',
			'message_ping' => 'TrackBack Pingを送信中です。',
			'err_msg_nomsg' => '記事が空です。',
			'err_msg_noauth' => '記事を投稿する権限がありません。',
			'err_msg_notemplate' => 'テンプレートが見つかりません。(%s)',
			'err_msg_noconf' => 'コンフィグファイルが見つかりません。(%s)',
			'err_msg_arg2' => '第二引数が変です。',
			'err_msg_noargs' => '引数を指定してください。',
		));
	} else {
		$messages = array('_weblog_msgs' => array(
			'lbl_author' => 'Name:',
			'lbl_category' => 'Category:',
			'lbl_subject' => 'Title:',
			'lbl_comment' => 'Comment',
			'send_trackback_ping' => 'Send Trackback Ping:',
			'allow_comment' => 'Allow Comment:',
			'auto_br' => 'Auto BR:',
			'update_stamp' => 'Update Timestamp:',
			'lbl_yes' => 'Yes',
			'lbl_no' => 'No',
			'lbl_by' => 'by',
			'lbl_at' => 'at',
			'lbl_daily_header' => 'Daily Archieve',
			'btn_post' => 'write:',
			'btn_submit' => 'Post Aritcle',
			'btn_edit' => 'Edit',
			'no_name' => 'No Name',
			'no_subject' => 'No Subject',
			'fmt_day' => 'm/d',
			'fmt_month' => 'Y/m',
			'fmt_fullday' => 'Y/m/d',
			'fmt_time' => 'H:i',
			'fmt_fulltime' => 'H:i:s',
			'message_sent' => 'Saving Message.',
			'message_ping' => 'Sending TrackBack Ping.',
			'err_msg_nomsg' => 'Posted message is empty.',
			'err_msg_noauth' => 'You can\'t post article.',
			'err_msg_arg2' => '2nd parameter isn\'t valid.',
			'err_msg_noargs' => 'Please specify parameters.',
		));
	}
	set_plugin_messages($messages);
}

function weblog_get_options($conf_name, $options) {
	//コンフィグの読み込み
	$config = new Config("plugin/weblog/$conf_name");
	if ($config->read()) {
		foreach ($config->get('Config') as $conf_item) {
			$options[$conf_item[0]] = $conf_item[1];
		}
	}
	return $options;
}

function weblog_set_return($page,$refer) {
	if (is_page($page)) {
		$refer=$page;
	}
	return array($page,$refer);
}

function weblog_load_template($conf_name,$template) {
	$retstr = "";
	$_page = ":config/plugin/weblog/$conf_name/$template";
	if (!is_page($_page)) {
		$_page = ":config/plugin/weblog/default/$template";
		if (!is_page($_page)) {
			return FALSE;
		}
	}
	$sources = get_source($_page);
	foreach ($sources as $source) {
		$source = preg_replace('/^(\*{1,6}.*)\[#[A-Za-z][\w-]+\](.*)$/m','$1$2',$source);
		$source = preg_replace("/^#freeze(?:\tuid:([0-9]+))?(?:\taid:([0-9,]+))?(?:\tgid:([0-9,]+))?\n/",'',$source);
		$source = preg_replace("/^#unvisible(?:\tuid:([0-9]+))?(?:\taid:([0-9,]+|all))?(?:\tgid:([0-9,]+))?\n/",'',$source);
		$source = preg_replace("/^\/\/ author:([0-9]+)\n/","",$source);
		$retstr .= $source;
	}
	return $retstr;
}
function weblog_conv_knj_escape($str)
{
	return mb_convert_encoding(preg_replace_callback(
			"/%u([0-9a-fA-F][0-9a-fA-F])([0-9a-fA-F][0-9a-fA-F])/","_weblog_conv_escape",$str),"EUC-JP","auto");
	
}
function _weblog_conv_escape($matches){
	return mb_convert_encoding(rawurldecode("%".$matches[1]."%".$matches[2]),"EUC-JP","UCS-2");
}
?>
