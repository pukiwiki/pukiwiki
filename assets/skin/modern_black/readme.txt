1. 解凍したmodernskinフォルダをPukiWikiのskinフォルダに入れる。
2. pukiwiki.ini.phpの85行目辺りにある
 「define('SKIN_DIR', 'skin/');」
    ↓
 「define('SKIN_DIR', 'skin/modernskin/');」
  へ変更する。

pukiwiki.skin.phpの定義WIKI_EXPLAIN...タイトルの下に載せる文字

＊modern_black.cssの適用
(初期の場合、)83行目の「 <link rel="stylesheet" type="text/css" href="<?php echo SKIN_DIR ?>modern.css" />」を
「 <link rel="stylesheet" type="text/css" href="<?php echo SKIN_DIR ?>modern_black.css" />」へ変更する。



アイコン提供元: https://icon-rainbow.com/
Version: 1.3
