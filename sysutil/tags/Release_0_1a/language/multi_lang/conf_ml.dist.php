<?php
// CONFIGURATIONS BEGIN
// default language
define ('SYSUTIL_ML_DEFAULT_LANGUNAME','japanese');

// list the language tags separated with comma
define('SYSUTIL_ML_LANGS','ja,en'); // [en]english[/en]  [ja]japanese[/ja] common

// list the language images separated with comma
define('SYSUTIL_ML_LANGIMAGES','modules/sysutil/images/japanese.gif,modules/sysutil/images/english.gif');

// list the language names separated with comma
define('SYSUTIL_ML_LANGNAMES','japanese,english');

// tag name for language image  (default [mlimg]. don't include specialchars)
define('SYSUTIL_ML_IMAGETAG','mlimg');
define('SYSUTIL_ML_URLTAG','mlurl');

// make regular expression which disallows language tags to cross it
define('SYSUTIL_ML_NEVERCROSSREGEX','/\<\/table\>/');

// the life time of language selection stored in cookie
define('SYSUTIL_ML_COOKIELIFETIME' ,365*86400);
define ('SYSUTIL_ML_PARAM_NAME','ml_lang');
define ('SYSUTIL_ML_COOKIE_NAME','ml_langname');
?>
