<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// modPukiWiki������󥰥��󥸥� �ᥤ�󥯥饹
//
class PukiWikiRender {
	var $_body;
	var $_settings;
	var $_linerules;
	var $_pattern;
	var $_replace;
	var $_source;
	var $_md5hash;
	
	function PukiWikiRender($config='') {
		//�ǥե���Ȥ�����ե������ɹ�
		require (MOD_PUKI_DEFAULT);
		PukiWikiConfig::initParams();
		foreach($_settings as $key=>$value) {
			PukiWikiConfig::setParam($key,$value);
		}
		PukiWikiConfig::initRules();
		PukiWikiConfig::addRuleArray($_rules);
		
		PukiWikiConfig::initInterWiki();
		$this->_body = &new PukiWikiBody($this,1);
		//���󥹥ȥ饯���Υѥ�᡼����$config�����ꤵ��Ƥ�����ϡ��ɤ߹��ࡣ
		if ($config and file_exists(MOD_PUKI_CONFIG_DIR.$config.".php")) {
			include (MOD_PUKI_CONFIG_DIR.$config.".php");
		}
	}

	function transform($wikistr) {
		$this->parse($wikistr);
		return $this->render();
	}

	function parse($wikistr) {
		//Wiki����������¸��md5�ϥå���μ���
		$this->_source = $wikistr;
		$this->_md5hash = md5($wikistr);

		//¾��PukiWiki�����ƥ�Ȥ�Ϣ�Ƚ����
		$this->_init_PukiWiki_env();
		
		// ����å����ǧ by nao-pon
		if (PukiWikiConfig::getParam('use_cache')) {
			$cache_file = MOD_PUKI_CACHE_DIR.$this->_md5hash.".cache";
			if (file_exists($cache_file)) return;
		}
		
		if (!is_array($wikistr)) {
			$wikistr = $this->_line_explode($wikistr);
		}
		$this->_body->parse($wikistr);
	}

	function render() {
		global $_PukiWikiFootExplain;

		// ����å����ǧ by nao-pon
		if (PukiWikiConfig::getParam('use_cache')) {
			$cache_file = MOD_PUKI_CACHE_DIR.$this->_md5hash.".cache";
			if (file_exists($cache_file)) return join('',file($cache_file));
		}
		
		$retstr = $this->_body->toString();

		$retstr = $this->_fix_table_br($retstr);
		if (count($_PukiWikiFootExplain)) {
			ksort($_PukiWikiFootExplain,SORT_NUMERIC);
			$retstr .= count($_PukiWikiFootExplain) ? PukiWikiConfig::getParam('note_hr').join("\n",$_PukiWikiFootExplain) : '';
		}
		$_PukiWikiFootExplain=array();

		if (PukiWikiConfig::getParam('use_cache'))
		{
			//����å�����¸ by nao-pon
			$fp = fopen($cache_file, "wb");
			fwrite($fp, $retstr);
			fclose($fp);
		}
		return  $retstr;
	}

	function getSource() {
		return $this->_source;
	}
	
	// �����������
	function getLocalPage($page = NULL)
	{
		if (! PukiWikiFunc::is_local_page($page)) {
			return "";
		} else {
			$source = str_replace("\r", '', file(PukiWikiFunc::get_local_filename($page)));
			return implode("",$source);
		}
	}
	// Private �᥽�åɴؿ���

	function _line_explode($string) {
		if (PukiWikiConfig::getParam("ExtTable")) {
			$string = preg_replace("/((\x0D\x0A)|(\x0D)|(\x0A))/","\n",$string);
			//ɽ��ս��Ƚ��Τ���ɽ��ɽ�δ֤϶��Ԥ�2��ɬ��
			$string = str_replace("|\n\n|","|\n\n\n|",$string);
			//ɽ��Ϥ��٤��ִ�
			$string = preg_replace("/(^|\n)(\|[^\r]+?\|)(\n[^|]|$)/e","'$1'.stripslashes(str_replace('->\n','___td_br___','$2')).'$3'",$string);
			//echo $string."<br/>";
			//ɽ��ɽ�δ֤϶���2�Ԥ�1�Ԥ��᤹
			$string = str_replace("|\n\n\n|","|\n\n|",$string);
		}
		$string = explode("\n", $string);
		return $string;
	}
	
	function _fix_table_br($string) {
		$string = str_replace("~___td_br___","<br>",$string);
		$string = str_replace("___td_br___","",$string);
		$string = preg_replace("/^<p>([^<>]*)<\/p>$/m","$1",$string);
		return $string;
	}

	function _init_PukiWiki_env() {
		//¾��PukiWiki�����ƥ�Ȥ�Ϣ�Ȼ��ν���� Original By nao-pon
		//  PukiWikiMod�Ѷ��̥�󥯤ؤ��б�
		//  AutoLinkͭ�����ˡ�AutoLink�ǡ����ɹ��ȡ�AutoLink�ǡ����������Υ���å��奯�ꥢ
	
		// PukiWikiMod ���̥�󥯥ǥ��쥯�ȥ��ɤ߹��� by nao-pon
		$wiki_common_dirs = "";
		if (defined('MOD_PUKI_WIKI_CACHE_DIR')) {
			if ((MOD_PUKI_WIKI_VER == "1.3") && file_exists(MOD_PUKI_WIKI_CACHE_DIR."config.php")) {
				include(MOD_PUKI_WIKI_CACHE_DIR."config.php");
			}
		}
		// PukiWikiMod ���̥�󥯥ǥ��쥯�ȥ�Ÿ��
		$wiki_common_dirs = preg_split("/\s+/",trim($wiki_common_dirs));
		sort($wiki_common_dirs,SORT_STRING);
		PukiWikiConfig::setParam('wiki_common_dirs',$wiki_common_dirs);

		// AutoLink�ǡ����ɤ߹��ߤȥ����å�(AutoLinkͭ�����Τ�)
		$autolink_dat = array();
		if ((PukiWikiConfig::getParam('autolink')) && (defined('MOD_PUKI_WIKI_CACHE_DIR')) && (file_exists(MOD_PUKI_WIKI_CACHE_DIR.'autolink.dat'))) {
			$autolink_dat = file(MOD_PUKI_WIKI_CACHE_DIR.'autolink.dat');
			if (!file_exists(MOD_PUKI_CACHE_DIR .'autolink.dat') || ($autolink_dat != file(MOD_PUKI_CACHE_DIR .'autolink.dat'))) {
				// ����ѥ����ȥ�󥯥ǡ�������¸
				list($pattern, $pattern_a, $forceignorelist) = $autolink_dat;
				if ($fp = fopen(MOD_PUKI_CACHE_DIR . 'autolink.dat', 'wb')) {
					set_file_buffer($fp, 0);
					flock($fp, LOCK_EX);
					rewind($fp);
					fputs($fp, trim($pattern)   . "\n");
					if (count($autolink_dat)==3) {
						fputs($fp, trim($pattern_a) . "\n");
						fputs($fp, trim($forceignorelist) . "\n");
					}
					flock($fp, LOCK_UN);
					fclose($fp);
				} else {
//					die_message('Cannot write autolink file '. MOD_PUKI_CACHE_DIR . '/autolink.dat<br />Maybe permission is not writable');
				}
				
				// �����ȥ�󥯥ǡ�������������Ƥ���Τǥ���å���򥯥ꥢ
				$dh = dir(MOD_PUKI_CACHE_DIR);
				while (($file = $dh->read()) !== FALSE) {
					if (substr($file,-6) != '.cache') {
						continue;
					}
					$file = MOD_PUKI_CACHE_DIR.$file;
					unlink($file);
				}
				$dh->close();
			}
		}
		PukiWikiConfig::setParam('autolink_dat',$autolink_dat);
	}
}
?>