<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: clear.inc.php,v 1.3 2004/08/23 11:46:10 henoheno Exp $
//
// div class="clear"を表示し、テキストの回りこみを解除する
// plugin=clear

function plugin_clear_convert() {
	return '<div class="clear"></div>';
}
?>
