<?php
//******************* ご注意 *******************
// このファイルを直接編集頂いても良いですが、
// バージョンアップ時に上書きされるのを防ぐために
// このファイル(xoops.dist.php)をxoops.phpに
// コピーしてから、カストマイズする事をおすすめします。
//******************* ご注意 *******************

/////////////////////////////////////////////////
//XOOPS固有の設定(以下は変えない事を推奨)
/////////////////////////////////////////////////
// URLの自動リンク生成はmodPukiWiki側では行わない
	PukiWikiConfig::setParam('autourllink',0);
/////////////////////////////////////////////////

/////////////////////////////////////////////////
//カストマイズ可能な代表的な設定例
/////////////////////////////////////////////////
// AutoLinkを有効にする場合は、AutoLink対象となる
// ページ名の最短バイト数を指定
// AutoLinkを無効にする場合は0
//  (PukiWikiMod及びB-Wiki専用)
//	PukiWikiConfig::setParam('autolink',3);
/////////////////////////////////////////////////
// PukiWikiModへのリンクを静的URL形式にする
//  (PukiWikiMod専用)
//	PukiWikiConfig::setParam('use_static_url',1);
/////////////////////////////////////////////////
// 拡張テーブル書式を使用する
// PukiWikiModの拡張テーブル書式を使用可能にします。
// デフォルトでは、PukiWiki1.4.xのテーブル書式のみが
// 使用可能です。
//	PukiWikiConfig::setParam('ExtTable',true);
/////////////////////////////////////////////////
// レンダリングキャッシュを有効にする
//	PukiWikiConfig::setParam('use_cache',1);
/////////////////////////////////////////////////
// 一番外側の<p></p>を出力しない
	PukiWikiConfig::setParam('omit_paragraph',1);

/////////////////////////////////////////////////
// その他カスタマイズ可能なパラメータは、default.phpを
// 参照して下さい。
// 但し、default.phpでの書式が違うので注意して下さい。
// default.phpにて、
//		$_settings['nowikiname'] = 1;
// とある場合には、当ファイルでは
//		PukiWikiConfig::setParam('nowikiname',1);
// というように設定して下さい。
/////////////////////////////////////////////////
?>
