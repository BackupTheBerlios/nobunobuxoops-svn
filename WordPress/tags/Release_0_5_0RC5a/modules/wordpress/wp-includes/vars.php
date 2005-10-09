<?php
/* Don't remove this line */ if (!defined('XOOPS_ROOT_PATH')) { exit; }
/* This file sets various arrays and variables for use in WordPress */
# WordPress version
$GLOBALS['wp_version'] = 'ME for XOOPS '.get_version();

# GreyMatter formatting search and replace arrays
$GLOBALS['wp_gmcode']['in'] = array(
	'#\\*\*(.+?)\\*\*#is',		// **bold**
	'#\\\\(.+?)\\\\#is',		// \\italic\\
	'#\__(.+?)\__#is'		// __underline__
);
$GLOBALS['wp_gmcode']['out'] = array(
	'<strong>$1</strong>',
	'<em>$1</em>',
	'<span style="text-decoration:underline">$1</span>'
);

# Translation of HTML entities and special characters
$GLOBALS['wp_htmltrans'] = array_flip(get_html_translation_table(HTML_ENTITIES));
$GLOBALS['wp_htmltrans']['<'] = '<';	# preserve HTML
$GLOBALS['wp_htmltrans']['>'] = '>';	# preserve HTML

$wp_htmltransbis = array(
	chr(226).chr(128).chr(147) => '&#8211;',
	chr(226).chr(128).chr(148) => '&#8212;',
	chr(226).chr(128).chr(152) => '&#8216;',
	chr(226).chr(128).chr(153) => '&#8217;',
	chr(226).chr(128).chr(156) => '&#8220;',
	chr(226).chr(128).chr(157) => '&#8221;',
	chr(226).chr(128).chr(162) => '&#8226;',
	chr(226).chr(130).chr(172) => '&#8364;',
	'&lt;' => '&#60;',	# preserve fake HTML
	'&gt;' => '&#62;',	# preserve fake HTML
	'&amp;' => '&#038;',	# preserve fake HTML
	'&sp;' => '&#32;', '&excl;' => '&#33;', '&quot;' => '&#34;', '&num;' => '&#35;', '&dollar;' => '&#36;', '&percnt;' => '&#37;', '&apos;' => '&#39;', '&lpar;' => '&#40;', '&rpar;' => '&#41;',
	'&ast;' => '&#42;', '&plus;' => '&#43;', '&comma;' => '&#44;', '&hyphen;' => '&#45;', '&minus;' => '&#45;', '&period;' => '&#46;', '&sol;' => '&#47;', '&colon;' => '&#58;', '&semi;' => '&#59;', '&lt;' => '&#60;',
	'&equals;' => '&#61;', '&gt;' => '&#62;', '&quest;' => '&#63;', '&commat;' => '&#64;', '&lsqb;' => '&#91;', '&bsol;' => '&#92;', '&rsqb;' => '&#93;', '&circ;' => '&#94;', '&lowbar;' => '&#95;', '&horbar;' => '&#95;',
	'&grave;' => '&#96;', '&lcub;' => '&#123;', '&verbar;' => '&#124;', '&rcub;' => '&#125;', '&tilde;' => '&#126;', '&lsquor;' => '&#130;', '&ldquor;' => '&#132;',
	'&ldots;' => '&#133;', '&Scaron;' => '&#138;', '&lsaquo;' => '&#139;', '&OElig;' => '&#140;', '&lsquo;' => '&#145;', '&rsquor;' => '&#145;', '&rsquo;' => '&#146;',
	'&ldquo;' => '&#147;', '&rdquor;' => '&#147;', '&rdquo;' => '&#148;', '&bull;' => '&#149;', '&ndash;' => '&#150;', '&endash;' => '&#150;', '&mdash;' => '&#151;', '&emdash;' => '&#151;', '&tilde;' => '&#152;', '&trade;' => '&#153;',
	'&scaron;' => '&#154;', '&rsaquo;' => '&#155;', '&oelig;' => '&#156;', '&Yuml;' => '&#159;', '&nbsp;' => '&#160;', '&iexcl;' => '&#161;', '&cent;' => '&#162;', '&pound;' => '&#163;', '&curren;' => '&#164;', '&yen;' => '&#165;',
	'&brvbar;' => '&#166;', '&brkbar;' => '&#166;', '&sect;' => '&#167;', '&uml;' => '&#168;', '&die;' => '&#168;', '&copy;' => '&#169;', '&ordf;' => '&#170;', '&laquo;' => '&#171;', '&not;' => '&#172;', '&shy;' => '&#173;',
	'&reg;' => '&#174;', '&macr;' => '&#175;', '&hibar;' => '&#175;', '&deg;' => '&#176;', '&plusmn;' => '&#177;', '&sup2;' => '&#178;', '&sup3;' => '&#179;', '&acute;' => '&#180;', '&micro;' => '&#181;', '&para;' => '&#182;',
	'&middot;' => '&#183;', '&cedil;' => '&#184;', '&sup1;' => '&#185;', '&ordm;' => '&#186;', '&raquo;' => '&#187;', '&frac14;' => '&#188;', '&frac12;' => '&#189;', '&half;' => '&#189;', '&frac34;' => '&#190;', '&iquest;' => '&#191;',
	'&Agrave;' => '&#192;', '&Aacute;' => '&#193;', '&Acirc;' => '&#194;', '&Atilde;' => '&#195;', '&Auml;' => '&#196;', '&Aring;' => '&#197;', '&AElig;' => '&#198;', '&Ccedil;' => '&#199;', '&Egrave;' => '&#200;', '&Eacute;' => '&#201;',
	'&Ecirc;' => '&#202;', '&Euml;' => '&#203;', '&Igrave;' => '&#204;', '&Iacute;' => '&#205;', '&Icirc;' => '&#206;', '&Iuml;' => '&#207;', '&ETH;' => '&#208;', '&Ntilde;' => '&#209;', '&Ograve;' => '&#210;', '&Oacute;' => '&#211;',
	'&Ocirc;' => '&#212;', '&Otilde;' => '&#213;', '&Ouml;' => '&#214;', '&times;' => '&#215;', '&Oslash;' => '&#216;', '&Ugrave;' => '&#217;', '&Uacute;' => '&#218;', '&Ucirc;' => '&#219;', '&Uuml;' => '&#220;', '&Yacute;' => '&#221;',
	'&THORN;' => '&#222;', '&szlig;' => '&#223;', '&agrave;' => '&#224;', '&aacute;' => '&#225;', '&acirc;' => '&#226;', '&atilde;' => '&#227;', '&auml;' => '&#228;', '&aring;' => '&#229;', '&aelig;' => '&#230;', '&ccedil;' => '&#231;',
	'&egrave;' => '&#232;', '&eacute;' => '&#233;', '&ecirc;' => '&#234;', '&euml;' => '&#235;', '&igrave;' => '&#236;', '&iacute;' => '&#237;', '&icirc;' => '&#238;', '&iuml;' => '&#239;', '&eth;' => '&#240;', '&ntilde;' => '&#241;',
	'&ograve;' => '&#242;', '&oacute;' => '&#243;', '&ocirc;' => '&#244;', '&otilde;' => '&#245;', '&ouml;' => '&#246;', '&divide;' => '&#247;', '&oslash;' => '&#248;', '&ugrave;' => '&#249;', '&uacute;' => '&#250;', '&ucirc;' => '&#251;',
	'&uuml;' => '&#252;', '&yacute;' => '&#253;', '&thorn;' => '&#254;', '&yuml;' => '&#255;', '&OElig;' => '&#338;', '&oelig;' => '&#339;', '&Scaron;' => '&#352;', '&scaron;' => '&#353;', '&Yuml;' => '&#376;', '&fnof;' => '&#402;',
	'&circ;' => '&#710;', '&tilde;' => '&#732;', '&Alpha;' => '&#913;', '&Beta;' => '&#914;', '&Gamma;' => '&#915;', '&Delta;' => '&#916;', '&Epsilon;' => '&#917;', '&Zeta;' => '&#918;', '&Eta;' => '&#919;', '&Theta;' => '&#920;',
	'&Iota;' => '&#921;', '&Kappa;' => '&#922;', '&Lambda;' => '&#923;', '&Mu;' => '&#924;', '&Nu;' => '&#925;', '&Xi;' => '&#926;', '&Omicron;' => '&#927;', '&Pi;' => '&#928;', '&Rho;' => '&#929;', '&Sigma;' => '&#931;',
	'&Tau;' => '&#932;', '&Upsilon;' => '&#933;', '&Phi;' => '&#934;', '&Chi;' => '&#935;', '&Psi;' => '&#936;', '&Omega;' => '&#937;', '&alpha;' => '&#945;', '&beta;' => '&#946;', '&gamma;' => '&#947;', '&delta;' => '&#948;',
	'&epsilon;' => '&#949;', '&zeta;' => '&#950;', '&eta;' => '&#951;', '&theta;' => '&#952;', '&iota;' => '&#953;', '&kappa;' => '&#954;', '&lambda;' => '&#955;', '&mu;' => '&#956;', '&nu;' => '&#957;', '&xi;' => '&#958;',
	'&omicron;' => '&#959;', '&pi;' => '&#960;', '&rho;' => '&#961;', '&sigmaf;' => '&#962;', '&sigma;' => '&#963;', '&tau;' => '&#964;', '&upsilon;' => '&#965;', '&phi;' => '&#966;', '&chi;' => '&#967;', '&psi;' => '&#968;',
	'&omega;' => '&#969;', '&thetasym;' => '&#977;', '&upsih;' => '&#978;', '&piv;' => '&#982;', '&ensp;' => '&#8194;', '&emsp;' => '&#8195;', '&thinsp;' => '&#8201;', '&zwnj;' => '&#8204;', '&zwj;' => '&#8205;', '&lrm;' => '&#8206;',
	'&rlm;' => '&#8207;', '&ndash;' => '&#8211;', '&mdash;' => '&#8212;', '&lsquo;' => '&#8216;', '&rsquo;' => '&#8217;', '&sbquo;' => '&#8218;', '&ldquo;' => '&#8220;', '&rdquo;' => '&#8221;', '&bdquo;' => '&#8222;', '&dagger;' => '&#8224;',
	'&Dagger;' => '&#8225;', '&bull;' => '&#8226;', '&hellip;' => '&#8230;', '&permil;' => '&#8240;', '&prime;' => '&#8242;', '&Prime;' => '&#8243;', '&lsaquo;' => '&#8249;', '&rsaquo;' => '&#8250;', '&oline;' => '&#8254;', '&frasl;' => '&#8260;',
	'&euro;' => '&#8364;', '&image;' => '&#8465;', '&weierp;' => '&#8472;', '&real;' => '&#8476;', '&trade;' => '&#8482;', '&alefsym;' => '&#8501;', '&larr;' => '&#8592;', '&uarr;' => '&#8593;', '&rarr;' => '&#8594;', '&darr;' => '&#8595;',
	'&harr;' => '&#8596;', '&crarr;' => '&#8629;', '&lArr;' => '&#8656;', '&uArr;' => '&#8657;', '&rArr;' => '&#8658;', '&dArr;' => '&#8659;', '&hArr;' => '&#8660;', '&forall;' => '&#8704;', '&part;' => '&#8706;', '&exist;' => '&#8707;',
	'&empty;' => '&#8709;', '&nabla;' => '&#8711;', '&isin;' => '&#8712;', '&notin;' => '&#8713;', '&ni;' => '&#8715;', '&prod;' => '&#8719;', '&sum;' => '&#8721;', '&minus;' => '&#8722;', '&lowast;' => '&#8727;', '&radic;' => '&#8730;',
	'&prop;' => '&#8733;', '&infin;' => '&#8734;', '&ang;' => '&#8736;', '&and;' => '&#8743;', '&or;' => '&#8744;', '&cap;' => '&#8745;', '&cup;' => '&#8746;', '&int;' => '&#8747;', '&there4;' => '&#8756;', '&sim;' => '&#8764;',
	'&cong;' => '&#8773;', '&asymp;' => '&#8776;', '&ne;' => '&#8800;', '&equiv;' => '&#8801;', '&le;' => '&#8804;', '&ge;' => '&#8805;', '&sub;' => '&#8834;', '&sup;' => '&#8835;', '&nsub;' => '&#8836;', '&sube;' => '&#8838;',
	'&supe;' => '&#8839;', '&oplus;' => '&#8853;', '&otimes;' => '&#8855;', '&perp;' => '&#8869;', '&sdot;' => '&#8901;', '&lceil;' => '&#8968;', '&rceil;' => '&#8969;', '&lfloor;' => '&#8970;', '&rfloor;' => '&#8971;', '&lang;' => '&#9001;',
	'&rang;' => '&#9002;', '&loz;' => '&#9674;', '&spades;' => '&#9824;', '&clubs;' => '&#9827;', '&hearts;' => '&#9829;', '&diams;' => '&#9830;'
);
$GLOBALS['wp_htmltrans'] = array_merge($GLOBALS['wp_htmltrans'],$wp_htmltransbis);

# Translation of invalid Unicode references range to valid range
$GLOBALS['wp_htmltranswinuni'] = array(
	'&#128;' => '&#8364;', // the Euro sign
	'&#129;' => '',
	'&#130;' => '&#8218;', // these are Windows CP1252 specific characters
	'&#131;' => '&#402;',  // they would look weird on non-Windows browsers
	'&#132;' => '&#8222;',
	'&#133;' => '&#8230;',
	'&#134;' => '&#8224;',
	'&#135;' => '&#8225;',
	'&#136;' => '&#710;',
	'&#137;' => '&#8240;',
	'&#138;' => '&#352;',
	'&#139;' => '&#8249;',
	'&#140;' => '&#338;',
	'&#141;' => '',
	'&#142;' => '&#382;',
	'&#143;' => '',
	'&#144;' => '',
	'&#145;' => '&#8216;',
	'&#146;' => '&#8217;',
	'&#147;' => '&#8220;',
	'&#148;' => '&#8221;',
	'&#149;' => '&#8226;',
	'&#150;' => '&#8211;',
	'&#151;' => '&#8212;',
	'&#152;' => '&#732;',
	'&#153;' => '&#8482;',
	'&#154;' => '&#353;',
	'&#155;' => '&#8250;',
	'&#156;' => '&#339;',
	'&#157;' => '',
	'&#158;' => '',
	'&#159;' => '&#376;'
);

# on which page are we ?
$GLOBALS['PHP_SELF'] = $_SERVER['PHP_SELF'];
$GLOBALS['pagenow'] = explode('/', $GLOBALS['PHP_SELF']);
$GLOBALS['pagenow'] = trim($GLOBALS['pagenow'][(sizeof($GLOBALS['pagenow'])-1)]);
$GLOBALS['pagenow'] = explode('?', $GLOBALS['pagenow']);
$GLOBALS['pagenow'] = $GLOBALS['pagenow'][0];
//if (($querystring_start == '/') && ($pagenow != 'post.php')) {
//	$pagenow = $siteurl.'/index.php';
//}

# browser detection
$GLOBALS['is_lynx'] = 0;
$GLOBALS['is_gecko'] = 0;
$GLOBALS['is_winIE'] = 0;
$GLOBALS['is_macIE'] = 0;
$GLOBALS['is_opera'] = 0;
$GLOBALS['is_NS4'] = 0;
$GLOBALS['is_docomo']=0;
if (!isset($GLOBALS['HTTP_USER_AGENT'])) {
	$GLOBALS['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
}
if (preg_match('/Lynx/', $GLOBALS['HTTP_USER_AGENT'])) {
	$GLOBALS['is_lynx'] = 1;
} elseif (preg_match('/Gecko/', $GLOBALS['HTTP_USER_AGENT'])) {
	$GLOBALS['is_gecko'] = 1;
} elseif ((preg_match('/MSIE/', $GLOBALS['HTTP_USER_AGENT'])) && (preg_match('/Win/', $GLOBALS['HTTP_USER_AGENT']))) {
	$GLOBALS['is_winIE'] = 1;
} elseif ((preg_match('/MSIE/', $GLOBALS['HTTP_USER_AGENT'])) && (preg_match('/Mac/', $GLOBALS['HTTP_USER_AGENT']))) {
	$GLOBALS['is_macIE'] = 1;
} elseif (preg_match('/Opera/', $GLOBALS['HTTP_USER_AGENT'])) {
	$GLOBALS['is_opera'] = 1;
} elseif (preg_match('/DoCoMo/', $GLOBALS['HTTP_USER_AGENT'])) {
	$GLOBALS['is_docomo'] = 1;
    $ua_list = explode("/", $GLOBALS['HTTP_USER_AGENT']);
    $GLOBALS['is_docomo'] = substr($ua_list[3], 1);
    $GLOBALS['is_docomo'] = $GLOBALS['is_docomo']*1024;
} elseif ((preg_match('/Nav/', $GLOBALS['HTTP_USER_AGENT']) ) || (preg_match('/Mozilla\/4\./', $GLOBALS['HTTP_USER_AGENT']))) {
	$GLOBALS['is_NS4'] = 1;
}
$GLOBALS['is_IE']    = (($GLOBALS['is_macIE']) || ($GLOBALS['is_winIE']));

# browser-specific javascript corrections
$GLOBALS['wp_macIE_correction']['in'] = array(
	'/\%uFFD4/', '/\%uFFD5/', '/\%uFFD2/', '/\%uFFD3/',
	'/\%uFFA5/', '/\%uFFD0/', '/\%uFFD1/', '/\%uFFBD/',
	'/\%uFF83%uFFC0/', '/\%uFF83%uFFC1/', '/\%uFF83%uFFC6/', '/\%uFF83%uFFC9/',
	'/\%uFFB9/', '/\%uFF81%uFF8C/', '/\%uFF81%uFF8D/', '/\%uFF81%uFFDA/',
	'/\%uFFDB/'
);
$GLOBALS['wp_macIE_correction']['out'] = array(
	'&lsquo;', '&rsquo;', '&ldquo;', '&rdquo;',
	'&bull;', '&ndash;', '&mdash;', '&Omega;',
	'&beta;', '&gamma;', '&theta;', '&lambda;',
	'&pi;', '&prime;', '&Prime;', '&ang;',
	'&euro;'
);

$GLOBALS['wp_gecko_correction']['in'] = array(
	'/'.chr(226).chr(128).chr(152).'/',
	'/'.chr(226).chr(128).chr(153).'/',
	'/'.chr(226).chr(128).chr(156).'/',
	'/'.chr(226).chr(128).chr(157).'/',
	'/'.chr(226).chr(128).chr(162).'/',
	'/'.chr(226).chr(128).chr(147).'/',
	'/'.chr(226).chr(128).chr(148).'/',
	'/'.chr(206).chr(169).'/',
	'/'.chr(206).chr(178).'/',
	'/'.chr(206).chr(179).'/',
	'/'.chr(206).chr(184).'/',
	'/'.chr(206).chr(187).'/',
	'/'.chr(207).chr(128).'/',
	'/'.chr(226).chr(128).chr(178).'/',
	'/'.chr(226).chr(128).chr(179).'/',
	'/'.chr(226).chr(136).chr(160).'/',
	'/'.chr(226).chr(130).chr(172).'/',
);
$GLOBALS['wp_gecko_correction']['out'] = array(
	'&8216;', '&rsquo;', '&ldquo;', '&rdquo;',
	'&bull;', '&ndash;', '&mdash;', '&Omega;',
	'&beta;', '&gamma;', '&theta;', '&lambda;',
	'&pi;', '&prime;', '&Prime;', '&ang;',
	'&euro;'
);

# server detection
$GLOBALS['is_Apache'] = strstr($_SERVER['SERVER_SOFTWARE'], 'Apache') ? 1 : 0;
$GLOBALS['is_IIS'] = strstr($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') ? 1 : 0;

# if the config file does not provide the smilies array, let's define it here
if (!isset($GLOBALS['wpsmiliestrans'][wp_id()])) {
	if (get_xoops_option(wp_mod() ,'wp_use_xoops_smilies')) {
		$db =& Database::getInstance();
		$getsmiles = $db->query("SELECT id, code, smile_url FROM ".$db->prefix("smiles")." ORDER BY id");
		if (($numsmiles = $db->getRowsNum($getsmiles)) == "0") {
			//EMPTY
		} else {
			while ($smiles = $db->fetchArray($getsmiles)) {
				$GLOBALS['wpsmiliestrans'][wp_id()][$smiles['code']] = $smiles['smile_url'];
			}
		}
	} else {
	    $GLOBALS['wpsmiliestrans'][wp_id()] = array(
	        ' :)'        => 'icon_smile.gif',
	        ' :D'        => 'icon_biggrin.gif',
	        ' :-D'       => 'icon_biggrin.gif',
	        ':grin:'    => 'icon_biggrin.gif',
	        ' :)'        => 'icon_smile.gif',
	        ' :-)'       => 'icon_smile.gif',
	        ':smile:'   => 'icon_smile.gif',
	        ' :('        => 'icon_sad.gif',
	        ' :-('       => 'icon_sad.gif',
	        ':sad:'     => 'icon_sad.gif',
	        ' :o'        => 'icon_surprised.gif',
	        ' :-o'       => 'icon_surprised.gif',
	        ':eek:'     => 'icon_surprised.gif',
	        ' 8O'        => 'icon_eek.gif',
	        ' 8-O'       => 'icon_eek.gif',
	        ':shock:'   => 'icon_eek.gif',
	        ' :?'        => 'icon_confused.gif',
	        ' :-?'       => 'icon_confused.gif',
	        ' :???:'     => 'icon_confused.gif',
	        ' 8)'        => 'icon_cool.gif',
	        ' 8-)'       => 'icon_cool.gif',
	        ':cool:'    => 'icon_cool.gif',
	        ':lol:'     => 'icon_lol.gif',
	        ' :x'        => 'icon_mad.gif',
	        ' :-x'       => 'icon_mad.gif',
	        ':mad:'     => 'icon_mad.gif',
	        ' :P'        => 'icon_razz.gif',
	        ' :-P'       => 'icon_razz.gif',
	        ':razz:'    => 'icon_razz.gif',
	        ':oops:'    => 'icon_redface.gif',
	        ':cry:'     => 'icon_cry.gif',
	        ':evil:'    => 'icon_evil.gif',
	        ':twisted:' => 'icon_twisted.gif',
	        ':roll:'    => 'icon_rolleyes.gif',
	        ':wink:'    => 'icon_wink.gif',
	        ' ;)'        => 'icon_wink.gif',
	        ' ;-)'       => 'icon_wink.gif',
	        ':!:'       => 'icon_exclaim.gif',
	        ':?:'       => 'icon_question.gif',
	        ':idea:'    => 'icon_idea.gif',
	        ':arrow:'   => 'icon_arrow.gif',
	        ' :|'        => 'icon_neutral.gif',
	        ' :-|'       => 'icon_neutral.gif',
	        ':neutral:' => 'icon_neutral.gif',
	        ':mrgreen:' => 'icon_mrgreen.gif',
	    );
	}
}

# sorts the smilies' array
if (!function_exists('smiliescmp')) {
	function smiliescmp ($a, $b) {
	   if (strlen($a) == strlen($b)) {
		  return strcmp($a, $b);
	   }
	   return (strlen($a) > strlen($b)) ? -1 : 1;
	}
}

if (get_xoops_option(wp_mod(),'wp_use_xoops_smilies')==0) {
	uksort($GLOBALS['wpsmiliestrans'][wp_id()], 'smiliescmp');
}

# generates smilies' search & replace arrays
$GLOBALS['wp_smiliessearch'][wp_id()] = array();
$GLOBALS['wp_smiliesreplace'][wp_id()] = array();
foreach($GLOBALS['wpsmiliestrans'][wp_id()] as $smiley => $img) {
	$GLOBALS['wp_smiliessearch'][wp_id()][] = $smiley;
	$smiley_masked = str_replace(' ', '', $smiley);
	$GLOBALS['wp_smiliesreplace'][wp_id()][] = " <img src='".$GLOBALS['smilies_directory']."/$img' alt='$smiley_masked' />";
}

?>