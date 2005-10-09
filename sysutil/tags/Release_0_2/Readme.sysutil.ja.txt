XOOPS System Hack Utilities, AutoLogin & Multi Language Module.

[概要]
　このモジュールは、一切のXOOPSコアファイルに対する修正無しで、
　GIJOE氏開発の以下のHACKと同等機能を提供するモジュールです。

　・Auto Login Hack
　・EMLH(Easiest Multi Language Hack)

　モジュール化するにあたって、幾つかの機能の互換性は無くなっていますが、
　基本的には上記のGIJOE氏開発による２つのHackと同等の機能を持っています。
　元々のHackに比べると、コアを修正する手間が不用な分、若干性能的には、
　無駄な処理を行っていますが、ほとんど影響しないと思います。

　また、Multi Language対応部分に関しては、EMLHに対して、XOOPSの言語環境
　切替の機能が付加されています。但しこの機能を使用すると、ブロック
　キャッシュにも対応できるというEMLHの特徴の一つが失われてしまう事に
　なります。

[導入方法]
１．解凍したファイル群を、XOOPSのルートにコピーして下さい。
　　主なディレクトリ構成は以下のようになっています。
　　
　　XOOPS_ROOT_PATH
　　|
　　+--language
　　|   |
　　|   +--multi_lang
　　|
　　+--modules
　　    |
　　    +--sysutil

２．sysutilモジュールを、インストールして下さい。

３．管理所メニューのsysutil-一般設定を開いて、
　　各設定項目を設定して下さい。
　　この時、Multi Languageを使用しない場合でも、
　　デフォルトの使用言語は適切に設定して下さい。
　　導入直後は、「english」に設定されていますが、 
　　日本語環境の場合には「japanese」に変更して下さい。

AutoLogin機能を使用される方のみ
４．標準の「ログイン」ブロックを非表示に変更し、替わりに
　　sysutilモジュールの「ログイン」を表示するように変更して下さい。

Multi Languageを使用される方のみ
５．language/multi_lang/conf_ml.dist.phpというファイルを、
　　language/multi_lang/conf_ml.phpという名前のファイルとしてコピーし、
　　そのファイルの中身を修正して下さい。
　　標準のファイルでは、日本語、英語の切替を行う事が可能となっています。
　　[mlimg]タグにて表示される国旗イメージは、日米の国旗のみがmodules/sysutil/images
　　ディレクトリ下においてあります。

６．必要に応じて、「言語切替」ブロックを表示するように設定して下さい。

すべての方
７．ＸＯＯＰＳの「システム管理」-「一般設定」-「一般設定」画面にて、
　　使用言語を「multi_lang」に変更して下さい。
