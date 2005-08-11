<?php
if (!defined('WP_LANGUAGE_XOOPS_MODINFO_READ')) {
define ('WP_LANGUAGE_XOOPS_MODINFO_READ','1');
// Module Info

// The name of this module
define("_MI_WORDPRESS_NAME","WordPress%s");

// A brief description of this module
define("_MI_WORDPRESS_DESC","WordPress MEのXOOPSモジュールです。");
define("_MI_WORDPRESS_AUTHOR",'<a href="http://www.kowa.org/" target="_blank">のぶのぶ</a>');

// Sub menu titles
define("_MI_WORDPRESS_SMNAME1","Blogを書く");
define("_MI_WORDPRESS_SMNAME2","記事一覧");
// Sample Blog Message
define("_MI_WORDPRESS_INST_POST_CONTENT","'WordPress ME for Xoops2の導入成功おめでとうございます。<br /> . This is the first post. Edit or delete it, then start blogging!'");
define("_MI_WORDPRESS_INST_POST_TITLE","'ようこそ、WordPressの世界へ！'");
// Sample Comment
define("_MI_WORDPRESS_INST_COMMENT_CONTENT"," 'コメントのサンプルです！<br />To delete a comment, just log in, and view the posts\' comments, there you will have the option to edit or delete them.'");
// WordPress OptionTable Values
define("_MI_WORDPRESS_INST_OPTIONS_22","'ja'");
define("_MI_WORDPRESS_INST_OPTIONS_52","'Y年n月j日(l)'");
define("_MI_WORDPRESS_INST_OPTIONS_53","'H時i分s秒'");
// Config titles
define("_MI_WPUSESPAW_CFG_MSG","投稿フォームにで使用するエディタ");
define("_MI_WPUSESPAW_CFG_DESC","投稿フォームにて使用するWYSIWYGエディタを指定します");
define("_MI_OPT_WYSIWYG_NONE","None");
define("_MI_OPT_WYSIWYG_SPAW","SPAW Editor");
define("_MI_OPT_WYSIWYG_KOIVI","KOIVI Editor");

define("_MI_WPEDITAUTHGRP_CFG_MSG","初期権限が投稿権限をもつグループ");
define("_MI_WPEDITAUTHGRP_CFG_DESC","ユーザ登録時に投稿権限(WordPressのユーザレベル１）のをもつグループ");

define("_MI_WPADMINAUTHGRP_CFG_MSG","初期権限が管理権限をもつグループ");
define("_MI_WPADMINAUTHGRP_CFG_DESC","ユーザ登録時に管理権限(WordPressのユーザレベル１０）のをもつグループ");

define("_MI_WP_USE_XOOPS_SMILE","XOOPSのスマイルアイコンを使用");
define("_MI_WP_USE_XOOPS_SMILE_DESC","スマイルアイコンをXOOPSで使用しているものに置き換える。");

define("_MI_WP_USE_THEME_TEMPLATE","記事ブロック表示にthemeディレクトリのテンプレートを使用");
define("_MI_WP_USE_THEME_TEMPLATE_DESC","記事ブロック表示にthemeディレクトリ下のテンプレート(content_block-template.php)を使用します。");

define("_MI_WP_USE_BLOCKCSSHEADER","BLOCKのCSS参照をHTMLヘッダ部分に挿入");
define("_MI_WP_USE_BLOCKCSSHEADER_DESC",'BLOCKのCSS指定を、HTMLのヘッダ部分でリンクします。<br/>XOOPSテンプレートのtheme.html内の<b>&lt;{$xoops_module_header}&gt;</b>の下行に、<b>&lt;{$xoops_block_header}&gt;</b>を挿入する必要があります。<br/><b>&lt;{$xoops_themecss}&gt;</b>を使用することも可能ですが、この場合は、XOOPSの標準テンプレートと同様の書式によって<b>&lt;{$xoops_themecss}&gt;</b>が使用されている事が前提になります。');
define('_MI_OPT_BLOCKCSSHEADER_NONE', 'しない');
define('_MI_OPT_BLOCKCSSHEADER_YES', '&lt;{$xoops_block_header}&gt;を使用');
define('_MI_OPT_BLOCKCSSHEADER_HACK', '&lt;{$xoops_themecss}&gt;を使用');

define("_MI_WP_USE_XOOPS_COMM","XOOPSのコメントシステムを使用");
define("_MI_WP_USE_XOOPS_COMM_DESC","コメントをXOOPS共通のコメントシステムを使用します、以下の２項目はこの設定が「はい」の時のみに有効です。");

define("_MI_WP_SHOW_ARCHIVE_MENU","「記事一覧」をサブメニューに表示");
define("_MI_WP_SHOW_ARCHIVE_MENU_DESC","「記事一覧」をサブメニューに表示して「記事一覧機能の使用を可能にします。");

define("_MI_WP_USE_KAKASI","「記事一覧」でKakasiを使用");
define("_MI_WP_USE_KAKASI_DESC","「記事一覧」でKakasiを使用してタイトルの読みによる並び替えを可能にします。");

define("_MI_WP_KAKASI_PATH","kakasiのフルパス");
define("_MI_WP_KAKASI_PATH_DESC","「記事一覧」で使用するkakasiプログラムファイルのフルパスを指定します。");

define("_MI_WP_KAKASI_CHARSET","kakasiの使用文字コード");
define("_MI_WP_KAKASI_CHARSET_DESC","「記事一覧」で使用するkakasiが使用する文字コードを指定します。");

// Block Name
define("_MI_WORDPRESS_BNAME1","WordPress%s カレンダー");
define("_MI_WORDPRESS_BDESC1","WordPress カレンダー");

define("_MI_WORDPRESS_BNAME2","WordPress%s 月別過去ログ");
define("_MI_WORDPRESS_BDESC2","WordPress 月別過去ログ");

define("_MI_WORDPRESS_BNAME3","WordPress%s カテゴリ一覧");
define("_MI_WORDPRESS_BDESC3","WordPress カテゴリ一覧");

define("_MI_WORDPRESS_BNAME4","WordPress%s Link");
define("_MI_WORDPRESS_BDESC4","WordPress Link");

define("_MI_WORDPRESS_BNAME5","WordPress%s 検索");
define("_MI_WORDPRESS_BDESC5","WordPress 検索");

define("_MI_WORDPRESS_BNAME6","WordPress%s 最近の投稿");
define("_MI_WORDPRESS_BDESC6","WordPress 最近の投稿");

define("_MI_WORDPRESS_BNAME7","WordPress%s 最近のコメント");
define("_MI_WORDPRESS_BDESC7","WordPress 最近のコメント");

define("_MI_WORDPRESS_BNAME8","WordPress%s 記事ブロック");
define("_MI_WORDPRESS_BDESC8","WordPress 記事ブロック");

define("_MI_WORDPRESS_BNAME9","WordPress%s 投稿者ブロック");
define("_MI_WORDPRESS_BDESC9","WordPress 投稿者ブロック");

define("_MI_WORDPRESS_AD_MENU1","WordPressオプション");
if (strstr(XOOPS_VERSION, "XOOPS 2.2")) {
define("_MI_WORDPRESS_AD_MENU2","ブロック管理");
define("_MI_WORDPRESS_AD_MENU3","グループ管理");
}else{
define("_MI_WORDPRESS_AD_MENU2","ブロックアクセス権限");
}
}
?>
