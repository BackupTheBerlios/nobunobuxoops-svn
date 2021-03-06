<?php
// this file contains customizable arrays for smilies, weekdays and month names.
if ( file_exists(dirname(__FILE__).'/language/'.$GLOBALS['xoopsConfig']['language'].'/main.php') ) {
	include_once dirname(__FILE__).'/language/'.$GLOBALS['xoopsConfig']['language'].'/main.php';
} else {
	include_once dirname(__FILE__).'/language/english/main.php';
}

// the weekdays and the months.. translate them if necessary
$GLOBALS['weekday'][0]=_WP_CAL_SUNDAY;
$GLOBALS['weekday'][1]=_WP_CAL_MONDAY;
$GLOBALS['weekday'][2]=_WP_CAL_TUESDAY;
$GLOBALS['weekday'][3]=_WP_CAL_WEDNESDAY;
$GLOBALS['weekday'][4]=_WP_CAL_THURSDAY;
$GLOBALS['weekday'][5]=_WP_CAL_FRIDAY;
$GLOBALS['weekday'][6]=_WP_CAL_SATURDAY;

$GLOBALS['s_weekday_length'] = _WP_CAL_SWEEK_LEN;

// the months, translate them if necessary - note: this isn't active everywhere yet
$GLOBALS['month']['01']=_WP_CAL_JANUARY;
$GLOBALS['month']['02']=_WP_CAL_FEBRUARY;
$GLOBALS['month']['03']=_WP_CAL_MARCH;
$GLOBALS['month']['04']=_WP_CAL_APRIL;
$GLOBALS['month']['05']=_WP_CAL_MAY;
$GLOBALS['month']['06']=_WP_CAL_JUNE;
$GLOBALS['month']['07']=_WP_CAL_JULY;
$GLOBALS['month']['08']=_WP_CAL_AUGUST;
$GLOBALS['month']['09']=_WP_CAL_SEPTEMBER;
$GLOBALS['month']['10']=_WP_CAL_OCTOBER;
$GLOBALS['month']['11']=_WP_CAL_NOVEMBER;
$GLOBALS['month']['12']=_WP_CAL_DECEMBER;

$GLOBALS['s_month_length'] = _WP_CAL_SMONTH_LEN;
$GLOBALS['wp_month_format'] = _WP_MONTH_FORMAT;

// here's the conversion table, you can modify it if you know what you're doing
if (get_xoops_option(wp_mod(), 'wp_use_xoops_smilies')) {
	// Get smilies infomation from XOOPS DB
	$_getsmiles = $GLOBALS['xoopsDB']->query('SELECT id, code, smile_url FROM '.$GLOBALS['xoopsDB']->prefix('smiles').' ORDER BY id');
	if ($GLOBALS['xoopsDB']->getRowsNum($_getsmiles) == '0') {
		//EMPTY
	} else {
		while ($_smiles = $GLOBALS['xoopsDB']->fetchArray($_getsmiles)) {
			$GLOBALS['wpsmiliestrans'][wp_id()][$_smiles['code']] = $_smiles['smile_url'];
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
include(get_custom_path('wp-config-custom.php'));
?>