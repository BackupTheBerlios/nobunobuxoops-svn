<?php
/*
 * PukiWiki weblog¥×¥é¥°¥¤¥óÍÑ¶¦ÄÌ´Ø¿ô
 *
 * $Id$
 *
 */
///////////////////////////////////////////////////////////

function weblog_msg_init() {
	if (LANG=='ja') {
		$messages = array('_weblog_msgs' => array(
			'lbl_author' => '¤ªÌ¾Á°:',
			'lbl_category' => '¥«¥Æ¥´¥ê:',
			'lbl_subject' => '¥¿¥¤¥È¥ë:',
			'lbl_comment' => '¥³¥á¥ó¥È',
			'send_trackback_ping' => '¥È¥é¥Ã¥¯¥Ð¥Ã¥¯Àè:',
			'allow_comment' => '¥³¥á¥ó¥È¤òµö²Ä¤¹¤ë:',
			'auto_br' => '¼«Æ°²þ¹Ô¤ò¹Ô¤¦:',
			'update_stamp' => '¥¿¥¤¥à¥¹¥¿¥ó¥×¤ò¹¹¿·¤¹¤ë:',
			'lbl_yes' => '¤Ï¤¤',
			'lbl_no' => '¤¤¤¤¤¨',
			'lbl_by' => 'Åê¹Æ¼Ô:',
			'lbl_at' => 'Åê¹ÆÆü»þ:',
			'lbl_daily_header' => 'ÆüÉÕÊÌ',
			'lbl_new_title' => '%s¤Ø¤ÎÅê¹Æ',
			'btn_post' => 'write:',
			'btn_submit' => 'Åê¹Æ¤¹¤ë',
			'btn_edit' => 'µ­»öÊÔ½¸',
			'no_name' => 'Æ¿Ì¾´õË¾',
			'no_subject' => '(ÌµÂê)',
			'fmt_day' => 'm·îdÆü',
			'fmt_month' => 'YÇ¯m·î',
			'fmt_fullday' => 'YÇ¯m·îdÆü',
			'fmt_time' => 'H»þiÊ¬',
			'fmt_fulltime' => 'H»þiÊ¬sÉÃ',
			'message_sent' => 'Åê¹Æµ­»ö¤ÎÊÝÂ¸Ãæ¤Ç¤¹¡£',
			'message_sent_complete' => 'Åê¹Æµ­»ö¤ÎÊÝÂ¸Ãæ¤¬´°Î»¤·¤Þ¤·¤¿¡£',
			'message_ping' => 'TrackBack Ping¤òÁ÷¿®Ãæ¤Ç¤¹¡£',
			'message_disable_comment' => '¥³¥á¥ó¥È¤ÎÅê¹Æ¤Ïµö²Ä¤µ¤ì¤Æ¤Þ¤»¤ó¡£',
			'err_msg_nomsg' => 'µ­»ö¤¬¶õ¤Ç¤¹¡£',
			'err_msg_noauth' => 'µ­»ö¤òÅê¹Æ¤¹¤ë¸¢¸Â¤¬¤¢¤ê¤Þ¤»¤ó¡£',
			'err_msg_notemplate' => '¥Æ¥ó¥×¥ì¡¼¥È¤¬¸«¤Ä¤«¤ê¤Þ¤»¤ó¡£(%s)',
			'err_msg_noconf' => '¥³¥ó¥Õ¥£¥°¥Õ¥¡¥¤¥ë¤¬¸«¤Ä¤«¤ê¤Þ¤»¤ó¡£(%s)',
			'err_msg_arg2' => 'ÂèÆó°ú¿ô¤¬ÊÑ¤Ç¤¹¡£',
			'err_msg_noargs' => '°ú¿ô¤ò»ØÄê¤·¤Æ¤¯¤À¤µ¤¤¡£',
			'err_nopages' => '<p>\'$1\' ¤Ë¤Ï¡¢²¼°ÌÁØ¤Î¥Ú¡¼¥¸¤¬¤¢¤ê¤Þ¤»¤ó¡£</p>',
			'msg_title' => '\'$1\'¤Ç»Ï¤Þ¤ë¥Ú¡¼¥¸¤Î°ìÍ÷',
			'msg_go' => '<span class="small">...</span>',
			'msg_daily' => '%s%s¤ÎÅê¹Æ(%d·ï)',
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
			'lbl_new_title' => 'New Posting to %s',
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
			'err_nopages' => '<p>\'$1\' has no child page.</p>',
			'msg_title' => 'Page Listing begining from \'$1\'',
			'msg_go' => '<span class="small">...</span>',
		));
	}
	set_plugin_messages($messages);
}

function weblog_get_options($conf_name, $options) {
	//¥³¥ó¥Õ¥£¥°¤ÎÆÉ¤ß¹þ¤ß
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

function weblog_assign_value($string,$values) {
	foreach ($values as $key=>$value) {
		if (is_array($value)) {
			$string = preg_replace("/\[".$key.":".$value[0]."\]/",$value[1],$string);
		} else {
			$string = preg_replace("/\[".$key."\]/",$value,$string);
		}
	}
	return $string;
}
//IE¤Îjavascript¤ÇUCS2¤Ëencode¤µ¤ì¤¿£²¥Ð¥¤¥ÈÊ¸»úÎó¤ÎEUC-JP¤Ø¤ÎÊÑ´¹
function weblog_conv_knj_escape($str)
{
	return mb_convert_encoding(preg_replace_callback(
			"/%u([0-9a-fA-F][0-9a-fA-F])([0-9a-fA-F][0-9a-fA-F])/","_weblog_conv_escape",$str),"EUC-JP","auto");
	
}
function _weblog_conv_escape($matches){
	return mb_convert_encoding(rawurldecode("%".$matches[1]."%".$matches[2]),"EUC-JP","UCS-2");
}
/*
 * $Log$
 * Revision 1.4  2004/02/25 08:37:14  nobu
 * CVS—pƒL[ƒ[ƒh’Ç‰Ái$Id‹y‚Ñ$Log)
 *
 */
?>
