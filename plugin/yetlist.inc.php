<?php
// $Id: yetlist.inc.php,v 1.8 2003/01/27 05:38:47 panda Exp $

// modified by PANDA <panda@arino.jp> http://home.arino.jp/
// Last-Update:2002-10-29 rev.2

function plugin_yetlist_action()
{
	global $script;
	
	$ret['msg'] = 'List of pages, are not made yet';
	$ret['body'] = '';
	
	$refer = array();
	foreach (get_existpages() as $_page) {
		$source = join("\n",preg_replace('/^(\s|\/\/|#).*$/','',get_source($_page)));
		$obj = new link_wrapper($_page);
		foreach ($obj->get_link($source) as $_obj) {
			if (($_obj->name != '') and ($_obj->type == 'WikiName') and !is_page($_obj->name)) {
				$refer[$_obj->name][] = $_page;
			}
		}
	}
	
	if (count($refer) == 0) {
		return $ret;
	}
	
	ksort($refer);
	
	foreach($refer as $page=>$refs) {
		$r_page = rawurlencode($page);
		$s_page = htmlspecialchars($page);
		
		$link_refs = array();
		foreach(array_unique($refs) as $_refer) {
			$r_refer = rawurlencode($_refer);
			$s_refer = htmlspecialchars($_refer);
			
			$link_refs[] = "<a href=\"$script?$r_refer\">$s_refer</a>";
		}
		$link_ref = join(' ',$link_refs);
		// 参照元ページが複数あった場合、referは最後のページを指す(いいのかな)
		$ret['body'] .= "<li><a href=\"$script?cmd=edit&amp;page=$r_page&amp;refer=$r_refer\">$s_page</a> <em>($link_ref)</em></li>\n";
	}
	
	if ($ret['body'] != '') {
		$ret['body'] = "<ul>\n{$ret['body']}</ul>\n";
	}
	
	return $ret;
}
?>
