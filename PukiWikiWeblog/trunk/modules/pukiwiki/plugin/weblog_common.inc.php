<?php
/*
 * PukiWiki weblog�ץ饰�����Ѷ��̴ؿ�
 *
 * $Id$
 *
 */
///////////////////////////////////////////////////////////

function weblog_msg_init() {
	if (LANG=='ja') {
		$messages = array('_weblog_msgs' => array(
			'lbl_author' => '��̾��:',
			'lbl_category' => '���ƥ���:',
			'lbl_subject' => '�����ȥ�:',
			'lbl_comment' => '������',
			'send_trackback_ping' => '�ȥ�å��Хå���:',
			'allow_comment' => '�����Ȥ���Ĥ���:',
			'auto_br' => '��ư���Ԥ�Ԥ�:',
			'update_stamp' => '�����ॹ����פ򹹿�����:',
			'lbl_yes' => '�Ϥ�',
			'lbl_no' => '������',
			'lbl_by' => '��Ƽ�:',
			'lbl_at' => '�������:',
			'lbl_daily_header' => '������',
			'lbl_new_title' => '%s�ؤ����',
			'btn_post' => 'write:',
			'btn_submit' => '��Ƥ���',
			'btn_edit' => '�����Խ�',
			'no_name' => 'ƿ̾��˾',
			'no_subject' => '(̵��)',
			'fmt_day' => 'm��d��',
			'fmt_month' => 'Yǯm��',
			'fmt_fullday' => 'Yǯm��d��',
			'fmt_time' => 'H��iʬ',
			'fmt_fulltime' => 'H��iʬs��',
			'message_sent' => '��Ƶ�������¸��Ǥ���',
			'message_sent_complete' => '��Ƶ�������¸�椬��λ���ޤ�����',
			'message_ping' => 'TrackBack Ping��������Ǥ���',
			'message_disable_comment' => '�����Ȥ���Ƥϵ��Ĥ���Ƥޤ���',
			'err_msg_nomsg' => '���������Ǥ���',
			'err_msg_noauth' => '��������Ƥ��븢�¤�����ޤ���',
			'err_msg_notemplate' => '�ƥ�ץ졼�Ȥ����Ĥ���ޤ���(%s)',
			'err_msg_noconf' => '����ե����ե����뤬���Ĥ���ޤ���(%s)',
			'err_msg_arg2' => '����������ѤǤ���',
			'err_msg_noargs' => '��������ꤷ�Ƥ���������',
			'err_nopages' => '<p>\'$1\' �ˤϡ������ؤΥڡ���������ޤ���</p>',
			'msg_title' => '\'$1\'�ǻϤޤ�ڡ����ΰ���',
			'msg_go' => '<span class="small">...</span>',
			'msg_daily' => '%s%s�����(%d��)',
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
	//����ե������ɤ߹���
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
//IE��javascript��UCS2��encode���줿���Х���ʸ�����EUC-JP�ؤ��Ѵ�
function weblog_conv_knj_escape($str)
{
	return mb_convert_encoding(preg_replace_callback(
			"/%u([0-9a-fA-F][0-9a-fA-F])([0-9a-fA-F][0-9a-fA-F])/","_weblog_conv_escape",$str),"EUC-JP","auto");
	
}
function _weblog_conv_escape($matches){
	return mb_convert_encoding(rawurldecode("%".$matches[1]."%".$matches[2]),"EUC-JP","UCS-2");
}
?>
